<?php
/**
 * Newspack Multibranded site taxonomy.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site\Meta;

use Newspack_Multibranded_Site\Meta;
use Newspack_Multibranded_Site\Taxonomy;

/**
 * Class to handle the Tag Primary brand Meta
 */
class Tag_Primary_Brand extends Meta {

	/**
	 * Get the taxonomy to register the meta to, if meta type is term
	 *
	 * @return string
	 */
	public static function get_taxonomy() {
		return 'post_tag';
	}

	/**
	 * Gets the meta key
	 *
	 * @return string
	 */
	public static function get_key() {
		return Taxonomy::PRIMARY_META_KEY;
	}

	/**
	 * Gets the meta description
	 *
	 * @return string
	 */
	public static function get_description() {
		return __( 'The primary brand for a tag', 'newspack-multibranded-site' );
	}

	/**
	 * Gets the meta schema
	 *
	 * @return array
	 */
	public static function get_schema() {
		return array(
			'type'     => 'integer',
			'nullable' => true,
		);
	}
}
