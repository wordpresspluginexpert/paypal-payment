<?php

/**
 *
 * @wordpress-plugin
 * Plugin Name:       PayPal Payment
 * Plugin URI:        http://www.mbjtechnolabs.com
 * Description:       Easy to use add a PayPal Payment button as a Page, Post and Widget with a shortcode
 * Version:           1.0.3
 * Author:            phpwebcreators
 * Author URI:        http://www.mbjtechnolabs.com
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       paypal-payment
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!defined('PPW_PLUGIN_URL'))
    define('PPW_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('PPW_PLUGIN_DIR'))
    define('PPW_PLUGIN_DIR', dirname(__FILE__));

/**
 * define plugin basename
 */
if (!defined('PPW_PLUGIN_BASENAME')) {
    define('PPW_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-paypal-payment-activator.php
 */
function activate_paypal_payment() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-paypal-payment-activator.php';
    MBJ_PayPal_Payment_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-paypal-payment-deactivator.php
 */
function deactivate_paypal_payment() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-paypal-payment-deactivator.php';
    MBJ_PayPal_Payment_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_paypal_payment');
register_deactivation_hook(__FILE__, 'deactivate_paypal_payment');

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-paypal-payment.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_paypal_payment() {

    $plugin = new MBJ_PayPal_Payment();
    $plugin->run();
}

run_paypal_payment();
