<?php
/**
 * Seed version-controlled block theme content into the current database.
 *
 * Usage:
 *   php bin/seed-content.php [--dry-run] [--skip-media] [--skip-pages]
 *                            [--update-pages] [--flush] [--allow-theme-mismatch]
 *
 * With WP-CLI:
 *   wp eval-file bin/seed-content.php -- --dry-run
 *
 * Scope:
 * - creates/updates theme-scoped templates, template parts, global styles
 * - creates/updates block navigation posts
 * - creates/updates attachment records from content/media/manifest.json
 * - creates pages (existing pages are left alone unless --update-pages)
 * - updates the selected site options, including site_logo/site_icon IDs
 *
 * It never touches home/siteurl, users, plugins, or the active theme setting.
 */

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 403 );
	exit( "CLI only.\n" );
}

if ( ! defined( 'ABSPATH' ) ) {
	require_once dirname( __DIR__ ) . '/wp-load.php';
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$seed_root        = dirname( __DIR__ );
$seed_content_dir = $seed_root . '/content';

$seed_raw_args = array();

if ( isset( $args ) && is_array( $args ) ) {
	$seed_raw_args = array_merge( $seed_raw_args, $args );
}

if ( isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] ) ) {
	$seed_raw_args = array_merge( $seed_raw_args, array_slice( $_SERVER['argv'], 1 ) );
}

$seed_flags = array();

foreach ( $seed_raw_args as $seed_raw_arg ) {
	if ( is_string( $seed_raw_arg ) && 0 === strpos( $seed_raw_arg, '--' ) ) {
		$seed_flags[ $seed_raw_arg ] = true;
	}
}

$seed_dry_run              = isset( $seed_flags['--dry-run'] );
$seed_skip_media           = isset( $seed_flags['--skip-media'] );
$seed_skip_pages           = isset( $seed_flags['--skip-pages'] );
$seed_update_pages         = isset( $seed_flags['--update-pages'] );
$seed_flush                = isset( $seed_flags['--flush'] );
$seed_allow_theme_mismatch = isset( $seed_flags['--allow-theme-mismatch'] );

/**
 * Print a progress line.
 *
 * @param string $message Message.
 * @return void
 */
function seed_log( $message ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::log( $message );
		return;
	}

	echo $message . "\n";
}

/**
 * Print a warning.
 *
 * @param string $message Message.
 * @return void
 */
function seed_warn( $message ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::warning( $message );
		return;
	}

	fwrite( STDERR, 'WARNING: ' . $message . "\n" );
}

/**
 * Stop the seed with an error.
 *
 * @param string $message Message.
 * @return void
 */
function seed_fail( $message ) {
	if ( class_exists( 'WP_CLI' ) ) {
		WP_CLI::error( $message );
	}

	fwrite( STDERR, 'ERROR: ' . $message . "\n" );
	exit( 1 );
}

/**
 * Print command usage.
 *
 * @return void
 */
function seed_usage() {
	echo "Usage: php bin/seed-content.php [options]\n\n";
	echo "  --dry-run                 Report changes without writing anything.\n";
	echo "  --skip-media              Match existing media but do not create/update attachments.\n";
	echo "  --skip-pages              Do not seed pages.\n";
	echo "  --update-pages            Update existing pages (default is create-only).\n";
	echo "  --flush                   Flush rewrite rules after seeding.\n";
	echo "  --allow-theme-mismatch    Allow seeding when the active theme differs from content/site.json.\n";
	echo "  --help                    Show this help.\n";
}

if ( isset( $seed_flags['--help'] ) ) {
	seed_usage();
	exit( 0 );
}

/**
 * Read and decode a JSON file.
 *
 * @param string $path     File path.
 * @param bool   $required Whether a missing file is fatal.
 * @return array
 */
function seed_read_json( $path, $required = true ) {
	if ( ! file_exists( $path ) ) {
		if ( $required ) {
			seed_fail( 'Missing file: ' . $path . ' (run php bin/export-content.php locally first)' );
		}

		return array();
	}

	$raw = file_get_contents( $path );

	if ( false === $raw ) {
		seed_fail( 'Could not read: ' . $path );
	}

	$data = json_decode( $raw, true );

	if ( JSON_ERROR_NONE !== json_last_error() ) {
		seed_fail( 'Invalid JSON in ' . $path . ': ' . json_last_error_msg() );
	}

	return is_array( $data ) ? $data : array();
}

/**
 * Read a text file.
 *
 * @param string $path File path.
 * @return string
 */
function seed_read_text( $path ) {
	if ( ! file_exists( $path ) ) {
		seed_fail( 'Missing file: ' . $path );
	}

	$contents = file_get_contents( $path );

	if ( false === $contents ) {
		seed_fail( 'Could not read: ' . $path );
	}

	return $contents;
}

