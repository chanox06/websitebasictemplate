<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme fallback for the shared Pincoder Core reCAPTCHA verifier.
 */
if ( ! function_exists( 'pincoder_verify_recaptcha' ) ) {
    function pincoder_verify_recaptcha( $token, $secret_key ) {
        $token      = is_string( $token ) ? sanitize_text_field( $token ) : '';
        $secret_key = is_string( $secret_key ) ? sanitize_text_field( $secret_key ) : '';

        if ( '' === $token || '' === $secret_key ) {
            return false;
        }

        $response = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'timeout' => 10,
                'body'    => [
                    'secret'   => $secret_key,
                    'response' => $token,
                    'remoteip' => isset( $_SERVER['REMOTE_ADDR'] )
                        ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )
                        : '',
                ],
            ]
        );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ) );

        return isset( $body->success ) && true === $body->success;
    }
}

/**
 * Theme fallback for the shared Pincoder Core HTML mail headers.
 */
if ( ! function_exists( 'pincoder_form_mail_headers' ) ) {
    function pincoder_form_mail_headers( $from_email, $display_name = '' ) {
        $from_email = sanitize_email( $from_email );
        $display_name = sanitize_text_field( $display_name );
        $from_header  = $display_name ? $display_name . ' <' . $from_email . '>' : $from_email;

        return "From: {$from_header}\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n";
    }
}