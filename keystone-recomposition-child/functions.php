<?php
/**
 * Keystone Recomposition Child Theme functions and definitions
 */

// Enqueue parent theme styles
add_action( 'wp_enqueue_scripts', 'keystone_recomposition_enqueue_styles' );
function keystone_recomposition_enqueue_styles() {
	wp_enqueue_style( 'keystone-recomposition-child-style', get_stylesheet_uri(), array( 'astra-theme-css' ), wp_get_theme()->get('Version') );
}

/**
 * Protect Google crawl budget and prevent URL indexing bloat.
 * Hooks into wp_head to output <meta name="robots" content="noindex, follow">
 * under specific conditions, particularly checking for non-whitelisted query parameters.
 */
add_action( 'wp_head', 'keystone_recomposition_seo_indexing_protection', 1 );
function keystone_recomposition_seo_indexing_protection() {
	// 1. Check for standard WordPress archive/search pages we want to noindex
	if ( is_date() || is_author() || is_tag() || is_search() ) {
		echo "<meta name='robots' content='noindex, follow'>\n";
		return;
	}

	// 2. Check for URL parameters
	if ( ! empty( $_GET ) ) {
		// Define the strict whitelist of allowed parameters
		$allowed_params = array(
			'page',
			'paged',
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'utm_term',
			'utm_content',
			'gclid',
			'fbclid',
			'ref'
		);

		// Get the keys of all current GET parameters
		$current_params = array_keys( $_GET );

		// Check if there's any parameter in $_GET that is NOT in our allowed list
		$has_unauthorized_params = false;
		foreach ( $current_params as $param ) {
			if ( ! in_array( $param, $allowed_params, true ) ) {
				$has_unauthorized_params = true;
				break;
			}
		}

		// If an unauthorized parameter is found, noindex the page
		if ( $has_unauthorized_params ) {
			echo "<meta name='robots' content='noindex, follow'>\n";
			return;
		}
	}
}
