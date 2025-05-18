# WooCommerce Notifications via Telegram Bot

[English](README.md) | [فارسی](docs/README-fa_IR.md)

A WordPress plugin that integrates WooCommerce with Telegram to send order status updates directly to your Telegram chat, group, super group.

## Features

- Send WooCommerce order status updates to Telegram
- Send Products out of stock or Limited quantity notification
- Support Telegram super group with different topic
- Support for both direct Telegram Bot API and Google Apps Script integration for countries have limit access to Telegram APIs
- Customizable message templates with dynamic placeholders
- Configurable order status triggers
- Test message functionality
- Easy setup through WooCommerce settings

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.2 or higher
- A Telegram Bot Token (get it from [@BotFather](https://t.me/botfather))

## Installation

1. Download the plugin files
2. Upload the plugin folder to your WordPress plugins directory (`wp-content/plugins/`)
3. Activate the plugin through the WordPress admin panel
4. Go to WooCommerce → Settings → Telegram Notifications to configure the plugin

## Configuration

1. Create a Telegram bot using [@BotFather](https://t.me/botfather) and get your bot token
2. Add your bot to a group/super group or start a chat with it
3. Get your chat ID or group ID
4. Configure the plugin settings in WooCommerce → Settings → Telegram Notifications:
   - Enter your Telegram Bot Token
   - Enter your Chat ID / Group ID
   - Select which order statuses should trigger notifications
   - Customize your message template
   - (Optional) Enable Google Apps Script integration

### Message Template Tags

The following tags can be used in your message template:

- `{order_id}` - The order ID
- `{status}` - The order status
- `{customer_name}` - Customer's full name
- `{total}` - Order total
- `{billing_email}` - Customer's billing email
- `{shipping_method}` - Selected shipping method
- `{payment_method}` - Selected payment method

## Google Apps Script Integration (Optional)

If you prefer to use Google Apps Script instead of direct Telegram API:

1. Create a new Google Apps Script project
2. Copy the provided script code into your project
3. Deploy the script as a web app
4. Enable the "Use Google Apps Script" option in the plugin settings
5. Enter your Google Script Web App URL

## Support

For support, please create an issue in the plugin's repository or contact the plugin author.

## Available Languages

- [English](README.md)
- [فارسی (Persian)](docs/README-fa_IR.md)

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Created by Satyar Salehian
