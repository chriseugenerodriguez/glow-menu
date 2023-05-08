<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/admin
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Shipping_Display_Mode_Admin
{

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
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     *
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Shipping_Display_Mode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Shipping_Display_Mode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $page_no = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
        $tab = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);
        if ('wc-settings' === $page_no && 'shipping_mode' === $tab) {
            wp_enqueue_style('wp-jquery-ui-dialog');
            wp_enqueue_style('wp-pointer');
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-shipping-display-mode-admin.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Shipping_Display_Mode_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Shipping_Display_Mode_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $page_no = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
        $tab = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);
        if ('wc-settings' === $page_no && 'shipping_mode' === $tab) {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-shipping-display-mode-admin.js', array(
                'jquery',
                'jquery-ui-dialog'
            ), $this->version, false);
            wp_enqueue_script('wp-pointer');
            wp_localize_script($this->plugin_name, 'wsdma', array());
        }
    }

    public function woo_shipping_admin_init_own()
    {
        require_once plugin_dir_path(__FILE__) . 'partials/woo-shipping-display-mode-admin-display.php';
        new WC_Settings_Shipping_Display_Mode_Methods();
    }

    // Function For Welcome page to plugin

    public function wo_shipping_welcome_shipping_display_mode_screen_do_activation_redirect()
    {

        if (!get_transient('_welcome_screen_shipping_display_mode_activation_redirect_data')) {
            return;
        }

        // Delete the redirect transient
        delete_transient('_welcome_screen_shipping_display_mode_activation_redirect_data');

        // if activating from network, or bulk
        $multi = filter_input(INPUT_POST, 'activate-multi', FILTER_SANITIZE_STRING);
        if (is_network_admin() || isset($multi) || !wp_verify_nonce($multi)) {
            return;
        }
        // Redirect to extra cost welcome  page
        wp_safe_redirect(add_query_arg(array('page' => 'wc-settings&tab=shipping_mode'), admin_url('index.php')));
        exit();
    }

    public function wo_shipping_welcome_pages_screen_shipping_display_mode()
    {
        add_dashboard_page('Woocommerce-Shipping-Method-Display-Style Dashboard', 'Woocommerce Shipping Method Display Style', 'read', 'woocommerce-shipping-display-mode', array(
            &$this,
            'wo_shipping_welcome_screen_content_shipping_display_mode'
        ));
    }

    public function wo_shipping_welcome_screen_content_shipping_display_mode()
    {
        ?>
        <div class="wrap about-wrap">
            <h1 style="font-size: 2.1em;"><?php printf(esc_html('Welcome to Woocommerce Shipping Method Display Style', 'woo-shipping-display-mode')); ?></h1>

            <div class="about-text woocommerce-about-text">
                <?php
                $message = '';
                printf(esc_html_e('%s WooCommerce shipping method display style plugin provides a configuration to display shipping methods as Radio button or select box on the checkout page.', 'woo-shipping-display-mode'), esc_attr($message), esc_attr($this->version));
                ?>
                <img class="version_logo_img"
                     src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'images/woo-shipping-display-mode.png'); ?>">
            </div>

            <?php
            $setting_tabs_wc = apply_filters('woo_shipping_display_mode_setting_tab', array(
                'about' => 'Overview',
                'other_plugins' => 'Checkout our other plugins'
            ));
            $tab = filter_input(INPUT_POST, 'tab', FILTER_SANITIZE_STRING);
            if ((!empty($tab) === $tab) && isset($tab) === $tab) {
                $current_tab_wc = sanitize_text_field(wp_unslash($tab));
            } else {
                $current_tab_wc = 'general';
            }
            ?>
            <h2 id="woo-extra-cost-tab-wrapper" class="nav-tab-wrapper">
                <?php
                foreach ($setting_tabs_wc as $name => $label) {
                    echo '<a href="' . esc_url(site_url('wp-admin/index.php?page=woocommerce-shipping-display-mode&tab=' . esc_attr($name))) . '" class="nav-tab ' . ($current_tab_wc === $name ? 'nav-tab-active' : '') . '">' . esc_html($label) . '</a>';
                }
                ?>
            </h2>

            <?php
            foreach ($setting_tabs_wc as $setting_tabkey_wc) {
                switch ($setting_tabkey_wc) {
                    case $current_tab_wc:
                        do_action('woocommerce_shipping_display_mode_' . $current_tab_wc);
                        break;
                }
            }
            ?>
            <hr/>
            <div class="return-to-dashboard">
                <a href="<?php echo esc_url(site_url('/wp-admin/admin.php?page=wc-settings&tab=shipping_mode')); ?>"><?php esc_html_e('Go to Woocommerce Shipping Method Display Style Settings', 'woo-shipping-display-mode'); ?></a>
            </div>
        </div>


        <?php
    }

    /**
     * Extra flate rate overview welcome page content function
     *
     */
    public function wo_shipping_woocommerce_shipping_display_mode_about()
    {
        ?>
        <div class="changelog">
            </br>
            <div class="changelog about-integrations">
                <div class="wc-feature feature-section col three-col">
                    <div>
                        <p class="shipping_display_mode_overview"><?php esc_html_e('Shipping Method Display Style for WooCommerce plugin provides you an interface in WooCommerce setting section from admin side. As you know WooCommerce has removed choose shipping display mode option from 2.5.1 version. So, this plugin provides these features, admin can choose shipping display modes like radio option or select option for shipping display mode.', 'woo-shipping-display-mode'); ?></p>

                        <p class="shipping_display_mode_overview"><?php esc_html_e('This Plugin is useful when you are using 2.5.2 or higher version of Woocommerce and you have to use more than 20 or 30 or more then that Shipping Method at that time you do not have forced to display all shipping method only using radio button, but you have also chosen a select option for display shipping mode and by choosing select option shipping method is displayed in the drop down box so you can avoid lengthy listing of shipping method.', 'woo-shipping-display-mode'); ?></p>

                        <p class="shipping_display_mode_overview"><?php esc_html_e('We have added', 'woo-shipping-display-mode'); ?>
                            <strong> <?php esc_html_e('Select shipping mode', 'woo-shipping-display-mode'); ?></strong><?php esc_html_e('option for default shipping method.', 'woo-shipping-display-mode'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * remove menu in deshboard
     *
     */
    public function wo_shipping_adjust_the_wp_menu()
    {
        remove_submenu_page('index.php', 'woocommerce-shipping-display-mode');
    }

    function wsdm_plugin_row_meta($links, $file)
    {

        if (strpos($file, 'woo-shipping-display-mode.php') !== false) {
            $new_links = array(
                'support' => '<a href="' . esc_url('https://www.thedotstore.com/support/') . '" target="_blank">' . esc_html__('Support', 'woo-shipping-display-mode') . '</a>',
            );

            $links = array_merge($links, $new_links);
        }

        return $links;
    }
}