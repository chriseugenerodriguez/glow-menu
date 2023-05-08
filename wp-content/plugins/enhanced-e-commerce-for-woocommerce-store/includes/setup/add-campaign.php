<?php
class AddCampaign {
  public $response;
  public $post_data;
  protected $merchantId;
  protected $new_campaign;
  protected $TVC_Admin_Helper;
  protected $site_url;

  public function __construct() {
    global $wpdb;
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->post_data = $_POST;    
    $this->merchantId = $this->TVC_Admin_Helper->get_merchantId();
    $this->currentCustomerId = $this->TVC_Admin_Helper->get_currentCustomerId(); 
    $this->site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
    $this->html_run();      
  }
  public function includes() {
    if (!class_exists('ShoppingApi')) {
      require_once(__DIR__ . '/ShoppingApi.php');
    }
  }
  public function html_run() {
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }

  public function country_dropdown($selected_code = '', $is_disabled = false) {
    $getCountris = file_get_contents(__DIR__ . "/json/countries.json");
    $contData = json_decode($getCountris);
    $wooCountry = $this->TVC_Admin_Helper->get_woo_country();
    $is_disabled = ($is_disabled) ? "style='max-width:30rem;height:35px;pointer-events:none;background:#f2f2f2;'" : "style='max-width: 30rem;height: 35px;'";
    $data = "<select id='sales_country' name='sales_country' class='form-group col-md-6' readonly='true' $is_disabled'>";
    foreach ($contData as $key => $value) {
      $selected = ($value->code == $wooCountry) ? "selected='selected'" : "";
      $data .= "<option value=" . esc_attr($value->code) . " " . esc_attr($selected) . " >" . esc_attr($value->name) . "</option>";
    }
    $data .= "</select>";
    return $data;
  }

