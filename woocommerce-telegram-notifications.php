<?php
/**
 * Plugin Name: Order and Stock Notifications via Telegram Bot for WooCommerce
 * Plugin URI: 
 * Description: Send WooCommerce order status updates directly to Telegram using Bot API or Google Apps Script
 * Version: 1.0.0
 * Author: Satyar Salehian
 * Author URI: 
 * Text Domain: order-and-stock-notifications-via-telegram-bot-for-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 9.8.0
 * WooCommerce-HPOS: true
 * License: GPLv2 or later
 */

// Load text domain for translations
add_action('plugins_loaded', 'wctelnot_load_textdomain');

function wctelnot_load_textdomain()
{
    load_plugin_textdomain(
        'order-and-stock-notifications-via-telegram-bot-for-woocommerce',
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
define('WCTELNOT_VERSION', '1.0.0');
define('WCTELNOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCTELNOT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once WCTELNOT_PLUGIN_DIR . 'includes/class-wctelnot-telegram-notifications.php';

// Initialize the plugin
function wctelnot_init()
{
    $plugin = new WCTELNOT_Telegram_Notifications();
    $plugin->init();
}
add_action('plugins_loaded', 'wctelnot_init');

// Activation hook
register_activation_hook(__FILE__, 'wctelnot_activate');
function wctelnot_activate()
{
    // Add default options
    $existing_settings = get_option('wctelnot_settings', array());
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
    update_option('wctelnot_settings', $settings);
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'wctelnot_deactivate');
function wctelnot_deactivate()
{
    // Cleanup if needed
}

register_uninstall_hook(__FILE__, 'wctelnot_uninstall');

function wctelnot_uninstall()
{
    // Delete all plugin options
    delete_option('wctelnot_settings');

    // (Optional) Delete scheduled actions if any
    if (function_exists('as_unschedule_all_actions')) {
        as_unschedule_all_actions('wctelnot_send_async_message');
    }
}