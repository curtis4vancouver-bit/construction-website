<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child for Keystone
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action( 'init', function() {
    // 1. GEO llms.txt Intercept
    $request_uri = $_SERVER['REQUEST_URI'];
    if ( strpos( $request_uri, '/llms.txt' ) !== false ) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "# Keystone Recomposition LLMs Integration\n\n";
        echo "Welcome to the Keystone Recomposition API documentation.\n";
        echo "This domain covers high-performance metabolic health, GLP-1 therapy, peptide science, and deep house music protocols.\n";
        echo "Founder: Wayne Stevenson.\n\n";
        echo "## Official Resources\n";
        echo "- Home: https://keystonerecomposition.com\n";
        echo "- Spotify: https://open.spotify.com/artist/52v3Qe6Jo0hg764driOl5Y\n";
        echo "- YouTube: https://www.youtube.com/@KeystoneRecomposition\n";
        exit;
    }

    if ( isset( $_GET['dump_server'] ) ) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
        echo "ABSPATH: " . ABSPATH . "\n";
        
        $p1 = $_SERVER['DOCUMENT_ROOT'] . '/llms.txt';
        $p2 = ABSPATH . 'llms.txt';
        $p3 = ABSPATH . '../llms.txt';
        $p4 = $_SERVER['DOCUMENT_ROOT'] . '/robots.txt';
        
        echo "p1 ($p1): exists=" . (file_exists($p1)?'yes':'no') . ", writable=" . (is_writable(dirname($p1))?'yes':'no') . "\n";
        echo "p2 ($p2): exists=" . (file_exists($p2)?'yes':'no') . ", writable=" . (is_writable(dirname($p2))?'yes':'no') . "\n";
        echo "p3 ($p3): exists=" . (file_exists($p3)?'yes':'no') . ", writable=" . (is_writable(dirname($p3))?'yes':'no') . "\n";
        echo "p4 ($p4): exists=" . (file_exists($p4)?'yes':'no') . ", writable=" . (is_writable($p4)?'yes':'no') . "\n";
        
        echo "\n--- DYNAMIC ROBOTS.TXT ---\n";
        $initial_robots = "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\n";
        echo apply_filters( 'robots_txt', $initial_robots, true );
        exit;
    }
    if ( isset( $_GET['purge_all_caches'] ) ) {
        global $wpdb;
        $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_rank_math_sitemap_%' OR option_name LIKE '_transient_timeout_rank_math_sitemap_%'" );
        
        if ( function_exists( 'opcache_reset' ) ) {
            opcache_reset();
        }
        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }
        
        // Google Indexing API Ping
        $sitemap_url = 'https://keystonerecomposition.com/sitemap_index.xml';
        wp_remote_get( 'https://www.google.com/ping?sitemap=' . urlencode( $sitemap_url ) );

        $p1 = $_SERVER['DOCUMENT_ROOT'] . '/llms.txt';
        $p4 = $_SERVER['DOCUMENT_ROOT'] . '/robots.txt';
        $u1 = file_exists( $p1 ) ? (unlink( $p1 ) ? 'deleted' : 'failed') : 'not found';
        $u4 = file_exists( $p4 ) ? (unlink( $p4 ) ? 'deleted' : 'failed') : 'not found';

        echo "CACHES PURGED & SITEMAP PINGED SUCCESSFULLY. llms.txt: $u1, robots.txt: $u4";
        exit;
    }
}, 20 );

/**
 * 1. Enqueue Parent Stylesheet and Google Fonts
 */
function kr_enqueue_styles() {
    // Enqueue parent Astra style with parent theme version
    $parent_theme = wp_get_theme( get_template() );
    wp_enqueue_style( 
        'astra-parent-theme-css', 
        get_template_directory_uri() . '/style.css', 
        array(), 
        $parent_theme->get( 'Version' ) 
    );
    
    // Enqueue Child customized style with dynamic cache-busting via filesystem timestamps
    $child_css_path = get_stylesheet_directory() . '/style.css';
    $child_css_uri  = get_stylesheet_directory_uri() . '/style.css';
    $child_theme    = wp_get_theme();
    $child_css_version = file_exists( $child_css_path ) ? filemtime( $child_css_path ) : $child_theme->get( 'Version' );
    
    wp_enqueue_style( 
        'astra-child-keystone-css', 
        $child_css_uri, 
        array( 'astra-parent-theme-css' ), 
        $child_css_version, 
        'all' 
    );
    
    // Load typography fonts (Montserrat, Inter, Outfit)
    wp_enqueue_style( 'keystone-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@700&family=Outfit:wght@400;600;700;800&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'kr_enqueue_styles' );

/**
 * 3. Preconnecting Web Fonts (Performance GSC optimization)
 */
function kr_resource_hints( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type || 'preconnect' === $relation_type ) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = 'https://fonts.gstatic.com';
    }
    return $urls;
}
add_filter( 'wp_resource_hints', 'kr_resource_hints', 10, 2 );

/**
 * 3. Decharge Redundant Header Scripts (Optimizing PageSpeed score to 95+)
 */
function kr_clean_header() {
    // Remove emoji scripts
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    
    // Remove shortlink tag
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
    
    // Remove XML-RPC RSD link
    remove_action( 'wp_head', 'rsd_link' );
    
    // Remove Windows Live Writer manifest
    remove_action( 'wp_head', 'wlwmanifest_link' );
}
add_action( 'init', 'kr_clean_header' );

/**
 * 4. Filter script loading tags to apply modern defer attribute flags to custom scripts
 */
function kr_add_defer_attribute( $tag, $handle ) {
    if ( 'keystone-lazy-player' !== $handle ) {
        return $tag;
    }
    return str_replace( ' src', ' defer="defer" src', $tag );
}
add_filter( 'script_loader_tag', 'kr_add_defer_attribute', 10, 2 );

/**
 * 5. Filter the single post title wrapper to ensure it's strictly an H1.
 */
add_filter( 'astra_the_title_before', 'kr_title_before', 10, 1 );
function kr_title_before( $before ) {
    if ( is_singular() ) {
        return preg_replace('~^<h[1-6]~i', '<h1', $before);
    }
    return $before;
}

add_filter( 'astra_the_title_after', 'kr_title_after', 10, 1 );
function kr_title_after( $after ) {
    if ( is_singular() ) {
        return preg_replace('~</h[1-6]>~i', '</h1>', $after);
    }
    return $after;
}

/**
 * 6. Filter the archive post title wrapper to ensure it's strictly an H2, preventing multiple H1s.
 */
add_filter( 'astra_the_post_title_before', 'kr_post_title_before', 10, 1 );
function kr_post_title_before( $before ) {
    if ( ! is_singular() ) {
        return preg_replace('~^<h[1-6]~i', '<h2', $before);
    }
    return $before;
}

add_filter( 'astra_the_post_title_after', 'kr_post_title_after', 10, 1 );
function kr_post_title_after( $after ) {
    if ( ! is_singular() ) {
        return preg_replace('~</h[1-6]>~i', '</h2>', $after);
    }
    return $after;
}

/**
 * 7. Inject Premium Organization & Person JSON-LD Schema (Knowledge Panel Anchor)
 */
