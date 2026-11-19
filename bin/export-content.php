<?php
/**
 * Export database-stored block theme content into version-controlled files.
 *
 * Run from the WordPress root:
 *   php bin/export-content.php [--out=DIR] [--theme=SLUG]
 *
 * The script only reads the database. It writes under content/ by default.
 */

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 403 );
	exit( "CLI only.\n" );
}

if ( ! defined( 'ABSPATH' ) ) {
	require_once dirname( __DIR__ ) . '/wp-load.php';
}

$export_root     = dirname( __DIR__ );
$content_dir     = $export_root . '/content';
$export_theme    = get_stylesheet();
$export_output_theme = $export_theme;
$export_counts   = array();
$export_warnings = array();

$export_args = isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] )
	? array_slice( $_SERVER['argv'], 1 )
	: array();

for ( $export_arg_index = 0; $export_arg_index < count( $export_args ); $export_arg_index++ ) {
	$export_arg = (string) $export_args[ $export_arg_index ];

	if ( '--help' === $export_arg || '-h' === $export_arg ) {
		export_print_usage();
		exit( 0 );
	}

	if ( 0 === strpos( $export_arg, '--out=' ) ) {
		$content_dir = substr( $export_arg, strlen( '--out=' ) );
		continue;
	}

	if ( '--out' === $export_arg && isset( $export_args[ $export_arg_index + 1 ] ) ) {
		$content_dir = (string) $export_args[ ++$export_arg_index ];
		continue;
	}

	if ( 0 === strpos( $export_arg, '--theme=' ) ) {
		$export_output_theme = substr( $export_arg, strlen( '--theme=' ) );
		continue;
	}

	if ( '--theme' === $export_arg && isset( $export_args[ $export_arg_index + 1 ] ) ) {
		$export_output_theme = (string) $export_args[ ++$export_arg_index ];
		continue;
	}

	export_fail( 'Unknown option: ' . $export_arg );
}

if ( '' === trim( $content_dir ) ) {
	export_fail( 'The output directory cannot be empty.' );
}

if ( ! preg_match( '/^[a-z0-9_-]+$/i', $export_output_theme ) ) {
	export_fail( 'Invalid output theme slug: ' . $export_output_theme );
}

if ( ! preg_match( '~^(?:/|[A-Za-z]:[\\\\/]|\\\\)~', $content_dir ) ) {
	$content_dir = $export_root . '/' . ltrim( $content_dir, '/\\' );
}

$content_dir = rtrim( str_replace( '\\', '/', $content_dir ), '/' );

/**
 * Print exporter usage.
 *
 * @return void
 */
function export_print_usage() {
	echo "Usage: php bin/export-content.php [--out=DIR] [--theme=SLUG]\n\n";
	echo "  --out=DIR     Write content to DIR instead of ./content.\n";
	echo "  --theme=SLUG  Write site.json and block theme references for SLUG.\n";
	echo "                Use this when exporting a site into a template that uses\n";
	echo "                a different theme folder name, e.g. basic-site-template.\n";
	echo "  --help        Show this help.\n";
}

/**
 * Print a progress line.
 *
 * @param string $message Message.
 * @return void
 */
function export_log( $message ) {
	echo $message . "\n";
}

/**
 * Stop the export with an error.
 *
 * @param string $message Message.
 * @return void
 */
function export_fail( $message ) {
	fwrite( STDERR, 'ERROR: ' . $message . "\n" );
	exit( 1 );
}

/**
 * Create a directory when needed.
 *
 * @param string $dir Directory path.
 * @return void
 */
function export_ensure_dir( $dir ) {
	if ( is_dir( $dir ) ) {
		return;
	}

	if ( ! wp_mkdir_p( $dir ) ) {
		export_fail( 'Could not create directory: ' . $dir );
	}
}

/**
 * Write a file and log it.
 *
 * @param string $path     File path.
 * @param string $contents File contents.
 * @return void
 */
