<?php

if ( ! function_exists( 'pincoder_form_nonce_field' ) ) {
    $form_security_path = get_template_directory() . '/inc/helpers/form-security.php';
    if ( is_readable( $form_security_path ) ) {
        require_once $form_security_path;
    }
}

/**
 * Return the shared configuration used by the custom theme forms.
 *
 * Change recipient_email here when the destination for form notifications
 * needs to be updated. This keeps the address out of functions.php and the
 * individual form handlers.
 *
 * @return array<string, mixed>
 */
if ( ! function_exists( 'basic_site_template_forms_config' ) ) {
    function basic_site_template_forms_config() {
        static $config = null;

        if ( null !== $config ) {
            return $config;
        }

        $site_config = function_exists( 'pincoder_site_config' )
            ? pincoder_site_config()
            : require get_template_directory() . '/inc/site-config.php';

        $config = array(
            // Contact-us is the canonical Pincoder form base for future forms.
            // Existing form handlers remain registered for backward compatibility.
            'forms_to_include'  => array(
                'contact-us',
                'set-an-appointment',
                'send-referrals',
                'newsletter',
                'careers',
                'book-now',
                'qd-pathways',
            ),
            'recaptcha_site_key'   => defined( 'GOOGLE_RECAPTCHA_SITE_KEY' ) ? GOOGLE_RECAPTCHA_SITE_KEY : '',
            'recaptcha_secret_key' => defined( 'GOOGLE_RECAPTCHA_SECRET_KEY' ) ? GOOGLE_RECAPTCHA_SECRET_KEY : '',
            // Leave blank until the new site's recipient is configured.
            // Form handlers fall back to the WordPress admin email.
            'recipient_email'      => '',
        );

        return $config;
    }
}

return basic_site_template_forms_config();