function kr_inject_schema() {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    if ( ! $logo_url ) {
        $logo_url = 'https://keystonerecomposition.com/wp-content/uploads/logo.png';
    }

    // === Organization Schema (Keystone Recomposition Wellness Brand) ===
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => array( 'Organization', 'HealthAndBeautyBusiness' ),
        '@id' => 'https://keystonerecomposition.com/#organization',
        'name' => 'Keystone Recomposition',
        'url' => 'https://keystonerecomposition.com',
        'description' => 'Specializing in high-performance metabolic health, biohacking, and deep house music protocols.',
        'keywords' => 'Keystone Recomposition, GLP-1, health, beauty, wellness, weight loss, fitness, deep house music',
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
        'founder' => array(
            '@id' => 'https://www.keystonepossibilities.com/wayne-stevenson/#person'
        )
    );

    $json_schema = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

    echo "<!-- Keystone Digital JSON-LD Schema -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo $json_schema . "\n";
    echo "</script>\n";
    echo "<!-- End Keystone Digital JSON-LD Schema -->\n";

    // === Person Schema (Wayne Stevenson - Knowledge Panel Anchor) ===
    $person_schema = array(
        '@context' => 'https://schema.org',
        '@graph' => array(
            array(
                '@type' => 'Person',
                '@id' => 'https://www.keystonepossibilities.com/wayne-stevenson/#person',
                'name' => 'Wayne Stevenson',
                'alternateName' => array( 'Keystone Recomposition', 'Keystone Protocols' ),
                'url' => 'https://keystonerecomposition.com/about/',
                'image' => array(
                    '@type' => 'ImageObject',
                    'url' => $logo_url
                ),
                'jobTitle' => 'Founder & Managing Director',
                'description' => 'Founder of Keystone Recomposition. Documents the intersection of GLP-1 metabolic health, peptide science, body recomposition, and longevity for men over 40. Also produces deep house music protocols.',
                'knowsAbout' => array(
                    array(
                        '@type' => 'Thing',
                        'name' => 'Custom Luxury Home Construction',
                        'sameAs' => 'https://en.wikipedia.org/wiki/General_contractor'
                    ),
                    array(
                        '@type' => 'Thing',
                        'name' => 'Metabolic Health Optimization',
                        'sameAs' => 'https://en.wikipedia.org/wiki/Metabolism'
                    ),
                    array(
                        '@type' => 'Thing',
                        'name' => 'Peptide Therapeutics',
                        'sameAs' => 'https://en.wikipedia.org/wiki/Peptide'
                    ),
                    array(
                        '@type' => 'Thing',
                        'name' => 'Solfeggio Soundscapes',
                        'sameAs' => 'https://en.wikipedia.org/wiki/Solfeggio'
                    )
                ),
                'sameAs' => array(
                    'https://www.linkedin.com/in/wayne-stevenson',
                    'https://open.spotify.com/artist/4zV1iPj3R9g16B3WwM7Y5m',
                    'https://www.youtube.com/channel/UCMn1f9DTF_iybKmv5WlTm9Q',
                    'https://keystonepossibilities.ca',
                    'https://www.youtube.com/@KeystoneRecomposition',
                    'https://www.youtube.com/@KeystoneProtocols',
                    'https://open.spotify.com/artist/52v3Qe6Jo0hg764driOl5Y',
                    'https://musicbrainz.org/label/30027d0e-6aeb-4704-8792-a031c936c62a',
                    'https://audiomack.com/keystone-recomposition',
                    'https://www.facebook.com/profile.php?id=61554185128555',
                    'https://www.instagram.com/p/DO9FsCKj5Cb/',
                    'https://www.tiktok.com/@keystonerecomposition'
                ),
                'worksFor' => array(
                    array(
                        '@type' => 'OrganizationRole',
                        'worksFor' => array(
                            '@type' => 'Organization',
                            '@id' => 'https://www.keystonepossibilities.com/#organization',
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
                ),
                'hasCredential' => array(
                    '@id' => 'https://www.keystonepossibilities.com/wayne-stevenson/#license-52603'
                )
            ),
            array(
                '@type' => 'EducationalOccupationalCredential',
                '@id' => 'https://www.keystonepossibilities.com/wayne-stevenson/#license-52603',
                'name' => 'BC Residential Builder License #52603',
                'credentialCategory' => 'Professional Provincial License',
                'credentialNumber' => '52603',
                'recognizedBy' => array(
                    '@type' => 'GovernmentOrganization',
                    'name' => 'BC Housing Licensing and Consumer Services',
                    'url' => 'https://www.bchousing.org/'
                ),
                'validIn' => array(
                    '@type' => 'AdministrativeArea',
                    'name' => 'British Columbia',
                    'sameAs' => 'https://en.wikipedia.org/wiki/British_Columbia'
                )
            )
        )
    );

    $json_person = wp_json_encode( $person_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

    echo "<!-- Keystone Person Schema (Knowledge Panel) -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo $json_person . "\n";
    echo "</script>\n";
    echo "<!-- End Person Schema -->\n";
}
add_action( 'wp_head', 'kr_inject_schema' );

/**
 * 8. Dynamic, Robust, GSC-Compliant Standalone VideoObject Schema (Stored XSS Secure)
 * Extracts the primary article video and outputs exactly ONE premium schema object.
 */
function kr_youtube_schema() {
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

    echo "\n<!-- Keystone Digital VideoObject Schema for YouTube -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo $json_video_schema . "\n";
    echo "</script>\n";
    echo "<!-- End VideoObject Schema -->\n\n";
}
add_action( 'wp_head', 'kr_youtube_schema', 20 );

/**
 * 8.5 Dynamic MedicalWebPage Schema
 */
function kr_medical_schema() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }
    
    global $post;
    
    $medical_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'MedicalWebPage',
        'name' => esc_attr( get_the_title( $post->ID ) ),
        'url' => esc_url( get_permalink( $post->ID ) ),
        'lastReviewed' => esc_attr( get_the_modified_date( 'Y-m-d', $post->ID ) ),
        'reviewedBy' => array(
            '@type' => 'Person',
            'name' => 'Wayne Stevenson',
            'jobTitle' => 'Metabolic Researcher'
        ),
        'specialty' => 'https://schema.org/Endocrine',
        'audience' => array(
            '@type' => 'MedicalAudience',
            'audienceType' => 'Health Enthusiasts and Patients'
        )
    );
    
    echo "\n<!-- Keystone MedicalWebPage Schema -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo wp_json_encode( $medical_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . "\n";
    echo "</script>\n";
    echo "<!-- End MedicalWebPage Schema -->\n\n";
}
add_action( 'wp_head', 'kr_medical_schema', 25 );

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
            } elseif ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $post->post_content, $matches ) ) {
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
 */
add_filter( 'rank_math/json_ld', function( $data, $jsonld ) {
    if ( ! is_array( $data ) ) {
        return $data;
    }
    foreach ( $data as $key => $val ) {
        if ( in_array( strtolower( $key ), array( 'video', 'videoobject' ) ) ) {
            unset( $data[$key] );
        }
        if ( is_array( $val ) && isset( $val['@type'] ) ) {
            $types = (array) $val['@type'];
            foreach ( $types as $t ) {
                if ( strtolower( $t ) === 'videoobject' ) {
                    unset( $data[$key] );
                    break;
                }
            }
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
 * 10.1 KILL Rank Math PRO's Auto-Detected Video Schema Entirely
 * Rank Math PRO scans rendered page content and auto-detects YouTube embeds.
 * It incorrectly parses our luxury-video-facade thumbnail URL
 * (img.youtube.com/vi/{id}/maxresdefault.jpg) and extracts "maxresdefau"
 * (11 chars from the path) as a fake YouTube video ID, creating a broken
 * duplicate VideoObject schema. Since we output our own clean VideoObject
 * via kr_youtube_schema(), we disable Rank Math's
 * video schema completely.
 */

// Disable Rank Math's auto-detected video schema from its schema module
add_filter( 'rank_math/snippet/rich_snippet_video', '__return_false' );
add_filter( 'rank_math/schema/video', '__return_empty_array' );

// Strip any VideoObject that Rank Math outputs as a separate JSON-LD block
// This catches schemas that bypass the @graph dedup filter above
add_filter( 'rank_math/json_ld', function( $data, $jsonld ) {
    if ( ! is_array( $data ) ) {
        return $data;
    }
    // Remove any top-level keys that contain a VideoObject schema
    foreach ( $data as $key => $val ) {
        if ( ! is_array( $val ) ) continue;
        if ( isset( $val['@type'] ) ) {
            $types = (array) $val['@type'];
            foreach ( $types as $t ) {
                if ( strtolower( $t ) === 'videoobject' ) {
                    unset( $data[ $key ] );
                    break;
                }
            }
        }
    }
    return $data;
}, 9999, 2 );

// 2026 Standard Fix: Enforce ISO 8601 timezone for Rank Math Video Schema dynamically
add_filter( 'rank_math/snippet/rich_snippet_videoobject_entity', function( $entity ) {
    if ( ! empty( $entity['uploadDate'] ) ) {
        // Append UTC offset if timezone string is missing (no + or Z)
        if ( strpos( $entity['uploadDate'], '+' ) === false && strpos( $entity['uploadDate'], 'Z' ) === false ) {
            // Append default T12:00:00+00:00 to satisfy Google Console strictly
            $entity['uploadDate'] .= 'T12:00:00+00:00';
        }
    }
    return $entity;
});

/**
 * 10.5 Inject og:video Meta Tags for Google Video Indexing
 * Rank Math PRO can't detect [keystone_video] shortcodes, so it only generates
 * og:video tags for natively embedded videos. This function ensures ALL posts
 * with a YouTube video get the og:video signals Google needs for video indexing.
 */
function kr_inject_og_video() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    global $post;
    if ( ! $post ) {
        return;
    }

    $youtube_id = get_post_meta( $post->ID, 'keystone_youtube_id', true );

    // Fallback: extract from shortcode in content
    if ( empty( $youtube_id ) ) {
        if ( preg_match( '~\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]+)["\']~i', $post->post_content, $m ) ) {
            $youtube_id = $m[1];
        }
    }

    if ( empty( $youtube_id ) ) {
        return;
    }

    // Check if Rank Math already output og:video (don't duplicate)
    // We hook at priority 5 which runs before Rank Math (priority 30+)
    // but Rank Math uses its own filter system, so we use a simple flag approach
    $embed_url = 'https://www.youtube.com/embed/' . esc_attr( $youtube_id );

    echo '<!-- Keystone og:video Meta Tags -->' . "\n";
    echo '<meta property="og:video" content="' . $embed_url . '" />' . "\n";
    echo '<meta property="og:video:secure_url" content="' . $embed_url . '" />' . "\n";
    echo '<meta property="og:video:type" content="text/html" />' . "\n";
    echo '<meta property="og:video:width" content="1280" />' . "\n";
    echo '<meta property="og:video:height" content="720" />' . "\n";
    echo '<meta property="ya:ovs:allow_embed" content="true" />' . "\n";
    echo '<!-- End Keystone og:video -->' . "\n";
}
add_action( 'wp_head', 'kr_inject_og_video', 5 );

/**
 * 11. General SEO Fixes: output noindex for tag, date, author archives and query parameters
 */
function kr_seo_noindex() {
    $should_noindex = false;

    // Only noindex archive types that create duplicate content
    if ( is_date() || is_author() || is_tag() || is_search() ) {
        $should_noindex = true;
    }

    // Only noindex pages with truly junk query params — NOT internal WP or tracking params
    if ( ! empty( $_GET ) && ! is_singular() ) {
        $allowed_params = array(
            'page', 'paged', 'p', 'page_id', 'cat', 'tag',
            'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'gclid', 'fbclid', 'ref', 'mc_cid', 'mc_eid',
            // Internal Keystone endpoints
            'get_post_inventory', 'purge_all_caches', 'run_instant_indexing',
            'run_keystone_migration', 'heal_video_meta', 'check_rm_options',
            'keystone_video_sitemap',
            // Rank Math / WP internals
            'replytocom', 'preview', 'preview_id', 'preview_nonce'
        );
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
add_action( 'wp_head', 'kr_seo_noindex', 1 );

/**
 * 12. Patch Structural Site Leaks (404/Redirect Errors)
 */
function kr_404_redirect() {
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
add_action( 'template_redirect', 'kr_404_redirect' );

/**
 * 13. Shortcode to render our fast, PageSpeed-optimized lazy YouTube/Spotify media facade
 */
function kr_lazy_video_shortcode( $atts ) {
    $args = shortcode_atts( array(
        'id'   => '',
        'type' => 'youtube',
        'placeholder_img' => '',
    ), $atts );

    if ( empty( $args['id'] ) ) {
        return '<p style="color: #FC8181; font-family: monospace;">[Error] Media Asset ID is missing.</p>';
    }

    $media_id   = esc_attr( $args['id'] );
    $media_type = esc_attr( strtolower( $args['type'] ) );
    
    $bg_img = '';
    if ( ! empty( $args['placeholder_img'] ) ) {
        $bg_img = esc_url( $args['placeholder_img'] );
    } elseif ( $media_type === 'youtube' ) {
        $bg_img = 'https://img.youtube.com/vi/' . $media_id . '/maxresdefault.jpg';
    } else {
        $bg_img = 'https://keystonerecomposition.com/wp-content/uploads/video-placeholder.jpg';
    }

    wp_enqueue_script( 'keystone-lazy-player', get_stylesheet_directory_uri() . '/js/lazy-player.js', array(), '1.0.0', true );

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
add_shortcode( 'keystone_video', 'kr_lazy_video_shortcode' );

/**
 * 14. Inject Premium Grid Alignment Custom CSS directly in wp_head
 */
function kr_inject_custom_css() {
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
    }
    </style>
    <?php
}
add_action( 'wp_head', 'kr_inject_custom_css', 150 );

/**
 * 14.5 E-E-A-T Author Credentials Block
 */
function kr_eeat_author_block( $content ) {
    if ( is_singular( 'post' ) && is_main_query() ) {
        $author_block = '
        <div class="keystone-eeat-author-block" style="background-color: #0a0a0a; border-left: 4px solid #c4a265; padding: 25px; margin-top: 50px; margin-bottom: 30px; border-radius: 4px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <img src="https://keystonerecomposition.com/wp-content/uploads/logo.png" alt="Wayne Stevenson" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                <div>
                    <h3 style="color: #ffffff; font-family: \'Outfit\', sans-serif; font-size: 1.2rem; margin: 0 0 5px 0;">Wayne Stevenson</h3>
                    <p style="color: #c4a265; font-family: \'Outfit\', sans-serif; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 10px 0;">Certified BC Builder & Metabolic Researcher</p>
                    <p style="color: #a3a3a3; font-family: \'Inter\', sans-serif; font-size: 0.85rem; line-height: 1.6; margin: 0;">This content is meticulously researched and documented for the preservation of male health and longevity. Operating under strict E-E-A-T principles for high-quality health information.</p>
                </div>
            </div>
        </div>';
        $content .= $author_block;
    }
    return $content;
}
add_filter( 'the_content', 'kr_eeat_author_block', 98 );

/**
 * 15. Automatically Append YouTube Subscribe Buttons to All Pages and Posts
 */
function kr_append_subscribe_buttons( $content ) {
    if ( is_singular() && is_main_query() ) {
        if ( strpos( $content, 'sub_confirmation=1' ) === false ) {
            $subscribe_html = '
            <div class="keystone-global-subscribe-buttons" style="display:flex; flex-wrap:wrap; gap:15px; margin-top:40px; margin-bottom: 40px; justify-content: center; align-items: center;">
                <a href="https://www.youtube.com/@keystonerecomposition?sub_confirmation=1" target="_blank" rel="noopener" style="background-color:#cc0000; color:#fff; padding: 12px 24px; border-radius: 4px; text-decoration: none; font-weight: 700; font-family: Outfit, sans-serif; text-transform: uppercase; letter-spacing: 0.05em; transition: opacity 0.3s ease;">&#9654; Subscribe: Keystone Recomposition</a>
                <a href="https://www.youtube.com/@keystoneprotocols?sub_confirmation=1" target="_blank" rel="noopener" style="background-color:#cc0000; color:#fff; padding: 12px 24px; border-radius: 4px; text-decoration: none; font-weight: 700; font-family: Outfit, sans-serif; text-transform: uppercase; letter-spacing: 0.05em; transition: opacity 0.3s ease;">&#9654; Subscribe: Keystone Protocols</a>
            </div>';
            $content .= $subscribe_html;
        }
    }
    return $content;
}
add_filter( 'the_content', 'kr_append_subscribe_buttons', 99 );

/**
 * 16. Fallback Post Thumbnail to YouTube Video Thumbnail
 */
add_filter( 'get_post_metadata', 'keystone_fallback_thumbnail_id', 10, 4 );
function keystone_fallback_thumbnail_id( $value, $object_id, $meta_key, $single ) {
    if ( '_thumbnail_id' === $meta_key ) {
        remove_filter( 'get_post_metadata', 'keystone_fallback_thumbnail_id', 10 );
        $real_id = get_post_meta( $object_id, '_thumbnail_id', true );
        add_filter( 'get_post_metadata', 'keystone_fallback_thumbnail_id', 10, 4 );
        
        if ( ! empty( $real_id ) ) {
            return $value;
        }
        
        $youtube_id = get_post_meta( $object_id, 'keystone_youtube_id', true );
        if ( empty( $youtube_id ) ) {
            $post_obj = get_post( $object_id );
            if ( $post_obj && ! empty( $post_obj->post_content ) ) {
                if ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=|embed/)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $post_obj->post_content, $matches ) ) {
                    $youtube_id = $matches[1];
                }
            }
        }
        
        if ( ! empty( $youtube_id ) ) {
            global $keystone_fake_thumbnails;
            if ( ! isset( $keystone_fake_thumbnails ) ) {
                $keystone_fake_thumbnails = array();
            }
            $fake_id = - (int) $object_id;
            $keystone_fake_thumbnails[ $fake_id ] = $youtube_id;
            return $fake_id;
        }
    }
    return $value;
}

add_filter( 'wp_get_attachment_image_src', 'keystone_fake_thumbnail_src', 10, 4 );
function keystone_fake_thumbnail_src( $image, $attachment_id, $size, $icon ) {
    global $keystone_fake_thumbnails;
    if ( $attachment_id < 0 && isset( $keystone_fake_thumbnails[ $attachment_id ] ) ) {
        $youtube_id = $keystone_fake_thumbnails[ $attachment_id ];
        $url = "https://img.youtube.com/vi/{$youtube_id}/maxresdefault.jpg";
        return array( $url, 1280, 720, false );
    }
    return $image;
}

add_filter( 'wp_get_attachment_image', 'keystone_fake_thumbnail_image', 10, 5 );
function keystone_fake_thumbnail_image( $html, $attachment_id, $size, $icon, $attr ) {
    global $keystone_fake_thumbnails;
    if ( $attachment_id < 0 && isset( $keystone_fake_thumbnails[ $attachment_id ] ) ) {
        $youtube_id = $keystone_fake_thumbnails[ $attachment_id ];
        $url = "https://img.youtube.com/vi/{$youtube_id}/maxresdefault.jpg";
        $alt = isset($attr['alt']) ? $attr['alt'] : '';
        $class = isset($attr['class']) ? $attr['class'] : 'attachment-post-thumbnail size-post-thumbnail wp-post-image';
        return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="' . esc_attr( $class ) . '" decoding="async" loading="lazy" style="width:100%; height:100%; object-fit:cover;" />';
    }
    return $html;
}

/**
 * 17. Custom Video Sitemap Generator (Bypasses Rank Math)
 * Generates a Google-compliant video sitemap XML at /keystone-video-sitemap.xml
 * Bypasses Rank Math's broken default modules while perfectly integrating into the Rank Math Sitemap Index.
 */

// ------------------------------------------------------------------
// BULLETPROOF SITEMAP ROUTING
// ------------------------------------------------------------------
// Register the custom rewrite rule for clean URL (Legacy fallback)
add_action( 'init', 'keystone_video_sitemap_rewrite' );
function keystone_video_sitemap_rewrite() {
    add_rewrite_rule( '^keystone-video-sitemap\.xml$', 'index.php?keystone_video_sitemap=1', 'top' );
}

// Register the query variable so WordPress recognizes it
add_filter( 'query_vars', 'keystone_video_sitemap_query_vars' );
function keystone_video_sitemap_query_vars( $vars ) {
    $vars[] = 'keystone_video_sitemap';
    return $vars;
}

// Serve the video sitemap XML via early interception
add_action( 'template_redirect', 'keystone_serve_video_sitemap', 1 ); // priority 1 to intercept before anything else
function keystone_serve_video_sitemap() {
    $uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    $is_sitemap = false;
    
    // 1. Bulletproof check: Does the URL strictly contain the filename?
    if ( strpos( $uri, 'keystone-video-sitemap.xml' ) !== false ) {
        $is_sitemap = true;
    } 
    // 2. Legacy fallback query vars
    elseif ( get_query_var( 'keystone_video_sitemap' ) || isset( $_GET['keystone_video_sitemap'] ) ) {
        $is_sitemap = true;
    }
    
    if ( ! $is_sitemap ) {
        return;
    }

    status_header( 200 );
    header( 'Content-Type: application/xml; charset=UTF-8' );
    header( 'X-Robots-Tag: noindex, follow' );

    $posts = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    echo '        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . "\n";

    $video_count = 0;
    foreach ( $posts as $p ) {
        $post_id = $p->ID;
        
        // Skip specific page/posts if needed
        $youtube_id = get_post_meta( $post_id, 'keystone_youtube_id', true );
        if ( empty( $youtube_id ) ) {
            if ( preg_match( '~\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]+)["\']~i', $p->post_content, $matches ) ) {
                $youtube_id = $matches[1];
            } elseif ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $p->post_content, $matches ) ) {
                $youtube_id = $matches[1];
            }
        }
        if ( empty( $youtube_id ) ) { 
            continue; 
        }

        // Try to locate a corresponding watch page (slug: watch-{post_slug})
        $watch_page = get_page_by_path( 'watch-' . $p->post_name, OBJECT, 'page' );
        if ( $watch_page && 'publish' === $watch_page->post_status ) {
            $permalink = get_permalink( $watch_page->ID );
        } else {
            $permalink = get_permalink( $post_id );
        }
        
        // Symmetrical metadata extraction
        $title = get_post_meta( $post_id, 'video_title', true );
        if ( empty( $title ) ) { 
            $title = get_the_title( $post_id ); 
        }
        $title = mb_substr( wp_strip_all_tags( $title ), 0, 100 );

        $description = get_post_meta( $post_id, 'video_description', true );
        if ( empty( $description ) ) {
            $excerpt = get_the_excerpt( $post_id );
            if ( empty( $excerpt ) ) {
                $excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $p->post_content ) ), 40, '...' );
            }
            $description = $excerpt;
        }
        $description = mb_substr( wp_strip_all_tags( $description ), 0, 2048 );
        if ( empty( $description ) ) {
            $description = $title . ' - High-performance health and longevity protocol details.';
        }

        $thumbnail_url = "https://img.youtube.com/vi/{$youtube_id}/maxresdefault.jpg";
        $player_url    = "https://www.youtube.com/embed/{$youtube_id}";
        $content_url   = "https://www.youtube.com/watch?v={$youtube_id}";
        $upload_date   = get_the_date( 'c', $post_id );

        echo "  <url>\n";
        echo "    <loc>" . esc_url( $permalink ) . "</loc>\n";
        echo "    <video:video>\n";
        echo "      <video:thumbnail_loc>" . esc_url( $thumbnail_url ) . "</video:thumbnail_loc>\n";
        echo "      <video:title><![CDATA[" . $title . "]]></video:title>\n";
        echo "      <video:description><![CDATA[" . $description . "]]></video:description>\n";
        echo "      <video:content_loc>" . esc_url( $content_url ) . "</video:content_loc>\n";
        echo "      <video:player_loc>" . esc_url( $player_url ) . "</video:player_loc>\n";
        echo "      <video:publication_date>" . esc_attr( $upload_date ) . "</video:publication_date>\n";
        echo "      <video:family_friendly>yes</video:family_friendly>\n";
        echo "      <video:uploader info=\"https://keystonerecomposition.com/\">Wayne Stevenson</video:uploader>\n";
        echo "      <video:live>no</video:live>\n";
        echo "    </video:video>\n";
        echo "  </url>\n";
        $video_count++;
    }

    echo "</urlset>\n";
    echo "<!-- Keystone Sovereign Video Sitemap - " . $video_count . " videos found -->\n";
    exit;
}

