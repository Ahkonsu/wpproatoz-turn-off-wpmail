<?php
/*
Plugin Name: WPProAtoZ Email & IP Guardian
Description: Allows enabling/disabling of outgoing WordPress emails via wp_mail() with a customizable admin notice, plus IP restriction features for site access control.
Version: 1.9
Author: WPProAtoZ.com / John Overall
Requires at least: 6.0
Requires PHP: 8.0
License: GPLv2 or later
Author URI: https://wpproatoz.com
Plugin URI: https://wpproatoz.com/wp-pro-a-to-z-plugins-available/
Text Domain: wpproatoz-email-ip-guardian
Update URI: https://github.com/Ahkonsu/wpproatoz-turn-off-wpmail/releases
GitHub Plugin URI: https://github.com/Ahkonsu/wpproatoz-turn-off-wpmail/releases
GitHub Branch: main
*/

// Plugin update checker
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/Ahkonsu/wpproatoz-turn-off-wpmail/',
    __FILE__,
    'wpproatoz-email-ip-guardian'
);

$myUpdateChecker->setBranch('main');

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add settings menu item under Settings
function tec_add_admin_menu() {
    add_options_page(
        'Toggle Email Control',        // Page title
        'IP & Email Controls',               // Menu title
        'manage_options',              // Capability
        'toggle-email-control',        // Menu slug
        'tec_settings_page'            // Callback function
    );
}
add_action('admin_menu', 'tec_add_admin_menu');

// Add Settings link to plugin listing
function tec_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=toggle-email-control') . '">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tec_add_settings_link');

// Register settings
function tec_register_settings() {
    // Email settings
    register_setting('tec_settings_group', 'tec_disable_emails', array(
        'type' => 'boolean',
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ));
    register_setting('tec_settings_group', 'tec_notice_text', array(
        'type' => 'string',
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field'
    ));
    register_setting('tec_settings_group', 'tec_log_emails', array(
        'type' => 'boolean',
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ));
    register_setting('tec_settings_group', 'tec_notice_color', array(
        'type' => 'string',
        'default' => '#dc3232',
        'sanitize_callback' => 'sanitize_hex_color'
    ));
    
    // IP restriction settings
    register_setting('tec_settings_group', 'tec_enable_ip_restriction', array(
        'type' => 'boolean',
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean'
    ));
    register_setting('tec_settings_group', 'tec_allowed_ips', array(
        'type' => 'array',
        'default' => array($_SERVER['REMOTE_ADDR']),
        'sanitize_callback' => 'tec_sanitize_ips'
    ));
}
add_action('admin_init', 'tec_register_settings');

// Sanitize IP addresses
function tec_sanitize_ips($ips) {
    if (!is_array($ips)) {
        return array();
    }
    return array_filter(array_map('sanitize_text_field', $ips), function($ip) {
        return filter_var($ip, FILTER_VALIDATE_IP) || preg_match('/^\d+\.\d+\.\d+\.\d+-\d+\.\d+\.\d+\.\d+$/', $ip);
    });
}

// Update .htaccess file using WordPress Filesystem API
function tec_update_htaccess($allowed_ips) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    
    $htaccess_path = ABSPATH . '.htaccess';
    $marker = 'WPProAtoZ Email & IP Guardian Restrictions';
    
    $lines = array(
        "# $marker - BEGIN",
        "# Protecting dev sites from bots and other undesirables",
        "<IfModule mod_authz_core.c>"
    );
    
    foreach ($allowed_ips as $ip) {
        if (strpos($ip, '-') !== false) {
            list($start, $end) = explode('-', $ip);
            $lines[] = "Require ip $start $end";
        } else {
            $lines[] = "Require ip $ip";
        }
    }
    
    $lines[] = "</IfModule>";
    $lines[] = "<IfModule !mod_authz_core.c>";
    $lines[] = "Order Deny,Allow";
    $lines[] = "Deny from all";
    foreach ($allowed_ips as $ip) {
        if (strpos($ip, '-') !== false) {
            list($start, $end) = explode('-', $ip);
            $lines[] = "Allow from $start";
            $lines[] = "Allow from $end";
        } else {
            $lines[] = "Allow from $ip";
        }
    }
    $lines[] = "</IfModule>";
    $lines[] = "# $marker - END";
    
    $new_content = implode("\n", $lines);
    
    // Initialize WP Filesystem
    if (!WP_Filesystem()) {
        error_log('WPProAtoZ Email & IP Guardian: Failed to initialize WP Filesystem');
        return false;
    }
    
    global $wp_filesystem;
    
    if (!$wp_filesystem->exists($htaccess_path)) {
        return $wp_filesystem->put_contents($htaccess_path, $new_content, FS_CHMOD_FILE);
    }
    
    $content = $wp_filesystem->get_contents($htaccess_path);
    $start = strpos($content, "# $marker - BEGIN");
    $end = strpos($content, "# $marker - END");
    
    if ($start !== false && $end !== false) {
        $content = substr($content, 0, $start) . $new_content . substr($content, $end + strlen("# $marker - END"));
    } else {
        $content .= "\n" . $new_content;
    }
    
    $result = $wp_filesystem->put_contents($htaccess_path, $content, FS_CHMOD_FILE);
    
    if (!$result) {
        error_log('WPProAtoZ Email & IP Guardian: Failed to write to .htaccess');
    }
    
    return $result;
}

