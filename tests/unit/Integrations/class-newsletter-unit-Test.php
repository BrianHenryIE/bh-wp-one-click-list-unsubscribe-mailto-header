<?php
/**
 *
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Newsletter
 */
class Newsletter_Unit_Test extends \Codeception\Test\Unit {

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

		$newsletter = new Newsletter( null );

		\WP_Mock::expectFilterAdded( 'newsletter_message_headers', array( $newsletter, 'add_unsubscribe_email_to_headers' ), 11, 3 );

		$newsletter->add_outgoing_filter();

	}

}
