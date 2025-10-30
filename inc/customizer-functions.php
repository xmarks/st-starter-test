<?php
/**
 * ST Starter Theme Customizer Functions
 *
 * @package ST_Starter
 */

if ( get_theme_mod( 'st_starter_disable_emojis', true ) ) {

	/**
	 * Disable WordPress Core Emojis
	 * Uses theme customizer setting `st_starter_disable_emojis`.
	 *
	 * @return void
	 */
	function st_starter_disable_wordpress_core_emojis(): void {

		// Remove actions and filters related to emojis.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		// Remove the TinyMCE emoji plugin.
		add_filter(
			'tiny_mce_plugins',
			function ( $plugins ) {
				if ( is_array( $plugins ) ) {
					return array_diff( $plugins, array( 'wpemoji' ) );
				} else {
					return array();
				}
			}
		);

		// Remove emoji CDN hostname from DNS prefetching hints.
		add_filter(
			'wp_resource_hints',
			function ( $urls, $relation_type ) {
				if ( 'dns-prefetch' === $relation_type ) {
					$emoji_svg_url = 'https://s.w.org/images/core/emoji/2/svg/';
					$urls          = array_diff( $urls, array( $emoji_svg_url ) );
				}
				return $urls;
			},
			10,
			2
		);
	}

	// Hook the function to 'init' and use theme setting "st_starter_disable_emojis".
	add_action( 'init', 'st_starter_disable_wordpress_core_emojis' );
}


if ( get_theme_mod( 'st_starter_enable_animatecss', false ) ) {

	/**
	 * Enqueue Stylesheet / Scripts for AnimateCSS support.
	 * Uses theme customizer setting `st_starter_enable_animatecss`.
	 *
	 * @return void
	 */
	function st_starter_enable_animatecss(): void {

		// Enqueue animateCSS Stylesheet.
		wp_enqueue_style( 'plugins.animatecss.animate' );

		// Enqueue script for in-view loading.
		wp_enqueue_script( 'plugins.animatecss.animate' );
	}

	// Hook the function to 'wp_enqueue_scripts' and use theme setting "st_starter_enable_animatecss".
	add_action( 'wp_enqueue_scripts', 'st_starter_enable_animatecss' );
}


if ( get_theme_mod( 'st_starter_disable_users_rest_api', false ) ) {

	/**
	 * Disable WordPress REST API `users` Endpoint for GET requests.
	 * Uses theme customizer setting `st_starter_disable_users_rest_api`.
	 *
	 * @param array $endpoints The registered REST API endpoints.
	 *
	 * @return array The filtered endpoints.
	 */
	function st_starter_disable_rest_users_get( array $endpoints ): array {

		// Define the routes to be disabled.
		$routes = array( '/wp/v2/users', '/wp/v2/users/(?P<id>[\d]+)' );

		// Loop through each route and remove the GET method handler.
		foreach ( $routes as $route ) {
			if ( empty( $endpoints[ $route ] ) ) {
				continue;
			}

			foreach ( $endpoints[ $route ] as $i => $handlers ) {
				if ( is_array( $handlers ) && isset( $handlers['methods'] ) && 'GET' === $handlers['methods'] ) {
					unset( $endpoints[ $route ][ $i ] );  // Unset the GET method handler.
				}
			}
		}

		return $endpoints;
	}

	// Hook the function into the 'rest_endpoints' action.
	add_filter( 'rest_endpoints', 'st_starter_disable_rest_users_get' );
}


if ( get_theme_mod( 'st_starter_disable_wp_version', false ) ) {

	/**
	 * Function to remove WordPress version query string from scripts and styles.
	 * Replace it instead with Theme Version Constant.
	 *
	 * @param string $src style or script src link.
	 * @return string
	 */
	function st_starter_remove_wp_version_from_assets( string $src ): string {

		// Get the installed WordPress version.
		$wp_version = get_bloginfo( 'version' );

		// Check if the URL contains the "ver=" parameter.
		if ( str_contains( $src, 'ver=' ) ) {
			// Extract the query string (everything after "?").
			$original_version = wp_parse_url( $src, PHP_URL_QUERY );

			// Convert query string into an associative array.
			parse_str( $original_version, $query_params );

			// If the 'ver' parameter exists and matches the WordPress version, replace it.
			if ( isset( $query_params['ver'] ) && $query_params['ver'] === $wp_version ) {
				$src = add_query_arg( 'ver', ST_STARTER_VERSION, remove_query_arg( 'ver', $src ) );
			}
		}

		return $src;
	}

	/**
	 * Function to remove WordPress version generator from <head>
	 *     Also calls style/script loader src ver replacer.
	 *
	 * @return void
	 */
	function st_starter_disable_wp_version(): void {
		// Remove WordPress version meta tag from the header.
		remove_action( 'wp_head', 'wp_generator' );

		// Remove WordPress version from RSS feeds.
		add_filter( 'the_generator', '__return_empty_string' );

		// Remove WordPress version from script and style URLs.
		add_filter( 'style_loader_src', 'st_starter_remove_wp_version_from_assets', 9999 );
		add_filter( 'script_loader_src', 'st_starter_remove_wp_version_from_assets', 9999 );
	}

	add_action( 'init', 'st_starter_disable_wp_version' );
}


/**
 * Handle disabling sizes defined from Customizer.
 *
 * @param array $sizes current sizes available.
 *
 * @return array
 */
function st_starter_disable_thumbnail_sizes( array $sizes ): array {

	$stores_persistent_sizes = get_option( 'st_starter_all_thumbnail_sizes', array() );

	foreach ( $stores_persistent_sizes as $size => $attributes ) {
		$size_setting_name = 'st_starter_disable_thumbnail_' . $size;
		$size_setting      = get_theme_mod( $size_setting_name, false );

		if ( $size_setting ) {
			if ( is_array( $sizes ) && in_array( $size, $sizes, true ) ) {
				$sizes = array_values( array_diff( $sizes, array( $size ) ) );
			}
		}
	}

	return $sizes;
}

add_filter( 'intermediate_image_sizes', 'st_starter_disable_thumbnail_sizes' );
add_filter( 'intermediate_image_sizes_advanced', 'st_starter_disable_thumbnail_sizes' );


if ( get_theme_mod( 'st_starter_html_header_code', '' ) ) {

	/**
	 * Output custom HTML header code inside <head>.
	 *
	 * @return void
	 */
	function st_starter_add_html_header_code(): void {
		echo wp_kses(
			get_theme_mod( 'st_starter_html_header_code', '' ),
			st_starter_allowed_html_head_tags()
		);
	}

	add_action( 'wp_head', 'st_starter_add_html_header_code' );
}
