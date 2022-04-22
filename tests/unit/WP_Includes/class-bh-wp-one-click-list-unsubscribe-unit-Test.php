<?php

namespace BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes;

use BrianHenryIE\ColorLogger\ColorLogger;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Settings_Page;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Admin\Plugins_Page;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\API_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\API\Settings_Interface;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Abstract_Unsubscribe_Integration_Unit_Test;
use BrianHenryIE\WP_One_Click_List_Unsubscribe\Integrations\Unsubscribe_Integration_Abstract;
use Codeception\Stub\Expected;
use WP_Mock\Matcher\AnyInstance;

/**
 * @coversDefaultClass \BrianHenryIE\WP_One_Click_List_Unsubscribe\WP_Includes\BH_WP_One_Click_List_Unsubscribe
 */
class BH_WP_One_Click_List_Unsubscribe_Unit_Test extends \Codeception\Test\Unit {

	protected function setup(): void {
		parent::setup();
		\WP_Mock::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers ::add_process_new_emails_hook
	 * @covers ::__construct
	 */
	public function test_add_process_new_emails_hook(): void {

		$api      = $this->makeEmpty( API_Interface::class );
		$settings = $this->makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_slug' => Expected::once(
					function() {
						return 'bh-wp-one-click-list-unsubscribe'; }
				),
			)
		);

		\WP_Mock::expectActionAdded(
			'bh_wp_mailboxes_fetch_emails_saved_bh-wp-one-click-list-unsubscribe',
			array( $api, 'process_new_emails' )
		);

		$logger = new ColorLogger();
		new BH_WP_One_Click_List_Unsubscribe( $api, $settings, $logger );
	}

	/**
	 * @covers ::define_plugins_page_hooks
	 */
	public function test_define_plugins_page_hooks(): void {

		\WP_Mock::expectFilterAdded(
			'plugin_action_links_bh-wp-one-click-list-unsubscribe/bh-wp-one-click-list-unsubscribe.php',
			array( new AnyInstance( Plugins_Page::class ), 'action_links' )
		);

		\WP_Mock::expectFilterAdded(
			'plugin_row_meta',
			array( new AnyInstance( Plugins_Page::class ), 'row_meta' ),
			20,
			4
		);

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty(
			Settings_Interface::class,
			array(
				'get_plugin_basename' => Expected::once(
					function() {
						return 'bh-wp-one-click-list-unsubscribe/bh-wp-one-click-list-unsubscribe.php'; }
				),
			)
		);
		$api      = $this->makeEmpty( API_Interface::class );
		new BH_WP_One_Click_List_Unsubscribe( $api, $settings, $logger );
	}


	/**
	 * @covers ::define_settings_page_hooks
	 */
	public function test_define_settings_page_hooks(): void {

		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Settings_Page::class ), 'enqueue_styles' )
		);
		\WP_Mock::expectActionAdded(
			'admin_enqueue_scripts',
			array( new AnyInstance( Settings_Page::class ), 'enqueue_scripts' )
		);
		\WP_Mock::expectActionAdded(
			'admin_menu',
			array( new AnyInstance( Settings_Page::class ), 'add_settings_page' )
		);
		\WP_Mock::expectActionAdded(
			'admin_init',
			array( new AnyInstance( Settings_Page::class ), 'setup_sections' )
		);
		\WP_Mock::expectActionAdded(
			'admin_init',
			array( new AnyInstance( Settings_Page::class ), 'setup_fields' )
		);

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty( API_Interface::class );
		new BH_WP_One_Click_List_Unsubscribe( $api, $settings, $logger );
	}

	/**
	 * @covers ::initialize_integrations
	 */
	public function test_initialize_integrations(): void {

		$integration = $this->makeEmpty(
			Unsubscribe_Integration_Abstract::class,
			array(
				'add_outgoing_filter' => Expected::once(),
			)
		);

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty(
			API_Interface::class,
			array(
				'get_unsubscribe_integrations' => Expected::once(
					function() use ( $integration ) {
						return array( $integration ); }
				),
			)
		);
		new BH_WP_One_Click_List_Unsubscribe( $api, $settings, $logger );
	}

	/**
	 * @covers ::set_locale
	 */
	public function test_set_locale_hooked(): void {

		\WP_Mock::expectActionAdded(
			'init',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

		$logger   = new ColorLogger();
		$settings = $this->makeEmpty( Settings_Interface::class );
		$api      = $this->makeEmpty( API_Interface::class );
		new BH_WP_One_Click_List_Unsubscribe( $api, $settings, $logger );
	}


}