/**
 * Remove a trailing slash from a URL.
 *
 * @param string $url URL.
 * @return string
 */
function seed_untrail( $url ) {
	return untrailingslashit( (string) $url );
}

/**
 * Replace the source site/upload URLs with the current environment URLs.
 *
 * @param string $text               Content.
 * @param array  $site               content/site.json data.
 * @param string $target_uploads_url Current uploads base URL.
 * @return string
 */
function seed_replace_urls( $text, $site, $target_uploads_url ) {
	$pairs = array(
		array( isset( $site['source_uploads_url'] ) ? $site['source_uploads_url'] : '', $target_uploads_url ),
		array( isset( $site['source_url'] ) ? $site['source_url'] : '', home_url( '/' ) ),
	);

	$replacements = array();

	foreach ( $pairs as $pair ) {
		$from = seed_untrail( $pair[0] );
		$to   = seed_untrail( $pair[1] );

		if ( '' === $from || $from === $to ) {
			continue;
		}

		$replacements[ $from ] = $to;
		$replacements[ str_replace( '/', '\\/', $from ) ] = str_replace( '/', '\\/', $to );
	}

	return empty( $replacements ) ? $text : strtr( $text, $replacements );
}

/**
 * Remap attachment IDs inside block markup.
 *
 * Handles image IDs, mediaId values, gallery ID arrays, wp-image-{ID}
 * classes, and data-id attributes.
 *
 * @param string $text Content.
 * @param array  $map  Old attachment ID => new attachment ID.
 * @return string
 */
function seed_remap_ids( $text, $map ) {
	if ( empty( $map ) ) {
		return $text;
	}

	$updated = preg_replace_callback(
		'/("ids"\s*:\s*\[)([^\]]*)(\])/',
		function ( $matches ) use ( $map ) {
			$ids = array_map( 'trim', explode( ',', $matches[2] ) );

			foreach ( $ids as $index => $id ) {
				if ( ! ctype_digit( $id ) ) {
					continue;
				}

				$old_id = (int) $id;

				if ( isset( $map[ $old_id ] ) && (int) $map[ $old_id ] > 0 ) {
					$ids[ $index ] = (string) (int) $map[ $old_id ];
				}
			}

			return $matches[1] . implode( ', ', $ids ) . $matches[3];
		},
		$text
	);

	if ( null !== $updated ) {
		$text = $updated;
	}

	foreach ( $map as $old_id => $new_id ) {
		$old_id = (int) $old_id;
		$new_id = (int) $new_id;

		if ( $old_id <= 0 || $new_id <= 0 || $old_id === $new_id ) {
			continue;
		}

		$updated = preg_replace( '/("(?:id|mediaId)"\s*:\s*)' . $old_id . '\b/', '${1}' . $new_id, $text );

		if ( null !== $updated ) {
			$text = $updated;
		}

		$text = str_replace( 'wp-image-' . $old_id, 'wp-image-' . $new_id, $text );
		$text = str_replace( 'data-id="' . $old_id . '"', 'data-id="' . $new_id . '"', $text );
		$text = str_replace( "data-id='" . $old_id . "'", "data-id='" . $new_id . "'", $text );
	}

	return $text;
}

/**
 * Prepare content for the target environment.
 *
 * @param string $text               Content.
 * @param array  $site               content/site.json data.
 * @param string $target_uploads_url Current uploads base URL.
 * @param array  $id_map             Attachment ID map.
 * @param array  $nav_map            Navigation ID map.
 * @param string $target_theme       Active theme stylesheet.
 * @return string
 */
function seed_prepare_content( $text, $site, $target_uploads_url, $id_map, $nav_map, $target_theme ) {
	$text = seed_replace_urls( $text, $site, $target_uploads_url );
	$text = seed_remap_ids( $text, $id_map );

	$source_theme = isset( $site['theme'] ) ? (string) $site['theme'] : '';

	if ( '' !== $source_theme && $source_theme !== $target_theme ) {
		$pattern = '/("theme"\s*:\s*")' . preg_quote( $source_theme, '/' ) . '(")/';
		$updated = preg_replace( $pattern, '${1}' . $target_theme . '${2}', $text );

		if ( null !== $updated ) {
			$text = $updated;
		}
	}

	foreach ( $nav_map as $old_nav_id => $new_nav_id ) {
		$old_nav_id = (int) $old_nav_id;
		$new_nav_id = (int) $new_nav_id;

		if ( $old_nav_id <= 0 || $new_nav_id <= 0 || $old_nav_id === $new_nav_id ) {
			continue;
		}

		$updated = preg_replace( '/("ref"\s*:\s*)' . $old_nav_id . '\b/', '${1}' . $new_nav_id, $text );

		if ( null !== $updated ) {
			$text = $updated;
		}
	}

	return $text;
}

