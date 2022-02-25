<?php


// Check cron is registered


/**
 * Class Settings_Page_Test. Tests the settings page.
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\Activator;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\BH_WP_One_Click_List_Unsubscribe;

class Activator_Test extends \Codeception\TestCase\WPTestCase {

	public function test_cron_added_on_activation() {

		// TODO: Is this necessary or should the plugin be activated in the integration test setting 'activatePlugins'?
		global $plugin_basename;
		do_action( 'activate_' . $plugin_basename );

		$cron_jobs = _get_cron_array();

		$this->assertNotFalse( $cron_jobs );

		$cron_actions = array_merge( ...$cron_jobs );

		$this->assertArrayHasKey( 'check_for_unsubscribe_emails', $cron_actions );

	}
}
