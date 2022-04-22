<?php
/**
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations;

use TNP_Email;
use TNP_User;

/**
 * Class Newsletter
 *
 * @package brianhenryie/bh-wp-one-click-list-unsubscribe
 */
class Newsletter extends Unsubscribe_Integration_Abstract {

	public function get_friendly_name(): string {
		return 'The Newsletter Plugin';
	}

	/**
	 * The POST list-unsubscribe header is added at priority 10.
	 *
	 * @see NewsletterUnsubscription::__construct
	 */
	public function add_outgoing_filter() {
		add_filter( 'newsletter_message_headers', array( $this, 'add_unsubscribe_email_to_headers' ), 11, 3 );
	}

	/**
	 * Adds the existing List-Unsubscribe header with the email address.
	 *
	 * Existing header:
	 * ```
	 * List-Unsubscribe: <https://example.org/?na=uc&nk=2-438aa70b50&nek=1-66e61e32ae>
	 * List-Unsubscribe-Post: List-Unsubscribe=One-Click
	 * ```
	 *
	 * After:
	 * ```
	 * List-Unsubscribe: <https://example.org/?na=uc&nk=2-438aa70b50&nek=1-66e61e32ae> <mailto:unsubscribe@example.org?subject=...>
	 * ```
	 *
	 * @hooked newsletter_message_headers
	 * Hooked at priority 11 because the Newsletter developers use the same filter to initially add the header.
	 * @see \NewsletterUnsubscription
	 *
	 * @param array<string,string> $headers
	 * @param TNP_Email            $email
	 * @param TNP_User             $user
	 *
	 * @return string[]
	 */
	public function add_unsubscribe_email_to_headers( array $headers, $email, $user ) {

		$headers = $this->api->add_mailto_to_existing_headers( $headers );

		return $headers;
	}

	public function is_subscribed( string $email_address ): bool {

		$newsletter = \Newsletter::instance();

		$tnp_user = $newsletter->get_user( $email_address );

		if ( is_null( $tnp_user ) ) {
			return false;
		}

		return $tnp_user->status === TNP_User::STATUS_CONFIRMED;
	}
}
