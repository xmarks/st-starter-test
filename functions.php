<?php
/**
 * ST Starter functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ST_Starter
 */

/**
 * Load Composer autoloader
 *  if present
 */
if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
	require_once get_template_directory() . '/vendor/autoload.php';
}


if ( ! defined( 'ST_STARTER_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'ST_STARTER_VERSION', '1.0.0' );
}


/**
 * Theme Core functions
 *  contains what remains usually unchanged
 */
require get_template_directory() . '/inc/core.php';


/**
 * Load Custom Post Types.
 */
require get_template_directory() . '/inc/cpt.php';


/**
 * Theme Widgets
 *  Register Widget areas
 */
require get_template_directory() . '/inc/widgets.php';


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';


/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';


/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';


/**
 * Customizer functions.
 */
require get_template_directory() . '/inc/customizer-functions.php';


/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}


/**
 * Load Theme debug functions
 *  - Make sure to call this last in functions.php file
 */
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	require_once get_template_directory() . '/inc/debug.php';
}
