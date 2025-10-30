<?php
/**
 * ST Starter core functions
 *
 * @package ST_Starter
 */

/**
 * Theme plugin dependencies notice
 *  List of required plugins and links to download them
 *
 * @return void
 */
function st_starter_plugin_dependencies(): void {
	// List of required plugins and their download links.
	$required_plugins = array(
		'advanced-custom-fields-pro/acf.php' => array(
			'name' => 'ACF Pro',
			'url'  => 'https://www.advancedcustomfields.com/pro/',
		),
		'acf-svg-icon/acf-svg-icon.php'      => array(
			'name' => 'ACF SVG Icon Field',
			'url'  => 'https://github.com/shoot56/acf-svg-icon',
		),
	);

	$inactive_plugins = array();

	foreach ( $required_plugins as $plugin_path => $plugin_info ) {
		if ( ! is_plugin_active( $plugin_path ) ) {
			$inactive_plugins[] = sprintf(
				'<strong><a href="%s" target="_blank">%s</a></strong>',
				esc_url( $plugin_info['url'] ),
				esc_html( $plugin_info['name'] )
			);
		}
	}

	if ( ! empty( $inactive_plugins ) ) {
		$plugin_list = implode( ', ', $inactive_plugins );

		echo '<div class="notice notice-error"><p>';

		printf(
			/* translators: %s is a list of plugin names with links */
			esc_html__( 'The following plugins are required by this theme: %s. Please install and activate them.', 'st-starter' ),
			wp_kses_post( $plugin_list )
		);

		echo '</p></div>';
	}
}

add_action( 'admin_notices', 'st_starter_plugin_dependencies' );


/**
 * Theme plugin suggestions notice
 *  List of suggested plugins and links to download them
 *
 * @return void
 */
function st_starter_plugins_suggestions(): void {
	if ( get_user_meta( get_current_user_id(), 'st_starter_ignore_plugin_notice', true ) ) {
		return; // User has dismissed the notice.
	}

	// List of suggested plugins and their download links.
	$required_plugins = array(
		'index-wp-mysql-for-speed/index-wp-mysql-for-speed.php' => array(
			'name' => 'Index WP MySQL For Speed',
			'url'  => 'https://wordpress.org/plugins/index-wp-mysql-for-speed/',
		),
		'redis-cache/redis-cache.php'                     => array(
			'name' => 'Redis Object Cache',
			'url'  => 'https://wordpress.org/plugins/redis-cache/',
		),
		'regenerate-thumbnails/regenerate-thumbnails.php' => array(
			'name' => 'Regenerate Thumbnails',
			'url'  => 'https://wordpress.org/plugins/regenerate-thumbnails/',
		),
		'svg-support/svg-support.php'                     => array(
			'name' => 'SVG Support',
			'url'  => 'https://wordpress.org/plugins/svg-support/',
		),
		'webp-converter-for-media'                        => array(
			'name' => 'Converter for Media',
			'url'  => 'https://wordpress.org/plugins/webp-converter-for-media/',
		),
	);

	$inactive_plugins = array();

	foreach ( $required_plugins as $plugin_path => $plugin_info ) {
		if ( ! is_plugin_active( $plugin_path ) ) {
			$inactive_plugins[] = sprintf(
				'<strong><a href="%s" target="_blank">%s</a></strong>',
				esc_url( $plugin_info['url'] ),
				esc_html( $plugin_info['name'] )
			);
		}
	}

	if ( ! empty( $inactive_plugins ) ) {
		echo '<div class="notice notice-warning is-dismissible st-plugin-notice"><p>';
		echo esc_html__( 'The following plugins are advisable to use with this theme:', 'st-starter' );
		echo '</p><ul>';

		foreach ( $inactive_plugins as $plugin_link ) {
			echo '<li>' . wp_kses_post( $plugin_link ) . '</li>';
		}

		echo '</ul>';
		echo '<p>' . esc_html__( 'Please install and activate them.', 'st-starter' ) . '</p>';
		echo '<p><a href="#" class="st-dismiss-plugin-notice">' . esc_html__( 'Don\'t remind me', 'st-starter' ) . '</a></p>';
		echo '</div>';
	}
}

add_action( 'admin_notices', 'st_starter_plugins_suggestions' );

/**
 * Handle AJAX request to dismiss notice.
 */
function st_starter_dismiss_plugin_notice(): void {
	check_ajax_referer( 'st_starter_ignore_plugin_notice', 'nonce' );

	update_user_meta( get_current_user_id(), 'st_starter_ignore_plugin_notice', 1 );

	wp_send_json_success();
}
add_action( 'wp_ajax_st_starter_dismiss_plugin_notice', 'st_starter_dismiss_plugin_notice' );


/**
 *  Sets up theme defaults and registers support for various WordPress features.
 *
 *  Note that this function is hooked into the after_setup_theme hook, which
 *  runs before the init hook. The init hook is too late for some features, such
 *  as indicating support for post thumbnails.
 *
 * @return void
 */