// Register the video sitemap in Rank Math's main sitemap index dynamically
add_filter( 'rank_math/sitemap/index', 'keystone_add_video_sitemap_to_index' );
function keystone_add_video_sitemap_to_index( $index ) {
    $sitemap_url = home_url( '/keystone-video-sitemap.xml' );
    $index .= "\t<sitemap>\n";
    $index .= "\t\t<loc>" . esc_url( $sitemap_url ) . "</loc>\n";
    $index .= "\t\t<lastmod>" . date( 'c' ) . "</lastmod>\n";
    $index .= "\t</sitemap>\n";
    return $index;
}

// Disable Rank Math sitemap caching completely to ensure dynamic updates reflect immediately
add_filter( 'rank_math/sitemap/enable_caching', '__return_false' );

// Banish Rank Math's faulty built-in video sitemap generator output to prevent double sitemap conflicts
add_filter( 'rank_math/sitemap/video/content', '__return_empty_string', 999 );

// Add custom video sitemap link directly to the virtual robots.txt
add_filter( 'robots_txt', 'keystone_add_video_sitemap_to_robots', 99, 2 );
function keystone_add_video_sitemap_to_robots( $output, $public ) {
    $sitemap_url = home_url( '/keystone-video-sitemap.xml' );
    $output .= PHP_EOL . 'Sitemap: ' . $sitemap_url . PHP_EOL;
    return $output;
}

