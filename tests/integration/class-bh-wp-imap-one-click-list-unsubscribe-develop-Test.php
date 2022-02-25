<?php
/**
 * Class Plugin_Test. Tests the root plugin setup.
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\BH_WP_One_Click_List_Unsubscribe;

/**
 * Verifies the plugin has been instantiated and added to PHP's $GLOBALS variable.
 */
class Plugin_Develop_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * Test the main plugin object is added to PHP's GLOBALS and that it is the correct class.
	 */
	public function test_plugin_instantiated() {

		$this->assertArrayHasKey( 'bh_wp_one_click_list_unsubscribe', $GLOBALS );

		$this->assertInstanceOf( API::class, $GLOBALS['bh_wp_one_click_list_unsubscribe'] );
	}

}
