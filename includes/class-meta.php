<?php
/**
 * Newspack Multi-branded site term meta parent class.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site;

/**
 * Newspack Multi-branded site term meta parent class
 */
abstract class Meta {

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
			'object_subtype' => Taxonomy::SLUG,
			'description'    => static::get_description(),
			'single'         => true,
			'show_in_rest'   => [
				'schema' => static::get_schema(),
			],
			'auth_callback'  => function() {
				return current_user_can( 'manage_options' );
			},
		];

		register_meta(
			'term',
			static::get_key(),
			$params
		);
	}

	/**
	 * Gets the meta key
	 *
	 * @return string
	 */
	abstract public static function get_key();

	/**
	 * Gets the meta description
	 *
	 * @return string
	 */
	abstract public static function get_description();

	/**
	 * Gets the meta schema
	 *
	 * @return array
	 */
	abstract public static function get_schema();

}
