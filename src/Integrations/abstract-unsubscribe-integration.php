<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Class Unsubscribe_Integration
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */
abstract class Unsubscribe_Integration_Abstract {
	use LoggerAwareTrait;

	protected API_Interface $api;

	/** @var Settings_Interface */
	protected $settings;

	/**
	 * MailPoet constructor.
	 *
	 * @param Settings_Interface $settings
	 */
	public function __construct( API_Interface $api, Settings_Interface $settings, LoggerInterface $logger ) {
		$this->setLogger( $logger );
		$this->api      = $api;
		$this->settings = $settings;
	}

	/**
	 * Friendly name to display in settings and when a user is unsubscribed.
	 *
	 * @return string
	 */
	abstract public function get_friendly_name(): string;

	/**
	 * @return mixed
	 */
	abstract public function add_outgoing_filter();

	/**
	 * Determine is the email address subscribed to the integration before/after.
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	abstract public function is_subscribed( string $email ): bool;

}
