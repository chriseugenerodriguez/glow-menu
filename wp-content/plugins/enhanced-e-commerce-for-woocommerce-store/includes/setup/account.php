<?php
class TVC_Account {
  protected $TVC_Admin_Helper="";
  protected $url = "";
  protected $subscriptionId = "";
  protected $google_detail;
  protected $customApiObj;
  public function __construct() {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->customApiObj = new CustomApi();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data(); 
    $this->TVC_Admin_Helper->add_spinner_html();     
    $this->create_form();
  }

  public function create_form() {
    $message = ""; $class="";        
    $googleDetail = [];
    $plan_name =  esc_html__("Free Plan","enhanced-e-commerce-for-woocommerce-store");
    $plan_price = esc_html__("Free","enhanced-e-commerce-for-woocommerce-store");
    $api_licence_key=""; 
    $paypal_subscr_id = "";   
    $product_sync_max_limit ="100";    
    $activation_date = "";
    $next_payment_date = "";
    //$subscription_type = "";
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
	<div class="tab-pane show active" id="tvc-account-page">
		<div class="tab-card" >
			<div class="row">
        <div class="col-md-10 col-lg-10 border-right">
          
          <div class="licence tvc-licence" >            
            <div class="tvc_licence_key_wapper ">              
                <p><?php esc_html_e("You are using our free plugin, no licence needed ! Happy analyzing..!! :)","enhanced-e-commerce-for-woocommerce-store"); ?></p>
                <p class="font-weight-bold"><?php esc_html_e("To unlock more features of google products, consider our","enhanced-e-commerce-for-woocommerce-store"); ?> <a href="<?php echo esc_url_raw($this->TVC_Admin_Helper->get_conv_pro_link("account_summary", "", "linkonly")); ?>" target="_blank"><?php esc_html_e("pro version.","enhanced-e-commerce-for-woocommerce-store"); ?></a></p>
                <p>
                  <b>
                    <?php esc_html_e("For Pro users, if you are facing difficulty in accessing premium features,","enhanced-e-commerce-for-woocommerce-store"); ?>
                    <a href="<?php echo esc_url_raw('https://www.conversios.io/help-center/Premium-Zip---Migration---User-Manual-&-FAQs.pdf'); ?>" target="_blank"><?php esc_html_e("refer this document to make changes.","enhanced-e-commerce-for-woocommerce-store"); ?></a>
                  </b>
                </p>
            </div>          
            <div class="google-account-analytics tvc_licence_key_change_wapper tvc-hide ">
              <div class="acc-num">
                <label class="ga-title tvc_licence_key_title"><?php esc_html_e("Licence key:","enhanced-e-commerce-for-woocommerce-store"); ?></label> 
                <p class="ga-text tvc_licence_key"><?php echo esc_attr($api_licence_key); ?></p>
                <p class="ga-text text-right tvc_licence_key_change"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/refresh.svg'); ?>" alt="active licence key"></p>
              </div>
            </div>          
          </div>

          <div class="tvc-table">
            <strong><?php esc_html_e("Account Summary","enhanced-e-commerce-for-woocommerce-store"); ?></strong>
            <table>
              <tbody>
                <tr><th><?php esc_html_e("Plan name","enhanced-e-commerce-for-woocommerce-store"); ?></th><td><?php echo esc_attr($plan_name); ?></td></tr>
                <tr><th><?php esc_html_e("Plan price","enhanced-e-commerce-for-woocommerce-store"); ?></th><td><?php echo esc_attr($plan_price); ?></td></tr>
                <tr><th><?php esc_html_e("Product sync limit","enhanced-e-commerce-for-woocommerce-store"); ?></th><td><?php echo esc_attr($product_sync_max_limit); ?></td></tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6 col-lg-4"></div>
      </div>
    </div>
	</div>
</div>
<?php echo get_connect_google_popup_html_to_active_licence();?>
<script>
jQuery(document).ready(function () {
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
</script>
<?php
    }
}
?>