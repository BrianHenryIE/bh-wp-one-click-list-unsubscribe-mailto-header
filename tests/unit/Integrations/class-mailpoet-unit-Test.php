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
	 *
	 * @covers ::add_outgoing_filter
	 */
	public function test_add_outgoing_filter() {

		$this->markTestIncomplete();

		$mailpoet = new MailPoet( null );

		$mailpoet->add_outgoing_filter();

	}

}