/**
 * Find a post by post type, slug, and optionally theme.
 *
 * @param string $post_type Post type.
 * @param string $slug      Post slug.
 * @param string $theme     Theme slug for theme-scoped post types.
 * @return WP_Post|null
 */
function seed_find_post( $post_type, $slug, $theme = '' ) {
	if ( '' === $slug ) {
		return null;
	}

	$args = array(
		'post_type'        => $post_type,
		'post_status'      => array( 'publish', 'draft', 'private', 'trash', 'auto-draft' ),
		'posts_per_page'   => -1,
		'orderby'          => 'ID',
		'order'            => 'ASC',
		'suppress_filters' => false,
	);

	if ( '' !== $theme && in_array( $post_type, array( 'wp_template', 'wp_template_part', 'wp_global_styles' ), true ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'wp_theme',
				'field'    => 'name',
				'terms'    => $theme,
			),
		);
	}

	foreach ( get_posts( $args ) as $post ) {
		if ( $post->post_name === $slug ) {
			return $post;
		}
	}

	return null;
}

/**
 * Find a page by slug without returning trashed pages.
 *
 * @param string $slug Page slug.
 * @return WP_Post|null
 */
function seed_find_page( $slug ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	foreach ( $pages as $page ) {
		if ( $page->post_name === $slug ) {
			return $page;
		}
	}

	return null;
}

/**
 * Detect a same-slug record owned by another block theme.
 *
 * @param string $post_type Post type.
 * @param string $slug      Slug.
 * @param string $theme     Target theme.
 * @return WP_Post|null
 */
function seed_find_slug_conflict( $post_type, $slug, $theme ) {
	$posts = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'draft', 'private', 'trash', 'auto-draft' ),
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	foreach ( $posts as $post ) {
		if ( $post->post_name !== $slug ) {
			continue;
		}

		$terms      = wp_get_object_terms( $post->ID, 'wp_theme', array( 'fields' => 'names' ) );
		$post_theme = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : '';

		if ( $post_theme !== $theme ) {
			return $post;
		}
	}

	return null;
}

/**
 * Find an attachment record by its uploads-relative file.
 *
 * @param string $relative_file Relative upload path.
 * @return WP_Post|null
 */
function seed_find_attachment( $relative_file ) {
	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_key'       => '_wp_attached_file',
			'meta_value'     => $relative_file,
		)
	);

	return ! empty( $attachments ) ? $attachments[0] : null;
}

/**
 * Create or update a theme-scoped post.
 *
 * @param string $post_type Post type.
 * @param string $slug      Post slug.
 * @param string $title     Post title.
 * @param string $content   Post content.
 * @param string $status    Post status.
 * @param string $theme     Theme slug for theme-scoped post types.
 * @param string $area      Template part area.
 * @param bool   $dry_run   Whether to skip writes.
 * @param array  $stats     Counters, by reference.
 * @return int Post ID, or 0 in a dry run when the post does not exist yet.
 */
function seed_upsert( $post_type, $slug, $title, $content, $status, $theme, $area, $dry_run, &$stats ) {
	$existing = seed_find_post( $post_type, $slug, $theme );

	if ( ! $existing ) {
		$conflict = seed_find_slug_conflict( $post_type, $slug, $theme );

		if ( $conflict ) {
			$terms          = wp_get_object_terms( $conflict->ID, 'wp_theme', array( 'fields' => 'names' ) );
			$conflict_theme = ( ! is_wp_error( $terms ) && ! empty( $terms ) ) ? $terms[0] : '(none)';

			seed_fail(
				'Slug "' . $slug . '" is already used by theme "' . $conflict_theme . '" for ' . $post_type .
				'. Remove or rename that database record before seeding.'
			);
		}
	}

	$post_data = array(
		'post_type'    => $post_type,
		'post_name'    => $slug,
		'post_title'   => '' !== (string) $title ? $title : $slug,
		'post_content' => $content,
		'post_status'  => $status,
	);

	$post_id = 0;

	if ( $existing ) {
		$unchanged = (
			$existing->post_content === $content &&
			$existing->post_title === (string) $title &&
			$existing->post_status === $status
		);

		if ( $dry_run ) {
			seed_log( '[dry-run] Would ' . ( $unchanged ? 'keep' : 'update' ) . ' ' . $post_type . '/' . $slug );
			$stats[ $unchanged ? 'unchanged' : 'updated' ] = ( $stats[ $unchanged ? 'unchanged' : 'updated' ] ?? 0 ) + 1;
			$post_id = (int) $existing->ID;
		} elseif ( $unchanged ) {
			seed_log( 'Unchanged ' . $post_type . '/' . $slug );
			$stats['unchanged'] = ( $stats['unchanged'] ?? 0 ) + 1;
			$post_id = (int) $existing->ID;
		} else {
			$post_data['ID'] = $existing->ID;
			$result          = wp_update_post( wp_slash( $post_data ), true );

			if ( is_wp_error( $result ) ) {
				seed_fail( 'Could not update ' . $post_type . '/' . $slug . ': ' . $result->get_error_message() );
			}

			seed_log( 'Updated ' . $post_type . '/' . $slug );
			$stats['updated'] = ( $stats['updated'] ?? 0 ) + 1;
			$post_id          = (int) $existing->ID;
		}
	} else {
		if ( $dry_run ) {
			seed_log( '[dry-run] Would create ' . $post_type . '/' . $slug );
			$stats['created'] = ( $stats['created'] ?? 0 ) + 1;
		} else {
			$post_id = wp_insert_post( wp_slash( $post_data ), true );

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				$message = is_wp_error( $post_id ) ? $post_id->get_error_message() : 'unknown error';
				seed_fail( 'Could not create ' . $post_type . '/' . $slug . ': ' . $message );
			}

			seed_log( 'Created ' . $post_type . '/' . $slug );
			$stats['created'] = ( $stats['created'] ?? 0 ) + 1;
			$post_id          = (int) $post_id;
		}
	}

	if ( $post_id > 0 && ! $dry_run ) {
		if ( '' !== $theme && in_array( $post_type, array( 'wp_template', 'wp_template_part', 'wp_global_styles' ), true ) ) {
			$term_result = wp_set_object_terms( $post_id, $theme, 'wp_theme' );

			if ( is_wp_error( $term_result ) ) {
				seed_warn( 'Could not assign theme term to ' . $post_type . '/' . $slug . ': ' . $term_result->get_error_message() );
			}
		}

		if ( 'wp_template_part' === $post_type && '' !== $area ) {
			$area_result = wp_set_object_terms( $post_id, $area, 'wp_template_part_area' );

			if ( is_wp_error( $area_result ) ) {
				seed_warn( 'Could not assign area to ' . $post_type . '/' . $slug . ': ' . $area_result->get_error_message() );
			}
		}
	}

	return $post_id;
}

