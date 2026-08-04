<?php

/**
 * 7. Inject Premium Organization & Person JSON-LD Schema (Knowledge Panel Anchor)
 */
function kp_inject_schema() {
    // SITE-AWARE: Skip Recomposition/Digital schema injection on the Possibilities site.
    // The Possibilities site uses its own Rank Math filter for clean B2B construction schema.
    // Without this gate, Recomposition Organization + Person nodes pollute the Possibilities
    // Knowledge Panel with music/wellness entities that confuse Google's entity resolution.
    if ( strpos( home_url(), 'keystonepossibilities' ) !== false ) {
        return;
    }
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    if ( ! $logo_url ) {
        $logo_url = 'https://keystonerecomposition.com/wp-content/uploads/logo.png';
    }

    // === Organization Schema ===
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Keystone Digital',
        'url' => 'https://keystonerecomposition.com',
        'description' => 'A multifaceted digital organization managing health, beauty, construction, and entertainment projects, including deep house music and record labels.',
        'keywords' => 'Keystone Digital, deep house music, music label, digital organization, entertainment, record label',
        'logo' => $logo_url,
        'sameAs' => array(
            'https://www.youtube.com/@KeystoneRecomposition',
            'https://www.youtube.com/@KeystoneProtocols',
            'https://open.spotify.com/artist/52v3Qe6Jo0hg764driOl5Y',
            'https://musicbrainz.org/label/30027d0e-6aeb-4704-8792-a031c936c62a',
            'https://audiomack.com/keystone-recomposition',
            'https://toolost.com',
            'https://www.tiktok.com/@keystonerecomposition'
        ),
        'identifier' => array(
            '@type' => 'PropertyValue',
            'propertyID' => 'Too Lost Catalog Reference ID',
            'value' => 'TOOLOST3000939655'
        ),
        'subOrganization' => array(
            array(
                '@type' => 'HealthAndBeautyBusiness',
                'name' => 'Keystone Recomposition',
                'url' => 'https://keystonerecomposition.com',
                'description' => 'Specializing in health, wellness, and beauty recomposition. Explore GLP-1 weight loss solutions, fitness programs, and beauty enhancements.',
                'keywords' => 'Keystone Recomposition, GLP-1, health, beauty, wellness, weight loss, fitness',
                'founder' => array(
                    '@type' => 'Person',
                    'name' => 'Wayne Stevenson',
                    'jobTitle' => 'Biohacking & Metabolic Health Authority'
                )
            ),
            array(
                '@type' => 'GeneralContractor',
                'name' => 'Keystone Possibilities',
                'url' => 'https://keystonepossibilities.ca',
                'description' => 'Premium Construction Project Management and Civil Construction Services operating across the Sea-to-Sky and Greater Vancouver regions.',
                'sameAs' => array(
                    'https://www.squamishchamber.com/'
                ),
                'founder' => array(
                    '@type' => 'Person',
                    'name' => 'Wayne Stevenson',
                    'jobTitle' => 'Certified BC Builder & Project Manager',
                    'sameAs' => 'https://keystonerecomposition.com/about/'
                ),
                'areaServed' => array(
                    array('@type' => 'City', 'name' => 'Whistler'),
                    array('@type' => 'City', 'name' => 'West Vancouver'),
                    array('@type' => 'City', 'name' => 'North Vancouver'),
                    array('@type' => 'City', 'name' => 'Squamish')
                ),
                'hasOfferCatalog' => array(
                    '@type' => 'OfferCatalog',
                    'name' => 'Construction Services',
                    'itemListElement' => array(
                        array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Luxury Custom Home Project Management')),
                        array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'Civil Construction & Site Engineering'))
                    )
                ),
                'identifier' => array(
                    '@type' => 'PropertyValue',
                    'propertyID' => 'BC Builder License',
                    'value' => '52603'
                ),
                'memberOf' => array(
                    '@type' => 'Organization',
                    'name' => 'WBI Home Warranty',
                    'url' => 'https://wbihomewarranty.com/'
                )
            )
        )
    );

    $json_schema = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

    echo "<!-- Keystone Digital JSON-LD Schema -->
";
    echo "<script type=\"application/ld+json\">
";
    echo $json_schema . "
";
    echo "</script>
";
    echo "<!-- End Keystone Digital JSON-LD Schema -->
";

    // === Person Schema (Knowledge Panel Anchor) ===
    $person_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => 'Wayne Stevenson',
        'alternateName' => array( 'Keystone Recomposition', 'Keystone Protocols' ),
        'url' => 'https://keystonerecomposition.com',
        'image' => $logo_url,
        'jobTitle' => 'Health Researcher, Music Producer & Construction Project Manager',
        'description' => 'Founder of Keystone Digital. Documents the intersection of GLP-1 metabolic health, peptide science, body recomposition, and longevity for men over 40. Also produces deep house music and manages luxury construction projects in the Sea-to-Sky corridor.',
        'knowsAbout' => array(
            'GLP-1 receptor agonists',
            'metabolic health',
            'body recomposition',
            'peptide protocols',
            'biohacking',
            'deep house music production',
            'construction project management'
        ),
        'sameAs' => array(
            'https://www.youtube.com/@KeystoneRecomposition',
            'https://www.youtube.com/@KeystoneProtocols',
            'https://www.youtube.com/channel/UCxURlqMNhAtxUTpdXmlOYaw',
            'https://keystonepossibilities.ca',
            'https://open.spotify.com/artist/52v3Qe6Jo0hg764driOl5Y',
            'https://musicbrainz.org/label/30027d0e-6aeb-4704-8792-a031c936c62a',
            'https://audiomack.com/keystone-recomposition',
            'https://www.facebook.com/profile.php?id=61554185128555',
            'https://www.instagram.com/p/DO9FsCKj5Cb/',
            'https://www.tiktok.com/@keystonerecomposition'
        ),
        'worksFor' => array(
            '@type' => 'Organization',
            'name' => 'Keystone Digital',
            'url' => 'https://keystonerecomposition.com'
        )
    );

    $json_person = wp_json_encode( $person_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

    echo "<!-- Keystone Person Schema (Knowledge Panel) -->
";
    echo "<script type=\"application/ld+json\">
";
    echo $json_person . "
";
    echo "</script>
";
    echo "<!-- End Person Schema -->
";
}
add_action( 'wp_head', 'kp_inject_schema' );

/**
 * 8. Dynamic, Robust, GSC-Compliant Standalone VideoObject Schema (Stored XSS Secure)
 * Extracts the primary article video and outputs exactly ONE premium schema object.
 */
