<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child for Keystone
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once get_stylesheet_directory() . '/inc/seo-video-injector.php';
require_once get_stylesheet_directory() . '/inc/indexing-api.php';
require_once get_stylesheet_directory() . '/inc/sovereign-migration.php';
require_once get_stylesheet_directory() . '/inc/enqueue.php';
require_once get_stylesheet_directory() . '/inc/seo-schema.php';
