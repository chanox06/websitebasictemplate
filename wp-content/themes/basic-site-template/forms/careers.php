<?php
/**
 * Your Company — Careers / Online Job Application form.
 *
 * Holds the application form markup and registers the [bst_careers_form]
 * shortcode used by the Careers page. The markup lives here (in the theme's
 * forms/ folder) so all form output stays together.
 *
 * The form posts to admin-post.php with action=career_application. The
 * matching handler validates the submission (nonce + reCAPTCHA), verifies the
 * required fields, accepts an optional-required resume upload (PDF/DOC/DOCX,
 * max 5 MB), emails the application to the site's configured recipient, and
 * redirects back to the form with a status flag.
 *
 * Privacy: the form intentionally collects only the information reasonably
 * needed for an initial employment application. It never asks for a Social
 * Security Number, bank details, government ID images, or medical history. The
 * uploaded resume is emailed and then deleted; nothing is stored publicly.
 *
 * @package basic-site-template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_theme_file_path( 'forms/config.php' );

/**
 * Job positions offered in the "Position Applying For" dropdown.
 *
 * Slug => human label. The slug is also used by the "Apply Now" buttons so the
 * dropdown can be pre-selected on the Careers page.
 *
 * @return array<string, string>
 */
function basic_site_template_careers_positions() {
    return array(
        'registered-nurse'          => 'Registered Nurse (RN)',
        'licensed-vocational-nurse' => 'Licensed Vocational Nurse (LVN)',
        'physical-therapist'        => 'Physical Therapist (PT)',
        'occupational-therapist'    => 'Occupational Therapist (OT)',
        'speech-therapist'          => 'Speech Therapist (ST)',
        'home-health-aide'          => 'Your Services Aide',
        'office-staff'              => 'Office Staff',
        'other'                     => 'Other',
    );
}

/**
 * Positions that require a professional license / certification.
 *
 * Used to require the license fields (both client- and server-side) only when
 * a licensed clinical role is selected.
 *
 * @return string[]
 */
function basic_site_template_careers_licensed_positions() {
    return array(
        'registered-nurse',
        'licensed-vocational-nurse',
        'physical-therapist',
        'occupational-therapist',
        'speech-therapist',
    );
}

/**
 * Employment type options offered in the dropdown.
 *
 * @return string[]
 */
function basic_site_template_careers_employment_types() {
    return array(
        'Full-Time',
        'Part-Time',
        'PRN',
        'No Preference',
    );
}

/**
 * General availability options offered in the dropdown.
 *
 * @return string[]
 */
function basic_site_template_careers_availability() {
    return array(
        'Weekdays',
        'Weekday Evenings',
        'Weekends',
        'On-Call / PRN',
        'Flexible',
    );
}

/**
 * US states offered in the State dropdowns.
 *
 * Reuses the appointment form list when available so both forms stay in sync.
 *
 * @return string[]
 */
