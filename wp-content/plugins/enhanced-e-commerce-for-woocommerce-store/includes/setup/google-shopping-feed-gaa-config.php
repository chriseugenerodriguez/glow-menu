<?php
class GAAConfiguration {
  protected $TVC_Admin_Helper;
  protected $subscriptionId;
  protected $TVCProductSyncHelper;
  public function __construct() {
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->TVCProductSyncHelper = new TVCProductSyncHelper();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->site_url = "admin.php?page=conversios-google-shopping-feed&tab=";     
    $this->url = $this->TVC_Admin_Helper->get_onboarding_page_url(); 
    $this->html_run();
  }
  public function includes() {
    if (!class_exists('Tatvic_Category_Wrapper')) {
      require_once(__DIR__ . '/class-tvc-product-sync-helper.php');
    }
  }

  public function html_run() {
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }

  public function configuration_list_html($title, $val){
    $imge = (isset($val) && $val != "" && $val != 0) ? esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg') : esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png');
    return '
      <div class="row mb-3">
        <div class="col-6 col-md-6 col-lg-6">
          <h2 class="ga-title">'.esc_attr($title).'</h2>
        </div>
        <div class="col-6 col-md-6 col-lg-6 text-right">
          <div class="list-image"><img src="'.esc_url_raw($imge).'"></div>
        </div>
      </div>';
  }

  public function configuration_error_list_html($title, $val, $call_domain_claim, $googleDetail){
    if(isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id && $this->subscriptionId != "" ){
      return '<div class="row mb-3">
          <div class="col-6 col-md-6 col-lg-6">
            <h2 class="ga-title">'.esc_attr($title).'</h2>
          </div>
          <div class="col-4 col-md-4 col-lg-4 text-right">
            <div class="list-image"><img id="refresh_'.esc_attr($call_domain_claim).'" onclick="'.esc_attr($call_domain_claim).'();" src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/refresh.png').'"><img src="' .esc_url_raw( ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png').'" alt="no-config-success"/></div>
          </div>
        </div>';
    }else{
      return '
        <div class="row mb-3">
          <div class="col-6 col-md-6 col-lg-6">
            <h2 class="ga-title">'.esc_attr($title).'</h2>
          </div>
          <div class="col-6 col-md-6 col-lg-6 text-right">
            <div class="list-image"><img src="' . esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/exclaimation.png').'" alt="no-config-success"/></div>
          </div>
        </div>';
    }
  }

  public function create_form() {
    if(isset($_GET['welcome_msg']) && sanitize_textarea_field($_GET['welcome_msg']) == true){
      $class = 'notice notice-success';
      $message = esc_html__("Get your WooCommerce products in front of the millions of shoppers across Google by setting up your Google Merchant Center account from below.","enhanced-e-commerce-for-woocommerce-store");
      printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
      ?>
      <script>
        jQuery(document).ready(function() {
          var msg="<?php echo esc_html($message);?>"
          tvc_helper.tvc_alert("success","Hey!",msg,true);
        });
      </script>
      <?php
    }
    // $category_wrapper_obj = new Tatvic_Category_Wrapper();
    // $category_wrapper = $category_wrapper_obj->category_table_content('mapping');
    $googleDetail = [];
    $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    $ee_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
    $product_sync_duration = isset($ee_additional_data['product_sync_duration'])?sanitize_text_field($ee_additional_data['product_sync_duration']):"";
    $pro_snyc_time_limit = (isset($ee_additional_data['pro_snyc_time_limit']) && $ee_additional_data['pro_snyc_time_limit'] > 0) ? sanitize_text_field($ee_additional_data['pro_snyc_time_limit']):"25";
    $product_sync_batch_size = (isset($ee_additional_data['product_sync_batch_size']) && $ee_additional_data['product_sync_batch_size'] > 0) ? sanitize_text_field($ee_additional_data['product_sync_batch_size']):"50";
    if(isset($google_detail['setting'])){
      if ($google_detail['setting']) {
        $googleDetail = $google_detail['setting'];
      }
    }

     $last_api_sync_up = "";
  if(isset($google_detail['sync_time']) && $google_detail['sync_time']){      
    $date_formate=get_option('date_format')." ".get_option('time_format');
    if($date_formate ==""){
      $date_formate = 'M-d-Y H:i';
    }
    $last_api_sync_up = date( $date_formate, $google_detail['sync_time']);      
  }
  $is_need_to_update = $this->TVC_Admin_Helper->is_need_to_update_api_to_db();
  ?>
<div class="con-tab-content">
	<div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 edit-section">
           <?php if($this->subscriptionId != ""){?>
            <div class="tvc-api-sunc">
              <span>
              <?php if($last_api_sync_up){
                echo esc_html__("Merchant center products details last synced at ","enhanced-e-commerce-for-woocommerce-store").esc_attr($last_api_sync_up); 
              }else{
                echo esc_html__("Refresh sync up","enhanced-e-commerce-for-woocommerce-store");
              }?>.</span><span id="products_count"></span><img id="refresh_api" onclick="call_tvc_api_sync_up();" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/refresh.png'); ?>">
            </div>
          <?php } ?>
          <div class="configuration-section" id="config-pt1">
            <?php echo get_google_shopping_tabs_html($this->site_url,(isset($googleDetail->google_merchant_center_id))?$googleDetail->google_merchant_center_id:""); ?>
          </div>
          <div class="mt-3" id="config-pt2">
            <div class="google-account-analytics" id="gaa-config">
              <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title"><?php esc_html_e("Connected Google Merchant center account:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text"><?php echo ((isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) != '') ? esc_attr($googleDetail->google_merchant_center_id) : '<span>'.esc_html__('Get started','enhanced-e-commerce-for-woocommerce-store').'</span>'); ?></p>
                  <?php
                    if(isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) != ''){
                      echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline" id="connect_google_merchant_center_account"><img src="'.esc_url_raw( ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                    }else{
                      echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline" id="connect_google_merchant_center_account"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                    }?>
                </div>
              </div>              
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6 col-lg-6">
                <h2 class="ga-title"><?php esc_html_e("Linked Google Ads Account:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              </div>
              <div class="col-6 col-md-6 col-lg-6 text-right">
                <div class="acc-num">
                  <p class="ga-text"><?php echo (isset($googleDetail->google_ads_id) && esc_attr($googleDetail->google_ads_id) != '' ? esc_attr($googleDetail->google_ads_id) : '<span>'.esc_html__('Get started','enhanced-e-commerce-for-woocommerce-store').'</span>');?></p>
                  <?php
                  if (isset($googleDetail->google_ads_id) && esc_attr($googleDetail->google_ads_id) != '') {
                    echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline" id="linked_google_ads_account"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                  } else {
                    echo '<p class="ga-text text-right"><a href="' .esc_url_raw($this->url) . '" class="text-underline" id="linked_google_ads_account"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                  } ?>
                </div>
              </div>
            </div>
            <?php
            if (isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) != '') {?>
            <div class="row mb-3">
              <div class="col-6 col-md-6">
                <h2 class="ga-title"><?php esc_html_e("Sync Products:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              </div>
              <div class="col-6 col-md-6">
                <button id="tvc_btn_product_sync" type="button" class="btn btn-primary btn-success" data-bs-toggle="modal" data-bs-target="#syncProduct"><?php esc_html_e("Sync New Products","enhanced-e-commerce-for-woocommerce-store"); ?></button>                        
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6">
                <h2 class="ga-title"><?php esc_html_e("Performance Max campaigns:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              </div>
              <div class="col-6 col-md-6">
                <a href="admin.php?page=conversios-pmax" class="btn btn-primary btn-success" id="shopping_campaign"><?php esc_html_e("Create Performance Max campaigns","enhanced-e-commerce-for-woocommerce-store"); ?></a>
              </div>
            </div>
            <?php }else{ ?>
            <div class="row mb-3">
              <div class="col-6 col-md-6">
                <h2 class="ga-title"><?php esc_html_e("Sync Products:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              </div>
              <div class="col-6 col-md-6">
              <a href="<?php echo esc_url_raw($this->url); ?>" class="btn btn-primary btn-success"><?php esc_html_e("Sync New Products","enhanced-e-commerce-for-woocommerce-store"); ?></a>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-6 col-md-6">
                <h2 class="ga-title"><?php esc_html_e("Performance Max campaigns:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              </div>
              <div class="col-6 col-md-6">
                <a href="<?php echo esc_url_raw($this->url); ?>" class="btn btn-primary btn-success " id="shopping_campaign"><?php esc_html_e("Performance Max campaigns","enhanced-e-commerce-for-woocommerce-store"); ?></a>
              </div>
            </div>
            <?php } ?>            
            <?php
                $is_domain_claim = (isset($googleDetail->is_domain_claim))?esc_attr($googleDetail->is_domain_claim):"";
                $is_site_verified = (isset($googleDetail->is_site_verified))?esc_attr($googleDetail->is_site_verified):"";
                if($is_site_verified ==1){
                  echo $this->configuration_list_html(esc_html__("Site Verified","enhanced-e-commerce-for-woocommerce-store"), esc_attr($is_site_verified));
                }else{
                  echo $this->configuration_error_list_html(esc_html__("Site Verified","enhanced-e-commerce-for-woocommerce-store"),esc_attr($is_site_verified),"call_site_verified", $googleDetail);
                }
                if($is_domain_claim ==1){
                  echo $this->configuration_list_html(esc_html__("Domain claim","enhanced-e-commerce-for-woocommerce-store"),esc_attr($is_domain_claim));
                }else{
                  echo $this->configuration_error_list_html(esc_html__("Domain claim","enhanced-e-commerce-for-woocommerce-store"),esc_attr($is_domain_claim), 'call_domain_claim', $googleDetail);
                }
              ?> 
               <div class="row">
                  <div class="col-6 col-md-6 col-lg-6">
                    <h2 class="ga-title"><?php esc_html_e("Select time interval for product sync:","enhanced-e-commerce-for-woocommerce-store"); ?>
                    <?php echo $this->TVC_Admin_Helper->get_conv_pro_link("product_feed"); ?>
                    </h2>
                  </div>                 
               </div>
               <div class="row ">
                  <div class="col-6 col-md-6 col-lg-6">
                      <h2 class="ga-title"><?php esc_html_e("Select product sync batch size:","enhanced-e-commerce-for-woocommerce-store"); ?>
                      <?php echo $this->TVC_Admin_Helper->get_conv_pro_link("product_feed"); ?>
                      </h2>
                  </div>                  
               </div>          
            </div>
          </div>
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
<?php 
// add product sync popup
echo $this->TVCProductSyncHelper->tvc_product_sync_popup_html();
?>
<?php  
$is_need_to_domain_claim = false;
if(isset($googleDetail->google_merchant_center_id) && esc_attr($googleDetail->google_merchant_center_id) && esc_attr($this->subscriptionId) != "" && isset($googleDetail->is_domain_claim) && esc_attr($googleDetail->is_domain_claim) == '0'){
  $is_need_to_domain_claim = true;
}?>
<script type="text/javascript">
  jQuery(document).ready(function() {

    jQuery(document).on("click", "#shopping_campaign", function(event){
      var el = $(this).text();
      var slug = el.replace(/\s+/g, '_').toLowerCase();
      user_tracking_data('click', 'null','gaa_config_page',slug);
    });    

    jQuery(document).on("click", "#connect_google_merchant_center_account", function(event){
      var el = $(this).attr("id");
      user_tracking_data('click', 'null','gaa_config_page',el);
    });    

    jQuery(document).on("click", "#linked_google_ads_account", function(event){
      var el = $(this).attr("id");
      user_tracking_data('click', 'null','gaa_config_page',el);
    });
       
    jQuery(document).on("click", "#tvc_btn_product_sync", function(event){
      var el = $(this).text();
      var slug = el.replace(/\s+/g, '_').toLowerCase();
      user_tracking_data('click', 'null','gaa_config_page',slug);
      var is_need_to_domain_claim = "<?php echo esc_attr($is_need_to_domain_claim); ?>";
      if(is_need_to_domain_claim == 1 || is_need_to_domain_claim == true){
        event.preventDefault();
        jQuery.post(tvc_ajax_url,{
          action: "tvc_call_domain_claim",
          apiDomainClaimNonce : "<?php echo wp_create_nonce('tvc_call_domain_claim-nonce'); ?>"
        },function( response ){
          
        });
      }
    }); 
  });
</script>
<script type="text/javascript">
      jQuery(".auto_product_sync_save").click(function(e){
        e.preventDefault();
        let pro_snyc_time_limit = jQuery(".pro_snyc_time_limit").val();
        let product_sync_duration = jQuery(".product_sync_duration").val();
        let product_sync_batch_size = jQuery(".product_sync_batch_size").val();
        var data = {
              action: "auto_product_sync_setting",
              product_sync_duration:product_sync_duration,
              pro_snyc_time_limit:pro_snyc_time_limit,
              product_sync_batch_size:product_sync_batch_size,
              auto_product_sync_setting : "<?php echo wp_create_nonce('auto_product_sync_setting-nonce'); ?>"
            };

            jQuery.ajax({
              type: "POST",
              dataType: "json",
              url: tvc_ajax_url,
              data: data,
            beforeSend: function(){
              tvc_helper.loaderSection(true);
            },
            success: function(response){
              if (response.error === false) {          
                tvc_helper.tvc_alert("success","",response.message);
                
              }else{
                tvc_helper.tvc_alert("error","",response.message);
              }
              setTimeout(function(){ 
                tvc_helper.loaderSection(false);
              }, 2000);
            }
        });
      });
</script>
<script type="text/javascript">
    var selectedopt =jQuery('.product_sync_duration').val();
    jQuery('#pro_snyc_time_limit').on('blur', function(e){
      e.preventDefault();
      var val = Number(jQuery(this).val()); 
      if(selectedopt== "hour"){
        if (val > 23 ) {
          jQuery(this).val(23);
        } else if (val < 12) {
           jQuery(this).val(12);
        }
      }else{
        if (val > 28 ) {
          jQuery(this).val(28);
        } else if (val < 1) {
           jQuery(this).val(1);
        }
      }
    });
                  

  jQuery('.product_sync_duration').change(function(){
    var val = Number(jQuery("#pro_snyc_time_limit").val());
    selectedopt=  jQuery(this).val();
    if(selectedopt== "hour"){
      if (val > 23 ) {
        //alert("hour");
        jQuery("#pro_snyc_time_limit").val(23);
      } else if (val < 12) {
         jQuery("#pro_snyc_time_limit").val(12);
      }
    }else{
      if (val > 28 ) {
        //alert("day");
        jQuery("#pro_snyc_time_limit").val(28);
      } else if (val < 1) {
         jQuery("#pro_snyc_time_limit").val(1);
      }
    }
  });

  function call_site_verified(){
    var tvs_this = event.target;
    jQuery("#refresh_call_site_verified").css("visibility","hidden");
    jQuery(tvs_this).after('<div class="domain-claim-spinner tvc-nb-spinner" id="site-verified-spinner"></div>');
    jQuery.post(tvc_ajax_url,{
      action: "tvc_call_site_verified",
      SiteVerifiedNonce : "<?php echo wp_create_nonce('tvc_call_site_verified-nonce'); ?>"
    },function( response ){
      var rsp = JSON.parse(response);    
      if(rsp.status == "success"){        
        tvc_helper.tvc_alert("success","",rsp.message,true);
        location.reload();
      }else{
        tvc_helper.tvc_alert("error","",rsp.message,true);
        jQuery("#refresh_call_site_verified").css("visibility","visible");
      }
        user_tracking_data('refresh_call', 'null','conversios-google-shopping-feed','call_site_verified');
      jQuery("#site-verified-spinner").remove();
    });
  }
  function call_domain_claim(){
    var tvs_this = event.target;
    jQuery("#refresh_call_domain_claim").css("visibility","hidden");
    jQuery(tvs_this).after('<div class="domain-claim-spinner tvc-nb-spinner" id="domain-claim-spinner"></div>');
    jQuery.post(tvc_ajax_url,{
      action: "tvc_call_domain_claim",
      apiDomainClaimNonce : "<?php echo wp_create_nonce('tvc_call_domain_claim-nonce'); ?>"
    },function( response ){
      var rsp = JSON.parse(response);    
      if(rsp.status == "success"){
        tvc_helper.tvc_alert("success","",rsp.message,true);        
        //alert(rsp.message);
        location.reload();
      }else{
        tvc_helper.tvc_alert("error","",rsp.message,true);
        jQuery("#refresh_call_domain_claim").css("visibility","visible");
      }
        user_tracking_data('refresh_call', 'null','conversios-google-shopping-feed','call_domain_claim');
      jQuery("#domain-claim-spinner").remove();
    });
  }
  jQuery(document).ready(function() {
    var is_need_to_update = "<?php echo esc_attr($is_need_to_update); ?>";
    if(is_need_to_update == 1 || is_need_to_update == true){
      call_tvc_api_sync_up();
    }    
  });
  function call_tvc_api_sync_up(){
    var tvs_this = jQuery("#refresh_api");
    jQuery("#tvc_msg").remove();
    jQuery("#refresh_api").css("visibility","hidden");
    jQuery(tvs_this).after('<div class="tvc-nb-spinner" id="tvc-nb-spinner"></div>');
    tvc_helper.tvc_alert("error","<?php esc_html_e("Attention !","enhanced-e-commerce-for-woocommerce-store"); ?>","<?php esc_html_e("Sync up is in the process do not refresh the page. it may take few minutes.","enhanced-e-commerce-for-woocommerce-store"); ?>");
    jQuery.post(tvc_ajax_url,{
      action: "tvc_call_api_sync",
      apiSyncupNonce : "<?php echo wp_create_nonce('tvc_call_api_sync-nonce'); ?>"
    },function( response ){
      var rsp = JSON.parse(response);    
      if(rsp.error == false){
        jQuery("#tvc-nb-spinner").remove();
        tvc_helper.tvc_alert("success","",rsp.message,true,2000);
      }else{
        tvc_helper.tvc_alert("error","",rsp.message,true,2000);
      }  
      user_tracking_data('refresh_api', 'null','conversios-google-shopping-feed','details_last_synced');
      setTimeout(function(){ location.reload();}, 2000);    
    });
  }  

</script>
  <?php 
  } //create_form
} ?>