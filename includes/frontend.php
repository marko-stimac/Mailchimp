<?php

namespace bideja;

if (! defined('ABSPATH')) {
    exit;
}

class Mailchimp
{
    private $settings = array(
        'api_key' => '',
        'list_id' => '',
        'status'  => 'pending',
    );

    public function __construct()
    {
        add_action('init', array( $this, 'retrieve_db_values' ));
        add_action('wp_enqueue_scripts', array( $this, 'register_scripts' ));
        add_action('plugins_loaded', array( $this, 'load_text_domain' ));
        add_action('wp_ajax_bi_subscribe_user', array( $this, 'bi_subscribe_user' ));
        add_action('wp_ajax_nopriv_bi_subscribe_user', array( $this, 'bi_subscribe_user' ));
    }

    /**
     *
     * Retrieve ACF fields from WordPress
    *
     */

    public function retrieve_db_values()
    {
        $this->settings['api_key'] = get_option('mailchimp_api_key');
        $this->settings['list_id'] = get_option('mailchimp_list_id');
    }


    /**
     *
     * Register scripts and styles
    *
     */

    public function register_scripts()
    {
        wp_enqueue_style('mailchimp', plugins_url('../assets/css/mailchimp.css', __FILE__));
        wp_register_script('mailchimp-subscribe', plugins_url('../assets/js/subscribe.js', __FILE__), array( 'jquery' ), false, true);
        wp_localize_script(
            'mailchimp-subscribe',
            'biAjaxSubscribe',
            array(
                'url'   => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bi_subscribe_form_nonce'),
            )
        );
    }

    /**
     *
     * Load text domain
    *
     */

    public function load_text_domain()
    {
        load_plugin_textdomain('bideja-mailchimp', false, BIDEJA_PATH . '/languages/');
    }

    /**
     *
     * Send request to Mailchimp for subscription
    *
     */

    public function bi_subscribe_user()
    {
        $user_email = $_POST['user-email'];

        $data = array(
            'apikey'        => $this->settings['api_key'],
            'email_address' => $user_email,
            'status'        => $this->settings['status'],
        );

        $mch_api = curl_init();
        curl_setopt($mch_api, CURLOPT_URL, 'https://' . substr($this->settings['api_key'], strpos($this->settings['api_key'], '-') + 1) . '.api.mailchimp.com/3.0/lists/' . $this->settings['list_id'] . '/members/' . md5(strtolower($data['email_address'])));
        curl_setopt($mch_api, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Authorization: Basic ' . base64_encode('user:' . $this->settings['api_key']) ));
        curl_setopt($mch_api, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
        curl_setopt($mch_api, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($mch_api, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($mch_api, CURLOPT_TIMEOUT, 10);
        curl_setopt($mch_api, CURLOPT_POST, true);
        curl_setopt($mch_api, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($mch_api, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($mch_api);
        if (! $result) {
            $data = array(
                'type'    => 'error',
                'message' => 'Saving user for some reason didn\'t work as expected',
            );
            header('HTTP/1.1 400 Bad Request');
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($data);
        }
        wp_die();
    }


    /**
     *
     * Form component
    *
     */

    public function show_component()
    {
        if (empty($this->settings['api_key']) || empty($this->settings['list_id'])) {
            throw new \Exception('You must set API key and List ID in Settings->Mailchimp');
        }

        wp_enqueue_script('mailchimp-subscribe');

        ob_start(); ?>
<form id="js-mailchimp-form" class="form-horizontal mailchimp" role="form" method="post">
      <div class="input-group">
            <label for="js-mailchimp-email" class="sr-only">
                  <?php _e('Email address', 'bideja-mailchimp'); ?></label>
            <input type="email" id="js-mailchimp-email" class="form-control" name="user-email" placeholder="<?php _e('Email address', 'bideja-mailchimp'); ?>" required />
            <span class="input-group-btn">
                  <button type="submit" class="btn" id="js-mailchimp-btn">
                         <?php _e('Subscribe', 'bideja-mailchimp'); ?></button>
            </span>
      </div>
      <div id="mailchimp-messages">
            <div class="mailchimp__message mailchimp__message--success">
                  <?php _e('You have successfully subscribed to the newsletter. Please check your email for confirmation.', 'bideja-mailchimp'); ?>
            </div>
            <div class="mailchimp__message mailchimp__message--fail">
                  <?php _e('An error occurred, please try again later.', 'bideja-mailchimp'); ?>
            </div>
      </div>
</form>

          <?php
        return ob_get_clean();
    }
}
