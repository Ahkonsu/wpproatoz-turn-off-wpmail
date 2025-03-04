<?php
/*
Plugin Name: Toggle Email Control
Description: Allows enabling/disabling of outgoing WordPress emails via wp_mail() with a customizable admin notice.
Version: 1.5
Author: Your Name
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add settings menu item
function tec_add_admin_menu() {
    add_options_page(
        'Toggle Email Control',
        'Email Control',
        'manage_options',
        'toggle-email-control',
        'tec_settings_page'
    );
}
add_action('admin_menu', 'tec_add_admin_menu');

// Register settings
function tec_register_settings() {
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
}
add_action('admin_init', 'tec_register_settings');

// Settings page HTML
function tec_settings_page() {
    ?>
    <div class="wrap">
        <h1>Toggle Email Control</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('tec_settings_group');
            do_settings_sections('tec_settings_group');
            $notice_text = get_option('tec_notice_text', '');
            ?>
            <table class="form-table">
                <tr>
                    <th><label for="tec_disable_emails">Disable Outgoing Emails</label></th>
                    <td>
                        <input type="checkbox" name="tec_disable_emails" id="tec_disable_emails" value="1" <?php checked(1, get_option('tec_disable_emails', 0)); ?> />
                        <p class="description">Check this box to disable all outgoing emails from WordPress. An admin notice will appear when enabled.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="tec_notice_text">Custom Notice Text</label></th>
                    <td>
                        <input type="text" name="tec_notice_text" id="tec_notice_text" value="<?php echo esc_attr($notice_text); ?>" class="regular-text" />
                        <p class="description">Enter custom text for the admin notice. Leave blank to use the default: "<strong>THIS DEV SITE HAS NO OUTGOING MAIL</strong> Mail from wp_mail() has been disabled for this site."</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Use the wp_mail filter to disable emails (first layer)
add_filter('wp_mail', 'tec_disable_wp_mail', PHP_INT_MAX);
function tec_disable_wp_mail($args) {
    if (get_option('tec_disable_emails', false)) {
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
        ?>
        <div class="error notice">
            <p><?php echo wp_kses_post($notice_text); ?></p>
        </div>
        <?php
    }
}
