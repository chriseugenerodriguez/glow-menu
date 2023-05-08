<?php
class TVC_PMax {
  protected $TVC_Admin_Helper;
  protected $PMax_Helper;
  protected $subscription_id;
  //protected $google_detail;
  protected $site_url;
  protected $google_ads_id;
  protected $currency_code;
  protected $currency_symbol;
  public function __construct() {
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->PMax_Helper = new Conversios_PMax_Helper();
    $this->subscription_id = $this->TVC_Admin_Helper->get_subscriptionId(); 
    //$this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    $this->subscription_data = $this->TVC_Admin_Helper->get_user_subscription_data();
    $this->site_url = "admin.php?page=conversios-pmax&tab=";    

    $this->subscription_data = $this->TVC_Admin_Helper->get_user_subscription_data();
    
    if(isset($this->subscription_data->google_ads_id) && $this->subscription_data->google_ads_id != ""){
      $this->google_ads_id = $this->subscription_data->google_ads_id;
    }
    $currency_code_rs = $this->PMax_Helper->get_campaign_currency_code($this->google_ads_id);
    if(isset($currency_code_rs->data->currencyCode)){
      $this->currency_code = $currency_code_rs->data->currencyCode;
    }
    $this->currency_symbol = $this->TVC_Admin_Helper->get_currency_symbols($this->currency_code);
    if($this->google_ads_id){
      $this->load_html();
    }else{
      $this->current_connect_google_ads_html();
    }
  }

  public function includes() {
    if (!class_exists('Conversios_PMax_Helper')) {
      require_once(ENHANCAD_PLUGIN_DIR . 'admin/helper/class-pmax-helper.php');
    }   
  }

