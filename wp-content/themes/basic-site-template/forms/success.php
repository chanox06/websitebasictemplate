<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the dedicated success page URL for a submitted form.
 *
 * @param string $form_name Form slug.
 * @return string
 */
if ( ! function_exists( 'basic_site_template_form_success_url' ) ) {
    function basic_site_template_form_success_url( $form_name ) {
        $page = get_page_by_path( 'form-success' );
        $base = $page ? get_permalink( $page ) : home_url( '/form-success/' );

        return add_query_arg(
            'success_form',
            sanitize_key( $form_name ),
            $base
        );
    }
}

/**
 * Render the dedicated success page content.
 *
 * @return string
 */
if ( ! function_exists( 'basic_site_template_form_success_shortcode' ) ) {
    function basic_site_template_form_success_shortcode() {
        $form_name = isset( $_GET['success_form'] ) ? sanitize_key( wp_unslash( $_GET['success_form'] ) ) : '';
        $content   = array(
            'contact-us' => array(
                'eyebrow'  => 'Message received',
                'title'    => 'Thank you for reaching out',
                'text'     => 'Your message has been sent successfully. A member of the Your Company team will review your inquiry and contact you shortly.',
                'button'   => 'Return to Contact Us',
                'url'      => home_url( '/company-contact-us/' ),
            ),
            'set-an-appointment' => array(
                'eyebrow'  => 'Request received',
                'title'    => 'Your appointment request is on its way',
                'text'     => 'Thank you for choosing Your Company. Our team will review your request and contact you to confirm the details.',
                'button'   => 'Return to Appointment Form',
                'url'      => home_url( '/company-set-an-appointment/' ),
            ),
            'careers' => array(
                'eyebrow'  => 'Application received',
                'title'    => 'Thank you for your interest',
                'text'     => 'Your application has been submitted successfully. Our team will review your information and contact you if your experience matches an available opportunity.',
                'button'   => 'Return to Careers',
                'url'      => home_url( '/company-careers/' ),
            ),
        );

        $selected = isset( $content[ $form_name ] ) ? $content[ $form_name ] : array(
            'eyebrow' => 'Submission complete',
            'title'   => 'Thank you',
            'text'    => 'Your submission has been received successfully. Our team will be in touch shortly.',
            'button'  => 'Back to Home',
            'url'     => home_url( '/' ),
        );

        ob_start();
        ?>
        <div class="bst-success-card">
            <div class="bst-success-card__mark" aria-hidden="true">✓</div>
            <p class="bst-success-card__eyebrow"><?php echo esc_html( $selected['eyebrow'] ); ?></p>
            <h1 class="bst-success-card__title"><?php echo esc_html( $selected['title'] ); ?></h1>
            <p class="bst-success-card__text"><?php echo esc_html( $selected['text'] ); ?></p>
            <div class="bst-success-card__actions">
                <a class="bst-success-card__btn bst-success-card__btn--primary" href="<?php echo esc_url( $selected['url'] ); ?>"><?php echo esc_html( $selected['button'] ); ?></a>
                <a class="bst-success-card__btn bst-success-card__btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to Home</a>
            </div>
            <p class="bst-success-card__note">If you need immediate assistance, please contact our office directly.</p>
        </div>
        <?php
        return ob_get_clean();
    }
}
add_shortcode( 'bst_form_success', 'basic_site_template_form_success_shortcode' );