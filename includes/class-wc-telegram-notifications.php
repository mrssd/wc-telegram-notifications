<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Telegram_Notifications
{
    private $settings;

    public function init()
    {
        // Get settings with defaults
        $default_settings = array(
            'bot_token' => '',
            'chat_id' => '',
            'topic_id' => '',
            'order_statuses' => array('processing', 'completed'),
            'message_template' => "New order #{order_id}\nStatus: {status}\nCustomer: {customer_name}\nTotal: {total}",
            'use_google_script' => '',
            'google_script_url' => '',
            'enable_stock_notifications' => '',
            'topic_id_stock' => '',
        );
        $this->settings = wp_parse_args(get_option('wc_telegram_notifications_settings', array()), $default_settings);

        // Add settings page
        add_filter('woocommerce_get_settings_pages', array($this, 'add_settings_page'));

        // Hook into WooCommerce order status changes
        add_action('woocommerce_order_status_changed', array($this, 'handle_order_status_change'), 10, 4);

        // Add test message functionality
        add_action('admin_post_wc_telegram_test_message', array($this, 'send_test_message'));

        // Declare HPOS compatibility
        add_action('before_woocommerce_init', array($this, 'declare_hpos_compatibility'));

        // Add settings sanitization
        add_filter('woocommerce_admin_settings_sanitize_option_wc_telegram_notifications_settings', array($this, 'sanitize_settings'), 10, 3);

        add_action('woocommerce_low_stock', array($this, 'send_stock_notification'), 10, 1);
        add_action('woocommerce_no_stock', array($this, 'send_stock_notification'), 10, 1);

        add_action('wc_telegram_send_async_message', array($this, 'send_telegram_message'), 10, 2);
    }

    public function declare_hpos_compatibility()
    {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
        }
    }

    public function add_settings_page($settings)
    {
        $settings[] = include WC_TELEGRAM_NOTIFICATIONS_PLUGIN_DIR . 'includes/class-wc-telegram-notifications-settings.php';
        return $settings;
    }

    public function handle_order_status_change($order_id, $old_status, $new_status, $order)
    {
        // Debug log for status comparison

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WC Telegram Notifications: Order status changed to ' . $new_status . '. Triggers: ' . print_r($this->settings['order_statuses'], true));
        }
        //Check if the new status is in our selected statuses

        $new_status = "wc-" . $new_status;
        if (!in_array($new_status, $this->settings['order_statuses'])) {
            return;
        }

        $message = $this->prepare_message($order, $new_status);
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("message : " . $message);
        }



        $topic_id = !empty($this->settings['topic_id']) ? $this->settings['topic_id'] : null;

        if (function_exists('as_schedule_single_action')) {
            as_schedule_single_action(time(), 'wc_telegram_send_async_message', array(
                'message' => $message,
                'topic_id' => $topic_id,
            ));
        }

    }

    private function prepare_message($order, $status)
    {
        $message = $this->settings['message_template'];
        $products_list = '';
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            $products_list .= sprintf(
                // Translators: 1: Product name 2: Quantity 3: Price
                esc_html__('• %1$s x %2$s %3$d - %4$s', 'wc-telegram-notifications'),
                $item->get_name(),
                __('Quantity', 'wc-telegram-notifications'),
                $item->get_quantity(),
                str_replace('&nbsp;', ' ', wp_strip_all_tags(wc_price($item->get_total()))),
            );
        }
        // Replace placeholders
        $replacements = array(
            '{order_id}' => $order->get_id(),
            '{status}' => $status,
            '{date}' => $order->get_date_created()->format('Y-m-d'),
            '{time}' => $order->get_date_created()->format('H:i'),
            '{customer_name}' => $order->get_formatted_billing_full_name(),
            '{total}' => number_format((float) $order->get_total(), 0),
            '{total_formated}' => str_replace('&nbsp;', ' ', wp_strip_all_tags(wc_price($order->get_total(), array(
                'currency' => $order->get_currency()
            )))),
            '{currency}' => function_exists('get_woocommerce_currency_symbol')
                ? get_woocommerce_currency_symbol($order->get_currency())
                : $order->get_currency(),
            '{billing_email}' => $order->get_billing_email(),
            '{shipping_method}' => $order->get_shipping_method(),
            '{payment_method}' => $order->get_payment_method_title(),
            '{note}' => $order->get_customer_note(),
            '{products}' => $products_list
        );

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
    /**
     * Check product stock and send notification if empty
     */
    public function send_stock_notification($product)
    {
        if (!wc_string_to_bool($this->settings['enable_stock_notifications'])) {
            return;
        }

        $message = $this->prepare_stock_message($product);


        $topic_id = !empty($this->settings['topic_id_stock']) ? $this->settings['topic_id_stock'] : null;

        if (function_exists('as_schedule_single_action')) {
            as_schedule_single_action(time(), 'wc_telegram_send_async_message', array(
                'message' => $message,
                'topic_id' => $topic_id,
            ));
        }
    }
    private function prepare_stock_message($product)
    {

        $stock_quantity = $product->get_stock_quantity();
        $message_type = $stock_quantity <= 0 ?
            __('is out of stock', 'wc-telegram-notifications') :
            __('is getting out of stock', 'wc-telegram-notifications');
        // Translators: %s is replaced with the stock status (either "is out of stock" or "is getting out of stock")
        $message = sprintf(__('⚠️ Product %s', 'wc-telegram-notifications'), $message_type) . "\n\n";
        $message .= sprintf(
            // Translators: 1: Product name 2: Product ID 3: Price 4: Stock quantity
            __('Name: %1$s', 'wc-telegram-notifications') . "\n" .
            // Translators: 1: Product name 2: Product ID 3: Price 4: Stock quantity
            __('ID: #%2$s', 'wc-telegram-notifications') . "\n" .
            // Translators: 1: Product name 2: Product ID 3: Price 4: Stock quantity
            __('Price: %3$s', 'wc-telegram-notifications') . "\n" .
            // Translators: 1: Product name 2: Product ID 3: Price 4: Stock quantity
            __('Quantity: %4$s', 'wc-telegram-notifications') . "\n",
            $product->get_name(),
            $product->get_id(),
            str_replace('&nbsp;', ' ', wp_strip_all_tags(wc_price($product->get_price()))),
            $product->get_stock_quantity()
        );

        $message .= __("Product link :", 'wc-telegram-notifications') . "\n" .
            $product->get_permalink();


        return $message;
    }
    public function send_telegram_message($message, $topic_id = null)
    {
        if (empty($this->settings['bot_token']) || empty($this->settings['chat_id'])) {
            error_log('WC Telegram Notifications: Bot token or chat ID not configured');
            return false;
        }

        $use_google_script = isset($this->settings['use_google_script']) ? $this->settings['use_google_script'] : 'no';

        if ($use_google_script === 'yes' && !empty($this->settings['google_script_url'])) {
            return $this->send_via_google_script($message, $topic_id);
        } else {
            return $this->send_via_telegram_api($message, $topic_id);
        }
    }

    private function send_via_telegram_api($message, $topic_id = null)
    {
        $api_url = sprintf(
            'https://api.telegram.org/bot%s/sendMessage',
            $this->settings['bot_token']
        );

        $body = array(
            'chat_id' => $this->settings['chat_id'],
            'text' => $message,
            'parse_mode' => 'HTML'
        );

        // Add message_thread_id if topic_id is set
        if ($topic_id) {
            $body['message_thread_id'] = $topic_id;
        }

        $args = array(
            'body' => $body,
            'timeout' => 30
        );

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            error_log('WC Telegram Notifications Error: ' . $response->get_error_message());
            return false;
        }

        return true;
    }

    private function send_via_google_script($message, $topic_id = null)
    {
        $api_url = $this->settings['google_script_url'];

        $body = array(
            'token' => $this->settings['bot_token'],
            'method' => 'sendMessage',
            'chat_id' => $this->settings['chat_id'],
            'text' => $message,
            'parse_mode' => 'HTML'
        );

        // Add message_thread_id if topic_id is set

        if ($topic_id) {
            $body['topic_id'] = $topic_id;
        }

        $args = array(
            'body' => $body,
            'timeout' => 30
        );

        $response = wp_remote_post($api_url, $args);

        if (is_wp_error($response)) {
            error_log('WC Telegram Notifications Error: ' . $response->get_error_message());
            return false;
        }

        return true;
    }

    public function send_test_message()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wc-telegram-notifications'));
        }


        $test_message = __('\nThis is a test message from your WooCommerce Telegram Notifications plugin.', 'wc-telegram-notifications');

        if ($this->send_telegram_message($test_message)) {
            wp_redirect(add_query_arg('wc_telegram_test', 'success', admin_url('admin.php?page=wc-settings&tab=telegram_notifications')));
        } else {
            wp_redirect(add_query_arg('wc_telegram_test', 'error', admin_url('admin.php?page=wc-settings&tab=telegram_notifications')));
        }
        exit;
    }

    public function sanitize_settings($value, $option, $raw_value)
    {
        if (!is_array($value)) {
            return $value;
        }

        // Preserve newlines in message template
        if (isset($raw_value['message_template'])) {
            $value['message_template'] = sanitize_textarea_field($raw_value['message_template']);
        }
        // Ensure checkbox values are properly set
        $value['use_google_script'] = isset($raw_value['use_google_script']) ? 'yes' : 'no';
        $value['enable_stock_notifications'] = isset($raw_value['enable_stock_notifications']) ? 'yes' : 'no';

        return $value;
    }
}