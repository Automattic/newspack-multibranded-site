<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

use Newspack_Multibranded_Site\Taxonomy;

/**
 * Tests editing the Primary brand option from the rest api.
 */
class Test_Rest_Primary_Brand extends Newspack_Multibranded_Rest_Testcase {

	/**
	 * Test editing without permissions
	 */
	public function test_unauthorized() {
		wp_set_current_user( 0 );
		$response = $this->dispatch_request_to_edit_option( Taxonomy::PRIMARY_OPTION_NAME, 123 );
		$this->assertSame( 401, $response->get_status() );

		wp_set_current_user( $this->secondary_user_id->ID );
		$response = $this->dispatch_request_to_edit_option( Taxonomy::PRIMARY_OPTION_NAME, 123 );
		$this->assertSame( 403, $response->get_status() );
	}

	/**
	 * Test setting a valid value
	 */
	public function test_valid_input() {
		wp_set_current_user( $this->user_id->ID );
		$response = $this->dispatch_request_to_edit_option( Taxonomy::PRIMARY_OPTION_NAME, 123 );
		$data     = $response->get_data();

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 123, $data[ Taxonomy::PRIMARY_OPTION_NAME ] );
	}

	/**
	 * Test setting an invalid value
	 */
	public function test_invalid_input() {
		wp_set_current_user( $this->user_id->ID );
		$response = $this->dispatch_request_to_edit_option( Taxonomy::PRIMARY_OPTION_NAME, 'invalid' );
		$data     = $response->get_data();
		$this->assertSame( 400, $response->get_status() );
	}

}
