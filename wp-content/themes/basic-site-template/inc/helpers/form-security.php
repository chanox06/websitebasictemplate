<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the shared nonce field used by public Pincoder forms.
 */
if ( ! function_exists( 'pincoder_form_nonce_field' ) ) {
    function pincoder_form_nonce_field( $form_name ) {
        wp_nonce_field( 'pincoder_submit_' . sanitize_key( $form_name ), 'pincoder_form_nonce' );
    }
}

/**
 * Verify a public Pincoder form request.
 */
if ( ! function_exists( 'pincoder_verify_form_request' ) ) {
function pincoder_verify_form_request( $form_name ) {
    if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
        return false;
    }

    $submitted_form = isset( $_POST['form_identifier'] )
        ? sanitize_key( wp_unslash( $_POST['form_identifier'] ) )
        : '';

    if ( sanitize_key( $form_name ) !== $submitted_form ) {
        return false;
    }

    $nonce = isset( $_POST['pincoder_form_nonce'] )
        ? sanitize_text_field( wp_unslash( $_POST['pincoder_form_nonce'] ) )
        : '';

    return '' !== $nonce && (bool) wp_verify_nonce( $nonce, 'pincoder_submit_' . sanitize_key( $form_name ) );
}
}

/**
 * Redirect a form submission back to the originating page safely.
 */
if ( ! function_exists( 'pincoder_form_redirect' ) ) {
function pincoder_form_redirect( $status, $form_name, $extra_args = [] ) {
    $referer = wp_get_referer();
    $target  = $referer ? remove_query_arg( [ 'status', 'form', 'msg' ], $referer ) : home_url( '/' );
    $args    = array_merge(
        [
            'status' => sanitize_key( $status ),
            'form'   => sanitize_key( $form_name ),
        ],
        is_array( $extra_args ) ? $extra_args : []
    );

    wp_safe_redirect( add_query_arg( $args, $target ) );
    exit;
}
}