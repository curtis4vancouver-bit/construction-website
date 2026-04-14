<?php
/**
 * Keystone Recomposition Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Keystone Recomposition Child
 */

/**
 * Enqueue parent and child scripts and styles.
 */
function keystone_recomposition_enqueue_styles() {
	wp_enqueue_style( 'keystone-recomposition-child-style', get_stylesheet_uri(), array( 'astra-theme-css' ), wp_get_theme()->get('Version') );
}
add_action( 'wp_enqueue_scripts', 'keystone_recomposition_enqueue_styles' );

/**
 * Remove native WordPress bloat from wp_head()
 */
function keystone_recomposition_cleanup_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );
}
add_action( 'init', 'keystone_recomposition_cleanup_head' );

/**
 * Disable native WordPress emojis comprehensively.
 */
function keystone_recomposition_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'keystone_recomposition_disable_emojis_tinymce' );

	// Remove emoji DNS prefetch
	add_filter( 'wp_resource_hints', 'keystone_recomposition_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'keystone_recomposition_disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array $plugins
 * @return array Difference between the two arrays
 */
function keystone_recomposition_disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference between the two arrays.
 */
function keystone_recomposition_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}

	return $urls;
}

/**
 * Defer non-critical JavaScript to improve INP scores.
 *
 * @param string $tag    The `<script>` tag for the enqueued script.
 * @param string $handle The script's registered handle.
 * @return string The modified `<script>` tag.
 */
function keystone_recomposition_defer_scripts( $tag, $handle ) {
	if ( is_admin() ) {
		return $tag;
	}

	// Exclude core scripts like jQuery from being deferred
	$excluded_scripts = array(
		'jquery',
		'jquery-core',
		'jquery-migrate',
	);

	if ( in_array( $handle, $excluded_scripts, true ) ) {
		return $tag;
	}

	if ( false === strpos( $tag, 'defer="defer"' ) && false === strpos( $tag, 'defer ' ) ) {
		$tag = str_replace( ' src', ' defer="defer" src', $tag );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'keystone_recomposition_defer_scripts', 10, 2 );