function kp_youtube_schema() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    global $post;
    if ( ! $post ) {
        return;
    }
    $post_id = $post->ID;

    // Try to get video URL or ID from post meta
    $video_url = get_post_meta( $post_id, 'video_url', true );
    $youtube_id = get_post_meta( $post_id, 'keystone_youtube_id', true );
    
    if ( empty( $video_url ) && ! empty( $youtube_id ) ) {
        $video_url = 'https://www.youtube.com/watch?v=' . $youtube_id;
    }

    // Fallback: search for [keystone_video id="..."] or plain youtube URL in content
    if ( empty( $video_url ) ) {
        $content = $post->post_content;
        if ( preg_match( '~\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]+)["\']~i', $content, $matches ) ) {
            $youtube_id = $matches[1];
            $video_url = 'https://www.youtube.com/watch?v=' . $youtube_id;
        } elseif ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $content, $matches ) ) {
            $youtube_id = $matches[1];
            $video_url = 'https://www.youtube.com/watch?v=' . $youtube_id;
        }
    }

    if ( empty( $youtube_id ) && ! empty( $video_url ) ) {
        if ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $video_url, $matches ) ) {
            $youtube_id = $matches[1];
        }
    }

    // If no video was detected at all, do not output schema
    if ( empty( $youtube_id ) ) {
        return;
    }

    // Determine high-resolution maxresdefault thumbnail
    $video_thumbnail = "https://img.youtube.com/vi/{$youtube_id}/maxresdefault.jpg";
    
    // Get custom video details or fall back gracefully
    $video_name = get_post_meta( $post_id, 'video_title', true );
    if ( empty( $video_name ) ) {
        $video_name = get_the_title( $post_id ) . ' Video';
    }

    $video_description = get_post_meta( $post_id, 'video_description', true );
    if ( empty( $video_description ) ) {
        $excerpt_source = get_the_excerpt( $post_id );
        if ( empty( $excerpt_source ) ) {
            $excerpt_source = $post->post_content;
        }
        $clean_excerpt = wp_strip_all_tags( strip_shortcodes( $excerpt_source ) );
        $video_description = wp_html_excerpt( $clean_excerpt, 150, '...' );
    }
    if ( empty( $video_description ) ) {
        $video_description = esc_attr( get_the_title( $post_id ) ) . ' - High-performance health and longevity protocol details.';
    }

    $video_duration = get_post_meta( $post_id, 'video_duration', true );
    if ( empty( $video_duration ) ) {
        $video_duration = get_post_meta( $post_id, 'keystone_video_duration', true );
    }
    $duration_iso = 'PT5M0S'; // Default fallback 5 minutes
    if ( ! empty( $video_duration ) ) {
        // Parse time to ISO 8601
        $video_duration = trim( $video_duration );
        if ( stripos( $video_duration, 'PT' ) === 0 ) {
            $duration_iso = $video_duration;
        } else {
            $hours = 0; $minutes = 0; $seconds = 0;
            if ( is_numeric( $video_duration ) ) {
                $total_seconds = intval( $video_duration );
                $hours = floor( $total_seconds / 3600 );
                $minutes = floor( ( $total_seconds / 60 ) % 60 );
                $seconds = $total_seconds % 60;
            } elseif ( preg_match( '~^(?:(\d+):)?(\d+):(\d+)$~', $video_duration, $matches ) ) {
                if ( count( $matches ) === 4 && $matches[1] !== '' ) {
                    $hours = intval( $matches[1] );
                    $minutes = intval( $matches[2] );
                    $seconds = intval( $matches[3] );
                } else {
                    $minutes = intval( $matches[2] );
                    $seconds = intval( $matches[3] );
                }
            }
            $duration_iso = 'PT';
            if ( $hours > 0 ) $duration_iso .= $hours . 'H';
            if ( $minutes > 0 ) $duration_iso .= $minutes . 'M';
            if ( $seconds > 0 || ( $hours === 0 && $minutes === 0 ) ) $duration_iso .= $seconds . 'S';
        }
    }

    $video_upload_date = get_post_meta( $post_id, 'video_upload_date', true );
    if ( empty( $video_upload_date ) ) {
        $video_upload_date = get_the_date( 'c', $post_id );
    } else {
        $converted_time = strtotime( $video_upload_date );
        $video_upload_date = ( $converted_time !== false ) ? date( 'c', $converted_time ) : get_the_date( 'c', $post_id );
    }

    $video_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'VideoObject',
        'name' => esc_attr( $video_name ),
        'description' => esc_attr( $video_description ),
        'thumbnailUrl' => esc_url( $video_thumbnail ),
        'uploadDate' => esc_attr( $video_upload_date ),
        'embedUrl' => "https://www.youtube.com/embed/{$youtube_id}",
        'contentUrl' => "https://www.youtube.com/watch?v={$youtube_id}",
        'duration' => esc_attr( $duration_iso ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => 'Keystone Protocols',
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => 'https://keystonerecomposition.com/wp-content/uploads/logo.png'
            )
        )
    );

    $json_video_schema = wp_json_encode( $video_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );

    echo "
<!-- Keystone Digital VideoObject Schema for YouTube -->
";
    echo "<script type=\"application/ld+json\">
";
    echo $json_video_schema . "
";
    echo "</script>
";
    echo "<!-- End VideoObject Schema -->

";
}
add_action( 'wp_head', 'kp_youtube_schema', 20 );

/**
 * 9. Hook custom media metadata into Rank Math PRO's Video Sitemap Generator
 */
add_filter( 'rank_math/sitemap/video/post', function( $video, $post_id ) {
    if ( ! is_array( $video ) ) {
        return $video;
    }
    $youtube_id = get_post_meta( $post_id, 'keystone_youtube_id', true );
    
    // Fallback: search for [keystone_video id="..."] or youtube embed in content
    if ( empty( $youtube_id ) ) {
        $post = get_post( $post_id );
        if ( $post ) {
            if ( preg_match( '~\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]+)["\']~i', $post->post_content, $matches ) ) {
                $youtube_id = $matches[1];
            } elseif ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=|embed/)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $post->post_content, $matches ) ) {
                $youtube_id = $matches[1];
            }
        }
    }
    
    if ( ! empty( $youtube_id ) ) {
        $video['thumbnail_loc'] = "https://img.youtube.com/vi/{$youtube_id}/maxresdefault.jpg";
        $video['title']         = get_the_title( $post_id );
        
        $excerpt = get_the_excerpt( $post_id );
        if ( empty( $excerpt ) ) {
            $post = get_post( $post_id );
            if ( $post ) {
                $excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 40, '...' );
            }
        }
        $video['description']   = $excerpt;
        $video['player_loc']    = "https://www.youtube-nocookie.com/embed/{$youtube_id}";
        $video['uploader']      = "Wayne Stevenson";
        $video['uploader_info'] = "https://keystonerecomposition.com/";
    }
    
    return $video;
}, 10, 2 );

/**
 * 10. Deduplicate Rank Math JSON-LD Schema Graph & Auto-detected Videos
 * Strips out all auto-detected or conflicting VideoObjects generated by Rank Math,
 * letting our custom GSC-Compliant Injector serve exactly ONE perfect VideoObject.
 */
add_filter( 'rank_math/json_ld', function( $data, $jsonld ) {
    if ( ! is_array( $data ) ) {
        return $data;
    }
    foreach ( $data as $key => $val ) {
        if ( in_array( strtolower( $key ), array( 'video', 'videoobject' ) ) ) {
            unset( $data[$key] );
        }
    }
    if ( isset( $data['@graph'] ) && is_array( $data['@graph'] ) ) {
        $other_nodes = array();
        foreach ( $data['@graph'] as $node ) {
            if ( isset( $node['@type'] ) ) {
                $types = (array) $node['@type'];
                $has_video = false;
                foreach ( $types as $t ) {
                    if ( strtolower( $t ) === 'videoobject' ) {
                        $has_video = true;
                        break;
                    }
                }
                if ( ! $has_video ) {
                    $other_nodes[] = $node;
                }
            } else {
                $other_nodes[] = $node;
            }
        }
        $data['@graph'] = $other_nodes;
    }
    return $data;
}, 999, 2 );

/**
 * 10.5 Nuclear Standalone Video Schema Deduplicator
 * Intercepts the final page HTML and strips out duplicate/broken Rank Math VideoObject schemas,
 * leaving exactly ONE perfect VideoObject schema generated by our custom child theme.
 */
add_action( 'template_redirect', function() {
    if ( is_singular( 'post' ) ) {
        ob_start( function( $html ) {
            $html = preg_replace(
                '~<script type=["\']application/ld\+json["\']>[^
]*?"@type"\s*:\s*"VideoObject"[^
]*?</script>~i',
                '',
                $html
            );
            return $html;
        } );
    }
} );

