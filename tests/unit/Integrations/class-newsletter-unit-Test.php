<?php
/**
 *
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;

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

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty( API_Interface::class );

		$newsletter = new Newsletter( $api, $settings, $logger );

		\WP_Mock::expectFilterAdded( 'newsletter_message_headers', array( $newsletter, 'add_unsubscribe_email_to_headers' ), 11, 3 );

		$newsletter->add_outgoing_filter();

	}

}
