<?php

class SyncProductConfiguration
{
protected $TVC_Admin_Helper;
protected $subscriptionId;
protected $TVC_Admin_DB_Helper;
protected $TVCProductSyncHelper;
public function __construct(){
  $this->includes();
	$this->TVC_Admin_Helper = new TVC_Admin_Helper();
  $this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
  $this->TVCProductSyncHelper = new TVCProductSyncHelper();
  $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();  
  $this->site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
  $this->TVC_Admin_Helper->need_auto_update_db(); 	
  $this->html_run();
}
public function includes(){
  if (!class_exists('TVCProductSyncHelper')) {
    require_once(__DIR__ . '/class-tvc-product-sync-helper.php');
  }
}
public function html_run(){
	$this->TVC_Admin_Helper->add_spinner_html();
  $this->create_form();
}

public function create_form(){
  if(isset($_GET['welcome_msg']) && sanitize_text_field($_GET['welcome_msg']) == true){
    $this->TVC_Admin_Helper->call_domain_claim();
    $class = 'notice notice-success';
    $message = esc_html__("Everthing is now set up. One more step - Sync your WooCommerce products into your Merchant Center and reach out to millions of shopper across Google.","enhanced-e-commerce-for-woocommerce-store");
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    ?>
    <script>
      jQuery(document).ready(function() {
        var msg="<?php echo esc_attr($message);?>"
        tvc_helper.tvc_alert("success", "<?php esc_html_e("Congratulation..!","enhanced-e-commerce-for-woocommerce-store"); ?>", msg, true);
      });
    </script>
    <?php
  }
	
	$syncProductStat = (object)[];
	$syncProductList = [];
  $last_api_sync_up ="";  
	$google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
	if(isset($google_detail['prod_sync_status'])){
    if ($google_detail['prod_sync_status']) {
      $syncProductStat = $google_detail['prod_sync_status'];
    }
  }
  global $wpdb;
  // $syncProductList = $this->TVC_Admin_DB_Helper->tvc_get_results("ee_products_sync_list");
  $syncProductList = $wpdb->get_results("select * from ".$wpdb->prefix."ee_products_sync_list LIMIT 2000");
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
  // $args = array('post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1);
  // $products = new WP_Query($args);
  // $woo_product = $products->found_posts;
  $woo_product = wp_count_posts( 'product' )->publish;

  $ee_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
  // print_r($ee_additional_data );
  // echo "=========";
  // print_r(_get_cron_array());
  $tablename = esc_sql( $wpdb->prefix ."ee_prouct_pre_sync_data" );
  $total_que_products = $wpdb->get_var("SELECT COUNT(*) as a FROM $tablename");
  $total_sync_products = $wpdb->get_var("SELECT COUNT(*) as a FROM $tablename where `status` = 1");
  $last_update_date_obj = $wpdb->get_row("SELECT update_date FROM $tablename where `status` = 1  ORDER BY update_date DESC");
  $prod_batch_response = unserialize(get_option('ee_prod_response'));
  $imin = $rhr = $rsec = $rmin = $sec = $total_batch_size = $total_pending_pro = $total_batches = $total_seconds = 0;
  $min = 1;
  if (!empty($last_update_date_obj)) {
    $last_update_date = $last_update_date_obj->update_date;
    //$interval = (new DateTime($last_update_date))->diff(new DateTime());
    $interval = (new DateTime($last_update_date))->diff(new DateTime(date('Y-m-d H:i:s', current_time( 'timestamp' ))));
    $imin = $interval->days * 24 * 60;
    $imin += $interval->h * 60;
    $imin += $interval->i;
  }

  if (isset($prod_batch_response['time_duration'])) {
    $minutes = $prod_batch_response['time_duration']->i;
    $seconds = $prod_batch_response['time_duration']->s;
    if ($minutes > 0) {
      $total_seconds = $minutes*60;
    }
    if ($seconds > 0) {
      $total_seconds = $total_seconds+$seconds;
    }
  }
  if (isset($ee_additional_data['product_sync_batch_size'])) {
    if ($total_que_products > 0) {
      $total_batch_size = $ee_additional_data['product_sync_batch_size'];
      $total_pending_pro = $total_que_products - $total_sync_products;
      $total_batches = ($total_pending_pro/$total_batch_size);
    } 
    if ($total_pending_pro == 0) {
      // add scheduled cron job  
      as_unschedule_all_actions( 'auto_product_sync_process_scheduler' );
    }
  }
  if ($total_batches > 0 && $total_seconds > 0) {
    if ($total_seconds > 60 ) {
      $sec = $total_seconds %60;
      $min = floor(($total_seconds%3600)/60);
    } else {
      //$sec = $total_seconds;
    }

    $total_require_secs = ($total_batches*($total_seconds));
    if ($total_require_secs > 60 ) {
      $rsec = $total_require_secs %60;
      $rmin = floor(($total_require_secs%3600)/60);
      $rhr = floor(($total_require_secs%86400)/3600);
    }
  }
  //$message = "Your WooCommerce products are being synced (".$total_sync_products."/".$total_que_products."). It is taking ".$min." Minutes and ".$sec." Seconds to sync [".$total_batch_size."] products. The remaining ".$total_pending_pro." products will be synced in ".$rhr." Hrs ".$rmin." Minutes and ".$rsec." Seconds.";
  $message = "Your WooCommerce products are being synced (".$total_sync_products."/".$total_que_products."). It is taking ".$min." Minutes to sync [".$total_batch_size."] products. The remaining ".$total_pending_pro." products will be synced in ".$rhr." Hrs ".$rmin." Minutes";
?>

<div class="con-tab-content">
	<div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-6 col-lg-8 edit-section">
          <div class="configuration-section" id="config-pt1">
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
          <?php echo get_google_shopping_tabs_html(esc_attr($this->site_url),(isset($googleDetail->google_merchant_center_id))?esc_attr($googleDetail->google_merchant_center_id):""); ?>                          
          </div>
          <div class="mt-3" id="config-pt2">
            <div class="sync-new-product" id="sync-product">
              <div class="row">
                <div class="col-12">
                  <div class="d-flex justify-content-between ">
                    <p class="mb-0 align-self-center product-title"><?php esc_html_e("Products in your Merchant Center account","enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <button id="tvc_btn_product_sync" class="btn btn-outline-primary align-self-center" data-bs-toggle="modal" data-bs-target="#syncProduct"><?php esc_html_e("Sync New Products","enhanced-e-commerce-for-woocommerce-store"); ?></button>
                  </div>
                  <?php
                    if (!empty($ee_additional_data) && isset($ee_additional_data['product_sync_alert']) && !empty($ee_additional_data['product_sync_alert'])) {
                      echo "<p style='color:#2D62ED'>".esc_attr($ee_additional_data['product_sync_alert'])."</p>";
                    }
                    if (!empty($ee_additional_data) && isset($ee_additional_data['is_process_start']) && ($ee_additional_data['is_process_start'] == true) && $total_seconds > 0) {
                      ?>
                      <p style='color:#2D62ED'><?php echo esc_attr($message,"enhanced-e-commerce-for-woocommerce-store"); ?></p>
                      <?php
                      if ($imin > 30) {
                        echo "<p style='color:red'>It seems like product sync failed, please try again <button id='tvc_btn_retry_sync' class='btn btn-outline-primary align-self-center'>Retry</button></p>";
                      }
                    }
                    ?>
                </div>
            	</div>
              <?php
              $sync_product_total = (property_exists($syncProductStat,"total")) ? $syncProductStat->total : "0";
              $sync_product_approved = (property_exists($syncProductStat,"approved")) ? $syncProductStat->approved : "0";
              $sync_product_disapproved = (property_exists($syncProductStat,"disapproved")) ? $syncProductStat->disapproved : "0";
              $sync_product_pending = (property_exists($syncProductStat,"pending")) ? $syncProductStat->pending : "0"; ?>
              <div class="product-card">
                <div class="row row-cols-5">
                  <div class="col">
                    <div class="card">
                      <h3 class="pro-count"><?php 
                      echo ($woo_product) ? esc_attr($woo_product) : "0"; ?></h3>
                      <p class="pro-title"><?php esc_html_e("Total Products","enhanced-e-commerce-for-woocommerce-store"); ?></p>                      
                    </div>
                  </div>
                  <div class="col">
                    <div class="card">
                      <h3 class="pro-count"><?php 
                      echo esc_attr($sync_product_total) ; ?></h3>
                      <p class="pro-title"><?php esc_html_e("Sync Products","enhanced-e-commerce-for-woocommerce-store"); ?></p>                      
                    </div>
                  </div>
                  <div class="col">
                    <div class="card pending">
                      <h3 class="pro-count">
                      <?php echo esc_attr($sync_product_pending);?></h3>
                      <p class="pro-title"><?php esc_html_e("Pending Review","enhanced-e-commerce-for-woocommerce-store"); ?></p>                        
                    </div>
                  </div>
                  <div class="col">
                    <div class="card approved">
                      <h3 class="pro-count"><?php echo esc_attr($sync_product_approved);?></h3>
                      <p class="pro-title"><?php esc_html_e("Approved","enhanced-e-commerce-for-woocommerce-store"); ?></p>                        
                    </div>
                  </div>
                  <div class="col">
                    <div class="card disapproved">
                      <h3 class="pro-count"><?php
                      echo esc_attr($sync_product_disapproved); ?></h3>
                      <p class="pro-title"><?php esc_html_e("Disapproved","enhanced-e-commerce-for-woocommerce-store"); ?></p>                        
                    </div>
                  </div>
                </div>
          		</div>
              <div class="total-products">                
                <div class="account-performance tvc-sync-product-list-wapper">
                  <div class="table-section">
                    <div class="table-responsive">
                      <table id="tvc-sync-product-list" class="table table-striped" style="width:100%">
                      	<thead>
                        	<tr>
                          	<th></th>
                          	<th style="vertical-align: top;"><?php esc_html_e("Product","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                          	<th style="vertical-align: top;"><?php esc_html_e("Google status","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                          	<th style="vertical-align: top;"><?php esc_html_e("Issues","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                        	</tr>
                      	</thead>
                      	<tbody>
                      	<?php
	                      if (isset($syncProductList) && count($syncProductList) > 0) {
                          foreach ($syncProductList as $skey => $sValue) { ?>
                            <tr><td class="product-image">
	                            <img src="<?php echo esc_url_raw($sValue->image_link); ?>" alt=""/></td>
	                            <td><?php echo esc_attr($sValue->name); ?></td>
	                            <td><?php echo esc_attr($sValue->google_status); ?></td>
	                            <td>
                              <?php 
                              $p_issues = json_decode($sValue->issues);
	                            if (count($p_issues) > 0) {
                                $str = '';
                                foreach ($p_issues as $key => $issue) {
                                  if ($key <= 2) {
                                    ($key <= 1) ? $str .= html_entity_decode(esc_html($issue)).", " : "";
                                  }
                                    ($key == 3) ? $str .= "..." : "";      			
                                 }
                                 echo esc_attr($str);
                              } else {
	                              echo "---";
	                            }?>
	                            </td></tr>
                            <?php
                          }	
                        }else{ ?>
                          <tr><td colspan="4"><?php echo esc_html__("Record not found","enhanced-e-commerce-for-woocommerce-store"); ?></td></tr>
                        <?php
                        } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
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
$is_need_to_domain_claim = false;
if(isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id && $this->subscriptionId != "" && isset($googleDetail->is_domain_claim) && $googleDetail->is_domain_claim == '0'){
  $is_need_to_domain_claim = true;
}?>
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery(document).on("click", "#tvc_btn_product_sync", function(event){
      var el = $(this).text();
      var slug = el.replace(/\s+/g, '_').toLowerCase();
      user_tracking_data('click', 'null','sync_product_page',slug);
  });
  //data table js
  jQuery('#tvc-sync-product-list').DataTable({
    "ordering": false,
    "scrollY": "600px",
    "lengthMenu": [ 10, 20, 50, 100, 200 ]
  });
  //auto syncup call
  /*var is_need_to_update = "<?php echo esc_attr($is_need_to_update); ?>";  
  if(is_need_to_update == 1 || is_need_to_update == true){
    call_tvc_api_sync_up();
  }*/
});
//Update syncup detail by ajax call
function call_tvc_api_sync_up(){
  var tvs_this = jQuery("#refresh_api");
  jQuery("#tvc_msg").remove();
  jQuery("#refresh_api").css("visibility","hidden");
  jQuery(tvs_this).after('<div class="tvc-nb-spinner" id="tvc-nb-spinner"></div>');
  tvc_helper.tvc_alert("error","<?php esc_html_e("Attention !","enhanced-e-commerce-for-woocommerce-store"); ?>", "<?php esc_html_e("Sync up is in the process do not refresh the page. it may take few minutes, if GMC product sync count is large.","enhanced-e-commerce-for-woocommerce-store"); ?>");
  ImportGMCProduct();
}
var total_import = 0;
function ImportGMCProduct(next_page_token = null){
  jQuery.post(tvc_ajax_url,{
    action: "tvc_call_import_gmc_product", 
    next_page_token:next_page_token,
    apiSyncupNonce : "<?php echo wp_create_nonce('tvc_call_api_sync-nonce'); ?>"
  },function( response ){
    var rsp = JSON.parse(response);    
    if(rsp.error == false && rsp.api_rs != null && rsp.api_rs.next_page_token != "" ){
      total_import = total_import+rsp.api_rs.sync_product;
      if(rsp.api_rs.next_page_token != null){
        jQuery("#products_count").html("- "+total_import);
        ImportGMCProduct(rsp.api_rs.next_page_token);
      }else{
        jQuery("#tvc-nb-spinner").remove();
        tvc_helper.tvc_alert("success","",rsp.message,true,3000);
        setTimeout(function(){ location.reload();}, 3000); 
      }
    }else{
      tvc_helper.tvc_alert("error","",rsp.message,true,3000);
      setTimeout(function(){ location.reload();}, 2000); 
    }
    
  });
}

jQuery(document).on("click", "#tvc_btn_retry_sync", function(event){
  reTrySycnProcess();
});
function reTrySycnProcess(){
  jQuery("#feed-spinner").css("display", "block");
  setTimeout(function(){ location.reload();}, 15000); 
  jQuery.post(tvc_ajax_url,{
    action: "auto_product_sync_process_scheduler",
    dataType: "json",
  },function( response ){
    var rsp = JSON.parse(response);
    jQuery("#feed-spinner").css("display", "none");
    tvc_helper.tvc_alert("success","",rsp.message);
    window.location.replace("<?php echo esc_url_raw($this->site_url.'sync_product_page'); ?>");
  });
}
</script>
		<?php
  }
}
?>