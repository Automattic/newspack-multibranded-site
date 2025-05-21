<?php
/**
 * Newspack Multibranded site taxonomy.
 *
 * @package Newspack
 */

namespace Newspack_Multibranded_Site;

use Newspack_Multibranded_Site\Customizations\Show_Page_On_Front;
use WP_Term;

/**
 * Class to handle the brands taxonomy
 *
 * Register the Brands taxonomy and handles the request to determine in which brand we are currently in.
 *
 * On the `wp` hook, after the query is performed, we check what's the current brand and store it in the static $current_brand property.
 * We can't do it earlier in parse_query because we need the queried object to check on it.
 *
 * Later on the request, we use the $current_brand property (through get_current_brand method) to determine the current brand.
 */
class Taxonomy {

	/**
	 * The taxonomy slug.
	 *
	 * @var string
	 */
	const SLUG = 'brand';

	/**
	 * The post types to which the taxonomy should be applied.
	 *
	 * @var array
	 */
	const POST_TYPES = array( 'post', 'page' );

	/**
	 * The meta key used to flag the primary brand.
	 *
	 * @var string
	 */
	const PRIMARY_META_KEY = '_primary_brand';

	/**
	 * The query string parameter used for brand overrides.
	 *
	 * @var string
	 */
	const BRAND_QUERY_PARAM = 'brand';

