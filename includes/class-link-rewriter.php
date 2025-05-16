<?php
/**
 * Newspack Multibranded Site Link Rewriter.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site;

defined( 'ABSPATH' ) || exit;

/**
 * Class to handle frontend link rewriting.
 */
class Link_Rewriter {

	/**
	 * Initialize hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue the frontend script.
	 */
	public static function enqueue_scripts() {
		// Only enqueue if we're on the frontend and not in the admin.
		if ( is_admin() ) {
			return;
		}

		// Enqueue WordPress dependencies.
		wp_enqueue_script( 'wp-dom-ready' );
		wp_enqueue_script( 'wp-url' );

		wp_enqueue_script(
			'newspack-link-rewriter',
			plugins_url( '../dist/linkRewriter.js', __FILE__ ),
			[ 'wp-dom-ready', 'wp-url' ],
			filemtime( NEWSPACK_MULTIBRANDED_SITE_PLUGIN_DIR . '/dist/linkRewriter.js' ),
			true
		);
	}
}
