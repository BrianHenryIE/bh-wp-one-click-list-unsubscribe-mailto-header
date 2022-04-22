<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\MailPoet;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Newsletter;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\API\API as BH_WP_Mailboxes;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_Email;
use Codeception\Stub;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API
 */
class API_WPUnit_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * TODO: This does a HTTP call when using `wp_http_validate_url()`.
	 *
	 * @covers ::remove_subscriber
	 */
	public function test_remove_subscriber(): void {

		$mailboxes = $this->makeEmpty( BH_WP_Mailboxes::class );
		$settings  = $this->makeEmpty( Settings_Interface::class );
		$logger    = new ColorLogger();

		$sut = new API( $mailboxes, $settings, $logger );

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



	/**
	 * @covers ::process_new_emails
	 */
	public function test_process_new_emails(): void {

		$logger    = new ColorLogger();
		$mailboxes = $this->makeEmpty( BH_WP_Mailboxes::class );
		$settings  = $this->makeEmpty( Settings_Interface::class );

		$sut = new API( $mailboxes, $settings, $logger );

		$unsubscribe_integration = $this->makeEmpty(
			Unsubscribe_Integration_Abstract::class,
			array(
				'get_friendly_name' => 'Test Integration',
				// before+after not present, before+after unsubscribed.
				'is_subscribed'     => Stub::consecutive( false, false, true, false ),
			)
		);

		add_filter(
			'bh_wp_one_click_list_unsubscribe_integrations',
			function() use ( $unsubscribe_integration ) {
				return array(
					$unsubscribe_integration,
				);
			}
		);

		add_filter(
			'pre_http_request',
			function( $short_circuit, $parsed_args, $url ) {
				return array(
					'response' => array( 'code' => 200 ),
				);
			},
			10,
			3
		);

		$emails = array(
			$this->make(
				BH_Email::class,
				array(
					'get_from_email' => 'asd',
					'get_subject'    => 'unsubscribe:asd',
				)
			),
			$this->make(
				BH_Email::class,
				array(
					'get_from_email' => 'asd',
					'get_subject'    => 'regular-email',
				)
			),
		);

		$result = $sut->process_new_emails( $emails );

		$this->assertCount( 1, $result['success'] );
		// $this->assertCount( 1, $result['failure'] );
	}
}
