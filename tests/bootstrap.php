<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package Newspack_Multibranded_Site
 */

$newspack_multibranded_site_test_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $newspack_multibranded_site_test_dir ) {
	$newspack_multibranded_site_test_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( "{$newspack_multibranded_site_test_dir}/includes/functions.php" ) ) {
	echo "Could not find {$newspack_multibranded_site_test_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "{$newspack_multibranded_site_test_dir}/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
function newspack_multibranded_site_manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/newspack-multibranded-site.php';
}

tests_add_filter( 'muplugins_loaded', 'newspack_multibranded_site_manually_load_plugin' );

require_once __DIR__ . '/../vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

// Start up the WP testing environment.
require "{$newspack_multibranded_site_test_dir}/includes/bootstrap.php";
