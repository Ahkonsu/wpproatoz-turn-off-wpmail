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
Contributors: [Your Name or Username]
Tags: email, wp_mail, disable email, admin notice, development
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.4
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Easily toggle outgoing WordPress emails on or off with a simple checkbox, complete with an admin notice when emails are disabled.

== Description ==

**Toggle Email Control** is a lightweight plugin designed for developers and site administrators who need to disable all outgoing emails from a WordPress site—perfect for development, staging, or testing environments. With a single checkbox in the admin settings, you can block emails sent via `wp_mail()` and receive a clear admin notice when the feature is active.

Key features:
- Disable all outgoing emails with one click.
- Displays a prominent admin notice when emails are disabled.
- Uses both `wp_mail` filter and `phpmailer_init` fallback for reliable email blocking.
- Ideal for preventing unwanted emails during testing or development.

No need to edit core files or `wp-config.php`—everything is managed through a simple settings page.

== Installation ==

1. Upload the `toggle-email-control` folder to the `/wp-content/plugins/` directory, or install the plugin directly through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Email Control** to configure the plugin.
4. Check the "Disable Outgoing Emails" box to block all emails, or leave it unchecked to allow normal email functionality.

== Usage ==

1. Go to **Settings > Email Control** in your WordPress admin dashboard.
2. Check the box labeled "Disable Outgoing Emails" and click "Save Changes" to stop all emails from being sent.
3. When enabled, a red notice will appear at the top of your admin screens: "THIS DEV SITE HAS NO OUTGOING MAIL".
4. Uncheck the box and save to re-enable email sending; the notice will disappear.

Tested with common email triggers like password resets, user registrations, and contact forms.

== Frequently Asked Questions ==

= Why would I want to disable outgoing emails? =
This is useful in development or staging environments where you don’t want test emails sent to real users (e.g., password resets or order confirmations).

= Does this block all emails? =
Yes, it blocks all emails sent through WordPress’s `wp_mail()` function, including those from core, themes, and plugins. The `phpmailer_init` fallback ensures reliability even if a plugin bypasses the standard filter.

= What happens when emails are disabled? =
Emails are silently blocked, and functions calling `wp_mail()` will still return as if the email was sent successfully, preventing errors in dependent plugins.

= Can I see which emails were blocked? =
The current version doesn’t log blocked emails, but you can use a plugin like "Email Log" alongside this to monitor email activity.

= Will this conflict with other email plugins? =
It’s designed to work with most setups. The high-priority filters (`PHP_INT_MAX`) help ensure it runs last, but test with your specific plugins (e.g., SMTP or transactional email services) to confirm compatibility.

== Screenshots ==

1. The settings page under "Settings > Email Control" with the toggle checkbox.
2. The admin notice displayed when outgoing emails are disabled.

== Changelog ==

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

= 1.4 =
This update ensures emails are fully blocked with a `phpmailer_init` fallback. Recommended for all users to ensure reliable operation.

== License ==

This plugin is licensed under the GPLv2 or later. You are free to use, modify, and distribute it as per the license terms.

== Credits ==

Developed with assistance from xAI’s Grok for debugging and optimization.
