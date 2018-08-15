<?php
/*
Plugin Name: _Burza ideja - Mailchimp
Description: Subscribe korisnika na Mailchimp
Version: 1.1
Text Domain: bideja-mailchimp
*/

namespace bideja;

if (!defined('ABSPATH')) {
    exit;
}

DEFINE('BIDEJA_PATH', plugin_basename(dirname(__FILE__)));

require_once 'includes/register-settings.php';
require_once 'includes/mailchimp.php';

new MailchimpBackend();
$mailchimp = new Mailchimp();
add_shortcode('mailchimp', array($mailchimp, 'show_component'));