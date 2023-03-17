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
			'multi-branded-sites',
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
		echo 'Hello World';
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
	 * @TODO Implement it.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {}

}
