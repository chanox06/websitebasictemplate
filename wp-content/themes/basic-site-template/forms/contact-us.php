<?php
/**
 * Your Company — Contact Us form.
 *
 * Holds the contact form markup and registers the [bst_contact_form]
 * shortcode used by the Contact Us page. The markup lives here (in the
 * theme's forms/ folder) instead of functions.php so all form output
 * stays together.
 *
 * The form posts to admin-post.php with action=contact_form and
 * form_identifier=contact-us; a handler can be registered later without
 * changing this markup.
 *
 * @package basic-site-template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_theme_file_path( 'forms/config.php' );

/**
 * Google reCAPTCHA v2 site key.
 *
 * Read from wp-config.php (GOOGLE_RECAPTCHA_SITE_KEY) so the key can differ
 * between environments without editing theme code.
 *
 * @return string
 */
function basic_site_template_recaptcha_site_key() {
    return defined( 'GOOGLE_RECAPTCHA_SITE_KEY' ) ? (string) GOOGLE_RECAPTCHA_SITE_KEY : '';
}

/**
 * Verify a Google reCAPTCHA v2 response token.
 *
 * Intended for the form submission handler (admin_post_contact_form); kept here
 * so the form stays self-contained.
 *
 * @param string $token Value of the g-recaptcha-response field.
 * @return bool True when Google confirms the token is valid.
 */
function basic_site_template_verify_recaptcha( $token ) {
    $secret = defined( 'GOOGLE_RECAPTCHA_SECRET_KEY' ) ? (string) GOOGLE_RECAPTCHA_SECRET_KEY : '';

    if ( '' === $secret || '' === $token ) {
        return false;
    }

    $response = wp_remote_post(
        'https://www.google.com/recaptcha/api/siteverify',
        array(
            'timeout' => 10,
            'body'    => array(
                'secret'   => $secret,
                'response' => $token,
                'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
            ),
        )
    );

    if ( is_wp_error( $response ) ) {
        return false;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );

    return ! empty( $body['success'] );
}

/**
 * [bst_contact_form] shortcode.
 *
 * Renders the Your Company contact form markup.
 *
 * @return string
 */
