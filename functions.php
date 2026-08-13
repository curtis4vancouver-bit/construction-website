<?php
/**
 * Keystone Possibilities Child Theme Functions
 * Certified BC Builder #52603 — Authority, SEO & GEO Schema Engine
 * 
 * @package KeystonePossibilitiesChild
 * @version 2.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ── 1. Enqueue Parent, Child Stylesheets & Media Facade Engine ──────────────
add_action('wp_enqueue_scripts', 'keystone_possibilities_enqueue_styles', 15);
function keystone_possibilities_enqueue_styles() {
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('keystone-possibilities-style', get_stylesheet_uri(), array('astra-parent-style'), '2.1.0');
    wp_enqueue_script('keystone-lazy-player', get_stylesheet_directory_uri() . '/js/lazy-player.js', array(), '2.1.0', true);
}

// Apply defer attribute to lazy player script for 100/100 Mobile PageSpeed
add_filter('script_loader_tag', 'keystone_possibilities_add_defer_attribute', 10, 2);
function keystone_possibilities_add_defer_attribute($tag, $handle) {
    if ('keystone-lazy-player' === $handle) {
        return str_replace(' src', ' defer="defer" src', $tag);
    }
    return $tag;
}

// ── 2. Require Master JSON-LD Schema & ParentOrganization Engine ─────────────
require_once __DIR__ . '/inc/seo-schema.php';

// ── 3. WebP Video Facade Player Shortcode ([keystone_video]) ─────────────────
add_shortcode('keystone_video', 'keystone_possibilities_lazy_video_shortcode');
function keystone_possibilities_lazy_video_shortcode($atts) {
    $args = shortcode_atts(array(
        'id'   => '',
        'type' => 'youtube',
        'placeholder_img' => '',
    ), $atts);

    if (empty($args['id'])) {
        return '<p style="color: #FC8181; font-family: monospace;">[Error] Media Asset ID is missing.</p>';
    }

    $media_id   = esc_attr($args['id']);
    $media_type = esc_attr(strtolower($args['type']));
    
    $bg_img = '';
    if (!empty($args['placeholder_img'])) {
        $bg_img = esc_url($args['placeholder_img']);
    } elseif ($media_type === 'youtube') {
        $bg_img = 'https://img.youtube.com/vi/' . $media_id . '/maxresdefault.jpg';
    } else {
        $bg_img = 'https://keystonepossibilities.ca/wp-content/uploads/whistler_luxury_estate.jpg';
    }

    ob_start();
    ?>
    <div class="luxury-video-facade" 
         data-video-id="<?php echo $media_id; ?>" 
         data-video-type="<?php echo $media_type; ?>" 
         role="region" 
         aria-label="Video Player Placeholder">
        
        <div class="facade-background" style="background-image: url('<?php echo $bg_img; ?>');"></div>
        <div class="facade-overlay"></div>
        
        <button class="play-button" aria-label="Play Embedded Video">
            <svg class="play-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 5V19L19 12L8 5Z" fill="currentColor"/>
            </svg>
        </button>
        <noscript>
            <iframe src="https://www.youtube.com/embed/<?php echo $media_id; ?>?rel=0" width="100%" height="100%" style="position: absolute; top: 0; left: 0;" frameborder="0" allowfullscreen></iframe>
        </noscript>
    </div>
    <?php
    return ob_get_clean();
}

// ── 4. Rank Math XML Sitemap Sanitizer (Eliminates GSC 301s and 404s) ────────
add_filter('rank_math/sitemap/entry', 'keystone_possibilities_sanitize_rank_math_sitemap', 10, 3);
function keystone_possibilities_sanitize_rank_math_sitemap($url, $type, $object) {
    // List of redirected, draft, or legacy URLs to strictly exclude from XML sitemaps
    $excluded_patterns = array(
        'sample-page',
        'test',
        'demo',
        'wp-admin',
        'cjc-1295-ipamorelin-glp-1-fatigue',
        'mounjaro-muscle-loss',
        'retatrutide-phase-3-data',
        'wolverine-stack',
        'glp-1',
        'peptide',
        '.html' // Force clean directory URLs in XML sitemaps
    );

    if (isset($url['loc'])) {
        foreach ($excluded_patterns as $pat) {
            if (strpos($url['loc'], $pat) !== false) {
                return false; // Strips from XML sitemap
            }
        }
    }
    return $url;
}

// ── 4.5. 301 Redirect & 410 Gone Handler for Pruned Legacy URLs ─────────────
add_action('template_redirect', 'keystone_possibilities_handle_301_410_redirects', 1);
function keystone_possibilities_handle_301_410_redirects() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = strtok($uri, '?');
    
    // Exact 301 redirects for legacy URLs
    $redirects_301 = array(
        '/about-us-general-contractor-squamish.html' => '/about-us-general-contractor-squamish/',
        '/about-us-general-contractor-squamish' => '/about-us-general-contractor-squamish/',
        '/west-vancouver-custom-homes.html' => '/west-vancouver-luxury-builder/',
        '/west-vancouver-custom-homes/' => '/west-vancouver-luxury-builder/',
        '/west-vancouver-custom-homes' => '/west-vancouver-luxury-builder/',
        '/squamish-custom-home-builder.html' => '/squamish-general-contractor/',
        '/whistler-luxury-builder.html' => '/whistler-luxury-home-builder/',
        '/pemberton-luxury-builder.html' => '/pemberton-luxury-builder/',
        '/cjc-1295-ipamorelin-glp-1-fatigue.html' => '/blog/',
        '/mounjaro-muscle-loss.html' => '/blog/',
        '/retatrutide-phase-3-data.html' => '/blog/',
        '/wolverine-stack.html' => '/blog/',
    );

    if (isset($redirects_301[$path])) {
        wp_redirect(home_url($redirects_301[$path]), 301);
        exit;
    }

    // 410 Gone for obsolete legacy files/directories
    $gone_paths = array(
        '/keystone_recomposition_/',
        '/logo/',
        '/keystone-recomposition-ltd/',
        '/keystone_recomposition_ltd_invert-removebg-preview/',
        '/the-journey/',
    );

    $normalized_path = '/' . trim($path, '/') . '/';
    if (in_array($normalized_path, $gone_paths, true)) {
        status_header(410);
        nocache_headers();
        echo '410 Gone - Resource permanently removed.';
        exit;
    }
}

// ── 5. Regional SEO/GEO Footer Mesh (Resolves GSC Crawl & Orphan Errors) ─────
add_action('wp_footer', 'keystone_possibilities_render_geo_mesh', 20);
function keystone_possibilities_render_geo_mesh() {
    ?>
    <div class="keystone-geo-footer-mesh" style="border-top:1px solid rgba(255,255,255,0.08); padding:28px 20px; background:#070b14; color:#94a3b8; font-size:0.82rem; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
        <div style="max-width:1200px; margin:0 auto;">
            <div style="color:#00f0ff; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:14px; font-size:0.75rem; display:flex; align-items:center; gap:8px;">
                <span>🏛️</span> KEYSTONE POSSIBILITIES — REGIONAL DIVISIONS & SPECIALIZED SERVICES
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; line-height:1.6;">
                <div>
                    <strong style="color:#fff; display:block; margin-bottom:6px; font-size:0.85rem;">Sea-to-Sky Corridor:</strong>
                    <a href="/squamish-general-contractor/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Squamish Custom Home Builder</a>
                    <a href="/whistler-luxury-home-builder/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Whistler Luxury Estate Builder</a>
                    <a href="/pemberton-luxury-builder/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Pemberton Acreage Builder</a>
                </div>
                <div>
                    <strong style="color:#fff; display:block; margin-bottom:6px; font-size:0.85rem;">Metro Vancouver:</strong>
                    <a href="/north-vancouver-home-builder/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• North Vancouver Luxury Builder</a>
                    <a href="/north-vancouver-multiplex-conversions/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• North Vancouver Bill 44 Multiplex</a>
                    <a href="/west-vancouver-luxury-builder/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• West Vancouver Steep Slope Builds</a>
                </div>
                <div>
                    <strong style="color:#fff; display:block; margin-bottom:6px; font-size:0.85rem;">Civil & Fiduciary PM:</strong>
                    <a href="/bc-hydro-registered-civil-contractor/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• BC Hydro Civil Utility Contractor</a>
                    <a href="/feasibility-study/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Construction Feasibility Studies</a>
                    <a href="/private-investors/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Private Investor Joint Ventures</a>
                </div>
                <div>
                    <strong style="color:#fff; display:block; margin-bottom:6px; font-size:0.85rem;">Direct Authority & Contact:</strong>
                    <span style="color:#94a3b8; display:block; margin-bottom:4px;">BC Housing License #52603</span>
                    <a href="tel:+16048489688" style="color:#00f0ff; text-decoration:none; font-weight:700; display:block; margin-bottom:4px; font-size:0.9rem;">📞 (604) 848-9688</a>
                    <a href="/contact-general-contractor-squamish/" style="color:#94a3b8; text-decoration:none; display:block; transition:color 0.2s;">• Schedule Fiduciary Consultation</a>
                </div>
            </div>
        </div>
    </div>
    <a href="tel:+16048489688" class="keystone-mobile-call-bar" aria-label="Call Keystone Possibilities (604) 848-9688">
        <span>📞 (604) 848-9688</span>
    </a>
    <?php
}
