<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/BrianHenryIE/BH-WP-Mail-Via-Gmail-API
 * @since      2.0.0
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings\Settings_Section_Element_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings\Settings_Section_Password_Element_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings\Settings_Section_Text_Element_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use DateTime;

/**
 * The settings page for the plugin.
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author     Brian Henry <BrianHenryIE@gmail.com>
 */
class Settings_Page {
	use LoggerAwareTrait;

	protected Settings_Interface $settings;

	protected API_Interface $api;

	/**
	 * Settings_Page constructor.
	 *
	 * @param Settings_Interface $settings
	 */
	public function __construct( API_Interface $api, Settings_Interface $settings, LoggerInterface $logger ) {
		$this->setLogger( $logger );
		$this->settings = $settings;
		$this->api      = $api;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->settings->get_plugin_slug(), plugin_dir_url( __FILE__ ) . 'css/bh-wp-one-click-list-unsubscribe-admin.css', array(), $this->settings->get_plugin_version(), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->settings->get_plugin_slug(), plugin_dir_url( __FILE__ ) . 'js/bh-wp-one-click-list-unsubscribe-admin.js', array( 'jquery' ), $this->settings->get_plugin_version(), false );

	}

	/**
	 * @hooked admin_menu
	 */
	function add_settings_page(): void {

		$page_title      = __( 'One-Click List-Unsubscribe', 'bh-wp-one-click-list-unsubscribe' );
		$menu_title      = __( 'List-Unsubscribe', 'bh-wp-one-click-list-unsubscribe' );
		$capability      = 'manage_options';
		$menu_slug       = $this->settings->get_plugin_slug();
		$output_callback = array( $this, 'display_plugin_settings_page' );

		add_options_page( $page_title, $menu_title, $capability, $menu_slug, $output_callback );
	}

	/**
	 * Called by WordPress to display the page.
	 *
	 * Makes the required variables ready for the template as strings.
	 */
	function display_plugin_settings_page(): void {

		$plugin_slug           = $this->settings->get_plugin_slug();
		$schedules             = $this->settings->get_cron_schedules();
		$fetch_emails_schedule = $schedules['fetch_emails'];

		$last_checked          = 'never';
		$last_checked_datetime = $this->api->get_last_checked_time();
		if ( ! is_null( $last_checked_datetime ) ) {
			$now      = new DateTime();
			$interval = $now->diff( $last_checked_datetime );
			$minutes  = intval( $interval->format( 'i' ) );
			if ( 0 !== $minutes ) {
				$last_checked = 1 == $minutes ? '1 minute ago' : "{$minutes} ago";
			}
		}

		$cpt            = $this->settings->get_cpt_underscored_20();
		$link_to_emails = admin_url( "edit.php?post_type={$cpt}" );

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'Admin/partials/admin-display.php';
	}

	/**
	 * Register the one settings section with WordPress.
	 *
	 * @hooked admin_init
	 */
	public function setup_sections(): void {

		$settings_page_slug_name = $this->settings->get_plugin_slug();

		add_settings_section(
			'default',
			'Settings',
			null,
			$settings_page_slug_name
		);
	}

	/**
	 * Field Configuration, each item in this array is one field/setting we want to capture.
	 *
	 * @hooked admin_init
	 *
	 * @see https://github.com/reside-eng/wordpress-custom-plugin/blob/master/admin/class-wordpress-custom-plugin-admin.php
	 *
	 * @since    1.0.0
	 */
	public function setup_fields(): void {

		$settings                = $this->settings;
		$section                 = 'default';
		$settings_page_slug_name = $this->settings->get_plugin_slug();

		$plugin_slug = $this->settings->get_plugin_slug();
		$version     = $this->settings->get_plugin_version();

		/** @var Settings_Section_Element_Abstract[] $fields */
		$fields = array();

		// Email Address.
		$fields[] = new class($settings, $section, $settings_page_slug_name ) extends Settings_Section_Text_Element_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_id(): string {
				return Settings::EMAIL_ADDRESS_OPTION;
			}

			/**
			 * @inheritDoc
			 */
			public function get_title(): string {
				return __( 'Return Email Address', 'bh-wp-one-click-list-unsubscribe' );
			}

			/**
			 * @inheritDoc
			 */
			public function get_value() {
				return $this->settings->get_email_address();
			}

			public function sanitize_callback( $value ) {
				return sanitize_email( $value );
			}

		};

		// IMAP Server.
		$fields[] = new class($settings, $section, $settings_page_slug_name ) extends Settings_Section_Text_Element_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_id(): string {
				return Settings::EMAIL_SERVER_OPTION;
			}

			/**
			 * @inheritDoc
			 */
			public function get_title(): string {
				return __( 'IMAP Server', 'bh-wp-one-click-list-unsubscribe' );
			}

			/**
			 * @inheritDoc
			 */
			function get_value() {
				return $this->settings->get_email_server();
			}
		};

		// Email Username.
		$fields[] = new class($this->settings, 'default', $settings_page_slug_name ) extends Settings_Section_Text_Element_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_id(): string {
				return Settings::EMAIL_USERNAME_OPTION;
			}

			/**
			 * @inheritDoc
			 */
			public function get_title(): string {
				return __( 'Email Username', 'bh-wp-one-click-list-unsubscribe' );
			}

			/**
			 * @inheritDoc
			 */
			function get_value() {
				return $this->settings->get_email_username();
			}

			function get_output_args(): array {
				$args                = parent::get_output_args();
				$args['placeholder'] = 'Often the email address itself.';
				return $args;
			}
		};

		// Email Password.
		$fields[] = new class($settings, $section, $settings_page_slug_name ) extends Settings_Section_Password_Element_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_id(): string {
				return Settings::EMAIL_PASSWORD_OPTION;
			}

			/**
			 * @inheritDoc
			 */
			public function get_title(): string {
				return __( 'Email Password', 'bh-wp-one-click-list-unsubscribe' );
			}

			/**
			 * @inheritDoc
			 */
			function get_value() {
				return $this->settings->get_email_password();
			}
		};

		foreach ( $fields as $field ) {

			list($id, $title, $print_field_callback, $page, $section, $output_args) = $field->get_add_settings_field_args();

			add_settings_field( $id, $title, $print_field_callback, $page, $section, $output_args );

			list($option_group, $option_name, $description_args) = $field->get_register_setting_args();

			// TODO: Maybe this should be in Settings class.
			register_setting( $option_group, $option_name, $description_args );

		}
	}
}
