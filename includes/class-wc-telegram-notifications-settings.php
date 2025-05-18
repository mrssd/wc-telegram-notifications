<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Settings_Telegram_Notifications extends WC_Settings_Page
{
    public function __construct()
    {
        $this->id = 'telegram_notifications';
        $this->label = __('Telegram Notifications', 'wc-telegram-notifications');

        parent::__construct();
    }
    private function get_tutorial_video_url()
    {
        $locale = get_locale();

        $video_urls = array(
            'en_US' => 'https://www.youtube.com/watch?v=Hq-jbnU_NRc',
            'fa_IR' => 'https://www.youtube.com/watch?v=8KppO5pUkdI',
            // Add more languages as needed
        );

        return isset($video_urls[$locale]) ? $video_urls[$locale] : $video_urls['en_US']; // fallback to English
    }
    public function get_settings()
    {
        $video_url = $this->get_tutorial_video_url();
        $settings = array(
            array(
                'title' => __('Telegram Notifications Settings', 'wc-telegram-notifications'),
                'type' => 'title',
                'desc' => sprintf(
                    /* translators: %s: Video tutorial link */
                    __('Configure your Telegram bot settings for WooCommerce order notifications. <a href="%s" target="_blank">Watch our tutorial video</a> to learn how to setup this plugin.', 'wc-telegram-notifications'),
                    esc_url($video_url)
                ),
                'id' => 'wc_telegram_notifications_settings'
            ),
            array(
                'title' => __('Telegram Bot Token', 'wc-telegram-notifications'),
                'desc' => __('Enter your Telegram Bot Token', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[bot_token]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Chat ID / Group ID', 'wc-telegram-notifications'),
                'desc' => __('Enter your Telegram Chat ID or Group ID', 'wc-telegram-notifications') . "<br>" .
                    __('- To find your Chat ID, use @userinfobot ', 'wc-telegram-notifications') . "<br>" .
                    __('- To find your Group ID, add this @getidsbot bot to your group. After getting group ID remmeber to remove this bot from the group.', 'wc-telegram-notifications') . "<br>" .
                    __('- If you are using a Super Group, prepend the group ID with -100. For example, if your group ID is 123456789, use -100123456789.', 'wc-telegram-notifications') . "<br>" .
                    __('- If you are using a normal group, prepend the group ID with -100. For example, if your group ID is 123456789, use -100123456789.', 'wc-telegram-notifications'),

                'id' => 'wc_telegram_notifications_settings[chat_id]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Orders Topic ID ', 'wc-telegram-notifications'),
                'desc' => __('Enter your Telegram topic ID for Super Groups or leave it empty if you use normal group or chat.', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[topic_id]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Order Status Triggers', 'wc-telegram-notifications'),
                'desc' => __('Select which order statuses should trigger notifications', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[order_statuses]',
                'type' => 'multiselect',
                'options' => wc_get_order_statuses(),
                'default' => array('processing', 'completed'),
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Message Template', 'wc-telegram-notifications'),
                'desc' => __('Customize your notification message. Available tags: {order_id}, {status}, {customer_name}, {currency}, {total}, {total_formated}, {billing_email}, {shipping_method}, {payment_method}, {date}, {time}, {products}', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[message_template]',
                'type' => 'textarea',
                'default' => "New order #{order_id}\nStatus: {status}\nCustomer: {customer_name}\nTotal: {total}",
                'css' => 'width: 400px; height: 150px;'
            ),
            array(
                'title' => __('Use Google Apps Script', 'wc-telegram-notifications'),
                'desc' => __('Enable to use Google Apps Script instead of direct Telegram API', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[use_google_script]',
                'type' => 'checkbox',
                'default' => 'no',
            ),
            array(
                'title' => __('Google Script Web App URL', 'wc-telegram-notifications'),
                'desc' => __('Enter your Google Apps Script Web App URL', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[google_script_url]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),


            array(
                'title' => __('Enable Stock Notifications', 'wc-telegram-notifications'),
                'desc' => __('Send notifications when products become out of stock', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[enable_stock_notifications]',
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __('Topic ID for Stock ', 'wc-telegram-notifications'),
                'desc' => __('Enter your Telegram topic ID for Stock - if this does not set the defult ones will use or leave it empty', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_settings[topic_id_stock]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'type' => 'sectionend',
                'id' => 'wc_telegram_notifications_settings'
            ),
            array(
                'title' => __('Test Notifications', 'wc-telegram-notifications'),
                'type' => 'title',
                'desc' => __('Send a test message to verify your settings.', 'wc-telegram-notifications'),
                'id' => 'wc_telegram_notifications_test'
            ),
        );

        return apply_filters('wc_telegram_notifications_settings', $settings);
    }

    public function output()
    {
        $settings = $this->get_settings();
        WC_Admin_Settings::output_fields($settings);

        // Add test message button
        ?>
        <p class="submit">
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=wc_telegram_test_message'), 'wc_telegram_test_message')); ?>"
                class="button button-primary">
                <?php esc_html_e('Send Test Message', 'wc-telegram-notifications'); ?>
            </a>
        </p>
        <?php

        // Display test message result
        if (isset($_GET['wc_telegram_test'])) {
            if ($_GET['wc_telegram_test'] === 'success') {
                echo wp_kses_post('<div class="notice notice-success"><p>' .
                    esc_html__('Test message sent successfully!', 'wc-telegram-notifications') .
                    '</p></div>');
            } else {
                echo wp_kses_post('<div class="notice notice-error"><p>' .
                    esc_html__('Failed to send test message. Please check your settings.', 'wc-telegram-notifications') .
                    '</p></div>');
            }
        }
    }

    public function save()
    {
        $settings = $this->get_settings();
        WC_Admin_Settings::save_fields($settings);
    }
}

return new WC_Settings_Telegram_Notifications();