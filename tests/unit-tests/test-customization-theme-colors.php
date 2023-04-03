<?php
/**
 * Class TestThemeColorsCustomization
 *
 * @package Newspack_Multibranded_Site
 */

use Newspack_Multibranded_Site\Taxonomy;
use Newspack_Multibranded_Site\Meta\Theme_Colors as Theme_Colors_Meta;
use Newspack_Multibranded_Site\Customizations\Theme_Colors as Theme_Colors_Customization;

/**
 * Test the Logo filter.
 */
class TestThemeColorsCustomization extends WP_UnitTestCase {

	/**
	 * Add registered colors as the theme does
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();
		add_filter(
			'newspack_multibranded_site_theme_colors',
			function() {
				return [
					[
						'theme_mod_name' => 'primary_color',
						'label'          => 'Primary Color',
						'default'        => '#00669b',
					],
				];
			}
		);
	}

	/**
	 * Tests filter theme colors
	 */
	public function test_filter_theme_colors() {
		$term_without_theme_colors = $this->factory->term->create_and_get( [ 'taxonomy' => Taxonomy::SLUG ] );
		$term_with_theme_colors    = $this->factory->term->create_and_get( [ 'taxonomy' => Taxonomy::SLUG ] );
		add_term_meta(
			$term_with_theme_colors->term_id,
			Theme_Colors_Meta::get_key(),
			[
				[
					'name'  => 'primary_color',
					'color' => '#000000',
				],
			]
		);

		Taxonomy::set_primary( $term_without_theme_colors );
		$this->assertSame( false, get_theme_mod( 'primary_color' ) );

		Taxonomy::set_primary( $term_with_theme_colors );
		$this->go_to( '/' ); // Resets the current brand.
		$this->assertSame( '#000000', get_theme_mod( 'primary_color' ) );
	}

	/**
	 * Tests has theme colors
	 */
	public function test_has_theme_colors() {
		$term_without_theme_colors = $this->factory->term->create_and_get( [ 'taxonomy' => Taxonomy::SLUG ] );
		$term_with_theme_colors    = $this->factory->term->create_and_get( [ 'taxonomy' => Taxonomy::SLUG ] );
		add_term_meta(
			$term_with_theme_colors->term_id,
			Theme_Colors_Meta::get_key(),
			[
				[
					'name'  => 'primary_color',
					'color' => '#000000',
				],
			]
		);

		Taxonomy::set_primary( $term_without_theme_colors );
		$this->assertFalse( Theme_Colors_Customization::current_brand_has_custom_colors() );
		$this->assertFalse( Theme_Colors_Customization::current_brand_has_custom_colors( [ 'primary_color' ] ) );
		$this->assertFalse( Theme_Colors_Customization::current_brand_has_custom_colors( [ 'primary_color', 'secondary_color' ] ) );
		$this->assertFalse( Theme_Colors_Customization::current_brand_has_custom_colors( [ 'secondary_color' ] ) );

		Taxonomy::set_primary( $term_with_theme_colors );
		$this->go_to( '/' ); // Resets the current brand.
		$this->assertTrue( Theme_Colors_Customization::current_brand_has_custom_colors() );
		$this->assertTrue( Theme_Colors_Customization::current_brand_has_custom_colors( [ 'primary_color' ] ) );
		$this->assertTrue( Theme_Colors_Customization::current_brand_has_custom_colors( [ 'primary_color', 'secondary_color' ] ) );
		$this->assertFalse( Theme_Colors_Customization::current_brand_has_custom_colors( [ 'secondary_color' ] ) );
	}
}
