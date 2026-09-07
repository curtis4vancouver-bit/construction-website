<?php
/**
 * Keystone Possibilities - Luxury Lead Capture & Notification Engine
 * 
 * Provides zero-loss lead capture for high-net-worth custom home and commercial multiplex clients.
 * Handles both REST API (/wp-json/keystone/v1/lead) and WP AJAX (admin-ajax.php?action=keystone_lead_capture).
 * Automatically notifies Wayne Stevenson at keystonepossibilities@gmail.com and persists to wp_options.
 * 
 * @package KeystonePossibilitiesChild
 * @version 2.5.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ── 1. Register REST API Endpoint ───────────────────────────────────────────
add_action('rest_api_init', function () {
    register_rest_route('keystone/v1', '/lead', array(
        'methods' => 'POST',
        'callback' => 'keystone_possibilities_handle_lead_rest',
        'permission_callback' => '__return_true', // Public lead intake
    ));
});

// ── 2. Register WordPress AJAX Actions (Logged-in & Guest) ───────────────────
add_action('wp_ajax_keystone_lead_capture', 'keystone_possibilities_handle_lead_ajax');
add_action('wp_ajax_nopriv_keystone_lead_capture', 'keystone_possibilities_handle_lead_ajax');

/**
 * Handle REST API Lead Submission
 */
function keystone_possibilities_handle_lead_rest(WP_REST_Request $request) {
    $params = $request->get_json_params();
    if (empty($params)) {
        $params = $request->get_body_params();
    }
    return keystone_possibilities_process_lead($params);
}

/**
 * Handle WP AJAX Lead Submission
 */
function keystone_possibilities_handle_lead_ajax() {
    $params = $_POST;
    $response = keystone_possibilities_process_lead($params);
    
    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => $response->get_error_message()), 400);
    } else {
        wp_send_json_success($response->get_data());
    }
}

/**
 * Core Lead Processing, Storage & Email Dispatch
 */
function keystone_possibilities_process_lead($data) {
    // 1. Sanitize input fields
    $name         = sanitize_text_field($data['name'] ?? $data['fullName'] ?? 'Prospective Client');
    $email        = sanitize_email($data['email'] ?? '');
    $phone        = sanitize_text_field($data['phone'] ?? $data['phoneNumber'] ?? '');
    $project_type = sanitize_text_field($data['project_type'] ?? $data['service'] ?? 'General Consultation / Feasibility');
    $location     = sanitize_text_field($data['location'] ?? $data['city'] ?? 'Sea-to-Sky / Metro Vancouver');
    $budget       = sanitize_text_field($data['budget'] ?? 'Not Specified');
    $message      = sanitize_textarea_field($data['message'] ?? $data['details'] ?? $data['notes'] ?? '');
    $source_url   = esc_url_raw($data['source_url'] ?? $_SERVER['HTTP_REFERER'] ?? home_url());
    $ip_address   = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'Unknown');
    $timestamp    = current_time('mysql');

    // Validation: Email or Phone is mandatory
    if (empty($email) && empty($phone)) {
        return new WP_Error('missing_contact', 'Please provide either a valid email address or phone number.', array('status' => 400));
    }

    $lead_record = array(
        'id'           => uniqid('kp_lead_'),
        'timestamp'    => $timestamp,
        'name'         => $name,
        'email'        => $email,
        'phone'        => $phone,
        'project_type' => $project_type,
        'location'     => $location,
        'budget'       => $budget,
        'message'      => $message,
        'source_url'   => $source_url,
        'ip_address'   => $ip_address,
        'status'       => 'new'
    );

    // 2. Persist lead to WordPress Database (wp_options log)
    $leads_log = get_option('keystone_leads_log', array());
    if (!is_array($leads_log)) {
        $leads_log = array();
    }
    // Prepend to maintain newest first; keep up to 500 records
    array_unshift($leads_log, $lead_record);
    if (count($leads_log) > 500) {
        $leads_log = array_slice($leads_log, 0, 500);
    }
    update_option('keystone_leads_log', $leads_log, false);

    // 3. Dispatch Instant Notification to Wayne Stevenson
    $to = 'keystonepossibilities@gmail.com';
    $subject = '🏛️ [NEW INQUIRY] ' . $name . ' — ' . $project_type . ' (' . $location . ')';

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Keystone Possibilities <info@keystonepossibilities.ca>'
    );
    if (!empty($email)) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    }

    $body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background-color: #04070d; color: #f8fafc; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #0a0f19; border: 1px solid #d4af37; border-radius: 12px; padding: 28px; }
            .header { border-bottom: 1px solid rgba(212, 175, 55, 0.3); padding-bottom: 16px; margin-bottom: 20px; }
            .gold { color: #d4af37; font-weight: 800; font-size: 1.3rem; text-transform: uppercase; letter-spacing: 0.05em; }
            .sub { color: #94a3b8; font-size: 0.85rem; margin-top: 4px; }
            .row { margin-bottom: 12px; font-size: 0.95rem; }
            .label { color: #38bdf8; font-weight: 700; width: 140px; display: inline-block; }
            .value { color: #ffffff; }
            .msg-box { background: rgba(15, 23, 42, 0.8); border-left: 3px solid #d4af37; padding: 14px; margin-top: 16px; border-radius: 0 8px 8px 0; color: #e2e8f0; line-height: 1.5; font-size: 0.95rem; }
            .footer { margin-top: 24px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 14px; font-size: 0.78rem; color: #64748b; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="gold">Keystone Possibilities Ltd</div>
                <div class="sub">New Executive Construction & Feasibility Inquiry</div>
            </div>
            <div class="row"><span class="label">Client Name:</span><span class="value"><strong>' . esc_html($name) . '</strong></span></div>
            <div class="row"><span class="label">Email:</span><span class="value"><a href="mailto:' . esc_attr($email) . '" style="color:#00f0ff;">' . esc_html($email) . '</a></span></div>
            <div class="row"><span class="label">Phone:</span><span class="value"><a href="tel:' . esc_attr($phone) . '" style="color:#00f0ff;">' . esc_html($phone) . '</a></span></div>
            <div class="row"><span class="label">Project Scope:</span><span class="value">' . esc_html($project_type) . '</span></div>
            <div class="row"><span class="label">Location / Site:</span><span class="value">' . esc_html($location) . '</span></div>
            <div class="row"><span class="label">Estimated Budget:</span><span class="value">' . esc_html($budget) . '</span></div>
            <div class="row"><span class="label">Received At:</span><span class="value">' . esc_html($timestamp) . ' (PST)</span></div>
            <div class="row"><span class="label">Submitted From:</span><span class="value"><a href="' . esc_url($source_url) . '" style="color:#94a3b8;">' . esc_html($source_url) . '</a></span></div>
            
            ' . (!empty($message) ? '<div class="msg-box"><strong>Project Details / Scope:</strong><br>' . nl2br(esc_html($message)) . '</div>' : '') . '
            
            <div class="footer">
                Certified BC Housing Licensed Residential Builder #52603 | BC Hydro ES54 Civil Utility Contractor<br>
                1 Watts Point Road, Squamish, BC V8B 0B1 | (604) 848-9688
            </div>
        </div>
    </body>
    </html>';

    @wp_mail($to, $subject, $body, $headers);

    return new WP_REST_Response(array(
        'success' => true,
        'message' => 'Thank you, ' . $name . '. Wayne Stevenson has received your consultation request and will follow up within 24 hours to review zoning, municipal bylaws, and project parameters.',
        'lead_id' => $lead_record['id']
    ), 200);
}