// Remove IP restrictions from .htaccess
function tec_remove_htaccess() {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    
    $htaccess_path = ABSPATH . '.htaccess';
    $marker = 'WPProAtoZ Email & IP Guardian Restrictions';
    
    if (!WP_Filesystem()) {
        error_log('WPProAtoZ Email & IP Guardian: Failed to initialize WP Filesystem for removal');
        return false;
    }
    
    global $wp_filesystem;
    
    if (!$wp_filesystem->exists($htaccess_path)) {
        return true;
    }
    
    $content = $wp_filesystem->get_contents($htaccess_path);
    $start = strpos($content, "# $marker - BEGIN");
    $end = strpos($content, "# $marker - END");
    
    if ($start !== false && $end !== false) {
        $content = substr($content, 0, $start) . substr($content, $end + strlen("# $marker - END"));
        $result = $wp_filesystem->put_contents($htaccess_path, $content, FS_CHMOD_FILE);
        if (!$result) {
            error_log('WPProAtoZ Email & IP Guardian: Failed to remove rules from .htaccess');
        }
        return $result;
    }
    
    return true;
}

// Settings page HTML
function tec_settings_page() {
    $notice_text = get_option('tec_notice_text', '');
    $notice_color = get_option('tec_notice_color', '#dc3232');
    $allowed_ips = get_option('tec_allowed_ips', array($_SERVER['REMOTE_ADDR']));
    ?>
    <div class="wrap">
        <h1>Toggle Email Control</h1>
        <div style="margin: 10px 0; padding: 10px; background: #fff; border: 1px solid #ccd0d4;">
            <strong>Status:</strong> Outgoing emails are currently 
            <?php echo get_option('tec_disable_emails', false) ? '<span style="color: red;">DISABLED</span>' : '<span style="color: green;">ENABLED</span>'; ?>
            | IP Restriction is currently 
            <?php echo get_option('tec_enable_ip_restriction', false) ? '<span style="color: red;">ENABLED</span>' : '<span style="color: green;">DISABLED</span>'; ?>
        </div>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('tec_settings_group');
            do_settings_sections('tec_settings_group');
            ?>
            <h2>Email Settings</h2>
            <table class="form-table">
                <tr>
                    <th><label for="tec_disable_emails">Disable Outgoing Emails</label></th>
                    <td>
                        <input type="checkbox" name="tec_disable_emails" id="tec_disable_emails" value="1" <?php checked(1, get_option('tec_disable_emails', 0)); ?> />
                        <p class="description">Check this box to disable all outgoing emails from WordPress.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="tec_notice_text">Custom Notice Text</label></th>
                    <td>
                        <input type="text" name="tec_notice_text" id="tec_notice_text" value="<?php echo esc_attr($notice_text); ?>" class="regular-text" />
                        <p class="description">Enter custom text for the admin notice. Leave blank for default.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="tec_notice_color">Notice Background Color</label></th>
                    <td>
                        <input type="color" name="tec_notice_color" id="tec_notice_color" value="<?php echo esc_attr($notice_color); ?>" />
                        <p class="description">Choose the background color for the admin notice.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="tec_log_emails">Log Blocked Emails</label></th>
                    <td>
                        <input type="checkbox" name="tec_log_emails" id="tec_log_emails" value="1" <?php checked(1, get_option('tec_log_emails', 0)); ?> />
                        <p class="description">Log details of blocked emails (stored in debug.log when WP_DEBUG is enabled).</p>
                    </td>
                </tr>
                <tr>
                    <th>Test Email</th>
                    <td>
                        <button type="button" id="tec_test_email" class="button">Send Test Email</button>
                        <span id="tec_test_result"></span>
                        <p class="description">Click to send a test email to <?php echo esc_html(get_option('admin_email')); ?>.</p>
                    </td>
                </tr>
            </table>

            <h2>IP Restriction Settings</h2>
            <table class="form-table">
                <tr>
                    <th><label for="tec_enable_ip_restriction">Enable IP Restriction</label></th>
                    <td>
                        <input type="checkbox" name="tec_enable_ip_restriction" id="tec_enable_ip_restriction" value="1" <?php checked(1, get_option('tec_enable_ip_restriction', 0)); ?> />
                        <p class="description">Check to restrict site access to only allowed IPs. Your current IP (<?php echo esc_html($_SERVER['REMOTE_ADDR']); ?>) will be automatically added.</p>
                    </td>
                </tr>
                <tr>
                    <th>Allowed IPs</th>
                    <td>
                        <p class="description">This plugin blocks access to all IPs except those listed below. Add IP addresses or ranges (e.g., 192.168.1.1-192.168.1.10).</p>
                        <div id="tec_ip_list">
                            <?php foreach ($allowed_ips as $index => $ip): ?>
                                <div style="margin-bottom: 5px;">
                                    <input type="text" name="tec_allowed_ips[]" value="<?php echo esc_attr($ip); ?>" class="regular-text" />
                                    <button type="button" class="button tec_remove_ip">Remove</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" id="tec_add_ip" class="button">Add IP</button>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#tec_test_email').click(function(e) {
            e.preventDefault();
            $('#tec_test_result').html('Sending...');
            $.post(ajaxurl, {
                action: 'tec_send_test_email'
            }, function(response) {
                $('#tec_test_result').html(response.data);
            });
        });
        
        $('#tec_add_ip').click(function() {
            $('#tec_ip_list').append(
                '<div style="margin-bottom: 5px;">' +
                '<input type="text" name="tec_allowed_ips[]" value="" class="regular-text" /> ' +
                '<button type="button" class="button tec_remove_ip">Remove</button>' +
                '</div>'
            );
        });
        
        $(document).on('click', '.tec_remove_ip', function() {
            if ($('#tec_ip_list div').length > 1) {
                $(this).parent().remove();
            }
        });
        
        $('#tec_enable_ip_restriction').change(function() {
            if ($(this).is(':checked')) {
                if (!confirm('Your current IP is <?php echo esc_js($_SERVER['REMOTE_ADDR']); ?>. Enable IP restriction now?')) {
                    $(this).prop('checked', false);
                }
            }
        });
    });
    </script>
    <?php
}