/**
 * Create or match attachment records for the media manifest.
 *
 * @param array  $items      Media manifest.
 * @param array  $uploads    wp_upload_dir() result.
 * @param bool   $dry_run    Whether to skip writes.
 * @param bool   $skip_media Whether to only match existing attachments.
 * @param array  $stats      Counters, by reference.
 * @param array  $id_map     Old ID => new ID map, by reference.
 * @return void
 */
function seed_media( $items, $uploads, $dry_run, $skip_media, &$stats, &$id_map ) {
	foreach ( $items as $item ) {
		$source_id = (int) ( isset( $item['source_id'] ) ? $item['source_id'] : 0 );
		$relative  = ltrim( (string) ( isset( $item['file'] ) ? $item['file'] : '' ), '/\\' );

		if ( '' === $relative ) {
			seed_warn( 'Skipped media entry without a file path.' );
			$stats['media_skipped'] = ( $stats['media_skipped'] ?? 0 ) + 1;
			continue;
		}

		$existing = seed_find_attachment( $relative );

		if ( $existing && $source_id > 0 ) {
			$id_map[ $source_id ] = (int) $existing->ID;
		}

		$absolute_file = $uploads['basedir'] . '/' . $relative;

		if ( ! file_exists( $absolute_file ) ) {
			seed_warn( 'Media file missing, skipped: ' . $relative );
			$stats['media_skipped'] = ( $stats['media_skipped'] ?? 0 ) + 1;
			continue;
		}

		if ( $skip_media ) {
			$stats['media_skipped'] = ( $stats['media_skipped'] ?? 0 ) + 1;
			continue;
		}

		$title       = (string) ( isset( $item['title'] ) ? $item['title'] : '' );
		$caption     = (string) ( isset( $item['caption'] ) ? $item['caption'] : '' );
		$description = (string) ( isset( $item['description'] ) ? $item['description'] : '' );
		$alt         = (string) ( isset( $item['alt'] ) ? $item['alt'] : '' );
		$metadata    = ( isset( $item['metadata'] ) && is_array( $item['metadata'] ) ) ? $item['metadata'] : null;

		if ( $existing ) {
			$post_data = array(
				'ID'           => $existing->ID,
				'post_title'   => $title,
				'post_excerpt' => $caption,
				'post_content' => $description,
			);

			$unchanged = (
				$existing->post_title === $title &&
				$existing->post_excerpt === $caption &&
				$existing->post_content === $description
			);

			if ( $dry_run ) {
				seed_log( '[dry-run] Would ' . ( $unchanged ? 'keep' : 'update' ) . ' attachment/' . $relative );
				$stats[ $unchanged ? 'media_unchanged' : 'media_updated' ] = ( $stats[ $unchanged ? 'media_unchanged' : 'media_updated' ] ?? 0 ) + 1;
				continue;
			}

			if ( ! $unchanged ) {
				$update_result = wp_update_post( wp_slash( $post_data ), true );

				if ( is_wp_error( $update_result ) ) {
					seed_warn( 'Could not update attachment/' . $relative . ': ' . $update_result->get_error_message() );
					$stats['media_skipped'] = ( $stats['media_skipped'] ?? 0 ) + 1;
					continue;
				}

				seed_log( 'Updated attachment/' . $relative );
				$stats['media_updated'] = ( $stats['media_updated'] ?? 0 ) + 1;
			} else {
				$stats['media_unchanged'] = ( $stats['media_unchanged'] ?? 0 ) + 1;
			}

			update_post_meta( $existing->ID, '_wp_attachment_image_alt', $alt );

			if ( null !== $metadata ) {
				$current_metadata = wp_get_attachment_metadata( $existing->ID );

				if ( wp_json_encode( $current_metadata ) !== wp_json_encode( $metadata ) ) {
					wp_update_attachment_metadata( $existing->ID, $metadata );
				}
			}

			continue;
		}

		if ( $dry_run ) {
			seed_log( '[dry-run] Would create attachment/' . $relative );
			$stats['media_created'] = ( $stats['media_created'] ?? 0 ) + 1;
			continue;
		}

		$mime = (string) ( isset( $item['mime'] ) ? $item['mime'] : '' );

		if ( '' === $mime ) {
			$filetype = wp_check_filetype( $absolute_file );
			$mime     = (string) $filetype['type'];
		}

		$attachment_args = array(
			'post_mime_type' => $mime,
			'post_title'     => $title,
			'post_content'   => $description,
			'post_excerpt'   => $caption,
			'post_status'    => 'inherit',
			'menu_order'     => (int) ( isset( $item['menu_order'] ) ? $item['menu_order'] : 0 ),
			'guid'           => $uploads['baseurl'] . '/' . $relative,
		);

		if ( ! empty( $item['post_date'] ) ) {
			$attachment_args['post_date'] = $item['post_date'];
		}

		$attachment_id = wp_insert_attachment( $attachment_args, $absolute_file, 0, true );

		if ( is_wp_error( $attachment_id ) ) {
			seed_warn( 'Could not create attachment/' . $relative . ': ' . $attachment_id->get_error_message() );
			$stats['media_skipped'] = ( $stats['media_skipped'] ?? 0 ) + 1;
			continue;
		}

		update_attached_file( $attachment_id, $absolute_file );

		$generated_metadata = null !== $metadata ? $metadata : wp_generate_attachment_metadata( $attachment_id, $absolute_file );

		if ( ! empty( $generated_metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $generated_metadata );
		}

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );

		if ( $source_id > 0 ) {
			$id_map[ $source_id ] = (int) $attachment_id;
		}

		seed_log( 'Created attachment/' . $relative );
		$stats['media_created'] = ( $stats['media_created'] ?? 0 ) + 1;
	}
}

