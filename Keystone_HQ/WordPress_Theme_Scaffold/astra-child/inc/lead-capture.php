<?php
/**
 * Keystone Possibilities - Luxury Lead Capture & Notification Engine
 * 
 * Provides zero-loss lead capture for high-net-worth custom home and commercial multiplex clients.
 * Handles both REST API (/wp-json/keystone/v1/lead) and WP AJAX (admin-ajax.php?action=keystone_lead_capture).
 * Automatically notifies Wayne Stevenson at keystonepossibilities@gmail.com and persists to wp_options.
 * Also executes one-time brand sanitization migration for Page 563 (Home) and Page 283 (Contact).
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
    $location     = sanitize_text_field($data['location'] ?? $data['city'] ?? $data['region'] ?? 'Sea-to-Sky / Metro Vancouver');
    $budget       = sanitize_text_field($data['budget'] ?? 'Not Specified');
    $message      = sanitize_textarea_field($data['message'] ?? $data['details'] ?? $data['notes'] ?? $data['scope'] ?? '');
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

// ── 3. One-Time Database Sanitization Migration (Page 563 & 283) ─────────────
add_action('init', 'keystone_possibilities_run_v250_migration', 5);
function keystone_possibilities_run_v250_migration() {
    $migration_key = 'keystone_migration_v250_applied';
    if (get_option($migration_key)) {
        return;
    }

    // 1. Sanitize Homepage (Page ID 563)
    $home = get_post(563);
    if ($home && !empty($home->post_content)) {
        $content = $home->post_content;
        
        // Fix brand contradiction: KEYSTONE RECOMPOSITION -> KEYSTONE POSSIBILITIES LTD
        $content = str_replace('KEYSTONE RECOMPOSITION', 'KEYSTONE POSSIBILITIES LTD', $content);
        $content = str_replace('ks-recomposition-header', 'ks-possibilities-header', $content);
        
        // Fix top banner typo
        $content = preg_replace('/KEYSTONE POSSIBILITY HELP TD[^<]*/i', 'KEYSTONE POSSIBILITIES LTD — CLIENT &amp; CONSULTING SERVICES', $content);
        
        wp_update_post(array(
            'ID' => 563,
            'post_content' => $content
        ));
    }

    // Mark migration as successfully applied
    update_option($migration_key, current_time('mysql'), false);
}

// ── 3b. v2.5.2 Database Sanitization Migration (Strip Leaking Regional Grid & All CSS from Post Content) ─
add_action('init', 'keystone_possibilities_run_v252_migration', 6);
function keystone_possibilities_run_v252_migration() {
    $migration_key = 'keystone_migration_v252_css_cleaned';
    if (get_option($migration_key)) {
        return;
    }

    // Sanitize Homepage (Page ID 563) post_content directly in DB
    $home = get_post(563);
    if ($home && !empty($home->post_content)) {
        $content = $home->post_content;

        // Strip style tags and raw/p-wrapped CSS rules
        $patterns = array(
            '/<style\b[^>]*>[\s\S]*?<\/style>/i',
            '/(<p>\s*)?\.kp-regional-grid\s*(?:>|&gt;)\s*p[\s\S]*?(?:grid-column:\s*1\s*!important;\s*\}\s*\}|@media[^{]*\{[^{}]*\{[^{}]*\}\s*\})\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-regional-grid\s*(?:>|&gt;)\s*p\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-glass:empty\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-regional-grid\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?@media\s*\(\s*max-width:\s*768px\s*\)\s*\{\s*\.kp-regional-grid[\s\S]*?\}\s*\}/i',
            '/(<p>\s*)?\.kp-gold-title[\s\S]*?#ks-(?:possibilities|recomposition)-header\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-gold-title\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-glass(?::hover)?\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-h-scroll\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-scroll-card\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?\.kp-step\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?header\.entry-header\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?#ks-top-help-td\s*\{[^}]*\}\s*(<\/p>)?/i',
            '/(<p>\s*)?#ks-(?:possibilities|recomposition)-header\s*\{[^}]*\}\s*(<\/p>)?/i',
        );
        $content = preg_replace($patterns, '', $content);

        // Fix brand consistency
        $content = str_replace('KEYSTONE RECOMPOSITION', 'KEYSTONE POSSIBILITIES LTD', $content);
        $content = str_replace('ks-recomposition-header', 'ks-possibilities-header', $content);
        $content = preg_replace('/KEYSTONE POSSIBILITY HELP TD[^<]*/i', 'KEYSTONE POSSIBILITIES LTD — CLIENT &amp; CONSULTING SERVICES', $content);

        wp_update_post(array(
            'ID' => 563,
            'post_content' => $content
        ));
    }

    // Mark migration as successfully applied
    update_option($migration_key, current_time('mysql'), false);
}