function export_write( $path, $contents ) {
	if ( false === file_put_contents( $path, $contents ) ) {
		export_fail( 'Could not write: ' . $path );
	}

	export_log( 'Wrote ' . str_replace( dirname( __DIR__ ) . '/', '', $path ) );
}

/**
 * Encode data as readable JSON.
 *
 * @param mixed $data Data.
 * @return string
 */
function export_json( $data ) {
	$json = wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

	if ( false === $json ) {
		export_fail( 'JSON encoding failed.' );
	}

	return $json . "\n";
}

/**
 * Build a safe slug, falling back to the post ID.
 *
 * @param string $slug     Raw slug.
 * @param int    $fallback Fallback post ID.
 * @return string
 */
function export_slug( $slug, $fallback ) {
	$slug = sanitize_title( (string) $slug );

	return '' !== $slug ? $slug : 'item-' . (int) $fallback;
}

/**
 * Return posts of a type assigned to one block theme.
 *
 * @param string $post_type Post type.
 * @param string $theme     Theme stylesheet.
 * @return WP_Post[]
 */
function export_theme_posts( $post_type, $theme ) {
	return get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'orderby'        => 'post_name',
			'order'          => 'ASC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'wp_theme',
					'field'    => 'name',
					'terms'    => $theme,
				),
			),
		)
	);
}

/**
 * Retarget explicit theme attributes in block markup.
 *
 * @param string $content       Block markup.
 * @param string $source_theme  Source theme slug.
 * @param string $target_theme  Target theme slug.
 * @return string
 */
function export_retarget_markup( $content, $source_theme, $target_theme ) {
	if ( $source_theme === $target_theme ) {
		return $content;
	}

	return preg_replace_callback(
		'/("theme"\s*:\s*")' . preg_quote( $source_theme, '/' ) . '(")/',
		function ( $matches ) use ( $target_theme ) {
			return $matches[1] . $target_theme . $matches[2];
		},
		$content
	);
}

/**
 * Return a page slug when the page exists and is not trashed.
 *
 * @param int $page_id Page ID.
 * @return string|null
 */
function export_page_slug( $page_id ) {
	$page_id = (int) $page_id;

	if ( $page_id <= 0 ) {
		return null;
	}

	$page = get_post( $page_id );

	if ( ! $page || 'page' !== $page->post_type || 'trash' === $page->post_status || '' === $page->post_name ) {
		return null;
	}

	return $page->post_name;
}

export_log( 'Exporting content for theme: ' . $export_theme );

export_ensure_dir( $content_dir );

$uploads = wp_upload_dir();

if ( ! empty( $uploads['error'] ) ) {
	export_fail( 'Uploads directory error: ' . $uploads['error'] );
}

export_write(
	$content_dir . '/site.json',
	export_json(
		array(
			'theme'              => $export_output_theme,
			'source_url'         => untrailingslashit( home_url( '/' ) ),
			'source_uploads_url' => untrailingslashit( $uploads['baseurl'] ),
		)
	)
);

/* -------------------------------------------------------------------------
 * Templates
 * ---------------------------------------------------------------------- */

export_ensure_dir( $content_dir . '/templates' );

$template_manifest = array();

foreach ( export_theme_posts( 'wp_template', $export_theme ) as $template_post ) {
	$template_slug = export_slug( $template_post->post_name, $template_post->ID );
	$template_file = $template_slug . '.html';

	export_write( $content_dir . '/templates/' . $template_file, export_retarget_markup( $template_post->post_content, $export_theme, $export_output_theme ) );

	$template_manifest[] = array(
		'slug'      => $template_slug,
		'title'     => $template_post->post_title,
		'status'    => $template_post->post_status,
		'source_id' => (int) $template_post->ID,
		'file'      => $template_file,
	);
}

export_write( $content_dir . '/templates/manifest.json', export_json( $template_manifest ) );
$export_counts['templates'] = count( $template_manifest );

