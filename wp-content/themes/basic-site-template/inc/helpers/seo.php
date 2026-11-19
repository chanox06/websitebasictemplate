<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return true when a dedicated SEO plugin appears to own document metadata.
 */
function pincoder_seo_is_managed_externally() {
    return function_exists( 'wpseo_head' )
        || defined( 'WPSEO_VERSION' )
        || class_exists( 'RankMath' )
        || defined( 'RANK_MATH_VERSION' )
        || defined( 'AIOSEO_VERSION' )
        || defined( 'SEOPRESS_VERSION' );
}

/**
 * Return a concise description for the current document.
 */
function pincoder_seo_get_description() {
    $configured_description = function_exists( 'pincoder_get_setting' )
        ? pincoder_get_setting( 'seo_description', pincoder_site_value( 'description', get_bloginfo( 'description' ) ) )
        : pincoder_site_value( 'description', get_bloginfo( 'description' ) );

    if ( is_singular() ) {
        $excerpt = get_the_excerpt( get_queried_object_id() );

        if ( $excerpt ) {
            return wp_trim_words( wp_strip_all_tags( $excerpt ), 32, '…' );
        }
    }

    return wp_trim_words( wp_strip_all_tags( $configured_description ), 32, '…' );
}

/**
 * Return the best available social/share image URL.
 */
function pincoder_seo_get_image_url() {
    $configured_image = function_exists( 'pincoder_get_setting' )
        ? esc_url_raw( pincoder_get_setting( 'seo_social_image', '' ) )
        : '';

    if ( $configured_image ) {
        return $configured_image;
    }

    if ( is_singular() && has_post_thumbnail() ) {
        $featured_image = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );

        if ( $featured_image ) {
            return $featured_image;
        }
    }

    $logo_id = get_theme_mod( 'custom_logo' );

    if ( $logo_id ) {
        $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );

        if ( $logo_url ) {
            return $logo_url;
        }
    }

    $default_image_path = get_template_directory() . '/images/social/default-og.jpg';

    return file_exists( $default_image_path )
        ? get_template_directory_uri() . '/images/social/default-og.jpg'
        : '';
}

/**
 * Output basic SEO metadata when no dedicated SEO plugin is active.
 */
function pincoder_output_basic_seo() {
    if ( is_admin() || is_feed() || is_trackback() || wp_doing_ajax() || pincoder_seo_is_managed_externally() ) {
        return;
    }

    $site_name  = get_bloginfo( 'name' ) ?: pincoder_site_value( 'company_name', '' );
    $title      = wp_get_document_title();
    $description = pincoder_seo_get_description();
    $url        = is_front_page() ? home_url( '/' ) : ( is_singular() ? get_permalink() : get_pagenum_link() );
    $image      = pincoder_seo_get_image_url();
    $og_type    = is_singular( 'post' ) ? 'article' : 'website';

    if ( $description ) {
        printf( "\n<meta name=\"description\" content=\"%s\">", esc_attr( $description ) );
    }

    if ( $url ) {
        printf( "\n<link rel=\"canonical\" href=\"%s\">", esc_url( $url ) );
    }

    printf( "\n<meta property=\"og:type\" content=\"%s\">", esc_attr( $og_type ) );
    printf( "\n<meta property=\"og:site_name\" content=\"%s\">", esc_attr( $site_name ) );
    printf( "\n<meta property=\"og:title\" content=\"%s\">", esc_attr( $title ) );
    printf( "\n<meta property=\"og:description\" content=\"%s\">", esc_attr( $description ) );
    printf( "\n<meta property=\"og:url\" content=\"%s\">", esc_url( $url ) );
    printf( "\n<meta name=\"twitter:card\" content=\"%s\">", esc_attr( $image ? 'summary_large_image' : 'summary' ) );
    printf( "\n<meta name=\"twitter:title\" content=\"%s\">", esc_attr( $title ) );
    printf( "\n<meta name=\"twitter:description\" content=\"%s\">", esc_attr( $description ) );

    if ( $image ) {
        printf( "\n<meta property=\"og:image\" content=\"%s\">", esc_url( $image ) );
        printf( "\n<meta name=\"twitter:image\" content=\"%s\">", esc_url( $image ) );
    }

    echo "\n";
}
add_action( 'wp_head', 'pincoder_output_basic_seo', 1 );