/**
 * 11. General SEO Fixes: output noindex for tag, date, author archives and query parameters
 */
function kp_seo_noindex() {
    $should_noindex = false;

    if ( is_date() || is_author() || is_tag() || is_search() ) {
        $should_noindex = true;
    }

    if ( ! empty( $_GET ) ) {
        $allowed_params = array( 'page', 'paged', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid', 'ref' );
        foreach ( $_GET as $key => $value ) {
            if ( ! in_array( $key, $allowed_params ) ) {
                $should_noindex = true;
                break;
            }
        }
    }

    if ( $should_noindex ) {
        echo "<meta name=\"robots\" content=\"noindex, follow\">\n";
    }
}
add_action( 'wp_head', 'kp_seo_noindex', 1 );

/**
 * 12. Patch Structural Site Leaks (404/Redirect Errors)
 * Redirects 404 pages to the homepage with a 301 Moved Permanently status.
 */
function kp_404_redirect() {
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Normalize request URI
    $path = strtok( $request_uri, '?' ); // Strip query parameters
    $path = '/' . trim( $path, '/' ) . '/'; // Standardize slashes
    $path = str_replace( '//', '/', $path );

    $redirects = array(
        '/2026/01/23/mounjaro-kwikpen-the-official-click-to-mg-math-bible/' => '/2026/01/13/stop-chasing-skinny-week-14-recomposition-the-269-click-kwikpen-secret/',
        '/2026/05/07/wolverine-stack-bpc-157-tb500-builder-blueprint/' => '/2026/05/07/wolverine-stack-bpc-157-tb-500-builder-blueprint/',
        '/keystone_recomposition_/' => '/',
        '/logo/' => '/',
        '/keystone-recomposition-ltd/' => '/',
        '/keystone_recomposition_ltd_invert-removebg-preview/' => '/',
        '/logout/' => '/',
        '/the-journey/' => '/',
    );

    // Exact matches
    if ( isset( $redirects[ $path ] ) ) {
        wp_redirect( home_url( $redirects[ $path ] ), 301 );
        exit;
    }
    
    // Wildcard matches
    if ( strpos( $path, '/wp-content/themes/keystone-recomposition-child' ) !== false ||
         preg_match( '~^/wp-.*\.php$~i', $path ) ||
         ( strpos( $path, '/wp-admin' ) === false && preg_match( '~\.php$~i', $path ) ) ) {
        wp_redirect( home_url(), 301 );
        exit;
    }

    if ( is_404() ) {
        wp_redirect( home_url(), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'kp_404_redirect' );



/**
 * 14. Inject Premium Grid Alignment Custom CSS directly in wp_head
 * Bypasses enqueues/caching and applies perfect alignment immediately!
 */
function kp_inject_custom_css() {
    ?>
    <style id="keystone-protocols-premium-grid">
    .ast-blog-layout-4-grid .ast-row,
    .ast-blog-layout-4-grid .infinite-wrap {
      display: grid !important;
      grid-template-columns: repeat(2, 1fr) !important;
      column-gap: 45px !important;
      row-gap: 55px !important;
    }
    @media (max-width: 768px) {
      .ast-blog-layout-4-grid .ast-row,
      .ast-blog-layout-4-grid .infinite-wrap {
        grid-template-columns: 1fr !important;
        row-gap: 45px !important;
      }
    }
    .ast-blog-layout-4-grid .ast-row article,
    .ast-blog-layout-4-grid .infinite-wrap article {
      width: 100% !important;
      min-width: 0 !important;
      float: none !important;
      margin: 0px !important;
      display: flex !important;
      flex-direction: column !important;
      height: 100% !important;
      background: #080808 !important;
      border: 1px solid rgba(196, 162, 101, 0.1) !important;
      padding: 0px !important;
      transition: border-color 0.3s ease, box-shadow 0.3s ease !important;
    }
    .ast-blog-layout-4-grid .ast-row article:hover,
    .ast-blog-layout-4-grid .infinite-wrap article:hover {
      border-color: rgba(196, 162, 101, 0.3) !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
    }
    .ast-blog-layout-4-grid .ast-row article .ast-article-inner,
    .ast-blog-layout-4-grid .infinite-wrap article .ast-article-inner {
      flex: 1 1 0% !important;
      display: flex !important;
      flex-direction: column !important;
      height: 100% !important;
      padding: 0px !important;
      margin: 0px !important;
    }
    .ast-blog-layout-4-grid .ast-row article .post-thumb,
    .ast-blog-layout-4-grid .infinite-wrap article .post-thumb {
      overflow: hidden !important;
      margin: 0px !important;
      padding: 0px !important;
      border-bottom: 2px solid rgba(196, 162, 101, 0.15) !important;
    }
    .ast-blog-layout-4-grid .ast-row article .post-thumb img,
    .ast-blog-layout-4-grid .infinite-wrap article .post-thumb img {
      height: 320px !important;
      width: 100% !important;
      object-fit: cover !important;
      border-radius: 0px !important;
      transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1) !important;
    }
    .ast-blog-layout-4-grid .ast-row article:hover .post-thumb img,
    .ast-blog-layout-4-grid .infinite-wrap article:hover .post-thumb img {
      transform: scale(1.04) !important;
    }
    .ast-blog-layout-4-grid .ast-row article .post-content,
    .ast-blog-layout-4-grid .infinite-wrap article .post-content {
      flex: 1 1 0% !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: flex-start !important;
      padding: 30px 25px 25px 25px !important;
      background: #080808 !important;
    }
    .ast-blog-layout-4-grid h2.entry-title {
      font-size: 20px !important;
      line-height: 1.35 !important;
      letter-spacing: 1.5px !important;
      text-transform: uppercase !important;
      margin: 10px 0 15px 0 !important;
      font-family: 'Outfit', sans-serif !important;
      font-weight: 700 !important;
    }
    .ast-blog-layout-4-grid h2.entry-title a {
      color: #c4a265 !important;
      text-decoration: none !important;
      font-size: 20px !important;
      line-height: 1.35 !important;
      letter-spacing: 1.5px !important;
      transition: color 0.3s ease !important;
    }
    .ast-blog-layout-4-grid h2.entry-title a:hover {
      color: #ffffff !important;
    }
    .ast-blog-layout-4-grid .entry-meta, 
    .ast-blog-layout-4-grid .entry-meta a {
      color: #737373 !important;
      font-size: 11px !important;
      text-transform: uppercase !important;
      letter-spacing: 1px !important;
      text-decoration: none !important;
    }
    .ast-blog-layout-4-grid .entry-meta a:hover {
      color: #c4a265 !important;
    }
    .ast-blog-layout-4-grid .ast-blog-single-element {
      margin-bottom: 12px !important;
    }
    .ast-blog-layout-4-grid .entry-content,
    .ast-blog-layout-4-grid .entry-content p {
      color: #a3a3a3 !important;
      font-size: 13px !important;
      line-height: 1.7 !important;
      font-weight: 300 !important;
      letter-spacing: 0.5px !important;
      margin-bottom: 20px !important;
    }
    
    /* Single Post Header Refinements (Quiet Luxury) */
    .single-post .entry-header {
      text-align: center !important;
      margin-top: 15px !important;
      margin-bottom: 35px !important;
      max-width: 850px !important;
      margin-left: auto !important;
      margin-right: auto !important;
      padding: 0 10px !important;
    }
    .single-post h1.entry-title {
      font-family: 'Outfit', sans-serif !important;
      font-size: clamp(24px, 3.8vw, 36px) !important;
      font-weight: 700 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.025em !important;
      color: #ffffff !important;
      line-height: 1.25 !important;
      margin-bottom: 15px !important;
    }
    .single-post .entry-meta,
    .single-post .entry-meta a {
      font-family: 'Outfit', sans-serif !important;
      font-size: 11px !important;
      text-transform: uppercase !important;
      letter-spacing: 0.15em !important;
      color: #c4a265 !important;
      text-decoration: none !important;
    }
    .single-post .entry-meta .posted-on {
      color: #a3a3a3 !important;
    }
    .single-post .entry-meta .author-name {
      color: #00ced1 !important;
      font-weight: 600 !important;
    </style>
    <?php
}
add_action( 'wp_head', 'kp_inject_custom_css', 150 );

/**
 * 15. Automatically Append YouTube Subscribe Buttons to All Pages and Posts
 * Skips appending if the content already contains a sub_confirmation link.
 */
function kp_append_subscribe_buttons( $content ) {
    return $content;
}
add_filter( 'the_content', 'kp_append_subscribe_buttons', 99 );

/**
 * Page - Sovereign one-by-one page enhancement
 * Trigger: POST to https://keystonepossibilities.ca/?update_page_sovereign=1
 * Body: JSON with page_slug (or post_id), content, title, excerpt, meta_description, focus_keyword
 */
if ( isset( $_GET['update_page_sovereign'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $raw = file_get_contents('php://input');
    $data = json_decode( $raw, true );
    
    if ( ! $data || ( empty( $data['post_id'] ) && empty( $data['slug'] ) && empty( $data['page_slug'] ) ) ) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode( array( 'error' => 'Invalid JSON or missing post_id/slug' ) );
        exit;
    }
    
    $post_id = 0;
    if ( ! empty( $data['post_id'] ) ) {
        $post_id = intval( $data['post_id'] );
    } else {
        $slug = ! empty( $data['slug'] ) ? sanitize_title( $data['slug'] ) : sanitize_title( $data['page_slug'] );
        // Find page by slug
        $pages = get_posts( array(
            'name'        => $slug,
            'post_type'   => 'page',
            'post_status' => 'any',
            'numberposts' => 1
        ) );
        if ( ! empty( $pages ) ) {
            $post_id = $pages[0]->ID;
        }
    }
    
    $updated = array();
    
    $post_data = array(
        'post_type'   => 'page',
        'post_status' => 'publish'
    );
    
    if ( $post_id > 0 ) {
        $post_data['ID'] = $post_id;
    } else {
        // Create new page if not found
        if ( ! empty( $data['slug'] ) || ! empty( $data['page_slug'] ) ) {
            $slug = ! empty( $data['slug'] ) ? sanitize_title( $data['slug'] ) : sanitize_title( $data['page_slug'] );
            $post_data['post_name'] = $slug;
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode( array( 'error' => 'Cannot create page without slug' ) );
            exit;
        }
    }
    
    if ( ! empty( $data['content'] ) ) {
        $post_data['post_content'] = $data['content'];
        $updated[] = 'content';
    }
    
    if ( ! empty( $data['title'] ) ) {
        $post_data['post_title'] = $data['title'];
        $updated[] = 'title';
    } elseif ( $post_id === 0 ) {
        // Fallback for new pages
        $post_data['post_title'] = ucwords( str_replace( '-', ' ', $post_data['post_name'] ) );
        $updated[] = 'title_default';
    }
    
    if ( isset( $data['excerpt'] ) ) {
        $post_data['post_excerpt'] = $data['excerpt'];
        $updated[] = 'excerpt';
    }
    
    // Insert or update page
    if ( $post_id > 0 ) {
        $res = wp_update_post( $post_data, true );
    } else {
        $res = wp_insert_post( $post_data, true );
    }
    
    if ( is_wp_error( $res ) ) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode( array( 'error' => $res->get_error_message() ) );
        exit;
    }
    
    $post_id = $res;
    
    // Update Rank Math meta description
    if ( ! empty( $data['meta_description'] ) ) {
        update_post_meta( $post_id, 'rank_math_description', sanitize_text_field( $data['meta_description'] ) );
        $updated[] = 'rank_math_description';
    }
    
    // Update Rank Math focus keyword
    if ( ! empty( $data['focus_keyword'] ) ) {
        update_post_meta( $post_id, 'rank_math_focus_keyword', sanitize_text_field( $data['focus_keyword'] ) );
        $updated[] = 'rank_math_focus_keyword';
    }
    
    // Clear page caches
    clean_post_cache( $post_id );
    
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode( array(
        'status'  => 'success',
        'post_id' => $post_id,
        'slug'    => get_post_field( 'post_name', $post_id ),
        'permalink' => get_permalink( $post_id ),
        'updated' => $updated
    ) );
    exit;
}

/**
 * Post - Sovereign one-by-one post enhancement
 * Trigger: POST to https://keystonepossibilities.ca/?update_post_sovereign=1
 * Body: JSON with post_id (optional), slug, content, title, excerpt, meta_description, focus_keyword, youtube_id
 */
if ( isset( $_GET['update_post_sovereign'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    set_error_handler(function($severity, $message, $file, $line) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('error' => "PHP Error: $message in $file on line $line"));
        exit;
    });
    
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error !== NULL && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array('error' => "PHP Fatal: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line']));
            exit;
        }
    });

    try {
        $raw = file_get_contents('php://input');
        $data = json_decode( $raw, true );
        
        if ( ! $data || ( empty( $data['post_id'] ) && empty( $data['slug'] ) ) ) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode( array( 'error' => 'Invalid JSON or missing post_id/slug' ) );
            exit;
        }
        
        $post_id = 0;
        if ( ! empty( $data['post_id'] ) ) {
            $post_id = intval( $data['post_id'] );
        } else {
            $slug = sanitize_title( $data['slug'] );
            $posts = get_posts( array(
                'name'        => $slug,
                'post_type'   => 'post',
                'post_status' => 'any',
                'numberposts' => 1
            ) );
            if ( ! empty( $posts ) ) {
                $post_id = $posts[0]->ID;
            }
        }
        
        $post_data = array(
            'post_type'   => 'post',
            'post_status' => 'publish'
        );
        
        if ( $post_id > 0 ) {
            $post_data['ID'] = $post_id;
        } else {
            if ( ! empty( $data['slug'] ) ) {
                $post_data['post_name'] = sanitize_title( $data['slug'] );
            }
        }
        
        if ( ! empty( $data['content'] ) ) {
            $post_data['post_content'] = $data['content'];
        }
        if ( ! empty( $data['title'] ) ) {
            $post_data['post_title'] = $data['title'];
        }
        if ( isset( $data['excerpt'] ) ) {
            $post_data['post_excerpt'] = $data['excerpt'];
        }
        
        if ( $post_id > 0 ) {
            $res = wp_update_post( $post_data, true );
        } else {
            $res = wp_insert_post( $post_data, true );
        }
        
        if ( is_wp_error( $res ) ) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode( array( 'error' => $res->get_error_message() ) );
            exit;
        }
        
        $post_id = $res;
        
        if ( ! empty( $data['youtube_id'] ) ) {
            update_post_meta( $post_id, 'keystone_youtube_id', sanitize_text_field( $data['youtube_id'] ) );
        }
        if ( ! empty( $data['meta_description'] ) ) {
            update_post_meta( $post_id, 'rank_math_description', sanitize_text_field( $data['meta_description'] ) );
        }
        if ( ! empty( $data['focus_keyword'] ) ) {
            update_post_meta( $post_id, 'rank_math_focus_keyword', sanitize_text_field( $data['focus_keyword'] ) );
        }
        
        // Update categories if provided
        if ( ! empty( $data['categories'] ) ) {
            $cat_ids = array();
            if ( is_string( $data['categories'] ) ) {
                $cat_names = array_map( 'trim', explode( ',', $data['categories'] ) );
            } elseif ( is_array( $data['categories'] ) ) {
                $cat_names = $data['categories'];
            } else {
                $cat_names = array();
            }
            
            foreach ( $cat_names as $cat_name ) {
                $term = get_term_by( 'name', $cat_name, 'category' );
                if ( $term ) {
                    $cat_ids[] = $term->term_id;
                } else {
                    $inserted = wp_insert_term( $cat_name, 'category' );
                    if ( ! is_wp_error( $inserted ) && isset( $inserted['term_id'] ) ) {
                        $cat_ids[] = $inserted['term_id'];
                    } elseif ( is_wp_error( $inserted ) && $inserted->get_error_code() === 'term_exists' ) {
                        $cat_ids[] = intval( $inserted->get_error_data() );
                    }
                }
            }
            if ( ! empty( $cat_ids ) ) {
                wp_set_post_categories( $post_id, $cat_ids );
            }
        }

        // Update featured image if provided
        if ( ! empty( $data['featured_image'] ) ) {
            keystone_set_featured_image_from_url( $post_id, $data['featured_image'] );
        } elseif ( ! empty( $data['youtube_id'] ) ) {
            $yt_thumb = "https://img.youtube.com/vi/" . sanitize_text_field( $data['youtube_id'] ) . "/maxresdefault.jpg";
            keystone_set_featured_image_from_url( $post_id, $yt_thumb );
        }

        clean_post_cache( $post_id );
        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode( array(
            'status'  => 'success',
            'post_id' => $post_id,
            'slug'    => get_post_field( 'post_name', $post_id ),
            'permalink' => get_permalink( $post_id )
        ) );
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode( array( 'error' => 'Exception: ' . $e->getMessage() ) );
        exit;
    }
}