/**
 * Seed pages from content/pages/manifest.json.
 *
 * Existing pages are not modified unless --update-pages is used.
 *
 * @param array    $manifest        Page manifest.
 * @param string   $pages_dir       Pages directory.
 * @param callable $prepare_content Content preparation callback.
 * @param bool     $dry_run         Whether to skip writes.
 * @param bool     $update_pages    Whether existing pages may be updated.
 * @param array    $stats           Counters, by reference.
 * @return array Page slug => page ID.
 */
function seed_pages( $manifest, $pages_dir, $prepare_content, $dry_run, $update_pages, &$stats ) {
	$page_ids = array();
	$written  = array();

	foreach ( $manifest as $entry ) {
		$slug = (string) ( isset( $entry['slug'] ) ? $entry['slug'] : '' );

		if ( '' === $slug ) {
			continue;
		}

		$file    = $pages_dir . '/' . ( isset( $entry['file'] ) ? $entry['file'] : $slug . '.html' );
		$content = $prepare_content( seed_read_text( $file ) );
		$existing = seed_find_page( $slug );

		$page_data = array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => '' !== (string) ( isset( $entry['title'] ) ? $entry['title'] : '' ) ? $entry['title'] : $slug,
			'post_content' => $content,
			'post_status'  => isset( $entry['status'] ) ? $entry['status'] : 'publish',
			'menu_order'   => (int) ( isset( $entry['menu_order'] ) ? $entry['menu_order'] : 0 ),
		);

		if ( $existing ) {
			$page_ids[ $slug ] = (int) $existing->ID;

			if ( ! $update_pages ) {
				seed_log( 'Skipped existing page/' . $slug . ' (use --update-pages to overwrite)' );
				$stats['pages_skipped'] = ( $stats['pages_skipped'] ?? 0 ) + 1;
				continue;
			}

			if ( $dry_run ) {
				seed_log( '[dry-run] Would update page/' . $slug );
				$stats['pages_updated'] = ( $stats['pages_updated'] ?? 0 ) + 1;
				$written[ $slug ]       = true;
				continue;
			}

			$page_data['ID'] = $existing->ID;
			$result          = wp_update_post( wp_slash( $page_data ), true );

			if ( is_wp_error( $result ) ) {
				seed_fail( 'Could not update page/' . $slug . ': ' . $result->get_error_message() );
			}

			seed_log( 'Updated page/' . $slug );
			$stats['pages_updated'] = ( $stats['pages_updated'] ?? 0 ) + 1;
			$written[ $slug ]       = true;
		} else {
			if ( $dry_run ) {
				seed_log( '[dry-run] Would create page/' . $slug );
				$stats['pages_created'] = ( $stats['pages_created'] ?? 0 ) + 1;
				continue;
			}

			$page_id = wp_insert_post( wp_slash( $page_data ), true );

			if ( is_wp_error( $page_id ) || ! $page_id ) {
				$message = is_wp_error( $page_id ) ? $page_id->get_error_message() : 'unknown error';
				seed_fail( 'Could not create page/' . $slug . ': ' . $message );
			}

			$page_ids[ $slug ] = (int) $page_id;
			$written[ $slug ]  = true;
			seed_log( 'Created page/' . $slug );
			$stats['pages_created'] = ( $stats['pages_created'] ?? 0 ) + 1;
		}

		if ( ! $dry_run && ! empty( $page_ids[ $slug ] ) ) {
			$template = isset( $entry['template'] ) ? (string) $entry['template'] : '';

			if ( '' !== $template ) {
				update_post_meta( $page_ids[ $slug ], '_wp_page_template', $template );
			} else {
				delete_post_meta( $page_ids[ $slug ], '_wp_page_template' );
			}
		}
	}

	foreach ( $manifest as $entry ) {
		$slug        = (string) ( isset( $entry['slug'] ) ? $entry['slug'] : '' );
		$parent_slug = (string) ( isset( $entry['parent_slug'] ) ? $entry['parent_slug'] : '' );

		if ( '' === $slug || '' === $parent_slug || empty( $written[ $slug ] ) || empty( $page_ids[ $slug ] ) || empty( $page_ids[ $parent_slug ] ) ) {
			continue;
		}

		if ( $dry_run ) {
			seed_log( '[dry-run] Would set parent of page/' . $slug . ' to page/' . $parent_slug );
			continue;
		}

		wp_update_post(
			array(
				'ID'          => $page_ids[ $slug ],
				'post_parent' => $page_ids[ $parent_slug ],
			)
		);
	}

	return $page_ids;
}

