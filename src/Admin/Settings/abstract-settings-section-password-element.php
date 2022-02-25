<?php
/**
 * An abstract settings element for password input.
 *
 * @link       https://BrianHenry.ie
 * @since      2.0.0
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings;

/**
 * Code common across setting text input elements.
 *
 * @see https://github.com/reside-eng/wordpress-custom-plugin
 * @see register_setting()
 * @see add_settings_field()
 *
 * Class Settings_Section_Element
 */
abstract class Settings_Section_Password_Element_Abstract extends Settings_Section_Text_Element_Abstract {

	/**
	 * Echo the HTML for configuring this setting.
	 *
	 * The following HTML input properties stop (some) password managers from auto-filling the password field.
	 * autocomplete="off" data-lpignore="true" data-form-type="text"
	 *
	 * @param array $arguments The field data as registered with add_settings_field().
	 */
	public function print_field_callback( $arguments ): void {

		$value = $this->get_value();

		if ( isset( $arguments['placeholder'] ) ) {
			printf( '<input name="%1$s" id="%1$s" type="password" placeholder="%2$s" autocomplete="off" data-lpignore="true" data-form-type="text" value="%3$s" />', esc_attr( $this->get_id() ), esc_attr( $arguments['placeholder'] ), esc_attr( $value ) );
		} else {
			printf( '<input name="%1$s" id="%1$s" type="password" autocomplete="off" data-lpignore="true" data-form-type="text" value="%2$s" />', esc_attr( $this->get_id() ), esc_attr( $value ) );
		}

		if ( isset( $arguments['helper'] ) ) {
			printf( '<span class="helper">%s</span>', esc_html( $arguments['helper'] ) );
		}

		if ( isset( $arguments['supplemental'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $arguments['supplemental'] ) );
		}

	}
}
