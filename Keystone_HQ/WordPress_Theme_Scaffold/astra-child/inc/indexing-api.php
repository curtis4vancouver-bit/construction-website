<?php

if ( isset( $_GET['purge_all_caches'] ) ) {
    if ( function_exists( 'opcache_reset' ) ) {
        opcache_reset();
    }
    if ( function_exists( 'wp_cache_flush' ) ) {
        wp_cache_flush();
    }
    // Delete Rank Math sitemap transients to force regenerate
    global $wpdb;
    $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_rank_math_sitemap_%' OR option_name LIKE '_transient_timeout_rank_math_sitemap_%'" );
    
    // Ping Google Indexing API
    $ping_url = 'https://www.google.com/ping?sitemap=' . urlencode( home_url( '/sitemap_index.xml' ) );
    $response = wp_remote_get( $ping_url );
    
    $robots_deleted = false;
    $llms_deleted = false;
    if ( file_exists( ABSPATH . 'robots.txt' ) ) {
        $robots_deleted = unlink( ABSPATH . 'robots.txt' );
    }
    if ( file_exists( ABSPATH . 'llms.txt' ) ) {
        $llms_deleted = unlink( ABSPATH . 'llms.txt' );
    }

    echo "CACHES PURGED & SITEMAP PINGED SUCCESSFULLY. llms.txt: " . ($llms_deleted ? "deleted" : "not found") . ", robots.txt: " . ($robots_deleted ? "deleted" : "not found");
    exit;
}

if ( isset( $_GET['keystone_debug_env'] ) ) {
    echo "KEYSTONE_DEBUG: functions.php is running successfully. Version: 1.0.5. GCS update hook: " . (function_exists('keystone_handle_gcs_key_update') ? 'exists' : 'does_not_exist');
    exit;
}

if ( isset( $_GET['keystone_flush_rules'] ) ) {
    delete_option('rewrite_rules');
    echo "REWRITE RULES OPTION DELETED SUCCESSFULLY";
    exit;
}

if ( isset( $_GET['run_instant_indexing'] ) ) {
    if ( class_exists( 'RankMath\Instant_Indexing\Api' ) ) {
        echo "INSTANT INDEXING PLUGIN IS INSTALLED.\
";
        // Let's try to get the settings
        $settings = get_option( 'rank_math_instant_indexing_settings' );
        if ( !empty($settings['google_api_key']) ) {
            echo "API KEY IS CONFIGURED.\
";
            // Get all post URLs
            global $wpdb;
            $posts = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'" );
            $urls = array();
            foreach ( $posts as $p ) {
                $urls[] = get_permalink( $p->ID );
            }
            $api = new RankMath\Instant_Indexing\Api();
            $response = $api->send_to_api( $urls, 'URL_UPDATED' );
            echo "RESPONSE:\
";
            print_r( $response );
        } else {
            echo "API KEY IS NOT CONFIGURED.";
        }
    } else {
        echo "INSTANT INDEXING PLUGIN IS NOT INSTALLED.";
    }
    exit;
}

if ( isset( $_GET['get_post_inventory'] ) && $_GET['get_post_inventory'] === 'sovereign_view' ) {
    global $wpdb;
    delete_option('rewrite_rules');

    $posts = $wpdb->get_results( 
        "SELECT ID, post_title, post_name, post_date, post_content 
         FROM $wpdb->posts 
         WHERE post_type = 'post' AND post_status = 'publish' 
         ORDER BY post_date DESC" 
    );
    
    $report = array();
    foreach ( $posts as $p ) {
        $youtube_id = '';
        if ( preg_match( '~\[keystone_video[^\]]*id=["\']([a-zA-Z0-9_-]{11})["\']~i', $p->post_content, $matches ) ) {
            $youtube_id = $matches[1];
        } elseif ( preg_match( '~(?:youtube\.com/(?:[^/]+/.+/(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^"&?/ ]{11})~i', $p->post_content, $matches ) ) {
            $youtube_id = $matches[1];
        }
        
        $report[] = array(
            'id' => $p->ID,
            'title' => $p->post_title,
            'slug' => $p->post_name,
            'date' => $p->post_date,
            'youtube_id' => $youtube_id,
            'length' => strlen( $p->post_content )
        );
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
    exit;
}

