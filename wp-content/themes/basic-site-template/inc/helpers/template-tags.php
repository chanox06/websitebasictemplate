<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render a page using the existing shortcode/PHP-page compatibility routes.
 *
 * This keeps current client pages working while later Gutenberg phases move
 * content into normal WordPress page content and reusable patterns.
 */
function pincoder_render_legacy_page_content( $slug = '' ) {
    $slug = sanitize_title( $slug );

    if ( '' === $slug ) {
        $slug = sanitize_title( get_post_field( 'post_name', get_the_ID() ) );
    }

    $form_routes = [
        'contact-us'         => '[bst_contact_form]',
        'set-an-appointment' => '[bst_appointment_form]',
        'careers'            => '[bst_careers_form]',
    ];

    if ( isset( $form_routes[ $slug ] ) ) {
        echo do_shortcode( $form_routes[ $slug ] );
        return;
    }

    $content = get_the_content();
    $trimmed_content = trim( strip_tags( strip_shortcodes( $content ) ) );

    if ( '' !== $trimmed_content || has_blocks( $content ) ) {
        the_content();
        return;
    }

    $legacy_template = get_theme_file_path( '/inc/pages/' . $slug . '.php' );

    if ( is_readable( $legacy_template ) ) {
        $page_title = get_the_title();
        include $legacy_template;
        return;
    }

    the_content();
}