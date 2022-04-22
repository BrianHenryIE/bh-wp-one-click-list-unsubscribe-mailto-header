<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract;
use DateTimeInterface;

interface API_Interface {

	/**
	 * @return Unsubscribe_Integration_Abstract[]
	 */
	public function get_unsubscribe_integrations(): array;

	/**
	 * @param array<string, string> $headers
	 *
	 * @return array<string, string>
	 */
	public function add_mailto_to_existing_headers( array $headers ): array;

	public function add_mailto_to_existing_list_unsubscribe_header( string $header_value ): string;

	public function check_for_unsubscribe_emails(): array;

	public function get_last_checked_time(): ?DateTimeInterface;
	public function get_next_check_time(): ?DateTimeInterface;
}
