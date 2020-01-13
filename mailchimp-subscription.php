<?php
/**
 * Plugin Name: Mailchimp subscription
 * Description: Mailchimp subscription form
 * Version: 1.2.0
 * Author: Marko Štimac
 * Author URI: https://marko-stimac.github.io/
 * Text Domain: mailchimp-subscription
 * Domain Path: /languages
 */

namespace ms\Mailchimp;

defined('ABSPATH') || exit;
define(__NAMESPACE__ . '\PLUGIN_VERSION', '1.2.0');

require_once 'includes/class-backend.php';
require_once 'includes/class-frontend.php';

new Backend();
$mailchimp = new Frontend();
add_shortcode('mailchimp', array($mailchimp, 'show_component'));