  public function load_html(){
    do_action('conversios_start_html_'.sanitize_text_field($_GET['page']));
    $this->current_html();
    $this->current_js();
    do_action('conversios_end_html_'.sanitize_text_field($_GET['page']));
  } 
  public function current_connect_google_ads_html() { 
    ?>
    <div class="section-campaignlisting dashbrdpage-wrap">      
      <div class="mt24 whiteroundedbx dshreport-sec" style="box-shadow: 0px 4px 10px rgb(0 0 0 / 25%);">
        <div class="row dsh-reprttop">
          <div class="dshrprttp-left">
            <h3><?php esc_html_e("Performance Max campaigns","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
            <?php /*<h4 style="margin-bottom: 15px;"><strong>Campaign Performance</strong></h4>*/ ?>
          </div>
        </div>
        <div class="google-account-analytics">
          <div class="row mb-3">
            <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title"><?php esc_html_e("Connected Google Ads account:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
            </div>
            <div class="col-6 col-md-6 col-lg-6 text-right">
              <div class="acc-num">
                <p class="ga-text">
                  <span><a href="<?php echo esc_url_raw($this->TVC_Admin_Helper->get_onboarding_page_url()); ?>" class="text-underline"><?php echo esc_html__('Get started','enhanced-e-commerce-for-woocommerce-store'); ?></a></span>
                </p>
               <p class="ga-text text-right"><a href="<?php echo esc_url_raw($this->TVC_Admin_Helper->get_onboarding_page_url()); ?>" class="text-underline"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg'); ?>" alt="connect account"/></a></p>
              </div>
            </div>          
          </div>
        </div>


      </div>
    </div>
    <?php
  }
  public function current_html() {    
  $icon = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/calendar-icon.png'); 
  $icon_caret = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/caret-down.png');   
    ?>
    <div class="section-campaignlisting dashbrdpage-wrap">      
      <div class="mt24 whiteroundedbx dshreport-sec" style="box-shadow: 0px 4px 10px rgb(0 0 0 / 25%);">
        <div class="row dsh-reprttop">
          <div class="dshrprttp-left">
            <h3><?php esc_html_e("Performance Max campaigns","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
            <?php /*<h4 style="margin-bottom: 15px;"><strong>Campaign Performance</strong></h4>*/ ?>
          </div>
          <div class="search-container">
            <a class="feedback_btn btn-campaign" href="<?php echo esc_url_raw($this->site_url.'pmax_add'); ?>"><?php esc_html_e("Create a New Campaign","enhanced-e-commerce-for-woocommerce-store"); ?></a>
          </div>
        </div>
        <div class="dashtablewrp campaign_pmax_list" id="campaign_pmax_list">
          <table class="dshreporttble mbl-table campaign-list-tbl">
              <thead>
                  <tr>
                    <th class="prdnm-cell"><?php esc_html_e("Campaign","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                    <th><?php esc_html_e("Daily Budget ","enhanced-e-commerce-for-woocommerce-store"); ?>(<spn class="ga_currency_symbols"><?php echo esc_attr($this->currency_symbol); ?></spn>)</th>
                    <th><?php esc_html_e("Status","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                    <th><?php esc_html_e("Clicks","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                    <th><?php esc_html_e("Cost ","enhanced-e-commerce-for-woocommerce-store"); ?>(<spn class="ga_currency_symbols"><?php echo esc_attr($this->currency_symbol); ?></spn>)</th>
                    <th><?php esc_html_e("Conversions ","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                    <th><?php esc_html_e("Sales","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                    <th><?php esc_html_e("Action","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                </tr>
              </thead>
              <tbody></tbody>
          </table>
          <div class="syncprofooter">
            <div class="properpage">
              <?php esc_html_e("Items per page:","enhanced-e-commerce-for-woocommerce-store"); ?>
              <select class="properselect" name="page_size" id="page_size" style="display: inline-table;">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
              </select>
            </div>
            
            <div class="syncpropagination">
              <div class="page_no_sec">
                <?php esc_html_e("Page","enhanced-e-commerce-for-woocommerce-store"); ?> <span id="page_no">1</span>
              </div>
              <ul> 
                <li class="prevli"><button data-token="" class="paginitem pgprevbtn" disabled><?php esc_html_e("Prev","enhanced-e-commerce-for-woocommerce-store"); ?></button></li>
                <li class="nextli"><button data-token="" data-token="" class="paginitem pgnextbtn"><?php esc_html_e("Next","enhanced-e-commerce-for-woocommerce-store"); ?></button></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- daterage script -->
  <?php
  }
  /**
   * Page custom js code
   *
   * @since    4.1.4
   */
  public function current_js(){
    /*ga_currency :'<?php echo esc_attr($this->ga_currency); ?>',*/
    ?>
    <script>
    var page = 1;
    var page_token = "";
    var page_size = jQuery('#page_size').val();
    var currency_symbol = '<?php echo esc_attr($this->currency_symbol); ?>';
    function get_page(page_size, page_token, page){
      var data = {
        action:'get_pmax_campaign_list',      
        subscription_id:'<?php echo esc_attr($this->subscription_id); ?>',
        plugin_url:'<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL); ?>',
        page_size: page_size,
        page_token: page_token,
        page:page,
        currency_symbol:currency_symbol,
        //start_date :jQuery.trim(start_date.replace(/\//g,"-")),
        //end_date :jQuery.trim(end_date.replace(/\//g,"-")),
        //g_mail:g_mail,
        google_ads_id:'<?php echo esc_attr($this->google_ads_id); ?>',
        conversios_nonce:'<?php echo wp_create_nonce( 'conversios_nonce' ); ?>'
      };
      // Call API      
      tvc_helper.get_call_ajax_request(data);
    }

    jQuery( document ).ready(function() {
      get_page(page_size, page_token, page);

      jQuery("#page_size").on( "change", function() {
        page_size = jQuery('#page_size').val();
        page_token = "";
        page =1;
        get_page(page_size, page_token, page);
      });

      jQuery(".pgprevbtn").on( "click", function(event) {
        event.preventDefault();
        page--;
        page_token = jQuery(this).attr("data-token");
        get_page(page_size, page_token, page);
      })
      jQuery(".pgnextbtn").on( "click", function(event) {
        event.preventDefault();
        page++;
        page_token = jQuery(this).attr("data-token");
        get_page(page_size, page_token, page);
      }) 

    });
    </script>
    <?php
  }   
}
?>