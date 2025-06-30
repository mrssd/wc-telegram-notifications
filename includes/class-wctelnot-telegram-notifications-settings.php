<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCTELNOT_Settings_Telegram_Notifications extends WC_Settings_Page
{
    public function __construct()
    {
        $this->id = 'telegram_notifications';
        $this->label = __('Telegram Notifications', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce');

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
                'title' => __('Telegram Notifications Settings', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'type' => 'title',
                'desc' => sprintf(
                    /* translators: %s: Video tutorial link */
                    __('Configure your Telegram bot settings for WooCommerce order notifications. <a href="%s" target="_blank">Watch our tutorial video</a> to learn how to setup this plugin.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                    esc_url($video_url)
                ),
                'id' => 'wctelnot_settings'
            ),
            array(
                'title' => __('Telegram Bot Token', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Enter your Telegram Bot Token', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[bot_token]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Chat ID / Group ID', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Enter your Telegram Chat ID or Group ID', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce') . "<br>" .
                    __('- To find your Chat ID, use @userinfobot ', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce') . "<br>" .
                    __('- To find your Group ID, add this @getidsbot bot to your group. After getting group ID remmeber to remove this bot from the group.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce') . "<br>" .
                    __('- If you are using a Super Group, prepend the group ID with -100. For example, if your group ID is 123456789, use -100123456789.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce') . "<br>" .
                    __('- If you are using a normal group, prepend the group ID with -100. For example, if your group ID is 123456789, use -100123456789.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),

                'id' => 'wctelnot_settings[chat_id]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Orders Topic ID ', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Enter your Telegram topic ID for Super Groups or leave it empty if you use normal group or chat.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[topic_id]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Order Status Triggers', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Select which order statuses should trigger notifications', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[order_statuses]',
                'type' => 'multiselect',
                'options' => wc_get_order_statuses(),
                'default' => array('processing', 'completed'),
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Message Template', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Customize your notification message. Available tags: {order_id}, {status}, {customer_name}, {currency}, {total}, {total_formated}, {billing_email}, {billing_phone}, {shipping_method}, {payment_method}, {date}, {time}, {products}', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[message_template]',
                'type' => 'textarea',
                'default' => "New order #{order_id}\nStatus: {status}\nCustomer: {customer_name}\nTotal: {total}",
                'css' => 'width: 400px; height: 150px;'
            ),
            array(
                'title' => __('Use Google Apps Script or Cloudflare Workers', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Enable to use Google Apps Script or Cloudflare Workers instead of direct Telegram API', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[use_google_script]',
                'type' => 'checkbox',
                'default' => 'no',
            ),
            array(
                'title' => __('Google Script Web App or Cloudflare Workers URL', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Enter your Google Apps Script Web App or Cloudflare Workers URL', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[google_script_url]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'title' => __('Use Default API Bridge', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Enable to use the default bridge for sending notifications - For more security use your own API bridge', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[use_default_bridge]',
                'type' => 'checkbox',
                'default' => 'no',
            ),
            array(
                'title' => __('Enable Stock Notifications', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Send notifications when products become out of stock', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[enable_stock_notifications]',
                'default' => 'no',
                'type' => 'checkbox'
            ),
            array(
                'title' => __('Topic ID for Stock ', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'desc' => __('Enter your Telegram topic ID for Stock - if this does not set the defult ones will use or leave it empty', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wctelnot_settings[topic_id_stock]',
                'type' => 'text',
                'default' => '',
                'css' => 'width: 400px;'
            ),
            array(
                'type' => 'sectionend',
                'id' => 'wctelnot_settings'
            ),
            array(
                'title' => __('Test Notifications', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'type' => 'title',
                'desc' => __('Send a test message to verify your settings.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'id' => 'wc_telegram_notifications_test'
            ),
        );

        return apply_filters('wctelnot_settings', $settings);
    }

    public function output()
    {
        $settings = $this->get_settings();
        WC_Admin_Settings::output_fields($settings);

        // Add test message button
        ?>
        <p class="submit">
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=wctelnot_test_message'), 'wctelnot_test_message')); ?>"
                class="button button-primary">
                <?php esc_html_e('Send Test Message', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'); ?>
            </a>
        </p>

        <!-- Add JavaScript validation for conflicting options -->
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var $useGoogleScript = $('#wctelnot_settings\\[use_google_script\\]');
                var $useDefaultBridge = $('#wctelnot_settings\\[use_default_bridge\\]');

                function checkConflict() {
                    if ($useGoogleScript.is(':checked') && $useDefaultBridge.is(':checked')) {
                        alert('<?php echo esc_js(__('You cannot enable both "Use Google Apps Script or Cloudflare Workers" and "Use Default API Bridge" at the same time. Please choose only one option.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce')); ?>');
                        return false;
                    }
                    return true;
                }

                $useGoogleScript.on('change', function () {
                    if ($(this).is(':checked') && $useDefaultBridge.is(':checked')) {
                        $useDefaultBridge.prop('checked', false);
                        alert('<?php echo esc_js(__('Use Default API Bridge has been disabled because Use Google Apps Script is now enabled.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce')); ?>');
                    }
                });

                $useDefaultBridge.on('change', function () {
                    if ($(this).is(':checked') && $useGoogleScript.is(':checked')) {
                        $useGoogleScript.prop('checked', false);
                        alert('<?php echo esc_js(__('Use Google Apps Script has been disabled because Use Default API Bridge is now enabled.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce')); ?>');
                    }
                });

                // Validate on form submission
                $('form').on('submit', function (e) {
                    if (!checkConflict()) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
        </script>
        <?php

        // Display test message result
        if (isset($_GET['wctelnot_test'])) {
            if ($_GET['wctelnot_test'] === 'success') {
                echo wp_kses_post('<div class="notice notice-success"><p>' .
                    esc_html__('Test message sent successfully!', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce') .
                    '</p></div>');
            } else {
                echo wp_kses_post('<div class="notice notice-error"><p>' .
                    esc_html__('Failed to send test message. Please check your settings.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce') .
                    '</p></div>');
            }
        }
    }

    public function save()
    {
        // Get the posted data
        $use_google_script = isset($_POST['wctelnot_settings']['use_google_script']) ? 'yes' : 'no';
        $use_default_bridge = isset($_POST['wctelnot_settings']['use_default_bridge']) ? 'yes' : 'no';

        // Validate that both options are not enabled simultaneously
        if ($use_google_script === 'yes' && $use_default_bridge === 'yes') {
            // Add error notice
            add_settings_error(
                'wctelnot_settings',
                'conflicting_options',
                __('Error: You cannot enable both "Use Google Apps Script or Cloudflare Workers" and "Use Default API Bridge" at the same time. Please choose only one option.', 'order-and-stock-notifications-via-telegram-bot-for-woocommerce'),
                'error'
            );

            // Disable both options to prevent conflict
            $_POST['wctelnot_settings']['use_google_script'] = 'no';
            $_POST['wctelnot_settings']['use_default_bridge'] = 'no';
        }

        $settings = $this->get_settings();
        WC_Admin_Settings::save_fields($settings);
    }
}

return new WCTELNOT_Settings_Telegram_Notifications();