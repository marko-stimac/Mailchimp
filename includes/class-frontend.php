<?php

/**
 * Load scripts, retrieve plugin settings and show form
 */

namespace ms\Mailchimp;

defined('ABSPATH') || exit;

class Frontend
{
    private $settings = array(
        'api_key' => '',
        'list_id' => '',
        'status' => 'pending',
    );

    public function __construct()
    {
        add_action('init', array($this, 'retrieve_db_values'));
        add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
        add_action('plugins_loaded', array($this, 'load_text_domain'));
        add_action('wp_ajax_subscribe_to_mailchimp', array($this, 'subscribe_to_mailchimp'));
        add_action('wp_ajax_nopriv_subscribe_to_mailchimp', array($this, 'subscribe_to_mailchimp'));
    }

    /**
     * Retrieve ACF fields from WordPress
     */
    public function retrieve_db_values()
    {
        $this->settings['api_key'] = get_option('mailchimp_api_key');
        $this->settings['list_id'] = get_option('mailchimp_list_id');
    }

    /**
     * Register scripts and styles
     */
    public function register_scripts()
    {
        wp_register_style('mailchimp', plugins_url('assets/mailchimp.css', __DIR__), null, PLUGIN_VERSION);
        wp_register_script('mailchimp', plugins_url('assets/mailchimp.js', __DIR__), array('jquery'), PLUGIN_VERSION, true);
        wp_localize_script(
            'mailchimp',
            'mailchimp',
            array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mailchimp_nonce'),
            )
        );
    }

    /**
     * Load text domain
     */
    public function load_text_domain()
    {
        load_plugin_textdomain('mailchimp-subscription', false, dirname(plugin_basename(__DIR__)) . '/languages');
    }

    /**
     * Send request to Mailchimp for subscription
     */
    public function subscribe_to_mailchimp()
    {
        $user_email = $_POST['user-email'];

        $args = array(
            'method' => 'PUT',
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode('user:' . $this->settings['api_key']),
            ),
            'body' => json_encode(array(
                'email_address' => $user_email,
                'status' => $this->settings['status'],
            )),
        );

        $response = wp_remote_post('https://' . substr($this->settings['api_key'], strpos($this->settings['api_key'], '-') + 1) . '.api.mailchimp.com/3.0/lists/' . $this->settings['list_id'] . '/members/' . md5(strtolower($user_email)), $args);

        // Return to JS
        echo json_encode($response['response']['code']);

        wp_die();

    }

    /**
     * Form component
     */
    public function show_component()
    {
        if (empty($this->settings['api_key']) || empty($this->settings['list_id'])) {
            echo 'You must set API key and List ID in Settings->Mailchimp!';
        }

        wp_enqueue_style('mailchimp');
        wp_enqueue_script('mailchimp');

        ob_start();?>
<form id="js-mailchimp-form" class="form-horizontal mailchimp" role="form" method="post">
	<div class="input-group">
		<label for="js-mailchimp-email" class="sr-only">
			<?php _e('Email address', 'mailchimp-subscription');?></label>
		<input type="email" id="js-mailchimp-email" class="form-control" name="user-email" placeholder="<?php _e('Email address', 'mailchimp-subscription');?>" required />
		<button type="submit" class="btn btn-primary" id="js-mailchimp-btn">
			<?php _e('Subscribe', 'mailchimp-subscription');?>
		</button>
	</div>
	<div id="mailchimp-messages">
		<div class="mailchimp__message mailchimp__message--success">
			<?php _e('You have successfully subscribed to the newsletter. Please check your email for confirmation.', 'mailchimp-subscription');?>
		</div>
		<div class="mailchimp__message mailchimp__message--fail">
			<?php _e('An error occurred, please try again later.', 'mailchimp-subscription');?>
		</div>
	</div>
</form>

<?php
return ob_get_clean();
    }
}