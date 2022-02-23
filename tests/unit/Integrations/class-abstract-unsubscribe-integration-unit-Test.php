<?php
/**
 *
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Codeception\Stub;

/**
 * @covers \BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract
 */
class Abstract_Unsubscribe_Integration_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Test the header is added!
	 *
	 * TODO: Use reflection to access the protected method.
	 *
	 * //covers ::add_mailto_to_existing_header
	 */
	public function test_add_mailto_to_existing_header() {

		$before_headers = array(
			'List-Unsubscribe' => '<https://example.org.org?mailpoet_router&endpoint=track&action=click&data=WyI0IiwiZDAzYWE3IiwiMSIsImIzNjU4YjMzMDEwMCIsZmFsc2Vd>',
		);

		$settings_mock = Stub::makeEmpty(
			Settings_Interface::class,
			array(
				'get_email_address' => 'test@example.org',
			)
		);

		$sut = new class( $settings_mock ) extends Unsubscribe_Integration_Abstract {
			public function add_outgoing_filter() {}
			public function test_add_mailto_to_existing_header( $headers ) {
				return $this->add_mailto_to_existing_header( $headers );
			}
		};

		$expected = '<https://example.org.org?mailpoet_router&endpoint=track&action=click&data=WyI0IiwiZDAzYWE3IiwiMSIsImIzNjU4YjMzMDEwMCIsZmFsc2Vd>, <mailto:test@example.org?subject=unsubscribe:aHR0cHM6Ly9leGFtcGxlLm9yZy5vcmc/bWFpbHBvZXRfcm91dGVyJmVuZHBvaW50PXRyYWNrJmFjdGlvbj1jbGljayZkYXRhPVd5STBJaXdpWkRBellXRTNJaXdpTVNJc0ltSXpOalU0WWpNek1ERXdNQ0lzWm1Gc2MyVmQ=>';

		$after_headers = $sut->test_add_mailto_to_existing_header( $before_headers );

		$this->assertSame( $expected, $after_headers['List-Unsubscribe'] );

	}

}
