<?php
/**
 * ST Starter scripts functions
 *
 * Enqueue / Register scripts and styles
 *
 * @package ST_Starter
 */

/**
 * Enqueue Stylesheets / Scripts
 *
 * @return void
 */
function st_starter_scripts(): void {
	// Theme Version Stylesheet.
	wp_enqueue_style( 'st-starter-style', get_stylesheet_uri(), array(), ST_STARTER_VERSION );

	// Theme Main Stylesheet.
	wp_enqueue_style( 'st-starter-main', get_template_directory_uri() . '/assets/css/main.min.css', array(), filemtime( get_template_directory() . '/assets/css/main.min.css' ) );

	// Theme Main Script.
	wp_enqueue_script( 'st-starter-main', get_template_directory_uri() . '/assets/js/main.min.js', array(), filemtime( get_template_directory() . '/assets/js/main.min.js' ), true );

	// Comments.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Load Custom Scripts / Styles Here.
}

add_action( 'wp_enqueue_scripts', 'st_starter_scripts' );


/**
 * Theme Dashboard Stylesheet
 *
 * @param string $hook // caller hook string.
 *
 * @return void
 */
function st_starter_admin_scripts( string $hook ): void {
	/*
	 * ──────────────────────────────────────────────────
	 * 1. Load the media‑modal files once.
	 *    Only required on nav‑menus.php, so guard it:
	 * ──────────────────────────────────────────────────
	 */
	if ( 'nav-menus.php' === $hook ) {
		wp_enqueue_media(); // adds  wp.media + styles.
		$script_deps = array( 'jquery', 'media-editor' );
	} else {
		$script_deps = array( 'jquery' );
	}

	// Main Admin Stylesheet. Runs *after* media‑modal JS.
	wp_enqueue_style(
		'admin-styles',
		get_template_directory_uri() . '/assets/css/admin.min.css',
		array(),
		filemtime( get_template_directory() . '/assets/css/admin.min.css' ),
		false
	);

	// Main Admin Scripts.
	wp_enqueue_script(
		'admin-scripts',
		get_template_directory_uri() . '/assets/js/admin.min.js',
		array( 'jquery' ),
		filemtime( get_template_directory() . '/assets/js/admin.min.js' ),
		true
	);

	// pass data to Main Admin Scripts.
	wp_localize_script(
		'admin-scripts',
		'st_starter_plugin_notice',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'st_starter_ignore_plugin_notice' ),
		)
	);
}

add_action( 'admin_enqueue_scripts', 'st_starter_admin_scripts' );


/**
 * Block Styles - Loading Enhancement
 *  Only Load Styles for used blocks
 *  since v5.8
 *
 * Ref_01: https://stackoverflow.com/a/76836510/22644768
 * Ref_02: https://make.wordpress.org/core/2021/07/01/block-styles-loading-enhancements-in-wordpress-5-8/
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
