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

/* =========================================
   ADMIN
========================================= */

/*
 * Remove the core "Fonts" screen under Appearance.
 *
 * Typography for this theme is managed in style.css (@font-face)
 * and theme.json (font-family presets only), so the Font Library
 * is not used and its menu entry is hidden.
 */
function basic_site_template_remove_fonts_menu() {
    remove_submenu_page( 'themes.php', 'font-library.php' );
}

add_action( 'admin_menu', 'basic_site_template_remove_fonts_menu', 999 );

/*
 * Removing the menu entry leaves the screen reachable by URL,
 * so send direct requests back to the dashboard.
 */
function basic_site_template_block_fonts_screen() {
    global $pagenow;

    if ( 'font-library.php' === $pagenow ) {
        wp_safe_redirect( admin_url( 'themes.php' ) );
        exit;
    }
}

add_action( 'admin_init', 'basic_site_template_block_fonts_screen' );

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