/**
 * Update the selected site options.
 *
 * @param array $options content/options.json data.
 * @param array $id_map  Attachment ID map.
 * @param bool  $dry_run Whether to skip writes.
 * @param array $stats   Counters, by reference.
 * @return void
 */
function seed_options( $options, $id_map, $dry_run, &$stats ) {
	$set_option = function ( $name, $value ) use ( $dry_run, &$stats ) {
		$current = get_option( $name );

		if ( (string) $current === (string) $value ) {
			seed_log( 'Unchanged option ' . $name );
			$stats['options_unchanged'] = ( $stats['options_unchanged'] ?? 0 ) + 1;
			return;
		}

		if ( $dry_run ) {
			seed_log( '[dry-run] Would update option ' . $name . ': ' . var_export( $current, true ) . ' -> ' . var_export( $value, true ) );
			$stats['options_updated'] = ( $stats['options_updated'] ?? 0 ) + 1;
			return;
		}

		update_option( $name, $value );
		seed_log( 'Updated option ' . $name );
		$stats['options_updated'] = ( $stats['options_updated'] ?? 0 ) + 1;
	};

	foreach ( array( 'blogname', 'blogdescription', 'show_on_front' ) as $option_name ) {
		if ( array_key_exists( $option_name, $options ) ) {
			$set_option( $option_name, $options[ $option_name ] );
		}
	}

	$front_page_slug = array_key_exists( 'page_on_front_slug', $options ) ? $options['page_on_front_slug'] : null;

	if ( ! empty( $front_page_slug ) ) {
		$front_page = seed_find_page( (string) $front_page_slug );

		if ( $front_page ) {
			$set_option( 'page_on_front', (int) $front_page->ID );
		} else {
			seed_warn( 'page_on_front_slug "' . $front_page_slug . '" was not found; leaving page_on_front unchanged.' );
		}
	} else {
		$set_option( 'page_on_front', 0 );
	}

	$posts_page_slug = array_key_exists( 'page_for_posts_slug', $options ) ? $options['page_for_posts_slug'] : null;

	if ( ! empty( $posts_page_slug ) ) {
		$posts_page = seed_find_page( (string) $posts_page_slug );

		if ( $posts_page ) {
			$set_option( 'page_for_posts', (int) $posts_page->ID );
		} else {
			seed_warn( 'page_for_posts_slug "' . $posts_page_slug . '" was not found; leaving page_for_posts unchanged.' );
		}
	} else {
		$set_option( 'page_for_posts', 0 );
	}

	$logo_source_id = array_key_exists( 'site_logo_source_id', $options ) ? (int) $options['site_logo_source_id'] : 0;

	if ( $logo_source_id > 0 ) {
		if ( ! empty( $id_map[ $logo_source_id ] ) ) {
			$set_option( 'site_logo', (int) $id_map[ $logo_source_id ] );
		} else {
			seed_warn( 'site_logo attachment ID ' . $logo_source_id . ' was not matched; leaving site_logo unchanged.' );
		}
	} else {
		$set_option( 'site_logo', 0 );
	}

	$icon_source_id = array_key_exists( 'site_icon_source_id', $options ) ? (int) $options['site_icon_source_id'] : 0;

	if ( $icon_source_id > 0 ) {
		if ( ! empty( $id_map[ $icon_source_id ] ) ) {
			$set_option( 'site_icon', (int) $id_map[ $icon_source_id ] );
		} else {
			seed_warn( 'site_icon attachment ID ' . $icon_source_id . ' was not matched; leaving site_icon unchanged.' );
		}
	} else {
		$set_option( 'site_icon', 0 );
	}

	if ( array_key_exists( 'privacy_policy_slug', $options ) && ! empty( $options['privacy_policy_slug'] ) ) {
		$privacy_page = seed_find_page( (string) $options['privacy_policy_slug'] );

		if ( $privacy_page ) {
			$set_option( 'wp_page_for_privacy_policy', (int) $privacy_page->ID );
		} else {
			seed_warn( 'privacy_policy_slug "' . $options['privacy_policy_slug'] . '" was not found; leaving wp_page_for_privacy_policy unchanged.' );
		}
	}
}