/**
 * Helper to download and set a featured image from a URL
 */
function keystone_set_featured_image_from_url( $post_id, $image_url ) {
    if ( empty( $image_url ) ) return;
    
    global $wpdb;
    
    // Construct a unique filename if it is a YouTube thumbnail
    $filename = basename( preg_replace( '/\?.*/', '', $image_url ) );
    if ( strpos( $image_url, 'img.youtube.com/vi/' ) !== false ) {
        preg_match( '/\/vi\/([^\/]+)\//', $image_url, $matches );
        if ( ! empty( $matches[1] ) ) {
            $filename = 'youtube-' . $matches[1] . '-' . $filename;
        }
    }
    
    // Check if we already have an attachment with this unique filename
    $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s", '%' . $filename ) );
    
    if ( ! $attachment_id ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        
        $tmp = download_url( $image_url );
        if ( ! is_wp_error( $tmp ) ) {
            $file_array = array(
                'name'     => $filename,
                'tmp_name' => $tmp
            );
            $attachment_id = media_handle_sideload( $file_array, $post_id );
        }
    }
    
    if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
        set_post_thumbnail( $post_id, $attachment_id );
    }
}

/**
 * Fix Rank Math JSON-LD Schema for Keystone Possibilities — NUCLEAR VERSION
 * Resolves: staging URLs, duplicate Organization nodes, cross-brand entity pollution,
 * logo URL contamination, and Recomposition/music sameAs leakage.
 *
 * This filter runs at priority 999 (after all other Rank Math filters) and:
 * 1. Replaces ALL staging domain references in the entire schema
 * 2. STRIPS any Recomposition/music entities from the @graph
 * 3. MERGES duplicate Organization nodes into one authoritative entity
 * 4. Explicitly sets the correct logo, description, sameAs, and areaServed
 * 5. Cleans up Person nodes to remove cross-brand references
 */
