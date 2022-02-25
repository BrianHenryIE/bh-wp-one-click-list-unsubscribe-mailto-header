<?php
/**
 *
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\MailPoet
 */
class MailPoet_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Test add_filter is called
	 */
	public function test_add_outgoing_filter() {

		$mailpoet = new MailPoet( null );

		\WP_Mock::expectFilterAdded( 'mailpoet_headers', array( $mailpoet, 'add_unsubscribe_email_to_headers' ), 10, 4 );

		$mailpoet->add_outgoing_filter();

	}

}
