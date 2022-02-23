<?php
/**
 *
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\integrations;

/**
 *
 */
class MailPoet_Integration_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * After the plugin is loaded, the integrations' functions for editing the outgoing mail should have been added.
	 */
	public function test_mailpoet_outgoing_filter() {

		$this->markTestIncomplete();

		$action_name       = 'mailpoet_headers';
		$expected_priority = 10;
		$class_type        = MailPoet::class;
		$method_name       = 'add_unsubscribe_email_to_headers';

		$hooked = $this->is_function_hooked_on_action( $class_type, $method_name, $action_name, $expected_priority );

		$this->assertTrue( $hooked );

	}




}
