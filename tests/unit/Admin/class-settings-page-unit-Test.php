<?php
/**
 * Tests for the settings page.
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Codeception\Test\Unit;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings_Page
 */
class Settings_Page_Test extends Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Check the register_setting() and add_settings_field() are both called four times.
	 */
	public function test_setup_fields() {

		\WP_Mock::userFunction(
			'register_setting',
			array(
				'times' => 4,
			)
		);

		\WP_Mock::userFunction(
			'add_settings_field',
			array(
				'times' => 4,
			)
		);

		$logger   = new ColorLogger();
		$settings = $this->createMock( Settings_Interface::class );
		$api      = $this->makeEmpty( API_Interface::class );

		$settings_page = new Settings_Page( $api, $settings, $logger );

		$settings_page->setup_fields();
	}

}
