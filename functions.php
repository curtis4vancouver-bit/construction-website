<?php
/**
 * Keystone Possibilities Child Theme Functions
 * Certified BC Builder #52603 — Authority, SEO & GEO Schema Engine
 * 
 * @package KeystonePossibilitiesChild
 * @version 2.5.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ── 1. Enqueue Parent, Child Stylesheets & Media Facade Engine ──────────────
add_action('wp_enqueue_scripts', 'keystone_possibilities_enqueue_styles', 15);
function keystone_possibilities_enqueue_styles() {
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('keystone-possibilities-style', get_stylesheet_uri(), array('astra-parent-style'), '2.5.0');
    wp_enqueue_script('keystone-lazy-player', get_stylesheet_directory_uri() . '/js/lazy-player.js', array(), '2.5.0', true);

    // Pass REST and AJAX endpoints to frontend for interactive lead capture
    wp_localize_script('keystone-lazy-player', 'keystoneData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'restUrl' => esc_url_raw(rest_url('keystone/v1/lead'))
    ));
}

// Apply defer attribute to lazy player script for 100/100 Mobile PageSpeed
add_filter('script_loader_tag', 'keystone_possibilities_add_defer_attribute', 10, 2);
function keystone_possibilities_add_defer_attribute($tag, $handle) {
    if ('keystone-lazy-player' === $handle) {
        return str_replace(' src', ' defer="defer" src', $tag);
    }
    return $tag;
}

// ── 2. Require Master JSON-LD Schema & Lead Capture Engines ──────────────────
require_once __DIR__ . '/inc/seo-schema.php';
require_once __DIR__ . '/inc/lead-capture.php';

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
        $bg_img = 'background-image: url(' . esc_url($args['placeholder_img']) . ');';
    } else {
        $bg_img = 'background-image: url(https://i.ytimg.com/vi_webp/' . $media_id . '/maxresdefault.webp);';
    }

    ob_start();
    ?>
    <div class="keystone-lazy-video-container" data-video-id="<?php echo $media_id; ?>" data-video-type="<?php echo $media_type; ?>" style="<?php echo $bg_img; ?> background-size: cover; background-position: center; border-radius: 16px; position: relative; overflow: hidden; aspect-ratio: 16/9; max-width: 900px; margin: 30px auto; border: 1px solid rgba(0, 240, 255, 0.4); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);">
        <button class="keystone-play-button" aria-label="Play Construction Overview Video" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 72px; height: 72px; background: rgba(0, 240, 255, 0.85); border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 25px rgba(0, 240, 255, 0.6); transition: transform 0.2s ease, background 0.2s ease;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="#000" style="margin-left: 4px;">
                <path d="M8 5v14l11-7z"/>
            </svg>
        </button>
    </div>
    <?php
    return ob_get_clean();
}

// ── 4. Rank Math XML Sitemap Sanitizer & Cache Bypass ────────────────────────
add_filter('rank_math/sitemap/entry', 'keystone_possibilities_sanitize_rank_math_sitemap', 10, 3);
function keystone_possibilities_sanitize_rank_math_sitemap($url, $type, $object) {
    if (empty($url) || !is_array($url) || empty($url['loc'])) {
        return false;
    }

    $loc = $url['loc'];

    // 1. Exclude legacy redirected paths, test URLs, and .html extensions
    $excluded_patterns = array(
        'sample-page',
        '/test/',
        '/demo/',
        'wp-admin',
        'wp-login',
        'about-us-general-contractor-squamish.html',
        'squamish-custom-home-builder',
        'squamish-general-contractor',
        'west-vancouver-luxury-builder',
        'whistler-luxury-home-builder',
        'whistler-luxury-builder',
        'north-vancouver-home-builder',
        'feasibility-study',
        'cjc-1295-ipamorelin-glp-1-fatigue',
        'mounjaro-muscle-loss',
        'retatrutide-phase-3-data',
        'wolverine-stack',
        'glp-1',
        'peptide',
        'the-journey',
        'keystone_recomposition',
        '/tag/',
        '/author/',
        '/date/',
        '.html' // Force clean directory URLs in XML sitemaps
    );

    foreach ($excluded_patterns as $pat) {
        if (stripos($loc, $pat) !== false) {
            return false; // Strips from XML sitemap
        }
    }

    // Boundary-aware check for standalone test and demo slugs
    $url_path = parse_url($loc, PHP_URL_PATH) ?? '';
    if (preg_match('#/(test|demo)(/|$|\.php|\.html)#i', $url_path)) {
        return false;
    }

    // 2. Drop taxonomy archives, authors, and users from sitemaps if thin
    if (in_array($type, array('term', 'author', 'user'), true)) {
        return false;
    }

    // 3. Inspect Post object for noindex robots meta & publish status
    if (is_object($object) && isset($object->ID)) {
        $robots = get_post_meta($object->ID, 'rank_math_robots', true);
        if (is_array($robots) && in_array('noindex', $robots, true)) {
            return false;
        }
        if (is_string($robots) && stripos($robots, 'noindex') !== false) {
            return false;
        }
        if (get_post_status($object->ID) !== 'publish') {
            return false;
        }
    }

    // 4. Inspect Term object for noindex robots meta
    if ($type === 'term' && is_object($object) && isset($object->term_id)) {
        $term_robots = get_term_meta($object->term_id, 'rank_math_robots', true);
        if (is_array($term_robots) && in_array('noindex', $term_robots, true)) {
            return false;
        }
    }

    return $url;
}

// Disable sitemap caching for instant updates
add_filter('rank_math/sitemap/enable_caching', '__return_false');

// Dynamic Robots Noindex for Thin Archives (Resolves Crawled - Not Indexed)
add_action('wp_head', function () {
    if (is_tag() || is_date() || is_author() || is_search() || is_404()) {
        echo '<meta name="robots" content="noindex, follow" />' . "\n";
    }
}, 1);

// Suppress corrupt auto-detected Video Schema (Fix maxresdefau 11-char bug)
add_filter('rank_math/snippet/rich_snippet_video', '__return_empty_array');
add_filter('rank_math/schema/video', '__return_empty_array');

// ── 4.5. 301 Redirect & 410 Gone Handler for Pruned Legacy URLs ─────────────
add_action('template_redirect', 'keystone_possibilities_handle_301_410_redirects', 1);
function keystone_possibilities_handle_301_410_redirects() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = strtok($uri, '?');
    
    // Strict 301 redirects mapping all legacy/variant slugs to verified 200 OK live URLs
    $redirects_301 = array(
        '/about-us-general-contractor-squamish.html' => '/about-us-general-contractor-squamish/',
        '/about-us-general-contractor-squamish' => '/about-us-general-contractor-squamish/',
        
        // Squamish
        '/squamish-custom-home-builder.html' => '/squamish-custom-homes/',
        '/squamish-custom-home-builder/' => '/squamish-custom-homes/',
        '/squamish-custom-home-builder' => '/squamish-custom-homes/',
        '/squamish-general-contractor/' => '/squamish-custom-homes/',
        '/squamish-general-contractor' => '/squamish-custom-homes/',
        
        // Whistler
        '/whistler-luxury-builder.html' => '/whistler-custom-homes/',
        '/whistler-luxury-builder/' => '/whistler-custom-homes/',
        '/whistler-luxury-builder' => '/whistler-custom-homes/',
        '/whistler-luxury-home-builder/' => '/whistler-custom-homes/',
        '/whistler-luxury-home-builder' => '/whistler-custom-homes/',
        
        // West Vancouver
        '/west-vancouver-custom-homes.html' => '/west-vancouver-custom-homes/',
        '/west-vancouver-luxury-builder/' => '/west-vancouver-custom-homes/',
        '/west-vancouver-luxury-builder' => '/west-vancouver-custom-homes/',
        
        // North Vancouver
        '/north-vancouver-home-builder/' => '/north-vancouver-custom-homes/',
        '/north-vancouver-home-builder' => '/north-vancouver-custom-homes/',
        
        // Feasibility Plan
        '/feasibility-study/' => '/feasibility-plan/',
        '/feasibility-study' => '/feasibility-plan/',
        
        // Articles / Blog
        '/blog/' => '/#articles',
        '/blog' => '/#articles',
        '/articles/' => '/#articles',
        '/articles' => '/#articles',
        
        // Obsolete peptide pages redirected to root
        '/cjc-1295-ipamorelin-glp-1-fatigue.html' => '/',
        '/mounjaro-muscle-loss.html' => '/',
        '/retatrutide-phase-3-data.html' => '/',
        '/wolverine-stack.html' => '/',
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
                    <a href="/squamish-custom-homes/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Squamish Custom Home Builder</a>
                    <a href="/whistler-custom-homes/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Whistler Luxury Estate Builder</a>
                    <a href="/pemberton-luxury-builder/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Pemberton Acreage Builder</a>
                </div>
                <div>
                    <strong style="color:#fff; display:block; margin-bottom:6px; font-size:0.85rem;">Metro Vancouver:</strong>
                    <a href="/north-vancouver-custom-homes/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• North Vancouver Luxury Builder</a>
                    <a href="/north-vancouver-multiplex-conversions/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• North Vancouver Bill 44 Multiplex</a>
                    <a href="/west-vancouver-custom-homes/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• West Vancouver Steep Slope Builds</a>
                </div>
                <div>
                    <strong style="color:#fff; display:block; margin-bottom:6px; font-size:0.85rem;">Civil & Fiduciary PM:</strong>
                    <a href="/bc-hydro-registered-civil-contractor/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• BC Hydro Civil Utility Contractor</a>
                    <a href="/feasibility-plan/" style="color:#94a3b8; text-decoration:none; display:block; margin-bottom:4px; transition:color 0.2s;">• Construction Feasibility Studies</a>
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