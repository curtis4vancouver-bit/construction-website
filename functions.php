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

// ── 1. Enqueue Parent and Child Stylesheets ─────────────────────────────────
add_action('wp_enqueue_scripts', 'keystone_possibilities_enqueue_styles', 15);
function keystone_possibilities_enqueue_styles() {
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('keystone-possibilities-style', get_stylesheet_uri(), array('astra-parent-style'), '2.1.0');
}

// ── 2. Master JSON-LD Schema Injection (Local GEO + Builder Authority) ───────
add_action('wp_head', 'keystone_possibilities_inject_master_schema', 1);
function keystone_possibilities_inject_master_schema() {
    // Only inject on frontend
    if (is_admin()) return;

    $master_schema = array(
        "@context" => "https://schema.org",
        "@graph" => array(
            // Primary Business Entity
            array(
                "@type" => array("ConstructionBusiness", "GeneralContractor", "HomeAndConstructionBusiness"),
                "@id" => "https://keystonepossibilities.ca/#organization",
                "name" => "Keystone Possibilities Ltd",
                "legalName" => "Keystone Possibilities Ltd.",
                "url" => "https://keystonepossibilities.ca",
                "logo" => array(
                    "@type" => "ImageObject",
                    "@id" => "https://keystonepossibilities.ca/#logo",
                    "url" => "https://keystonepossibilities.ca/wp-content/uploads/logo.png",
                    "contentUrl" => "https://keystonepossibilities.ca/wp-content/uploads/logo.png",
                    "caption" => "Keystone Possibilities Ltd — BC Builder License #52603"
                ),
                "image" => "https://keystonepossibilities.ca/wp-content/uploads/logo.png",
                "description" => "Certified BC Housing Licensed Residential Builder (License #52603) specializing in custom luxury homes, fiduciary project management, Bill 44 multiplex conversions, and BC Hydro civil utility contracting across Squamish, Whistler, Pemberton, West Vancouver, and North Vancouver.",
                "telephone" => "+1-604-848-9688",
                "email" => "info@keystonepossibilities.ca",
                "priceRange" => "$$$$",
                "currenciesAccepted" => "CAD",
                "paymentAccepted" => "Bank Transfer, Check, Financing",
                "identifier" => array(
                    array(
                        "@type" => "PropertyValue",
                        "propertyID" => "BC Housing Licensed Residential Builder",
                        "value" => "52603",
                        "url" => "https://www.bchousing.org/licensing-consumer-disclosure/licencee-search"
                    ),
                    array(
                        "@type" => "PropertyValue",
                        "propertyID" => "BC Hydro Civil Utility Contractor",
                        "value" => "ES54 Registered Civil Contractor"
                    ),
                    array(
                        "@type" => "PropertyValue",
                        "propertyID" => "Home Warranty Protection",
                        "value" => "Mandatory 2-5-10 Year New Home Warranty (WBI / National Home Warranty)"
                    )
                ),
                "license" => "https://www.bchousing.org/licensing-consumer-disclosure/licencee-search",
                "address" => array(
                    "@type" => "PostalAddress",
                    "streetAddress" => "1 Watts Point Road",
                    "addressLocality" => "Squamish",
                    "addressRegion" => "BC",
                    "postalCode" => "V8B 0B1",
                    "addressCountry" => "CA"
                ),
                "geo" => array(
                    "@type" => "GeoCoordinates",
                    "latitude" => 49.6767,
                    "longitude" => -123.1508
                ),
                "areaServed" => array(
                    array("@type" => "City", "name" => "Squamish", "sameAs" => "https://en.wikipedia.org/wiki/Squamish,_British_Columbia"),
                    array("@type" => "City", "name" => "Whistler", "sameAs" => "https://en.wikipedia.org/wiki/Whistler,_British_Columbia"),
                    array("@type" => "City", "name" => "Pemberton", "sameAs" => "https://en.wikipedia.org/wiki/Pemberton,_British_Columbia"),
                    array("@type" => "City", "name" => "West Vancouver", "sameAs" => "https://en.wikipedia.org/wiki/West_Vancouver"),
                    array("@type" => "City", "name" => "North Vancouver", "sameAs" => "https://en.wikipedia.org/wiki/North_Vancouver_(city)"),
                    array("@type" => "City", "name" => "Lions Bay", "sameAs" => "https://en.wikipedia.org/wiki/Lions_Bay"),
                    array("@type" => "City", "name" => "Britannia Beach", "sameAs" => "https://en.wikipedia.org/wiki/Britannia_Beach")
                ),
                "hasOfferCatalog" => array(
                    "@type" => "OfferCatalog",
                    "name" => "Keystone Possibilities Construction & Fiduciary Services",
                    "itemListElement" => array(
                        array(
                            "@type" => "Offer",
                            "itemOffered" => array(
                                "@type" => "Service",
                                "name" => "Custom Luxury Home Construction",
                                "description" => "Turnkey design-build contracting with mandatory 2-5-10 Year Home Warranty, Step Code 5 energy performance, and high-altitude alpine framing."
                            )
                        ),
                        array(
                            "@type" => "Offer",
                            "itemOffered" => array(
                                "@type" => "Service",
                                "name" => "Fiduciary Project Management & Owner Representation",
                                "description" => "100% transparent cost-plus accounting, trade bidding oversight, municipal permit acceleration, and owner advocacy."
                            )
                        ),
                        array(
                            "@type" => "Offer",
                            "itemOffered" => array(
                                "@type" => "Service",
                                "name" => "BC Bill 44 Multiplex & Density Conversions",
                                "description" => "Comprehensive architectural feasibility, civil site servicing, and construction of 3-to-6 unit multiplexes under BC small-scale multi-unit housing legislation."
                            )
                        ),
                        array(
                            "@type" => "Offer",
                            "itemOffered" => array(
                                "@type" => "Service",
                                "name" => "BC Hydro Registered Civil Contracting",
                                "description" => "ES54 certified underground utility trenching, ducting, civil excavation, and municipal infrastructure connections."
                            )
                        ),
                        array(
                            "@type" => "Offer",
                            "itemOffered" => array(
                                "@type" => "Service",
                                "name" => "Construction Feasibility Studies & Land Due Diligence",
                                "description" => "Pre-acquisition site feasibility, geotechnical review, civil utility costing, and municipal zoning risk assessments."
                            )
                        )
                    )
                ),
                "sameAs" => array(
                    "https://www.facebook.com/profile.php?id=61554185128555",
                    "https://www.youtube.com/@KeystonePossibilities",
                    "https://www.instagram.com/keystonepossibilities"
                ),
                "founder" => array(
                    "@type" => "Person",
                    "@id" => "https://keystonepossibilities.ca/#founder",
                    "name" => "Wayne Stevenson",
                    "jobTitle" => "Certified BC Builder & Fiduciary Project Manager",
                    "url" => "https://keystonepossibilities.ca/about-us-general-contractor-squamish/",
                    "sameAs" => "https://keystonerecomposition.com/about/"
                )
            ),
            // WebSite Node with SearchAction
            array(
                "@type" => "WebSite",
                "@id" => "https://keystonepossibilities.ca/#website",
                "url" => "https://keystonepossibilities.ca",
                "name" => "Keystone Possibilities Ltd",
                "publisher" => array("@id" => "https://keystonepossibilities.ca/#organization"),
                "potentialAction" => array(
                    "@type" => "SearchAction",
                    "target" => "https://keystonepossibilities.ca/?s={search_term_string}",
                    "query-input" => "required name=search_term_string"
                )
            )
        )
    );

    echo "\n<!-- Keystone Possibilities 2026 Master Authority Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($master_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</script>\n";
}

// ── 3. Automatic VideoObject Schema Injector for Blog Posts & Showcases ──────
add_action('wp_head', 'keystone_possibilities_inject_video_schema', 2);
function keystone_possibilities_inject_video_schema() {
    if (!is_singular()) return;
    
    global $post;
    if (!$post) return;

    // Check if post contains YouTube embed or video ID
    $content = $post->post_content;
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $content, $matches)) {
        $video_id = $matches[1];
        $video_schema = array(
            "@context" => "https://schema.org",
            "@type" => "VideoObject",
            "name" => get_the_title(),
            "description" => wp_strip_all_tags(get_the_excerpt() ? get_the_excerpt() : wp_trim_words($content, 35)),
            "thumbnailUrl" => array(
                "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg",
                "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg"
            ),
            "uploadDate" => get_the_date('c'),
            "embedUrl" => "https://www.youtube.com/embed/{$video_id}",
            "publisher" => array("@id" => "https://keystonepossibilities.ca/#organization")
        );
        echo "\n<!-- Keystone Possibilities VideoObject Schema -->\n";
        echo '<script type="application/ld+json">' . json_encode($video_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</script>\n";
    }
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
