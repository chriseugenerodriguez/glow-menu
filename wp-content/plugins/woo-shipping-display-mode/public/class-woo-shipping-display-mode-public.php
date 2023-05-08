<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/public
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Shipping_Display_Mode_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-shipping-display-mode-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-shipping-display-mode-public.js', array( 'jquery' ), $this->version, false );
	}

	public function wo_shipping_woocommerce_shipping_chosen_method_custom( $get_default_method_available_methods_chosen_method ) {
		return $get_default_method_available_methods_chosen_method;
	}

	public function myplugin_plugin_path() {

		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	public function wo_shipping_woocommerce_locate_template( $template, $template_name, $template_path ) {
		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = myplugin_plugin_path() . '/woocommerce/';

		$template = locate_template(
			array(

				$template_path . $template_name,
				$template_name,
			)
		);

		// Modification: Get the template from this plugin, if it exists
		if ( get_option( 'wsdm_override_custom_theme_template' ) === 'yes' ) {
			if ( file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}
		} else {
			if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}
		}

		// Use default template.
		if ( ! $template ) {
			$template = $_template;
		}
		// Return what we found
		return $template;
	}

}