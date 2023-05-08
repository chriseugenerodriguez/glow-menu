<?php
echo "<script>var return_url ='".esc_url_raw($this->url)."';</script>";
$TVC_Admin_Helper = new TVC_Admin_Helper();
$this->customApiObj = new CustomApi();
$class="";
$message_p = "";
$validate_pixels = array();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$googleDetail = "";
if(isset($google_detail['setting'])){
  $googleDetail = $google_detail['setting'];
}

if(isset($_POST['ee_submit_plugin']) && wp_verify_nonce( $_POST['conv_settings_nonce_field'], 'conv_settings_nonce_save' ) ){
  $validate_pixels = $this->validate_pixels();    
  $settings = $TVC_Admin_Helper->get_ee_options_settings();
  if(!empty(sanitize_text_field($_POST['ga_id']))){
    $settings['tracking_option'] = "UA";
  }
  if(!empty(sanitize_text_field($_POST['gm_id']))){
    $settings['tracking_option'] = "GA4";
  }
  if(!empty(sanitize_text_field($_POST['gm_id'])) && !empty(sanitize_text_field($_POST['ga_id']))){
    $settings['tracking_option'] = "BOTH";
  }
  update_option('ads_tracking_id', sanitize_text_field($_POST['google_ads_id']));
  
  $settings['ga_eeT'] = isset($_POST["ga_eeT"])?sanitize_text_field($_POST["ga_eeT"]):"";
  //content grouping start
  $settings['ga_CG'] = isset($_POST["ga_CG"])?sanitize_text_field($_POST["ga_CG"]):"";
  $settings['ga_optimize_id'] = isset($_POST["ga_optimize_id"])?sanitize_text_field($_POST["ga_optimize_id"]):"";
  //content grouping end
  $settings['ga_ST'] = isset($_POST["ga_ST"])?sanitize_text_field($_POST["ga_ST"]):"";           
  $settings['gm_id'] = isset($_POST["gm_id"])?sanitize_text_field($_POST["gm_id"]):"";
  $settings['ga_id'] = isset($_POST["ga_id"])?sanitize_text_field($_POST["ga_id"]):"";
  $settings['google_ads_id'] = isset($_POST["google_ads_id"])?sanitize_text_field($_POST["google_ads_id"]):"";
  $settings['google_merchant_id'] = isset($_POST["google_merchant_id"])?sanitize_text_field($_POST["google_merchant_id"]):"";
  $settings['ga_gUser'] = isset($_POST["ga_gUser"])?sanitize_text_field($_POST["ga_gUser"]):"";
  //$_POST['ga_gCkout'] = 'on';
  $settings['ga_Impr'] = isset($_POST["ga_Impr"])?sanitize_text_field($_POST["ga_Impr"]):"1";
  $settings['ga_IPA'] = isset($_POST["ga_IPA"])?sanitize_text_field($_POST["ga_IPA"]):"";
  $settings['ga_PrivacyPolicy'] = isset($_POST["ga_PrivacyPolicy"])?sanitize_text_field($_POST["ga_PrivacyPolicy"]):"";
  $settings['google-analytic'] = '';
  $tracking_integration = array("tracking_method", "want_to_use_your_gtm", "use_your_gtm_id", "tvc_product_list_data_collection_method", "tvc_product_detail_data_collection_method", "tvc_checkout_data_collection_method", "tvc_thankyou_data_collection_method", "tvc_product_detail_addtocart_selector", "tvc_product_detail_addtocart_selector_type", "tvc_product_detail_addtocart_selector_val", "tvc_checkout_step_2_selector", "tvc_checkout_step_2_selector_type", "tvc_checkout_step_2_selector_val", "tvc_checkout_step_3_selector", "tvc_checkout_step_3_selector_type", "tvc_checkout_step_3_selector_val", "microsoft_ads_pixel_id", "twitter_ads_pixel_id", "pinterest_ads_pixel_id", "snapchat_ads_pixel_id", "tiKtok_ads_pixel_id","fb_conversion_api_token");
  foreach($tracking_integration as $val){
    $settings[$val] = isset($_POST[$val])?sanitize_text_field($_POST[$val]):"";
  }
  //$settings['want_to_use_your_gtm'] = 0;
  
  $settings['fb_pixel_id'] = isset($_POST["fb_pixel_id"])?sanitize_text_field($_POST["fb_pixel_id"]):"";
  $settings['ga4_api_secret'] = isset($_POST["ga4_api_secret"])?sanitize_text_field($_POST["ga4_api_secret"]):"";

  //Add disabled user roles
  $settings['conv_disabled_users'] = [];
  if(!empty($_POST["conv_disabled_users"]))
  {
    $arr = $_POST["conv_disabled_users"];
    array_walk($arr, function(&$value) {
      $value = sanitize_text_field($value);
    });
    $settings['conv_disabled_users'] = $arr;
  }

   //Add badge settings
   $settings['conv_show_badge'] = "";
   $settings['conv_badge_position'] = "";
   if(!empty($_POST["conv_show_badge"]))
   {
     $settings['conv_show_badge'] = sanitize_text_field($_POST["conv_show_badge"]);
   }
   if(!empty($_POST["conv_badge_position"]))
   {
     $settings['conv_badge_position'] = sanitize_text_field($_POST["conv_badge_position"]); 
   }

  $TVC_Admin_Helper->save_ee_options_settings($settings);
  $TVC_Admin_Helper->update_app_status();

  //Save selected pixel events
  $conv_posted_events = [];
  if(!empty($_POST["conv_selected_events"]))
  {
    $arr = $_POST["conv_selected_events"];
    array_walk($arr, function(&$value) {
      $temp_arr = [];
      for ($i=0; $i < count($value); $i++) { 
        $temp_arr[] = sanitize_text_field($value[$i]);
      }
      $value = $temp_arr;
    });
    $conv_posted_events = $arr;
  }
  $TVC_Admin_Helper->set_conv_selected_events($conv_posted_events);
  
  //google ads start
  $response = $this->customApiObj->updateTrackingOption($_POST);
  //$googleDetail = $this->google_detail;
  $googleDetail_setting = $googleDetail;
  if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '') {
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
    if(isset($_POST['ee_conversio_send_to_static']) && !empty($_POST['ee_conversio_send_to_static'])){
      update_option('ee_conversio_send_to', sanitize_text_field($_POST['ee_conversio_send_to_static']) );
      $googleDetail_setting->ee_conversio_send_to = sanitize_text_field($_POST['ee_conversio_send_to_static']);
    }
 
    if(isset($_POST['link_google_analytics_with_google_ads'])){
      $googleDetail_setting->link_google_analytics_with_google_ads = sanitize_text_field($_POST['link_google_analytics_with_google_ads']);
    }else{
      $googleDetail_setting->link_google_analytics_with_google_ads = 0;
    }
    
    //Disabled user roles and tracking events in the API data
    $googleDetail_setting->conv_disabled_users = $settings['conv_disabled_users'];
    $googleDetail_setting->conv_selected_events = $conv_posted_events;

    $google_detail['setting'] = $googleDetail_setting;
    $googleDetail = $googleDetail_setting; 
    $TVC_Admin_Helper->set_ee_options_data($google_detail);
  }
 //google ads end
  $class='alert-message tvc-alert-success';
  $message_p = esc_html__( 'Your settings have been saved.', 'enhanced-e-commerce-for-woocommerce-store' );    
}
$data = unserialize(get_option('ee_options'));
$conv_selected_events = unserialize(get_option('conv_selected_events'));
$this->current_customer_id = $TVC_Admin_Helper->get_currentCustomerId();
$subscription_id = $TVC_Admin_Helper->get_subscriptionId();
if(!$subscription_id){
  wp_redirect("admin.php?page=conversios_onboarding");
  exit;
}
$TVC_Admin_Helper->add_spinner_html();
$is_show_tracking_method_options =  $TVC_Admin_Helper->is_show_tracking_method_options($subscription_id); 

//$googleDetail = "";