/* -------------------------------------------------------------------------
 * Template parts
 * ---------------------------------------------------------------------- */

export_ensure_dir( $content_dir . '/parts' );

$part_manifest = array();

foreach ( export_theme_posts( 'wp_template_part', $export_theme ) as $part_post ) {
	$part_slug = export_slug( $part_post->post_name, $part_post->ID );
	$part_file = $part_slug . '.html';
	$areas     = wp_get_object_terms( $part_post->ID, 'wp_template_part_area', array( 'fields' => 'names' ) );
	$area      = ( ! is_wp_error( $areas ) && ! empty( $areas ) ) ? $areas[0] : 'uncategorized';

	export_write( $content_dir . '/parts/' . $part_file, export_retarget_markup( $part_post->post_content, $export_theme, $export_output_theme ) );

	$part_manifest[] = array(
		'slug'      => $part_slug,
		'title'     => $part_post->post_title,
		'status'    => $part_post->post_status,
		'area'      => $area,
		'source_id' => (int) $part_post->ID,
		'file'      => $part_file,
	);
}

export_write( $content_dir . '/parts/manifest.json', export_json( $part_manifest ) );
$export_counts['parts'] = count( $part_manifest );

/* -------------------------------------------------------------------------
 * Navigation
 * ---------------------------------------------------------------------- */

export_ensure_dir( $content_dir . '/navigation' );

$navigation_manifest = array();
$navigation_posts    = get_posts(
	array(
		'post_type'      => 'wp_navigation',
		'post_status'    => array( 'publish', 'draft', 'private' ),
		'posts_per_page' => -1,
		'orderby'        => 'post_name',
		'order'          => 'ASC',
	)
);

foreach ( $navigation_posts as $navigation_post ) {
	$navigation_slug = export_slug( $navigation_post->post_name, $navigation_post->ID );
	$navigation_file = $navigation_slug . '.html';

	export_write( $content_dir . '/navigation/' . $navigation_file, $navigation_post->post_content );

	$navigation_manifest[] = array(
		'slug'      => $navigation_slug,
		'title'     => $navigation_post->post_title,
		'status'    => $navigation_post->post_status,
		'source_id' => (int) $navigation_post->ID,
		'file'      => $navigation_file,
	);
}

export_write( $content_dir . '/navigation/manifest.json', export_json( $navigation_manifest ) );
$export_counts['navigation'] = count( $navigation_manifest );

/* -------------------------------------------------------------------------
 * Pages
 * ---------------------------------------------------------------------- */

export_ensure_dir( $content_dir . '/pages' );

$page_manifest = array();
$page_posts    = get_posts(
	array(
		'post_type'      => 'page',
		'post_status'    => array( 'publish', 'draft', 'private' ),
		'posts_per_page' => -1,
		'orderby'        => 'post_name',
		'order'          => 'ASC',
	)
);

foreach ( $page_posts as $page_post ) {
	if ( '' === $page_post->post_name ) {
		$export_warnings[] = 'Skipped page without a slug: ' . $page_post->post_title . ' (ID ' . $page_post->ID . ')';
		continue;
	}

	$page_slug = export_slug( $page_post->post_name, $page_post->ID );
	$page_file = $page_slug . '.html';

	export_write( $content_dir . '/pages/' . $page_file, $page_post->post_content );

	$page_manifest[] = array(
		'slug'        => $page_slug,
		'title'       => $page_post->post_title,
		'status'      => $page_post->post_status,
		'source_id'   => (int) $page_post->ID,
		'menu_order'  => (int) $page_post->menu_order,
		'template'    => (string) get_post_meta( $page_post->ID, '_wp_page_template', true ),
		'parent_slug' => $page_post->post_parent ? export_page_slug( $page_post->post_parent ) : null,
		'file'        => $page_file,
	);
}

export_write( $content_dir . '/pages/manifest.json', export_json( $page_manifest ) );
$export_counts['pages'] = count( $page_manifest );

