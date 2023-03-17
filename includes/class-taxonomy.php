<?php
/**
 * Newspack Multi-branded site taxonomy.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site;

/**
 * Class to handle the brands taxonomy
 */
class Taxonomy {

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );
	}

	/**
	 * Registers the taxonomy
	 *
	 * @return void
	 */
	public static function register_taxonomy() {
		$labels = array(
			'name'              => _x( 'Brands', 'taxonomy general name', 'newspack-multibranded-site' ),
			'singular_name'     => _x( 'Brand', 'taxonomy singular name', 'newspack-multibranded-site' ),
			'search_items'      => __( 'Search Brands', 'newspack-multibranded-site' ),
			'all_items'         => __( 'All Brands', 'newspack-multibranded-site' ),
			'parent_item'       => __( 'Parent Brand', 'newspack-multibranded-site' ),
			'parent_item_colon' => __( 'Parent Brand:', 'newspack-multibranded-site' ),
			'edit_item'         => __( 'Edit Brand', 'newspack-multibranded-site' ),
			'update_item'       => __( 'Update Brand', 'newspack-multibranded-site' ),
			'add_new_item'      => __( 'Add New Brand', 'newspack-multibranded-site' ),
			'new_item_name'     => __( 'New Brand Name', 'newspack-multibranded-site' ),
			'menu_name'         => __( 'Brands', 'newspack-multibranded-site' ),
		);
		$params = array(
			'labels'             => $labels,
			'hierarchical'       => false,
			'publicly_queryable' => false,
			'show_in_nav_menus'  => false,
			'show_ui'            => false,
			'show_admin_column'  => false,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'brand' ),
		);
		register_taxonomy( 'brand', array( 'post', 'page' ), $params );
	}

}
