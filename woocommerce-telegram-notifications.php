<?php
/**
 * Plugin Name: Order and Stock Notifications via Telegram Bot for WooCommerce
 * Plugin URI: 
 * Description: Send WooCommerce order status updates directly to Telegram using Bot API or Google Apps Script
 * Version: 1.0.0
 * Author: Satyar Salehian
 * Author URI: 
 * Text Domain: wc-telegram-notifications
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 9.8.0
 * WooCommerce-HPOS: true
 * License: GPLv2 or later
 */

// Load text domain for translations
add_action('plugins_loaded', 'wc_telegram_notifications_load_textdomain');

function wc_telegram_notifications_load_textdomain()
{
    load_plugin_textdomain(
        'wc-telegram-notifications',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}
// HPOS compatibility declaration
add_action('before_woocommerce_init', function () {
    if (class_exists('\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

// Define plugin constants
define('WC_TELEGRAM_NOTIFICATIONS_VERSION', '1.0.0');
define('WC_TELEGRAM_NOTIFICATIONS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WC_TELEGRAM_NOTIFICATIONS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WC_TELEGRAM_NOTIFICATIONS_PLUGIN_DIR . 'includes/class-wc-telegram-notifications.php';

// Initialize the plugin
function wc_telegram_notifications_init()
{
    $plugin = new WC_Telegram_Notifications();
    $plugin->init();
}
add_action('plugins_loaded', 'wc_telegram_notifications_init');

// Activation hook
register_activation_hook(__FILE__, 'wc_telegram_notifications_activate');
function wc_telegram_notifications_activate()
{
    // Add default options
    $existing_settings = get_option('wc_telegram_notifications_settings', array());
    $default_settings = array(
        'bot_token' => '',
        'chat_id' => '',
        'topic_id' => '',
        'order_statuses' => array('processing', 'completed'),
        'message_template' => "New order #{order_id}\nStatus: {status}\nCustomer: {customer_name}\nTotal: {total}",
        'use_google_script' => 'no',
        'google_script_url' => '',
        'enable_stock_notifications' => 'no',
        'topic_id_stock' => ''
    );

    // Merge with existing settings to preserve user values
    $settings = wp_parse_args($existing_settings, $default_settings);
    update_option('wc_telegram_notifications_settings', $settings);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'wc_telegram_notifications_deactivate');
function wc_telegram_notifications_deactivate()
{
    // Cleanup if needed
}

register_uninstall_hook(__FILE__, 'wc_telegram_notifications_uninstall');

function wc_telegram_notifications_uninstall()
{
    // Delete all plugin options
    delete_option('wc_telegram_notifications_settings');

    // (Optional) Delete scheduled actions if any
    if (function_exists('as_unschedule_all_actions')) {
        as_unschedule_all_actions('wc_telegram_send_async_message');
    }
}