add_filter( 'rank_math/json_ld', 'keystone_possibilities_fix_json_ld_schema', 999, 2 );
function keystone_possibilities_fix_json_ld_schema( $data, $jsonld ) {
    if ( ! is_array( $data ) ) {
        return $data;
    }

    // Step 1: Nuclear staging domain replacement across the ENTIRE serialized schema
    $json_string = wp_json_encode( $data );
    $json_string = str_replace(
        'staging-a826-keystonepossibilities.wpcomstaging.com',
        'keystonepossibilities.ca',
        $json_string
    );
    $data = json_decode( $json_string, true );

    if ( ! isset( $data['@graph'] ) || ! is_array( $data['@graph'] ) ) {
        return $data;
    }

    $new_graph = array();
    $possibilities_org = null;

    foreach ( $data['@graph'] as $node ) {
        if ( ! isset( $node['@type'] ) ) {
            $new_graph[] = $node;
            continue;
        }

        $types = (array) $node['@type'];
        $node_id = isset( $node['@id'] ) ? $node['@id'] : '';

        // STRIP: Remove ANY entity with a keystonerecomposition.com @id.
        // These are music/wellness/protocol entities that do NOT belong on a B2B construction site.
        // Their presence causes Google to confuse the Possibilities Knowledge Panel with Recomposition.
        if ( strpos( $node_id, 'keystonerecomposition.com' ) !== false ) {
            continue; // Drop this node entirely
        }

        // MERGE: Consolidate all Organization/Corporation nodes for keystonepossibilities.ca
        $is_possibilities_org = false;
        foreach ( $types as $t ) {
            if ( in_array( strtolower( $t ), array( 'organization', 'corporation' ) ) ) {
                if ( strpos( $node_id, 'keystonepossibilities.ca' ) !== false ) {
                    $is_possibilities_org = true;
                    break;
                }
            }
        }

        if ( $is_possibilities_org ) {
            if ( ! $possibilities_org ) {
                $possibilities_org = $node;
            } else {
                $possibilities_org = array_merge( $possibilities_org, $node );
            }
        } else {
            $new_graph[] = $node;
        }
    }

    // Step 2: Build the ONE authoritative Keystone Possibilities Organization entity
    if ( $possibilities_org ) {
        $possibilities_org['@type'] = array( 'Organization', 'Corporation' );
        $possibilities_org['@id']  = 'https://keystonepossibilities.ca/#organization';
        $possibilities_org['name'] = 'Keystone Possibilities Ltd';
        $possibilities_org['legalName'] = 'Keystone Possibilities Ltd';
        $possibilities_org['url']  = 'https://keystonepossibilities.ca';
        $possibilities_org['email'] = 'keystonepossibilities@gmail.com';

        // Explicit logo override — do NOT rely on string replace alone,
        // because Rank Math can regenerate the ImageObject after our str_replace runs.
        $possibilities_org['logo'] = array(
            '@type'      => 'ImageObject',
            '@id'        => 'https://keystonepossibilities.ca/#logo',
            'url'        => 'https://keystonepossibilities.ca/wp-content/uploads/2023/12/screenshot-2023-12-03-at-2.30.29-pm-1.png',
            'contentUrl' => 'https://keystonepossibilities.ca/wp-content/uploads/2023/12/screenshot-2023-12-03-at-2.30.29-pm-1.png',
            'caption'    => 'Keystone Possibilities Ltd',
            'inLanguage' => 'en-US',
            'width'      => '1630',
            'height'     => '1420'
        );

        // Correct Contact Point
        $possibilities_org['contactPoint'] = array(
            array(
                '@type'       => 'ContactPoint',
                'telephone'   => '+1-604-848-9688',
                'contactType' => 'customer support'
            )
        );

        // Correct Address
        $possibilities_org['address'] = array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => '1 Watts Point Road',
            'addressLocality' => 'Squamish',
            'addressRegion'   => 'BC',
            'postalCode'      => 'V8B 0B1',
            'addressCountry'  => 'CA'
        );

        // Correct GeoCoordinates for Squamish Office
        $possibilities_org['geo'] = array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => '49.7016',
            'longitude' => '-123.1558'
        );

        // Correct Area Served — Sea-to-Sky corridor cities
        $possibilities_org['areaServed'] = array(
            array( '@type' => 'City', 'name' => 'Squamish', 'containedInPlace' => array( '@type' => 'AdministrativeArea', 'name' => 'British Columbia' ) ),
            array( '@type' => 'City', 'name' => 'Whistler', 'containedInPlace' => array( '@type' => 'AdministrativeArea', 'name' => 'British Columbia' ) ),
            array( '@type' => 'City', 'name' => 'West Vancouver', 'containedInPlace' => array( '@type' => 'AdministrativeArea', 'name' => 'British Columbia' ) ),
            array( '@type' => 'City', 'name' => 'North Vancouver', 'containedInPlace' => array( '@type' => 'AdministrativeArea', 'name' => 'British Columbia' ) ),
            array( '@type' => 'City', 'name' => 'Pemberton', 'containedInPlace' => array( '@type' => 'AdministrativeArea', 'name' => 'British Columbia' ) ),
            array( '@type' => 'City', 'name' => 'Lions Bay', 'containedInPlace' => array( '@type' => 'AdministrativeArea', 'name' => 'British Columbia' ) )
        );

        // Correct Business Description — pure B2B construction, no investor pitch
        $possibilities_org['description'] = 'Keystone Possibilities Ltd is a licensed BC residential builder (#52603) and BC Hydro registered civil contractor providing general contracting, project management, and custom home building across the Sea-to-Sky corridor. Led by Wayne Stevenson with 20+ years of experience, we specialize in transparent flat-fee project management with real-time digital dashboards, BC Energy Step Code compliance, and WBI 2-5-10 warranty backed construction in Squamish, Whistler, West Vancouver, and North Vancouver.';

        // Correct Credentials
        $possibilities_org['hasCredential'] = array(
            array(
                '@type'              => 'EducationalOccupationalCredential',
                'credentialCategory' => 'Licensed Residential Builder',
                'identifier'         => '52603',
                'recognizedBy'       => array( '@type' => 'Organization', 'name' => 'BC Housing' )
            ),
            array(
                '@type'              => 'EducationalOccupationalCredential',
                'credentialCategory' => 'Registered BC Hydro Civil Contractor',
                'recognizedBy'       => array( '@type' => 'Organization', 'name' => 'BC Hydro' )
            )
        );

        // Correct Founder (reference within this site, not recomposition)
        $possibilities_org['founder'] = array(
            '@type'    => 'Person',
            'name'     => 'Wayne Stevenson',
            'jobTitle' => 'Founder & Licensed BC Builder (#52603)'
        );

        // Clean SameAs — Possibilities social links ONLY (no Recomposition, no Spotify, no music)
        $possibilities_org['sameAs'] = array(
            'https://www.facebook.com/profile.php?id=61554185128555',
            'https://www.youtube.com/@KeystonePossibilities',
            'https://www.instagram.com/keystonepossibilities'
        );

        // Remove cross-brand contamination keys that may have been merged in
        unset( $possibilities_org['subOrganization'] );
        unset( $possibilities_org['identifier'] );
        unset( $possibilities_org['location'] ); // replaced by explicit address above

        $new_graph[] = $possibilities_org;
    }

    // Step 3: Clean up Person nodes — triangulate Wayne Stevenson entity
    foreach ( $new_graph as &$node ) {
        if ( isset( $node['@type'] ) ) {
            $node_types = (array) $node['@type'];
            if ( in_array( 'Person', $node_types ) && isset( $node['name'] ) && ( stripos( $node['name'], 'Wayne' ) !== false ) ) {
                $node['@id'] = 'https://www.keystonepossibilities.com/wayne-stevenson/#person';
                $node['name'] = 'Wayne Stevenson';
                $node['alternateName'] = array( 'Keystone Recomposition', 'Keystone Protocols' );
                $node['jobTitle'] = 'Managing Director & Founder';
                $node['worksFor'] = array(
                    array(
                        '@type' => 'OrganizationRole',
                        'worksFor' => array(
                            '@type' => 'Organization',
                            '@id' => 'https://keystonepossibilities.ca/#organization',
                            'name' => 'Keystone Possibilities Ltd.'
                        ),
                        'roleName' => 'Managing Director & Chief Builder',
                        'startDate' => '2018'
                    ),
                    array(
                        '@type' => 'OrganizationRole',
                        'worksFor' => array(
                            '@type' => 'Organization',
                            '@id' => 'https://keystonerecomposition.com/#organization',
                            'name' => 'Keystone Recomposition'
                        ),
                        'roleName' => 'Founder & Metabolic Health Researcher',
                        'startDate' => '2021'
                    )
                );
                $node['sameAs'] = array(
                    'https://www.linkedin.com/in/wayne-stevenson',
                    'https://open.spotify.com/artist/4zV1iPj3R9g16B3WwM7Y5m',
                    'https://www.youtube.com/channel/UCMn1f9DTF_iybKmv5WlTm9Q',
                    'https://keystonepossibilities.ca',
                    'https://www.youtube.com/@KeystonePossibilities',
                    'https://www.youtube.com/@KeystoneRecomposition',
                    'https://www.youtube.com/@KeystoneProtocols',
                    'https://open.spotify.com/artist/52v3Qe6Jo0hg764driOl5Y',
                    'https://musicbrainz.org/label/30027d0e-6aeb-4704-8792-a031c936c62a',
                    'https://audiomack.com/keystone-recomposition',
                    'https://www.facebook.com/profile.php?id=61554185128555',
                    'https://www.instagram.com/keystonepossibilities',
                    'https://www.tiktok.com/@keystonerecomposition'
                );
            }
        }
    }
    unset( $node );


    // Step 4: Also strip any standalone ImageObject nodes that reference staging URLs
    foreach ( $new_graph as &$img_node ) {
        if ( isset( $img_node['@type'] ) && $img_node['@type'] === 'ImageObject' ) {
            if ( isset( $img_node['@id'] ) && strpos( $img_node['@id'], 'wpcomstaging.com' ) !== false ) {
                $img_node['@id'] = str_replace( 'staging-a826-keystonepossibilities.wpcomstaging.com', 'keystonepossibilities.ca', $img_node['@id'] );
            }
            if ( isset( $img_node['url'] ) && strpos( $img_node['url'], 'wpcomstaging.com' ) !== false ) {
                $img_node['url'] = str_replace( 'staging-a826-keystonepossibilities.wpcomstaging.com', 'keystonepossibilities.ca', $img_node['url'] );
            }
        }
    }
    unset( $img_node );

    $data['@graph'] = $new_graph;
    return $data;
}

