<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract;
use DateTime;

interface API_Interface {

	/**
	 * @return Unsubscribe_Integration_Abstract[]
	 */
	public function get_unsubscribe_integrations(): array;

	public function check_for_unsubscribe_emails(): array;

	public function get_last_checked_time(): ?DateTime;
	public function get_next_check_time(): ?DateTime;
}