?>
<div class="con-tab-content">
  <?php
  if(!empty($validate_pixels)){
    foreach($validate_pixels as $erkey => $erval){
      //print_r($erval);
      if(isset($erval["error"]) && $erval["error"] && isset($erval["message"]) && $erval["message"]){
        printf('<div class="alert-message tvc-alert-error"><div class="alert">%1$s</div></div>', esc_html($erval["message"]));
      }
    }
  }
  if($message_p){
    printf('<div class="%1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message_p));
  }?>
  <div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 border-right google-account-analytics">
          
          <div class="licence tvc-licence" >            
            <div class="tvc_licence_key_wapper">
              <p><?php esc_html_e("You are using our free plugin, no licence needed ! Happy analyzing..!! :)","enhanced-e-commerce-for-woocommerce-store"); ?></p>
              <p class="font-weight-bold"><?php esc_html_e("To unlock more features of google products, consider our","enhanced-e-commerce-for-woocommerce-store"); ?> <a href="<?php echo esc_url_raw($TVC_Admin_Helper->get_pro_plan_site().'?utm_source=EE+Plugin+User+Interface&utm_medium=Google+Analytics+Screen+pro+version&utm_campaign=Upsell+at+Conversios'); ?>" target="_blank"><?php esc_html_e("pro version.","enhanced-e-commerce-for-woocommerce-store"); ?></a></p>              
              <p>
                <b>
                  <?php esc_html_e("For Pro users, if you are facing difficulty in accessing premium features,","enhanced-e-commerce-for-woocommerce-store"); ?>
                  <a href="<?php echo esc_url_raw('https://www.conversios.io/help-center/Premium-Zip---Migration---User-Manual-&-FAQs.pdf'); ?>" target="_blank"><?php esc_html_e("refer this document to make changes.","enhanced-e-commerce-for-woocommerce-store"); ?></a>
                </b>
              </p>
            </div>   
          </div>
          
          <!-- start setting form-->
          <form id="ee_plugin_form" class="tvc_ee_plugin_form" name="google-analytic-setting-form" method="post">
            <h4><?php esc_html_e("Implementation Method","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
            <div class="con-setting-container">
              <table class="table">
                <tbody>
                  <?php 
                  $tracking_method = isset($data['tracking_method'])?$data['tracking_method']:"gtag";
                  if( $is_show_tracking_method_options){?>
                  <tr>
                    <th width="155px">
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/events-hit.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Tracking Method:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Recommended : Select GTM for all pixel tracking, accuracy and faster page load.","enhanced-e-commerce-for-woocommerce-store"); ?></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <label  class="align-middle">
                        <?php  
                        $list = array(
                          "gtag" => "gtag.js",
                          "gtm" => "Google Tag Manager"
                        );?>
                        <select name="tracking_method" id="tracking_method" class="select-lsm css-selector">
                          <?php if(!empty($list)){
                            foreach($list as $key => $val){
                              $selected = ($tracking_method == $key)?"selected":"";
                              ?>
                              <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                              <?php
                            }
                          }?>
                        </select>                      
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("We recommend using Google Tag Manager for speed and 95% accuracy.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                      </label>
                      <small><?php esc_html_e("Recommended : Select GTM for all pixel tracking, accuracy and faster page load.","enhanced-e-commerce-for-woocommerce-store"); ?></small>
                    </td>
                  </tr>
                  <?php }else{ $tracking_method = "gtm"; ?>
                    <input type="hidden" name="tracking_method" id="tracking_method" value="gtm">
                  <?php                    
                    //echo "only GTM";
                  }?>
                  <tr class="only-for-gtm <?php echo ($tracking_method != "gtm")?"tvc-hide":""; ?>">
                    <th>
                     <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/gtm_logo.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Google tag manager container id:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of using your GTM","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://marketingplatform.google.com/about/tag-manager/benefits/"); ?>">click here.</a></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <?php $want_to_use_your_gtm = (isset($data['want_to_use_your_gtm']) && $data['want_to_use_your_gtm'] != "")?$data['want_to_use_your_gtm']:"0"; ?>
                      <div class="cstmrdobtn-item">
                        <label for="want_to_use_your_gtm_default">
                          <input type="radio" <?php echo esc_attr(($want_to_use_your_gtm == "0")?'checked="checked"':''); ?> name="want_to_use_your_gtm" id="want_to_use_your_gtm_default" value="0">
                          <span class="checkmark"></span>
                          <?php esc_html_e("Default (Conversios container - GTM-K7X94DG)","enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                      </div>
                      <div class="cstmrdobtn-item">
                        <label for="want_to_use_your_gtm_own" class="<?php echo ($want_to_use_your_gtm == '0')?'avoid-clicks':''; ?>">
                           <input type="radio" <?php echo esc_attr(($want_to_use_your_gtm == "1")?'checked="checked"':''); ?> name="want_to_use_your_gtm" id="want_to_use_your_gtm_own" value="1">
                            <span class="checkmark"></span>
                            <?php esc_html_e("Use your own GTM container","enhanced-e-commerce-for-woocommerce-store"); ?>
                            <?php $use_your_gtm_id = isset($data['use_your_gtm_id'])?$data['use_your_gtm_id']:""; ?>                       
                            <input type="hidden" name="use_your_gtm_id" id="use_your_gtm_id" value="<?php echo esc_attr($use_your_gtm_id); ?>">
                        </label>
                        <?php echo $TVC_Admin_Helper->get_conv_pro_link("pixel_setting"); ?>
                      </div>
                    </td>
                  </tr>
                    
                </tbody>
              </table>
            </div>
            
            <!-- Start event selection settings -->
            <div class="con-setting-container">
              <h4><?php esc_html_e("Event Settings","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
              <table class="table">
                <tbody>
                <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/disabled-tracking-for-user-roles.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Disable tracking for:","enhanced-e-commerce-for-woocommerce-store"); ?></label>                          
                        </div>
                      </div>
                    </th>
                    <td>
                    <div class="ga_conv_event_selection">
                        <select class="conv_multiselect2_input" id="conv_disabled_users" name="conv_disabled_users[]" multiple="multiple" data-placeholder="Select role">
                          <?php foreach($TVC_Admin_Helper->conv_get_user_roles() as $slug => $name){ 
                          $is_selected = "";
                          if(!empty($data['conv_disabled_users']))
                          {
                            $is_selected = in_array($slug, $data['conv_disabled_users']) ? "selected" : "";
                          }
                          ?>  
                          <option value="<?php echo esc_attr($slug); ?>" <?php echo $is_selected; ?> ><?php echo esc_attr($name); ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </td>
                  </tr> 
                </tbody>
                </table>
            </div>
            
            <!-- End event selection settings -->  

            <!-- start Google Analytics section -->
            <h4><?php esc_html_e("Google Analytics","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
            <div class="con-setting-container">
              <table class="table">
                <tbody>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/google_analytics_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Google Analytics 3 Account:","enhanced-e-commerce-for-woocommerce-store"); ?></label>                          
                        </div>
                      </div>
                    </th>
                    <td>
                      <div class="acc-num">
                        <p class="ga-text">
                          <?php echo  (isset($data['ga_id']) && $data['ga_id'] != '') ? $data['ga_id'] : '<span>'.esc_html__("Get started","enhanced-e-commerce-for-woocommerce-store").'</span>'; ?>
                        </p>
                        <?php
                        if (isset($data['ga_id']) && $data['ga_id'] != '') {
                          echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'.esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                        } else { 
                          echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                        }?>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/google_analytics_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Google Analytics 4 Account:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of GA tracking for ecommerce business","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/analytics/answer/10089681?hl=en"); ?>">click here.</a></small>
                        </div>
                      </div>                      
                    </th>
                    <td>
                      <div class="acc-num">
                        <p class="ga-text"><?php echo (isset($data['gm_id']) && $data['gm_id'] != '') ? esc_attr($data['gm_id']) : '<span>'.esc_html__("Get started","enhanced-e-commerce-for-woocommerce-store").'</span>'; ?></p>
                        <?php
                        if (isset($data['gm_id']) && esc_attr($data['gm_id']) != '') {
                          echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                        } else { 
                          echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                        }?>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- End Google Analytics section -->
            <!-- start Google Ads section -->
            <h4><?php esc_html_e("Google Ads","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
            <div class="con-setting-container">         
              <table class="table">
                <tbody>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/google ads_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Google Ads account:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of integrating google ads account","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://support.google.com/google-ads/answer/3124536?hl=en"); ?>">click here.</a></small>
                        </div>
                      </div>                      
                    </th>
                    <td>                                
                      <div class="acc-num">
                        <p class="ga-text">

                          <?php echo  (isset($data['google_ads_id']) && $data['google_ads_id'] != '') ? esc_attr($data['google_ads_id']) : '<span>'.esc_html__("Get started","enhanced-e-commerce-for-woocommerce-store").'</span>'; ?>
                        </p>
                        <?php
                        if (isset($data['google_ads_id']) && esc_attr($data['google_ads_id']) != '') {
                          echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg').'" alt="refresh"/></a></p>';
                        } else { 
                          echo '<p class="ga-text text-right"><a href="' . esc_url_raw($this->url) . '" class="text-underline"><img src="'. esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/add.svg').'" alt="connect account"/></a></p>';
                        }?>
                      </div>
                    </td>
                  </tr>
                </tbody>
            </table>
            </div>
            <!-- End Google Ads section -->
            <!-- start Google Ads section -->
            <h4><?php esc_html_e("Pixel Integrations","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
            <div class="con-setting-container">
              <table class="table" style="margin-bottom: 0;">
                <tbody>
                  <!-- Start Other Pixel Settings section -->
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/fb-icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Meta (Facebook) Pixel ID:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of adding FB pixel","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://www.facebook.com/business/tools/meta-pixel"); ?>">click here.</a></small>
                          <small><?php esc_html_e("How to find FB pixel ID?","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/meta_pixel.pdf"); ?>">click here.</a></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <?php $fb_pixel_id = isset($data['fb_pixel_id'])?$data['fb_pixel_id']:""; ?>
                      <input type="text"  class="fromfiled" name="fb_pixel_id" id="fb_pixel_id" value="<?php echo esc_attr($fb_pixel_id); ?>">
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("The Facebook pixel ID looks like. 518896233175751","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">                        
                        <div class="pixel-text ml-40">
                          <label>
                            <?php esc_html_e("Meta (Facebook) Conversion API token","enhanced-e-commerce-for-woocommerce-store"); ?>
                          </label>
                          <small><?php esc_html_e("Benefits of Meta (Facebook) Conversion API","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://www.facebook.com/business/help/2041148702652965?id=818859032317965"); ?>">click here.</a></small>
                          <small><?php esc_html_e("How to find Meta (Facebook) Conversion API token?","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/FaceBook-Conversios-API-Token.pdf"); ?>">click here.</a></small>                                   
                        </div>
                      </div>
                    </th>
                    <td>
                      <label class="custom-control-label">
                        <?php esc_html_e("Send events directly from your web server to Facebook through the Conversion API.","enhanced-e-commerce-for-woocommerce-store"); ?>
                      </label>
                      <?php $fb_conversion_api_token = isset($data['fb_conversion_api_token'])?$data['fb_conversion_api_token']:""; ?> 
                      <input type="hidden" name="fb_conversion_api_token" id="fb_conversion_api_token" value="<?php echo esc_attr($fb_conversion_api_token); ?>" >
                      <br>
                      <?php echo $TVC_Admin_Helper->get_conv_pro_link("pixel_setting"); ?>
                    </td>
                  </tr>
                  <!-- End Other Pixel Settings section -->
                </tbody>
              </table>
              <table class="table">
                <tbody>
                  <!-- Start Other Pixel Settings section -->
                  <tr>
                    <th>
                     <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/bing_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Microsoft Ads (Bing) Pixel ID:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of adding Microsoft pixel","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://help.ads.microsoft.com/#apex/ads/en/56681/2-500"); ?>">click here.</a></small>
                          <small><?php esc_html_e("How to find Bing pixel ID?","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/microsoft_bing_ads_pixel.pdf"); ?>">click here.</a></small>                                   
                        </div>
                      </div>
                    </th>
                    <td>
                      <?php $microsoft_ads_pixel_id = isset($data['microsoft_ads_pixel_id'])?$data['microsoft_ads_pixel_id']:""; ?>
                      <input type="text"  class="fromfiled only-for-gtm-lock" <?php echo ($tracking_method != "gtm")?"disabled":""; ?> name="microsoft_ads_pixel_id" id="microsoft_ads_pixel_id" value="<?php echo esc_attr($microsoft_ads_pixel_id); ?>">
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("The Microsoft Ads pixel ID looks like. 343003931 ","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/twitter_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Twitter Pixel ID:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of adding Twitter pixel","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html"); ?>">click here.</a></small>
                          <small><?php esc_html_e("How to find Twitter pixel ID?","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/twitter_ads_pixel.pdf"); ?>">click here.</a></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <?php $twitter_ads_pixel_id = isset($data['twitter_ads_pixel_id'])?$data['twitter_ads_pixel_id']:""; ?>
                      <input type="text"  class="fromfiled only-for-gtm-lock" <?php echo ($tracking_method != "gtm")?"disabled":""; ?> name="twitter_ads_pixel_id" id="twitter_ads_pixel_id" value="<?php echo esc_attr($twitter_ads_pixel_id); ?>">
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("The Twitter Ads pixel ID looks like. ocihb","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/pinterest_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Pinterest Pixel ID:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of adding Pinterest pixel","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://help.pinterest.com/en/business/article/install-the-pinterest-tag"); ?>">click here.</a></small>
                          <small><?php esc_html_e("How to find Pinterest pixel ID?","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/pinterest _pixel.pdf"); ?>">click here.</a></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <?php $pinterest_ads_pixel_id = isset($data['pinterest_ads_pixel_id'])?$data['pinterest_ads_pixel_id']:""; ?>
                      <input type="text"  class="fromfiled only-for-gtm-lock" <?php echo ($tracking_method != "gtm")?"disabled":""; ?> name="pinterest_ads_pixel_id" id="pinterest_ads_pixel_id" value="<?php echo esc_attr($pinterest_ads_pixel_id); ?>">
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("The Pinterest Ads pixel ID looks like. 2612831678022","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/snapchat_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Snapchat Pixel ID:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of adding Snapchat pixel","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://forbusiness.snapchat.com/advertising/snap-pixel#:~:text=Having%20a%20Snap%20Pixel%20installed,your%20ad%20to%20their%20actions."); ?>">click here.</a></small>
                          <small><?php esc_html_e("How to find Snapchat pixel ID?","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/snapchat_pixel.pdf"); ?>">click here.</a></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <?php $snapchat_ads_pixel_id = isset($data['snapchat_ads_pixel_id'])?$data['snapchat_ads_pixel_id']:""; ?>
                      <input type="text"  class="fromfiled only-for-gtm-lock" <?php echo ($tracking_method != "gtm")?"disabled":""; ?> name="snapchat_ads_pixel_id" id="snapchat_ads_pixel_id" value="<?php echo esc_attr($snapchat_ads_pixel_id); ?>">
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("The Snapchat Ads pixel ID looks like. 12e1ec0a-90aa-4267-b1a0-182c455711e9","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/tiKtok_icon.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("TiKTok Pixel ID:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Benefits of adding TiKTok pixel","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://ads.tiktok.com/help/article?aid=9663&redirected=1"); ?>">click here.</a></small>
                          <small><?php esc_html_e("How to find TiKTok pixel ID?","enhanced-e-commerce-for-woocommerce-store"); ?><a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/help-center/tiktok_pixel.pdf"); ?>">click here.</a></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <?php $tiKtok_ads_pixel_id = isset($data['tiKtok_ads_pixel_id'])?$data['tiKtok_ads_pixel_id']:""; ?>
                      <input type="text"  class="fromfiled only-for-gtm-lock" <?php echo ($tracking_method != "gtm")?"disabled":""; ?> name="tiKtok_ads_pixel_id" id="tiKtok_ads_pixel_id" value="<?php echo esc_attr($tiKtok_ads_pixel_id); ?>">
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("The TiKTok Ads pixel ID looks like. CBET743C77U5BM7P178N","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>
                  <!-- End Other Pixel Settings section -->
                </tbody>
              </table>
            </div>
            
            <!-- Start Advance settings section -->
            <h4><?php esc_html_e("Advanced Options","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
            <div class="con-setting-container">
              <!-- start Advance Setting for GA-->
              <div class="ga-title con_tracking_integration con_faq_title not-for-gtm <?php echo ($tracking_method == "gtm")?"tvc-hide":""; ?>" data-id="sec_con_integration_advset">
                <?php esc_html_e("Google Analytics Settings","enhanced-e-commerce-for-woocommerce-store"); ?>
                <div class="tvc-tooltip">
                  <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Google Analytics Settings.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                </div>
                <img class="faq_icon" style="height: 20px;" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/arrow-down-sign.png"); ?>" >
              </div>
              <div class="sec_con_integration advance-setting not-for-gtm" id="sec_con_integration_advset">
                <table class="table">
                  <tbody>
                    <tr>
                      <th>
                        <label class="ga-title align-middle" for="tracking_code"><?php esc_html_e("Tracking Code","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                      </th>
                      <td>
                        <label  class="align-middle">
                          <?php $ga_ST = !empty($data['ga_ST']) ? 'checked' : ''; ?>
                          <input type="checkbox"  name="ga_ST" id="ga_ST" <?php echo esc_attr($ga_ST); ?> >
                          <label class="custom-control-label" for="ga_ST"><?php esc_html_e("Add Global Site Tracking Code 'gtag.js'","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <div class="tvc-tooltip">
                            <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("This feature adds new gtag.js tracking code to your store. You don't need to enable this if gtag.js is implemented via any third party analytics plugin.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                          </div>
                        </label>
                        <label  class="align-middle">
                          <?php $ga_eeT = !empty($data['ga_eeT']) ? 'checked' : ''; ?>
                          <input type="checkbox"  name="ga_eeT" id="ga_eeT" <?php echo esc_attr($ga_eeT); ?> >
                          <label class="custom-control-label" for="ga_eeT"><?php esc_html_e("Add Enhanced Ecommerce Tracking Code","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <div class="tvc-tooltip">
                            <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("This feature adds Enhanced Ecommerce Tracking Code to your Store.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                          </div>                        
                        </label>
                        <label class="align-middle">
                          <?php $ga_gUser = !empty($data['ga_gUser']) ? 'checked' : ''; ?>
                          <input type="checkbox"  name="ga_gUser" id="ga_gUser" <?php echo esc_attr($ga_gUser); ?> >
                          <label class="custom-control-label" for="ga_gUser"><?php esc_html_e("Add Code to Track the Login Step of Guest Users (Optional)","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <div class="tvc-tooltip">
                            <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("If you have Guest Check out enable, we recommend you to add this code.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                          </div>
                        </label>
                      </td>
                    </tr>
                    
                    <tr>
                      <th>
                         <label class="ga-title align-middle"><?php esc_html_e("GA4 - API secrets ","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                      </th>
                      <td>
                        <?php $ga4_api_secret = isset($data['ga4_api_secret'])?$data['ga4_api_secret']:""; ?>
                        
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("How to get 'Measurement Protocol API Secret' in GA4: Click Admin > Click Data streams (Under Property) > Select the stream > Additional Settings - Measurement Protocol API secrets > Create a new API secret.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                        <label class="custom-control-label"><?php esc_html_e("To track refund order","enhanced-e-commerce-for-woocommerce-store"); ?></label> 
                        <?php echo $TVC_Admin_Helper->get_conv_pro_link("pixel_setting"); ?>
                      </td>
                    </tr>
                    
                    <tr>
                      <th>
                        <label class="ga-title align-middle" for= "ga_CG" ><?php esc_html_e("Content Grouping","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                      </th>
                      <td>
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Content grouping helps you group your web pages (content).","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                        <label class="custom-control-label"><?php esc_html_e("Add Code to enable content grouping","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                        <?php echo $TVC_Admin_Helper->get_conv_pro_link("pixel_setting"); ?>
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <label class="ga-title align-middle" for= "ga_optimize" ><?php esc_html_e("Google Optimize","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                      </th>
                      <td>
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Enter a valid google optimize container ID.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>                        
                        <label class="custom-control-label"><?php esc_html_e("Test webpage performance","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                        <?php echo $TVC_Admin_Helper->get_conv_pro_link("pixel_setting"); ?>
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <label class="ga-title align-middle" for="ga_Impr"><?php esc_html_e("Impression Thresold","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                      </th>
                      <td>
                        <?php $ga_Impr = !empty($data['ga_Impr']) ? $data['ga_Impr'] : 6; ?>
                        <input type="number" min="1" id="ga_Impr"  name = "ga_Impr" value = "<?php echo esc_attr($ga_Impr); ?>">
                        <label for="ga_Impr"></label>
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("This feature sets Impression threshold for category page. It sends hit after these many numbers of products impressions.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>                    
                        <p class="description"><b><?php esc_html_e("Note : To avoid processing load on server we recommend upto 6 Impression Thresold.","enhanced-e-commerce-for-woocommerce-store"); ?></b></p>
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <label class="ga-title align-middle" for="ga_IPA"><?php esc_html_e("I.P. Anoymization","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                      </th>
                      <td>
                        <label  class="align-middle">
                          <?php $ga_IPA = !empty($data['ga_IPA']) ? 'checked' : ''; ?>
                          <input class="" type="checkbox" name="ga_IPA" id="ga_IPA"  <?php echo esc_attr($ga_IPA); ?>>
                          <label class="custom-control-label" for="ga_IPA"><?php esc_html_e("Enable I.P. Anonymization","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <div class="tvc-tooltip">
                            <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Use this feature to anonymize (or stop collecting) the I.P Address of your users in Google Analytics. Be in legal compliance by using I.P Anonymization which is important for EU countries As per the GDPR compliance.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                          </div>
                        </label>
                      </td>
                    </tr>
                   </tbody>
                 </table>
              </div>
              <!-- End Advance Setting for GA-->
              <!-- start Google Analytics Event Tracking - Custom Integration settings for GA-->      
              <div class="ga-title con_tracking_integration con_faq_title" data-id="sec_con_integration">
                <?php esc_html_e("Event Tracking - Custom Integration","enhanced-e-commerce-for-woocommerce-store"); ?>
                <div class="tvc-tooltip">
                  <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("This feature is for the woocommerce store which has changed standard woocommerce hooks or implemented custom woocommerce hooks.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                </div>
                <img class="faq_icon" style="height: 20px;" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/arrow-down-sign.png"); ?>" >
              </div>
              <div class="sec_con_integration" id="sec_con_integration">
                <table class="table tracking-trigger">
                  <tr>
                    <th colspan="2">
                      <span>
                      <?php esc_html_e("Product data collection method ","enhanced-e-commerce-for-woocommerce-store"); ?>
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("When you have custom woocommerce implementation and you have modified standard woocommerce hooks, you can configure/select your custom hooks from below to enable google analytics tracking for specific events.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </span>
                    <span style="float: right;"><a href="<?php echo esc_url_raw("https://".TVC_AUTH_CONNECT_URL."/help-center/event-tracking-custom-integration.pdf"); ?>" target="_blank">Detailed Document</a></span>
                  </th>
                  </tr>
                  <tr>
                    <th style="padding: 0 1rem; width: 35%;">
                      <label class="ga-title align-middle"><?php esc_html_e("Product list","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </th>
                    <td>
                      <?php $tvc_product_list_data_collection_method = isset($data['tvc_product_list_data_collection_method'])?$data['tvc_product_list_data_collection_method']:"woocommerce_after_shop_loop_item"; 
                        $list = array(
                          "woocommerce_before_shop_loop_item" => "woocommerce_before_shop_loop_item (default hook)",
                          "woocommerce_after_shop_loop_item" => "woocommerce_after_shop_loop_item (default hook)",
                          "woocommerce_before_shop_loop_item_title" => "woocommerce_before_shop_loop_item_title (default hook)",
                          "woocommerce_shop_loop_item_title" => "woocommerce_shop_loop_item_title (default hook)",
                          "woocommerce_after_shop_loop_item_title" => "woocommerce_after_shop_loop_item_title (default hook)",
                          "conversios_shop_loop_item" => "conversios_shop_loop_item (conversios hook)"
                        ); ?>
                        <select name="tvc_product_list_data_collection_method" class="data_collection_method">
                          <?php if(!empty($list)){
                            foreach($list as $key => $val){
                              $selected = ($tvc_product_list_data_collection_method == $key)?"selected":"";
                              ?>
                              <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                              <?php
                            }
                          }?>
                        </select>
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("When product impressions, clicks or add to cart google analytics ecommerce events are not working on your store, select the implemented hook from the dropdown.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                        <small>(At home, collection, shop, product details and cart page)</small>
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0 1rem;">
                      <label class="ga-title align-middle"><?php esc_html_e("Product detail page","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </th>
                    <td>
                      <?php $tvc_product_detail_data_collection_method = isset($data['tvc_product_detail_data_collection_method'])?$data['tvc_product_detail_data_collection_method']:"woocommerce_after_single_product"; 
                        $list = array(
                          "woocommerce_before_single_product" => "woocommerce_before_single_product (default hook)",
                          "woocommerce_after_single_product" => "woocommerce_after_single_product (default hook)",
                          "woocommerce_single_product_summary" => "woocommerce_single_product_summary (default hook)",
                          "conversios_single_product" => "conversios_single_product (conversios hook)",
                          "on_page" => "On page load"
                        ); ?>
                        <select name="tvc_product_detail_data_collection_method" class="data_collection_method">
                          <?php if(!empty($list)){
                            foreach($list as $key => $val){
                              $selected = ($tvc_product_detail_data_collection_method == $key)?"selected":"";
                              ?>
                              <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                              <?php
                            }
                          }?>
                        </select>
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("When your product detail page is not being tracked in google analytics (view_item), try changing the relevant hook from the dropdown.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0 1rem;">
                      <label class="ga-title align-middle"><?php esc_html_e("Checkout page","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </th>
                    <td>
                      <?php 
                      $tvc_checkout_data_collection_method = isset($data['tvc_checkout_data_collection_method'])?$data['tvc_checkout_data_collection_method']:"woocommerce_before_checkout_form";
                      $list = array(
                        "woocommerce_before_checkout_form" => "woocommerce_before_checkout_form (default hook)",
                        "woocommerce_after_checkout_form" => "woocommerce_after_checkout_form (default hook)",
                        "woocommerce_checkout_billing" => "woocommerce_checkout_billing (default hook)",
                        "woocommerce_checkout_shipping" => "woocommerce_checkout_shipping (default hook)",
                        "woocommerce_checkout_order_review" => "woocommerce_checkout_order_review (default hook)",
                        "conversios_checkout_form" => "conversios_checkout_form (conversios hook)",
                        "on_page" =>"On page load"
                      ); ?>
                      <select name="tvc_checkout_data_collection_method" class="data_collection_method">
                        <?php if(!empty($list)){
                          foreach($list as $key => $val){
                            $selected = ($tvc_checkout_data_collection_method == $key)?"selected":"";
                            ?>
                            <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                            <?php
                          }
                        }?>
                      </select>
                      <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("When your checkout is not being tracked in google analytics (checkout events), try changing the relevant hook from the dropdown.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0 1rem;">
                      <label class="ga-title align-middle"><?php esc_html_e("Order confirmation page","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </th>
                    <td>
                      <?php 
                      $tvc_thankyou_data_collection_method = isset($data['tvc_thankyou_data_collection_method'])?$data['tvc_thankyou_data_collection_method']:"woocommerce_thankyou";
                      $list = array(
                        "woocommerce_thankyou" => "woocommerce_thankyou (default hook)",
                        "woocommerce_before_thankyou" => "woocommerce_before_thankyou (default hook)",
                        "conversios_thankyou" => "conversios_thankyou (conversios hook)",
                        "on_page" =>"On page load"
                      ); ?>
                      <select name="tvc_thankyou_data_collection_method" class="data_collection_method">
                        <?php if(!empty($list)){
                          foreach($list as $key => $val){
                            $selected = ($tvc_thankyou_data_collection_method == $key)?"selected":"";
                            ?>
                            <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                            <?php
                          }
                        }?>
                      </select>
                     <div class="tvc-tooltip">
                        <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("When your transactions are not being tracked in google analytics (purchase event), try changing the relevant hook from the dropdown.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                      </div>
                    </td>
                  </tr>                  
                  <tr>
                    <th colspan="2">
                      <span>
                        <?php esc_html_e("Event selector","enhanced-e-commerce-for-woocommerce-store"); ?>
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("If you change your front end class or id for below events, select/input the changed class or id.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                      </span>
                    </th>
                  </tr>
                  <tr>
                    <th style="padding: 0 1rem;">
                      <label class="ga-title align-middle"><?php esc_html_e("Product page AddToCart button","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </th>
                    <td>
                      <?php $tvc_product_detail_addtocart_selector = (isset($data['tvc_product_detail_addtocart_selector']) && $data['tvc_product_detail_addtocart_selector'])?$data['tvc_product_detail_addtocart_selector']:"default"; 
                      $list = array(
                        "default" => "default",
                        "custom" => "custom"
                      ); ?>
                      <select name="tvc_product_detail_addtocart_selector" class="select-sm css-selector">
                        <?php if(!empty($list)){
                          foreach($list as $key => $val){
                            $selected = ($tvc_product_detail_addtocart_selector == $key)?"selected":"";
                            ?>
                            <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                            <?php
                          }
                        }?>
                      </select>

                      <span class="tvc-css-selector-sec default-selector-sec <?php echo ($tvc_product_detail_addtocart_selector != "default")?"tvc-hide":""; ?>">
                        <input type="text" class="select-sm" value="class" disabled>                          
                        <input type="text"  class="fromfiled default_selector_val" value="single_add_to_cart_button" disabled>
                      </span>

                      <span class="tvc-css-selector-sec <?php echo ($tvc_product_detail_addtocart_selector == "default")?"tvc-hide":""; ?>">
                        <?php $tvc_product_detail_addtocart_selector_type = (isset($data['tvc_product_detail_addtocart_selector_type']) && $data['tvc_product_detail_addtocart_selector_type'])?$data['tvc_product_detail_addtocart_selector_type']:""; 
                        $list = array(
                          "id" => "id",
                          "class" => "class"
                        ); ?>
                        <select name="tvc_product_detail_addtocart_selector_type" class="select-sm">
                          <?php if(!empty($list)){
                            foreach($list as $key => $val){
                              $selected = ($tvc_product_detail_addtocart_selector_type == $key)?"selected":"";
                              ?>
                              <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                              <?php
                            }
                          }?>
                        </select>
                        <?php $tvc_product_detail_addtocart_selector_val = isset($data['tvc_product_detail_addtocart_selector_val'])?$data['tvc_product_detail_addtocart_selector_val']:""; ?>
                        <input type="text"  class="fromfiled" name="tvc_product_detail_addtocart_selector_val" id="tvc_product_detail_addtocart_selector_val" value="<?php echo esc_attr($tvc_product_detail_addtocart_selector_val); ?>">
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Enter your button selector (id or calss) value. You can add multiple classes using comma separated string.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                      </span>                      
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0 1rem;">
                      <label class="ga-title align-middle"><?php esc_html_e("Checkout Step 2","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </th>
                    <td>
                      <?php $tvc_checkout_step_2_selector = ( isset($data['tvc_checkout_step_2_selector']) && $data['tvc_checkout_step_2_selector'] )?$data['tvc_checkout_step_2_selector']:"default"; 
                      $list = array(
                        "default" => "default",
                        "custom" => "custom"
                      ); ?>
                      <select name="tvc_checkout_step_2_selector" class="select-sm css-selector">
                        <?php if(!empty($list)){
                          foreach($list as $key => $val){
                            $selected = ($tvc_checkout_step_2_selector == $key)?"selected":"";
                            ?>
                            <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                            <?php
                          }
                        }?>
                      </select>
                      <span class="tvc-css-selector-sec default-selector-sec <?php echo ($tvc_checkout_step_2_selector != "default")?"tvc-hide":""; ?>">
                        <input type="text" class="select-sm" value="name" disabled>
                          
                        <input type="text"  class="fromfiled default_selector_val" value="input[name=billing_first_name]" disabled>
                      </span>
                      <span class="tvc-css-selector-sec <?php echo ($tvc_checkout_step_2_selector == "default")?"tvc-hide":""; ?>">
                        <?php $tvc_checkout_step_2_selector_type = isset($data['tvc_checkout_step_2_selector_type'])?$data['tvc_checkout_step_2_selector_type']:""; 
                        $list = array(
                          "id" => "id",
                          "class" => "class"
                        ); ?>
                        <select name="tvc_checkout_step_2_selector_type" class="select-sm">
                          <?php if(!empty($list)){
                            foreach($list as $key => $val){
                              $selected = ($tvc_checkout_step_2_selector_type == $key)?"selected":"";
                              ?>
                              <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                              <?php
                            }
                          }?>
                        </select>
                        <?php $tvc_checkout_step_2_selector_val = isset($data['tvc_checkout_step_2_selector_val'])?$data['tvc_checkout_step_2_selector_val']:""; ?>
                        <input type="text"  class="fromfiled" name="tvc_checkout_step_2_selector_val" id="tvc_checkout_step_2_selector_val" value="<?php echo esc_attr($tvc_checkout_step_2_selector_val); ?>">
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Enter your selector (id or calss) value. You can add multiple classes using comma separated string.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th style="padding: 0 1rem;">
                      <label class="ga-title align-middle"><?php esc_html_e("Checkout Step 3","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </th>
                    <td>
                      <?php $tvc_checkout_step_3_selector = ( isset($data['tvc_checkout_step_3_selector']) && $data['tvc_checkout_step_3_selector'] )?$data['tvc_checkout_step_3_selector']:"default"; 
                      $list = array(
                        "default" => "default",
                        "custom" => "custom"
                      ); ?>
                      <select name="tvc_checkout_step_3_selector" class="select-sm css-selector">
                        <?php if(!empty($list)){
                          foreach($list as $key => $val){
                            $selected = ($tvc_checkout_step_3_selector == $key)?"selected":"";
                            ?>
                            <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                            <?php
                          }
                        }?>
                      </select>
                      <span class="tvc-css-selector-sec default-selector-sec <?php echo ($tvc_checkout_step_3_selector != "default")?"tvc-hide":""; ?>">
                        <input type="text" class="select-sm" value="id" disabled>
                          
                        <input type="text"  class="fromfiled default_selector_val" value="place_order" disabled>
                      </span>
                      <span class="tvc-css-selector-sec <?php echo ($tvc_checkout_step_3_selector == "default")?"tvc-hide":""; ?>">
                        <?php $tvc_checkout_step_3_selector_type = isset($data['tvc_checkout_step_3_selector_type'])?$data['tvc_checkout_step_3_selector_type']:""; 
                        $list = array(
                          "id" => "id",
                          "class" => "class"
                        ); ?>
                        <select name="tvc_checkout_step_3_selector_type" class="select-sm">
                          <?php if(!empty($list)){
                            foreach($list as $key => $val){
                              $selected = ($tvc_checkout_step_3_selector_type == $key)?"selected":"";
                              ?>
                              <option value="<?php echo esc_attr($key);?>" <?php echo $selected; ?>><?php echo esc_attr($val);?></option>
                              <?php
                            }
                          }?>
                        </select>
                        <?php $tvc_checkout_step_3_selector_val = isset($data['tvc_checkout_step_3_selector_val'])?$data['tvc_checkout_step_3_selector_val']:""; ?>
                        <input type="text"  class="fromfiled" name="tvc_checkout_step_3_selector_val" id="tvc_checkout_step_3_selector_val" value="<?php echo esc_attr($tvc_checkout_step_3_selector_val); ?>">
                        <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Enter your button selector (id or calss) value. You can add multiple classes using comma separated string.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div>
                      </span>
                    </td>
                  </tr>
                </table>
              </div>
              <!-- End Google Analytics Event Tracking - Custom Integration settings for GA-->
              <!-- start Google Ads Setting section -->               
              <div class="ga-title con_tracking_integration con_faq_title" data-id="sec_con_integration_GAds">
                <?php esc_html_e("Google Ads Settings","enhanced-e-commerce-for-woocommerce-store"); ?>
                <div class="tvc-tooltip">
                  <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e(" Advance Setting.","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                </div>
                <img class="faq_icon" style="height: 20px;" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/arrow-down-sign.png"); ?>" >
              </div>
              <div class="sec_con_integration" id="sec_con_integration_GAds">
                <?php //google ads code start
                if (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != '') { ?>
                  <label class="align-middle">
                    <div class="tvc-custom-control tvc-custom-checkbox">
                    <input type="checkbox" class="tvc-custom-control-input" id="customCheck1" name="remarketing_tags" value="1" <?php echo (esc_attr($googleDetail->remarketing_tags) == 1) ? 'checked="checked"' : ''; ?> >
                    <label class="custom-control-label" for="customCheck1"><?php esc_html_e("Enable remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </div>
                  </label>                
                  <label class="align-middle">
                    <div class="tvc-custom-control tvc-custom-checkbox">
                    <input type="checkbox" class="tvc-custom-control-input" id="customCheck2" name="dynamic_remarketing_tags" value="1" <?php echo (esc_attr($googleDetail->dynamic_remarketing_tags) == 1) ? 'checked="checked"' : ''; ?>>
                    <label class="custom-control-label" for="customCheck2"><?php esc_html_e("Enable dynamic remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                  </div>
                  </label>                
                  <label class="align-middle">
                    <div class="tvc-custom-control tvc-custom-checkbox">
                      <input type="checkbox" class="tvc-custom-control-input" id="customCheck3" name="link_google_analytics_with_google_ads" value="1" <?php echo (esc_attr($googleDetail->link_google_analytics_with_google_ads) == 1) ? 'checked="checked"' : ''; ?> >
                      <label class="custom-control-label" for="customCheck3"><?php esc_html_e("Link Google analytics with google ads","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    </div>
                  </label> 
                <?php }else{ ?>
                    <h2 class="ga-title"><?php esc_html_e("Connect Google Ads account to enable below features.","enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                    <label class="align-middle">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg'); ?>" alt="configuration  success" class="config-success"><label class="custom-control-label"><?php esc_html_e("Enable remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></label></label>
                    <label class="align-middle">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg'); ?>" alt="configuration  success" class="config-success"><label class="custom-control-label"><?php esc_html_e("Enable dynamic remarketing tags","enhanced-e-commerce-for-woocommerce-store"); ?></label></label>
                    <label class="align-middle"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/config-success.svg'); ?>" alt="configuration  success" class="config-success"><label class="custom-control-label"><?php esc_html_e("Link Google analytics with google ads","enhanced-e-commerce-for-woocommerce-store"); ?></label></label>
                <?php
                  } ?>
                <label class="align-middle">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/lock-orange.png'); ?>" class="config-success">
                    <label class="custom-control-label">
                      <?php esc_html_e("Enable Google Ads conversion tracking","enhanced-e-commerce-for-woocommerce-store"); ?>
                      <?php echo $TVC_Admin_Helper->get_conv_pro_link("pixel_setting"); ?>
                    </label>
                    <?php $is_g_ad_c_tracking = (property_exists($googleDetail,"google_ads_conversion_tracking") && $googleDetail->google_ads_conversion_tracking == 1)?"1":"0"; ?>
                    <input type="hidden" name="google_ads_conversion_tracking" value="<?php echo esc_attr($is_g_ad_c_tracking); ?>" >
                  </label>                      
                  <label class="align-middle">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/lock-orange.png'); ?>" class="config-success">
                    <label class="custom-control-label"><?php esc_html_e("Enable Google Ads Enhanced conversion tracking","enhanced-e-commerce-for-woocommerce-store"); ?>
                    <?php echo $TVC_Admin_Helper->get_conv_pro_link("pixel_setting"); ?>
                    </label>
                    <?php $ga_EC = get_option("ga_EC"); ?>
                    <?php $ee_conversio_send_to = get_option('ee_conversio_send_to'); ?>

                    <input type="hidden" name="ee_conversio_send_to" id="ee_conversio_send_to" value="<?php echo esc_attr($ee_conversio_send_to);?>">
                    <input type="hidden" name="ga_EC" value="<?php echo esc_attr($ga_EC); ?>" >
                  </label>
              </div>
              <!-- End Google Ads Setting section -->
            </div>
            <!-- End Advance settings section -->

            <!-- Start badge settings -->
            <div class="con-setting-container">
              <h4><?php esc_html_e("Trusted Partner Badge Settings","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
              <table class="table">
                <tbody>
                <tr>
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/badge_showhide.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Show badge:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          <small><?php esc_html_e("Develop the trust of your store visitors by displaying Conversios Badge in your website footer","enhanced-e-commerce-for-woocommerce-store"); ?></small>
                        </div>
                      </div>
                    </th>
                    <td>
                      <div class="show_hide_badge_radio">
                        <label>
                          <input type="radio" name="conv_show_badge" <?php echo !empty($data['conv_show_badge']) && esc_attr($data['conv_show_badge']) == "yes" ? "checked" : ""; ?> value="yes">
                            Yes
                        </label>
                        <label>
                          <input type="radio" name="conv_show_badge" <?php echo empty($data['conv_show_badge']) || esc_attr($data['conv_show_badge']) == "no" ? "checked" : ""; ?> value="no">
                            No
                        </label>
                      </div>
                    </td>
                  </tr>
                  <tr class="only-for-badgeshow <?php echo !empty($data['conv_show_badge']) && esc_attr($data['conv_show_badge']) == "yes" ? "" : "tvc-hide"; ?>">
                    <th>
                      <div class="pixel-logo-text-left">
                        <div class="pixel-logo">
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/badge_position.png'); ?>"/>
                        </div>
                        <div class="pixel-text">
                          <label><?php esc_html_e("Badge position:","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                        </div>
                      </div>
                    </th>
                    <td>
                      <div class="show_hide_badge_radio">
                        <label>
                          <input type="radio" name="conv_badge_position" <?php echo !empty($data['conv_badge_position']) && esc_attr($data['conv_badge_position']) == "left" ? "checked" : ""; ?> value="left">
                          Bottom-Left
                        </label>
                        <label>
                          <input type="radio" name="conv_badge_position" <?php echo empty($data['conv_badge_position']) || esc_attr($data['conv_badge_position']) == "center" ? "checked" : ""; ?> value="center">
                          Bottom-Center
                        </label>
                        <label>
                          <input type="radio" name="conv_badge_position" <?php echo !empty($data['conv_badge_position']) && ($data['conv_badge_position']) == "right" ? "checked" : ""; ?> value="right">
                          Bottom-Right
                        </label>
                      </div>
                    </td>
                  </tr> 
                </tbody>
                </table>
            </div>
            <!-- End bedge settings -->  


            <h4><label for="ga_PrivacyPolicy"><?php esc_html_e("Privacy Policy","enhanced-e-commerce-for-woocommerce-store"); ?></label></h4>
            <div class="con-setting-container-last">              
              <label class="align-middle">
                <?php $ga_PrivacyPolicy = !empty($data['ga_PrivacyPolicy']) ? 'checked' : ''; ?>
                <input type="checkbox" name="ga_PrivacyPolicy" id="ga_PrivacyPolicy" required="required" <?php echo esc_attr($ga_PrivacyPolicy); ?>>
                <label class="custom-control-label" for="ga_PrivacyPolicy"><?php esc_html_e("Accept Privacy Policy of Plugin","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                
                <p class="description"><?php esc_html_e("By using Conversios plugin, you agree to Conversios plugin's","enhanced-e-commerce-for-woocommerce-store"); ?> <a href= "<?php echo esc_url_raw("https://www.conversios.io/privacy-policy/?ref=plugin_policy&utm_source=plugin_backend&utm_medium=woo_premium_plugin&utm_campaign=GDPR_complaince_ecomm_plugins"); ?>" target="_blank"><?php esc_html_e("Privacy Policy","enhanced-e-commerce-for-woocommerce-store"); ?></a></p>
              </label>                    
            </div>
            <p class="submit save-for-later" id="save-for-later">
              <input type="hidden" id="ga_id" name = "ga_id" value="<?= esc_attr((!empty($data['ga_id']))?$data['ga_id']:""); ?>"/>
              <input type="hidden" id="gm_id" name = "gm_id" value="<?= esc_attr((!empty($data['gm_id']))?$data['gm_id']:""); ?>"/>
              <input type="hidden" id="google_ads_id" name = "google_ads_id" value="<?= esc_attr((!empty($data['google_ads_id']))?$data['google_ads_id']:""); ?>"/>
              <input type="hidden" id="google_merchant_id" name = "google_merchant_id" value="<?= esc_attr((!empty($data['google_merchant_id']))?$data['google_merchant_id']:""); ?>"/>
              <input type="hidden" name="subscription_id" value="<?php echo esc_attr((!empty($data['subscription_id']))?$data['subscription_id']:""); ?>">
              <?php wp_nonce_field( 'conv_settings_nonce_save', 'conv_settings_nonce_field' ); ?>
              <button type="submit"  class="btn btn-primary" id="ee_submit_plugin" name="ee_submit_plugin"><?php esc_html_e("Save","enhanced-e-commerce-for-woocommerce-store"); ?></button>
            </p>
          </form>
        </div>
        <div class="col-md-6 col-lg-4">
          <?php echo get_tvc_google_ga_sidebar(); ?>
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
  echo get_connect_google_popup_html_to_active_licence();
?>
<script>
jQuery(document).on('click','.con_edit_text', function(event) {
  event.preventDefault();
  jQuery("#con_conversion_label").removeAttr('disabled');
});



jQuery(document).on('click','.con_conversion_label', function(event) {
      event.preventDefault();
      var data = {
        action: "con_get_conversion_list",
        TVCNonce : "<?php echo wp_create_nonce('con_get_conversion_list-nonce'); ?>"
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
          if(response == 0) { 
            jQuery('.google_conversion_label_message').show();
            jQuery('.google_ads_conversion_sec_static').show();
            jQuery('#con_conversion_label').hide();
            tvc_helper.loaderSection(false);
            return;
          }else{
            var response;           
            var con_select='<option value="">Please select conversion label</option>';
            for (var key in response){               
              con_select +='<option value="'+response[key]+'">'+response[key]+'</option>';
            }
            document.getElementById('con_conversion_label').innerHTML = con_select;
            jQuery("#con_conversion_label").removeClass("tvc-hide");
            jQuery('.con_conversion_label').hide();
            tvc_helper.loaderSection(false);
          }
        }

      });
});


jQuery(document).ready(function () {
  
  jQuery('.conv_multiselect2_input').select2({
    closeOnSelect : false,
		tags: true,
    createTag: function () {
      return null;
    }
  });

  jQuery(document).on('submit','#ee_plugin_form',function(e){
    if(jQuery('.google_ads_conversion_sec_static').is(":visible") && jQuery("#ee_conversio_send_to_static").val() != "")
    {
      var inpval = jQuery("#ee_conversio_send_to_static").val();
      var regex = /^AW-+[0-9{5,}]+[\/]+[a-zA-Z0-9{5,}]/;
      if( regex.test(inpval) )
      {
        //console.log('Pass');
      }
      else
      {
        e.preventDefault();
        //console.log('Fail');
        jQuery("#ee_conversio_send_to_static_msg").show();
      }
    }
  });

  jQuery(document).on('click','.con_faq_title',function(event){
    let faq_id = jQuery(this).attr("data-id");
    jQuery(this).toggleClass('active');
    jQuery('#'+faq_id).toggleClass('active');
  });

  jQuery(document).on('change','.css-selector',function(event){
    //console.log(jQuery(this).val());
    if(jQuery(this).val() == "custom"){
      //console.log(jQuery(this).next());
      jQuery(this).next().addClass("tvc-hide");
      jQuery(this).next().next().removeClass("tvc-hide");
    }else{
      jQuery(this).next().next().addClass("tvc-hide");
      jQuery(this).next().removeClass("tvc-hide");
    }
  });
  //Event Tracking on change hide additional pixels for GTM
  jQuery(document).on('change','#tracking_method',function(event){
    if(jQuery(this).val() != "gtm"){
      jQuery(".only-for-gtm").addClass("tvc-hide");
      jQuery(".not-for-gtm").removeClass("tvc-hide");
      jQuery(".only-for-gtm-lock").val("");
      jQuery(".only-for-gtm-lock").prop('disabled', true);
    }else{
      jQuery(".only-for-gtm").removeClass("tvc-hide");
       jQuery(".not-for-gtm").addClass("tvc-hide");
      jQuery(".only-for-gtm-lock").prop('disabled', false);
    }
  });

  jQuery('input[type=radio][name=conv_show_badge]').change(function() {
    if (jQuery(this).val() == 'yes') {
        jQuery(".only-for-badgeshow").show();
      }
      else if (jQuery(this).val() == 'no') {
        jQuery(".only-for-badgeshow").hide();
      }
  });

  /*if (jQuery("#want_to_use_your_gtm").is(":checked")) {
    jQuery("#use_your_gtm_id").removeClass("tvc-hide");
  }else{
    jQuery("#use_your_gtm_id").addClass("tvc-hide");
  }

  jQuery("#want_to_use_your_gtm").click(function (){
    console.log("cafdfsd");
    if (jQuery("#want_to_use_your_gtm").is(":checked")) {
      jQuery("#use_your_gtm_id").removeClass("tvc-hide");
    }else{
      jQuery("#use_your_gtm_id").addClass("tvc-hide");
      //jQuery("#use_your_gtm_id").val("");
    }
  });*/

  let want_to_use_your_gtm = jQuery("input[type=radio][name=want_to_use_your_gtm]:checked").attr("id");
  //console.log(want_to_use_your_gtm);
  if(want_to_use_your_gtm != ""){
    jQuery(".slctunivr-filed_gtm").slideUp();
    jQuery("#htnl_"+want_to_use_your_gtm).slideDown();
  }
  jQuery("input[type=radio][name=want_to_use_your_gtm]").on( "change", function() {
    let want_to_use_your_gtm = jQuery(this).attr("id");
    //console.log(want_to_use_your_gtm);
    //is_validate_step("step_1");
    jQuery(".slctunivr-filed_gtm").slideUp();
    jQuery("#htnl_"+want_to_use_your_gtm).slideDown();            
  });

  jQuery(document).on('submit','form#ee_plugin_form',function(event){
    if(jQuery('select[name="tvc_product_detail_addtocart_selector"]').val() == "custom" ){
      if(jQuery('input[name="tvc_product_detail_addtocart_selector_val"]').val() == "" ){
        jQuery('input[name="tvc_product_detail_addtocart_selector_val"]').focus();
        jQuery('input[name="tvc_product_detail_addtocart_selector_val"]').css("border","1px #f10909 solid")
        return false;
      }
    }
    if(jQuery('select[name="tvc_checkout_step_2_selector"]').val() == "custom" ){
      if(jQuery('input[name="tvc_checkout_step_2_selector_val"]').val() == "" ){
        jQuery('input[name="tvc_checkout_step_2_selector_val"]').focus();
        jQuery('input[name="tvc_checkout_step_2_selector_val"]').css("border","1px #f10909 solid")
        return false;
      }
    }
    if(jQuery('select[name="tvc_checkout_step_3_selector"]').val() == "custom" ){
      if(jQuery('input[name="tvc_checkout_step_3_selector_val"]').val() == "" ){
        jQuery('input[name="tvc_checkout_step_3_selector_val"]').focus();
        jQuery('input[name="tvc_checkout_step_3_selector_val"]').css("border","1px #f10909 solid")
        return false;
      }
    }
  });
  //pixel validation
  jQuery(document).ready(function(){  
    jQuery("#fb_pixel_id,#microsoft_ads_pixel_id,#twitter_ads_pixel_id,#pinterest_ads_pixel_id,#snapchat_ads_pixel_id,#tiKtok_ads_pixel_id").blur(function(){  
      var ele_id = this.id;
      var ele_val = jQuery(this).val();
      var regex_arr = {
        fb_pixel_id: new RegExp(/^\d{14,16}$/m),
        microsoft_ads_pixel_id: new RegExp(/^\d{7,9}$/m),
        twitter_ads_pixel_id: new RegExp(/^[a-z0-9]{5,7}$/m),
        pinterest_ads_pixel_id: new RegExp(/^\d{13}$/m),
        snapchat_ads_pixel_id: new RegExp(/^[a-z0-9\-]*$/m),
        tiKtok_ads_pixel_id: new RegExp(/^[A-Z0-9]{20,20}$/m)
      };
      if(ele_val.match(regex_arr[ele_id]) || ele_val===""){
         jQuery(this).removeClass("redinvalid");
        }else{
         jQuery(this).addClass("redinvalid");
      }
      if(jQuery("#fb_pixel_id,#microsoft_ads_pixel_id,#twitter_ads_pixel_id,#pinterest_ads_pixel_id,#snapchat_ads_pixel_id,#tiKtok_ads_pixel_id").hasClass("redinvalid"))
			{
				jQuery('#ee_submit_plugin').attr('disabled', true);
        jQuery('#ee_submit_plugin').addClass('convdisabled');
			}
			else{
				jQuery( "#ee_submit_plugin" ).prop( "disabled", false );
        jQuery('#ee_submit_plugin').removeClass('convdisabled');
			}
    });  
   });  

  /*facebook*/
  jQuery("#fb_pixel_id").keypress(function (evt){
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /[-\d\.]/; // dowolna liczba (+- ,.) :)
    var objRegex = /^-?\d*[\.]?\d*$/;
    var val = $(evt.target).val();
    if(!regex.test(key) || !objRegex.test(val+key) || 
            !theEvent.keyCode == 46 || !theEvent.keyCode == 8) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    };
  });

  jQuery(document).on('click','#tvc_google_connect_active_licence_close',function(event){
    jQuery('#tvc_google_connect_active_licence').modal('hide');
  });
  jQuery(document).on('click','.tvc_licence_key_change',function(event){
    jQuery(".tvc_licence_key_change_wapper").slideUp(500);
    jQuery(".tvc_licence_key_wapper").slideDown(700);
  });
  jQuery(document).on('submit','form#tvc-licence-active',function(event){
    event.preventDefault();
    let licence_key = jQuery("#licence_key").val();
    var form_data = jQuery("#tvc-licence-active").serialize();
    if(licence_key!=""){
      var data = {
        action: "tvc_call_active_licence",
        licence_key:licence_key,
        conv_licence_nonce: "<?php echo wp_create_nonce('conv_lic_nonce'); ?>"        
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
            setTimeout(function(){ 
              location.reload();
            }, 2000);
          }else{
            if( response.is_connect == false){    
              jQuery('#tvc_google_connect_active_licence').modal('show');          
            }else{
              tvc_helper.tvc_alert("error","",response.message);
            }
          }
          tvc_helper.loaderSection(false);
        }
      });
    }else{
      tvc_helper.tvc_alert("error","Licence key is required.");
    }
  });
});
  
   jQuery(function () {  
        jQuery("#google_ads_conversion_tracking").click(function () {  
            if (jQuery("#google_ads_conversion_tracking").is(":checked")) {  
                jQuery('#google_ads_conversion_sec :input').removeAttr('disabled'); 
            } else {  
                //To disable all input elements within div use the following code:  
                jQuery('#google_ads_conversion_sec :input').attr('disabled', 'disabled');  
            }  
        });  
    });  

  jQuery('#google_ads_conversion_tracking').click(function(){
    if(!this.checked){
          jQuery("#ga_EC").prop("checked", false);
           }else{
          jQuery("#ga_EC").prop("checked", true);
        }
      });
    </script>