/* -------------------------------------------------------------------------
 * Run
 * ---------------------------------------------------------------------- */

$seed_site  = seed_read_json( $seed_content_dir . '/site.json' );
$seed_theme = get_stylesheet();

if ( ! $seed_allow_theme_mismatch && isset( $seed_site['theme'] ) && $seed_site['theme'] !== $seed_theme ) {
	seed_fail(
		'Active theme "' . $seed_theme . '" does not match content/site.json theme "' . $seed_site['theme'] .
		'". Activate the correct theme or pass --allow-theme-mismatch.'
	);
}

$seed_uploads = wp_upload_dir();

if ( ! empty( $seed_uploads['error'] ) ) {
	seed_fail( 'Uploads directory error: ' . $seed_uploads['error'] );
}

$seed_target_uploads_url = seed_untrail( $seed_uploads['baseurl'] );
$seed_id_map             = array();
$seed_nav_map            = array();
$seed_stats              = array();

seed_log( 'Seeding content from: ' . $seed_content_dir );
seed_log( 'Target theme: ' . $seed_theme );
seed_log( 'Target URL: ' . home_url( '/' ) );

if ( $seed_dry_run ) {
	seed_log( 'Dry run: no database writes will be made.' );
}

$seed_prepare = function ( $text ) use ( $seed_site, $seed_target_uploads_url, &$seed_id_map, &$seed_nav_map, $seed_theme ) {
	return seed_prepare_content( $text, $seed_site, $seed_target_uploads_url, $seed_id_map, $seed_nav_map, $seed_theme );
};

/*
 * Imports contain block comments and JSON attributes. Remove KSES while the
 * seed writes posts so the markup is preserved, then restore it at the end.
 */
kses_remove_filters();

add_filter(
	'wp_revisions_to_keep',
	function ( $keep, $post ) {
		if ( is_object( $post ) && in_array( $post->post_type, array( 'wp_template', 'wp_template_part', 'wp_navigation', 'wp_global_styles' ), true ) ) {
			return 0;
		}

		return $keep;
	},
	10,
	2
);

/* Media first: templates, pages, and options depend on the ID map. */
$seed_media_manifest = seed_read_json( $seed_content_dir . '/media/manifest.json', false );

if ( ! empty( $seed_media_manifest ) ) {
	seed_log( 'Media entries: ' . count( $seed_media_manifest ) );
	seed_media( $seed_media_manifest, $seed_uploads, $seed_dry_run, $seed_skip_media, $seed_stats, $seed_id_map );
	seed_log( 'Media ID mappings: ' . count( $seed_id_map ) );
}

/* Global styles. */
$seed_global_styles_file = $seed_content_dir . '/global-styles.json';