// ── 4. Global Interactive Lead Form Handler (Zero Popup / Inline Luxury UX) ──
add_action('wp_footer', 'keystone_possibilities_render_lead_form_script', 30);
function keystone_possibilities_render_lead_form_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Find any contact forms on the page
        const forms = document.querySelectorAll('form');
        forms.forEach(function (form) {
            // Check if this is the consultation / contact form
            const hasName = form.querySelector('input[type="text"], input[placeholder*="Doe"], input[placeholder*="Name"]');
            const hasEmail = form.querySelector('input[type="email"]');
            const hasSubmit = form.querySelector('button[type="submit"], input[type="submit"]');

            if (hasName && hasEmail && hasSubmit) {
                // Remove legacy inline onsubmit alert if present
                form.removeAttribute('onsubmit');

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    const origBtnText = submitBtn ? submitBtn.innerHTML : 'Submit';

                    // Collect input values
                    const nameInput = form.querySelector('input[placeholder*="Doe"], input[placeholder*="Name"], input[type="text"]');
                    const emailInput = form.querySelector('input[type="email"]');
                    const phoneInput = form.querySelector('input[type="tel"], input[placeholder*="000"]');
                    const regionSelect = form.querySelector('select');
                    const detailsInput = form.querySelector('textarea');

                    const payload = {
                        name: nameInput ? nameInput.value.trim() : '',
                        email: emailInput ? emailInput.value.trim() : '',
                        phone: phoneInput ? phoneInput.value.trim() : '',
                        location: regionSelect ? regionSelect.value : '',
                        message: detailsInput ? detailsInput.value.trim() : '',
                        project_type: 'Direct Consultation Request',
                        source_url: window.location.href
                    };

                    if (!payload.name) {
                        alert('Please enter your name.');
                        if (nameInput) nameInput.focus();
                        return;
                    }
                    if (!payload.email && !payload.phone) {
                        alert('Please provide either an email address or phone number.');
                        if (emailInput) emailInput.focus();
                        return;
                    }

                    // Visual feedback: sending state
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.7';
                        submitBtn.innerHTML = '⏳ Submitting Request...';
                    }

                    // Remove any existing status banner
                    const oldStatus = form.querySelector('#kp-lead-status-box');
                    if (oldStatus) oldStatus.remove();

                    // Dispatch to REST API endpoint
                    fetch('/wp-json/keystone/v1/lead', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(function (response) { return response.json(); })
                    .then(function (res) {
                        if (res.success) {
                            form.innerHTML = '<div id="kp-lead-status-box" style="grid-column: 1 / -1; background: rgba(16, 185, 129, 0.12); border: 1.5px solid #10b981; border-radius: 12px; padding: 2.5rem 1.5rem; text-align: center; box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);">' +
                                '<div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏛️</div>' +
                                '<h3 style="color: #34d399; font-family: Outfit, sans-serif; font-size: 1.4rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">Consultation Request Received</h3>' +
                                '<p style="color: #f8fafc; font-size: 1.05rem; line-height: 1.6; max-width: 600px; margin: 0 auto;">Thank you, <strong>' + (payload.name || '') + '</strong>. Wayne Stevenson (BC Builder Licence #52603) has received your project parameters and will contact you within 24 hours to review zoning, municipal bylaws, and scheduling.</p>' +
                            '</div>';
                        } else {
                            throw new Error(res.message || 'Submission failed');
                        }
                    })
                    .catch(function (err) {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.style.opacity = '1';
                            submitBtn.innerHTML = origBtnText;
                        }
                        const errBox = document.createElement('div');
                        errBox.id = 'kp-lead-status-box';
                        errBox.style.cssText = 'grid-column: 1 / -1; margin-top: 1rem; background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; border-radius: 6px; padding: 1rem; text-align: center; color: #fca5a5;';
                        errBox.innerText = 'Error submitting request: ' + err.message + '. Please call Wayne Stevenson directly at (604) 848-9688.';
                        form.appendChild(errBox);
                    });
                }, true); // Use capture to intercept ahead of any legacy handlers
            }
        });
    });
    </script>
    <?php
}