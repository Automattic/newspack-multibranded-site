<?php
/**
 * Plugin Name: Newspack Multibranded Site (final version, please migrate)
 * Description: Final version released from the legacy plugin repository. This copy will not receive further updates. Download the current version at https://newspack.com/download-center
 * Version: 2.2.0
 * Author: Automattic
 * Author URI: https://newspack.com/
 * License: GPL3
 * Text Domain: newspack-multibranded-site
 * Domain Path: /languages/
 *
 * @package newspack-multibranded-site
 */

defined( 'ABSPATH' ) || exit;

// Define NEWSPACK_MULTIBRANDED_SITE_PLUGIN_DIR.
if ( ! defined( 'NEWSPACK_MULTIBRANDED_SITE_PLUGIN_DIR' ) ) {
	define( 'NEWSPACK_MULTIBRANDED_SITE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Define NEWSPACK_MULTIBRANDED_SITE_PLUGIN_FILE.
if ( ! defined( 'NEWSPACK_MULTIBRANDED_SITE_PLUGIN_FILE' ) ) {
	define( 'NEWSPACK_MULTIBRANDED_SITE_PLUGIN_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

Newspack_Multibranded_Site\Initializer::init();

// Load language files.
add_action(
	'init',
	function () {
		load_plugin_textdomain( 'newspack-multibranded-site', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
);

add_action(
	'plugins_loaded',
	function () {
		if ( class_exists( 'Newspack_Manager\\Updater' ) ) {
			new Newspack_Manager\Updater(
				'newspack-multibranded-site/newspack-multibranded-site.php',
				NEWSPACK_MULTIBRANDED_SITE_PLUGIN_FILE,
				'Automattic/newspack-multibranded-site'
			);
		}
	}
);

/**
 * Warn administrators that this build came from the legacy plugin repository.
 */
function newspack_multibranded_site_legacy_repo_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p><strong><?php esc_html_e( 'You are running an outdated version of the Newspack Multibranded Site plugin.', 'newspack-multibranded-site' ); ?></strong></p>
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: 1: URL of the announcement post. 2: URL of the download center. */
					__( 'This is the final version released from the legacy plugin repository, and it will not receive further updates. <a href="%1$s">Read the announcement</a>, then download the current version from the <a href="%2$s">Newspack download center</a>.', 'newspack-multibranded-site' ),
					[
						'a' => [
							'href' => [],
						],
					]
				),
				esc_url( 'https://newspack.com/newspack-plugins-and-themes-have-a-new-home/' ),
				esc_url( 'https://newspack.com/download-center' )
			);
			?>
		</p>
	</div>
	<?php
}

/*
 * Newspack wizard screens call remove_all_actions() on the notice hooks at priority -9999,
 * so this notice runs ahead of that to stay visible on every admin screen.
 */
add_action( 'all_admin_notices', 'newspack_multibranded_site_legacy_repo_notice', -99999 );