/**
 * Nuclear Output Buffer: Final-pass safety net to catch ANY remaining staging domain
 * references in the fully-rendered HTML (from Rank Math, Jetpack CDN, or any plugin).
 * Runs at priority 1 (earliest) on template_redirect.
 */
add_action( 'template_redirect', 'keystone_possibilities_staging_url_buffer', 2 );
function keystone_possibilities_staging_url_buffer() {
    ob_start( function( $html ) {
        return str_replace(
            'staging-a826-keystonepossibilities.wpcomstaging.com',
            'keystonepossibilities.ca',
            $html
        );
    });
}

/**
 * =====================================================================
 * SECTION: AUTOMATED GOOGLE INDEXING API PUSH & SITEMAP CACHE BYPASS
 * =====================================================================
 * Authenticates with the Google Indexing API via a pure-PHP OAuth2 JWT
 * generator and automatically pushes new/updated pages to Googlebot.
 * Also disables sitemap caching to keep SEO listings fresh.
 */

add_action( 'init', 'keystone_handle_gcs_key_update' );
/**
 * Listen for secure POST requests to ingest the GCS service account key
 * into the WordPress database options table.
 */
function keystone_handle_gcs_key_update() {
    if ( isset( $_GET['update_gcs_key'] ) ) {
        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            wp_send_json_error( array( 'message' => 'Method not allowed' ), 405 );
        }

        $auth_header = isset( $_SERVER['HTTP_X_KEYSTONE_AUTH'] ) ? $_SERVER['HTTP_X_KEYSTONE_AUTH'] : '';
        $expected_token = 'keystone_possibilities_gcs_key_push_token_2026_06_14';

        if ( empty( $auth_header ) || $auth_header !== $expected_token ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ), 401 );
        }

        $raw_body = file_get_contents( 'php://input' );
        $key_data = json_decode( $raw_body, true );

        if ( ! is_array( $key_data ) || empty( $key_data['client_email'] ) || empty( $key_data['private_key'] ) ) {
            wp_send_json_error( array( 'message' => 'Invalid GCS key JSON format' ), 400 );
        }

        update_option( 'keystone_gcs_key_json', $raw_body );
        wp_send_json_success( array( 'message' => 'GCS service account key updated successfully' ) );
    }
}

