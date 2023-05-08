<?php

/**
* TVC Ajax File Class.
*
* @package TVC Product Feed Manager/Data/Classes
*/
if(!defined('ABSPATH')){
exit;
}

if(!class_exists('TVC_Ajax_File')) :
/**
 * Ajax File Class
 */
class TVC_Ajax_File extends TVC_Ajax_Calls {
  private $apiDomain;
  protected $access_token;
  protected $refresh_token;
  public function __construct(){
    parent::__construct();
    $this->apiDomain = TVC_API_CALL_URL;
    // hooks
    add_action('wp_ajax_tvcajax-get-campaign-categories', array($this, 'tvcajax_get_campaign_categories'));
    add_action('wp_ajax_tvcajax-update-campaign-status', array($this, 'tvcajax_update_campaign_status'));
    add_action('wp_ajax_tvcajax-delete-campaign', array($this, 'tvcajax_delete_campaign'));
    
    add_action('wp_ajax_tvcajax-gmc-category-lists', array($this, 'tvcajax_get_gmc_categories'));
    //add_action('wp_ajax_tvcajax-custom-metrics-dimension', array($this, 'tvcajax_custom_metrics_dimension'));
    add_action('wp_ajax_tvcajax-store-time-taken', array($this, 'tvcajax_store_time_taken'));

    add_action('wp_ajax_tvc_call_api_sync', array($this, 'tvc_call_api_sync'));
    add_action('wp_ajax_tvc_call_import_gmc_product', array($this, 'tvc_call_import_gmc_product'));
    add_action('wp_ajax_tvc_call_domain_claim', array($this, 'tvc_call_domain_claim'));
    add_action('wp_ajax_tvc_call_site_verified', array($this, 'tvc_call_site_verified'));
    add_action('wp_ajax_tvc_call_notice_dismiss', array($this, 'tvc_call_notice_dismiss'));
    add_action('wp_ajax_tvc_call_notice_dismiss_trigger', array($this, 'tvc_call_notice_dismiss_trigger'));
    add_action('wp_ajax_tvc_call_notification_dismiss', array($this, 'tvc_call_notification_dismiss'));
	  add_action('wp_ajax_auto_product_sync_setting', array($this, 'auto_product_sync_setting'));
    add_action('wp_ajax_con_get_conversion_list', array($this, 'con_get_conversion_list'));
    add_action('wp_ajax_tvc_call_active_licence', array($this, 'tvc_call_active_licence'));
    add_action('wp_ajax_tvc_call_add_survey', array($this, 'tvc_call_add_survey'));
    add_action('wp_ajax_cov_save_badge_settings', array($this, 'cov_save_badge_settings'));

    add_action('wp_ajax_tvc_call_add_customer_feedback', array($this, 'tvc_call_add_customer_feedback'));

    // Not in use after product sync from backend
    //add_action('wp_ajax_tvcajax_product_sync_bantch_wise', array($this, 'tvcajax_product_sync_bantch_wise_old'));
    add_action('wp_ajax_tvcajax_product_sync_bantch_wise', array($this, 'tvcajax_product_sync_bantch_wise'));
    add_action('wp_ajax_update_user_tracking_data', array($this,'update_user_tracking_data') );
    add_action('init_product_sync_process_scheduler', array($this,'tvc_call_start_product_sync_process'), 10, 1 );
    /*add_action('auto_product_sync_process_scheduler', array($this,'tvc_call_auto_product_sync_process') );
    add_action('wp_ajax_auto_product_sync_process_scheduler', array($this,'tvc_call_auto_product_sync_process') );*/
    add_action('wp_ajax_auto_product_sync_process_scheduler', array($this,'tvc_call_start_product_sync_process') );
  }
    public function cov_save_badge_settings(){
      $val = isset($_POST['bagdeVal']) ? sanitize_text_field($_POST['bagdeVal']) : "no";
      $data = array();
      $data = unserialize(get_option('ee_options'));
      $data['conv_show_badge']=sanitize_text_field($val);
      if($val =="yes"){
        $data['conv_badge_position']=sanitize_text_field("center");  
      }else{
        $data['conv_badge_position']="";
      }
      update_option('ee_options',serialize($data));
      exit;
    }