	/**
	 * The current brand, determined depending on the context on WP initiazliation.
	 *
	 * @var ?WP_Term
	 */
	private static $current_brand;

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		add_action( 'init', [ __CLASS__, 'register_taxonomy' ] );
		add_action( 'wp', [ __CLASS__, 'determine_current_brand' ] );
	}

	/**
	 * Get the current brand, depending on the context
	 *
	 * @return ?WP_Term The current brand term.
	 */
	public static function get_current() {
		return self::$current_brand;
	}

	/**
	 * Get the list of post types that the taxonomy should be applied to.
	 *
	 * @return array The list of post type slugs.
	 */
	public static function get_post_types() {
		$post_types = self::POST_TYPES;
		if ( class_exists( 'Newspack_Popups' ) ) {
			$post_types[] = \Newspack_Popups::NEWSPACK_POPUPS_CPT;
		}
		return $post_types;
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
			'hierarchical'       => true, // Just to get the checkbox UI.
			'publicly_queryable' => true,
			'show_in_nav_menus'  => true,
			'show_in_menu'       => false,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'capabilities'       => array(
				'manage_terms' => 'manage_options',
				'edit_terms'   => 'manage_options',
				'delete_terms' => 'manage_options',
				'assign_terms' => 'edit_posts',
			),
		);
		register_taxonomy( self::SLUG, self::get_post_types(), $params );

		// Initialize metadata.
		Meta\Url::init();
		Meta\Show_Page_On_Front::init();
		Meta\Post_Primary_Brand::init();
		Meta\Logo::init();
		Meta\Theme_Colors::init();
		Meta\Menus::init();
		Meta\User_Primary_Brand::init();
		Meta\Tag_Primary_Brand::init();
		Meta\Category_Primary_Brand::init();
	}

	/**
	 * Get the current brand based on a post.
	 *
	 * If a post has is of a supported post type and has only one brand, it will return this brand, otherwise it will return null.
	 *
	 * @param int|WP_Post $post_or_post_id The Post object or the post id.
	 * @param ?WP_Term    $brand_override  Optional brand to override with, if it's one of the post's brands.
	 * @return ?WP_Term The current brand for the post.
	 */
	public static function get_current_brand_for_post( $post_or_post_id, $brand_override = null ) {
		// Account for Brands with page on front.
		if ( $post_or_post_id instanceof WP_Term ) {
			return self::get_current_brand_for_term( $post_or_post_id, $brand_override );
		}

		$post = $post_or_post_id instanceof \WP_Post ? $post_or_post_id : get_post( $post_or_post_id );

		if ( ! in_array( $post->post_type, self::POST_TYPES, true ) ) {
			return;
		}

		// Get all brands for the post.
		$terms = wp_get_post_terms( $post->ID, self::SLUG );

		// If post has only one brand, always respect that brand.
		if ( 1 === count( $terms ) ) {
			return $terms[0];
		}

		// If post has multiple brands, handle brand override and primary brand.
		if ( count( $terms ) > 1 ) {
			// If we have a brand override and it's one of the post's brands, use it.
			if ( $brand_override ) {
				foreach ( $terms as $term ) {
					if ( $term->term_id === $brand_override->term_id ) {
						return $brand_override;
					}
				}
			}

			// Check if post has a primary brand.
			$post_primary_brand = get_post_meta( $post->ID, self::PRIMARY_META_KEY, true );

			if ( $post_primary_brand ) {
				$term = get_term( $post_primary_brand, self::SLUG );
				if ( $term instanceof WP_Term ) {
					return $term;
				}
			}

			// Multiple brands without primary or valid override.
			return;
		}

		// Check if post is a cover page for a brand.
		if ( 'page' === $post->post_type ) {
			$brand = Show_Page_On_Front::get_brand_page_is_cover_for( $post->ID );
			if ( $brand ) {
				$term = get_term( $brand, self::SLUG );
				if ( $term instanceof WP_Term ) {
					return $term;
				}
			}
		}

		// Check if post is assigned to a brand through a category.
		$categories     = wp_get_post_categories( $post->ID );
		$category_brand = null;
		foreach ( $categories as $category ) {
			// Only pass the override if the post has brands.
			$brand = self::get_current_brand_for_term( $category, count( $terms ) ? $brand_override : null );
			if ( $brand ) {
				if ( ! $category_brand || $category_brand->term_id === $brand->term_id ) {
					$category_brand = $brand;
					continue;
				}

				// Found more than one eligible brand, return null.
				return null;
			}
		}

		return $category_brand;
	}

	/**
	 * Get the current brand based on a term.
	 *
	 * If a term is a brand, it will return this brand.
	 *
	 * @param int|WP_Term $term_or_term_id The Term object or the term id.
	 * @param ?WP_Term    $brand_override  Optional brand to override with.
	 * @return ?WP_Term The current brand for the term.
	 */
	public static function get_current_brand_for_term( $term_or_term_id, $brand_override = null ) {
		$term = $term_or_term_id instanceof WP_Term ? $term_or_term_id : get_term( $term_or_term_id );

		if ( self::SLUG === $term->taxonomy ) {
			return $term;
		}

		if ( in_array( $term->taxonomy, [ 'category', 'post_tag' ], true ) ) {
			return self::recursive_search_term_primary_brand( $term, $brand_override );
		}
	}

	/**
	 * Finds the primary brand for a term, searching recursively through ancestors.
	 *
	 * @param WP_Term  $term           The Term.
	 * @param ?WP_Term $brand_override Optional brand to override with.
	 * @return ?WP_Term The primary brand for the term.
	 */
	protected static function recursive_search_term_primary_brand( WP_Term $term, $brand_override = null ) {
		$primary_brand = get_term_meta( $term->term_id, self::PRIMARY_META_KEY, true );
		if ( $primary_brand ) {
			$brand = get_term( $primary_brand, self::SLUG );
			return ( $brand instanceof WP_Term ) ? $brand : null;
		}

		if ( $term->parent ) {
			$parent = get_term( $term->parent, $term->taxonomy );
			if ( $parent instanceof WP_Term ) {
				$result = self::recursive_search_term_primary_brand( $parent, $brand_override );
				if ( $result ) {
					return $result;
				}
			}
		}

		return $brand_override;
	}

	/**
	 * Get the current brand based on an author.
	 *
	 * If the author has a primary brand, it will return this brand.
	 *
	 * @param int      $author_id      The author ID.
	 * @param ?WP_Term $brand_override Optional brand to override with.
	 * @return ?WP_Term The current brand for the author.
	 */
	public static function get_current_brand_for_author( $author_id, $brand_override = null ) {
		$author_brand = get_user_meta( $author_id, self::PRIMARY_META_KEY, true );

		if ( $author_brand ) {
			$brand = get_term( $author_brand, self::SLUG );
			return ( $brand instanceof WP_Term ) ? $brand : null;
		}

		return $brand_override;
	}

	/**
	 * Get brand override from URL query parameter if present and valid.
	 *
	 * @return \WP_Term|null The brand term if a valid brand override is found in the URL, null otherwise.
	 */
	private static function get_brand_override() {
		if ( ! isset( $_GET[ self::BRAND_QUERY_PARAM ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return null;
		}

		$brand_slug = sanitize_text_field( wp_unslash( $_GET[ self::BRAND_QUERY_PARAM ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$term       = get_term_by( 'slug', $brand_slug, self::SLUG );

		/**
		 * Filters the brand override term retrieved from the URL query parameter.
		 *
		 * Allows custom logic to modify or replace the brand override term before it's used
		 * in brand resolution logic. Return null to disable the override.
		 *
		 * @param ?\WP_Term $brand_override The brand term retrieved by slug from the URL, or null if not valid.
		 */
		return apply_filters( 'newspack_brand_override', $term instanceof \WP_Term ? $term : null );
	}

	/**
	 * Determines and stores the current brand depending on the current context.
	 *
	 * @return void
	 */
	public static function determine_current_brand() {
		self::$current_brand = null;

		if ( is_front_page() ) {
			return;
		}

		$brand_override = self::get_brand_override();

		if ( is_singular() ) {
			self::$current_brand = self::get_current_brand_for_post( get_queried_object(), $brand_override );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			self::$current_brand = self::get_current_brand_for_term( get_queried_object(), $brand_override );
		} elseif ( is_author() ) {
			self::$current_brand = self::get_current_brand_for_author( get_queried_object_id(), $brand_override );
		}
	}
}
