<?php
/**
 * Keystone Recomposition Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Keystone Recomposition Child
 * @since 1.0.0
 */

/**
 * Enqueue styles
 */
function keystone_recomposition_child_enqueue_styles() {
	wp_enqueue_style(
		'keystone-recomposition-child-style',
		get_stylesheet_uri(),
		array( 'astra-theme-css' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'keystone_recomposition_child_enqueue_styles' );

/**
 * Generate Article JSON-LD Schema for single posts
 */
function keystone_recomposition_generate_article_schema() {
	if ( ! is_single() ) {
		return;
	}

	global $post;

	$article_schema = array(
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => get_the_title( $post ),
		'datePublished' => get_the_time( 'c', $post ),
		'dateModified'  => get_the_modified_time( 'c', $post ),
	);

	$author_id = $post->post_author;
	if ( $author_id ) {
		$article_schema['author'] = array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
		);
	}

	if ( has_post_thumbnail( $post ) ) {
		$article_schema['image'] = array(
			get_the_post_thumbnail_url( $post, 'full' )
		);
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo_url       = '';
	if ( $custom_logo_id ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	}

	$article_schema['publisher'] = array(
		'@type' => 'Organization',
		'name'  => 'Keystone Digital',
	);

	if ( $logo_url ) {
		$article_schema['publisher']['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $logo_url,
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $article_schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'keystone_recomposition_generate_article_schema' );

/**
 * Generate VideoObject JSON-LD Schema for embedded YouTube videos
 */
function keystone_recomposition_generate_video_schema() {
	if ( ! is_single() ) {
		return;
	}

	global $post;
	$content = $post->post_content;

	// Regular expression to find YouTube iframes or URLs
	// This matches YouTube iframes or basic YouTube URLs on their own lines.
	$pattern = '/(?:<iframe[^>]*src="[^"]*youtube\.com\/embed\/([^"?]+)[^"]*"[^>]*>|<\/iframe>|(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+))/i';

	if ( preg_match_all( $pattern, $content, $matches ) ) {
		// $matches[1] will have iframe IDs, $matches[2] will have URL IDs
		$video_ids = array_filter( array_merge( $matches[1], $matches[2] ) );
		$video_ids = array_unique( $video_ids );

		if ( empty( $video_ids ) ) {
			return;
		}

		foreach ( $video_ids as $video_id ) {
			$video_id = trim( $video_id );
			if ( empty( $video_id ) ) {
				continue;
			}

			$youtube_url = 'https://www.youtube.com/watch?v=' . $video_id;
			$oembed_url  = 'https://www.youtube.com/oembed?url=' . urlencode( $youtube_url ) . '&format=json';

			$response = wp_remote_get( $oembed_url );

			$video_schema = array(
				'@context'     => 'https://schema.org',
				'@type'        => 'VideoObject',
				'embedUrl'     => 'https://www.youtube.com/embed/' . $video_id,
				'contentUrl'   => $youtube_url,
			);

			$api_success = false;
			if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );

				if ( $data && isset( $data['title'] ) ) {
					$video_schema['name']         = $data['title'];
					$video_schema['thumbnailUrl'] = isset( $data['thumbnail_url'] ) ? $data['thumbnail_url'] : '';
					$api_success = true;
				}
			}

			// Fallback values or missing required properties
			if ( ! $api_success ) {
				$video_schema['name']         = get_the_title( $post );
				$video_schema['thumbnailUrl'] = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'full' ) : '';
			}

			// Set description and uploadDate (oEmbed doesn't always provide these, use post data as fallback)
			$video_schema['description'] = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $content ), 30 );
			$video_schema['uploadDate']  = get_the_time( 'c', $post );

			echo '<script type="application/ld+json">' . wp_json_encode( $video_schema ) . '</script>' . "\n";
		}
	}
}
add_action( 'wp_head', 'keystone_recomposition_generate_video_schema' );

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
