<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 *
 * @wordpress-plugin
 * Plugin Name:       One-Click List-Unsubscribe
 * Plugin URI:        http://github.com/BrianHenryIE/bh-wp-one-click-list-unsubscribe/
 * Description:       Adds a return unsubscribe email address (rfc8058) to outgoing newsletters and checks that email address's inbox for unsubscribe requests.
 * Version:           2.0.7
 * Requires PHP:      7.4
 * Author:            Brian Henry
 * Author URI:        http://brianhenry.ie/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-one-click-list-unsubscribe
 * Domain Path:       /languages
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\BH_WP_One_Click_List_Unsubscribe;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Logger\Logger;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_WP_Mailboxes;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

define( 'BH_WP_ONE_CLICK_LIST_UNSUBSCRIBE_VERSION', '2.0.7' );
define( 'BH_WP_ONE_CLICK_LIST_UNSUBSCRIBE_BASENAME', plugin_basename( __FILE__ ) );

function instantiate_bh_wp_one_click_list_unsubscribe() {

	$settings = new Settings();
	$logger   = Logger::instance( $settings );

	$mailboxes = BH_WP_Mailboxes::instance( $settings, $logger );

	$api = new API( $mailboxes, $settings, $logger );

	$plugin = new BH_WP_One_Click_List_Unsubscribe( $api, $settings, $logger );

	return $api;
}

$GLOBALS['bh_wp_one_click_list_unsubscribe'] = instantiate_bh_wp_one_click_list_unsubscribe();
