<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/includes
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Shipping_Display_Mode {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_Shipping_Display_Mode_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'woo-shipping-display-mode';
		$this->version     = '1.0.0';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woo_Shipping_Display_Mode_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_Shipping_Display_Mode_i18n. Defines internationalization functionality.
	 * - Woo_Shipping_Display_Mode_Admin. Defines all hooks for the admin area.
	 * - Woo_Shipping_Display_Mode_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-shipping-display-mode-i18n.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-shipping-display-mode-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-shipping-display-mode-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-shipping-display-mode-public.php';

		/**
		 * User Feedback popup notice
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-shipping-display-mode-user-feedback.php';		

		$this->loader = new Woo_Shipping_Display_Mode_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woo_Shipping_Display_Mode_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 10 );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 10 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'woo_shipping_admin_init_own' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'wo_shipping_welcome_shipping_display_mode_screen_do_activation_redirect' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wo_shipping_welcome_pages_screen_shipping_display_mode' );
		$this->loader->add_action( 'wo_shipping_woocommerce_shipping_display_mode_about', $plugin_admin, 'wo_shipping_woocommerce_shipping_display_mode_about' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wo_shipping_adjust_the_wp_menu', 999 );
		$this->loader->add_action( 'plugin_row_meta', $plugin_admin, 'wsdm_plugin_row_meta',10,2 );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woo_Shipping_Display_Mode_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'woocommerce_locate_template', $plugin_public, 'wo_shipping_woocommerce_locate_template', 5, 3 );
		$this->loader->add_filter( 'woocommerce_shipping_chosen_method', $plugin_public, 'wo_shipping_woocommerce_shipping_chosen_method_custom', 10, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woo_Shipping_Display_Mode_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}