/* -------------------------------------------------------------------------
 * Media
 * ---------------------------------------------------------------------- */

export_ensure_dir( $content_dir . '/media' );

$media_manifest = array();
$attachment_posts = get_posts(
	array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => -1,
		'orderby'        => 'ID',
		'order'          => 'ASC',
	)
);

foreach ( $attachment_posts as $attachment_post ) {
	$attached_file = (string) get_post_meta( $attachment_post->ID, '_wp_attached_file', true );

	if ( '' === $attached_file ) {
		$export_warnings[] = 'Skipped attachment without _wp_attached_file: ID ' . $attachment_post->ID;
		continue;
	}

	$absolute_file = $uploads['basedir'] . '/' . ltrim( $attached_file, '/\\' );

	if ( ! file_exists( $absolute_file ) ) {
		$export_warnings[] = 'Missing media file for attachment ID ' . $attachment_post->ID . ': ' . $attached_file;
	}

	$metadata = wp_get_attachment_metadata( $attachment_post->ID );

	$media_manifest[] = array(
		'source_id'   => (int) $attachment_post->ID,
		'file'        => $attached_file,
		'title'       => $attachment_post->post_title,
		'caption'     => $attachment_post->post_excerpt,
		'description' => $attachment_post->post_content,
		'mime'        => $attachment_post->post_mime_type,
		'alt'         => (string) get_post_meta( $attachment_post->ID, '_wp_attachment_image_alt', true ),
		'metadata'    => is_array( $metadata ) ? $metadata : null,
		'post_date'   => $attachment_post->post_date,
		'menu_order'  => (int) $attachment_post->menu_order,
	);
}

export_write( $content_dir . '/media/manifest.json', export_json( $media_manifest ) );
$export_counts['media'] = count( $media_manifest );

/* -------------------------------------------------------------------------
 * Options
 * ---------------------------------------------------------------------- */

export_write(
	$content_dir . '/options.json',
	export_json(
		array(
			'blogname'            => get_option( 'blogname' ),
			'blogdescription'     => get_option( 'blogdescription' ),
			'show_on_front'       => get_option( 'show_on_front' ),
			'page_on_front_slug'  => export_page_slug( get_option( 'page_on_front' ) ),
			'page_for_posts_slug' => export_page_slug( get_option( 'page_for_posts' ) ),
			'site_logo_source_id' => (int) get_option( 'site_logo' ),
			'site_icon_source_id' => (int) get_option( 'site_icon' ),
			'privacy_policy_slug' => export_page_slug( get_option( 'wp_page_for_privacy_policy' ) ),
		)
	)
);

/* -------------------------------------------------------------------------
 * Global styles
 * ---------------------------------------------------------------------- */

$global_styles_posts = export_theme_posts( 'wp_global_styles', $export_theme );

if ( ! empty( $global_styles_posts ) ) {
	$global_styles_post = $global_styles_posts[0];

	export_write(
		$content_dir . '/global-styles.json',
		export_json(
			array(
				'slug'      => 'wp-global-styles-' . $export_output_theme,
				'title'     => $global_styles_post->post_title,
				'status'    => $global_styles_post->post_status,
				'source_id' => (int) $global_styles_post->ID,
				'content'   => $global_styles_post->post_content,
			)
		)
	);

	$export_counts['global_styles'] = 1;
}

export_log( '' );
export_log( 'Export complete for theme: ' . $export_theme );

if ( $export_output_theme !== $export_theme ) {
	export_log( 'Output theme slug: ' . $export_output_theme );
}

foreach ( $export_counts as $export_label => $export_count ) {
	export_log( sprintf( '  %-13s %d', $export_label . ':', $export_count ) );
}

export_log( 'Content directory: ' . $content_dir );

if ( ! empty( $export_warnings ) ) {
	export_log( '' );
	export_log( 'Warnings:' );

	foreach ( $export_warnings as $export_warning ) {
		export_log( '  - ' . $export_warning );
	}
}