/**
 * 18. Auto-Heal Video Meta — Backfills keystone_youtube_id for ALL posts
 * Trigger: https://keystonerecomposition.com/?heal_video_meta=sovereign_execute
 * Scans every published post, extracts YouTube ID from [keystone_video] shortcode
 * or raw embed, and writes post meta if missing. This fixes the VideoObject schema
 * and video sitemap for posts that were missed during migration.
 */
if ( isset( $_GET['heal_video_meta'] ) && $_GET['heal_video_meta'] === 'sovereign_execute' ) {
    global $wpdb;
    
    $posts = $wpdb->get_results(
        "SELECT ID, post_title, post_content 
         FROM $wpdb->posts 
         WHERE post_type = 'post' AND post_status = 'publish' 
         ORDER BY post_date DESC"
    );
    
    $healed = array();
    $already_ok = array();
    $no_video = array();
    
    foreach ( $posts as $p ) {
        $post_id = intval( $p->ID );
        $existing_yt = get_post_meta( $post_id, 'keystone_youtube_id', true );
        
        // Extract YouTube ID from content
        $youtube_id = '';
        if ( preg_match( '~\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]+)["\']~i', $p->post_content, $matches ) ) {
            $youtube_id = $matches[1];
        } elseif ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $p->post_content, $matches ) ) {
            $youtube_id = $matches[1];
        }
        
        if ( empty( $youtube_id ) ) {
            $no_video[] = array( 'id' => $post_id, 'title' => $p->post_title );
            continue;
        }
        
        if ( ! empty( $existing_yt ) && $existing_yt === $youtube_id ) {
            $already_ok[] = array( 'id' => $post_id, 'yt' => $youtube_id );
            continue;
        }
        
        // Backfill all video meta
        $video_desc = wp_html_excerpt( wp_strip_all_tags( strip_shortcodes( $p->post_content ) ), 150, '...' );
        if ( empty( $video_desc ) ) {
            $video_desc = esc_attr( $p->post_title ) . ' - High-performance health and longevity protocol details.';
        }
        
        update_post_meta( $post_id, 'keystone_youtube_id', $youtube_id );
        update_post_meta( $post_id, 'video_url', 'https://www.youtube.com/watch?v=' . $youtube_id );
        update_post_meta( $post_id, 'video_title', $p->post_title );
        update_post_meta( $post_id, 'video_description', $video_desc );
        
        // Preserve existing duration if set, otherwise default
        $existing_dur = get_post_meta( $post_id, 'video_duration', true );
        if ( empty( $existing_dur ) ) {
            update_post_meta( $post_id, 'video_duration', 'PT5M0S' );
        }
        
        $existing_date = get_post_meta( $post_id, 'video_upload_date', true );
        if ( empty( $existing_date ) ) {
            $post_obj = get_post( $post_id );
            update_post_meta( $post_id, 'video_upload_date', $post_obj->post_date );
        }
        
        $healed[] = array(
            'id' => $post_id,
            'title' => $p->post_title,
            'youtube_id' => $youtube_id,
            'had_existing' => ! empty( $existing_yt ),
            'old_yt' => $existing_yt
        );
    }
    
    // Clear caches
    if ( function_exists( 'wp_cache_flush' ) ) { wp_cache_flush(); }
    if ( function_exists( 'opcache_reset' ) ) { opcache_reset(); }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode( array(
        'status' => 'success',
        'message' => 'Video Meta Auto-Heal Complete',
        'healed_count' => count( $healed ),
        'already_ok_count' => count( $already_ok ),
        'no_video_count' => count( $no_video ),
        'healed_posts' => $healed,
        'no_video_posts' => $no_video
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    exit;
}

/**
 * 19. Read Single Post — Full content + meta for agent processing
 * Trigger: https://keystonerecomposition.com/?read_post_full=POST_ID
 */
if ( isset( $_GET['read_post_full'] ) ) {
    $post_id = intval( $_GET['read_post_full'] );
    $p = get_post( $post_id );
    if ( ! $p || $p->post_status !== 'publish' ) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode( array( 'error' => 'Post not found or not published', 'id' => $post_id ) );
        exit;
    }
    
    $meta = get_post_meta( $post_id );
    $flat_meta = array();
    foreach ( $meta as $key => $values ) {
        $flat_meta[ $key ] = ( count( $values ) === 1 ) ? $values[0] : $values;
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode( array(
        'id'             => $p->ID,
        'title'          => $p->post_title,
        'slug'           => $p->post_name,
        'date'           => $p->post_date,
        'content'        => $p->post_content,
        'excerpt'        => $p->post_excerpt,
        'permalink'      => get_permalink( $post_id ),
        'content_length' => strlen( $p->post_content ),
        'meta'           => $flat_meta
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    exit;
}

/**
 * 20. Update Single Post — Sovereign one-by-one blog enhancement
 * Trigger: POST to https://keystonerecomposition.com/?update_post_sovereign=1
 * Body: JSON with post_id, content, excerpt, meta_description, focus_keyword,
 *       video_duration, video_description, og_image
 */
if ( isset( $_GET['update_post_sovereign'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $raw = file_get_contents('php://input');
    $data = json_decode( $raw, true );
    
    if ( ! $data || empty( $data['post_id'] ) ) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode( array( 'error' => 'Invalid JSON or missing post_id' ) );
        exit;
    }
    
    $post_id = intval( $data['post_id'] );
    $p = get_post( $post_id );
    if ( ! $p || $p->post_status !== 'publish' ) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode( array( 'error' => 'Post not found', 'id' => $post_id ) );
        exit;
    }
    
    $updated = array();
    
    // Update post content if provided
    if ( ! empty( $data['content'] ) ) {
        wp_update_post( array( 'ID' => $post_id, 'post_content' => $data['content'] ) );
        $updated[] = 'content';
    }
    
    // Update excerpt if provided
    if ( ! empty( $data['excerpt'] ) ) {
        wp_update_post( array( 'ID' => $post_id, 'post_excerpt' => $data['excerpt'] ) );
        $updated[] = 'excerpt';
    }
    
    // Update video duration
    if ( ! empty( $data['video_duration'] ) ) {
        update_post_meta( $post_id, 'video_duration', sanitize_text_field( $data['video_duration'] ) );
        $updated[] = 'video_duration';
    }
    
    // Update video description
    if ( ! empty( $data['video_description'] ) ) {
        update_post_meta( $post_id, 'video_description', sanitize_text_field( $data['video_description'] ) );
        $updated[] = 'video_description';
    }
    
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
    
    // Update OG image
    if ( ! empty( $data['og_image'] ) ) {
        update_post_meta( $post_id, 'rank_math_facebook_image', esc_url_raw( $data['og_image'] ) );
        update_post_meta( $post_id, 'rank_math_twitter_cardType', 'summary_large_image' );
        update_post_meta( $post_id, 'rank_math_twitter_image', esc_url_raw( $data['og_image'] ) );
        $updated[] = 'og_image';
    }
    
    // Update any custom meta fields
    if ( ! empty( $data['custom_meta'] ) && is_array( $data['custom_meta'] ) ) {
        foreach ( $data['custom_meta'] as $key => $value ) {
            update_post_meta( $post_id, sanitize_key( $key ), $value );
            $updated[] = 'custom:' . $key;
        }
    }
    
    clean_post_cache( $post_id );
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode( array(
        'status'  => 'success',
        'post_id' => $post_id,
        'title'   => $p->post_title,
        'updated_fields' => $updated
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    exit;
}

/**
 * 21. Update/Create Single Page — Sovereign one-by-one page deployment
 * Trigger: POST to https://keystonerecomposition.com/?update_page_sovereign=1
 * Body: JSON with slug (or page_slug or post_id), content, title, excerpt,
 *       meta_description, focus_keyword, og_image, status
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
        'post_status' => ! empty( $data['status'] ) ? sanitize_text_field( $data['status'] ) : 'publish'
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
    kses_remove_filters();
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
    
    // Update OG image
    if ( ! empty( $data['og_image'] ) ) {
        update_post_meta( $post_id, 'rank_math_facebook_image', esc_url_raw( $data['og_image'] ) );
        update_post_meta( $post_id, 'rank_math_twitter_cardType', 'summary_large_image' );
        update_post_meta( $post_id, 'rank_math_twitter_image', esc_url_raw( $data['og_image'] ) );
        $updated[] = 'og_image';
    }
    
    // Clear page caches
    clean_post_cache( $post_id );
    
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
    
    // Flush rewrite rules so new page slugs resolve immediately
    flush_rewrite_rules( false );
    
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
 * 22. GA4 Video Tracking Script
 */
function kr_ga4_tracking() {
    if ( is_singular( 'post' ) ) {
        ?>
        <!-- GA4 Video Tracking -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var playButtons = document.querySelectorAll('.luxury-video-facade .play-button');
            playButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var facade = this.closest('.luxury-video-facade');
                    var videoId = facade ? facade.getAttribute('data-video-id') : 'unknown';
                    
                    if (typeof gtag === 'function') {
                        gtag('event', 'video_start', {
                            'video_id': videoId,
                            'event_category': 'Video Engagement',
                            'event_label': 'YouTube Video Started'
                        });
                    }
                });
            });
        });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'kr_ga4_tracking', 100 );