// Use the wp_mail filter to disable emails (first layer)
add_filter('wp_mail', 'tec_disable_wp_mail', PHP_INT_MAX);
function tec_disable_wp_mail($args) {
    if (get_option('tec_disable_emails', false)) {
        if (get_option('tec_log_emails', false) && defined('WP_DEBUG') && WP_DEBUG) {
            error_log('WPProAtoZ Email & IP Guardian - Blocked Email: ' . print_r($args, true));
        }
        return false;
    }
    return $args;
}

// Fallback: Use phpmailer_init to block emails at the mailer level
add_action('phpmailer_init', 'tec_block_phpmailer', PHP_INT_MAX);
function tec_block_phpmailer($phpmailer) {
    if (get_option('tec_disable_emails', false)) {
        $phpmailer->ClearAllRecipients();
        $phpmailer->ClearAttachments();
        $phpmailer->Body = '';
        $phpmailer->Subject = '';
    }
}

// Add admin notice when emails are disabled
add_action('admin_notices', 'tec_nomail_notice');
function tec_nomail_notice() {
    if (get_option('tec_disable_emails', false)) {
        $default_notice = '<strong>THIS DEV SITE HAS NO OUTGOING MAIL</strong> Mail from wp_mail() has been disabled for this site.';
        $custom_notice = get_option('tec_notice_text', '');
        $notice_text = !empty($custom_notice) ? $custom_notice : $default_notice;
        $notice_color = get_option('tec_notice_color', '#dc3232');
        ?>
        <div class="notice" style="background-color: <?php echo esc_attr($notice_color); ?>; color: white; border: none;">
            <p><?php echo wp_kses_post($notice_text); ?></p>
        </div>
        <?php
    }
}

// AJAX handler for test email
add_action('wp_ajax_tec_send_test_email', 'tec_send_test_email');
function tec_send_test_email() {
    $to = get_option('admin_email');
    $subject = 'WPProAtoZ Email & IP Guardian - Test Email';
    $message = 'This is a test email from WPProAtoZ Email & IP Guardian plugin.';
    $result = wp_mail($to, $subject, $message);
    
    wp_send_json_success($result ? 
        'Test email sent successfully!' : 
        'Test email blocked (as expected if emails are disabled).'
    );
}

// Update .htaccess on settings save
add_action('update_option_tec_enable_ip_restriction', function($old_value, $new_value) {
    if ($new_value) {
        tec_update_htaccess(get_option('tec_allowed_ips', array($_SERVER['REMOTE_ADDR'])));
    } else {
        tec_remove_htaccess();
    }
}, 10, 2);

add_action('update_option_tec_allowed_ips', function($old_value, $new_value) {
    if (get_option('tec_enable_ip_restriction', false)) {
        tec_update_htaccess($new_value);
    }
}, 10, 2);