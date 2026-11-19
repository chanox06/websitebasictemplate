<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Build a consistent, formal HTML notification email for the site forms.
 *
 * @param string $title       Email heading.
 * @param string $intro       Introductory text.
 * @param array  $rows        Associative label => value pairs.
 * @param string $reply_name  Optional Reply-To display name.
 * @param string $reply_email Optional Reply-To email address.
 * @param string $footer_note Optional note shown above the automated footer.
 * @return array{body:string,headers:array<int,string>}
 */
if ( ! function_exists( 'basic_site_template_build_form_email' ) ) {
    function basic_site_template_build_form_email( $title, $intro, $rows, $reply_name = '', $reply_email = '', $footer_note = '' ) {
        $site_name = function_exists( 'pincoder_site_value' )
            ? pincoder_site_value( 'company_name', get_bloginfo( 'name' ) )
            : get_bloginfo( 'name' );
        $site_url  = home_url( '/' );
        $table_rows = '';

        foreach ( (array) $rows as $label => $value ) {
            $value = trim( (string) $value );

            if ( '' === $value ) {
                continue;
            }

            $table_rows .= '<tr>'
                . '<td style="width:36%;padding:13px 16px;border-bottom:1px solid #e8edf2;background:#f7f9fb;vertical-align:top;font-family:Arial,sans-serif;font-size:13px;font-weight:700;line-height:1.5;color:#0B2240;">' . esc_html( $label ) . '</td>'
                . '<td style="padding:13px 16px;border-bottom:1px solid #e8edf2;vertical-align:top;font-family:Arial,sans-serif;font-size:14px;line-height:1.6;color:#3F4A57;">' . nl2br( esc_html( $value ) ) . '</td>'
                . '</tr>';
        }

        $reply_block = '';
        if ( is_email( $reply_email ) ) {
            $reply_block = '<p style="margin:24px 0 0;font-family:Arial,sans-serif;font-size:13px;line-height:1.6;color:#64748B;">Reply directly to this email to contact '
                . esc_html( $reply_name ? $reply_name : $reply_email )
                . '.</p>';
        }

        $footer_note = $footer_note ? '<p style="margin:20px 0 0;padding-top:18px;border-top:1px solid #e8edf2;font-family:Arial,sans-serif;font-size:12px;line-height:1.6;color:#64748B;">' . esc_html( $footer_note ) . '</p>' : '';

        $body  = '<!doctype html><html><body style="margin:0;padding:0;background:#f1f4f7;">';
        $body .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;margin:0;padding:0;background:#f1f4f7;">';
        $body .= '<tr><td align="center" style="padding:32px 14px;">';
        $body .= '<table role="presentation" width="640" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e0e6eb;">';
        $body .= '<tr><td style="padding:26px 30px;background:#0B2240;border-bottom:5px solid #B8862A;">';
        $body .= '<a href="' . esc_url( $site_url ) . '" style="font-family:Arial,sans-serif;font-size:22px;font-weight:700;line-height:1.2;color:#ffffff;text-decoration:none;">' . esc_html( $site_name ) . '</a>';
        $body .= '<div style="margin-top:7px;font-family:Arial,sans-serif;font-size:11px;letter-spacing:1.6px;text-transform:uppercase;color:#d9e3ed;">Website notification</div>';
        $body .= '</td></tr>';
        $body .= '<tr><td style="padding:34px 30px 30px;">';
        $body .= '<div style="margin-bottom:10px;font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#B8862A;">New submission</div>';
        $body .= '<h1 style="margin:0 0 12px;font-family:Arial,sans-serif;font-size:25px;line-height:1.25;color:#0B2240;">' . esc_html( $title ) . '</h1>';
        $body .= '<p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#3F4A57;">' . esc_html( $intro ) . '</p>';
        $body .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;border:1px solid #e8edf2;border-collapse:collapse;">';
        $body .= '<tr><td colspan="2" style="padding:12px 16px;background:#eef3f7;border-bottom:1px solid #dfe6ec;font-family:Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#0B2240;">Submission details</td></tr>';
        $body .= $table_rows;
        $body .= '</table>';
        $body .= $reply_block;
        $body .= $footer_note;
        $body .= '</td></tr>';
        $body .= '<tr><td style="padding:18px 30px;background:#f4f1ea;font-family:Arial,sans-serif;font-size:11px;line-height:1.6;color:#64748B;">This is an automated notification from ' . esc_html( $site_name ) . '. Please keep this message for your records.</td></tr>';
        $body .= '</table></td></tr></table></body></html>';

        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        if ( is_email( $reply_email ) ) {
            $headers[] = 'Reply-To: ' . sanitize_text_field( $reply_name ) . ' <' . sanitize_email( $reply_email ) . '>';
        }

        return array(
            'body'    => $body,
            'headers' => $headers,
        );
    }
}