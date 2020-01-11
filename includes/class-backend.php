<?php

/**
 * Create plugin page at Settings->Mailchimp and register plugin settings
 */

namespace ms\Mailchimp;

defined('ABSPATH') || exit;

class Backend
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'create_options_page'));
        add_action('admin_init', array($this, 'register_options_settings'));
        add_filter('plugin_row_meta', array($this, 'modify_plugin_meta'), 10, 2);
    }

    /**
     * Create options page
     */
    public function create_options_page()
    {
        add_options_page(
            'Mailchimp',
            'Mailchimp',
            'manage_options',
            'mailchimp.php',
            array($this, 'createOptionsFields')
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
     * Add link to readme file on installed plugin listing
     */
    public function modify_plugin_meta($links_array, $file)
    {
        if (strpos($file, 'mailchimp-subscription.php') !== false) {
            $links_array[] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=mailchimp.php')) . '">Settings</a>';
        }
        return $links_array;
    }

    /**
     *
     * Show options form
     */
    public function createOptionsFields()
    {
        ?>
<div class="wrap">
	<h1>Mailchimp settings</h1>
	<p>In order to generate this keys log in to your <a href="https://www.mailchimp.com/" target="_blank">Mailchimp account</a></p>
	<p>To get <strong>API key</strong> click on your username->Account, then go to Extras->API keys and generate a new key.</p>
	<p>To get <strong>Audience ID</strong> go to Audience, on the right click View Contacts then Settings->Audience name and defaults and copy Audience ID.</p>
	<p></p>
	<form method="post" action="options.php">
		<?php settings_fields('mailchimp-settings-group');?>
		<?php do_settings_sections('mailchimp-settings-group');?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">API key:</th>
				<td><input type="text" name="mailchimp_api_key" value="<?php echo esc_attr(get_option('mailchimp_api_key')); ?>" /></td>
			</tr>
			<tr valign="top">
				<th scope="row">Audience ID:</th>
				<td><input type="text" name="mailchimp_list_id" value="<?php echo esc_attr(get_option('mailchimp_list_id')); ?>" /></td>
			</tr>
		</table>
		<?php submit_button();?>
	</form>
</div>
<?php
}
}