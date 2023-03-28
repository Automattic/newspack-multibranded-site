<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

use Newspack_Multibranded_Site\Taxonomy;

/**
 * Tests editing the Post primary brand metadata from the rest api.
 */
class Test_Rest_Post_Primary_Brand extends Newspack_Multibranded_Rest_Testcase {

	/**
	 * A testing brand term.
	 *
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * Setting up the test.
	 *
	 * @before
	 */
	public function set_up() {
		parent::set_up();
		$this->post = $this->factory->post->create_and_get();
	}

	/**
	 * Test editing without permissions
	 */
	public function test_unauthorized() {
		wp_set_current_user( 0 );
		$response = $this->distpatch_request_to_edit_postmeta( $this->post->ID, Taxonomy::PRIMARY_META_KEY, 123 );
		$this->assertSame( 401, $response->get_status() );

		wp_set_current_user( $this->secondary_user_id->ID );
		$response = $this->distpatch_request_to_edit_postmeta( $this->post->ID, Taxonomy::PRIMARY_META_KEY, 123 );
		$this->assertSame( 403, $response->get_status() );
	}

	/**
	 * Test setting a valid value
	 */
	public function test_valid_input() {
		wp_set_current_user( $this->user_id->ID );
		$response = $this->distpatch_request_to_edit_postmeta( $this->post->ID, Taxonomy::PRIMARY_META_KEY, 123 );
		$data     = $response->get_data();

		$this->assertSame( 200, $response->get_status() );
		$this->assertSame( 123, $data['meta'][ Taxonomy::PRIMARY_META_KEY ] );
	}

	/**
	 * Test setting an invalid value
	 */
	public function test_invalid_input() {
		wp_set_current_user( $this->user_id->ID );
		$response = $this->distpatch_request_to_edit_postmeta( $this->post->ID, Taxonomy::PRIMARY_META_KEY, 'invalid' );
		$data     = $response->get_data();
		$this->assertSame( 400, $response->get_status() );
	}

}
