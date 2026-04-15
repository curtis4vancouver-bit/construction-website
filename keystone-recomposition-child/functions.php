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
