<?php
/**
 * Your Company — Set an Appointment form.
 *
 * Holds the appointment form markup and registers the
 * [bst_appointment_form] shortcode used by the Set an Appointment page.
 * The markup lives here (in the theme's forms/ folder) instead of
 * functions.php so all form output stays together.
 *
 * The field set mirrors the legacy site's appointment form
 * (forms/appointmentForm.php) so the client keeps the same intake
 * information: contact details, preferred contact method, best time to
 * call, and preferred date/time.
 *
 * The form posts to admin-post.php with action=appointment_form and
 * form_identifier=set-an-appointment; a handler can be registered later
 * without changing this markup.
 *
 * @package basic-site-template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_theme_file_path( 'forms/config.php' );

/**
 * US states offered in the State dropdown.
 *
 * Matches the list used by the legacy Your Company appointment form
 * (50 states plus the District of Columbia, Puerto Rico and the US
 * Virgin Islands).
 *
 * @return string[]
 */
function basic_site_template_appointment_states() {
    return array(
        'Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado',
        'Connecticut', 'Delaware', 'District Of Columbia', 'Florida', 'Georgia',
        'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky',
        'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan',
        'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada',
        'New Hampshire', 'New Jersey', 'New Mexico', 'New York',
        'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon',
        'Pennsylvania', 'Puerto Rico', 'Rhode Island', 'South Carolina',
        'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont',
        'Virgin Islands', 'Virginia', 'Washington', 'West Virginia',
        'Wisconsin', 'Wyoming',
    );
}

/**
 * "Best time to call" options offered in the dropdown.
 *
 * Matches the legacy Your Company appointment form.
 *
 * @return string[]
 */
function basic_site_template_appointment_call_times() {
    return array(
        'Anytime',
        'Morning at Home',
        'Morning at Work',
        'Afternoon at Home',
        'Afternoon at Work',
        'Evening at Home',
        'Evening at Work',
    );
}

/**
 * [bst_appointment_form] shortcode.
 *
 * Renders the Your Company "Set an Appointment" form markup.
 *
 * @return string
 */
