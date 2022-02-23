<?php
/**
 * Tests for Settings_Section_Element_Abstract.
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 * @author  Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Partials;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings\Settings_Section_Element_Abstract;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings\Settings_Section_Element_Abstract
 */
class Settings_Section_Element_Test extends \Codeception\Test\Unit {

	/**
	 * Vanilla test to verify get_add_settings_field_args() returns the correct values.
	 *
	 * @covers ::get_add_settings_field_args
	 */
	public function test_settings_field_args() {

		$settings                = $this->createMock( Settings_Interface::class );
		$section                 = 'default';
		$settings_page_slug_name = 'settings_page_name';
		$plugin_name             = 'plugin_name';
		$version                 = '1.0.0';

		$field = new class( $settings, $section, $settings_page_slug_name, $plugin_name, $version ) extends Settings_Section_Element_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_title(): string {
				return 'title';
			}

			/**
			 * @inheritDoc
			 */
			public function get_id(): string {
				return 'uid';
			}

			/**
			 * @inheritDoc
			 */
			function get_value() {
				return 'value';
			}

			/**
			 * @inheritDoc
			 */
			public function print_field_callback( $arguments ): void {
				echo 'print input';
			}

			/**
			 * @inheritDoc
			 */
			public function sanitize_callback( $value ) {
				return 'sanitized value';
			}
		};

		list($id, $title, $callback, $page, $section, $output_args) = $field->get_add_settings_field_args();

		$this->assertSame( 'uid', $id );
		$this->assertSame( 'title', $title );
		$this->assertIsCallable( $callback );
		$this->assertSame( 'settings_page_name', $page );
		$this->assertSame( 'default', $section );
		$this->assertIsArray( $output_args );

	}

	/**
	 * Vanilla test to check get_register_setting_args() returns the correct values.
	 *
	 * @covers ::get_register_setting_args
	 */
	public function test_register_setting_args() {
		$settings                = $this->createMock( Settings_Interface::class );
		$section                 = 'default';
		$settings_page_slug_name = 'settings_page_name';
		$plugin_name             = 'plugin_name';
		$version                 = '1.0.0';

		$field = new class( $settings, $section, $settings_page_slug_name, $plugin_name, $version ) extends Settings_Section_Element_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_title(): string {
				return 'title';
			}

			/**
			 * @inheritDoc
			 */
			public function get_id(): string {
				return 'uid';
			}

			/**
			 * @inheritDoc
			 */
			function get_value() {
				return 'value';
			}

			/**
			 * @inheritDoc
			 */
			public function print_field_callback( $arguments ): void {
				echo 'print input';
			}

			/**
			 * @inheritDoc
			 */
			public function sanitize_callback( $value ) {
				return 'sanitized value';
			}
		};

		list( $option_group, $option_name, $description_args ) = $field->get_register_setting_args();

		$this->assertSame( 'settings_page_name', $option_group );
		$this->assertSame( 'uid', $option_name );
		$this->assertIsArray( $description_args );

		$this->assertArrayHasKey( 'sanitize_callback', $description_args );
		$this->assertIsCallable( $description_args['sanitize_callback'] );

	}

	/**
	 * Test additional add_settings_field args array entries.
	 *
	 * @covers ::get_add_settings_field_args
	 */
	public function test_override_get_output_args() {

		$settings                = $this->createMock( Settings_Interface::class );
		$section                 = 'default';
		$settings_page_slug_name = 'settings_page_name';
		$plugin_name             = 'plugin_name';
		$version                 = '1.0.0';

		$field = new class( $settings, $section, $settings_page_slug_name, $plugin_name, $version ) extends Settings_Section_Element_Abstract {

			/**
			 * @inheritDoc
			 */
			public function get_title(): string {
				return 'title';
			}

			/**
			 * @inheritDoc
			 */
			public function get_id(): string {
				return 'uid';
			}

			/**
			 * @inheritDoc
			 */
			function get_value() {
				return 'value';
			}

			/**
			 * @inheritDoc
			 */
			public function print_field_callback( $arguments ): void {
				echo 'print input';
			}

			/**
			 * @inheritDoc
			 */
			public function sanitize_callback( $value ) {
				return 'sanitized value';
			}

			function get_output_args(): array {
				$args                = parent::get_output_args();
				$args['placeholder'] = 'placeholder text';
				return $args;
			}
		};

		list($id, $title, $callback, $page, $section, $output_args) = $field->get_add_settings_field_args();

		$this->assertArrayHasKey( 'placeholder', $output_args );
	}

}