if ( file_exists( $seed_global_styles_file ) ) {
	$seed_global_styles = seed_read_json( $seed_global_styles_file, false );

	if ( ! empty( $seed_global_styles['content'] ) ) {
		seed_upsert(
			'wp_global_styles',
			'wp-global-styles-' . $seed_theme,
			isset( $seed_global_styles['title'] ) ? $seed_global_styles['title'] : 'Custom Styles',
			$seed_prepare( (string) $seed_global_styles['content'] ),
			isset( $seed_global_styles['status'] ) ? $seed_global_styles['status'] : 'publish',
			$seed_theme,
			'',
			$seed_dry_run,
			$seed_stats
		);
	}
}

/* Navigation. */
$seed_navigation_manifest = seed_read_json( $seed_content_dir . '/navigation/manifest.json', false );

foreach ( $seed_navigation_manifest as $seed_navigation ) {
	$seed_navigation_slug = (string) ( isset( $seed_navigation['slug'] ) ? $seed_navigation['slug'] : '' );

	if ( '' === $seed_navigation_slug ) {
		continue;
	}

	$seed_navigation_file    = $seed_content_dir . '/navigation/' . ( isset( $seed_navigation['file'] ) ? $seed_navigation['file'] : $seed_navigation_slug . '.html' );
	$seed_navigation_content = $seed_prepare( seed_read_text( $seed_navigation_file ) );
	$seed_navigation_id      = seed_upsert(
		'wp_navigation',
		$seed_navigation_slug,
		isset( $seed_navigation['title'] ) ? $seed_navigation['title'] : $seed_navigation_slug,
		$seed_navigation_content,
		isset( $seed_navigation['status'] ) ? $seed_navigation['status'] : 'publish',
		'',
		'',
		$seed_dry_run,
		$seed_stats
	);

	if ( ! empty( $seed_navigation['source_id'] ) && $seed_navigation_id > 0 ) {
		$seed_nav_map[ (int) $seed_navigation['source_id'] ] = (int) $seed_navigation_id;
	}
}

/* Templates and parts. */
foreach ( array( 'templates' => 'wp_template', 'parts' => 'wp_template_part' ) as $seed_folder => $seed_post_type ) {
	$seed_manifest = seed_read_json( $seed_content_dir . '/' . $seed_folder . '/manifest.json', false );

	foreach ( $seed_manifest as $seed_entry ) {
		$seed_slug = (string) ( isset( $seed_entry['slug'] ) ? $seed_entry['slug'] : '' );

		if ( '' === $seed_slug ) {
			continue;
		}

		$seed_file    = $seed_content_dir . '/' . $seed_folder . '/' . ( isset( $seed_entry['file'] ) ? $seed_entry['file'] : $seed_slug . '.html' );
		$seed_content = $seed_prepare( seed_read_text( $seed_file ) );

		seed_upsert(
			$seed_post_type,
			$seed_slug,
			isset( $seed_entry['title'] ) ? $seed_entry['title'] : $seed_slug,
			$seed_content,
			isset( $seed_entry['status'] ) ? $seed_entry['status'] : 'publish',
			$seed_theme,
			isset( $seed_entry['area'] ) ? $seed_entry['area'] : '',
			$seed_dry_run,
			$seed_stats
		);
	}
}

/* Pages. */
if ( ! $seed_skip_pages ) {
	$seed_pages_manifest = seed_read_json( $seed_content_dir . '/pages/manifest.json', false );

	if ( ! empty( $seed_pages_manifest ) ) {
		seed_pages(
			$seed_pages_manifest,
			$seed_content_dir . '/pages',
			$seed_prepare,
			$seed_dry_run,
			$seed_update_pages,
			$seed_stats
		);
	}
}

/* Options last, so page/attachment IDs are available. */
$seed_options = seed_read_json( $seed_content_dir . '/options.json', false );

if ( ! empty( $seed_options ) ) {
	seed_options( $seed_options, $seed_id_map, $seed_dry_run, $seed_stats );
}

if ( $seed_flush && ! $seed_dry_run ) {
	flush_rewrite_rules( false );
	seed_log( 'Flushed rewrite rules.' );
}

kses_init_filters();

seed_log( '' );
seed_log( 'Seed complete.' );

$seed_labels = array(
	'created'           => 'Created',
	'updated'           => 'Updated',
	'unchanged'         => 'Unchanged',
	'media_created'     => 'Media created',
	'media_updated'     => 'Media updated',
	'media_unchanged'   => 'Media unchanged',
	'media_skipped'     => 'Media skipped',
	'pages_created'     => 'Pages created',
	'pages_updated'     => 'Pages updated',
	'pages_skipped'     => 'Pages skipped',
	'options_updated'   => 'Options updated',
	'options_unchanged' => 'Options unchanged',
);

foreach ( $seed_labels as $seed_key => $seed_label ) {
	if ( ! empty( $seed_stats[ $seed_key ] ) ) {
		seed_log( sprintf( '  %-18s %d', $seed_label . ':', $seed_stats[ $seed_key ] ) );
	}
}

if ( $seed_dry_run ) {
	seed_log( 'Dry run: no changes were written.' );
}