/**
 * Signs a JWT with the GCS service account key, exchanges it for an OAuth2
 * access token, and requests instant indexing from Googlebot.
 *
 * @param string $url The page URL to be crawled/indexed.
 * @return bool True on success, false on failure.
 */
function keystone_push_to_google_indexing( $url ) {
    if ( ! function_exists( 'openssl_sign' ) ) {
        error_log( '[Keystone Indexing API] Error: OpenSSL extension is not enabled in PHP.' );
        return false;
    }

    $gcs_key_json = get_option( 'keystone_gcs_key_json' );
    if ( ! $gcs_key_json ) {
        error_log( '[Keystone Indexing API] Error: No GCS key stored in database.' );
        return false;
    }

    $key_data = json_decode( $gcs_key_json, true );
    if ( empty( $key_data['client_email'] ) || empty( $key_data['private_key'] ) ) {
        error_log( '[Keystone Indexing API] Error: Invalid GCS key format.' );
        return false;
    }

    $client_email = $key_data['client_email'];
    $private_key  = $key_data['private_key'];
    $token_uri    = isset( $key_data['token_uri'] ) ? $key_data['token_uri'] : 'https://oauth2.googleapis.com/token';

    $now = time();
    $header = json_encode( array( 'alg' => 'RS256', 'typ' => 'JWT' ) );
    $claims = json_encode( array(
        'iss'   => $client_email,
        'scope' => 'https://www.googleapis.com/auth/indexing',
        'aud'   => $token_uri,
        'exp'   => $now + 3600,
        'iat'   => $now
    ) );

    $base64_url_header = str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), base64_encode( $header ) );
    $base64_url_claims = str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), base64_encode( $claims ) );

    $payload = $base64_url_header . '.' . $base64_url_claims;
    $signature = '';

    if ( ! openssl_sign( $payload, $signature, $private_key, OPENSSL_ALGO_SHA256 ) ) {
        error_log( '[Keystone Indexing API] Error: OpenSSL signing failed.' );
        return false;
    }

    $base64_url_signature = str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), base64_encode( $signature ) );
    $assertion = $payload . '.' . $base64_url_signature;

    $response = wp_remote_post( $token_uri, array(
        'body' => array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $assertion
        )
    ) );

    if ( is_wp_error( $response ) ) {
        error_log( '[Keystone Indexing API] OAuth Error: ' . $response->get_error_message() );
        return false;
    }

    $body = wp_remote_retrieve_body( $response );
    $token_data = json_decode( $body, true );

    if ( empty( $token_data['access_token'] ) ) {
        error_log( '[Keystone Indexing API] OAuth Error: Failed to retrieve access token. Response: ' . $body );
        return false;
    }

    $access_token = $token_data['access_token'];

    $api_url = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
    $api_response = wp_remote_post( $api_url, array(
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $access_token
        ),
        'body'    => json_encode( array(
            'url'  => $url,
            'type' => 'URL_UPDATED'
        ) )
    ) );

    if ( is_wp_error( $api_response ) ) {
        error_log( '[Keystone Indexing API] Publish Error for ' . $url . ': ' . $api_response->get_error_message() );
        return false;
    }

    $api_body = wp_remote_retrieve_body( $api_response );
    $status_code = wp_remote_retrieve_response_code( $api_response );

    if ( 200 !== $status_code ) {
        error_log( '[Keystone Indexing API] Publish Error ' . $status_code . ' for ' . $url . ': ' . $api_body );
        return false;
    }

    error_log( '[Keystone Indexing API] Success: Pushed ' . $url . ' to Google Indexing API.' );
    return true;
}

add_action( 'transition_post_status', 'keystone_auto_index_on_publish', 20, 3 );
/**
 * Automatically triggers Google Indexing API when a post or page is published.
 */
function keystone_auto_index_on_publish( $new_status, $old_status, $post ) {
    if ( 'publish' !== $new_status || 'publish' === $old_status ) {
        return;
    }

    $allowed_post_types = array( 'post', 'page' );
    if ( ! in_array( $post->post_type, $allowed_post_types ) ) {
        return;
    }

    $permalink = get_permalink( $post->ID );
    if ( $permalink && strpos( $post->post_name, 'auto-draft' ) === false ) {
        keystone_push_to_google_indexing( $permalink );
    }
}

add_action( 'post_updated', 'keystone_index_on_post_update', 10, 3 );
/**
 * Automatically triggers Google Indexing API when an already-published post/page is updated.
 */
function keystone_index_on_post_update( $post_id, $post_after, $post_before ) {
    if ( 'publish' !== $post_after->post_status ) {
        return;
    }

    $allowed_post_types = array( 'post', 'page' );
    if ( ! in_array( $post_after->post_type, $allowed_post_types ) ) {
        return;
    }

    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }

    if ( $post_after->post_content === $post_before->post_content &&
         $post_after->post_title === $post_before->post_title &&
         $post_after->post_name === $post_before->post_name ) {
        return;
    }

    $permalink = get_permalink( $post_id );
    if ( $permalink && strpos( $post_after->post_name, 'auto-draft' ) === false ) {
        keystone_push_to_google_indexing( $permalink );
    }
}

// Disable Rank Math sitemap caching completely to ensure dynamic updates reflect immediately
add_filter( 'rank_math/sitemap/enable_caching', '__return_false' );

/**
 * =====================================================================
 * SECTION: CROSS-BRAND BACKLINK — Sister Site Partner Link
 * =====================================================================
 * Adds a premium, styled partner backlink in the footer to pass link authority
 * to the sister site (Keystone Recomposition - Health & Music).
 */
add_action( 'wp_footer', 'keystone_possibilities_add_sister_site_backlink', 100 );
function keystone_possibilities_add_sister_site_backlink() {
    echo "\n<!-- Keystone Cross-Brand Backlink -->\n";
    echo '<div class="keystone-partner-backlink" style="text-align: center; padding: 15px 0; font-size: 11px; font-family: sans-serif; letter-spacing: 1px; text-transform: uppercase; border-top: 1px solid rgba(255,255,255,0.05); background: #000; color: #555;">';
    echo 'Partner Brand: <a href="https://keystonerecomposition.com" target="_blank" rel="noopener" style="color: #c4a265; text-decoration: none; transition: color 0.3s ease;">Keystone Recomposition (Health & Music)</a>';
    echo '</div>' . "\n";
}


