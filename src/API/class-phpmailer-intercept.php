<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\API;

use PHPMailer\PHPMailer\PHPMailer;

class PHPMailer_Intercept extends \PHPMailer\PHPMailer\PHPMailer {

	/**
	 * @see PHPMailer::send()
	 *
	 * @return bool
	 * @throws \PHPMailer\PHPMailer\Exception
	 */
	public function send() {

		/** @var API $one_click_unsubscribe_api */
		$one_click_unsubscribe_api = $GLOBALS['bh_wp_one_click_list_unsubscribe'];

		if ( $one_click_unsubscribe_api instanceof API ) {

			$headers = $this->getCustomHeaders();
			$this->clearCustomHeaders();

			foreach ( $headers as $header ) {
				if ( 'List-Unsubscribe' === $header[0] ) {
					$header[1] = $one_click_unsubscribe_api->add_mailto_to_existing_list_unsubscribe_header( $header[1] );
				}
				$this->addCustomHeader( $header );
			}
		}

		return parent::send();
	}
}