function basic_site_template_contact_form_shortcode() {
    $form_name = 'contact-us';
    $notice    = '';

    if ( isset( $_GET['form'], $_GET['status'] ) && $form_name === sanitize_key( wp_unslash( $_GET['form'] ) ) ) {
        $status = sanitize_key( wp_unslash( $_GET['status'] ) );

        if ( 'success' === $status ) {
            $notice = '<div class="bst-form__alert bst-form__alert--success">Thank you for your message. We’ll get back to you shortly.</div>';
        } elseif ( 'error' === $status ) {
            $notice = '<div class="bst-form__alert bst-form__alert--error">Sorry, something went wrong while sending your message. Please try again.</div>';
        }
    }

    if ( '' !== basic_site_template_recaptcha_site_key() ) {
        wp_enqueue_script(
            'basic_site_template-google-recaptcha',
            'https://www.google.com/recaptcha/api.js',
            array(),
            null,
            true
        );
    }

    ob_start();
    ?>
    <form class="bst-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
        <input type="hidden" name="action" value="contact_form">
        <input type="hidden" name="form_identifier" value="<?php echo esc_attr( $form_name ); ?>">
        <?php if ( function_exists( 'pincoder_form_nonce_field' ) ) { pincoder_form_nonce_field( $form_name ); } ?>

        <div class="bst-form__header">
            <h2 class="bst-form__title">Send Us a Message</h2>
        </div>

        <?php echo $notice; ?>

        <div class="bst-form__grid">
            <div class="bst-form__field bst-form__field--full">
                <label for="bst-full-name">Full Name <span class="bst-form__req">*</span></label>
                <input id="bst-full-name" type="text" name="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="bst-form__field">
                <label for="bst-email">Email <span class="bst-form__req">*</span></label>
                <input id="bst-email" type="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="bst-form__field">
                <label for="bst-phone">Phone Number</label>
                <input id="bst-phone" type="tel" name="phone_number" placeholder="Enter your phone number">
            </div>

            <div class="bst-form__field bst-form__field--full">
                <label for="bst-message">Comment or Message <span class="bst-form__req">*</span></label>
                <textarea id="bst-message" name="message" rows="5" placeholder="How can we help you?" required></textarea>
            </div>
        </div>

        <div class="bst-form__field bst-form__consent">
            <label>
                <input type="checkbox" name="privacy_policy" value="yes" required>
                <span>By submitting this form you agree to allow Your Company to contact you regarding your inquiry. <span class="bst-form__req">*</span></span>
            </label>
        </div>

        <?php if ( '' !== basic_site_template_recaptcha_site_key() ) : ?>
        <div class="bst-form__field bst-form__captcha">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( basic_site_template_recaptcha_site_key() ); ?>"></div>
        </div>
        <?php endif; ?>

        <div class="bst-form__actions">
            <button type="submit" name="submit" class="bst-form__submit">Send Message</button>
        </div>
    </form>

    <section class="bst-map" aria-label="Business location map placeholder">
        <div class="bst-map__placeholder">
            <p>Replace this placeholder with the new business location, address, or approved map embed.</p>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bst_contact_form', 'basic_site_template_contact_form_shortcode' );

/**
 * Redirect the visitor back to the Contact Us form with a status flag.
 *
 * @param string $status 'success' or 'error'.
 * @return void
 */
function basic_site_template_contact_form_redirect( $status ) {
    $redirect = wp_get_referer();

    if ( ! $redirect ) {
        $redirect = home_url( '/' );
    }

    if ( 'success' === $status && function_exists( 'basic_site_template_form_success_url' ) ) {
        wp_safe_redirect( basic_site_template_form_success_url( 'contact-us' ) );
        exit;
    }

    $redirect = remove_query_arg( array( 'form', 'status' ), $redirect );
    $redirect = add_query_arg(
        array(
            'form'   => 'contact-us',
            'status' => sanitize_key( $status ),
        ),
        $redirect
    );

    wp_safe_redirect( $redirect . '#contact-form' );
    exit;
}

/**
 * Handle the Contact Us form submission.
 *
 * Validates the nonce, reCAPTCHA token, and required fields, then emails the
 * message to the site recipient and returns the visitor to the form with a
 * status flag the shortcode turns into a notice.
 *
 * @return void
 */
function basic_site_template_contact_form_handle_submission() {
    // 1. Nonce.
    $nonce = isset( $_POST['pincoder_form_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['pincoder_form_nonce'] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'pincoder_submit_contact-us' ) ) {
        basic_site_template_contact_form_redirect( 'error' );
    }

    // 2. Collect and sanitize.
    $data = array(
        'full_name'    => isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '',
        'email'        => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
        'phone_number' => isset( $_POST['phone_number'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_number'] ) ) : '',
        'message'      => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
        'consent'      => ! empty( $_POST['privacy_policy'] ) ? 'yes' : '',
    );

    // 3. reCAPTCHA (the site's approved anti-spam method).
    if ( '' !== basic_site_template_recaptcha_site_key() ) {
        $token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

        if ( ! basic_site_template_verify_recaptcha( $token ) ) {
            basic_site_template_contact_form_redirect( 'error' );
        }
    }

    // 4. Required fields.
    $missing = array();

    foreach ( array( 'full_name', 'email', 'message', 'consent' ) as $field ) {
        if ( '' === trim( (string) $data[ $field ] ) ) {
            $missing[] = $field;
        }
    }

    if ( '' !== $data['email'] && ! is_email( $data['email'] ) ) {
        $missing[] = 'email';
    }

    if ( ! empty( $missing ) ) {
        basic_site_template_contact_form_redirect( 'error' );
    }

    // 5. Build and send the notification email.
    $form_config = basic_site_template_forms_config();
    $recipient   = apply_filters( 'basic_site_template_contact_recipient', $form_config['recipient_email'] );

    if ( ! is_email( $recipient ) ) {
        $recipient = get_option( 'admin_email' );
    }

    $subject = sprintf( 'New Contact Message - %s', $data['full_name'] );

    $rows = array(
        'Full Name'    => $data['full_name'],
        'Email'        => $data['email'],
        'Phone Number' => $data['phone_number'],
        'Message'      => $data['message'],
    );

    $email = basic_site_template_build_form_email(
        'New Contact Us Message',
        'A new message was submitted through the Your Company website contact form.',
        $rows,
        $data['full_name'],
        $data['email'],
        'Please review the inquiry and respond according to your normal contact process.'
    );

    if ( wp_mail( $recipient, $subject, $email['body'], $email['headers'] ) ) {
        basic_site_template_contact_form_redirect( 'success' );
    }

    basic_site_template_contact_form_redirect( 'error' );
}
add_action( 'admin_post_nopriv_contact_form', 'basic_site_template_contact_form_handle_submission' );
add_action( 'admin_post_contact_form', 'basic_site_template_contact_form_handle_submission' );
