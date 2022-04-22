<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WC_Shipment_Tracking_Updates\API\Trackers\USPS\USPS_Settings;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Codeception\Stub\Expected;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\WP_Mail
 */
class WP_Mail_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::get_friendly_name
	 */
	public function test_get_name(): void {
		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty( API_Interface::class );

		$sut = new WP_Mail( $api, $settings, $logger );

		$result = $sut->get_friendly_name();

		$this->assertEquals( 'wp_mail()', $result );
	}

	/**
	 * @covers ::add_outgoing_filter
	 */
	public function test_add_outgoing_filter() {

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty( API_Interface::class );

		$sut = new WP_Mail( $api, $settings, $logger );

		\WP_Mock::expectFilterAdded( 'wp_mail', array( $sut, 'update_outgoing_unsubscribe_headers' ) );

		$sut->add_outgoing_filter();
	}

	/**
	 * @covers ::update_outgoing_unsubscribe_headers
	 */
	public function test_update_headers(): void {

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty(
			API_Interface::class,
			array(
				'add_mailto_to_existing_headers' => Expected::once(
					function( $headers ) {
						return array( 'List-Unsubscribe' => 'updated_header' );
					}
				),
			)
		);

		$sut = new WP_Mail( $api, $settings, $logger );

		$email = array(
			'headers' => array( 'List-Unsubscribe: <http://localhost:8080/bh-wp-ngl-wp-mail/newsletter/archive/43/?unsubscribe=4|9bcc7899>' ),
		);

		$result = $sut->update_outgoing_unsubscribe_headers( $email );

		$this->assertEquals( 'List-Unsubscribe:updated_header', $result['headers'][0] );
	}


	/**
	 * @covers ::update_outgoing_unsubscribe_headers
	 */
	public function test_update_headers_accepts_string(): void {

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty(
			API_Interface::class,
			array(
				'add_mailto_to_existing_headers' => Expected::once(
					function( $headers ) {
						return array( 'List-Unsubscribe' => 'updated_header' );
					}
				),
			)
		);

		$sut = new WP_Mail( $api, $settings, $logger );

		$email = array(
			'headers' => 'List-Unsubscribe: <http://localhost:8080/bh-wp-ngl-wp-mail/newsletter/archive/43/?unsubscribe=4|9bcc7899>',
		);

		$result = $sut->update_outgoing_unsubscribe_headers( $email );

		$this->assertEquals( 'List-Unsubscribe:updated_header', $result['headers'][0] );
	}

	/**
	 * @covers ::is_subscribed
	 */
	public function test_is_subscribed(): void {

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty( API_Interface::class );

		$sut = new WP_Mail( $api, $settings, $logger );

		$email_address = 'test@example.org';

		\WP_Mock::expectFilter( 'bh_wp_one_click_list_unsubscribe_is_subscribed', false, $email_address );

		$sut->is_subscribed( $email_address );
	}

}
