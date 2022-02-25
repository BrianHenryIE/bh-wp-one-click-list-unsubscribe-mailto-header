<?php
/**
 * Class Settings_Page_Test. Tests the settings page.
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\BH_WP_One_Click_List_Unsubscribe;

class Settings_Page_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * Only three of the settings were printing out ... they didn't have unique names.
	 */
	public function test_setup_fields_add_settings_fields() {

		/** @var BH_WP_One_Click_List_Unsubscribe $bh_wp_one_click_list_unsubscribe */
		$bh_wp_one_click_list_unsubscribe = $GLOBALS['bh_wp_one_click_list_unsubscribe'];

		/** @var Settings_Page $settings_page */
		$settings_page = $bh_wp_one_click_list_unsubscribe->settings_page;

		$settings_page->setup_fields();

		global $wp_settings_fields;

		$this->assertCount( 4, $wp_settings_fields['bh-wp-one-click-list-unsubscribe']['default'] );
	}

}