    public function update_user_tracking_data(){
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce'), 'update_user_tracking_data-nonce')) {
        $event_name = isset($_POST['event_name']) ? sanitize_text_field($_POST['event_name']) : "";
        $screen_name = isset($_POST['screen_name']) ? sanitize_text_field($_POST['screen_name']) : "";
        $error_msg = isset($_POST['error_msg']) ? sanitize_text_field($_POST['error_msg']) : "";
        $event_label = isset($_POST['event_label']) ? sanitize_text_field($_POST['event_label']) : "";
        // $timestamp = isset($_POST['timestamp'])?sanitize_text_field($_POST['timestamp']):"";
        $timestamp = date("YmdHis");
        $t_data = array(
          'event_name' => esc_sql($event_name),
          'screen_name' => esc_sql($screen_name),
          'timestamp' => esc_sql($timestamp),
          'error_msg' => esc_sql($error_msg),
          'event_label' => esc_sql($event_label),
        );
        if (!empty($t_data)) {

          $options_val = get_option('ee_ut');
          if (!empty($options_val)) {
            $odata = (array) maybe_unserialize($options_val);
            array_push($odata, $t_data);
            update_option("ee_ut", serialize($odata));
          } else {
            $t_d[] = $t_data;
            update_option("ee_ut", serialize($t_d));
          }
        }
        wp_die();
      }else{
        echo json_encode(array("error"=>true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
      }
      // IMPORTANT: don't forget to exit
      exit;
  }

  // Not in use after product sync from backend
  public function tvcajax_product_sync_bantch_wise_old(){
    global $wpdb;
    $rs = array();
    // barch size for inser data in DB
    $product_db_batch_size = 100;
    // barch size for inser product in GMC
    //$product_batch_size = 25;
    $product_batch_size = isset($_POST['product_batch_size'])?sanitize_text_field($_POST['product_batch_size']):"25";
    if(!class_exists('CustomApi')){
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
    }
    if(!class_exists('TVCProductSyncHelper')){
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
    }
    $customObj = new CustomApi();
    $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    $TVCProductSyncHelper = new TVCProductSyncHelper();
    //sleep(3);
    $prouct_pre_sync_table = esc_sql( $wpdb->prefix ."ee_prouct_pre_sync_data" );
    
    $sync_produt = ""; $sync_produt_p = ""; $is_synced_up = ""; $sync_message = "";
    $sync_progressive_data = isset($_POST['sync_progressive_data'])?$_POST['sync_progressive_data']:"";

    $sync_produt = isset($sync_progressive_data['sync_produt'])?sanitize_text_field($sync_progressive_data['sync_produt']):"";
    $sync_produt = sanitize_text_field($sync_produt);

    $sync_step = isset($sync_progressive_data['sync_step'])?sanitize_text_field($sync_progressive_data['sync_step']):"1";
    $sync_step = sanitize_text_field($sync_step);

    $total_product =isset($sync_progressive_data['total_product'])?sanitize_text_field($sync_progressive_data['total_product']):"0";
    $total_product = sanitize_text_field($total_product);

    $last_sync_product_id =isset($sync_progressive_data['last_sync_product_id'])?$sync_progressive_data['last_sync_product_id']:"";
    $last_sync_product_id = sanitize_text_field( intval( $last_sync_product_id ) );

    $skip_products =isset($sync_progressive_data['skip_products'])?sanitize_text_field($sync_progressive_data['skip_products']):"0";
    $skip_products = sanitize_text_field($skip_products);

    $account_id = isset($_POST['account_id'])?sanitize_text_field($_POST['account_id']):"";
    $customer_id = isset($_POST['customer_id'])?sanitize_text_field($_POST['customer_id']):"";
    $subscription_id = isset($_POST['subscription_id'])?sanitize_text_field($_POST['subscription_id']):"";
    $data = isset($_POST['tvc_data'])?$_POST['tvc_data']:"";
    parse_str($data, $formArray);    
    if(!empty($formArray)){
      foreach ($formArray as $key => $value) {
        $formArray[$key] = sanitize_text_field($value);
      }
    }
    if( $sync_progressive_data == "" && $TVC_Admin_DB_Helper->tvc_row_count("ee_prouct_pre_sync_data") > 0 ){
      $TVC_Admin_DB_Helper->tvc_safe_truncate_table($prouct_pre_sync_table);
    }
    /*
     * step one start
     */
    if($total_product <= $sync_produt && $sync_step == 1){
      $sync_step = 2;
      $sync_produt = 0;
    }
    if($sync_step == 1){
      //parse_str($data, $formArray);      
      $mappedCatsDB = [];
      $mappedCats = [];
      $mappedAttrs = [];
      $skipProducts = [];
      foreach($formArray as $key => $value){
        if(preg_match("/^category-name-/i", $key)){
          if($value != ''){
            $keyArray = explode("name-", $key);
            $mappedCatsDB[$keyArray[1]]['name'] = $value;
          }
          unset($formArray[$key]);
        }else if(preg_match("/^category-/i", $key)){
          if($value != '' && $value > 0){
            $keyArray = explode("-", $key);
            $mappedCats[$keyArray[1]] = $value;
            $mappedCatsDB[$keyArray[1]]['id'] = $value;
          }
          unset($formArray[$key]);
        }else{
          if($value){
              $mappedAttrs[$key] = $value;
          }
        }
      }
      //add/update data in defoult profile
      $profile_data = array("profile_title"=>esc_sql("Default"),"g_attribute_mapping"=>json_encode($mappedAttrs),"update_date"=>date('Y-m-d'));
      if($TVC_Admin_DB_Helper->tvc_row_count("ee_product_sync_profile") ==0){
        $TVC_Admin_DB_Helper->tvc_add_row("ee_product_sync_profile", $profile_data, array("%s", "%s","%s"));
      }else{
        $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_profile", $profile_data, array("id" => 1));
      }
      update_option("ee_prod_mapped_cats", serialize($mappedCatsDB));
      update_option("ee_prod_mapped_attrs", serialize($mappedAttrs)); 

      /*
       * start product add in DB
       * start clategory list
       */
      if(!empty($mappedCats)){
        $batch_count =0; 
        $values = array();
        $place_holders = array();
        foreach($mappedCats as $mc_key => $mappedCat){
          $all_products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
              array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $mc_key, /* category name */
                'operator' => 'IN',
                'include_children' => false
              )
            )
          ));
          /*
           * start product list , it's run per category
           */
          if(!empty($all_products)){
            foreach($all_products as $postkey => $postvalue){
              $batch_count++;        
              array_push( $values, esc_sql($postvalue->ID), esc_sql($mc_key), esc_sql($mappedCat), 1, date('Y-m-d') );
              $place_holders[] = "('%d', '%d', '%d','%d', '%s')";
              if($batch_count >= $product_db_batch_size){
                $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, update_date) VALUES ";
                $query .= implode( ', ', $place_holders );
                $wpdb->query($wpdb->prepare( $query, $values ));
                $batch_count = 0;
                $values = array();
                $place_holders = array();
              }
            } //end product list loop
          }// end product loop if
        }//end clategory loop
        /*
         * add last batch data in DB
         */
        if($batch_count > 0){
          $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, update_date) VALUES ";
          $query .= implode( ', ', $place_holders );
          $wpdb->query($wpdb->prepare( $query, $values ));          
        }

      }//end category if
      $total_product = $TVC_Admin_DB_Helper->tvc_row_count("ee_prouct_pre_sync_data");
      $sync_produt = $total_product;
      $sync_produt_p = ($sync_produt*100)/$total_product; 
      $is_synced_up = ($total_product <= $sync_produt)?true:false;
      $sync_message = esc_html__("Initiated, products are being synced to Merchant Center.Do not refresh..","enhanced-e-commerce-for-woocommerce-store");
      //step one end
    }else if($sync_step == 2){      
      $rs = $TVCProductSyncHelper->call_batch_wise_sync_product($last_sync_product_id, $product_batch_size);
      if(isset($rs['products_sync'])){
        $sync_produt = (int)$sync_produt + $rs['products_sync'];
      }else{
        echo json_encode(array('status'=>'false', 'message'=> $rs['message'], "api_rs"=>$rs));
        exit;
      }
      $skip_products=(isset($rs['skip_products']))?$rs['skip_products']:0;
      $last_sync_product_id = (isset($rs['last_sync_product_id']))?$rs['last_sync_product_id']:0;
      $sync_produt_p = ($sync_produt*100)/$total_product;
      $is_synced_up = ($total_product <= $sync_produt)?true:false;
      $sync_message = esc_html__("Initiated, products are being synced to Merchant Center.Do not refresh..","enhanced-e-commerce-for-woocommerce-store");
      if($total_product <= $sync_produt){
        //$customObj->setGmcCategoryMapping($catMapRequest);
        //$customObj->setGmcAttributeMapping($attrMapRequest);        
        $sync_message = esc_html__("Initiated, products are being synced to Merchant Center.Do not refresh..","enhanced-e-commerce-for-woocommerce-store");
        $TVC_Admin_DB_Helper->tvc_safe_truncate_table($prouct_pre_sync_table);
      }
    }
    $sync_produt_p = round($sync_produt_p,0);
    $sync_progressive_data = array("sync_step"=>$sync_step, "total_product"=>$total_product, "sync_produt"=>$sync_produt, "sync_produt_p"=>$sync_produt_p, 'skip_products'=>$skip_products, "last_sync_product_id"=>$last_sync_product_id, "is_synced_up"=>$is_synced_up, "sync_message"=>$sync_message);
    echo json_encode(array('status'=>'success', "sync_progressive_data" => $sync_progressive_data, "api_rs"=>$rs));
    exit;
  }

  public function tvcajax_product_sync_bantch_wise(){
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce'), 'tvcajax_product_sync_bantch_wise-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        try {
          $mappedCats = [];
          $mappedAttrs = [];
          $mappedCatsDB = [];
          $product_batch_size = isset($_POST['product_batch_size']) ? sanitize_text_field($_POST['product_batch_size']) : "25"; // barch size for inser product in GMC
          $data = isset($_POST['tvc_data']) ? $_POST['tvc_data'] : "";

          $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
          parse_str($data, $formArray);
          if (!empty($formArray)) {
            foreach ($formArray as $key => $value) {
              $formArray[$key] = sanitize_text_field($value);
            }
          }

          /*
           * Collect Attribute/Categories Mapping
           */
          foreach ($formArray as $key => $value) {
            if (preg_match("/^category-name-/i", $key)) {
              if ($value != '') {
                $keyArray = explode("name-", $key);
                $mappedCatsDB[$keyArray[1]]['name'] = $value;
              }
              unset($formArray[$key]);
            } else if (preg_match("/^category-/i", $key)) {
              if ($value != '' && $value > 0) {
                $keyArray = explode("-", $key);
                $mappedCats[$keyArray[1]] = $value;
                $mappedCatsDB[$keyArray[1]]['id'] = $value;
              }
              unset($formArray[$key]);
            } else {
              if ($value) {
                $mappedAttrs[$key] = $value;
              }
            }
          }

          //add/update data in default profile
          $profile_data = array("profile_title" => esc_sql("Default"), "g_attribute_mapping" => json_encode($mappedAttrs), "update_date" => date('Y-m-d'));
          if ($TVC_Admin_DB_Helper->tvc_row_count("ee_product_sync_profile") == 0) {
            $TVC_Admin_DB_Helper->tvc_add_row("ee_product_sync_profile", $profile_data, array("%s", "%s", "%s"));
          } else {
            $TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_profile", $profile_data, array("id" => 1));
          }
          // Update settings
          update_option("ee_prod_mapped_cats", serialize($mappedCatsDB));
          update_option("ee_prod_mapped_attrs", serialize($mappedAttrs));

          // Batch settings
          $ee_additional_data['is_mapping_update'] = true;
          $ee_additional_data['is_process_start'] = false;
          $ee_additional_data['is_auto_sync_start'] = false;
          $ee_additional_data['product_sync_batch_size'] = $product_batch_size;
          $ee_additional_data['product_sync_alert'] = "Product sync settings updated successfully process will start soon...";
          $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);

          // add scheduled cron job 
          //wp_schedule_single_event(time()+1, 'init_product_sync_process_scheduler', array(time()));
          /*as_unschedule_all_actions( 'auto_product_sync_process_scheduler' );*/ // Changes done by chirag 09012023
          as_unschedule_all_actions( 'init_product_sync_process_scheduler' );
          as_enqueue_async_action('init_product_sync_process_scheduler');

          /*wp_clear_scheduled_hook( 'init_product_sync_process_scheduler' );
          $TVC_Admin_Helper->plugin_log("step 1", 'product_sync');// Add logs
          if ( ! wp_next_scheduled( 'init_product_sync_process_scheduler' ) ) {
            $TVC_Admin_Helper->plugin_log("step 2", 'product_sync');// Add logs
              wp_schedule_single_event( time()+2 , 'init_product_sync_process_scheduler' );
              $TVC_Admin_Helper->plugin_log("step 3", 'product_sync');// Add logs
          }
          $TVC_Admin_Helper->plugin_log("step 4", 'product_sync');// Add logs*/

          $TVC_Admin_Helper->plugin_log("mapping saved and product sync process scheduled", 'product_sync');// Add logs

          $sync_message = esc_html__("Initiated, products are being synced to Merchant Center.Do not refresh..", "enhanced-e-commerce-for-woocommerce-store");
          $sync_progressive_data = array("sync_message" => $sync_message);
          echo json_encode(array('status' => 'success', "sync_progressive_data" => $sync_progressive_data));
        } catch (Exception $e) {
          $ee_additional_data['product_sync_alert'] = $e->getMessage();
          $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
          $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
        }
        wp_die();
      }else{
        echo json_encode(array("error"=>true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
      }
      // IMPORTANT: don't forget to exit
      exit;
  }

  // function tvc_call_start_product_sync_process_old(){
  //   $TVC_Admin_Helper = new TVC_Admin_Helper();
  //   $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
  //   try {
  //     global $wpdb;
  //     $product_db_batch_size = 200; // batch size to insert in database
  //     $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
  //     $prouct_pre_sync_table = esc_sql( $wpdb->prefix ."ee_prouct_pre_sync_data" );
  //     $mappedCats = unserialize(get_option('ee_prod_mapped_cats'));
  //     if (!empty($ee_additional_data) && isset($ee_additional_data['is_mapping_update']) && $ee_additional_data['is_mapping_update'] == true) {
  //       // Add products in product pre sync table
  //       if(!empty($mappedCats)){
  //         // truncate data from product pre sync table
  //         if( $TVC_Admin_DB_Helper->tvc_row_count("ee_prouct_pre_sync_data") > 0 ){
  //           $TVC_Admin_DB_Helper->tvc_safe_truncate_table($prouct_pre_sync_table);
  //         }

  //         $batch_count =0; 
  //         $values = array();
  //         $place_holders = array();
  //         foreach($mappedCats as $mc_key => $mappedCat){
  //           $term = get_term_by( 'term_id', $mc_key,'product_cat' , 'ARRAY_A' );
  //           //$TVC_Admin_Helper->plugin_log(" = = = =category id ".json_encode($term), 'product_sync');
  //           //die;
  //           $total_page = 1;
  //           if(isset($term["count"]) && $term["count"] > 1000){
  //             $total_page = ceil($term["count"]/1000);
  //           }
            
  //           for($i = 1; $i<= $total_page; $i++){
  //             $TVC_Admin_Helper->plugin_log("category > ".$mappedCat['name']." > total_page ".json_encode($total_page)." page > ". $i, 'product_sync');
  //             $all_products = get_posts(array(
  //               'post_type' => 'product',
  //               'posts_per_page' => 1000,
  //               'paged' => $i,
  //               'numberposts' => -1,
  //               'post_status' => 'publish',
  //               'fields' => 'ids',
  //               'tax_query' => array(
  //                 array(
  //                   'taxonomy' => 'product_cat',
  //                   'field' => 'term_id',
  //                   'terms' => $mc_key, /* category name */
  //                   'operator' => 'IN',
  //                   'include_children' => false
  //                 )
  //               )
  //             ));
  //             $TVC_Admin_Helper->plugin_log("category id ".$mc_key." gmc product name ".$mappedCat['name']." - product count - ".count($all_products), 'product_sync'); // Add logs
  //             if(!empty($all_products)){
  //               foreach($all_products as $postvalue){
  //                 $batch_count++;        
  //                 array_push( $values, esc_sql($postvalue), esc_sql($mc_key), esc_sql($mappedCat['id']), 1, date( 'Y-m-d H:i:s', current_time( 'timestamp') ) );
  //                 $place_holders[] = "('%d', '%d', '%d','%d', '%s')";
  //                 if($batch_count >= $product_db_batch_size){
  //                   $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date) VALUES ";
  //                   $query .= implode( ', ', $place_holders );
  //                   $wpdb->query($wpdb->prepare( $query, $values ));
  //                   $batch_count = 0;
  //                   $values = array();
  //                   $place_holders = array();
  //                 }
  //               } //end product list loop
  //             }// end products if
  //           }// Pagination loop
  //         }//end category loop

  //         // Add products in database
  //         if($batch_count > 0){
  //           $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date) VALUES ";
  //           $query .= implode( ', ', $place_holders );
  //           $wpdb->query($wpdb->prepare( $query, $values ));          
  //         }

  //         // add scheduled cron job 
  //         if ( false === as_next_scheduled_action( 'tvc_add_cron_interval_for_product_sync' ) ) {
  //           as_schedule_single_action( time()+5, 'auto_product_sync_process_scheduler' );
  //         }
  //       }

  //       $ee_additional_data['is_mapping_update'] = false;
  //       $ee_additional_data['is_process_start'] = true;
  //       $ee_additional_data['product_sync_alert'] = "Product sync process is ready to start";
  //       $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
  //     }
  //   } catch (Exception $e) {
  //     $ee_additional_data['product_sync_alert'] = $e->getMessage();
  //     $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
  //     $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
  //   }
  //   return true;
  // }

  // function tvc_call_auto_product_sync_process_old(){
  //   $TVC_Admin_Helper = new TVC_Admin_Helper();
  //   $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
  //   $ee_additional_data['product_sync_alert'] = NULL;
  //   $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
  //   try {
  //     // add scheduled cron job 
  //     as_unschedule_all_actions( 'auto_product_sync_process_scheduler' );
  //     $TVC_Admin_Helper->plugin_log("auto product sync process start", 'product_sync');
  //     global $wpdb;
  //     if (!empty($ee_additional_data) && isset($ee_additional_data['is_process_start']) && $ee_additional_data['is_process_start'] == true) {
  //       if(!class_exists('TVCProductSyncHelper')){
  //         include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
  //       }
  //       $TVCProductSyncHelper = new TVCProductSyncHelper();
  //       $response = $TVCProductSyncHelper->call_batch_wise_auto_sync_product();
  //       if (!empty($response) && isset($response['message'])) {
  //         $TVC_Admin_Helper->plugin_log("Batch wise auto sync process response ".$response['message'], 'product_sync');
  //       }

  //       $tablename = esc_sql( $wpdb->prefix ."ee_prouct_pre_sync_data" );
  //       $total_pending_pro = $wpdb->get_var("SELECT COUNT(*) as a FROM $tablename where `status` = 0");
  //       if($total_pending_pro == 0){
  //         // Truncate pre sync table
  //         $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
  //         $TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
          
  //         $ee_additional_data['is_process_start'] = false;
  //         $ee_additional_data['is_auto_sync_start'] = true;
  //         $ee_additional_data['product_sync_alert'] = NULL;
  //         $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
  //         $TVC_Admin_Helper->plugin_log("product sync process done", 'product_sync');
  //       } else {
  //         // add scheduled cron job 
  //         if ( false === as_next_scheduled_action( 'tvc_add_cron_interval_for_product_sync' ) ) {
  //           // as_schedule_cron_action( time(), '0/3 * * * *', 'auto_product_sync_process_scheduler' );
  //           as_schedule_single_action( time()+5, 'auto_product_sync_process_scheduler' );
  //         }
  //         $TVC_Admin_Helper->plugin_log("recall product sync process", 'product_sync');
  //         // $this->tvc_call_auto_product_sync_process();
  //       }
  //     } else {
  //       // add scheduled cron job
  //       as_unschedule_all_actions( 'auto_product_sync_process_scheduler' );
  //     }
  //     echo json_encode(array('status'=>'success', "message" => esc_html__("Product sync process started successfully")));
  //     return true;
  //   } catch (Exception $e) {
  //     $ee_additional_data['product_sync_alert'] = $e->getMessage();
  //     $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
  //     $TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
  //     return true;
  //   }
  // }

  function tvc_call_start_product_sync_process(){
    $TVC_Admin_Helper = new TVC_Admin_Helper();
    try {
      $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
      as_unschedule_all_actions( 'init_product_sync_process_scheduler' );
      as_schedule_single_action( time(), 'init_product_sync_process_scheduler' );
      global $wpdb;
      if (!empty($ee_additional_data) && isset($ee_additional_data['is_mapping_update']) && $ee_additional_data['is_mapping_update'] == true) {
        $product_db_batch_size = 200; // batch size to insert in database
        $prouct_pre_sync_table = esc_sql( $wpdb->prefix ."ee_prouct_pre_sync_data" );
        $mappedCats = unserialize(get_option('ee_prod_mapped_cats'));
        // Add products in product pre sync table
        if(!empty($mappedCats)){
          // truncate data from product pre sync table
          if( $TVC_Admin_DB_Helper->tvc_row_count("ee_prouct_pre_sync_data") > 0 ){
            $TVC_Admin_DB_Helper->tvc_safe_truncate_table($prouct_pre_sync_table);
          }

          $batch_count =0; 
          $values = array();
          $place_holders = array();
          foreach($mappedCats as $mc_key => $mappedCat){
            $term = get_term_by( 'term_id', $mc_key,'product_cat' , 'ARRAY_A' );
            //$TVC_Admin_Helper->plugin_log(" = = = =category id ".json_encode($term), 'product_sync');
            //die;
            $total_page = 1;
            if(isset($term["count"]) && $term["count"] > 1000){
              $total_page = ceil($term["count"]/1000);
            }
            
            for($i = 1; $i<= $total_page; $i++){
              $TVC_Admin_Helper->plugin_log("Manual - category > ".$mappedCat['name']." > total_page ".json_encode($total_page)." page > ". $i, 'product_sync');
              $all_products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => 1000,
                'paged' => $i,
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
                'tax_query' => array(
                  array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $mc_key, /* category name */
                    'operator' => 'IN',
                    'include_children' => false
                  )
                )
              ));
              $TVC_Admin_Helper->plugin_log("Manual - category id ".$mc_key." gmc product name ".$mappedCat['name']." - product count - ".count($all_products), 'product_sync'); // Add logs
              if(!empty($all_products)){
                foreach($all_products as $postvalue){
                  $batch_count++;        
                  array_push( $values, esc_sql($postvalue), esc_sql($mc_key), esc_sql($mappedCat['id']), 1, date( 'Y-m-d H:i:s', current_time( 'timestamp') ) );
                  $place_holders[] = "('%d', '%d', '%d','%d', '%s')";
                  if($batch_count >= $product_db_batch_size){
                    $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date) VALUES ";
                    $query .= implode( ', ', $place_holders );
                    $wpdb->query($wpdb->prepare( $query, $values ));
                    $batch_count = 0;
                    $values = array();
                    $place_holders = array();
                  }
                } //end product list loop
              }// end products if
            }// Pagination loop
          }//end category loop

          // Add products in database
          if($batch_count > 0){
            $query = "INSERT INTO `$prouct_pre_sync_table` (w_product_id, w_cat_id, g_cat_id, product_sync_profile_id, create_date) VALUES ";
            $query .= implode( ', ', $place_holders );
            $wpdb->query($wpdb->prepare( $query, $values ));          
          }
        }

        $ee_additional_data['is_mapping_update'] = false;
        $ee_additional_data['is_process_start'] = true;
        $ee_additional_data['product_sync_alert'] = "Product sync process is ready to start";
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
      } 

      $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
      if (!empty($ee_additional_data) && isset($ee_additional_data['is_process_start']) && $ee_additional_data['is_process_start'] == true) {
        $TVC_Admin_Helper->plugin_log("Manual - product sync process start", 'product_sync');
        if(!class_exists('TVCProductSyncHelper')){
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php');
        }
        $TVCProductSyncHelper = new TVCProductSyncHelper();
        $response = $TVCProductSyncHelper->call_batch_wise_auto_sync_product();
        if (!empty($response) && isset($response['message'])) {
          $TVC_Admin_Helper->plugin_log("Manual - Batch wise auto sync process response ".$response['message'], 'product_sync');
        }

        $tablename = esc_sql( $wpdb->prefix ."ee_prouct_pre_sync_data" );
        $total_pending_pro = $wpdb->get_var("SELECT COUNT(*) as a FROM $tablename where `status` = 0");
        if($total_pending_pro == 0){
          // Truncate pre sync table
          $TVC_Admin_DB_Helper->tvc_safe_truncate_table($tablename);
          
          $ee_additional_data['is_process_start'] = false;
          $ee_additional_data['is_auto_sync_start'] = true;
          $ee_additional_data['product_sync_alert'] = NULL;
          $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
          $TVC_Admin_Helper->plugin_log("Manual - product sync process done", 'product_sync');
          as_unschedule_all_actions( 'init_product_sync_process_scheduler' );
        } else {
          $TVC_Admin_Helper->plugin_log("Manual - recall product sync process for remaining ". $total_pending_pro. " products", 'product_sync');
          /*wp_clear_scheduled_hook( 'init_product_sync_process_scheduler' );
          $TVC_Admin_Helper->plugin_log("step 11", 'product_sync');// Add logs
          if ( ! wp_next_scheduled( 'init_product_sync_process_scheduler' ) ) {
            $TVC_Admin_Helper->plugin_log("step 22", 'product_sync');// Add logs
              wp_schedule_single_event( time(), 'init_product_sync_process_scheduler' );
              $TVC_Admin_Helper->plugin_log("step 33", 'product_sync');// Add logs
          }*/
          // $TVC_Admin_Helper->plugin_log(" stary ". as_next_scheduled_action( 'init_product_sync_process_scheduler' ));
          // if ( !as_next_scheduled_action( 'init_product_sync_process_scheduler' ) ) {
            // as_schedule_cron_action( time(), '0/3 * * * *', 'init_product_sync_process_scheduler' );
            /*as_unschedule_all_actions( 'init_product_sync_process_scheduler' );
            as_schedule_single_action( time(), 'init_product_sync_process_scheduler' );*/
          // }
          // $this->tvc_call_start_product_sync_process();
        }
      } else {
        $TVC_Admin_Helper->plugin_log("Manual - Nothing to Sync", 'product_sync');
      }
      echo json_encode(array('status'=>'success', "message" => esc_html__("Product sync process started successfully")));
      return true;
    } catch (Exception $e) {
      $ee_additional_data['product_sync_alert'] = $e->getMessage();
      $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
      $TVC_Admin_Helper->plugin_log("Manual - Error - ".$e->getMessage(), 'product_sync');
    }
    return true;
  }

  public function tvc_call_add_customer_feedback(){
    if(wp_verify_nonce( $_POST['conv_customer_feed_nonce_field'], 'conv_customer_feed_nonce_field_save' ))
    {
      if( isset($_POST['que_one']) &&  isset($_POST['que_two']) && isset($_POST['que_three']) ){
        $formdata = array(); 
        $formdata['business_insights_index'] = sanitize_text_field($_POST['que_one']);
        $formdata['automate_integrations_index'] = sanitize_text_field($_POST['que_two']);
        $formdata['business_scalability_index'] = sanitize_text_field($_POST['que_three']);
        $formdata['subscription_id'] = isset($_POST['subscription_id'])?sanitize_text_field($_POST['subscription_id']):"";
        $formdata['customer_id'] = isset($_POST['customer_id'])?sanitize_text_field($_POST['customer_id']):"";
        $formdata['feedback'] = isset($_POST['feedback_description'])?sanitize_text_field($_POST['feedback_description']):"";
        $customObj = new CustomApi();
        unset($_POST['action']);    
        echo json_encode($customObj->record_customer_feedback($formdata));
        exit;
      }else{
        echo json_encode(array("error"=>true, "message" => esc_html__("Please answer the required questions","enhanced-e-commerce-for-woocommerce-store") ));
      }       
    }
    else
    {
      echo json_encode(array("error"=>true, "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
    }
    // IMPORTANT: don't forget to exit
    exit;
      
  }
  public function tvc_call_add_survey(){
    if ( is_admin() && wp_verify_nonce( $_POST['tvc_call_add_survey'], 'tvc_call_add_survey-nonce' )) {
      if(!class_exists('CustomApi')){
        include(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
      }
      $customObj = new CustomApi();
      unset($_POST['action']);    
      echo json_encode($customObj->add_survey_of_deactivate_plugin($_POST));
    }else
    {
      echo json_encode(array('error' => true, "is_connect"=>false, 'message' => esc_html__("Admin security nonce is not verified.","enhanced-e-commerce-for-woocommerce-store")));
    }
    // IMPORTANT: don't forget to exit
    exit;
  }
  //active licence key
  public function tvc_call_active_licence(){
    if ( is_admin() && wp_verify_nonce( $_POST['conv_licence_nonce'], 'conv_lic_nonce' ) ) {
      $licence_key = isset($_POST['licence_key'])?sanitize_text_field($_POST['licence_key']):"";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $subscription_id = $TVC_Admin_Helper->get_subscriptionId();      
      if($subscription_id!="" && $licence_key != ""){
        $response = $TVC_Admin_Helper->active_licence($licence_key, $subscription_id);
        
        if($response->error== false){
          //$key, $html, $title = null, $link = null, $link_title = null, $overwrite= false
          //$TVC_Admin_Helper->add_ee_msg_nofification("active_licence_key", esc_html__("Your plan is now successfully activated.","enhanced-e-commerce-for-woocommerce-store"), esc_html__("Congratulations!!","enhanced-e-commerce-for-woocommerce-store"), "", "", true);
          $TVC_Admin_Helper->update_subscription_details_api_to_db();
          echo json_encode(array('error' => false, "is_connect"=>true, 'message' => esc_html__("The licence key has been activated.","enhanced-e-commerce-for-woocommerce-store") ));
        }else{
          echo json_encode(array('error' => true, "is_connect"=>true, 'message' => $response->message));
        }       
      }else if($licence_key != ""){ 
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['temp_active_licence_key'] = $licence_key;
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);       
        echo json_encode(array('error' => true, "is_connect"=>false, 'message' => ""));
      }else{
        echo json_encode(array('error' => true, "is_connect"=>false, 'message' => esc_html__("Licence key is required.","enhanced-e-commerce-for-woocommerce-store")));
      }      
    }
    else
    {
      echo json_encode(array('error' => true, "is_connect"=>false, 'message' => esc_html__("Admin security nonce is not verified.","enhanced-e-commerce-for-woocommerce-store")));
    }
    exit;
  }
  public function auto_product_sync_setting(){
    if ( is_admin() && wp_verify_nonce( $_POST['auto_product_sync_setting'], 'auto_product_sync_setting-nonce' )) { 
      as_unschedule_all_actions( 'ee_auto_product_sync_check' );
      $product_sync_duration = isset($_POST['product_sync_duration'])?sanitize_text_field($_POST['product_sync_duration']):"";
      $pro_snyc_time_limit = isset($_POST['pro_snyc_time_limit'])?sanitize_text_field($_POST['pro_snyc_time_limit']):"";
      $product_sync_batch_size = isset($_POST['product_sync_batch_size'])?sanitize_text_field($_POST['product_sync_batch_size']):"";
      $TVC_Admin_Helper = new TVC_Admin_Helper();      
      if($product_sync_duration != "" && $pro_snyc_time_limit != "" && $product_sync_batch_size != ""){ 
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['product_sync_duration'] = $product_sync_duration;
        $ee_additional_data['pro_snyc_time_limit'] = $pro_snyc_time_limit;
        $ee_additional_data['product_sync_batch_size'] = $product_sync_batch_size;
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        new TVC_Admin_Auto_Product_sync_Helper();
        echo json_encode(array('error' => false, 'message' => esc_html__("Time interval and batch size successfully saved.","enhanced-e-commerce-for-woocommerce-store")));
      }else{
        echo json_encode(array('error' => true, 'message' => esc_html__("Error occured while saving the settings.","enhanced-e-commerce-for-woocommerce-store")));
      }      
    }else{
      echo json_encode(array('error' => true, "is_connect"=>false, 'message' => esc_html__("Admin security nonce is not verified.","enhanced-e-commerce-for-woocommerce-store")));        
    }
    // IMPORTANT: don't forget to exit
    exit;
  }

  public function con_get_conversion_list(){
      if ($this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce'), 'con_get_conversion_list-nonce')) {
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $this->customApiObj = new CustomApi();
        $this->current_customer_id = $TVC_Admin_Helper->get_currentCustomerId();
        if ($this->current_customer_id != "") {
          $response = $this->customApiObj->get_conversion_list($this->current_customer_id);
          if (property_exists($response, "error") && $response->error == false) {
            if (property_exists($response, "data") && $response->data != "" && !empty($response->data)) {
              $selected_conversio_send_to = get_option('ee_conversio_send_to');
              $conversion_label = array();
              foreach ($response->data as $key => $value) {
                $con_string = strip_tags($value->tagSnippets);
                $conversion_label_check = $TVC_Admin_Helper->get_conversion_label($con_string);
                if ($conversion_label_check != "" && $conversion_label_check != null) {
                  $conversion_label[] = $TVC_Admin_Helper->get_conversion_label($con_string);
                }
              }
              echo json_encode($conversion_label);
              exit;
            }
          }
        }
      }
      // IMPORTANT: don't forget to exit
      wp_die(0);
  }
  public function tvc_call_notification_dismiss(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'TVCNonce'), 'tvc_call_notification_dismiss-nonce')){      
      $ee_dismiss_id = isset($_POST['data']['ee_dismiss_id'])?sanitize_text_field($_POST['data']['ee_dismiss_id']):"";
      if($ee_dismiss_id != ""){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $ee_msg_list = $TVC_Admin_Helper->get_ee_msg_nofification_list();
        if( isset($ee_msg_list[$ee_dismiss_id]) ){          
          unset($ee_msg_list[$ee_dismiss_id]);
          $ee_msg_list[$ee_dismiss_id]["active"]=0;
          $TVC_Admin_Helper->set_ee_msg_nofification_list($ee_msg_list);
          echo json_encode(array('status' => 'success', 'message' => ""));
        }        
      }       
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
    }
    // IMPORTANT: don't forget to exit
    exit;
  }
  public function tvc_call_notice_dismiss(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiNoticDismissNonce'), 'tvc_call_notice_dismiss-nonce')){      
      $ee_notice_dismiss_id = isset($_POST['data']['ee_notice_dismiss_id'])?sanitize_text_field($_POST['data']['ee_notice_dismiss_id']):"";
      $ee_notice_dismiss_id = sanitize_text_field($ee_notice_dismiss_id);
      if($ee_notice_dismiss_id != ""){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
        $ee_additional_data['dismissed_'.$ee_notice_dismiss_id] = 1;
        $TVC_Admin_Helper->set_ee_additional_data($ee_additional_data);
        echo json_encode(array('status' => 'success', 'message' => $ee_additional_data));
      }       
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
    }
    // IMPORTANT: don't forget to exit
    exit;
  }

  public function tvc_call_notice_dismiss_trigger(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiNoticDismissNonce'), 'tvc_call_notice_dismiss-nonce')){   
      $ee_notice_dismiss_id_trigger = isset($_POST['data']['ee_notice_dismiss_id_trigger'])?sanitize_text_field($_POST['data']['ee_notice_dismiss_id_trigger']):"";
      $ee_notice_dismiss_id_trigger = sanitize_text_field($ee_notice_dismiss_id_trigger);
    if($ee_notice_dismiss_id_trigger != ""){
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $ee_additional_data = $TVC_Admin_Helper->get_ee_additional_data();
      $slug = $ee_notice_dismiss_id_trigger;
      $title = "";
      $content = "";
      $status = "0";
      $TVC_Admin_Helper->tvc_dismiss_admin_notice($slug, $content, $status,$title);
     }
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
    }
    // IMPORTANT: don't forget to exit
    exit;
   }
  public function tvc_call_import_gmc_product(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiSyncupNonce'), 'tvc_call_api_sync-nonce')){
      $next_page_token = isset($_POST['next_page_token'])?sanitize_text_field($_POST['next_page_token']):"";
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $api_rs = $TVC_Admin_Helper->update_gmc_product_to_db($next_page_token);
      if( isset($api_rs['error']) ){
        echo json_encode($api_rs);
      }else{
        echo json_encode(array('error' => true, 'message' => esc_html__("Please try after some time.","enhanced-e-commerce-for-woocommerce-store")));
      }
      
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
    }
    // IMPORTANT: don't forget to exit
    exit;
  }
  public function tvc_call_api_sync(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiSyncupNonce'), 'tvc_call_api_sync-nonce')){
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $api_rs = $TVC_Admin_Helper->set_update_api_to_db();
        if(isset($api_rs['error']) && isset($api_rs['message']) && sanitize_text_field($api_rs['message'])){
          echo json_encode($api_rs);
        }else{
          echo json_encode(array('error' => true, 'message' => esc_html__("Please try after some time.","enhanced-e-commerce-for-woocommerce-store")));
        }
        
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
    }
    // IMPORTANT: don't forget to exit
    exit;
  }
  public function tvc_call_site_verified(){
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'SiteVerifiedNonce'), 'tvc_call_site_verified-nonce')){
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $tvc_rs =[];
      $tvc_rs = $TVC_Admin_Helper->call_site_verified();
      if(isset($tvc_rs['error']) && $tvc_rs['error'] == 1){
        echo json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }else{
        echo json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }      
      exit;
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
      exit;
    }
  }
  public function tvc_call_domain_claim(){     
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'apiDomainClaimNonce'), 'tvc_call_domain_claim-nonce')){
      $TVC_Admin_Helper = new TVC_Admin_Helper();
      $tvc_rs = $TVC_Admin_Helper->call_domain_claim();
      if(isset($tvc_rs['error']) && $tvc_rs['error'] == 1){
        echo json_encode(array('status' => 'error', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }else{
        echo json_encode(array('status' => 'success', 'message' => sanitize_text_field($tvc_rs['msg'])));
      }      
      exit;
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
      exit;
    }
  }
  public function get_tvc_access_token(){
    if(!empty($this->access_token)){
        return $this->access_token;
    }else{
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
        $this->access_token = sanitize_text_field(base64_decode($google_detail['setting']->access_token));
        return $this->access_token;
    }
  }
  
  public function get_tvc_refresh_token(){
    if(!empty($this->refresh_token)){
        return $this->refresh_token;
    }else{
        $TVC_Admin_Helper = new TVC_Admin_Helper();
        $google_detail = $TVC_Admin_Helper->get_ee_options_data();          
        $this->refresh_token = sanitize_text_field(base64_decode($google_detail['setting']->refresh_token));
        return $this->refresh_token;
    }
  }
  /**
   * Delete the campaign
   */
  public function tvcajax_delete_campaign(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignDeleteNonce'), 'tvcajax_delete_campaign-nonce')){

          $merchantId = filter_input(INPUT_POST, 'merchantId');
          $customerId = filter_input(INPUT_POST, 'customerId');
          $campaignId = filter_input(INPUT_POST, 'campaignId');

          $url = $this->apiDomain.'/campaigns/delete';
          $data = [
              'merchant_id' => sanitize_text_field($merchantId),
              'customer_id' => sanitize_text_field($customerId),
              'campaign_id' => sanitize_text_field($campaignId)
          ];
          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'method' => 'DELETE',
              'body' => wp_json_encode($data)
          );
          // Send remote request
          $request = wp_remote_request(esc_url_raw($url), $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              $message = $response_body->message;
              echo json_encode(['status' => 'success', 'message' => $message]);
          }else{
              $message = is_array($response_body->errors) ? $response_body->errors[0] : "Face some unprocessable entity";
              echo json_encode(['status' => 'error', 'message' => $message]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }
      }else{
        echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
      }
      // IMPORTANT: don't forget to exit
    exit;
  }

  /**
   * Update the campaign status pause/active
   */
  public function tvcajax_update_campaign_status(){
    // make sure this call is legal
    if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignStatusNonce'), 'tvcajax-update-campaign-status-nonce')){
        if(!class_exists('ShoppingApi')){
          include(ENHANCAD_PLUGIN_DIR . 'includes/setup/ShoppingApi.php');
        }

        $header = array(
            "Authorization: Bearer MTIzNA==",
            "Content-Type" => "application/json"
        );

        $merchantId = filter_input(INPUT_POST, 'merchantId');
        $customerId = filter_input(INPUT_POST, 'customerId');
        $campaignId = filter_input(INPUT_POST, 'campaignId');
        $budgetId = filter_input(INPUT_POST, 'budgetId');
        $campaignName = filter_input(INPUT_POST, 'campaignName');
        $budget = filter_input(INPUT_POST, 'budget');
        $status = filter_input(INPUT_POST, 'status');
        $curl_url = $this->apiDomain.'/campaigns/update';
        $shoppingObj = new ShoppingApi();
        $campaignData = $shoppingObj->getCampaignDetails($campaignId);

        $data = [
            'merchant_id' => sanitize_text_field($merchantId),
            'customer_id' => sanitize_text_field($customerId),
            'campaign_id' => sanitize_text_field($campaignId),
            'account_budget_id' => sanitize_text_field($budgetId),
            'campaign_name' => sanitize_text_field($campaignName),
            'budget' => sanitize_text_field($budget),
            'status' => sanitize_text_field($status),
            'target_country' => sanitize_text_field($campaignData->data['data']->targetCountry),
            'ad_group_id' => sanitize_text_field($campaignData->data['data']->adGroupId),
            'ad_group_resource_name' => sanitize_text_field($campaignData->data['data']->adGroupResourceName)
        ];
        
        $args = array(
          'headers' =>$header,
          'method' => 'PATCH',
          'body' => wp_json_encode($data)
        );
        $request = wp_remote_request(esc_url_raw($curl_url), $args);
        // Retrieve information
        $response_code = wp_remote_retrieve_response_code($request);
        $response_message = wp_remote_retrieve_response_message($request);
        $response = json_decode(wp_remote_retrieve_body($request));
        if (isset($response->error) && $response->error == false) {
          $message = $response->message;
          echo json_encode(['status' => 'success', 'message' => $message]);
        }else{
          $message = is_array($response->errors) ? $response->errors[0] : esc_html__("Face some unprocessable entity","enhanced-e-commerce-for-woocommerce-store");
          echo json_encode(['status' => 'error', 'message' => $message]);
        }
    }else{
      echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
    }
    // IMPORTANT: don't forget to exit
    exit;
  }

  /**
   * Returns the campaign categories from a selected country
   */
  public function tvcajax_get_campaign_categories(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'campaignCategoryListsNonce'), 'tvcajax-campaign-category-lists-nonce')){

          $country_code = filter_input(INPUT_POST, 'countryCode');
          $customer_id = filter_input(INPUT_POST, 'customerId');
          $url = $this->apiDomain.'/products/categories';

          $data = [
              'customer_id' => sanitize_text_field($customer_id),
              'country_code' =>sanitize_text_field( $country_code)
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_post(esc_url_raw($url), $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              echo json_encode($response_body->data);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              echo json_encode([]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

  /**
   * Returns the campaign categories from a selected country
   */
  public function tvcajax_get_gmc_categories(){
      // make sure this call is legal
      if($this->safe_ajax_call(filter_input(INPUT_POST, 'gmcCategoryListsNonce'), 'tvcajax-gmc-category-lists-nonce')){

          $country_code = filter_input(INPUT_POST, 'countryCode');
          $customer_id = filter_input(INPUT_POST, 'customerId');
          $parent = filter_input(INPUT_POST, 'parent');
          $url = $this->apiDomain.'/products/gmc-categories';

          $data = [
              'customer_id' => sanitize_text_field($customer_id),
              'country_code' => sanitize_text_field($country_code),
              'parent' => sanitize_text_field($parent)
          ];

          $args = array(
              'headers' => array(
                  'Authorization' => "Bearer MTIzNA==",
                  'Content-Type' => 'application/json'
              ),
              'body' => wp_json_encode($data)
          );

          // Send remote request
          $request = wp_remote_post(esc_url_raw($url), $args);

          // Retrieve information
          $response_code = wp_remote_retrieve_response_code($request);
          $response_message = wp_remote_retrieve_response_message($request);
          $response_body = json_decode(wp_remote_retrieve_body($request));

          if((isset($response_body->error) && $response_body->error == '')){
              echo json_encode($response_body->data);
//                    return new WP_REST_Response(
//                        array(
//                            'status' => $response_code,
//                            'message' => $response_message,
//                            'data' => $response_body->data
//                        )
//                    );
          }else{
              echo json_encode([]);
              // return new WP_Error($response_code, $response_message, $response_body);
          }

          //   echo json_encode( $categories );
      }else{
        echo json_encode(array('status' => 'error', "message" => esc_html__("Admin security nonce is not verified.", "enhanced-e-commerce-for-woocommerce-store") ));
        exit;
      }

      // IMPORTANT: don't forget to exit
      exit;
  }

}
// End of TVC_Ajax_File_Class
endif;
$tvcajax_file_class = new TVC_Ajax_File();