<?php
class TVC_Pricings {
  protected $TVC_Admin_Helper="";
  protected $url = "";
  protected $subscriptionId = "";
  protected $google_detail;
  protected $customApiObj;
  protected $pro_plan_site;

  public function __construct() {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->customApiObj = new CustomApi();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data(); 
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->pro_plan_site = $this->TVC_Admin_Helper->get_pro_plan_site();     
    $this->create_form();
  }

  public function create_form() {
    $close_icon = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close.png');
    $check_icon = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/check.png');
    ?>
<div class="con-tab-content">
	<div class="tab-pane show active" id="tvc-account-page">
		<div class="tab-card" >			
       <div class="tvc-price-table-features columns-5">
        <div class="tvc-container"> 
          <div class="clearfix">
            <div class="row-heading clearfix">
              <div class="column tvc-blank-col">
              </div>
              <div class="column column-price tvc-blank-col">
                <div class="tvc_popular">
                  <div class="tvc_popular_inner"><?php esc_html_e("Festive Plans - Limited Time","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                </div>
              </div>
            </div>
            <div class="row-heading clearfix">
               <div class="column tvc-blank-col"><span><?php esc_html_e("Features","enhanced-e-commerce-for-woocommerce-store"); ?></span></div>
               <div class="column discounted ">
                  <div class="name-wrap"><div class="name"><?php esc_html_e("FREE","enhanced-e-commerce-for-woocommerce-store"); ?></div></div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">                      
                      <div class="price-current"><span class="inner"><?php esc_html_e("FREE","enhanced-e-commerce-for-woocommerce-store"); ?></span></div>
                      
                    </div>  
                    <a href="javascript:void(0)" class="btn tvc-btn current_active_plan"><?php esc_html_e("Currently Active","enhanced-e-commerce-for-woocommerce-store"); ?></a>                  
                  </div>              
               </div>
               <div class="column discounted ">
                  <div class="name-wrap"><div class="name"><?php esc_html_e("STARTER","enhanced-e-commerce-for-woocommerce-store"); ?></div></div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">
                      <div class="price-normal">
                        <div class="tvc-plan-off"><?php esc_html_e("1 active website","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                      </div>
                      <div class="price-current"><span class="inner"><?php printf("%s <span>%s</span>",esc_html_e("$89"),esc_html_e("/year")); ?></span></div>
                      <div class="tvc_if_pay_yearly"><?php esc_html_e("($7/month)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                      <div class="tvc_if_pay_month"><?php esc_html_e("$19/month if paid monthly","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                     <a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/checkout/?pid=planD_1_y&utm_source=EE+Plugin+User+Interface&utm_medium=STARTER&utm_campaign=Upsell+at+Conversios"); ?>" class="btn tvc-btn"><?php esc_html_e("Get Started","enhanced-e-commerce-for-woocommerce-store"); ?></a> 
                    </div>
                  </div>              
               </div>
               <div class="column discounted ">
                  <div class="name-wrap"><div class="name"><?php esc_html_e("BUSINESS","enhanced-e-commerce-for-woocommerce-store"); ?></div></div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">
                      <div class="price-normal">
                        <div class="tvc-plan-off"><?php esc_html_e("5 active website","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                      </div>                    
                      <div class="price-current"><span class="inner"><?php printf("%s <span>%s</span>",esc_html_e("$189"),esc_html_e("/year")); ?></span></div>
                     <div class="tvc_if_pay_yearly"><?php esc_html_e("($16/month)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                     <div class="tvc_if_pay_month"><?php esc_html_e("$49/month if paid monthly","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                      <a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/checkout/?pid=planD_2_y&utm_source=EE+Plugin+User+Interface&utm_medium=BUSINESS&utm_campaign=Upsell+at+Conversios"); ?>" class="btn tvc-btn"><?php esc_html_e("Get Started","enhanced-e-commerce-for-woocommerce-store"); ?></a>
                    </div>                    
                  </div>              
               </div>
               <div class="column discounted popular">                
                  <div class="name-wrap">
                     <div class="name"><?php esc_html_e("AGENCY","enhanced-e-commerce-for-woocommerce-store"); ?></div>                
                  </div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">
                      <div class="price-normal">
                        <div class="tvc-plan-off"><?php esc_html_e("10 active website","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                      </div>                      
                      <div class="price-current"><span class="inner"><?php printf("%s <span>%s</span>",esc_html_e("$289"),esc_html_e("/year")); ?></span></div>
                      <div class="tvc_if_pay_yearly"><?php esc_html_e("($24/month)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                     <div class="tvc_if_pay_month"><?php esc_html_e("$79/month if paid monthly","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                     <a target="_blank"  href="<?php echo esc_url_raw("https://conversios.io/checkout/?pid=planD_3_y&utm_source=EE+Plugin+User+Interface&utm_medium=AGENCY&utm_campaign=Upsell+at+Conversios"); ?>" class="btn tvc-btn"><?php esc_html_e("Get Started","enhanced-e-commerce-for-woocommerce-store"); ?></a>
                    </div>                    
                  </div>              
               </div>
               <div class="column discounted ">
                  <div class="name-wrap">
                     <div class="name"><?php esc_html_e("AGENCY PLUS","enhanced-e-commerce-for-woocommerce-store"); ?></div>                
                  </div>
                  <div class="tvc-list-price-month">
                    <div class="tvc-list-price">
                      <div class="price-normal">
                        <div class="tvc-plan-off"><?php esc_html_e("25 active website","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                      </div> 
                      <div class="price-current"><span class="inner"><?php printf("%s <span>%s</span>",esc_html_e("$389"),esc_html_e("/year")); ?></span></div>
                      <div class="tvc_if_pay_yearly"><?php esc_html_e("($32/month)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                     <div class="tvc_if_pay_month"><?php esc_html_e("$99/month if paid monthly","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                      <a target="_blank" href="<?php echo esc_url_raw("https://conversios.io/checkout/?pid=planD_4_y&utm_source=EE+Plugin+User+Interface&utm_medium=AGENCY+PLUS&utm_campaign=Upsell+at+Conversios"); ?>" class="btn tvc-btn"><?php esc_html_e("Get Started","enhanced-e-commerce-for-woocommerce-store"); ?></a>
                    </div>                    
                  </div>                
               </div>
            </div>
            <div class="row-subheading clearfix"><?php esc_html_e("Accessibility Features","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Active websites","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column">1</div>
               <div class="column">1</div>
               <div class="column">5</div>
               <div class="column popular ">10</div>
               <div class="column">25</div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Dedicated customer success manager","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Website audit for google analytics tracking and all the pixels","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("2 hours consultation with ecommerce and google shopping experts","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-footer clearfix">
               <div class="column"><?php esc_html_e("Priority Support (24*5)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-subheading clearfix"><?php esc_html_e("Google Tag Manager for Google Analytics and Pixels","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Using Conversios GTM","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span> <div class="tvc-tooltip">
                  <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("The plugin by default uses conversios GTM","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                </div></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Use you own GTM","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span> <div class="tvc-tooltip">
                          <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("Use your own GTM with the plugin for all the 64 tags and triggers","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                        </div></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Universal Analytics Tracking","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Analytics 4 Tracking","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Dual Set up (UA + GA4)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Ads pixel","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Ads Enhanced Conversion tracking","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Ads Conversion tracking","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Facebook pixel","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Microsoft Ads pixel","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Twitter Ads pixel","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Pinterest Ads pixel","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Snapchat Ads pixel","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("TiKTok Ads pixel","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Analytics and Google Ads linking","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Actionable Dashboard (GA3/ GA4)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
                <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Facebook conversion API","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Server side tagging","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div>
            <div class="row-subheading clearfix"><?php esc_html_e("Product Feed Management for Google Shopping","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Merchant Center account management","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Site verification","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Domain claim","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Products Sync via Content API","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 100)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Automatic Products Update","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(upto 100)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Unlimited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Schedule Product Sync","enhanced-e-commerce-for-woocommerce-store"); ?><span class="con_new_features">New</span> <div class="tvc-tooltip">
                  <span class="tvc-tooltiptext tvc-tooltip-right"><?php esc_html_e("You can set up frequency to update your product feed (ie. Daily, Weekly)","enhanced-e-commerce-for-woocommerce-store"); ?></span>
                  <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/icon/informationI.svg"); ?>" alt=""/>
                </div></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Google Ads and Google Merchant Center account linking","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Dynamic Remarketing Tags for eCommerce events","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Limited)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"><br><?php esc_html_e("(Complete)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Compatibility with Brands Plugin","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($close_icon); ?>" alt="no"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular "><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Performance max campaigns","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column popular"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
               <div class="column"><img src="<?php echo esc_url_raw($check_icon); ?>" alt="yes"></div>
            </div><div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Product filters for selected products sync","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div>
            <div class="row-feature clearfix">
               <div class="column"><?php esc_html_e("Facebook catalog feed","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column popular"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
               <div class="column"><?php esc_html_e("(Upcoming)","enhanced-e-commerce-for-woocommerce-store"); ?></div>
            </div>
            
            
            
          </div>
        </div>
      </div>
      <div class="tvc-guarantee">
        <div class="guarantee">
          <div class="title"><?php printf("<span>%s</span>%s", esc_html_e("15 Days","enhanced-e-commerce-for-woocommerce-store"), esc_html_e("100% No-Risk Money Back Guarantee!","enhanced-e-commerce-for-woocommerce-store")); ?></div>
          <div class="description"><?php esc_html_e("You are fully protected by our 100% No-Risk-Double-Guarantee. If you don’t like over the next 15 days, then we will happily refund 100% of your money. No questions asked.","enhanced-e-commerce-for-woocommerce-store"); ?></div>
        </div>
      </div>
    </div>
	</div>
</div>
<?php
    }
}
?>