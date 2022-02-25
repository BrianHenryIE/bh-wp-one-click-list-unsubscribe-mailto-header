<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * frontend-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Plugins_Page;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings_Page;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Psr\Log\LoggerInterface;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * frontend-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */
class BH_WP_One_Click_List_Unsubscribe {

	protected LoggerInterface $logger;

	/** @var Settings_Interface */
	protected Settings_Interface $settings;

	/** @var API_Interface */
	protected API_Interface $api;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the frontend-facing side of the site.
	 *
	 * @since    1.0.0
	 *
	 * @param Settings_Interface $settings The plugin settings.
	 */
	public function __construct( API_Interface $api, Settings_Interface $settings, LoggerInterface $logger ) {

		$this->logger   = $logger;
		$this->settings = $settings;
		$this->api      = $api;

		$this->set_locale();
		$this->define_settings_page_hooks();
		$this->define_plugins_page_hooks();
		$this->initialize_integrations();
		$this->add_process_new_emails_hook();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function set_locale() {

		$plugin_i18n = new I18n();

		add_action( 'init', array( $plugin_i18n, 'load_plugin_textdomain' ) );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function define_settings_page_hooks() {

		$settings_page = new Settings_Page( $this->api, $this->settings, $this->logger );

		add_action( 'admin_enqueue_scripts', array( $settings_page, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $settings_page, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $settings_page, 'add_settings_page' ) );
		add_action( 'admin_init', array( $settings_page, 'setup_sections' ) );
		add_action( 'admin_init', array( $settings_page, 'setup_fields' ) );
	}

	public function define_plugins_page_hooks(): void {

		$plugins_page = new Plugins_Page( $this->settings );

		$plugin_basename = $this->settings->get_plugin_basename();

		add_filter( "plugin_action_links_{$plugin_basename}", array( $plugins_page, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $plugins_page, 'row_meta' ), 20, 4 );
	}

	/**
	 * Call the function on each integration to add the filter to edit the list-unsubscribe header on outgoing emails.
	 */
	protected function initialize_integrations() {

		$integrations = $this->api->get_unsubscribe_integrations();

		foreach ( $integrations as $integration ) {

			$integration->add_outgoing_filter();
		}
	}

	/**
	 * Add the action that listens for the new emails to process.
	 */
	protected function add_process_new_emails_hook(): void {

		$plugin_slug = $this->settings->get_plugin_slug();

		add_action( 'bh_wp_mailboxes_fetch_emails_saved_' . $plugin_slug, array( $this->api, 'process_new_emails' ) );
	}

}
