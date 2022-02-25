<?php
/**
 * Plugin Name:       One-Click List-Unsubscribe .env.secret credentials
 *
 * include this in wp-config to set the credentials in local testing.
 */

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings;

require_once __DIR__ . '/../../vendor/autoload.php';


$dotenv = \Dotenv\Dotenv::createImmutable( __DIR__ . '/../../', '.env.secret', true );
$dotenv->load();


$plugin_settings = array(
	Settings::EMAIL_ADDRESS_OPTION  => $_ENV['IMAP_EMAIL_ADDRESS'],
	Settings::EMAIL_USERNAME_OPTION => $_ENV['IMAP_USERNAME'],
	Settings::EMAIL_PASSWORD_OPTION => $_ENV['IMAP_PASSWORD'],
	Settings::EMAIL_SERVER_OPTION   => $_ENV['IMAP_SERVER'],
);

foreach ( $plugin_settings as $option_name => $return_value ) {
	// Return .env.secret values to WordPress.
	add_filter(
		"pre_option_{$option_name}",
		function() use ( $return_value ) {
			return $return_value;
		}
	);

	// Do not save the secret values to the WordPress db.
	add_filter(
		"pre_update_option_{$option_name}",
		function( $value, $old_value, $option ) use ( $return_value ) {
			return ( $value === $return_value ) ? $old_value : $value;
		},
		10,
		3
	);
}
