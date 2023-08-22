<?php
/**
 * Newspack Multi-branded site dependencies check.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site;

/**
 * Class to check dependencies for the plugin
 */
class Dependencies {

	/**
	 * Check if all dependencies are present
	 *
	 * @return boolean
	 */
	public static function has_dependencies() {
		return class_exists( '\Newspack\Newspack' );
	}

	/**
	 * Adds a notice to let admins know that dependencies are not met.
	 */
	public static function add_notice() {
		add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
	}

	/**
	 * Displays the admin notice
	 *
	 * @return void
	 */
	public static function admin_notice() {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Multi-branded site requires the Newspack plugin.', 'newspack-multibranded-site' ); ?></p>
		</div>
		<?php
	}

}