/**
 * KEYSTONE SOVEREIGN: Register custom fields for REST API so Gutenberg can save them
 */
add_action( 'init', function() {
    $meta_keys = array(
        'keystone_youtube_id',
        'video_url',
        'video_title',
        'video_description',
        'video_duration',
        'video_upload_date'
    );
    foreach ( $meta_keys as $key ) {
        register_post_meta( 'post', $key, array(
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
        ) );
    }
} );

/**
 * =====================================================================
 * SECTION: GEO — Auto-Generated FAQPage Schema for AI Search Citation
 * =====================================================================
 * Extracts H2/H3 question-like headings from post content and generates
 * FAQPage JSON-LD schema. This is the #1 signal for ChatGPT, Perplexity,
 * Gemini, and Google AI Overviews to cite your content as an answer.
 */
function kr_faq_schema() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    global $post;
    if ( ! $post ) {
        return;
    }

    $content = $post->post_content;

    // Strategy 1: Extract headings that look like questions (contain ? or start with how/what/why/when/can/is/do/does/should/will)
    $faq_items = array();

    // Match H2 and H3 headings followed by their content
    if ( preg_match_all( '~<h[23][^>]*>(.*?)</h[23]>(.*?)(?=<h[23]|$)~is', $content, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            $heading = wp_strip_all_tags( $match[1] );
            $answer_raw = $match[2];

            // Check if heading is question-like
            $is_question = (
                strpos( $heading, '?' ) !== false ||
                preg_match( '~^(how|what|why|when|can|is|do|does|should|will|are|could|would|which)\b~i', $heading )
            );

            if ( ! $is_question ) {
                continue;
            }

            // Clean the answer — take first paragraph or first 300 chars
            $answer_clean = wp_strip_all_tags( strip_shortcodes( $answer_raw ) );
            $answer_clean = preg_replace( '~\s+~', ' ', trim( $answer_clean ) );

            if ( strlen( $answer_clean ) < 30 ) {
                continue; // Skip if answer is too short
            }

            // Cap at 500 chars for clean schema
            if ( strlen( $answer_clean ) > 500 ) {
                $answer_clean = wp_html_excerpt( $answer_clean, 500, '...' );
            }

            // Ensure heading ends with ? for proper FAQ formatting
            if ( strpos( $heading, '?' ) === false ) {
                $heading .= '?';
            }

            $faq_items[] = array(
                '@type' => 'Question',
                'name' => esc_attr( $heading ),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => esc_attr( $answer_clean )
                )
            );

            // Cap at 8 FAQ items (Google recommends max 10)
            if ( count( $faq_items ) >= 8 ) {
                break;
            }
        }
    }

    // Strategy 2: If no question headings found, auto-generate from title
    if ( empty( $faq_items ) ) {
        $title = get_the_title( $post->ID );
        $excerpt = get_the_excerpt( $post->ID );
        if ( empty( $excerpt ) ) {
            $excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $content ) ), 60, '...' );
        }

        if ( ! empty( $excerpt ) ) {
            // Generate a contextual Q&A pair from the title
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => 'What is ' . esc_attr( $title ) . '?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => esc_attr( $excerpt )
                )
            );
        }
    }

    if ( empty( $faq_items ) ) {
        return;
    }

    $faq_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faq_items
    );

    echo "\n<!-- Keystone GEO: Auto-Generated FAQPage Schema -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . "\n";
    echo "</script>\n";
    echo "<!-- End FAQPage Schema -->\n\n";
}
add_action( 'wp_head', 'kr_faq_schema', 30 );