function st_starter_setup(): void {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on ST Starter, use a find and replace
	 * to change 'st-starter' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'st-starter', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'st-starter' ),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}

add_action( 'after_setup_theme', 'st_starter_setup' );


/**
 *  Set the content width in pixels, based on the theme's design and stylesheet.
 *
 *  Priority 0 to make it available to lower priority callbacks.
 *
 * @return void
 * @global int $content_width
 */
function st_starter_content_width(): void {
	$GLOBALS['content_width'] = apply_filters( 'st_starter_content_width', 640 );
}

add_action( 'after_setup_theme', 'st_starter_content_width', 0 );


/**
 * Theme Styles & Scripts
 *  Register Widget areas
 */
require get_template_directory() . '/inc/scripts.php';


/**
 * Function responsible for generating a clean file name
 *  - used by st_register_and_enqueue_assets()
 *  - used by st_register_plugin_assets_admin()
 *
 * @param string $relative_path The relative file path to be cleaned.
 * @return string The sanitized file name.
 */
function st_generate_asset_handle( string $relative_path ): string {
	$handle = str_replace( array( '/', '\\' ), '.', $relative_path ); // Convert path separators to dots.

	$handle = preg_replace( '/\.min/', '', $handle ); // Remove ".min" from filename.

	$handle = preg_replace( '/\.(css|js)$/', '', $handle ); // Remove ".css" or ".js" extension.

	return trim( $handle, '.' );// Remove any leading/trailing dots.
}


if ( ! function_exists( 'st_register_and_enqueue_assets' ) ) {
	/**
	 * Function will enqueue or register theme styles & scripts
	 *
	 *  1. will look for files in
	 *      - `\assets\css\styles-enqueue\`
	 *      - `\assets\css\styles-register\`
	 *      - `\assets\js\scripts-enqueue\`
	 *      - `\assets\js\scripts-register\`
	 *
	 *  2. will add handle to files based on their directory.
	 *      the filename will be a dot-notation of the file's directory and the file name. eg:
	 *      - source: `\assets\css\styles-enqueue\main.min.css`
	 *      - handle: `main`
	 *      - source: `\assets\css\styles-enqueue\plugins\splidejs\core.min.css`
	 *      - handle: `plugins.splidejs.core`
	 *      - source: `\assets\js\scripts-register\pages\archive.min.js`
	 *      - handle: `pages.archive`
	 *
	 *  3. will add the file timestamp as a file version
	 *
	 * @return void
	 */
	function st_register_and_enqueue_assets(): void {
		// Use get_stylesheet_directory() for child themes.
		$base_dir = get_template_directory();

		$assets = array(
			'css' => array(
				'register' => '/assets/css/styles-register/',
				'enqueue'  => '/assets/css/styles-enqueue/',
			),
			'js'  => array(
				'register' => '/assets/js/scripts-register/',
				'enqueue'  => '/assets/js/scripts-enqueue/',
			),
		);

		foreach ( $assets as $type => $dirs ) {
			foreach ( $dirs as $action => $dir ) {
				$path = $base_dir . $dir;
				if ( ! is_dir( $path ) ) {
					continue;
				}

				$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) );

				foreach ( $files as $file ) {
					if ( $file->isFile() && pathinfo( $file, PATHINFO_EXTENSION ) === $type ) {
						$relative_path = str_replace( $path, '', $file->getPathname() );
						$handle        = st_generate_asset_handle( $relative_path );
						$asset_url     = get_template_directory_uri() . $dir . $relative_path;
						$file_version  = filemtime( $file->getPathname() );

						if ( 'css' === $type ) {
							if ( 'register' === $action ) {
								wp_register_style( $handle, $asset_url, array(), $file_version );
							} else {
								wp_enqueue_style( $handle, $asset_url, array(), $file_version );
							}
						} elseif ( 'js' === $type ) {
							if ( 'register' === $action ) {
								wp_register_script( $handle, $asset_url, array(), $file_version, true );
							} else {
								wp_enqueue_script( $handle, $asset_url, array(), $file_version, true );
							}
						}
					}
				}
			}
		}
	}

	// Hook into WordPress.
	add_action( 'wp_enqueue_scripts', 'st_register_and_enqueue_assets' );
}


