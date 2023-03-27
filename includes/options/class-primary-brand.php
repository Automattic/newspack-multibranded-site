<?php
/**
 * Newspack Multi-branded site term meta parent class.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site\Options;

use Newspack_Multibranded_Site\Taxonomy;

/**
 * Newspack Multi-branded site term meta parent class
 */
abstract class Primary_Brand {

	/**
	 * Initializes the Meta
	 */
	public static function init() {
		self::register_option();
	}

	/**
	 * Register the tem meta
	 *
	 * @return void
	 */
	public static function register_option() {
		$params = [
			'description'  => static::get_description(),
			'show_in_rest' => [
				'schema' => static::get_schema(),
			],
		];

		register_setting(
			Taxonomy::SLUG,
			static::get_key(),
			$params
		);
	}

	/**
	 * Gets the meta key
	 *
	 * @return string
	 */
	public static function get_key() {
		return Taxonomy::PRIMARY_OPTION_NAME;
	}

	/**
	 * Gets the meta description
	 *
	 * @return string
	 */
	public static function get_description() {
		return __( 'The primary brand for the site', 'newspack-multibranded-site' );
	}

	/**
	 * Gets the meta schema
	 *
	 * @return array
	 */
	public static function get_schema() {
		return [
			'type' => 'integer',
		];
	}

}
