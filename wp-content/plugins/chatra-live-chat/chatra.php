<?php
/**
 * Plugin Name: Chatra Live Chat + ChatBot + Cart Saver
 * Plugin URI: https://chatra.com/help/cms/wordpress/
 * Description: Chatra allows you to chat with your visitors, view the list of visitors who are currently online on your website and start a conversation manually or via configurable automatic targeted messages.
 * Author: Chatra
 * Author URI: https://chatra.com
 * Version: 1.0.11
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: chatra-live-chat
 * Domain Path: /languages
 */

// Add multilingual support
add_action('init', 'chatra_plugin_init');
function chatra_plugin_init()
{
    load_plugin_textdomain('chatra-live-chat', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Add settings page and register settings with WordPress
add_action('admin_menu', 'chatra_setup');
function chatra_setup()
{
    add_submenu_page('options-general.php', __('Chatra widget', 'chatra-live-chat'), __('Chatra widget', 'chatra-live-chat'), 'manage_options', 'options-chatra', 'chatra_settings');
    register_setting('chatra', 'chatra-code');
}

// Display settings page
function chatra_settings()
{
    echo "<h2>" . __('Chat widget setup', 'chatra-live-chat') . "</h2>";
    if (get_option('chatra-code')) {
        echo "<p>";
        printf(__('Seems like everything is OK! <br>Check your <a href="%s" target="_blank">website</a> to see if the live chat widget is present.<br>Log in to your <a href="%s" target="_blank">Chatra dashboard</a> to chat with your website visitors and manage preferences.', 'chatra-live-chat'), home_url(), 'https://app.chatra.io/?utm_source=WP&utm_campaign=WP');
        echo "</p>";
    } else {
        echo "<p>";
        printf(__('Signup for a free Chatra account at <a href="%s" target="_blank">app.chatra.io</a>,<br> then copy and paste <a href="%s" target="_blank">Widget code</a> from Chatra dashboard settings into the form below:', 'chatra-live-chat'), 'https://app.chatra.io/?utm_source=WP&utm_campaign=WP', 'https://app.chatra.io/settings/integrations/widget?utm_source=WP&utm_campaign=WP');
        echo "</p>";
    }
    echo "<form action=\"options.php\" method=\"POST\">";
    settings_fields('chatra');
    do_settings_sections('chatra');
    echo "<textarea cols=\"80\" rows=\"14\" name=\"chatra-code\">" . esc_attr(get_option('chatra-code')) . "</textarea>";
    submit_button();
    echo "</form>";
}

// Add the code to footer
add_action('wp_footer', 'add_chatra_code');
function add_chatra_code()
{
    echo get_option('chatra-code');
}
