<?php
/**
 * Keystone Possibilities Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Keystone Possibilities Child
 * @since 1.0.0
 */

/**
 * Enqueue parent theme styles.
 */
function astra_child_enqueue_styles() {
	wp_enqueue_style( 'astra-theme-css', get_template_directory_uri() . '/style.css', array(), ASTRA_THEME_VERSION, 'all' );
	wp_enqueue_style( 'astra-child-css', get_stylesheet_directory_uri() . '/style.css', array( 'astra-theme-css' ), '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles', 15 );

/**
 * Handle custom 301 redirects to fix 404 errors and GSC broken links.
 */
function keystone_custom_redirects() {
	// Only process on frontend
	if ( is_admin() ) {
		return;
	}

	$request_uri = $_SERVER['REQUEST_URI'];

	// Parse the URL to separate path and query string
	$parsed_url = wp_parse_url( $request_uri );
	$path = isset( $parsed_url['path'] ) ? trailingslashit( $parsed_url['path'] ) : '/';
	$query = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';

	$redirect_to = false;

	// Specific redirect for contact page
	if ( '/contact-2/' === $path ) {
		$redirect_to = home_url( '/contact/' );
	}

	// List of broken slugs to redirect to the homepage
	$homepage_redirects = array(
		'/1121/',
		'/fulton/',
		'/saint-a/',
		'/foundation/',
		'/project-manager/',
		'/2025/10/07/step-/',
		'/2025/11/13/a-bc-/',
		'/20171020_153133/',
		'/20171020_153133-1/',
		'/final-logo-ks/',
		'/final-logo-ks4/',
		'/final-logo-ks-2/',
		'/final-logo-ks4-w/',
		'/final-logo-ks4-w-1/',
		'/noun-framing-203197/',
		'/cropped-final-logo-ks-jpg/',
		'/cropped-final-logo-ks-png/',
		'/screenshot-2023-10-10-at-4-37-35-pm/',
	);

	if ( in_array( $path, $homepage_redirects, true ) ) {
		$redirect_to = home_url( '/' );
	}

	if ( $redirect_to ) {
		// Append query string if it exists to preserve parameters like UTM tags
		$redirect_url = $redirect_to . $query;
		wp_safe_redirect( $redirect_url, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'keystone_custom_redirects' );

/**
 * Redirect attachment pages to parent post, direct file URL, or homepage.
 */
function keystone_attachment_redirect() {
	if ( is_admin() ) {
		return;
	}

	if ( is_attachment() ) {
		global $post;
		if ( ! empty( $post->post_parent ) ) {
			wp_safe_redirect( get_permalink( $post->post_parent ), 301 );
			exit;
		} else {
			$attachment_url = wp_get_attachment_url( $post->ID );
			if ( $attachment_url ) {
				wp_safe_redirect( $attachment_url, 301 );
				exit;
			} else {
				wp_safe_redirect( home_url( '/' ), 301 );
				exit;
			}
		}
	}
}
add_action( 'template_redirect', 'keystone_attachment_redirect' );

/**
 * Add noindex, follow to search result pages to prevent indexing of search queries.
 */
function keystone_noindex_search_results() {
	if ( is_search() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}
}
add_action( 'wp_head', 'keystone_noindex_search_results' );
