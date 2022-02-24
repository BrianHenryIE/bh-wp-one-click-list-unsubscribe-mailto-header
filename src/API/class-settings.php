<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Logger\API\Logger_Settings_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\Mailbox_Settings_Defaults_Trait;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\Mailbox_Settings_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_WP_Mailboxes_Settings_Defaults_Trait;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\Account_Credentials_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\API\Ddeboer_Imap\IMAP_Credentials_Interface;
use Psr\Log\LogLevel;

class Settings implements Settings_Interface, Logger_Settings_Interface {
	use BH_WP_Mailboxes_Settings_Defaults_Trait;

	const EMAIL_ADDRESS_OPTION  = 'bh_wp_one_click_list_unsubscribe_email_address';
	const EMAIL_USERNAME_OPTION = 'bh_wp_one_click_list_unsubscribe_email_username';
	const EMAIL_PASSWORD_OPTION = 'bh_wp_one_click_list_unsubscribe_email_password';
	const EMAIL_SERVER_OPTION   = 'bh_wp_one_click_list_unsubscribe_email_server';
	const SUBJECT_REGEX_OPTION  = 'bh_wp_one_click_list_unsubscribe_subject_regex';


	public function get_plugin_slug(): string {
		return 'bh-wp-one-click-list-unsubscribe';
	}

	public function get_plugin_version(): string {
		return defined( 'BH_WP_ONE_CLICK_LIST_UNSUBSCRIBE_VERSION' ) ? BH_WP_ONE_CLICK_LIST_UNSUBSCRIBE_VERSION : '2.0.3';
	}

	public function get_plugin_basename(): string {
		return defined( 'BH_WP_ONE_CLICK_LIST_UNSUBSCRIBE_BASENAME' ) ? BH_WP_ONE_CLICK_LIST_UNSUBSCRIBE_BASENAME : 'bh-wp-one-click-list-unsubscribe/bh-wp-one-click-list-unsubscribe.php';
	}

	public function get_cpt_friendly_name(): string {
		return 'One-Click List-Unsubscribe Emails';
	}

	public function get_configured_mailbox_settings(): array {

		// TODO: Check it is configured before instantiating the class.

		$server        = get_option( self::EMAIL_SERVER_OPTION );
		$username      = get_option( self::EMAIL_USERNAME_OPTION );
		$password      = get_option( self::EMAIL_PASSWORD_OPTION );
		$email_address = get_option( self::EMAIL_ADDRESS_OPTION );

		if ( empty( $server )
			|| empty( $username )
			|| empty( $password )
			|| empty( $email_address )
		) {
			return array();
		}

		$imap_mailbox_settings = new class() implements Mailbox_Settings_Interface {
			use Mailbox_Settings_Defaults_Trait;

			public function get_account_unique_friendly_name(): string {
				return get_option( Settings::EMAIL_ADDRESS_OPTION );
			}

			/**
			 * If the subject contains "unsubscribe:".
			 *
			 * @return string
			 */
			// public function get_subject_regex(): string {
			// return get_option( self::SUBJECT_REGEX_OPTION, '/unsubscribe:/' );
			// }

			public function get_credentials(): Account_Credentials_Interface {
				return new class() implements IMAP_Credentials_Interface {

					public function get_email_imap_server(): string {
						return get_option( Settings::EMAIL_SERVER_OPTION );
					}

					public function get_email_account_username(): string {
						return get_option( Settings::EMAIL_USERNAME_OPTION );

					}

					public function get_email_account_password(): string {
						return get_option( Settings::EMAIL_PASSWORD_OPTION );
					}
				};
			}
		};
		return array( $imap_mailbox_settings );
	}

	/**
	 * The minimum severity of logs to record.
	 *
	 * @return string
	 * @see LogLevel
	 */
	public function get_log_level(): string {
		return LogLevel::DEBUG;
	}

	/**
	 * Plugin name for use by the logger in friendly messages printed to WordPress admin UI.
	 *
	 * @return string
	 * @see Logger
	 */
	public function get_plugin_name(): string {
		return 'One-Click List-Unsubscribe';
	}

	/**
	 * The email address to use in the one-click list-unsubscribe header.
	 *
	 * @return string
	 */
	public function get_email_address(): ?string {
		return get_option( self::EMAIL_ADDRESS_OPTION, null );
	}

	public function get_email_server(): ?string {
		return get_option( self::EMAIL_SERVER_OPTION, null );
	}

	public function get_email_username(): ?string {
		return get_option( self::EMAIL_USERNAME_OPTION, null );
	}


	public function get_email_password(): ?string {
		return get_option( self::EMAIL_PASSWORD_OPTION, null );
	}

	/**
	 * Returning null here, because we do not need to save email attachments, so don't need a private uploads directory.
	 */
	public function get_private_uploads_directory_name(): ?string {
		return null;
	}

}
