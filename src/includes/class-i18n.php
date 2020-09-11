<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BH_WP_IMAP_One_Click_List_Unsubscribe
 * @subpackage BH_WP_IMAP_One_Click_List_Unsubscribe/includes
 */

namespace BH_WP_IMAP_One_Click_List_Unsubscribe\includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    BH_WP_IMAP_One_Click_List_Unsubscribe
 * @subpackage BH_WP_IMAP_One_Click_List_Unsubscribe/includes
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */
class I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bh-wp-imap-one-click-list-unsubscribe',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
