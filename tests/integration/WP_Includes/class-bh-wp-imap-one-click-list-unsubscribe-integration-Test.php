<?php
/**
 * Tests for BH_WP_One_Click_List_Unsubscribe main setup class. Tests the actions are correctly added.
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Admin;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\frontend\Frontend;

/**
 * Class Develop_Test
 */
class BH_WP_One_Click_List_Unsubscribe_Integration_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * Verify admin_enqueue_scripts action is correctly added for styles, at priority 10.
	 */
	public function test_action_admin_enqueue_scripts_styles() {

		$action_name       = 'admin_enqueue_scripts';
		$expected_priority = 10;
		$class_type        = Admin::class;
		$method_name       = 'enqueue_styles';

		$hooked = $this->is_function_hooked_on_action( $class_type, $method_name, $action_name, $expected_priority );

		$this->assertTrue( $hooked );

	}

	/**
	 * Verify admin_enqueue_scripts action is added for scripts, at priority 10.
	 */
	public function test_action_admin_enqueue_scripts_scripts() {

		$action_name       = 'admin_enqueue_scripts';
		$expected_priority = 10;
		$class_type        = Admin::class;
		$method_name       = 'enqueue_scripts';

		$hooked = $this->is_function_hooked_on_action( $class_type, $method_name, $action_name, $expected_priority );

		$this->assertTrue( $hooked );

	}

	protected function is_function_hooked_on_action( $class_type, $method_name, $action_name, $expected_priority = 10 ) {

		global $wp_filter;

		$this->assertArrayHasKey( $action_name, $wp_filter, "$method_name definitely not hooked to $action_name" );

		$actions_hooked = $wp_filter[ $action_name ];

		$this->assertArrayHasKey( $expected_priority, $actions_hooked, "$method_name definitely not hooked to $action_name priority $expected_priority" );

		$hooked_method = null;
		foreach ( $actions_hooked[ $expected_priority ] as $action ) {
			$action_function = $action['function'];
			if ( is_array( $action_function ) ) {
				if ( $action_function[0] instanceof $class_type ) {
					if ( $method_name === $action_function[1] ) {
						$hooked_method = $action_function[1];
						break;
					}
				}
			}
		}

		$this->assertNotNull( $hooked_method, "No methods on an instance of $class_type hooked to $action_name" );

		$this->assertEquals( $method_name, $hooked_method, "Unexpected method name for $class_type class hooked to $action_name" );

		return true;
	}
}
