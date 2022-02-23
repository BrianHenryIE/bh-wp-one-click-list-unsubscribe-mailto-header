<?php


// Check cron is removed


/**
 *
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\Deactivator;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\BH_WP_One_Click_List_Unsubscribe;

class Activator_Test extends \Codeception\TestCase\WPTestCase {

	public function test_cron_removed_on_deactivation() {

		// TODO: Is this necessary or should the plugin be activated in the integration test setting 'activatePlugins'?
		global $plugin_basename;
		do_action( 'activate_' . $plugin_basename );

		$cron_jobs = _get_cron_array();

		$this->assertNotFalse( $cron_jobs );

		$cron_actions = array_merge( ...$cron_jobs );

		$this->assertArrayHasKey( 'check_for_unsubscribe_emails', $cron_actions );

		// ACT

		do_action( 'deactivate_' . $plugin_basename );

		$cron_jobs = _get_cron_array();

		$cron_actions = array_merge( ...$cron_jobs );

		$this->assertArrayNotHasKey( 'check_for_unsubscribe_emails', $cron_actions );

	}
}
