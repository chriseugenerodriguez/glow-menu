<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 * @author     Tatvic
 */

class Enhanced_Ecommerce_Google_Analytics_Admin extends TVC_Admin_Helper {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since      1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    protected $ga_id;
    protected $ga_LC;
    protected $ga_eeT;
    protected $site_url;
    protected $pro_plan_site;
    protected $google_detail;
    public function __construct($plugin_name, $version) {                       
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->google_detail = $this->get_ee_options_data();
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
      $screen = get_current_screen();
      if ($screen->id == 'toplevel_page_conversios'  || (isset($_GET['page']) && strpos(sanitize_text_field($_GET['page']), 'conversios') !== false) ) {
          if(sanitize_text_field($_GET['page']) == "conversios_onboarding"){
            return;
          }          
          if(is_rtl()){ 
            wp_register_style('plugin-bootstrap', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/bootstrap/css/bootstrap.rtl.min.css') );
          }else{
            wp_register_style('plugin-bootstrap', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/bootstrap/css/bootstrap.min.css') );
          }
          wp_enqueue_style('plugin-bootstrap');
          wp_enqueue_style('custom-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/custom-style.css'), array(), esc_attr($this->version), 'all' );
          //if(is_rtl()){  }
          if($this->is_current_tab_in(array('sync_product_page','gaa_config_page')) || $screen->id=="conversios_page_conversios-google-analytics"){
              wp_register_style('plugin-select2', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/select2.css') );
              wp_enqueue_style('plugin-select2');
              wp_register_style('plugin-steps', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/jquery-steps/jquery.steps.css'));
              wp_enqueue_style('plugin-steps');
              wp_register_style('tvc-dataTables-css', esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/css/dataTables.bootstrap5.min.css'));
              wp_enqueue_style('tvc-dataTables-css');
          }else if($this->is_current_tab_in(array("shopping_campaigns_page","add_campaign_page"))){
            
            wp_register_style('plugin-select2', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/select2.css') );
            wp_enqueue_style('plugin-select2');
            wp_register_style('tvc-bootstrap-datepicker-css', esc_url_raw(ENHANCAD_PLUGIN_URL. '/includes/setup/plugins/datepicker/bootstrap-datepicker.min.css'));
            wp_enqueue_style('tvc-bootstrap-datepicker-css');
          }
          wp_enqueue_style(esc_attr($this->plugin_name), esc_url_raw(plugin_dir_url(__FILE__) . 'css/enhanced-ecommerce-google-analytics-admin.css'), array(), esc_attr($this->version), 'all');
      }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
      $screen = get_current_screen();
      if ($screen->id == 'toplevel_page_conversios'  || (isset($_GET['page']) && strpos(sanitize_text_field($_GET['page']), 'conversios') !== false) ) {
          if(sanitize_text_field($_GET['page']) == "conversios_onboarding"){
            return;
          }
          
          wp_register_script('popper_bootstrap', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/bootstrap/js/popper.min.js') );
          wp_enqueue_script('popper_bootstrap');
          wp_register_script('atvc_bootstrap', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/bootstrap/js/bootstrap.min.js') );
          wp_enqueue_script('atvc_bootstrap');
         
          wp_enqueue_script( 'tvc-ee-custom-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/tvc-ee-custom.js'), array( 'jquery' ), esc_attr($this->version), false );

          wp_enqueue_script( 'tvc-ee-slick-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/slick.min.js'), array( 'jquery' ), esc_attr($this->version), false );
          
          if($this->is_current_tab_in(array('sync_product_page','gaa_config_page')) || $screen->id=="conversios_page_conversios-google-analytics"){
            wp_register_script('plugin-select2', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/select2.min.js') );
            wp_enqueue_script('plugin-select2');
            wp_register_script('plugin-step-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/jquery-steps/jquery.steps.js') );
            wp_enqueue_script('plugin-step-js');
          }
          if($this->is_current_tab_in(array('sync_product_page'))){
            wp_enqueue_script( 'tvc-ee-dataTables-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/jquery.dataTables.min.js'), array( 'jquery' ), esc_attr($this->version), false );
            wp_enqueue_script( 'tvc-ee-dataTables-v5-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/dataTables.bootstrap5.min.js'), array( 'jquery' ), esc_attr($this->version), false );
          }
          if($this->is_current_tab_in(array("shopping_campaigns_page","add_campaign_page"))){
            wp_register_script('plugin-select2', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/select2.min.js') );
            wp_enqueue_script('plugin-select2');
            wp_register_script('plugin-chart', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/chart.js'));
            wp_enqueue_script('plugin-chart');
            //wp_register_script('tvc-bootstrap-datepicker-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/datepicker/bootstrap-datepicker.min.js'));
            //wp_enqueue_script('tvc-bootstrap-datepicker-js');
             wp_enqueue_script( 'jquery-ui-datepicker' );
          }
      }
    }
}
