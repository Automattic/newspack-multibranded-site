<?php
/**
 * Newspack Multi-branded site taxonomy.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site\Customizations;

use Newspack_Multibranded_Site\Taxonomy;

/**
 * Class to handle the Logo Customization
 */
class Save_Entry_Point {

	const COOKIE_NAME = 'newspack_multibranded_site_entry_point';

	/**
	 * Initializes
	 */
	public static function init() {
		add_action( 'newspack_multibranded_site_current_brand_changed', [ __CLASS__, 'save_entry_point' ] );
		add_filter( 'newspack_multibranded_site_current_brand', [ __CLASS__, 'get_entry_point' ] );
	}

	/**
	 * Filters the logo
	 *
	 * @param int $logo_id The logo ID.
	 * @return int
	 */
	public static function save_entry_point( $brand_id ) {
		if ( ! isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			if ( is_null( $brand_id ) ) {
				$brand_id = 'none';
			}
			setcookie( self::COOKIE_NAME, $brand_id );
		}
	}

	public static function get_entry_point( $brand ) {
		if ( ! is_null( $brand ) ) {
			return $brand;
		}

		if ( isset( $_COOKIE[ self::COOKIE_NAME ] ) && 'none' !== $_COOKIE[ self::COOKIE_NAME ] ) {
			$brand_id    = $_COOKIE[ self::COOKIE_NAME ];
			$entry_point = get_term( $brand_id, Taxonomy::SLUG );
			if ( $entry_point instanceof \WP_Term ) {
				return $entry_point;
			}
		}
		return $brand;
	}

}
