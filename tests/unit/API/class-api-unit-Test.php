<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\MailPoet;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Newsletter;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\API\API as BH_WP_Mailboxes;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_Email;
use Codeception\Stub;
use Codeception\Stub\Expected;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API
 */
class API_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Verifies filter is called, and classnames are instantiated and returned.
	 *
	 * @covers ::get_unsubscribe_integrations
	 */
	public function test_get_unsubscribe_integrations(): void {
		$logger    = new ColorLogger();
		$mailboxes = $this->makeEmpty( BH_WP_Mailboxes::class );
		$settings  = $this->makeEmpty( Settings_Interface::class );

		$sut = new API( $mailboxes, $settings, $logger );

		\WP_Mock::onFilter( 'bh_wp_one_click_list_unsubscribe_integrations' )
				->with( array( MailPoet::class, Newsletter::class ) )
				->reply( array( MailPoet::class, Newsletter::class ) );

		$result = $sut->get_unsubscribe_integrations();
		$this->assertInstanceOf( Unsubscribe_Integration_Abstract::class, array_pop( $result ) );
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
				'remove_subscriber' => Stub::consecutive( array( 'success' => true ), array( 'success' => false ) ),
			)
		);

		\WP_Mock::onFilter( 'bh_wp_one_click_list_unsubscribe_integrations' )
				->with( array( MailPoet::class, Newsletter::class ) )
				->reply( array( $unsubscribe_integration ) );

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
		$this->assertCount( 1, $result['failure'] );

	}

	/**
	 * @covers ::check_for_unsubscribe_emails
	 */
	public function test_check_for_unsubscribe_emails(): void {

		$logger    = new ColorLogger();
		$mailboxes = $this->makeEmpty(
			BH_WP_Mailboxes::class,
			array(
				'check_email' => Expected::once( array() ),
			)
		);
		$settings  = $this->makeEmpty( Settings_Interface::class );

		$sut = new API( $mailboxes, $settings, $logger );

		\WP_Mock::userFunction(
			'remove_action',
			array(
				'args'  => array( 'bh_wp_mailboxes_fetch_emails_saved_bh-wp-one-click-list-unsubscribe', \WP_Mock\Functions::type( 'array' ) ),
				'times' => 1,
			)
		);

		$result = $sut->check_for_unsubscribe_emails();

		$this->assertCount( 0, $result['success'] );
		$this->assertCount( 0, $result['failure'] );
	}
}
