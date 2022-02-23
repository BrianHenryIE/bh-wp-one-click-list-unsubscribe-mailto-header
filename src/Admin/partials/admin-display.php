<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 *  @package brianhenryie/bh-wp-one-click-list-unsubscribe
 *
 * @var string $plugin_slug
 * @var string $fetch_emails_schedule The wp_cron schedule for checking emails.
 * @var string $last_checked The last successful run time.
 * @var string $link_to_emails
 */

?>

<div class="wrap bh-wp-one-click-list-unsubscribe">

	<h1>List-Unsubscribe One-Click</h1>

	<h3>Adds the List-Unsubscribe One-Click header (RFC 8058) to outgoing mail, then checks the specified email account for the unsubscribe emails.</h3>

	<p>The email account specified below will be checked on a schedule of <code><?php echo $fetch_emails_schedule; ?></code>. It was last checked <code><?php echo $last_checked; ?></code>. <a href="<?php echo $link_to_emails; ?>">View Emails</a></p>

	<form method="POST" action="options.php">
		<?php
		settings_fields( $plugin_slug );
		do_settings_sections( $plugin_slug );
		submit_button();
		?>
	</form>

	<p><a href="https://wordpress.org/support/plugin/<?php echo $plugin_slug; ?>">Support on WordPress.org</a> &#x2022; <a href="https://github.com/BrianHenryIE/<?php echo $plugin_slug; ?>">Code on GitHub</a> &#x2022; <a href="https://BrianHenry.ie">Plugin by BrianHenryIE</a></p>

</div>