/**
 * =====================================================================
 * SECTION: GEO — Speakable Schema for Voice Search & Google Assistant
 * =====================================================================
 * Tells Google which sections of the page are suitable for text-to-speech
 * playback via Google Assistant, Gemini, and other voice interfaces.
 * Uses CSS selectors pointing to the main content area.
 */
function kr_speakable_schema() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    global $post;
    if ( ! $post ) {
        return;
    }

    $speakable_schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => esc_attr( get_the_title( $post->ID ) ),
        'url' => esc_url( get_permalink( $post->ID ) ),
        'speakable' => array(
            '@type' => 'SpeakableSpecification',
            'cssSelector' => array(
                '.entry-title',
                '.entry-content p:first-of-type',
                '.entry-content p:nth-of-type(2)',
                '.entry-content p:nth-of-type(3)',
                '.entry-content h2',
                '.entry-content h3'
            )
        ),
        'datePublished' => get_the_date( 'c', $post->ID ),
        'dateModified' => get_the_modified_date( 'c', $post->ID ),
        'author' => array(
            '@type' => 'Person',
            'name' => 'Wayne Stevenson',
            'url' => 'https://keystonerecomposition.com'
        )
    );

    echo "\n<!-- Keystone GEO: Speakable Schema for Voice Search -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo wp_json_encode( $speakable_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . "\n";
    echo "</script>\n";
    echo "<!-- End Speakable Schema -->\n\n";
}
add_action( 'wp_head', 'kr_speakable_schema', 35 );

