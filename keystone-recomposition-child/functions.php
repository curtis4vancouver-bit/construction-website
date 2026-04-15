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
