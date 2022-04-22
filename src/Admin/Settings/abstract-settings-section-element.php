<?php
/**
 * An abstract settings element for sharing functionality in setting elements. Plain old object.
 * e.g. a text input can extend this to implement common text input functionality, then an email
 * input can extend this and only needs to specify the sanitization callback.
 *
 * @link       https://BrianHenry.ie
 * @since      2.0.0
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;

/**
 * Code common across setting elements.
 *
 * @see https://github.com/reside-eng/wordpress-custom-plugin
 * @see register_setting()
 * @see add_settings_field()
 *
 * Class Settings_Section_Element
 */
abstract class Settings_Section_Element_Abstract {

	/** @var Settings_Interface */
	protected $settings;

	/**
	 * The slug of the settings page this setting is shown on.
	 *
	 * @var string $page The settings page page slug.
	 */
	protected string $page;

	/**
	 * The section name as used with add_settings_section().
	 *
	 * @var string $section The section/tab the setting is displayed in.
	 */
	protected string $section = 'default';

	/**
	 * The data array the WordPress Settings API passes to print_field_callback().
	 *
	 * @var array{helper:string, supplemental:string, default:mixed, placeholder:string} Array of data available to print_field_callback()
	 */
	protected array $add_settings_field_output_args = array();

	/**
	 * The options array used when registering the setting.
	 *
	 * @var array Configuration options for register_setting()
	 */
	protected array $register_setting_description_args;

	/**
	 * Settings_Section_Element constructor.
	 *
	 * @param Settings_Interface $settings Plugin settings.
	 * @param string             $section The name of the section the settings are displayed in.
	 * @param string             $settings_page_slug_name The page slug the settings section is on.
	 */
	public function __construct( $settings, $section, $settings_page_slug_name ) {

		$this->settings = $settings;
		$this->page     = $settings_page_slug_name;
		$this->section  = $section ?? 'default';

		$this->register_setting_description_args = array(
			'sanitize_callback' => array( $this, 'sanitize_callback' ),
			'show_in_rest'      => false,
			'description'       => '', // only relevant when show_in_rest = true.
		);
	}

	/**
	 * Add the configured settings field to the page and section.
	 */
	public function get_add_settings_field_args(): array {

		 $args = array(
			 $this->get_id(),
			 $this->get_title(),
			 array( $this, 'print_field_callback' ),
			 $this->get_page(),
			 $this->get_section(),
			 $this->get_output_args(),
		 );

		 return $args;
	}


	/**
	 * Register the setting with WordPress so it whitelisted for saving.
	 */
	public function get_register_setting_args(): array {

		$args = array(
			$this->get_page(),
			$this->get_id(),
			$this->get_description_args(),
		);

		return $args;
	}

	/**
	 * The name of the setting as it is printed in the left column of the settings table.
	 *
	 * @return string $title The title of the setting.
	 */
	abstract public function get_title(): string;

	/**
	 * The unique setting id, as used in the wp_options table.
	 *
	 * @return string The id of the setting in the database.
	 */
	abstract public function get_id(): string;

	/**
	 * The setting's existing value. Used in HTML value="".
	 *
	 * @return mixed The value.
	 */
	abstract function get_value();

	public function get_page(): string {
		return $this->page;
	}

	public function get_section(): string {
		return $this->section;
	}

	protected function get_output_args(): array {
		return $this->add_settings_field_output_args;
	}

	protected function get_description_args(): array {

		return $this->register_setting_description_args;
	}

	/**
	 * Echo the HTML for configuring this setting.
	 *
	 * @param array $arguments The field data as registered with add_settings_field().
	 */
	abstract public function print_field_callback( $arguments ): void;

	/**
	 * Carry out any sanitization and pre-processing of the POSTed data before it is saved in the database.
	 *
	 * @param mixed $value The value entered by the user as POSTed to WordPress.
	 *
	 * @return mixed
	 */
	abstract public function sanitize_callback( $value );

}
