<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Mailboxes\BH_WP_Mailboxes_Settings_Interface;

interface Settings_Interface extends BH_WP_Mailboxes_Settings_Interface {

	public function get_plugin_slug(): string;
	public function get_plugin_version(): string;
	public function get_plugin_basename(): string;

	/**
	 * The return email address to use in the one-click list-unsubscribe header.
	 *
	 * @return string
	 */
	public function get_email_address(): ?string;

	public function get_email_server(): ?string;

	public function get_email_username(): ?string;

	public function get_email_password(): ?string;
}
