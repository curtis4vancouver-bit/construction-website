<?php

/**
 * 1. Enqueue Parent Stylesheet and Google Fonts
 */
function kp_enqueue_styles() {
    // Enqueue parent Astra style
    wp_enqueue_style( 'astra-parent-theme-css', get_template_directory_uri() . '/style.css' );
    
    // Enqueue Child customized style
    wp_enqueue_style( 'astra-child-keystone-css', get_stylesheet_directory_uri() . '/style.css', array( 'astra-parent-theme-css' ), '1.0.3' );
    
    // Load typography fonts (Montserrat, Inter, Outfit)
    wp_enqueue_style( 'keystone-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@700&family=Outfit:wght@400;600;700;800&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'kp_enqueue_styles' );

/**
 * 3. Preconnecting Web Fonts (Performance GSC optimization)
 */
function kp_resource_hints( $urls, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type || 'preconnect' === $relation_type ) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = 'https://fonts.gstatic.com';
    }
    return $urls;
}
add_filter( 'wp_resource_hints', 'kp_resource_hints', 10, 2 );

/**
 * 3. Decharge Redundant Header Scripts (Optimizing PageSpeed score to 95+)
 */
function kp_clean_header() {
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
add_action( 'init', 'kp_clean_header' );

/**
 * 4. Filter script loading tags to apply modern defer attribute flags to custom scripts
 */
function kp_add_defer_attribute( $tag, $handle ) {
    if ( 'keystone-lazy-player' !== $handle ) {
        return $tag;
    }
    return str_replace( ' src', ' defer="defer" src', $tag );
}
add_filter( 'script_loader_tag', 'kp_add_defer_attribute', 10, 2 );

/**
 * 5. Filter the single post title wrapper to ensure it's strictly an H1.
 */
add_filter( 'astra_the_title_before', 'kp_title_before', 10, 1 );
function kp_title_before( $before ) {
    if ( is_singular() ) {
        return preg_replace('~^<h[1-6]~i', '<h1', $before);
    }
    return $before;
}

add_filter( 'astra_the_title_after', 'kp_title_after', 10, 1 );
function kp_title_after( $after ) {
    if ( is_singular() ) {
        return preg_replace('~</h[1-6]>~i', '</h1>', $after);
    }
    return $after;
}

/**
 * 6. Filter the archive post title wrapper to ensure it's strictly an H2, preventing multiple H1s.
 */
add_filter( 'astra_the_post_title_before', 'kp_post_title_before', 10, 1 );
function kp_post_title_before( $before ) {
    if ( ! is_singular() ) {
        return preg_replace('~^<h[1-6]~i', '<h2', $before);
    }
    return $before;
}

add_filter( 'astra_the_post_title_after', 'kp_post_title_after', 10, 1 );
function kp_post_title_after( $after ) {
    if ( ! is_singular() ) {
        return preg_replace('~</h[1-6]>~i', '</h2>', $after);
    }
    return $after;
}

