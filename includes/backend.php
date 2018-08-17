<?php

namespace bideja;

if (! defined('ABSPATH')) {
    exit;
}

class MailchimpBackend
{
    public function __construct()
    {
        add_action('admin_menu', array( $this, 'create_options_page' ));
        add_action('admin_init', array( $this, 'register_options_settings' ));
    }

    /**
     *
     * Create options page
     */

    public function create_options_page()
    {
        add_options_page(
            'Mailchimp',
            'Mailchimp',
            'manage_options',
            'mailchimp.php',
            array( $this, 'mailchimp_options_callback' )
        );
    }

    /**
     *
     * Register plugin settings
     */

    public function register_options_settings()
    {
        register_setting('mailchimp-settings-group', 'mailchimp_api_key');
        register_setting('mailchimp-settings-group', 'mailchimp_list_id');
    }

    /**
     *
     * Show options form
     */

    public function mailchimp_options_callback()
    {
        ?>
<div class="wrap">
	  <h1>Mailchimp settings</h1>
	  <form method="post" action="options.php">
			<?php settings_fields('mailchimp-settings-group'); ?>
			<?php do_settings_sections('mailchimp-settings-group'); ?>
			<table class="form-table">
				  <tr valign="top">
						 <th scope="row">API key:</th>
						 <td><input type="text" name="mailchimp_api_key" value="<?php echo esc_attr(get_option('mailchimp_api_key')); ?>" /></td>
				  </tr>
				  <tr valign="top">
						 <th scope="row">List ID:</th>
						 <td><input type="text" name="mailchimp_list_id" value="<?php echo esc_attr(get_option('mailchimp_list_id')); ?>"
							   /></td>
				  </tr>
			</table>
			<?php submit_button(); ?>
	  </form>
</div>
		  <?php
    }
}
