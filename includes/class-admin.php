<?php
/**
 * Newspack Multi-branded site plugin administration screen handling.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site;

/**
 * Class to handle the plugin admin pages
 */
class Admin {
	const MULTI_BRANDED_PAGE_SLUG = 'newspack-multi-branded-sites';

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
	}

	/**
	 * Adds the admin page
	 *
	 * @return void
	 */
	public static function add_admin_menu() {
		$page_suffix = add_menu_page(
			__( 'Multi-branded site', 'newspack-multibranded-site' ),
			__( 'Multi-branded site', 'newspack-multibranded-site' ),
			'manage_options',
			self::MULTI_BRANDED_PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);

		add_action( 'load-' . $page_suffix, array( __CLASS__, 'admin_init' ) );
	}

	/**
	 * Renders the page content
	 *
	 * @return void
	 */
	public static function render_page() {
		echo '<div id="root"></div>';
	}

	/**
	 * Callback for the load admin page hook.
	 *
	 * @return void
	 */
	public static function admin_init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue admin page assets.
	 *
	 * @param string $handler Page handler.
	 *
	 * @return void
	 */
	public static function enqueue_scripts( $handler ) {
		if ( false === strpos( $handler, self::MULTI_BRANDED_PAGE_SLUG ) ) {
			return;
		};

		\wp_register_script(
			self::MULTI_BRANDED_PAGE_SLUG,
			plugins_url( '../dist/admin.js', __FILE__ ),
			array( 'wp-components', 'wp-api-fetch' ),
			filemtime( NEWSPACK_MULTIBRANDED_SITE_PLUGIN_DIR . 'dist/admin.js' ),
			true
		);
		\wp_enqueue_script( self::MULTI_BRANDED_PAGE_SLUG );

		\wp_register_style(
			self::MULTI_BRANDED_PAGE_SLUG,
			plugins_url( '../dist/admin.css', __FILE__ ),
			array( 'wp-components' ),
			filemtime( NEWSPACK_MULTIBRANDED_SITE_PLUGIN_DIR . 'dist/admin.css' )
		);
		\wp_style_add_data( self::MULTI_BRANDED_PAGE_SLUG, 'rtl', 'replace' );
		\wp_enqueue_style( self::MULTI_BRANDED_PAGE_SLUG );

		\wp_enqueue_style( // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			'tachyons',
			'https://unpkg.com/tachyons@4.12.0/css/tachyons.min.css'
		);
	}

}