function basic_site_template_appointment_form_shortcode() {
    $form_name = 'set-an-appointment';
    $notice    = '';

    if ( isset( $_GET['form'], $_GET['status'] ) && $form_name === sanitize_key( wp_unslash( $_GET['form'] ) ) ) {
        $status = sanitize_key( wp_unslash( $_GET['status'] ) );

        if ( 'success' === $status ) {
            $notice = '<div class="bst-form__alert bst-form__alert--success">Thank you for your message. We’ll get back to you shortly.</div>';
        } elseif ( 'error' === $status ) {
            $notice = '<div class="bst-form__alert bst-form__alert--error">Sorry, something went wrong while sending your request. Please try again.</div>';
        }
    }

    if ( function_exists( 'basic_site_template_recaptcha_site_key' ) && '' !== basic_site_template_recaptcha_site_key() ) {
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
        <input type="hidden" name="action" value="appointment_form">
        <input type="hidden" name="form_identifier" value="<?php echo esc_attr( $form_name ); ?>">
        <?php if ( function_exists( 'pincoder_form_nonce_field' ) ) { pincoder_form_nonce_field( $form_name ); } ?>

        <div class="bst-form__header">
            <h2 class="bst-form__title">Set an Appointment Form</h2>
            <p class="bst-form__note"><span class="bst-form__req">*</span> Required Information</p>
        </div>

        <?php echo $notice; ?>

        <div class="bst-form__grid">
            <div class="bst-form__field bst-form__field--full">
                <label for="bst-appt-name">Name <span class="bst-form__req">*</span></label>
                <input id="bst-appt-name" type="text" name="name" placeholder="Enter name here" required>
            </div>

            <div class="bst-form__field bst-form__field--full">
                <label for="bst-appt-address">Address <span class="bst-form__req">*</span></label>
                <input id="bst-appt-address" type="text" name="address" placeholder="Enter address here" required>
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-city">City <span class="bst-form__req">*</span></label>
                <input id="bst-appt-city" type="text" name="city" placeholder="Enter city here" required>
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-state">State</label>
                <select id="bst-appt-state" name="state">
                    <option value="">Please select state.</option>
                    <?php foreach ( basic_site_template_appointment_states() as $state ) : ?>
                        <option value="<?php echo esc_attr( $state ); ?>"><?php echo esc_html( $state ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-zip">Zip Code <span class="bst-form__req">*</span></label>
                <input id="bst-appt-zip" type="text" name="zip_code" inputmode="numeric" autocomplete="postal-code" placeholder="Enter zip code here" required>
            </div>

            <div class="bst-form__field bst-form__field--full">
                <span class="bst-form__label">How do you prefer to be contacted? <span class="bst-form__req">*</span></span>
                <div class="bst-form__radios">
                    <label class="bst-form__radio"><input type="radio" name="prefer_to_be_contacted_by" value="Phone" required> Phone</label>
                    <label class="bst-form__radio"><input type="radio" name="prefer_to_be_contacted_by" value="Fax"> Fax</label>
                    <label class="bst-form__radio"><input type="radio" name="prefer_to_be_contacted_by" value="Email"> Email</label>
                </div>
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-email">Email Address <span class="bst-form__req">*</span></label>
                <input id="bst-appt-email" type="email" name="email_address" autocomplete="email" placeholder="example@domain.com" required>
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-fax">Fax Number</label>
                <input id="bst-appt-fax" type="text" name="fax_number" placeholder="Enter fax number here">
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-phone">Phone Number <span class="bst-form__req">*</span></label>
                <input id="bst-appt-phone" type="tel" name="phone_number" autocomplete="tel" placeholder="+1 (number)" required>
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-time">Best time to call <span class="bst-form__req">*</span></label>
                <select id="bst-appt-time" name="best_time_to_call" required>
                    <option value="">- Please select -</option>
                    <?php foreach ( basic_site_template_appointment_call_times() as $call_time ) : ?>
                        <option value="<?php echo esc_attr( $call_time ); ?>"><?php echo esc_html( $call_time ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-date">Preferred Date</label>
                <input id="bst-appt-date" type="date" name="preferred_date" placeholder="Enter preferred date here">
            </div>

            <div class="bst-form__field">
                <label for="bst-appt-preferred-time">Preferred Time</label>
                <input id="bst-appt-preferred-time" type="time" name="preferred_time" placeholder="Enter preferred time here">
            </div>

            <div class="bst-form__field bst-form__field--full">
                <label for="bst-appt-comment">Comment</label>
                <textarea id="bst-appt-comment" name="comment" rows="5" placeholder="Enter comment here"></textarea>
            </div>
        </div>

        <div class="bst-form__field bst-form__consent">
            <label>
                <input type="checkbox" name="privacy_policy" value="yes" required>
                <span>By submitting this form you agree to allow Your Company to contact you regarding your inquiry. <span class="bst-form__req">*</span></span>
            </label>
        </div>

        <?php if ( function_exists( 'basic_site_template_recaptcha_site_key' ) && '' !== basic_site_template_recaptcha_site_key() ) : ?>
        <div class="bst-form__field bst-form__captcha">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( basic_site_template_recaptcha_site_key() ); ?>"></div>
        </div>
        <?php endif; ?>

        <div class="bst-form__actions">
            <button type="submit" name="submit" class="bst-form__submit">Submit</button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bst_appointment_form', 'basic_site_template_appointment_form_shortcode' );

/**
 * Redirect the visitor back to the appointment form with a status flag.
 *
 * @param string $status 'success' or 'error'.
 * @return void
 */
function basic_site_template_appointment_form_redirect( $status ) {
    $redirect = wp_get_referer();

    if ( ! $redirect ) {
        $redirect = home_url( '/' );
    }

    if ( 'success' === $status && function_exists( 'basic_site_template_form_success_url' ) ) {
        wp_safe_redirect( basic_site_template_form_success_url( 'set-an-appointment' ) );
        exit;
    }

    $redirect = remove_query_arg( array( 'form', 'status' ), $redirect );
    $redirect = add_query_arg(
        array(
            'form'   => 'set-an-appointment',
            'status' => sanitize_key( $status ),
        ),
        $redirect
    );

    wp_safe_redirect( $redirect . '#appointment-form' );
    exit;
}

/**
 * Handle the Set an Appointment form submission.
 *
 * Validates the nonce, reCAPTCHA token, and required fields, then emails the
 * request to the site recipient and returns the visitor to the form with a
 * status flag the shortcode turns into a notice.
 *
 * @return void
 */
function basic_site_template_appointment_form_handle_submission() {
    // 1. Nonce.
    $nonce = isset( $_POST['pincoder_form_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['pincoder_form_nonce'] ) ) : '';

    if ( ! wp_verify_nonce( $nonce, 'pincoder_submit_set-an-appointment' ) ) {
        basic_site_template_appointment_form_redirect( 'error' );
    }

    // 2. Collect and sanitize.
    $data = array(
        'name'                      => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
        'address'                   => isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '',
        'city'                      => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
        'state'                     => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
        'zip_code'                  => isset( $_POST['zip_code'] ) ? sanitize_text_field( wp_unslash( $_POST['zip_code'] ) ) : '',
        'prefer_to_be_contacted_by' => isset( $_POST['prefer_to_be_contacted_by'] ) ? sanitize_text_field( wp_unslash( $_POST['prefer_to_be_contacted_by'] ) ) : '',
        'email_address'             => isset( $_POST['email_address'] ) ? sanitize_email( wp_unslash( $_POST['email_address'] ) ) : '',
        'fax_number'                => isset( $_POST['fax_number'] ) ? sanitize_text_field( wp_unslash( $_POST['fax_number'] ) ) : '',
        'phone_number'              => isset( $_POST['phone_number'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_number'] ) ) : '',
        'best_time_to_call'         => isset( $_POST['best_time_to_call'] ) ? sanitize_text_field( wp_unslash( $_POST['best_time_to_call'] ) ) : '',
        'preferred_date'            => isset( $_POST['preferred_date'] ) ? sanitize_text_field( wp_unslash( $_POST['preferred_date'] ) ) : '',
        'preferred_time'            => isset( $_POST['preferred_time'] ) ? sanitize_text_field( wp_unslash( $_POST['preferred_time'] ) ) : '',
        'comment'                   => isset( $_POST['comment'] ) ? sanitize_textarea_field( wp_unslash( $_POST['comment'] ) ) : '',
        'consent'                   => ! empty( $_POST['privacy_policy'] ) ? 'yes' : '',
    );

    // 3. reCAPTCHA (the site's approved anti-spam method).
    if ( function_exists( 'basic_site_template_recaptcha_site_key' ) && '' !== basic_site_template_recaptcha_site_key() ) {
        $token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

        if ( ! function_exists( 'basic_site_template_verify_recaptcha' ) || ! basic_site_template_verify_recaptcha( $token ) ) {
            basic_site_template_appointment_form_redirect( 'error' );
        }
    }

    // 4. Required fields.
    $required = array( 'name', 'address', 'city', 'zip_code', 'prefer_to_be_contacted_by', 'email_address', 'phone_number', 'best_time_to_call', 'consent' );

    $missing = array();
    foreach ( $required as $field ) {
        if ( '' === trim( (string) $data[ $field ] ) ) {
            $missing[] = $field;
        }
    }

    if ( '' !== $data['email_address'] && ! is_email( $data['email_address'] ) ) {
        $missing[] = 'email_address';
    }

    if ( ! empty( $missing ) ) {
        basic_site_template_appointment_form_redirect( 'error' );
    }

    // 5. Build and send the notification email.
    basic_site_template_appointment_form_send( $data );
}
add_action( 'admin_post_nopriv_appointment_form', 'basic_site_template_appointment_form_handle_submission' );
add_action( 'admin_post_appointment_form', 'basic_site_template_appointment_form_handle_submission' );

/**
 * Build and send the appointment notification email.
 *
 * @param array $data Sanitized appointment values.
 * @return void
 */
function basic_site_template_appointment_form_send( $data ) {
    $form_config = basic_site_template_forms_config();
    $recipient   = apply_filters( 'basic_site_template_appointment_recipient', $form_config['recipient_email'] );

    if ( ! is_email( $recipient ) ) {
        $recipient = get_option( 'admin_email' );
    }

    $subject = sprintf( 'New Appointment Request - %s', $data['name'] );

    $rows = array(
        'Name'                     => $data['name'],
        'Address'                  => $data['address'],
        'City'                     => $data['city'],
        'State'                    => $data['state'],
        'Zip Code'                 => $data['zip_code'],
        'Preferred Contact Method' => $data['prefer_to_be_contacted_by'],
        'Email Address'            => $data['email_address'],
        'Phone Number'             => $data['phone_number'],
        'Fax Number'               => $data['fax_number'],
        'Best Time to Call'        => $data['best_time_to_call'],
        'Preferred Date'           => $data['preferred_date'],
        'Preferred Time'           => $data['preferred_time'],
        'Comment'                  => $data['comment'],
    );

    $email = basic_site_template_build_form_email(
        'New Appointment Request',
        'A new appointment request was submitted through the Your Company website.',
        $rows,
        $data['name'],
        $data['email_address'],
        'Please contact the requester to confirm availability and appointment details.'
    );

    if ( wp_mail( $recipient, $subject, $email['body'], $email['headers'] ) ) {
        basic_site_template_appointment_form_redirect( 'success' );
    }

    basic_site_template_appointment_form_redirect( 'error' );
}