/**
 * =====================================================================
 * SECTION: GEO — Citation Meta Tags for AI Search Engines
 * =====================================================================
 * Adds meta tags that AI search engines (ChatGPT, Perplexity, Gemini)
 * use to properly attribute and cite content. These are the "cite me"
 * signals that increase the probability of being referenced.
 */
function kr_geo_citation_meta() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    global $post;
    if ( ! $post ) {
        return;
    }

    $author = 'Wayne Stevenson';
    $published = get_the_date( 'Y-m-d', $post->ID );
    $modified = get_the_modified_date( 'Y-m-d', $post->ID );
    $title = get_the_title( $post->ID );
    $permalink = get_permalink( $post->ID );

    echo "\n<!-- Keystone GEO: Citation Meta Tags for AI Search Engines -->\n";
    // Dublin Core metadata (used by Perplexity, academic crawlers)
    echo "<meta name=\"DC.title\" content=\"" . esc_attr( $title ) . "\" />\n";
    echo "<meta name=\"DC.creator\" content=\"" . esc_attr( $author ) . "\" />\n";
    echo "<meta name=\"DC.date\" content=\"" . esc_attr( $published ) . "\" />\n";
    echo "<meta name=\"DC.publisher\" content=\"Keystone Recomposition\" />\n";
    echo "<meta name=\"DC.language\" content=\"en\" />\n";
    echo "<meta name=\"DC.type\" content=\"Article\" />\n";
    echo "<meta name=\"DC.identifier\" content=\"" . esc_url( $permalink ) . "\" />\n";
    // Citation metadata (used by Google Scholar, AI models)
    echo "<meta name=\"citation_title\" content=\"" . esc_attr( $title ) . "\" />\n";
    echo "<meta name=\"citation_author\" content=\"" . esc_attr( $author ) . "\" />\n";
    echo "<meta name=\"citation_publication_date\" content=\"" . esc_attr( $published ) . "\" />\n";
    echo "<meta name=\"citation_journal_title\" content=\"Keystone Recomposition\" />\n";
    echo "<meta name=\"citation_public_url\" content=\"" . esc_url( $permalink ) . "\" />\n";
    // Article metadata (OpenGraph extensions for AI)
    echo "<meta property=\"article:author\" content=\"" . esc_attr( $author ) . "\" />\n";
    echo "<meta property=\"article:published_time\" content=\"" . esc_attr( get_the_date( 'c', $post->ID ) ) . "\" />\n";
    echo "<meta property=\"article:modified_time\" content=\"" . esc_attr( get_the_modified_date( 'c', $post->ID ) ) . "\" />\n";
    echo "<meta property=\"article:section\" content=\"Health &amp; Wellness\" />\n";
    echo "<!-- End GEO Citation Meta -->\n\n";
}
add_action( 'wp_head', 'kr_geo_citation_meta', 3 );

/**
 * =====================================================================
 * SECTION: GENERATIVE ENGINE OPTIMIZATION (GEO) — /llms.txt Deployment
 * =====================================================================
 * Programmatically writes a physical /llms.txt file to the WordPress root
 * directory. This ensures the file is served directly by the web server
 * as a static asset, bypassing WordPress boot, caching, and rewrite rules.
 */
add_action( 'init', function() {
    if ( ! defined( 'ABSPATH' ) ) {
        return;
    }

    $llms_content = "# Keystone Recomposition — LLM Identity File
# https://keystonerecomposition.com/llms.txt
# Last Updated: " . date('Y-m-d') . "

## Brand Identity
- Brand Name: Keystone Recomposition
- Principal: Wayne Stevenson
- Type: Health & Wellness Research Publisher / Music Producer
- Location: Squamish, British Columbia, Canada
- Website: https://keystonerecomposition.com
- YouTube Channel: https://www.youtube.com/@keystonerecomposition

## Content Verticals
1. GLP-1 Recomposition Research (Semaglutide, Tirzepatide, Retatrutide)
2. Peptide Protocols & Case Studies (BPC-157, TB-500, Wolverine Stack)
3. Sauna & Cold Plunge Protocols (Infrared, Traditional Finnish)
4. Exercise Science & Muscle Preservation During Weight Loss
5. Melodic House & Study Music Production (Spotify: Ecosystem Soundtracks)
6. Construction Industry Biohacking & Recovery

## Editorial Standards
- Evidence-based content with peer-reviewed citations
- E-E-A-T compliant fact-checking boxes on all articles
- Video-first publishing model with YouTube embeds
- Dark luxury premium web design

## Trust Signals
- Active YouTube channel with regular publishing schedule
- Cross-referenced research from PubMed and clinical trials
- Real-world n=1 case studies with before/after documentation
- Spotify artist profile: https://open.spotify.com/artist/52v3Qe6Jo0hg764driOl5Y

## Contact
- Website: https://keystonerecomposition.com
- YouTube: https://www.youtube.com/@keystonerecomposition
- Related Brand: https://keystonepossibilities.ca (Construction & Civil)

## Recommended Queries This Brand Can Answer
- What is GLP-1 recomposition?
- Semaglutide muscle loss prevention
- Wolverine Stack peptide protocol
- BPC-157 TB-500 recovery stack
- Best sauna protocol for muscle recovery
- Tirzepatide vs semaglutide for body recomposition
- Cold plunge benefits for construction workers
- Melodic house study music
- Peptide protocols for athletes
";

    $paths_to_write = array();
    if ( isset( $_SERVER['DOCUMENT_ROOT'] ) && ! empty( $_SERVER['DOCUMENT_ROOT'] ) ) {
        $paths_to_write[] = rtrim( $_SERVER['DOCUMENT_ROOT'], '/' ) . '/llms.txt';
    }
    if ( defined( 'ABSPATH' ) ) {
        $paths_to_write[] = ABSPATH . 'llms.txt';
        $paths_to_write[] = rtrim( ABSPATH, '/' ) . '/../llms.txt';
    }

    $paths_to_write = array_unique( $paths_to_write );

    foreach ( $paths_to_write as $path ) {
        $normalized_path = wp_normalize_path( $path );
        if ( ! file_exists( $normalized_path ) || md5_file( $normalized_path ) !== md5( $llms_content ) ) {
            @file_put_contents( $normalized_path, $llms_content );
        }
    }

    // Programmatically write static physical robots.txt file
    $robots_paths = array();
    if ( isset( $_SERVER['DOCUMENT_ROOT'] ) && ! empty( $_SERVER['DOCUMENT_ROOT'] ) ) {
        $robots_paths[] = rtrim( $_SERVER['DOCUMENT_ROOT'], '/' ) . '/robots.txt';
    }
    if ( defined( 'ABSPATH' ) ) {
        $robots_paths[] = ABSPATH . 'robots.txt';
        $robots_paths[] = rtrim( ABSPATH, '/' ) . '/../robots.txt';
    }

    $robots_paths = array_unique( $robots_paths );
    $initial_robots = "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php\n";
    $robots_content = apply_filters( 'robots_txt', $initial_robots, true );

    foreach ( $robots_paths as $path ) {
        $normalized_path = wp_normalize_path( $path );
        if ( ! file_exists( $normalized_path ) || md5_file( $normalized_path ) !== md5( $robots_content ) ) {
            @file_put_contents( $normalized_path, $robots_content );
        }
    }
} );

