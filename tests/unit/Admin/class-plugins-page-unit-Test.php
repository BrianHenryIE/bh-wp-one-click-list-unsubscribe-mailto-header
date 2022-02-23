<?php

namespace BrianHenryIE\Admin;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Plugins_Page;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Codeception\Stub\Expected;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Plugins_Page
 */
class Plugins_Page_Unit_Test extends \Codeception\Test\Unit {

	protected function setUp(): void {
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::action_links
	 */
	public function test_actions_links_settings_link(): void {

		\WP_Mock::userFunction(
			'admin_url',
			array(
				'return_arg' => 0,
			)
		);

		$settings = $this->makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_slug' => Expected::once(
					function() {
						return 'bh-wp-one-click-list-unsubscribe';
					}
				),
			)
		);

		$sut = new Plugins_Page( $settings );

		$before = array();

		$result = $sut->action_links( $before );

		$this->assertIsArray( $result );

		$link_html = $result[0];

		$this->assertStringContainsString( 'Settings', $link_html );

		$this->assertStringContainsString( 'href="/admin.php?page=bh-wp-one-click-list-unsubscribe', $link_html );

	}

	/**
	 * @covers ::row_meta
	 */
	public function test_github_link(): void {

		$settings = $this->makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_slug'     => Expected::once(
					function() {
						return 'bh-wp-one-click-list-unsubscribe';
					}
				),
				'get_plugin_basename' => Expected::once(
					function() {
						return 'bh-wp-one-click-list-unsubscribe/bh-wp-one-click-list-unsubscribe.php';
					}
				),
			)
		);

		$sut = new Plugins_Page( $settings );

		$plugin_meta      = array();
		$plugin_file_name = 'bh-wp-one-click-list-unsubscribe/bh-wp-one-click-list-unsubscribe.php';
		$plugin_data      = array();
		$status           = '';

		$result = $sut->row_meta( $plugin_meta, $plugin_file_name, $plugin_data, $status );

		$added_url = $result[0];
		$this->assertStringContainsString( 'https://github.com/BrianHenryIE/bh-wp-one-click-list-unsubscribe', $added_url );
	}
}