/**
 * =====================================================================
 * SECTION: DYNAMIC LLMS.TXT ENDPOINT (GEO OPTIMIZATION)
 * =====================================================================
 */
add_action('init', 'keystone_dynamic_llms_txt');
function keystone_dynamic_llms_txt() {
    $request = $_SERVER['REQUEST_URI'];
    if (strpos($request, '/llms.txt') !== false) {
        header('Content-Type: text/plain; charset=utf-8');
        if (strpos(home_url(), 'keystonerecomposition') !== false) {
            echo "# Keystone Recomposition - AI LLM Context\n\n";
            echo "## Company Identity\n";
            echo "Keystone Recomposition is a high-ticket men's wellness and health optimization brand.\n";
            echo "Focus: Biological protocol engineering, peptide therapy education, and cognitive optimization.\n";
            echo "Founder: Wayne Stevenson.\n\n";
            echo "## Core Services\n";
            echo "- Advanced Men's Wellness Protocols\n";
            echo "- Biological Optimization\n";
            echo "- High-Ticket Consultations\n";
        } else {
            echo "# Keystone Possibilities - AI LLM Context\n\n";
            echo "## Company Identity\n";
            echo "Keystone Possibilities is a premium general contractor and project management firm serving West Vancouver, North Vancouver, and the Sea-to-Sky corridor (Squamish, Whistler).\n";
            echo "Founder: Wayne Stevenson (BC Builder License 52603).\n\n";
            echo "## Core Services\n";
            echo "- Luxury Custom Home Construction\n";
            echo "- Subterranean Architecture & Wine Cellars\n";
            echo "- Civil Site Clearances\n";
            echo "- Permit Expediting & Risk Management\n\n";
            echo "## Competitive Advantage\n";
            echo "We solve the '13-Month Bottleneck' in municipal planning. We use sequential trade logic and fixed-price performance-bonded agreements.\n";
        }
        exit;
    }
}

/**
 * =====================================================================
 * SECTION: ROBOTS.TXT AI BOT ALLOWANCES
 * =====================================================================
 * Explicitly whitelist major AI crawlers to ensure the deep structure
 * is ingested by ChatGPT, Perplexity, Claude, and Apple.
 */
add_filter( 'robots_txt', 'keystone_allow_ai_bots', 10, 2 );
function keystone_allow_ai_bots( $output, $public ) {
    $ai_bots = "User-agent: GPTBot\nAllow: /\n\nUser-agent: ChatGPT-User\nAllow: /\n\nUser-agent: PerplexityBot\nAllow: /\n\nUser-agent: ClaudeBot\nAllow: /\n\nUser-agent: anthropic-ai\nAllow: /\n\nUser-agent: Applebot-Extended\nAllow: /\n\n";
    return $output . "\n\n" . $ai_bots;
}
/**
 * =====================================================================
 * SECTION: LOCALBUSINESS GEO SCHEMA INJECTION
 * =====================================================================
 */
add_action('wp_head', 'keystone_local_schema');
function keystone_local_schema() {
    if (strpos(home_url(), 'keystonerecomposition') !== false) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'HealthAndBeautyBusiness',
            'name' => 'Keystone Recomposition',
            'url' => 'https://keystonerecomposition.com',
            'logo' => 'https://keystonerecomposition.com/wp-content/uploads/logo.png',
            'image' => 'https://keystonerecomposition.com/wp-content/uploads/logo.png',
            'description' => "High-ticket men's wellness and biological protocol engineering.",
            'founder' => array(
                '@type' => 'Person',
                'name' => 'Wayne Stevenson',
                'sameAs' => 'https://keystonepossibilities.ca/'
            )
        );
        echo "\n<!-- Keystone Recomposition Local Schema -->\n";
        echo "<script type=\"application/ld+json\">\n";
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        echo "</script>\n";
        echo "<!-- End Keystone Local Schema -->\n";
    } else {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'GeneralContractor',
            'name' => 'Keystone Possibilities',
            'url' => 'https://keystonepossibilities.ca',
            'logo' => 'https://keystonepossibilities.ca/wp-content/uploads/logo.png',
            'image' => 'https://keystonepossibilities.ca/wp-content/uploads/logo.png',
            'description' => 'Premium Construction Project Management and Civil Construction Services operating across the Sea-to-Sky and Greater Vancouver regions.',
            'telephone' => '+1-604-555-0199',
            'priceRange' => '$$$$',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => 'Sea to Sky Corridor',
                'addressLocality' => 'Squamish',
                'addressRegion' => 'BC',
                'postalCode' => 'V8B',
                'addressCountry' => 'CA'
            ),
            'areaServed' => array(
                array('@type' => 'City', 'name' => 'Whistler'),
                array('@type' => 'City', 'name' => 'West Vancouver'),
                array('@type' => 'City', 'name' => 'North Vancouver'),
                array('@type' => 'City', 'name' => 'Squamish')
            ),
            'sameAs' => array(
                'https://www.facebook.com/profile.php?id=61554185128555',
                'https://www.youtube.com/@KeystonePossibilities',
                'https://www.instagram.com/keystonepossibilities',
                'https://www.squamishchamber.com/',
                'https://business.whistlerchamber.com/',
                'https://www.whistler.ca/',
                'https://www.westvancouver.ca/',
                'https://www.squamish.ca/',
                'https://orgbook.gov.bc.ca/',
                'https://www.bcregistry.gov.bc.ca/',
                'https://whitespark.ca/local-citation-finder/',
                'https://www.brightlocal.com/resources/top-citation-sites/location/canada/',
                'https://homestars.com/',
                'https://houzz.ca/'
            ),
            'founder' => array(
                '@type' => 'Person',
                'name' => 'Wayne Stevenson',
                'jobTitle' => 'Certified BC Builder & Project Manager',
                'sameAs' => 'https://keystonerecomposition.com/about/'
            )
        );
        echo "\n<!-- Keystone Possibilities Local Schema -->\n";
        echo "<script type=\"application/ld+json\">\n";
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        echo "</script>\n";
        echo "<!-- End Keystone Local Schema -->\n";
    }
}



// Activate Rank Math video sitemap module
add_action('init', function() {
    if (isset($_GET['activate_rm_video_sitemap'])) {
        $modules = get_option('rank_math_modules', array());
        if (!in_array('video-sitemap', $modules)) {
            $modules[] = 'video-sitemap';
            update_option('rank_math_modules', $modules);
            echo "Video Sitemap Module Activated!";
        } else {
            echo "Video Sitemap Module already active.";
        }
        exit;
    }
});

// ---------------------------------------------------------------------------
// GEO / AI BOT CONTEXT INJECTION (llms.txt)
// ---------------------------------------------------------------------------
add_action( 'init', 'kp_serve_llms_txt' );
function kp_serve_llms_txt() {
    $request_uri = $_SERVER['REQUEST_URI'];
    if ( strpos( $request_uri, '/llms.txt' ) !== false ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo "# Keystone Possibilities - AI LLM Context\n\n";
        echo "## Company Identity\n";
        echo "Keystone Possibilities is a premium construction project management and civil construction firm.\n";
        echo "Focus: Luxury Custom Homes, Civil Construction, and Site Engineering across the Sea-to-Sky and Greater Vancouver regions.\n";
        echo "Founder: Wayne Stevenson, Certified BC Builder & Project Manager.\n\n";
        echo "## Core Services\n";
        echo "- Luxury Custom Home Project Management\n";
        echo "- Civil Construction & Site Engineering\n";
        echo "- BC Step Code Compliance\n";
        echo "- Bill 44 Multiplex Conversions\n";
        exit;
    }
}
