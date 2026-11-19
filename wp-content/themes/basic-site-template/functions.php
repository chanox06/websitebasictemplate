<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_theme_file_path( 'inc/site-config.php' );
require_once get_theme_file_path( 'inc/helpers/seo.php' );

function clean_gutenberg_theme_setup() {
    add_theme_support( 'custom-logo', [
        'height'      => 120,
        'width'       => 360,
        'flex-height' => true,
        'flex-width'  => true,
    ] );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'style.css' );
    add_editor_style( 'pages.css' );
    add_editor_style( 'editor-style.css' );
}
add_action( 'after_setup_theme', 'clean_gutenberg_theme_setup' );

function clean_gutenberg_editor_assets() {
    wp_enqueue_style(
        'clean-gutenberg-theme-editor',
        get_stylesheet_uri(),
        [ 'wp-edit-blocks' ],
        filemtime( get_theme_file_path( 'style.css' ) )
    );

    wp_enqueue_style(
        'clean-gutenberg-editor-overrides',
        get_theme_file_uri( 'editor-style.css' ),
        [ 'clean-gutenberg-theme-editor' ],
        filemtime( get_theme_file_path( 'editor-style.css' ) )
    );
}
add_action( 'enqueue_block_editor_assets', 'clean_gutenberg_editor_assets' );


/* =========================================
   FRONT-END STYLES
========================================= */

function basic_site_template_frontend_styles() {

    /* Main global stylesheet */
    wp_enqueue_style(
        'basic_site_template-main-style',
        get_stylesheet_uri(),
        [],
        filemtime( get_theme_file_path( 'style.css' ) )
    );


    /*
     * Load pages.css on every non-home page.
     *
     * Front page = homepage
     */
    if ( ! is_front_page() ) {

        wp_enqueue_style(
            'basic_site_template-pages-style',
            get_theme_file_uri( 'pages.css' ),
            [ 'basic_site_template-main-style' ],
            filemtime( get_theme_file_path( 'pages.css' ) )
        );
    }
}

add_action( 'wp_enqueue_scripts', 'basic_site_template_frontend_styles' );


/* =========================================
   FRONT-END SCRIPTS
======================================== */

function basic_site_template_frontend_scripts() {

    /* WOW-style scroll reveal - homepage only */
    if ( is_front_page() ) {

        wp_enqueue_script(
            'basic_site_template-wow-lite',
            get_theme_file_uri( 'assets/js/wow-lite.js' ),
            [],
            filemtime( get_theme_file_path( 'assets/js/wow-lite.js' ) ),
            true
        );

        /* Development notice for homepage CTAs and navigation tabs */
        wp_enqueue_script(
            'basic_site_template-development-modal',
            get_theme_file_uri( 'assets/js/development-modal.js' ),
            [],
            filemtime( get_theme_file_path( 'assets/js/development-modal.js' ) ),
            true
        );
    }
}

add_action( 'wp_enqueue_scripts', 'basic_site_template_frontend_scripts' );


/* =========================================
   FAQ PAGE SCRIPTS
========================================= */

function basic_site_template_faq_scripts() {

    /* Progressive-enhancement accordion for the FAQ page only */
    if ( is_page( 'company-faq' ) ) {

        wp_enqueue_script(
            'basic_site_template-faq',
            get_theme_file_uri( 'assets/js/faq.js' ),
            [],
            filemtime( get_theme_file_path( 'assets/js/faq.js' ) ),
            true
        );
    }
}

add_action( 'wp_enqueue_scripts', 'basic_site_template_faq_scripts' );


/*
 * Flag that JavaScript is available, as early as possible.
 *
 * Adds a "js" class to <html> so the WOW scroll-animation styles
 * (which keep .wow elements hidden until they animate in) never
 * hide content when JavaScript is disabled.
 */
function basic_site_template_js_flag() {
    echo "<script>document.documentElement.classList.add('js');</script>\n";
}

add_action( 'wp_head', 'basic_site_template_js_flag', 1 );


/* =========================================================
   CONTACT FORM
   ---------------------------------------------------------
   The contact form markup lives in forms/contact-us.php.
   Load that file so its [bst_contact_form] shortcode is
   registered, keeping the markup out of functions.php.
========================================================= */

/**
 * Enqueue the forms stylesheet on the front end.
 */
function basic_site_template_forms_styles() {
    $relative = 'forms-style.css';
    $path     = get_theme_file_path( $relative );

    if ( ! file_exists( $path ) ) {
        return;
    }

    wp_enqueue_style(
        'basic_site_template-forms-style',
        get_theme_file_uri( $relative ),
        array( 'basic_site_template-main-style' ),
        filemtime( $path )
    );
}
add_action( 'wp_enqueue_scripts', 'basic_site_template_forms_styles' );

/*
 * Shared form helpers. form-security.php provides the nonce field rendered by
 * the forms, and form-processing.php provides the reCAPTCHA verifier and HTML
 * mail-header helper. Load them before the form files so their functions exist
 * everywhere the forms are output or processed.
 */
require_once get_theme_file_path( 'inc/helpers/form-security.php' );
require_once get_theme_file_path( 'inc/helpers/form-processing.php' );
require_once get_theme_file_path( 'forms/email-template.php' );
require_once get_theme_file_path( 'forms/success.php' );

require_once get_theme_file_path( 'forms/contact-us.php' );
require_once get_theme_file_path( 'forms/set-an-appointment.php' );
require_once get_theme_file_path( 'forms/careers.php' );


/* =========================================
   CUSTOM WORDPRESS LOGIN LOGO
========================================= */

function wpadmin_custom_login_logo() {
    ?>
    <style>
        body.login h1 a {
            background-image: url('<?php echo esc_url( get_template_directory_uri() . '/images/logo.png' ); ?>') !important;
            background-size: contain !important;
            background-position: center !important;
            background-repeat: no-repeat !important;

            width: 260px !important;
            height: 100px !important;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'wpadmin_custom_login_logo' );


/* Change login logo link */
function wpadmin_login_logo_url() {
    return home_url( '/' );
}
add_filter( 'login_headerurl', 'wpadmin_login_logo_url' );
