<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.thedotstore.com/
 * @since             1.0.0
 * @package           Woo_Shipping_Display_Mode
 *
 * @wordpress-plugin
 * Plugin Name:       Shipping Method Display Style for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/woo-shipping-display-mode/
 * Description:       This plugin provides a configuration to display shipping methods as Radio button or select box on the checkout page.
 * Version:           3.7.6
 * Author:            theDotstore
 * Author URI:        https://www.thedotstore.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-shipping-display-mode
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'wsdm_fs' ) ) {
    wsdm_fs()->set_basename( false, __FILE__ );
    return;
}


if ( !function_exists( 'wsdm_fs' ) ) {
    // Create a helper function for easy SDK access.
    function wsdm_fs()
    {
        global  $wsdm_fs ;
        
        if ( !isset( $wsdm_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wsdm_fs = fs_dynamic_init( array(
                'id'             => '4753',
                'slug'           => 'woo-shipping-display-mode',
                'type'           => 'plugin',
                'public_key'     => 'pk_3004a64759ed9ac7042e91bf71969',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'first-path' => 'admin.php?page=wc-settings&tab=shipping_mode',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wsdm_fs;
    }
    
    // Init Freemius.
    wsdm_fs();
    // Signal that SDK was initiated.
    do_action( 'wsdm_fs_loaded' );
    wsdm_fs()->add_action( 'after_uninstall', 'wsdm_fs_uninstall_cleanup' );
}

if ( !defined( 'WSDM_PLUGIN_URL' ) ) {
    define( 'WSDM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-shipping-display-mode-activator.php
 */
function activate_woo_shipping_display_mode()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-shipping-display-mode-activator.php';
    Woo_Shipping_Display_Mode_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-shipping-display-mode-deactivator.php
 */
function deactivate_woo_shipping_display_mode()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-shipping-display-mode-deactivator.php';
    Woo_Shipping_Display_Mode_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_shipping_display_mode' );
register_deactivation_hook( __FILE__, 'deactivate_woo_shipping_display_mode' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-shipping-display-mode.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_shipping_display_mode()
{
    $plugin = new Woo_Shipping_Display_Mode();
    $plugin->run();
}

run_woo_shipping_display_mode();
/**
 * This plugin will return plugin directory path.
 *
 * @return string
 */
function myplugin_plugin_path()
{
    return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

/**
 * Load the plugin text domain for translation.
 *
 * @since    1.0.0
 */
function wsdm_load_plugin_textdomain()
{
    load_plugin_textdomain( 'woo-shipping-display-mode', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'wsdm_load_plugin_textdomain' );