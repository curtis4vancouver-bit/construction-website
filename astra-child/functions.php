<?php
/**
 * Keystone Possibilities Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

// Enqueue Parent Theme Styles
function keystone_possibilities_child_enqueue_styles() {
    wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'keystone_possibilities_child_enqueue_styles', 15 );

// Fix 404 Errors with 301 Redirects
function keystone_seo_custom_redirects() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($request_uri);
    $path = $parsed_url['path'];
    $query = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';

    // Redirect /contact-2/ to /contact/
    if ( $path === '/contact-2/' ) {
        wp_redirect( home_url('/contact/') . $query, 301 );
        exit;
    }

    // Redirect /1121/ to /
    if ( $path === '/1121/' ) {
        wp_redirect( home_url('/') . $query, 301 );
        exit;
    }
}
add_action( 'template_redirect', 'keystone_seo_custom_redirects' );

// Fix Attachment Page Bloat
function keystone_seo_attachment_redirect() {
    if ( is_attachment() ) {
        global $post;
        if ( $post && $post->post_parent ) {
            // Redirect to parent post
            wp_redirect( get_permalink( $post->post_parent ), 301 );
            exit;
        } else {
            // Redirect to direct file URL
            $file_url = wp_get_attachment_url( $post->ID );
            if ( $file_url ) {
                wp_redirect( $file_url, 301 );
                exit;
            } else {
                // Fallback to home if no file URL found
                wp_redirect( home_url('/'), 301 );
                exit;
            }
        }
    }
}
add_action( 'template_redirect', 'keystone_seo_attachment_redirect' );

// Fix Search Query Indexing
function keystone_seo_noindex_search() {
    if ( is_search() ) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
}
add_action( 'wp_head', 'keystone_seo_noindex_search' );