  public function create_form() {
    $message = "";
    $campaigns_list = [];
    $categories = [];
    $campaign_performance = [];
    $account_performance = [];
    $product_performance = [];
    $product_partition_performance = [];
    $api_obj = new ShoppingApi();

    $campaigns_list_res = $api_obj->getCampaigns();
    if(isset($campaigns_list_res->errors) && !empty($campaigns_list_res->errors)){  
    }else if( isset($campaigns_list_res->data) ){
      $campaigns_list_res = $campaigns_list_res->data;
      if(isset($campaigns_list_res['status']) && $campaigns_list_res['status'] == 200){
        $campaigns_list = $campaigns_list_res['data'];
      }
    }

    $defaultCountry = $this->TVC_Admin_Helper->get_woo_country();
    
    if (isset($_POST['create_campaign'])) {
      $campaign_name = isset($_POST['campaign_name'])?sanitize_text_field($_POST['campaign_name']):"";
      $campaign_budget = isset($_POST['campaign_budget'])?sanitize_text_field($_POST['campaign_budget']):"";
      $sales_country = isset($_POST['sales_country'])?sanitize_text_field($_POST['sales_country']):"";
      $all_products = isset($_POST['all_products'])?sanitize_text_field($_POST['all_products']):"";
      $category_id = isset($_POST['dimension'])?sanitize_text_field($_POST['dimension']):"";
      $category_level = isset($_POST['category_level'])?sanitize_text_field($_POST['category_level']):"";

      $campaign = $api_obj->createCampaign($campaign_name, $campaign_budget, $sales_country, $all_products, $category_id, $category_level);
      if(isset($campaign->errors) && !empty($campaign->errors)){
        $class = 'alert-message tvc-alert-error';
        $message = esc_html__((is_array($campaign->errors) && isset($campaign->errors[0])) ? $campaign->errors[0] : 'Error', 'enhanced-e-commerce-for-woocommerce-store');
      }else{
        $class = 'alert-message tvc-alert-success';
        $campaign_neme = isset($campaign->data)?'with Resource name '.$campaign->data:"";
        $message = esc_html__('Smart Shopping Campaign Created Successfully '.$campaign_neme, 'enhanced-e-commerce-for-woocommerce-store');            
      }
    }else if (isset($_POST['update_campaign'])) {
      $campaign_name = isset($_POST['campaign_name'])?sanitize_text_field($_POST['campaign_name']):"";
      $campaign_budget = isset($_POST['campaign_budget'])?sanitize_text_field($_POST['campaign_budget']):"";
      $campaign_id = isset($_POST['campaign_id'])?sanitize_text_field($_POST['campaign_id']):"";
      $budget_id = isset($_POST['budget_id'])?sanitize_text_field($_POST['budget_id']):"";
      $sales_country = isset($_POST['sales_country'])?sanitize_text_field($_POST['sales_country']):"";
      $all_products = isset($_POST['all_products'])?sanitize_text_field($_POST['all_products']):"";
      $ad_group_id = isset($_POST['ad_group_id'])?sanitize_text_field($_POST['ad_group_id']):"";
      $ad_group_resource_name = isset($_POST['ad_group_resource_name'])?sanitize_text_field($_POST['ad_group_resource_name']):"";
      $category_id = isset($_POST['dimension']) ? sanitize_text_field($_POST['dimension']):"";
      $category_level = isset($_POST['category_level'])?sanitize_text_field($_POST['category_level']):"";

      $campaign = $api_obj->updateCampaign($campaign_name, $campaign_budget, $campaign_id, $budget_id, $sales_country, $all_products, $category_id, $category_level, $ad_group_id, $ad_group_resource_name);
      if (isset($campaign->errors) && !empty($campaign->errors)) {
        $class = 'alert-message tvc-alert-error';
        $message = esc_html__(isset($campaign->errors) ? $campaign->errors[0] : 'Error', 'enhanced-e-commerce-for-woocommerce-store');
      } else if(isset($campaign->data)){      
        $campaign_neme = isset($campaign->data)?'with Resource name '.$campaign->data:"";
        $class = 'alert-message tvc-alert-success';
        $message = esc_html__('Smart Shopping Campaign Updated Successfully ' . $campaign_neme, 'enhanced-e-commerce-for-woocommerce-store');
        // $url = admin_url('admin.php?page=tvc-configuration-page');
        //wp_redirect($url);        
      }
      ?>
      <script>jQuery('#feed-spinner').css('display', 'none');</script>
      <?php
    }
    $currency = $this->TVC_Admin_Helper->get_user_currency_symbol();
    if (isset($_GET['edit']) && sanitize_text_field($_GET['edit']) != '') {
      $campaign_details_res = $api_obj->getCampaignDetails(sanitize_text_field($_GET['edit']));
      if (isset($campaign_details_res->errors) && !empty($campaign_details_res->errors)) {
        $error_code = array_keys($campaign_details_res->errors)[0];
        if($error_code == 404){
          $error_msg = esc_html__("Campaign details not found","enhanced-e-commerce-for-woocommerce-store");
        }else{
          if (isset($campaign_details_res->error_data) && !empty($campaign_details_res->error_data)) {
            // $error_msg = array_values($campaign_details_res->error_data)[0]->errors[0];
          }
        }
        $class = 'alert-message tvc-alert-error';
        $message = esc_html__(isset($error_msg) ? $error_msg : 'There was some error fetching campaign details.', 'enhanced-e-commerce-for-woocommerce-store');
      } else {
        $campaign_details = $campaign_details_res->data;
        if ($campaign_details['status'] == 200) {
          $campaign_details = $campaign_details['data'];
        }
      }
    }

     ?>

<div class="con-tab-content">
  <?php if($message){
    printf('<div class="%1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message));
  }?>
  <div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 edit-section">
          <div class="edit-header-section">           
            <script>              
              document.write('<a href="<?php echo esc_url_raw($this->site_url."shopping_campaigns_page"); ?>" class="back-btn"><img src="'+"<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/left-angle-arrow.svg"); ?>"+'" alt="back"/><span>'+"<?php esc_html_e("Campaigns","enhanced-e-commerce-for-woocommerce-store");?>"+'</span></a>');
            </script>
          </div>
        <?php
        if (!isset($_GET['edit'])) { ?>
          <form method="post" id="create-form">
            <div class="form-group">
              <h2 class="lead"><?php esc_html_e("Create a Smart Shopping Campaign to promote your products","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              <p style="text-align:left; font-size: 14px;"><?php esc_html_e("A Smart Shopping campaign shows your products to potential customers across Google, Google Search Partners, the Google Display Network, YouTube, and Gmail.","enhanced-e-commerce-for-woocommerce-store"); ?></p>
              <p style="text-align:left">
                <a href="<?php echo esc_url_raw("https://support.google.com/google-ads/answer/7674739?hl=en"); ?>" target="_blank"><?php esc_html_e("Learn more about Smart Shopping Campaigns.","enhanced-e-commerce-for-woocommerce-store"); ?></a>
              </p>
            </div>
            <div class="form-row" style="margin-bottom: 0">
              <div class="col-md-12 row">
                <label for="campaign-name" class="form-group col-md-4 mt-2 text-left font-weight-bold"><?php esc_html_e("Campaign name: ","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                <input type="text" class="form-group col-md-6" name="campaign_name" id="campaign-name" required>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-budget" class="form-group col-md-4 mt-2 mb-0 text-left"><span class="font-weight-bold"><?php esc_html_e("Daily Campaign Budget","enhanced-e-commerce-for-woocommerce-store"); ?> (<?php echo  esc_attr($currency) ;?>):</span> <p style="text-align:left;font-size: 11px;"><?php esc_html_e("Only pay if someone clicks your ad. Recommended minimum budget of $5 per day.","enhanced-e-commerce-for-woocommerce-store"); ?></p></label>  
                <input type="number" class="form-group col-md-6" name="campaign_budget" id="campaign-budget" style="height: 35px;" required>    
              </div>
              <div class="col-md-12 row">
                <label for="sales-country" class="form-group col-md-4 mt-2 text-left"><span class="font-weight-bold"><?php esc_html_e("Target Country:","enhanced-e-commerce-for-woocommerce-store"); ?></span> <p style="text-align:left;font-size: 11px;"><?php esc_html_e("If you want to target multiple countries, then create multiple campaigns.","enhanced-e-commerce-for-woocommerce-store"); ?></p></label><?php echo  $this->country_dropdown(); ?>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-products" class="form-group col-md-4 mt-2 text-left font-weight-bold"><?php esc_html_e("Products in campaign:","enhanced-e-commerce-for-woocommerce-store"); ?> </label>
                <label class="mt-2"><input type="radio" id="campaign-products" name="all_products" value="1" checked /><?php esc_html_e("Include all Merchant Center products","enhanced-e-commerce-for-woocommerce-store"); ?></label>
              </div>
              <div class="col-md-12 row" style="display:none;">
                <div class="col-md-4"></div>
                <label class=""><input type="radio" id="campaign-product-partition" value="0" name="all_products" /><?php esc_html_e("Select products using product partition filters","enhanced-e-commerce-for-woocommerce-store"); ?></label>
              </div>
              
              <div class="col-md-12 row">
                  <label class="form-group col-md-12 mt-2 mb-0 text-left font-weight-bold"><?php esc_html_e("Campaign duration:","enhanced-e-commerce-for-woocommerce-store"); ?> </label>
                  <p class="ml-3" style="text-align:left; font-size: 14px;"><?php esc_html_e("Your campaign will run until you pause it. You can pause your campaign at any time, however it can take up to 30 days for google to optimize your products and ads.","enhanced-e-commerce-for-woocommerce-store"); ?></p>
              </div>
            </div>
            <div class="col-12">
              <button onclick="showLoader()" type="submit" class="btn btn-primary" id="create_campaign" name="create_campaign"><?php esc_html_e("Create Smart Shopping Campaign","enhanced-e-commerce-for-woocommerce-store"); ?></button>
            </div>
          </form>
          <hr>
          <form method="post">
            <div class="text-left">
              <p style="font-size: 14px;"><?php esc_html_e("Please note that campaigns will be created with accounts configured in previous steps.","enhanced-e-commerce-for-woocommerce-store"); ?></p>
              <p style="font-size: 14px;"><span><?php esc_html_e("Google Merchant Center :","enhanced-e-commerce-for-woocommerce-store"); ?> <?php echo  esc_attr($this->merchantId); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span><?php esc_html_e("Google Ads Account Id :","enhanced-e-commerce-for-woocommerce-store"); ?> <?php echo esc_attr($this->currentCustomerId); ?></span></p>
            </div>
          </form>
          <?php
          } else if (isset($_GET['edit']) && sanitize_text_field($_GET['edit']) != '') { ?>
          <form method="post">
            <div class="form-group">
              <h2 class="lead"><?php esc_html_e("Create/Update a Smart Shopping Campaign to promote your products","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              <p style="text-align:left; font-size: 14px;"><?php esc_html_e("A Smart Shopping campaign shows your products to potential customers across Google, Google Search Partners, the Google Display Network, YouTube, and Gmail.","enhanced-e-commerce-for-woocommerce-store"); ?></p>
              <p style="text-align:left">
                <a href="<?php echo esc_url_raw("https://support.google.com/google-ads/answer/7674739?hl=en"); ?>" target="_blank"><?php esc_html_e("Learn more about Smart Shopping Campaigns.","enhanced-e-commerce-for-woocommerce-store"); ?></a>
              </p>
            </div>
            <div class="form-row" style="margin-bottom: 0">
              <div class="col-md-12 row">
                <label for="campaign-name" class="form-group col-md-4 mt-2 text-left font-weight-bold"><?php esc_html_e("Campaign name:","enhanced-e-commerce-for-woocommerce-store"); ?> </label>
                <input type="text" class="form-group col-md-6" name="campaign_name" value="<?php echo (isset($campaign_details) && $campaign_details != '')?esc_attr($campaign_details->compaignName):""; ?>" id="campaign-name" required>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-budget" class="form-group col-md-4 mt-2 mb-0 text-left"><span class="font-weight-bold"><?php esc_html_e("Daily Campaign Budget","enhanced-e-commerce-for-woocommerce-store"); ?> (<?php echo esc_attr($currency); ?>):</span> <p style="text-align:left;font-size: 11px;"><?php esc_html_e("Only pay if someone clicks your ad. Recommended minimum budget of","enhanced-e-commerce-for-woocommerce-store"); ?> <?php echo esc_attr($currency); ?>5 per day.</p></label>    
                <input type="number" class="form-group col-md-6" name="campaign_budget" id="campaign-budget" value="<?php echo (isset($campaign_details) && $campaign_details != '')?esc_attr($campaign_details->dailyBudget):""; ?>" style="height: 35px;" required>    
              </div>
              <input type="hidden" name="campaign_id" value="<?php echo (isset($campaign_details) && $campaign_details != '')?esc_attr($campaign_details->compaignId):""; ?>" />
              <input type="hidden" name="budget_id" value="<?php echo (isset($campaign_details) && $campaign_details != '')?esc_attr($campaign_details->budgetId):""; ?>" />
              <input type="hidden" name="ad_group_id" value="<?php echo (isset($campaign_details) && $campaign_details != '')?esc_attr($campaign_details->adGroupId):""; ?>" />
              <input type="hidden" name="ad_group_resource_name" value="<?php echo(isset($campaign_details) && $campaign_details != '')? esc_attr($campaign_details->adGroupResourceName):""; ?>" />
              <div class="col-md-12 row">
                <label for="sales-country" class="form-group col-md-4 mt-2 text-left"><span class="font-weight-bold"><?php esc_html_e("Target Country:","enhanced-e-commerce-for-woocommerce-store"); ?></span> <p style="text-align:left;font-size: 11px;"><?php esc_html_e("If you want to target multiple countries, then create multiple campaigns.","enhanced-e-commerce-for-woocommerce-store"); ?></p></label>
                <?php echo $this->country_dropdown($defaultCountry, true); ?>
              </div>
              <div class="col-md-12 row">
                <label for="campaign-products" class="form-group col-md-4 mt-2 text-left font-weight-bold"><?php esc_html_e("Products in campaign:","enhanced-e-commerce-for-woocommerce-store"); ?> </label>
                <label class="mt-2">
                <?php
                if(isset($campaign_details) && $campaign_details->category_level > 0 && $campaign_details->category_id > 0){?>
                  <input type="radio" id="campaign-products" name="all_products" value="1" />
                <?php
                }else{ ?>
                  <input type="radio" id="campaign-products" name="all_products" value="1" checked />
                <?php
                } ?> <?php esc_html_e("Include all Merchant Center products","enhanced-e-commerce-for-woocommerce-store"); ?></label>
              </div>
              <div class="col-md-12 row" style="display: none;">
                <div class="col-md-4"></div>
                <label>
                <?php
                if (isset($campaign_details) && $campaign_details->category_level > 0 && $campaign_details->category_id > 0) { ?>
                  <input type="radio" id="campaign-product-partition" value="0" name="all_products" checked />
                <?php
                } else { ?>
                  <input type="radio" id="campaign-product-partition" value="0" name="all_products" />
                <?php
                } ?> 
                <?php esc_html_e("Select products using product partition filters","enhanced-e-commerce-for-woocommerce-store"); ?></label>
              </div>
              <div class="col-md-12 row">
                <label class="form-group col-md-12 mt-2 mb-0 text-left font-weight-bold"><?php esc_html_e("Campaign duration:","enhanced-e-commerce-for-woocommerce-store"); ?> </label>
                <p class="ml-3" style="text-align:left; font-size: 14px;"><?php esc_html_e("Your campaign will run until you pause it. You can pause your campaign at any time, however it can take up to 30 days for google to optimize your products and ads.","enhanced-e-commerce-for-woocommerce-store"); ?></p>
              </div>
            </div>
            <div class="col-12">
                <button onclick="showLoader()" type="submit" class="btn btn-primary btn-success" id="update_campaign" name="update_campaign"><?php esc_html_e("Update Smart Shopping Campaign","enhanced-e-commerce-for-woocommerce-store"); ?></button>
            </div>
          </form>
          <hr>
          <form method="post">
            <div class="text-left">
              <p style="font-size: 14px;"><?php esc_html_e("Please note that campaigns will be created with accounts configured in previous steps.","enhanced-e-commerce-for-woocommerce-store"); ?></p>
              <p style="font-size: 14px;"><span><?php esc_html_e("Google Merchant Center :","enhanced-e-commerce-for-woocommerce-store"); ?> <?php echo esc_attr($this->merchantId); ?></span>&nbsp;&nbsp;&nbsp;&nbsp;<span><?php esc_html_e("Google Ads Account Id :","enhanced-e-commerce-for-woocommerce-store"); ?> <?php echo esc_attr($this->currentCustomerId); ?></span></p>
            </div>
        </form>
        <?php } ?>
        </div>
        <div class="col-md-6 col-lg-4">
          <?php echo get_tvc_help_html(); ?>
          <div class="tvc-youtube-video">
            <span>Video tutorial:</span>
            <a href="https://www.youtube.com/watch?v=FAV4mybKogg" target="_blank">Walkthrough about Onboarding</a>
            <a href="https://www.youtube.com/watch?v=4pb-oPWHb-8" target="_blank">Walkthrough about Product Sync</a>
            <a href="https://www.youtube.com/watch?v=_C9cemX6jCM" target="_blank">Walkthrough about Smart Shopping Campaign</a>
          </div> 
        </div>
      </div>
    </div>
  </div>
</div>
    
<script>
  jQuery(document).ready(function() {
    jQuery('.select2').select2();
  });

  jQuery(document).on("click", "#create_campaign", function(event){
      var el = $(this).text();
      var slug = el.replace(/\s+/g, '_').toLowerCase();
      user_tracking_data('click', 'null','add_campaign_page',slug);
  });

  jQuery('.create-merchant-center').addClass('active');
  jQuery('.create-google-ads').addClass('active');
  jQuery('.sync-products').addClass('active');
  jQuery('.create-smart-shopping').addClass('active');
  function changeCategory() {
    let dimension = jQuery('#dimension').val();
    let level = jQuery('#dimension').find('option[value='+dimension+']'). attr('level');
    jQuery('#category_level').val(level);
  }
  /*function selectCountry() {
    var sales_country = document.getElementById('sales_country').value;
    var customer_id = "<?php //echo esc_attr($this->currentCustomerId); ?>";
    jQuery('#feed-spinner').css('display', 'block');
    jQuery.post(
      tvc_ajax_url,
      {
          action: 'tvcajax-get-campaign-categories',
          countryCode: sales_country,
          customerId: customer_id,
          campaignCategoryListsNonce: myAjaxNonces.campaignCategoryListsNonce
      },
      function( response ) {
        
        let categories = JSON.parse(response);        
        // callback( tvc_validateResponse( response ) );
        jQuery('#dimension').html('');
        jQuery.each(categories, function(key, category) {   
          jQuery('#dimension').append(jQuery('<option value='+category.id+' level='+category.level+'>'+category.name+'</option>'));
        });
        jQuery('#feed-spinner').css('display', 'none');
      }
    );
  } */
  function showLoader() {
      if(jQuery('#campaign-name').val()!=''&& jQuery('#campaign-budget').val()!=''){
          jQuery('#feed-spinner').css('display', 'block');
      }
  }
  </script>
  <?php
  }
  public function success_message($customerId) {
    $class = 'notice notice-success';
    $message = esc_html__($customerId . ' Set as default Ads Account ID.', 'enhanced-e-commerce-for-woocommerce-store');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
  }

  public function success_messageV2($campaign_name, $status) {
    $active = ($status == true) ? 'active' : 'inactive';
    $message = $campaign_name . ' set as ' . $active;
    return $message;
  }

  public function success_deleteMessage($campaign_name) {
    $message = $campaign_name . ' is deleted successfully';
    return $message;
  }
}