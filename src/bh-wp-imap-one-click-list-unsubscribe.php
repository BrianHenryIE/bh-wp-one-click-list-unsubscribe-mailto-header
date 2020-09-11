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
 * @package           BH_WP_IMAP_One_Click_List_Unsubscribe
 *
 * @wordpress-plugin
 * Plugin Name:       BH WP IMAP One Click List Unsubscribe
 * Plugin URI:        http://github.com/username/bh-wp-imap-one-click-list-unsubscribe/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Brian Henry
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-imap-one-click-list-unsubscribe
 * Domain Path:       /languages
 */

namespace BH_WP_IMAP_One_Click_List_Unsubscribe;

use BH_WP_IMAP_One_Click_List_Unsubscribe\includes\Activator;
use BH_WP_IMAP_One_Click_List_Unsubscribe\includes\Deactivator;
use BH_WP_IMAP_One_Click_List_Unsubscribe\includes\BH_WP_IMAP_One_Click_List_Unsubscribe;
use BH_WP_IMAP_One_Click_List_Unsubscribe\WPPB\WPPB_Loader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BH_WP_IMAP_ONE_CLICK_LIST_UNSUBSCRIBE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function activate_bh_wp_imap_one_click_list_unsubscribe() {

	Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function deactivate_bh_wp_imap_one_click_list_unsubscribe() {

	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'BH_WP_IMAP_One_Click_List_Unsubscribe\activate_bh_wp_imap_one_click_list_unsubscribe' );
register_deactivation_hook( __FILE__, 'BH_WP_IMAP_One_Click_List_Unsubscribe\deactivate_bh_wp_imap_one_click_list_unsubscribe' );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wp_imap_one_click_list_unsubscribe() {

	$loader = new WPPB_Loader();
	$plugin = new BH_WP_IMAP_One_Click_List_Unsubscribe( $loader );

	return $plugin;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['bh_wp_imap_one_click_list_unsubscribe'] = $bh_wp_imap_one_click_list_unsubscribe = instantiate_bh_wp_imap_one_click_list_unsubscribe();
$bh_wp_imap_one_click_list_unsubscribe->run();
