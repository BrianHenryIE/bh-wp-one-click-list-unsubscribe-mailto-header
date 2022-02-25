<?php
/**
 *
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\ColorLogger\ColorLogger;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract
 */
class Abstract_Unsubscribe_Integration_WPUnit_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * TODO: This does a HTTP call when using `wp_http_validate_url()`.
	 *
	 * @covers ::remove_subscriber
	 */
	public function test_remove_subscriber() {

		$logger = new ColorLogger();

		$sut = new class( null ) extends Unsubscribe_Integration_Abstract {
			public function add_outgoing_filter() { }
		};
		$sut->setLogger( $logger );

		$email_address = 'test@example.org';
		$subject       = 'unsubscribe:aHR0cHM6Ly9icmlhbmhlbnJ5LmllP21haWxwb2V0X3JvdXRlciZlbmRwb2ludD10cmFjayZhY3Rpb249Y2xpY2smZGF0YT1XeUkwSWl3aVpEQXpZV0UzSWl3aU9TSXNJakl5TTJVMU9HTTRaR1l5TXlJc1ptRnNjMlZk';

		$request_args = null;
		$request_url  = null;

		// return true to short-circuit http request
		// @see \WP_Http::request()
		add_filter(
			'pre_http_request',
			function( $short_circuit, $parsed_args, $url ) use ( &$request_args, &$request_url ) {

				$request_args = $parsed_args;
				$request_url  = $url;

				return array(
					'response' => array( 'code' => 200 ),
				);
			},
			10,
			3
		);

		$sut->remove_subscriber( $email_address, $subject );

		$expected = 'https://brianhenry.ie?mailpoet_router&endpoint=track&action=click&data=WyI0IiwiZDAzYWE3IiwiOSIsIjIyM2U1OGM4ZGYyMyIsZmFsc2Vd';

		$this->assertSame( $expected, $request_url );

		$this->assertSame( 'List-Unsubscribe=One-Click', $request_args['body'] );

	}

}
