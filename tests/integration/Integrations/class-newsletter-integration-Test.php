<?php
/**
 *
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\integrations;

/**
 *
 */
class Newsletter_Integration_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * After the plugin is loaded, the integrations' functions for editing the outgoing mail should have been added.
	 */
	public function test_mailpoet_outgoing_filter() {

		$this->markTestIncomplete();

		$action_name       = 'newsletter_message_headers';
		$expected_priority = 11;
		$class_type        = Newsletter::class;
		$method_name       = 'add_unsubscribe_email_to_headers';

		$hooked = $this->is_function_hooked_on_action( $class_type, $method_name, $action_name, $expected_priority );

		$this->assertTrue( $hooked );

	}

	public function test_header_added_to_mail(): void {

		// Maybe do this in Jest?!
	}

}
