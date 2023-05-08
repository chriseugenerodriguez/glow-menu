<?php
class GoogleAds {
  protected $TVC_Admin_Helper="";
  protected $url = "";
  protected $subscriptionId = "";
  protected $google_detail;
  protected $customApiObj;
 
  public function __construct($theURL = '') {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->customApiObj = new CustomApi();
    $this->current_customer_id = $this->TVC_Admin_Helper->get_currentCustomerId();
    $this->url = $this->TVC_Admin_Helper->get_onboarding_page_url(); 
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data(); 
    $this->create_form();
	  $this->current_js();
  }

  public function create_form() {
    $message = ""; $class="";
    if (isset($_POST['google-add'])) {
      $response = $this->customApiObj->updateTrackingOption($_POST);
      $googleDetail = $this->google_detail;
      $googleDetail_setting = $this->google_detail['setting'];
      if(isset($_POST['remarketing_tags'])){
        update_option('ads_ert', sanitize_text_field($_POST['remarketing_tags']) );
        $googleDetail_setting->remarketing_tags = sanitize_text_field($_POST['remarketing_tags']);
      }else{
        update_option('ads_ert', 0);
        $googleDetail_setting->remarketing_tags = 0;
      }
      if(isset($_POST['dynamic_remarketing_tags'])){
        update_option('ads_edrt', sanitize_text_field($_POST['dynamic_remarketing_tags']) );
        $googleDetail_setting->dynamic_remarketing_tags = sanitize_text_field($_POST['dynamic_remarketing_tags']);
      }else{
        update_option('ads_edrt', 0);
        $googleDetail_setting->dynamic_remarketing_tags = 0;
      }

      
      if(isset($_POST['google_ads_conversion_tracking'])){
        update_option('google_ads_conversion_tracking', sanitize_text_field($_POST['google_ads_conversion_tracking']) );
        $googleDetail_setting->google_ads_conversion_tracking = sanitize_text_field($_POST['google_ads_conversion_tracking']);
      }else{
        update_option('google_ads_conversion_tracking', 0);
        $googleDetail_setting->google_ads_conversion_tracking = 0;
      }
      if(isset($_POST['ga_EC'])){
        update_option('ga_EC', sanitize_text_field($_POST['ga_EC']) );
      }else{
        update_option('ga_EC', 0);
      }
      if(isset($_POST['ee_conversio_send_to'])){
        update_option('ee_conversio_send_to', sanitize_text_field($_POST['ee_conversio_send_to']) );
        $googleDetail_setting->ee_conversio_send_to = sanitize_text_field($_POST['ee_conversio_send_to']);
      }
    
      if(isset($_POST['link_google_analytics_with_google_ads'])){
        $googleDetail_setting->link_google_analytics_with_google_ads = sanitize_text_field($_POST['link_google_analytics_with_google_ads']);
      }else{
        $googleDetail_setting->link_google_analytics_with_google_ads = 0;
      }
      $googleDetail['setting'] = $googleDetail_setting;                  
      $this->TVC_Admin_Helper->set_ee_options_data($googleDetail);      
      $class = 'alert-message tvc-alert-success';
      $message = esc_html__("Your tracking options have been saved.","enhanced-e-commerce-for-woocommerce-store");                 
    }
        
    $googleDetail = [];
    if(isset($this->google_detail['setting'])){
      if ($this->google_detail['setting']) {
        $googleDetail = $this->google_detail['setting'];
      }
    }
    ?>
<div class="con-tab-content">
  <?php if($message){
    printf('<div class="%1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message));
  }?>
	<div class="tab-pane show active" id="googleAds">
		<div class="tab-card" >
			<div class="row">
        <div class="col-md-6 col-lg-8 border-right">
          <form method="post" name="google-analytic" class="tvc_ee_plugin_form"> 
        		<input type="hidden" name="subscription_id" value="<?php echo (($this->subscriptionId)?esc_attr($this->subscriptionId):"");?>">
              <div class="google-account-analytics">
                <div class="row mb-3">
                  <div class="col-6 col-md-6 col-lg-6">
                      <h2 class="ga-title"><?php esc_html_e("Connected Google Ads account:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                  </div>
                  <div class="col-6 col-md-6 col-lg-6 text-right">
                    <div class="acc-num">
                      <p class="ga-text">
                        <?php echo  (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '') ? esc_attr($googleDetail->google_ads_id) :'<span>'. esc_html__('Get started','enhanced-e-commerce-for-woocommerce-store').'</span>'; ?>
                      </p>
                      <?php
                      if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '') {
                        echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                      } else { 
                        echo '<p class="ga-text text-right"><a href="' . $this->url . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                      }?>
                    </div>
                  </div>
                  
                </div>
                <div class="row mb-3">
                  <div class="col-6 col-md-6 col-lg-6">
                    <h2 class="ga-title"><?php esc_html_e("Linked Google Analytics Account:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                  </div>
                  <div class="col-6 col-md-6 col-lg-6 text-right">
                    <div class="acc-num">
                      <p class="ga-text">
                        <?php echo isset($googleDetail->property_id) && $googleDetail->property_id != '' ? esc_attr($googleDetail->property_id) : '<span>'. esc_html__('Get started','enhanced-e-commerce-for-woocommerce-store').'</span>';?>
                      </p>
                      <?php
                      if(isset($googleDetail->property_id) && $googleDetail->property_id != ''){
                          echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                      }else{
                          echo '<p class="ga-text text-right"><a href="' . $this->url . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                      } ?>
                    </div>
                  </div>                  
                </div>
              <div class="row mb-3">
                <div class="col-6 col-md-6 col-lg-6">
                  <h2 class="ga-title"><?php esc_html_e("Linked Google Merchant Center Account:","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                </div>
                <div class="col-6 col-md-6 col-lg-6 text-right">
                  <div class="acc-num">
                    <p class="ga-text"><?php echo isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != '' ? esc_attr($googleDetail->google_merchant_center_id) :'<span>'. esc_html__('Get started','enhanced-e-commerce-for-woocommerce-store').'</span>'; ?>
                    </p>
                    <?php
                    if (isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != '') {
                      echo '<p class="ga-text text-right"><a target="_blank" href="' . $this->url . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                    } else {
                      echo '<p class="ga-text text-right"><a href="#" class="text-underline" data-toggle="modal" data-target="#tvc_google_connect"><img src="'.esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                    } ?>
                  </div>
                </div>                
              </div>
              <?php
              if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '') { ?>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="tvc-custom-control tvc-custom-checkbox">
                      <input type="checkbox" class="tvc-custom-control-input" id="customCheck1" name="remarketing_tags" value="1" <?php echo (esc_attr($googleDetail->remarketing_tags) == 1) ? 'checked="checked"' : ''; ?> >
                      <label class="custom-control-label" for="customCheck1"><?php esc_html_e("Enable remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="tvc-custom-control tvc-custom-checkbox">
                      <input type="checkbox" class="tvc-custom-control-input" id="customCheck2" name="dynamic_remarketing_tags" value="1" <?php echo (esc_attr($googleDetail->dynamic_remarketing_tags) == 1) ? 'checked="checked"' : ''; ?>>
                      <label class="custom-control-label" for="customCheck2"><?php esc_html_e("Enable dynamic remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="tvc-custom-control tvc-custom-checkbox">
                      <input type="checkbox" class="tvc-custom-control-input" id="customCheck3" name="link_google_analytics_with_google_ads" value="1" <?php echo (esc_attr($googleDetail->link_google_analytics_with_google_ads) == 1) ? 'checked="checked"' : ''; ?> >
                      <label class="custom-control-label" for="customCheck3"><?php esc_html_e("Link Google analytics with google ads","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </div>
                  </div>
                </div>
              </div>
              <?php
              }else{ ?>
              <h2 class="ga-title"><?php esc_html_e("Connect Google Ads account to enable below features.","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
              <br>
              <ul>
                <li><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg'); ?>" alt="configuration  success" class="config-success"><?php esc_html_e("Enable remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></li>
                <li><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg'); ?>" alt="configuration  success" class="config-success"><?php esc_html_e("Enable dynamic remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></li>
                <!--<li><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg'); ?>" alt="configuration  success" class="config-success"><?php esc_html_e("Enable Google Ads conversion tracking","enhanced-e-commerce-for-woocommerce-store"); ?><span class="tvc-pro"> (PRO)</span></li>-->
                <li><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg'); ?>" alt="configuration  success" class="config-success"><?php esc_html_e("Link Google analytics with google ads","enhanced-e-commerce-for-woocommerce-store"); ?></li>        
              </ul>
              <?php
              } ?>		
               <div class="row">
                  <div class="col-md-12">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/lock-orange.png'); ?>" class="config-success"><?php esc_html_e("Enable Google Ads conversion tracking","enhanced-e-commerce-for-woocommerce-store"); ?><span class="tvc-pro"> (PRO)</span>
                  </div>
                  <div class="col-md-12">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/lock-orange.png'); ?>"  class="config-success"><?php esc_html_e("Enable Google Ads Enhanced conversion tracking","enhanced-e-commerce-for-woocommerce-store"); ?><span class="tvc-pro"> (PRO)</span>
                  </div>
               </div>
           <?php// } ?>
            </div>
            <?php
            if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '') { ?>
            <div class="text-left">
              <button type="submit" id="google-add" class="btn btn-primary btn-success" name="google-add"><?php esc_html_e("Save","enhanced-e-commerce-for-woocommerce-store"); ?></button>
            </div>
            <?php } ?>
          </form>
        </div>
        <div class="col-md-6 col-lg-4">          
          <?php echo get_tvc_google_ads_help_html(); ?>  
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
    }
	public function current_js(){
    ?>
     <script>  
        jQuery(function () {  
            jQuery("#google_ads_conversion_tracking").click(function () {  
                if (jQuery("#google_ads_conversion_tracking").is(":checked")) {  
                    jQuery('#google_ads_conversion_sec :input').removeAttr('disabled');  
                    jQuery('#google_ads_conversion_sec :select').removeAttr('disabled');   
                } else {  
                    //To disable all input elements within div use the following code:  
                    jQuery('#google_ads_conversion_sec :input').attr('disabled', 'disabled');  
  
                    //To disable all select elements within div use the following code:  
                    jQuery('#google_ads_conversion_sec :select').attr('disabled', 'disabled');
                }  
            });  
        });  
    </script>
    <script>
      jQuery('#google_ads_conversion_tracking').click(function(){
        if(!this.checked){
              jQuery("#ga_EC").prop("checked", false);
               }else{
              jQuery("#ga_EC").prop("checked", true);
            }
          });
    </script>
<?php
  }
}
?>