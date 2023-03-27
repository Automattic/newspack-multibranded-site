<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

use PHPUnit\Framework\TestCase;
use Newspack_Multibranded_Site\Taxonomy;

/**
 * Tests editing the Url metadata from the rest api.
 */
class Test_Rest_Primary_Brand extends WP_UnitTestCase {

	/**
	 * REST Server object.
	 *
	 * @var WP_REST_Server
	 */
	private $server;

	/**
	 * The current user id.
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * The secondary user id.
	 *
	 * @var int
	 */
	private $secondary_user_id;

	/**
	 * A testing brand term.
	 *
	 * @var WP_Term
	 */
	private $term1;

	/**
	 * Setting up the test.
	 *
	 * @before
	 */
	public function set_up() {
		parent::set_up();

		// See https://core.trac.wordpress.org/ticket/48300.
		do_action( 'init' );

		global $wp_rest_server;

		$wp_rest_server = new WP_REST_Server();
		$this->server   = $wp_rest_server;

		do_action( 'rest_api_init' );

		$this->user_id = $this->factory->user->create_and_get(
			array(
				'role' => 'administrator',
			)
		);

		$this->secondary_user_id = $this->factory->user->create_and_get(
			array(
				'role' => 'editor',
			)
		);

		$this->term1 = $this->factory->term->create_and_get( array( 'taxonomy' => Taxonomy::SLUG ) );
	}

	/**
	 * Dispatches a POST request to edit the site option
	 *
	 * @param string $value The new meta value.
	 * @return WP_REST_Response
	 */
	private function distpatch_request_to_edit_the_option( $value ) {
		$endpoint = '/wp/v2/settings';

		$request = new WP_REST_Request(
			'POST',
			$endpoint
		);

		$request->set_header( 'content-type', 'application/json' );

		$request->set_body(
			wp_json_encode(
				[
					Taxonomy::PRIMARY_OPTION_NAME => $value,
				]
			)
		);

		$response = $this->server->dispatch( $request );

		return $response;
	}

	/**
	 * Test editing without permissions
	 */
	public function test_unauthorized() {
		wp_set_current_user( 0 );
		$response = $this->distpatch_request_to_edit_the_option( 123 );
		$this->assertSame( 401, $response->get_status() );

		wp_set_current_user( $this->secondary_user_id->ID );
		$response = $this->distpatch_request_to_edit_the_option( 123 );
		$this->assertSame( 403, $response->get_status() );
	}

	/**
	 * Test setting a valid value
	 */
	public function test_valid_input() {
		wp_set_current_user( $this->user_id->ID );
		$response = $this->distpatch_request_to_edit_the_option( 123 );
		$data     = $response->get_data();

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 123, $data[ Taxonomy::PRIMARY_OPTION_NAME ] );
	}

	/**
	 * Test setting an invalid value
	 */
	public function test_invalid_input() {
		wp_set_current_user( $this->user_id->ID );
		$response = $this->distpatch_request_to_edit_the_option( 'invalid' );
		$data     = $response->get_data();
		$this->assertSame( 400, $response->get_status() );
	}

}