function basic_site_template_careers_states() {
    if ( function_exists( 'basic_site_template_appointment_states' ) ) {
        return basic_site_template_appointment_states();
    }

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
 * Map of plain-text application fields (name => already sanitized on collect).
 *
 * @return array<int, string>
 */
function basic_site_template_careers_text_fields() {
    return array(
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'position',
        'employment_type',
        'years_experience',
        'license_certification',
        'license_number',
        'license_state',
        'license_expiration',
        'available_start_date',
        'general_availability',
    );
}

/**
 * Fields that must be present for the application to be accepted.
 *
 * @return string[]
 */
function basic_site_template_careers_required_fields() {
    return array(
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'position',
        'consent',
    );
}

/**
 * Maximum accepted resume size in bytes (5 MB).
 *
 * @return int
 */
function basic_site_template_careers_max_resume_bytes() {
    return 5 * 1024 * 1024;
}

/**
 * Allowed resume mime types.
 *
 * @return array<string, string>
 */
function basic_site_template_careers_allowed_mimes() {
    return array(
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );
}

/**
 * [bst_careers_form] shortcode.
 *
 * Renders the Your Company online job application form.
 *
 * @return string
 */
function basic_site_template_careers_form_shortcode() {
    $positions      = basic_site_template_careers_positions();
    $employment     = basic_site_template_careers_employment_types();
    $availability   = basic_site_template_careers_availability();
    $states         = basic_site_template_careers_states();
    $licensed       = basic_site_template_careers_licensed_positions();
    $non_licensed   = array( 'home-health-aide', 'office-staff', 'other' );
    $form_name      = 'careers';

    $notice = '';
    $vals   = array();

    if ( isset( $_GET['form'], $_GET['status'] ) && $form_name === sanitize_key( wp_unslash( $_GET['form'] ) ) ) {
        $status = sanitize_key( wp_unslash( $_GET['status'] ) );

        if ( 'success' === $status ) {
            $notice = '<div class="bst-form__alert bst-form__alert--success" role="status">Thank you. Your application has been received. We’ll contact you if there is a potential fit.</div>';
        } elseif ( 'error' === $status ) {
            $reason   = isset( $_GET['creason'] ) ? sanitize_key( wp_unslash( $_GET['creason'] ) ) : '';
            $ce_error = 'Sorry, something went wrong while sending your application. Please review the form and try again.';

            if ( 'captcha' === $reason ) {
                $ce_error = 'We could not verify the security check. Please complete the "I\'m not a robot" box and try again.';
            } elseif ( 'resume' === $reason ) {
                $ce_error = 'Your resume could not be uploaded. Please attach a PDF, DOC, or DOCX file no larger than 5 MB.';
            } elseif ( 'security' === $reason ) {
                $ce_error = 'Your session expired. Please review the form and submit it again.';
            }

            $notice = '<div class="bst-form__alert bst-form__alert--error" role="alert">' . esc_html( $ce_error ) . '</div>';

            if ( isset( $_GET['cerr'] ) ) {
                $err_key = sanitize_key( wp_unslash( $_GET['cerr'] ) );
                $stored  = get_transient( 'bst_careers_vals_' . $err_key );

                if ( is_array( $stored ) ) {
                    $vals = $stored;
                    delete_transient( 'bst_careers_vals_' . $err_key );
                }
            }
        }
    }

    /**
     * Escape a remembered text value for reuse in an input/select.
     *
     * @param string $field Field key.
     * @return string
     */
    $val = function ( $field ) use ( $vals ) {
        return isset( $vals[ $field ] ) ? esc_attr( $vals[ $field ] ) : '';
    };

    $selected_position = isset( $vals['position'] ) && '' !== $vals['position']
        ? $vals['position']
        : ( isset( $_GET['career_position'] ) ? sanitize_key( wp_unslash( $_GET['career_position'] ) ) : '' );

    if ( '' !== basic_site_template_careers_site_key() ) {
        wp_enqueue_script(
            'basic_site_template-google-recaptcha',
            'https://www.google.com/recaptcha/api.js',
            array(),
            null,
            true
        );
    }

    wp_enqueue_script(
        'basic_site_template-careers',
        get_theme_file_uri( 'assets/js/careers.js' ),
        array(),
        filemtime( get_theme_file_path( 'assets/js/careers.js' ) ),
        true
    );

    ob_start();
    ?>
    <form class="bst-form bst-form--careers" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="career_application">
        <input type="hidden" name="form_identifier" value="<?php echo esc_attr( $form_name ); ?>">
        <?php wp_nonce_field( 'bst_careers_apply', 'bst_careers_nonce' ); ?>

        <div class="bst-form__header">
            <h2 class="bst-form__title">Apply for a Career Opportunity</h2>
            <p class="bst-form__intro">Complete the form below to apply. Fields marked with <span class="bst-form__req">*</span> are required.</p>
        </div>

        <?php echo $notice; ?>

        <fieldset class="bst-form__section">
            <legend class="bst-form__section-title">Personal Information</legend>
            <div class="bst-form__grid">
                <div class="bst-form__field">
                    <label for="bst-career-first-name">First Name <span class="bst-form__req">*</span></label>
                    <input id="bst-career-first-name" type="text" name="first_name" value="<?php echo $val( 'first_name' ); ?>" autocomplete="given-name" required>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-last-name">Last Name <span class="bst-form__req">*</span></label>
                    <input id="bst-career-last-name" type="text" name="last_name" value="<?php echo $val( 'last_name' ); ?>" autocomplete="family-name" required>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-email">Email Address <span class="bst-form__req">*</span></label>
                    <input id="bst-career-email" type="email" name="email" value="<?php echo $val( 'email' ); ?>" autocomplete="email" required>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-phone">Phone Number <span class="bst-form__req">*</span></label>
                    <input id="bst-career-phone" type="tel" name="phone_number" value="<?php echo $val( 'phone_number' ); ?>" autocomplete="tel" required>
                </div>

                <div class="bst-form__field bst-form__field--full">
                    <label for="bst-career-address">Address <span class="bst-form__req">*</span></label>
                    <input id="bst-career-address" type="text" name="address" value="<?php echo $val( 'address' ); ?>" autocomplete="street-address" required>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-city">City <span class="bst-form__req">*</span></label>
                    <input id="bst-career-city" type="text" name="city" value="<?php echo $val( 'city' ); ?>" autocomplete="address-level2" required>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-state">State <span class="bst-form__req">*</span></label>
                    <select id="bst-career-state" name="state" autocomplete="address-level1" required>
                        <option value="">- Please select -</option>
                        <?php foreach ( $states as $state ) : ?>
                            <option value="<?php echo esc_attr( $state ); ?>"<?php echo selected( $val( 'state' ), $state, false ); ?>><?php echo esc_html( $state ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-zip">ZIP Code <span class="bst-form__req">*</span></label>
                    <input id="bst-career-zip" type="text" name="zip_code" value="<?php echo $val( 'zip_code' ); ?>" inputmode="numeric" pattern="[0-9]{5}(-[0-9]{4})?" autocomplete="postal-code" required>
                </div>
            </div>
        </fieldset>

        <fieldset class="bst-form__section">
            <legend class="bst-form__section-title">Position Information</legend>
            <div class="bst-form__grid">
                <div class="bst-form__field">
                    <label for="bst-career-position">Position Applying For <span class="bst-form__req">*</span></label>
                    <select id="bst-career-position" name="position" required>
                        <option value="">- Please select -</option>
                        <?php foreach ( $positions as $slug => $label ) : ?>
                            <option value="<?php echo esc_attr( $slug ); ?>"<?php echo selected( $selected_position, $slug, false ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-employment-type">Employment Type Preference</label>
                    <select id="bst-career-employment-type" name="employment_type">
                        <option value="">No Preference</option>
                        <?php foreach ( $employment as $type ) : ?>
                            <option value="<?php echo esc_attr( $type ); ?>"<?php echo selected( $val( 'employment_type' ), $type, false ); ?>><?php echo esc_html( $type ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </fieldset>

        <fieldset class="bst-form__section">
            <legend class="bst-form__section-title">Professional Information</legend>
            <div class="bst-form__grid">
                <div class="bst-form__field">
                    <label for="bst-career-years">Years of Relevant Experience</label>
                    <select id="bst-career-years" name="years_experience">
                        <option value="">- Please select -</option>
                        <?php foreach ( array( 'Less than 1 year', '1-2 years', '3-5 years', '6-10 years', 'More than 10 years' ) as $range ) : ?>
                            <option value="<?php echo esc_attr( $range ); ?>"<?php echo selected( $val( 'years_experience' ), $range, false ); ?>><?php echo esc_html( $range ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="bst-form__grid bst-form__license" id="bst-career-license"<?php echo in_array( $selected_position, $non_licensed, true ) ? ' hidden' : ''; ?>>
                <div class="bst-form__field">
                    <label for="bst-career-license-cert">Professional License / Certification</label>
                    <input id="bst-career-license-cert" type="text" name="license_certification" value="<?php echo $val( 'license_certification' ); ?>">
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-license-number">License Number</label>
                    <input id="bst-career-license-number" type="text" name="license_number" value="<?php echo $val( 'license_number' ); ?>">
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-license-state">License State</label>
                    <select id="bst-career-license-state" name="license_state">
                        <option value="">- Please select -</option>
                        <?php foreach ( $states as $state ) : ?>
                            <option value="<?php echo esc_attr( $state ); ?>"<?php echo selected( $val( 'license_state' ), $state, false ); ?>><?php echo esc_html( $state ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-license-expiration">License Expiration Date</label>
                    <input id="bst-career-license-expiration" type="date" name="license_expiration" value="<?php echo $val( 'license_expiration' ); ?>">
                </div>
            </div>
            <p class="bst-form__note bst-form__license-note" id="bst-career-license-note"<?php echo in_array( $selected_position, $licensed, true ) ? '' : ' hidden'; ?>>License details are required for licensed clinical roles.</p>
        </fieldset>

        <fieldset class="bst-form__section">
            <legend class="bst-form__section-title">Availability</legend>
            <div class="bst-form__grid">
                <div class="bst-form__field">
                    <label for="bst-career-start">Available Start Date</label>
                    <input id="bst-career-start" type="date" name="available_start_date" value="<?php echo $val( 'available_start_date' ); ?>">
                </div>

                <div class="bst-form__field">
                    <label for="bst-career-availability">General Availability</label>
                    <select id="bst-career-availability" name="general_availability">
                        <option value="">- Please select -</option>
                        <?php foreach ( $availability as $option ) : ?>
                            <option value="<?php echo esc_attr( $option ); ?>"<?php echo selected( $val( 'general_availability' ), $option, false ); ?>><?php echo esc_html( $option ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </fieldset>

        <fieldset class="bst-form__section">
            <legend class="bst-form__section-title">Resume &amp; Cover Message</legend>
            <div class="bst-form__grid">
                <div class="bst-form__field bst-form__field--full">
                    <label for="bst-career-resume">Resume Upload <span class="bst-form__req">*</span></label>
                    <input id="bst-career-resume" type="file" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                    <p class="bst-form__note">Accepted file types: PDF, DOC, DOCX. Maximum size: 5 MB.</p>
                </div>

                <div class="bst-form__field bst-form__field--full">
                    <label for="bst-career-cover">Cover Message</label>
                    <textarea id="bst-career-cover" name="cover_message" rows="5"><?php echo isset( $vals['cover_message'] ) ? esc_textarea( $vals['cover_message'] ) : ''; ?></textarea>
                    <p class="bst-form__note">Tell us about your experience and interest in joining Your Company.</p>
                </div>
            </div>
        </fieldset>

        <div class="bst-form__field bst-form__consent">
            <label>
                <input type="checkbox" name="consent" value="yes"<?php echo ! empty( $vals['consent'] ) ? ' checked' : ''; ?> required>
                <span>I certify that the information provided in this application is true and complete to the best of my knowledge. <span class="bst-form__req">*</span></span>
            </label>
        </div>

        <?php if ( '' !== basic_site_template_careers_site_key() ) : ?>
        <div class="bst-form__field bst-form__captcha">
            <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( basic_site_template_careers_site_key() ); ?>"></div>
        </div>
        <?php endif; ?>

        <div class="bst-form__actions">
            <button type="submit" name="submit" class="bst-form__submit">Submit Application</button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'bst_careers_form', 'basic_site_template_careers_form_shortcode' );

/**
 * Return the configured Google reCAPTCHA v2 site key.
 *
 * Prefers the helper defined by the contact form so all forms share one key,
 * and falls back to the wp-config constant directly.
 *
 * @return string
 */
function basic_site_template_careers_site_key() {
    if ( function_exists( 'basic_site_template_recaptcha_site_key' ) ) {
        return basic_site_template_recaptcha_site_key();
    }

    return defined( 'GOOGLE_RECAPTCHA_SITE_KEY' ) ? (string) GOOGLE_RECAPTCHA_SITE_KEY : '';
}


/**
 * Send an applicant back to the form with an error flag.
 *
 * Remembered values are stored in a short-lived transient so the form can be
 * re-filled on the next page load, and the transient key travels in the URL.
 *
 * @param array  $data   Sanitized values to remember.
 * @param string $reason Short error reason code.
 * @return void
 */
function basic_site_template_careers_fail( $data = array(), $reason = '' ) {
    $key = wp_generate_password( 12, false, false );
    set_transient( 'bst_careers_vals_' . $key, is_array( $data ) ? $data : array(), 10 * MINUTE_IN_SECONDS );

    $redirect = wp_get_referer();
    if ( ! $redirect ) {
        $redirect = home_url( '/' );
    }
    $redirect = remove_query_arg( array( 'form', 'status', 'cerr', 'creason' ), $redirect );
    $redirect = add_query_arg(
        array(
            'form'    => 'careers',
            'status'  => 'error',
            'cerr'    => $key,
            'creason' => $reason,
        ),
        $redirect
    );

    wp_safe_redirect( $redirect . '#career-application' );
    exit;
}

/**
 * Redirect an applicant back to the form with a success flag.
 *
 * @return void
 */
function basic_site_template_careers_succeed() {
    if ( function_exists( 'basic_site_template_form_success_url' ) ) {
        wp_safe_redirect( basic_site_template_form_success_url( 'careers' ) );
        exit;
    }

    $redirect = wp_get_referer();
    if ( ! $redirect ) {
        $redirect = home_url( '/' );
    }
    $redirect = remove_query_arg( array( 'form', 'status', 'cerr', 'creason' ), $redirect );
    $redirect = add_query_arg( array( 'form' => 'careers', 'status' => 'success' ), $redirect );

    wp_safe_redirect( $redirect . '#career-application' );
    exit;
}

/**
 * Validate and store the uploaded resume in the uploads directory.
 *
 * @return string|WP_Error Absolute path to the uploaded file, or an error.
 */
function basic_site_template_careers_upload_resume() {
    if ( empty( $_FILES['resume'] ) || ! isset( $_FILES['resume']['error'] ) ) {
        return new WP_Error( 'resume', 'missing' );
    }

    $error = (int) $_FILES['resume']['error'];

    if ( UPLOAD_ERR_NO_FILE === $error ) {
        return new WP_Error( 'resume', 'missing' );
    }
    if ( UPLOAD_ERR_OK !== $error ) {
        return new WP_Error( 'resume', 'upload' );
    }
    if ( (int) $_FILES['resume']['size'] > basic_site_template_careers_max_resume_bytes() ) {
        return new WP_Error( 'resume', 'size' );
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';

    $overrides = array(
        'test_form' => false,
        'mimes'     => basic_site_template_careers_allowed_mimes(),
    );

    $moved = wp_handle_upload( $_FILES['resume'], $overrides );

    if ( ! is_array( $moved ) || isset( $moved['error'] ) ) {
        return new WP_Error( 'resume', 'type' );
    }

    return $moved['file'];
}


/**
 * Handle the career application submission.
 *
 * Validates (nonce + reCAPTCHA + required fields), accepts the resume upload,
 * emails the application to the site recipient, and returns the applicant to
 * the form with a success or error notice.
 *
 * @return void
 */
function basic_site_template_careers_handle_submission() {
    $positions    = basic_site_template_careers_positions();
    $licensed     = basic_site_template_careers_licensed_positions();
    $non_licensed = array( 'home-health-aide', 'office-staff', 'other' );

    // 1. Nonce.
    $nonce = isset( $_POST['bst_careers_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['bst_careers_nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $nonce, 'bst_careers_apply' ) ) {
        basic_site_template_careers_fail( array(), 'security' );
    }

    // 2. Collect and sanitize the submitted values.
    $data = array();
    foreach ( basic_site_template_careers_text_fields() as $field ) {
        $data[ $field ] = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
    }
    $data['cover_message']      = isset( $_POST['cover_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cover_message'] ) ) : '';
    $data['consent']            = ! empty( $_POST['consent'] ) ? 'yes' : '';

    // 3. reCAPTCHA (the site's approved anti-spam method).
    if ( '' !== basic_site_template_careers_site_key() ) {
        $token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

        if ( ! function_exists( 'basic_site_template_verify_recaptcha' ) || ! basic_site_template_verify_recaptcha( $token ) ) {
            basic_site_template_careers_fail( $data, 'captcha' );
        }
    }

    // 4. Required fields.
    $missing = array();
    foreach ( basic_site_template_careers_required_fields() as $field ) {
        if ( '' === trim( (string) $data[ $field ] ) ) {
            $missing[] = $field;
        }
    }
    if ( '' !== $data['email'] && ! is_email( $data['email'] ) ) {
        $missing[] = 'email';
    }
    if ( '' !== $data['position'] && ! isset( $positions[ $data['position'] ] ) ) {
        $missing[] = 'position';
    }

    // License fields are required only for licensed clinical roles.
    if ( in_array( $data['position'], $licensed, true ) ) {
        foreach ( array( 'license_certification', 'license_number', 'license_state', 'license_expiration' ) as $field ) {
            if ( '' === trim( (string) $data[ $field ] ) ) {
                $missing[] = $field;
            }
        }
    }

    if ( ! empty( $missing ) ) {
        basic_site_template_careers_fail( $data, 'required' );
    }

    // 5. Resume upload.
    $attachment = basic_site_template_careers_upload_resume();
    if ( is_wp_error( $attachment ) ) {
        basic_site_template_careers_fail( $data, 'resume' );
    }

    // 6. Build and send the notification email.
    $position_label = $positions[ $data['position'] ];
    $applicant_name = trim( $data['first_name'] . ' ' . $data['last_name'] );
    $subject        = sprintf( 'Career Application – %s – %s', $position_label, $applicant_name );

    $form_config = basic_site_template_forms_config();
    $recipient   = apply_filters( 'basic_site_template_careers_recipient', $form_config['recipient_email'] );
    if ( ! is_email( $recipient ) ) {
        $recipient = get_option( 'admin_email' );
    }

    $email = basic_site_template_build_form_email(
        'New Career Application',
        'A new application was submitted through the Your Company careers form.',
        basic_site_template_careers_email_fields( $data, $position_label ),
        $applicant_name,
        $data['email'],
        'The applicant\'s resume is attached for review. The uploaded file is deleted after email delivery.'
    );

    $attachments = ( is_string( $attachment ) && '' !== $attachment ) ? array( $attachment ) : array();

    $sent = wp_mail( $recipient, $subject, $email['body'], $email['headers'], $attachments );

    // Remove the uploaded resume — nothing is stored publicly.
    if ( ! empty( $attachments ) ) {
        wp_delete_file( $attachment );
    }

    if ( $sent ) {
        basic_site_template_careers_succeed();
    }

    basic_site_template_careers_fail( $data, 'mail' );
}
add_action( 'admin_post_nopriv_career_application', 'basic_site_template_careers_handle_submission' );
add_action( 'admin_post_career_application', 'basic_site_template_careers_handle_submission' );


/**
 * Build the field data for the application notification email.
 *
 * @param array  $data           Sanitized application values.
 * @param string $position_label Human-readable position label.
 * @return array<string, string>
 */
function basic_site_template_careers_email_fields( $data, $position_label ) {
    return array(
        'Position Applying For'         => $position_label,
        'Employment Type Preference'    => $data['employment_type'],
        'First Name'                    => $data['first_name'],
        'Last Name'                     => $data['last_name'],
        'Email Address'                 => $data['email'],
        'Phone Number'                  => $data['phone_number'],
        'Address'                       => $data['address'],
        'City'                          => $data['city'],
        'State'                         => $data['state'],
        'ZIP Code'                      => $data['zip_code'],
        'Years of Relevant Experience'  => $data['years_experience'],
        'Professional License / Cert.'  => $data['license_certification'],
        'License Number'                => $data['license_number'],
        'License State'                 => $data['license_state'],
        'License Expiration Date'       => $data['license_expiration'],
        'Available Start Date'          => $data['available_start_date'],
        'General Availability'          => $data['general_availability'],
        'Cover Message'                 => $data['cover_message'],
    );
}

