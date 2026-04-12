<?php
/**
 * Keystone Possibilities Child Theme functions and definitions
 */

/**
 * Enqueue parent and child styles.
 */
function keystone_child_enqueue_styles() {
	// Enqueue parent theme style
	wp_enqueue_style( 'astra-theme-css', get_template_directory_uri() . '/style.css' );

	// Enqueue child theme style
	wp_enqueue_style( 'keystone-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'astra-theme-css' ), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'keystone_child_enqueue_styles' );

/**
 * Handle custom redirects for 404s and specific paths.
 */
function keystone_custom_redirects() {
	// Get the current URL path
	$request_uri = $_SERVER['REQUEST_URI'];
	$parsed_url = wp_parse_url( $request_uri );
	$path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
	$query_string = isset( $_SERVER['QUERY_STRING'] ) && ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '';

	// Array of broken paths to redirect to homepage
	$broken_paths = array(
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
		'/screenshot-2023-10-10-at-4-37-35-pm/'
	);

	if ( $path === '/contact-2/' ) {
		wp_redirect( home_url( '/contact/' ) . $query_string, 301 );
		exit;
	} elseif ( in_array( $path, $broken_paths, true ) ) {
		wp_redirect( home_url( '/' ) . $query_string, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'keystone_custom_redirects' );

/**
 * Redirect attachment pages to parent post, file URL, or homepage.
 */
function keystone_redirect_attachment_pages() {
	if ( is_attachment() ) {
		global $post;
		$query_string = isset( $_SERVER['QUERY_STRING'] ) && ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '';

		if ( ! empty( $post->post_parent ) ) {
			wp_redirect( get_permalink( $post->post_parent ) . $query_string, 301 );
			exit;
		}

		$file_url = wp_get_attachment_url( $post->ID );
		if ( $file_url ) {
			wp_redirect( $file_url . $query_string, 301 );
			exit;
		}

		wp_redirect( home_url( '/' ) . $query_string, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'keystone_redirect_attachment_pages' );

/**
 * Add noindex, follow to search pages.
 */
function keystone_noindex_search_pages() {
	if ( is_search() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}
}
add_action( 'wp_head', 'keystone_noindex_search_pages' );
