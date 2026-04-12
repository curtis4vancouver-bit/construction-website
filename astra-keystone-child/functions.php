<?php
/**
 * Astra Keystone Child Theme functions and definitions
 */

/**
 * Enqueue parent theme styles.
 */
function astra_keystone_child_enqueue_styles() {
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array( 'astra-theme-css' ), wp_get_theme()->get( 'Version' ), 'all' );
}
add_action( 'wp_enqueue_scripts', 'astra_keystone_child_enqueue_styles', 15 );

/**
 * Redirect attachment pages to parent post or homepage.
 * Redirect specific dead URLs to their new targets.
 */
function keystone_seo_redirects() {
    // 1. Attachment Page Bloat Fix
    if ( is_attachment() ) {
        global $post;
        if ( $post && $post->post_parent ) {
            // Redirect to parent post
            wp_redirect( get_permalink( $post->post_parent ), 301 );
            exit;
        } else {
            // Unattached, redirect to homepage
            wp_redirect( home_url( '/' ), 301 );
            exit;
        }
    }

    // 2. Dead URLs Redirect Fix
    $request_uri = $_SERVER['REQUEST_URI'];
    $parsed_uri = parse_url( $request_uri, PHP_URL_PATH ); // Extract path to ignore query strings

    // Exact matches
    $exact_redirects = array(
        '/contact-2/' => '/contact/',
        '/1121/'      => '/',
    );

    if ( array_key_exists( $parsed_uri, $exact_redirects ) ) {
        wp_redirect( home_url( $exact_redirects[ $parsed_uri ] ), 301 );
        exit;
    }

    // Pattern match (e.g., /wp-content/themes/astra/*)
    // WordPress routes these through index.php if they result in a 404, which template_redirect will catch.
    if ( strpos( $parsed_uri, '/wp-content/themes/astra/' ) === 0 ) {
        wp_redirect( home_url( '/' ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'keystone_seo_redirects' );
