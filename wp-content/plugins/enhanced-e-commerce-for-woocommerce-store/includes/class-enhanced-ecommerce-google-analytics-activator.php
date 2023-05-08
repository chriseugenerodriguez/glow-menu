<?php

/**
 * Fired during plugin activation
 *
 * @link       test.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics_Activator
 * @subpackage Enhanced_Ecommerce_Google_Analytics_Activator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Enhanced_Ecommerce_Google_Analytics_Activator
 * @subpackage Enhanced_Ecommerce_Google_Analytics_Activator/includes
 * @author     Tatvic
 */

class Enhanced_Ecommerce_Google_Analytics_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {  	
    	/*if (!is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
           wp_die(wp_sprintf("%s <br><a href='" . esc_url_raw(admin_url( 'plugins.php' )) . "'>&laquo; %s</a>", esc_html__("Hey, It seems WooCommerce plugin is not active on your wp-admin. Conversios.io - Google Analytics and Google Shopping plugin can only be activated if you have active WooCommerce plugin in your wp-admin.","enhanced-e-commerce-for-woocommerce-store"), esc_html__("Return to Plugins","enhanced-e-commerce-for-woocommerce-store")));
        }*/
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $TVC_Admin_Helper->update_app_status();
        $TVC_Admin_Helper->app_activity_detail("activate");         
    }
}
