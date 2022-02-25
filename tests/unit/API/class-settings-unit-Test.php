<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings
 */
class Settings_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::get_plugin_slug
	 */
	public function test_get_plugin_slug(): void {
		$sut    = new Settings();
		$result = $sut->get_plugin_slug();
		$this->assertEquals( 'bh-wp-one-click-list-unsubscribe', $result );
	}

	/**
	 * TODO: This test should really be about comparing the plugin header version, defined version, and class version.
	 *
	 * @covers ::get_plugin_version
	 */
	public function test_get_plugin_version(): void {
		$sut    = new Settings();
		$result = $sut->get_plugin_version();
		$this->assertEquals( '2.0.6', $result );
	}

	/**
	 * @covers ::get_plugin_basename
	 */
	public function test_get_plugin_basename(): void {
		$sut    = new Settings();
		$result = $sut->get_plugin_basename();
		$this->assertEquals( 'bh-wp-one-click-list-unsubscribe/bh-wp-one-click-list-unsubscribe.php', $result );
	}

	/**
	 * @covers ::get_cpt_friendly_name
	 */
	public function test_get_cpt_friendly_name(): void {
		$sut    = new Settings();
		$result = $sut->get_cpt_friendly_name();
		$this->assertEquals( 'One-Click List-Unsubscribe Emails', $result );
	}

	/**
	 * @covers ::get_plugin_name
	 */
	public function test_get_plugin_name(): void {
		$sut    = new Settings();
		$result = $sut->get_plugin_name();
		$this->assertEquals( 'One-Click List-Unsubscribe', $result );
	}

	/**
	 * @covers ::get_email_address
	 */
	public function test_get_email_address(): void {

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'  => array( 'bh_wp_one_click_list_unsubscribe_email_address', null ),
				'times' => 1,
			)
		);

		$sut = new Settings();
		$sut->get_email_address();

	}


	/**
	 * @covers ::get_email_password
	 */
	public function test_get_email_password(): void {

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'  => array( 'bh_wp_one_click_list_unsubscribe_email_password', null ),
				'times' => 1,
			)
		);

		$sut = new Settings();
		$sut->get_email_password();

	}

	/**
	 * @covers ::get_email_server
	 */
	public function test_get_email_server(): void {

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'  => array( 'bh_wp_one_click_list_unsubscribe_email_server', null ),
				'times' => 1,
			)
		);

		$sut = new Settings();
		$sut->get_email_server();

	}

	/**
	 * @covers ::get_email_username
	 */
	public function test_get_email_username(): void {

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'  => array( 'bh_wp_one_click_list_unsubscribe_email_username', null ),
				'times' => 1,
			)
		);

		$sut = new Settings();
		$sut->get_email_username();

	}

	/**
	 * @covers ::get_private_uploads_directory_name
	 */
	public function test_get_private_uploads_directory_name(): void {
		$sut    = new Settings();
		$result = $sut->get_private_uploads_directory_name();

		$this->assertNull( $result );
	}

	/**
	 * @covers ::get_configured_mailbox_settings
	 */
	public function test_get_configured_mailbox_settings_empty(): void {

		$sut = new Settings();

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'   => array( 'bh_wp_one_click_list_unsubscribe_email_server' ),
				'return' => null,
				'times'  => 1,
			)
		);

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'  => array( 'bh_wp_one_click_list_unsubscribe_email_username' ),
				'times' => 1,
			)
		);

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'  => array( 'bh_wp_one_click_list_unsubscribe_email_password' ),
				'times' => 1,
			)
		);

		\WP_Mock::userFunction(
			'get_option',
			array(
				'args'  => array( 'bh_wp_one_click_list_unsubscribe_email_address' ),
				'times' => 1,
			)
		);

		$result = $sut->get_configured_mailbox_settings();

		$this->assertEmpty( $result );

	}
}
