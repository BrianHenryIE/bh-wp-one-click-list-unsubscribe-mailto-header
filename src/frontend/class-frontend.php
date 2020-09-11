<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    BH_WP_IMAP_One_Click_List_Unsubscribe
 * @subpackage BH_WP_IMAP_One_Click_List_Unsubscribe/frontend
 */

namespace BH_WP_IMAP_One_Click_List_Unsubscribe\frontend;

use BH_WP_IMAP_One_Click_List_Unsubscribe\WPPB\WPPB_Object;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the frontend-facing stylesheet and JavaScript.
 *
 * @package    BH_WP_IMAP_One_Click_List_Unsubscribe
 * @subpackage BH_WP_IMAP_One_Click_List_Unsubscribe/frontend
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */
class Frontend extends WPPB_Object {

	/**
	 * Register the stylesheets for the frontend-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bh-wp-imap-one-click-list-unsubscribe-frontend.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the frontend-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bh-wp-imap-one-click-list-unsubscribe-frontend.js', array( 'jquery' ), $this->version, false );

	}

}
