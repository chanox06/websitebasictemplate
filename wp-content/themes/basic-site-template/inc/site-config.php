<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

$config = [
    'company_name'        => '__COMPANY_NAME__',
    'short_name'          => '__SHORT_NAME__',
    'tagline'             => '__TAGLINE__',
    'description'         => '__SITE_DESCRIPTION__',
    'seo_description'     => '__SITE_DESCRIPTION__',
    'seo_social_image'    => '',
    'logo_file'           => 'logo.webp',
    'favicon_file'        => 'favicon.png',
    'phone_display'       => '__PHONE_DISPLAY__',
    'phone_href'          => '__PHONE_HREF__',
    'email'               => '__EMAIL__',
    'address'             => '__ADDRESS__',
    'social_linkedin'     => '',
    'social_facebook'     => '',
    'social_instagram'    => '',
    'social_youtube'      => '',
    'primary_color'       => '#0B2240',
    'accent_color'        => '#B8862A',
];

$GLOBALS['basic_site_template_config'] = $config;

if ( ! function_exists( 'pincoder_site_config' ) ) {
    /**
     * Return the centralized site configuration.
     *
     * @return array<string, string>
     */
    function pincoder_site_config() {
        $site_config = isset( $GLOBALS['basic_site_template_config'] )
            ? $GLOBALS['basic_site_template_config']
            : array();

        return is_array( $site_config ) ? $site_config : array();
    }
}

if ( ! function_exists( 'pincoder_site_value' ) ) {
    /**
     * Return one configured site value or a fallback.
     *
     * @param string $key      Configuration key.
     * @param mixed  $fallback Fallback value.
     * @return mixed
     */
    function pincoder_site_value( $key, $fallback = '' ) {
        $config = pincoder_site_config();

        return array_key_exists( $key, $config ) && '' !== $config[ $key ]
            ? $config[ $key ]
            : $fallback;
    }
}

if ( ! function_exists( 'pincoder_get_setting' ) ) {
    /**
     * Compatibility accessor for theme settings.
     *
     * @param string $key      Configuration key.
     * @param mixed  $fallback Fallback value.
     * @return mixed
     */
    function pincoder_get_setting( $key, $fallback = '' ) {
        return pincoder_site_value( $key, $fallback );
    }
}

return $config;