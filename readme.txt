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

=== WPProAtoZ Email & IP Guardian ===
Contributors: WPProAtoZ.com
Tags: email, wp_mail, disable email, ip restriction, admin notice, development, cidr
Requires at least: 6.0
Tested up to: 6.7.2
Stable tag: 1.9.1
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
A lightweight plugin to toggle outgoing WordPress emails and restrict site access by IP, perfect for development and testing environments.

== Description ==
**WPProAtoZ Email & IP Guardian** is a lightweight plugin designed for developers and site administrators who need to manage outgoing emails and site access in WordPress. With a simple settings page under "Settings" > "IP & Email Controls", you can disable all emails sent via `wp_mail()` and restrict site access to specific IPs or IP ranges using `.htaccess`. Ideal for development, staging, or testing environments, this plugin prevents unwanted emails and unauthorized access with minimal configuration.

Key features:
- Disable all outgoing emails with a single checkbox.
- Customize the admin notice displayed when emails are disabled (default: "<strong>THIS DEV SITE HAS NO OUTGOING MAIL</strong> Mail from wp_mail() has been disabled for this site.").
- Log blocked emails to debug.log when WP_DEBUG is enabled.
- Test email functionality with a built-in button.
- Restrict site access to allowed IPs or IP ranges via `.htaccess`.
- Dynamic IP management with add/remove functionality and optional notes.
- CIDR notation support (e.g., 192.168.1.0/24).
- Uses both `wp_mail` filter and `phpmailer_init` fallback for reliable email blocking.
- Documentation tab for easy updates via external HTML file.

No need to edit core files or `wp-config.php`—everything is managed through an intuitive settings page.

== Installation ==
1. Upload the `wpproatoz-email-ip-guardian` folder to the `/wp-content/plugins/` directory, or install the plugin directly through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > IP & Email Controls** to configure the plugin.

== Usage ==
1. Go to **Settings > IP & Email Controls** in your WordPress admin dashboard.
2. **Email Settings**:
   - Check "Disable Outgoing Emails" and save to stop all emails. A customizable admin notice will appear.
   - Adjust the "Custom Notice Text" field (optional) and "Notice Background Color".
   - Enable "Log Blocked Emails" to track blocked emails in debug.log (requires WP_DEBUG).
   - Click "Send Test Email" to test email functionality.
3. **IP Restriction Settings**:
   - Check "Enable IP Restriction" to limit site access to listed IPs. Your current IP is auto-added.
   - Add or remove IPs/ranges/CIDRs (e.g., 192.168.1.1-192.168.1.10 or 192.168.1.0/24) in the "Allowed IPs" section, with optional notes.
   - Save changes to update `.htaccess`.
4. To revert, uncheck the respective options and save.
5. Switch to the **Documentation** tab for guides and updates (loads from `documentation.html` in the plugin directory).

Tested with common email triggers (e.g., password resets, registrations) and IP restrictions on Apache servers.

== Frequently Asked Questions ==
= Why would I want to disable outgoing emails? =
Useful in development or staging environments to prevent test emails from reaching real users.

= Can I customize the admin notice? =
Yes, enter custom text and choose a background color in the settings. Defaults apply if left blank.

= Does this block all emails? =
Yes, it blocks all `wp_mail()` emails using a filter and `phpmailer_init` fallback for reliability.

= What happens when emails are disabled? =
Emails are silently blocked, and `wp_mail()` returns as if successful, avoiding plugin errors.

= Can I see which emails were blocked? =
Enable "Log Blocked Emails" to log details to debug.log (requires WP_DEBUG).

= How does IP restriction work? =
It writes rules to `.htaccess` to allow only specified IPs/ranges/CIDRs, blocking all others.

= Does it support IP ranges or CIDR? =
Yes, enter ranges like "192.168.1.1-192.168.1.10" or CIDRs like "192.168.1.0/24" in the Allowed IPs section.

= What are IP notes for? =
Optional labels (e.g., "Office VPN") to track what each IP is for—doesn't affect restrictions.

= How do I update documentation? =
Edit `documentation.html` in the plugin directory; changes load on page refresh.

== Screenshots ==
1. The settings page under "Settings > IP & Email Controls" with email and IP options.
2. The admin notice displayed when outgoing emails are disabled (default or custom).
3. IP list with notes and CIDR example.

== Changelog ==
= 1.9.1 =
* Fixed initial .htaccess write for IP restrictions on first enable.
* Added CIDR notation support (e.g., 192.168.1.0/24) for efficient IP ranges.
* Added optional notes field for each allowed IP entry, with balanced UI layout.
* Added Documentation tab to settings page, loading from documentation.html for easy updates.

= 1.9 =
* Renamed plugin to "WPProAtoZ Email & IP Guardian" for better branding.
* Added "Settings" link to plugin listing for quick access.
* Updated text domain and `.htaccess` marker to reflect new name.

= 1.8-beta =
* Added IP restriction feature with dynamic IP/range management via `.htaccess`.
* Enhanced email blocking with dual-layer approach (`wp_mail` filter and `phpmailer_init`).

= 1.7 =
* Adjusted plugin name consistency and minor tweaks.

= 1.6 =
* Added screenshot to documentation.

= 1.5 =
* Introduced customizable admin notice text with a default fallback.

= 1.4 =
* Added `phpmailer_init` fallback for reliable email blocking.
* Fixed issue where emails were sent in some edge cases.

= 1.3 =
* Modified `wp_mail` filter to return `false` when emails are disabled.

= 1.2 =
* Resolved infinite recursion by switching to `wp_mail` filter from pluggable function.

= 1.1 =
* Added admin notice that toggles with email disable setting.

= 1.0 =
* Initial release with basic email toggle functionality.

== Upgrade Notice ==
= 1.9.1 =
Update for enhanced IP features (CIDR, notes), fixed initial setup, and new Documentation tab.

= 1.9 =
Update for a new name, improved branding, and a convenient "Settings" link in the plugin listing.

= 1.8-beta =
Major update adding IP restriction features—test thoroughly in a staging environment.

= 1.5 =
Adds customizable notice text—update to personalize your admin experience!

== License ==
This plugin is licensed under the GPLv2 or later. You are free to use, modify, and distribute it as per the license terms.

== Credits ==
Developed with assistance from xAI’s Grok for debugging and optimization.