if ( ! function_exists( 'st_register_plugin_assets_admin' ) ) {
	/**
	 * Same logic, but for Admin Dashboard
	 *
	 *  1. will look for files in
	 *      - `\assets\css\styles-register\plugins\`
	 *      - `\assets\js\scripts-register\plugins\`
	 *
	 *  2. will add handle to files based on their directory.
	 *      the filename will be a dot-notation of the file's directory and the file name. eg:
	 *      - source: `\assets\css\styles-register\plugins\splidejs\core.min.css`
	 *      - handle: `plugins.splidejs.core`
	 *      - source: `\assets\js\scripts-register\plugins\splidejs\core.min.css`
	 *      - handle: `plugins.splidejs.core`
	 *
	 *  3. will add the file timestamp as a file version
	 *
	 * @return void
	 */
	function st_register_plugin_assets_admin(): void {
		// Use get_stylesheet_directory() for child themes.
		$base_dir = get_template_directory();

		$assets = array(
			'css' => '/assets/css/styles-register/',
			'js'  => '/assets/js/scripts-register/',
		);

		foreach ( $assets as $type => $dir ) {
			$path = $base_dir . $dir;
			if ( ! is_dir( $path ) ) {
				continue;
			}

			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path ) );

			foreach ( $files as $file ) {
				if ( $file->isFile() && pathinfo( $file, PATHINFO_EXTENSION ) === $type ) {
					$relative_path = str_replace( $path, '', $file->getPathname() );

					// Check if the file is in the "plugins" subdirectory.
					if ( ! str_contains( $relative_path, 'plugins/' ) ) {
						continue;
					}

					$handle       = st_generate_asset_handle( $relative_path );
					$asset_url    = get_template_directory_uri() . $dir . $relative_path;
					$file_version = filemtime( $file->getPathname() );

					if ( 'css' === $type ) {
						wp_register_style( $handle, $asset_url, array(), $file_version );
					} elseif ( 'js' === $type ) {
						wp_register_script( $handle, $asset_url, array(), $file_version, true );
					}
				}
			}
		}
	}

	// Hook into WordPress.
	add_action( 'admin_enqueue_scripts', 'st_register_plugin_assets_admin' );
}


if ( ! function_exists( 'st_classes_autoloader' ) ) {
	/**
	 * Autoloader for files in the classes directory.
	 * Only Classes should be added here. No executables.
	 *
	 * @param string $class_name File & Class Name.
	 * @return void
	 */
	function st_classes_autoloader( string $class_name ): void {
		// Convert class name to lowercase with hyphens.
		// Example: ST_Walker_Nav_Menu -> class-st-walker-nav-menu.php.
		$file_name = 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';

		// Build the full path to the file.
		$file = __DIR__ . '/classes/' . $file_name;

		// Check if the file exists and include it.
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}

	// Register the autoloader.
	spl_autoload_register( 'st_classes_autoloader' );
}


if ( function_exists( 'acf_add_options_page' )
	&& ! function_exists( 'my_acf_op_init' ) ) {
	/**
	 * Theme General Settings
	 * Supported by ACF
	 *
	 * @return void
	 */
	function my_acf_op_init(): void {
		acf_add_options_page(
			array(
				'page_title' => __( 'Theme General Settings' ),
				'menu_title' => __( 'Theme Settings' ),
				'post_id'    => 'options',
				'redirect'   => false,
			)
		);
	}

	add_action( 'acf/init', 'my_acf_op_init' );
}


if ( ! function_exists( 'st_acf_save_json_path' ) ) {
	/**
	 * Save acf-json to the stylesheet directory.
	 *
	 * @return string
	 * @link https://support.advancedcustomfields.com/forums/topic/acf-json-fields-not-loading-from-parent-theme/
	 * @since 1.0.0
	 */
	function st_acf_save_json_path(): string {
		return get_stylesheet_directory() . '/acf-json';
	}

	add_filter( 'acf/settings/save_json', 'st_acf_save_json_path' );
}


if ( ! function_exists( 'st_acf_load_json_paths' ) ) {
	/**
	 * Load acf-json from parent theme and child theme, if available.
	 *
	 * @param array $paths Array of acf-json paths.
	 *
	 * @return array
	 * @link https://support.advancedcustomfields.com/forums/topic/acf-json-fields-not-loading-from-parent-theme/
	 * @since 1.0.0
	 */
	function st_acf_load_json_paths( array $paths ): array {
		$paths = array( get_template_directory() . '/acf-json' );

		if ( is_child_theme() ) {
			$paths[] = get_stylesheet_directory() . '/acf-json';
		}

		return $paths;
	}

	add_filter( 'acf/settings/load_json', 'st_acf_load_json_paths' );
}


if ( function_exists( 'acf_register_block_type' )
	&& ! function_exists( 'st_block_register' ) ) {
	/**
	 * Setup acf gutenberg blocks
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function st_block_register(): void {
		$blocks = glob( get_stylesheet_directory() . '/blocks/*' );
		foreach ( $blocks as $block ) {
			if ( is_dir( $block ) ) {
				register_block_type( $block );
			}
		}
	}

	add_action( 'acf/init', 'st_block_register' );
}


if ( ! function_exists( 'st_add_theme_block_categories' ) ) {
	/**
	 * Register new Gutenberg blocks category
	 *
	 * @param array $categories Current list of categories.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function st_add_theme_block_categories( array $categories ): array {
		$custom_category_one = array(
			'slug'  => 'st-starter',
			'title' => __( 'ST Starter Sections', 'st-starter' ),
			'icon'  => 'admin-appearance',
		);
		array_unshift( $categories, $custom_category_one );

		return $categories;
	}

	add_filter( 'block_categories_all', 'st_add_theme_block_categories', 10, 2 );
}
