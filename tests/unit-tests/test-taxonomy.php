<?php
/**
 * Class TestTaxonomy
 *
 * @package Newspack_Multibranded_Site
 */

/**
 * Sample test case.
 */
class TestTaxonomy extends WP_UnitTestCase {

	/**
	 * A simple test to see if the taxonomy was registered.
	 */
	public function test_taxonomy_exists() {
		// Replace this with some actual testing code.
		$this->assertTrue( taxonomy_exists( 'brand' ), 'The taxonomy is not registered' );
	}
}