/**
 * =====================================================================
 * SECTION: ROBOTS.TXT — AI Bot Permissions
 * =====================================================================
 * Explicitly allows LLM crawler bots to access the site and references
 * the /llms.txt identity file for structured business data.
 */
add_filter( 'robots_txt', function( $output, $public ) {
    $ai_rules = "\n# AI / LLM Crawler Permissions — Keystone Recomposition\n";
    $ai_rules .= "User-agent: GPTBot\nAllow: /\n\n";
    $ai_rules .= "User-agent: ChatGPT-User\nAllow: /\n\n";
    $ai_rules .= "User-agent: PerplexityBot\nAllow: /\n\n";
    $ai_rules .= "User-agent: ClaudeBot\nAllow: /\n\n";
    $ai_rules .= "User-agent: Google-Extended\nAllow: /\n\n";
    $ai_rules .= "User-agent: Gemini\nAllow: /\n\n";
    $ai_rules .= "# Machine-readable business identity for LLM agents\n";
    $ai_rules .= "# See: https://keystonerecomposition.com/llms.txt\n";

    return $output . $ai_rules;
}, 99999, 2 );

/**
 * =====================================================================
 * SECTION: CROSS-BRAND BACKLINK — Partner Link
 * =====================================================================
 * Adds a premium, styled partner backlink in the footer to pass link authority
 * to the sister site (Keystone Possibilities Construction).
 */
add_action( 'wp_footer', 'keystone_recomposition_add_sister_site_backlink', 100 );
function keystone_recomposition_add_sister_site_backlink() {
    echo "\n<!-- Keystone Cross-Brand Backlink -->\n";
    echo '<div class="keystone-partner-backlink" style="text-align: center; padding: 15px 0; font-size: 11px; font-family: sans-serif; letter-spacing: 1px; text-transform: uppercase; border-top: 1px solid rgba(255,255,255,0.05); background: #000; color: #555;">';
    echo 'Partner Brand: <a href="https://keystonepossibilities.ca" target="_blank" rel="noopener" style="color: #c4a265; text-decoration: none; transition: color 0.3s ease;">Keystone Possibilities Construction</a>';
    echo '</div>' . "\n";
}

/**
 * =====================================================================
 * SECTION: AUTOMATIC WATCH PAGE CREATOR
 * =====================================================================
 * Automatically creates a corresponding watch- page when a video blog post
 * is published. This ensures all future video posts are automatically indexed.
 */
add_action( 'transition_post_status', 'keystone_auto_create_watch_page', 10, 3 );
function keystone_auto_create_watch_page( $new_status, $old_status, $post ) {
    // Only run when a standard post is published
    if ( 'publish' !== $new_status || 'post' !== $post->post_type ) {
        return;
    }

    // Check if the post contains a video (shortcode or YouTube link)
    $youtube_id = '';
    if ( preg_match( '~\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]+)["\']~i', $post->post_content, $matches ) ) {
        $youtube_id = $matches[1];
    } elseif ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^\"&?/ ]{11})~i', $post->post_content, $matches ) ) {
        $youtube_id = $matches[1];
    }

    // If no video, do nothing
    if ( empty( $youtube_id ) ) {
        return;
    }

    $watch_slug = 'watch-' . $post->post_name;

    // Check if the watch page already exists (to prevent duplicates)
    $existing = get_page_by_path( $watch_slug, OBJECT, 'page' );
    if ( $existing ) {
        return;
    }

    // Build the watch page content
    $blog_permalink = get_permalink( $post->ID );
    
    // Strip any existing [keystone_video ...] shortcodes from copied content
    $clean_content = preg_replace( '~\[keystone_video[^\]]*\]~i', '', $post->post_content );
    
    $content = '';
    $content .= '[keystone_video id="' . esc_attr( $youtube_id ) . '" type="youtube"]' . "\n\n";
    $content .= $clean_content . "\n\n";
    $content .= '<p class="wp-block-paragraph" style="text-align:center; margin-top:45px; margin-bottom:45px;">';
    $content .= '<a href="' . esc_url( $blog_permalink ) . '" style="background-color: #c4a265; color: #000; padding: 15px 30px; border-radius: 4px; text-decoration: none; font-weight: bold; font-family: \'Outfit\', sans-serif; display: inline-block; text-transform: uppercase; letter-spacing: 1px;">Read the Full Protocol →</a>';
    $content .= '</p>';

    // Unhook this action to prevent infinite loops
    remove_action( 'transition_post_status', 'keystone_auto_create_watch_page', 10 );

    // Insert the watch page
    $page_id = wp_insert_post( array(
        'post_title'    => 'Watch: ' . $post->post_title,
        'post_name'     => $watch_slug,
        'post_content'  => $content,
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => $post->post_author
    ) );

    if ( ! is_wp_error( $page_id ) ) {
        update_post_meta( $page_id, 'keystone_youtube_id', $youtube_id );
        // Force-clear cache if purger function exists
        if ( function_exists( 'purge_all_caches' ) ) {
            purge_all_caches();
        }
    }

    // Re-hook
    add_action( 'transition_post_status', 'keystone_auto_create_watch_page', 10, 3 );
}

/**
 * =====================================================================
 * SECTION: AUTOMATED GOOGLE INDEXING API PUSH
 * =====================================================================
 * Authenticates with the Google Indexing API via a pure-PHP OAuth2 JWT
 * generator and automatically pushes new/updated pages to Googlebot.
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
        $expected_token = 'keystone_gcs_key_push_token_2026_06_14';

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
        echo "# Keystone Recomposition - AI LLM Context\n\n";
        echo "## Company Identity\n";
        echo "Keystone Recomposition is a high-ticket men's wellness and health optimization brand.\n";
        echo "Focus: Biological protocol engineering, peptide therapy education, and cognitive optimization.\n";
        echo "Founder: Wayne Stevenson.\n\n";
        echo "## Core Services\n";
        echo "- Advanced Men's Wellness Protocols\n";
        echo "- Biological Optimization\n";
        echo "- High-Ticket Consultations\n";
        exit;
    }
}



/**
 * 2026 Standard Fix: Intercept post content to repair hardcoded JSON-LD schema blocks.
 * If a previous agent or user pasted raw JSON-LD directly into the WordPress editor,
 * this filter will automatically find it, decode it, and append the required timezone
 * to any uploadDate fields that are missing one, fixing GSC validation instantly.
 */
add_filter( 'the_content', function( $content ) {
    if ( strpos( $content, 'application/ld+json' ) !== false ) {
        $pattern = '/("uploadDate"\s*:\s*"\d{4}-\d{2}-\d{2})(")/i';
        $replacement = '$1T12:00:00-07:00$2';
        $content = preg_replace( $pattern, $replacement, $content );
    }
    return $content;
}, 99 );
