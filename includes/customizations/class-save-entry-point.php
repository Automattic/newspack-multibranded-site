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
	public static function save_entry_point( $brand ) {
		if ( ! isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			error_log( 'COOKIE NOT SET' );
			if ( is_null( $brand ) ) {
				error_log( 'BRAND IS NULL' );
				$brand = 'none';
			}
			if ( $brand instanceof \WP_Term ) {
				$brand = $brand->term_id;
			}
			error_log( 'SETTING COOKIE  as ' . $brand );
			setcookie( self::COOKIE_NAME, $brand );
		}
		error_log( 'COOKIE ALREADY SET' );
	}

	public static function get_entry_point( $brand ) {
		if ( ! is_null( $brand ) ) {
			error_log( 'BRAND NOT NULL, DONT READ COOKIE' );
			return $brand;
		}

		if ( isset( $_COOKIE[ self::COOKIE_NAME ] ) && 'none' !== $_COOKIE[ self::COOKIE_NAME ] ) {
			error_log( 'BRAND IS NULL, READ COOKIE' );
			$brand_id = $_COOKIE[ self::COOKIE_NAME ];
			error_log( 'BRAND ID IS ' . $brand_id );
			$entry_point = get_term( $brand_id, Taxonomy::SLUG );
			if ( $entry_point instanceof \WP_Term ) {
				return $entry_point;
			}
		}
		error_log( 'BRAND IS NULL, NO COOKIE' );
		return $brand;
	}

}
