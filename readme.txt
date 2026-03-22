=== Order and Stock Notifications via Telegram Bot for WooCommerce ===
Contributors: mrssd
Donate link: https://paypal.me/satyarsd
Tags: telegram, woocommerce, notifications
Requires at least: 5.0
Tested up to: 6.9.4
Stable tag: 1.0.3
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
A lightweight plugin that sends WooCommerce order or stock updates to Telegram using a bot.

== Description ==
- Send WooCommerce order status updates to Telegram 
- Without adding any delay in order checkouts
- Send Products out of stock or Limited quantity notification
- Support Telegram super group with different topic
- Support for both direct Telegram Bot API and Google Apps Script integration for countries have limit access to Telegram APIs
- Customizable message templates with dynamic placeholders
- Configurable order status triggers
- Test message functionality
- Easy setup through WooCommerce settings

== Installation ==
Installation Video:
https://www.youtube.com/watch?v=Hq-jbnU_NRc

1. Download the plugin files
2. Upload the plugin folder to your WordPress plugins directory (`wp-content/plugins/`)
3. Activate the plugin through the WordPress admin panel
4. Go to WooCommerce → Settings → Telegram Notifications to configure the plugin

For using Goolge Script, use the source in Docs/google_script_source.js in Github :
https://github.com/mrssd/wc-telegram-notifications

For using Cloudflare Workers, use the source in Docs/cloudflare_workers.js in Github :
https://github.com/mrssd/wc-telegram-notifications

== Frequently Asked Questions ==
- Is this plugin works in countries with Telegram limitation ? Yes, you can use Google Script to use this plugin

== Changelog ==
= 1.0.0 =
* Initial release.
= 1.0.1 =
* Resolving sending message priority.
= 1.0.2 =
* Adding Cloudflare Workers for Telegram API's connection.
* Adding Billing Phone number tag in message template.
* Improving installation's document.
= 1.0.3 =
* Update the compatibility with newer version of WP



== Upgrade Notice ==
= 1.0.0 =
Initial release.
= 1.0.1 =
* Resolving sending message priority.
= 1.0.2 =
* Adding Cloudflare Workers for Telegram API's connection.
* Adding Billing Phone number tag in message template.
* Improving installation's document.
= 1.0.3 =
* Update the compatibility with newer version of WP

