<?php
// Enqueue parent theme styles
add_action( 'wp_enqueue_scripts', 'keystone_recomposition_child_enqueue_styles' );
function keystone_recomposition_child_enqueue_styles() {
    wp_enqueue_style( 'astra-theme-css', get_template_directory_uri() . '/style.css' );
}

/**
 * Filter the single post title wrapper to ensure it's strictly an H1.
 */
add_filter( 'astra_the_title_before', 'keystone_recomposition_child_title_before', 10, 1 );
function keystone_recomposition_child_title_before( $before ) {
    if ( is_singular() ) {
        // Force the opening tag to be an h1 for single posts/pages.
        // Astra natively passes <h1 ...> for some places, but let's be sure.
        return preg_replace('/^<h[1-6]/i', '<h1', $before);
    }
    return $before;
}

add_filter( 'astra_the_title_after', 'keystone_recomposition_child_title_after', 10, 1 );
function keystone_recomposition_child_title_after( $after ) {
    if ( is_singular() ) {
        return preg_replace('/<\/h[1-6]>/i', '</h1>', $after);
    }
    return $after;
}

/**
 * Filter the archive post title wrapper to ensure it's strictly an H2, preventing multiple H1s.
 */
add_filter( 'astra_the_post_title_before', 'keystone_recomposition_child_post_title_before', 10, 1 );
function keystone_recomposition_child_post_title_before( $before ) {
    if ( ! is_singular() ) {
        // Change any h1-h6 opening tag to h2 for post loops in archives/index
        return preg_replace('/^<h[1-6]/i', '<h2', $before);
    }
    return $before;
}

add_filter( 'astra_the_post_title_after', 'keystone_recomposition_child_post_title_after', 10, 1 );
function keystone_recomposition_child_post_title_after( $after ) {
    if ( ! is_singular() ) {
        return preg_replace('/<\/h[1-6]>/i', '</h2>', $after);
    }
    return $after;
}

/**
 * Inject JSON-LD Schema into wp_head
 */
function keystone_recomposition_child_inject_schema() {
    // Attempt to get custom logo from theme mods, fallback to default URL
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
    if ( ! $logo_url ) {
        $logo_url = 'https://keystonerecomposition.com/wp-content/uploads/logo.png';
    }

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
            'https://musicbrainz.org/label/30027d0e-6aeb-4704-8792-a031c936c62a',
            'https://audiomack.com/keystone-recomposition',
            'https://toolost.com'
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
                'keywords' => 'Keystone Recomposition, GLP-1, health, beauty, wellness, weight loss, fitness'
            ),
            array(
                '@type' => 'HomeAndConstructionBusiness',
                'name' => 'Keystone Possibilities',
                'url' => 'https://keystonepossibilities.ca',
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

    echo "<!-- Keystone Digital JSON-LD Schema -->\n";
    echo "<script type=\"application/ld+json\">\n";
    echo $json_schema . "\n";
    echo "</script>\n";
    echo "<!-- End Keystone Digital JSON-LD Schema -->\n";
}
add_action( 'wp_head', 'keystone_recomposition_child_inject_schema' );

/**
 * General SEO Fixes: output noindex for specific archives and unrecognized query parameters.
 */
function keystone_recomposition_child_seo_noindex() {
    $should_noindex = false;

    // Check for specific archive types that should not be indexed
    if ( is_date() || is_author() || is_tag() || is_search() ) {
        $should_noindex = true;
    }

    // Check for unrecognized GET parameters
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
add_action( 'wp_head', 'keystone_recomposition_child_seo_noindex', 1 );

/**
 * Automatically inject VideoObject Schema for YouTube embeds in singular posts/pages
 */
function keystone_recomposition_child_youtube_schema() {
    if ( ! is_singular() ) {
        return;
    }

    global $post;
    $content = $post->post_content;

    // Regex to match youtube iframes or plain youtube links (often auto-embedded by WP)
    // Matches youtube.com/embed/ID, youtube.com/watch?v=ID, youtu.be/ID
    $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i';

    if ( preg_match_all( $pattern, $content, $matches ) ) {
        $video_ids = array_unique( $matches[1] );

        foreach ( $video_ids as $video_id ) {
            $thumbnail_urls = array(
                'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg',
                'https://img.youtube.com/vi/' . $video_id . '/sddefault.jpg',
                'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg',
                'https://img.youtube.com/vi/' . $video_id . '/default.jpg'
            );
            $embed_url = 'https://www.youtube.com/embed/' . $video_id;
            $content_url = 'https://www.youtube.com/watch?v=' . $video_id;

            $video_name = get_the_title();
            if ( empty( $video_name ) ) {
                $video_name = 'Video for ' . get_bloginfo( 'name' );
            }

            $video_description = wp_trim_words( wp_strip_all_tags( $content ), 40, '...' );
            if ( empty( $video_description ) ) {
                $video_description = $video_name;
            }

            $video_schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'VideoObject',
                'name' => $video_name,
                'description' => $video_description,
                'thumbnailUrl' => $thumbnail_urls,
                'uploadDate' => get_the_date('c'),
                'embedUrl' => $embed_url,
                'contentUrl' => $content_url
            );

            $json_video_schema = wp_json_encode( $video_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

            echo "<!-- Keystone Digital VideoObject Schema for YouTube -->\n";
            echo "<script type=\"application/ld+json\">\n";
            echo $json_video_schema . "\n";
            echo "</script>\n";
            echo "<!-- End VideoObject Schema -->\n";
        }
    }
}
add_action( 'wp_head', 'keystone_recomposition_child_youtube_schema' );
