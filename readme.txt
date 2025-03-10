 ___  _     _                         
 / _ \| |   | |                        
/ /_\ \ |__ | | _____  _ __  ___ _   _ 
|  _  | '_ \| |/ / _ \| '_ \/ __| | | |
| | | | | | |   < (_) | | | \__ \ |_| |
\_| |_/_| |_|_|\_\___/|_| |_|___/\__,_|
                                       
                                      
		
		
		\||/
                |  @___oo
      /\  /\   / (__,,,,|
     ) /^\) ^\/ _)
     )   /^\/   _)
     )   _ /  / _)
 /\  )/\/ ||  | )_)
<  >      |(,,) )__)
 ||      /    \)___)\
 | \____(      )___) )___
  \______(_______;;; __;;;
=== Toggle Email Control ===
Contributors: WPProAtoz.com
Tags: email, wp_mail, disable email, admin notice, development
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.5
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily toggle outgoing WordPress emails on or off with a simple checkbox, complete with a customizable admin notice when emails are disabled.

== Description ==

**Toggle Email Control** is a lightweight plugin designed for developers and site administrators who need to disable all outgoing emails from a WordPress site—perfect for development, staging, or testing environments. With a single checkbox in the admin settings, you can block emails sent via `wp_mail()` and customize the admin notice that appears when the feature is active.

Key features:
- Disable all outgoing emails with one click.
- Displays a customizable admin notice when emails are disabled (default: "<strong>THIS DEV SITE HAS NO OUTGOING MAIL</strong> Mail from wp_mail() has been disabled for this site.").
- Uses both `wp_mail` filter and `phpmailer_init` fallback for reliable email blocking.
- Ideal for preventing unwanted emails during testing or development.

No need to edit core files or `wp-config.php`—everything is managed through a simple settings page.

== Installation ==

1. Upload the `toggle-email-control` folder to the `/wp-content/plugins/` directory, or install the plugin directly through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Email Control** to configure the plugin.

== Usage ==

1. Go to **Settings > Email Control** in your WordPress admin dashboard.
2. Check the box labeled "Disable Outgoing Emails" and click "Save Changes" to stop all emails from being sent.
3. (Optional) Enter custom text in the "Custom Notice Text" field to change the admin notice. Leave blank to use the default.
4. When enabled, a red notice will appear at the top of your admin screens with your custom or default message.
5. Uncheck the box and save to re-enable email sending; the notice will disappear.

Tested with common email triggers like password resets, user registrations, and contact forms.

== Frequently Asked Questions ==

= Why would I want to disable outgoing emails? =
This is useful in development or staging environments where you don’t want test emails sent to real users (e.g., password resets or order confirmations).

= Can I customize the admin notice? =
Yes! In the settings, you can enter custom text for the notice. If left blank, it defaults to: "<strong>THIS DEV SITE HAS NO OUTGOING MAIL</strong> Mail from wp_mail() has been disabled for this site."

= Does this block all emails? =
Yes, it blocks all emails sent through WordPress’s `wp_mail()` function, including those from core, themes, and plugins. The `phpmailer_init` fallback ensures reliability even if a plugin bypasses the standard filter.

= What happens when emails are disabled? =
Emails are silently blocked, and functions calling `wp_mail()` will still return as if the email was sent successfully, preventing errors in dependent plugins.

= Can I see which emails were blocked? =
The current version doesn’t log blocked emails, but you can use a plugin like "Email Log" alongside this to monitor email activity.

== Screenshots ==

1. The settings page under "Settings > Email Control" with the toggle checkbox and custom notice text field.
2. The admin notice displayed when outgoing emails are disabled (default or custom text).

== Changelog ==

= 1.5 =
* Added option to customize the admin notice text with a default fallback.

= 1.4 =
* Added `phpmailer_init` fallback to ensure reliable email blocking.
* Fixed issue where emails were still sent in some cases.

= 1.3 =
* Adjusted `wp_mail` filter to return `false` when emails are disabled.

= 1.2 =
* Fixed infinite recursion issue by switching from pluggable function to `wp_mail` filter.

= 1.1 =
* Added admin notice that toggles with the email disable setting.

= 1.0 =
* Initial release with basic email toggle functionality.

== Upgrade Notice ==

= 1.5 =
This update adds the ability to customize the admin notice text. Update to personalize your experience!

== License ==

This plugin is licensed under the GPLv2 or later. You are free to use, modify, and distribute it as per the license terms.

== Credits ==

Developed with assistance from xAI’s Grok for debugging and optimization.
