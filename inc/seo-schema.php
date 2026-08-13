<?php
/**
 * Keystone Possibilities Child Theme - SEO & ParentOrganization Schema Engine
 * Keystone Empire Network — Master Authority Schema & Footer Cross-Link Mesh
 * 
 * @package KeystonePossibilitiesChild
 * @version 2.2.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// ── 1. Master JSON-LD Schema Injection (Local GEO + ParentOrganization Schema) ──
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
                "parentOrganization" => array(
                    "@type" => "Organization",
                    "@id" => "https://keystonepossibilities.ca/#parent-organization",
                    "name" => "Keystone Empire",
                    "alternateName" => "Keystone Group",
                    "url" => "https://keystonepossibilities.ca",
                    "description" => "Master parent organization network governing Keystone Possibilities Ltd. and Keystone Recomposition.",
                    "subOrganization" => array(
                        array(
                            "@type" => "Organization",
                            "@id" => "https://keystonepossibilities.ca/#organization",
                            "name" => "Keystone Possibilities Ltd.",
                            "url" => "https://keystonepossibilities.ca"
                        ),
                        array(
                            "@type" => "Organization",
                            "@id" => "https://keystonerecomposition.com/#organization",
                            "name" => "Keystone Recomposition",
                            "url" => "https://keystonerecomposition.com"
                        )
                    ),
                    "sameAs" => array(
                        "https://keystonepossibilities.ca",
                        "https://keystonerecomposition.com"
                    )
                ),
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
                    "https://www.instagram.com/keystonepossibilities",
                    "https://keystonerecomposition.com"
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
            // Parent Organization Entity (Keystone Empire / Keystone Group)
            array(
                "@type" => "Organization",
                "@id" => "https://keystonepossibilities.ca/#parent-organization",
                "name" => "Keystone Empire",
                "alternateName" => "Keystone Group",
                "url" => "https://keystonepossibilities.ca",
                "description" => "Master parent organization network governing Keystone Possibilities Ltd. and Keystone Recomposition.",
                "subOrganization" => array(
                    array(
                        "@type" => "Organization",
                        "@id" => "https://keystonepossibilities.ca/#organization",
                        "name" => "Keystone Possibilities Ltd.",
                        "url" => "https://keystonepossibilities.ca"
                    ),
                    array(
                        "@type" => "Organization",
                        "@id" => "https://keystonerecomposition.com/#organization",
                        "name" => "Keystone Recomposition",
                        "url" => "https://keystonerecomposition.com"
                    )
                ),
                "sameAs" => array(
                    "https://keystonepossibilities.ca",
                    "https://keystonerecomposition.com"
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

    echo "\n<!-- Keystone Possibilities 2026 Master Authority & ParentOrganization Schema -->\n";
    echo '<script type="application/ld+json">' . json_encode($master_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</script>\n";
}

// ── 2. Automatic VideoObject Schema Injector ────────────────────────────────
add_action('wp_head', 'keystone_possibilities_inject_video_schema', 2);
function keystone_possibilities_inject_video_schema() {
    if (!is_singular()) return;
    
    global $post;
    if (!$post) return;

    $post_id = $post->ID;
    $video_id = get_post_meta($post_id, 'keystone_youtube_id', true);
    
    if (empty($video_id)) {
        $video_url = get_post_meta($post_id, 'video_url', true);
        if (!empty($video_url) && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video_url, $m)) {
            $video_id = $m[1];
        }
    }

    if (empty($video_id)) {
        $content = $post->post_content;
        if (preg_match('/\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]{11})["\']/i', $content, $m)) {
            $video_id = $m[1];
        } elseif (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $content, $m)) {
            $video_id = $m[1];
        }
    }

    if (!empty($video_id)) {
        $video_title = get_post_meta($post_id, 'video_title', true);
        if (empty($video_title)) {
            $video_title = get_the_title($post_id);
        }

        $video_desc = get_post_meta($post_id, 'video_description', true);
        if (empty($video_desc)) {
            $video_desc = wp_strip_all_tags(get_the_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words($post->post_content, 35));
        }

        $video_schema = array(
            "@context" => "https://schema.org",
            "@type" => "VideoObject",
            "name" => esc_attr($video_title),
            "description" => esc_attr($video_desc),
            "thumbnailUrl" => array(
                "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg",
                "https://img.youtube.com/vi/{$video_id}/hqdefault.jpg"
            ),
            "uploadDate" => get_the_date('c', $post_id),
            "contentUrl" => "https://www.youtube.com/watch?v={$video_id}",
            "embedUrl" => "https://www.youtube.com/embed/{$video_id}",
            "publisher" => array("@id" => "https://keystonepossibilities.ca/#organization")
        );
        echo "\n<!-- Keystone Possibilities Dynamic VideoObject Schema -->\n";
        echo '<script type="application/ld+json">' . json_encode($video_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</script>\n";
    }
}

// ── 3. Keystone Empire Network Standardized Footer Bar ───────────────────────
add_action('wp_footer', 'keystone_possibilities_render_empire_footer', 30);
function keystone_possibilities_render_empire_footer() {
    ?>
    <!-- Keystone Empire Network Standardized Footer Bar -->
    <div class="keystone-empire-footer-bar" style="background:#04070d; border-top:1px solid rgba(196,162,101,0.25); padding:16px 20px; text-align:center; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; color:#94a3b8; font-size:0.82rem;">
        <div style="max-width:1200px; margin:0 auto; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
            <div style="display:flex; align-items:center; gap:10px; text-align:left;">
                <span style="color:#c4a265; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; font-size:0.78rem; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#c4a265" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    KEYSTONE EMPIRE NETWORK
                </span>
                <span style="color:rgba(255,255,255,0.2);">|</span>
                <span style="color:#cbd5e1; font-size:0.8rem; font-weight:500;">Keystone Possibilities — BC Building Code &amp; Construction Consulting</span>
            </div>
            <div style="display:flex; align-items:center; gap:8px; font-size:0.8rem;">
                <span style="color:#64748b;">Sister Flagship:</span>
                <a href="https://keystonerecomposition.com" target="_blank" rel="noopener" style="color:#c4a265; font-weight:600; text-decoration:none; transition:color 0.2s; display:inline-flex; align-items:center; gap:4px;">
                    Keystone Recomposition — Evidence-Based Clinical Peptides &amp; Protocol Analytics &#8594;
                </a>
            </div>
        </div>
    </div>
    <?php
}
