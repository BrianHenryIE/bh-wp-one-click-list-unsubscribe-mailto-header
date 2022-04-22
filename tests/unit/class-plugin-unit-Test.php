<?php
/**
 * Tests for the root plugin file.
 *
 * @package BrianHenryIE\WC_Shipment_Tracking_Updates
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\BH_WP_One_Click_List_Unsubscribe;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Logger\Logger;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_WP_Mailboxes;

class Plugin_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		\WP_Mock::tearDown();
		\Patchwork\restoreAll();
	}

	/**
	 * Verifies the plugin initialization.
	 */
	public function test_plugin_include(): void {

		// Prevents code-coverage counting, and removes the need to define the WordPress functions that are used in that class.
		\Patchwork\redefine(
			array( BH_WP_One_Click_List_Unsubscribe::class, '__construct' ),
			function( $api, $settings, $logger ) {}
		);

		\Patchwork\redefine(
			array( Logger::class, '__construct' ),
			function( $settings ) {}
		);

		$bh_wp_mailboxes = $this->makeEmpty( BH_WP_Mailboxes::class );
		\Patchwork\redefine(
			array( BH_WP_Mailboxes::class, 'instance' ),
			function( $settings ) use ( $bh_wp_mailboxes ) {
				return $bh_wp_mailboxes;
			}
		);

		$plugin_root_dir = dirname( __DIR__, 2 ) . '/src';

		\WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_root_dir . '/',
				'times'  => 1,
			)
		);

		\WP_Mock::userFunction(
			'plugin_basename',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => 'bh-wp-one-click-list-unsubscribe/bh-wp-one-click-list-unsubscribe.php',
				'times'  => 1,
			)
		);

		ob_start();

		include $plugin_root_dir . '/bh-wp-one-click-list-unsubscribe.php';

		$printed_output = ob_get_contents();

		ob_end_clean();

		$this->assertEmpty( $printed_output );

		$this->assertArrayHasKey( 'bh_wp_one_click_list_unsubscribe', $GLOBALS );

		$this->assertInstanceOf( API::class, $GLOBALS['bh_wp_one_click_list_unsubscribe'] );

	}

}
