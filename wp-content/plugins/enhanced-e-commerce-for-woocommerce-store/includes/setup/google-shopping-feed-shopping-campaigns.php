<?php

class CampaignsConfiguration
{
  protected $merchantId;
  protected $currentCustomerId;
  protected $subscriptionId;

  protected $customApiObj;
  protected $currency_symbol;
  public function __construct(){
    $this->includes();
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->customApiObj = new CustomApi();

    $this->merchantId = $this->TVC_Admin_Helper->get_merchantId();
    $this->currentCustomerId = $this->TVC_Admin_Helper->get_currentCustomerId();    
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();;
    $this->new_campaign = true;

    $this->date_range_type = (isset($_POST['customRadio']))  ? sanitize_text_field($_POST['customRadio']) : 1;
    $this->days = (isset($_POST['days']) && sanitize_text_field($_POST['days']) != '') ? sanitize_text_field($_POST['days']) : 7;
    $this->from_date = (isset($_POST['from_date']) && sanitize_text_field($_POST['from_date']) != '') ? sanitize_text_field($_POST['from_date']) : date("Y-m-d", strtotime("-1 month"));
    $this->to_date = (isset($_POST['to_date']) && sanitize_text_field($_POST['to_date'] )!= '') ? sanitize_text_field($_POST['to_date']) : date("Y-m-d");
    $this->country = $this->TVC_Admin_Helper->get_woo_country();
    $this->currency_symbol = $this->TVC_Admin_Helper->get_user_currency_symbol();
    $this->site_url = "admin.php?page=conversios-google-shopping-feed&tab="; 
    $this->html_run();
  }

  public function includes(){
    if (!class_exists('CustomApi.php')) {
      require_once(__DIR__ . '/CustomApi.php');
    }
    if (!class_exists('ShoppingApi')) {
      require_once(__DIR__ . '/ShoppingApi.php');
    }
    if (!class_exists('Tatvic_Category_Wrapper')) {
      require_once(__DIR__ . '/tatvic-category-wrapper.php');
    }
  }

  public function html_run(){
  	$this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }

  public function create_form(){
    $class = "";
    $message = "";
    $googleDetail = [];
    $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    if(isset($google_detail['setting'])){
      if ($google_detail['setting']) {
        $googleDetail = $google_detail['setting'];
      }
    }

    $date_range_type = ((isset($_POST['customRadio'])) ? sanitize_text_field($_POST['customRadio']) : 1);
		$days = ((isset($_POST['days']) && sanitize_text_field($_POST['days']) != '') ? sanitize_text_field($_POST['days']) : 7);
		$from_date = ((isset($_POST['from_date']) && sanitize_text_field($_POST['from_date']) != '') ? sanitize_text_field($_POST['from_date']) : "");
		$to_date = ((isset($_POST['to_date']) && sanitize_text_field($_POST['to_date']) != '') ? sanitize_text_field($_POST['to_date']) : "");
    $from_date = sanitize_text_field($from_date);
    $to_date = sanitize_text_field($to_date);
    $campaigns_list = [];
    $categories = [];
    $campaign_performance = [];
    $account_performance = [];
    $product_performance = [];
    $product_partition_performance = [];
		$api_old_obj = new ShoppingApi();

    $campaigns_list_res = $api_old_obj->getCampaigns();
    if(isset($campaigns_list_res->errors) || isset($campaigns_list_res->errors[423])){
      $class = 'tvc-alert-error';
      $message = esc_html__("If Data is not generated please make sure your Google Ads account should link with our MCC account.","enhanced-e-commerce-for-woocommerce-store");
    }
    
    if (isset($campaigns_list_res->errors) && !empty($campaigns_list_res->errors) && $message == "") {
      $class = 'tvc-alert-error';
      $message = esc_html__("Not any campaigns found.","enhanced-e-commerce-for-woocommerce-store");
    } else if(isset($campaigns_list_res->data)){
      $campaigns_list_res = $campaigns_list_res->data;
      if ($campaigns_list_res['status'] == 200) {
        $campaigns_list = $campaigns_list_res['data'];
      }
    }
    $totalConversion = 0;
    $totalSale = 0;
    $totalCost = 0;
    $totalClick = 0;
		if(count($campaigns_list) > 0) {
		  //Account Performance
	    $account_performance_res = $api_old_obj->accountPerformance( $this->from_date, $this->to_date );
	    if (isset($account_performance_res->errors) && !empty($account_performance_res->errors)) {
      
	    } else {
        $account_performance_res = $account_performance_res->data;
        if ($account_performance_res['status'] == 200) {
          $account_performance = $account_performance_res['data'];
        }
	    }
	    // Count account performance	    
	    if (!empty($account_performance->dailyConversions)) {
        foreach($account_performance->dailyConversions as $key => $dailyConversion){
            $totalConversion = $totalConversion + esc_attr($dailyConversion->conversions);
        }
	    }	    
	    if (!empty($account_performance->dailySales)) {
        foreach ($account_performance->dailySales as $key => $dailySale) {
          $totalSale = $totalSale + esc_attr($dailySale->sales);
        }
	    }	    
	    if (!empty($account_performance->dailyCost)) {
        foreach ($account_performance->dailyCost as $key => $dailyCostData) {
          $totalCost = $totalCost + esc_attr($dailyCostData->costs);
        }
	    }
	   
	    if (!empty($account_performance->dailyClicks)) {
        foreach ($account_performance->dailyClicks as $key => $dailyClick) {
          $totalClick = $totalClick + esc_attr($dailyClick->clicks);
        }
	    }
		  //Campaign Performance
		  $campaign_performance_res = $api_old_obj->campaignPerformance($this->date_range_type, $this->days, $this->from_date, $this->to_date);
	    if (isset($campaign_performance_res->errors) && !empty($campaign_performance_res->errors)) {

	    } else if(isset($campaign_performance_res->data)){
        $campaign_performance_res = $campaign_performance_res->data;
        if ($campaign_performance_res['status'] == 200) {
          $campaign_performance = $campaign_performance_res['data'];
        }
	    }
		}
    if(isset($_GET['id']) && sanitize_text_field($_GET['id']) != '') {
      $CampaignDetail = $api_old_obj->getCampaignDetails($_GET['id']);
      if (isset($CampaignDetail->errors) && !empty($CampaignDetail->errors)) {
        
      }else if(isset($CampaignDetail->data['data'])){
        $adGroupId = $CampaignDetail->data['data']->adGroupId;
        
        //Product Performance
        $product_performance_res = $api_old_obj->productPerformance(sanitize_text_field($_GET['id']), 2, 30, $this->from_date, $this->to_date, $adGroupId);


        if (isset($product_performance_res->errors) && !empty($product_performance_res->errors)) {

        } else if(isset($product_performance_res->data)){
          $product_performance_res = $product_performance_res->data;
          if ($product_performance_res['status'] == 200) {
            $product_performance = $product_performance_res['data'];
          }
        }
        //Product Partition Performance
        $product_partition_performance_res = $api_old_obj->productPartitionPerformance(sanitize_text_field($_GET['id']), 2, 30, $this->from_date, $this->to_date, $adGroupId);

        if (isset($product_partition_performance_res->errors) && !empty($product_partition_performance_res->errors)) {

        } else if(isset($product_partition_performance_res->data)){
          $product_partition_performance_res = $product_partition_performance_res->data;
          if ($product_partition_performance_res['status'] == 200) {
              $product_partition_performance = $product_partition_performance_res['data'];
          }
        }

      }

			

			
		}
    
    ?>
<div class="con-tab-content">
  <?php if($message){
    printf('<div class="alert-message %1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message));
  }?>
  <div class="tab-pane show active" id="googleShoppingFeed">
    <div class="tab-card">
      <div class="row">
        <div class="col-md-12">
          <div class="edit-section">
            <div class="row">
              <div class="configuration-section col-md-6 col-lg-8">
                <?php echo get_google_shopping_tabs_html($this->site_url, (isset($googleDetail->google_merchant_center_id))?$googleDetail->google_merchant_center_id:""); ?>
              </div>
            </div>
            <div class="con-tab-content" id="myTabContent">
              <div class="tab-pane fade show active" id="smartShopping">
                <div class="smart-shopping-section">
                  <form method="post" id="date_range_form">
                    <input type="hidden" id="customRange2" name="customRadio" value="2" class="custom-control-input" checked="checked">
                    <div class="campaigns">
                      <div class="d-flex justify-content-between align-items-center">
                        <h3 class="title mb-0"><?php esc_html_e("Smart Shopping Campaigns","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                        <div class="campaing-date">
                          <div class="input-group input-daterange">
                              <input type="text" class="form-control" id="from_date" name="from_date" value="<?php echo esc_attr($this->from_date); ?>">
                              <div class="input-group-addon text px-3">to</div>
                              <input type="text" class="form-control" id="to_date" name="to_date" value="<?php echo esc_attr($this->to_date); ?>">
                              <label class="mt-2 mb-2 error-msg float-left hidden" id="errorMessage"><?php esc_html_e("Please select both from and to date","enhanced-e-commerce-for-woocommerce-store"); ?></label>
                          </div>
                        </div>
                        <div class="create-campaign-btn">
                            <button type="button" class="btn btn-outline-primary" onclick="validateAll()" id="select_range" name="select_range"><?php esc_html_e("Submit","enhanced-e-commerce-for-woocommerce-store"); ?></button>
                            <a href="<?php echo esc_url_raw($this->site_url.'add_campaign_page');?>" class="btn btn-outline-primary" id="create_a_new_campaign"><?php esc_html_e("Create a New Campaign","enhanced-e-commerce-for-woocommerce-store"); ?></a>
                        </div>
                      </div>
                    </div>
                  </form>                
                  <div class="account-performance">
                    <div class="row">
                    	<div class="col-md-12">
                        <h3 class="title"><?php esc_html_e("Account Performance","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title"><?php esc_html_e("Daily Clicks","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
                          <canvas id="dailyClick" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title"><?php esc_html_e("Daily Cost","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
                          <canvas id="dailyCost" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title"><?php esc_html_e("Daily Conversions","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
                          <canvas id="dailyConversions" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    	<div class="col-md-6 col-lg-3">
                        <div class="chart">
                          <h4 class="sub-title"><?php esc_html_e("Daily Sales","enhanced-e-commerce-for-woocommerce-store"); ?></h4>
                          <canvas id="dailySales" width="240" height="180"></canvas>
                        </div>
                    	</div>
                    </div>
                  </div>
                </div>
                <div class="account-performance">
                  <div class="row">
                    <div class="col-lg-10">
                      <h3 class="title"><?php esc_html_e("Campaign Performance","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                      <div class="table-section">
                        <table id="campaingPerformance" class="table dt-responsive nowrap" style="width:100%">
                          <thead>
                            <tr>
                              <th><?php esc_html_e("Campaign","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th width="100"><?php esc_html_e("Daily Budget","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Active","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Clicks","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Cost","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Conversions","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Sales","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Action","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php 
                            $total_campaigns = count($campaign_performance);
                            for ($i = 0; $i < $total_campaigns; $i++) {
                              $checked =  esc_attr($campaign_performance[$i]->active) == 0 ? '' : 'checked';
                              if (esc_attr($campaign_performance[$i]->active) != 2) {?>
                                <tr>
                                  <td><a href="<?php echo esc_url_raw($this->site_url.'shopping_campaigns_page&id='.$campaign_performance[$i]->compaignId); ?>" class="text-underline"><?php echo esc_attr($campaign_performance[$i]->compaignName); ?></a></td>
                                  <td><?php echo  esc_attr($this->currency_symbol.$campaign_performance[$i]->dailyBudget); ?></td>
                                  <td class="text-center">
                                    <div class="form-check form-switch">
                                      <input type="checkbox" class="form-check-input"  id="customSwitch<?php echo esc_attr($i); ?>" <?php echo esc_attr($checked); ?> onchange="updateCampaignStatus('<?php echo esc_attr($this->merchantId); ?>','<?php echo esc_attr($this->currentCustomerId); ?>','<?php echo esc_attr($campaign_performance[$i]->compaignId); ?>','<?php echo esc_attr($campaign_performance[$i]->dailyBudget); ?>','<?php echo esc_attr($campaign_performance[$i]->budgetId); ?>','<?php echo esc_attr($i); ?>')">
                                      <label class="form-check-label" for="customSwitch<?php echo esc_attr($i); ?>"></label>
                                    </div>
                                  </td>
                                  <td class="text-center"><?php echo esc_attr(number_format($campaign_performance[$i]->clicks, 2) ); ?></td>
                                  <td class="text-center"><?php echo esc_attr(number_format($campaign_performance[$i]->cost, 2)); ?></td>
                                  <td class="text-center"><?php echo esc_attr(number_format($campaign_performance[$i]->conversions, 2)); ?></td>
                                  <td class="text-center"><?php echo esc_attr(number_format($campaign_performance[$i]->sales, 2)); ?></td>
                                    <input type="hidden" value="<?php echo  esc_attr($campaign_performance[$i]->compaignName); ?>" id="campaign_name_<?php echo  esc_attr($i); ?>" />
                                  <td><a href="<?php echo esc_url_raw($this->site_url.'add_campaign_page&edit='.$campaign_performance[$i]->compaignId); ?>"><?php esc_html_e("Edit","enhanced-e-commerce-for-woocommerce-store"); ?></a> | <a href="#" onclick="deleteCampaign('<?php echo esc_attr($this->merchantId); ?>','<?php echo esc_attr($this->currentCustomerId); ?>','<?php echo esc_attr($campaign_performance[$i]->compaignId); ?>','<?php echo esc_attr($i); ?>')"><?php esc_html_e("Delete","enhanced-e-commerce-for-woocommerce-store"); ?></a></td>
                                </tr>
                              <?php
                              }				                
                            }?>
                          </tbody>
                        </table>
                      </div>
                    </div>                    
                    <div class="col-lg-4">
                      <div class="pro-account ml-lg-auto">
                        <div class="card">
                          <div class="card-body">
                            <div class="account-img">
                              <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/icon/undraw_update_uxn2.svg'); ?>" alt=""/>
                            </div>
                            <div class="pro-content">
                              <h3 class="userName"><?php esc_html_e("Hello","enhanced-e-commerce-for-woocommerce-store"); ?> <?php echo esc_attr(get_bloginfo()); ?>!</h3>
                              <p>Explore <a target="_blank" href="<?php echo esc_url_raw($this->TVC_Admin_Helper->get_pro_plan_site()); ?>" class="font-weight-bold"><?php esc_html_e("Pro account","enhanced-e-commerce-for-woocommerce-store"); ?></a> <?php esc_html_e("with Premium features.","enhanced-e-commerce-for-woocommerce-store"); ?></p>
                            </div>
                          </div>
                          <div class="card-footer">
                            <a target="_blank" href="<?php echo esc_url_raw($this->TVC_Admin_Helper->get_pro_plan_site().'pricings'); ?>"><button class="btn btn-primary"><?php esc_html_e("Learn more","enhanced-e-commerce-for-woocommerce-store"); ?></button></a>
                          </div>
                        </div>
                      </div>
                    </div>                  
                  </div>
                </div><!-- account-performance-->
                <?php
                if(isset($_GET['id']) && sanitize_text_field($_GET['id']) != '') { ?>
                <div class="account-performance">
                  <div class="row">
                    <div class="col-lg-10">
                      <h3 class="title"><?php esc_html_e("Product Performance :","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                      <div class="table-section">
                        <table id="productPerformance" class="table dt-responsive nowrap" style="width:100%">
                          <thead>
                              <tr>
                                  <th></th>
                                  <th><?php esc_html_e("Product","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                  <th class="text-center"><?php esc_html_e("Clicks","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                  <th class="text-center"><?php esc_html_e("Cost","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                  <th class="text-center"><?php esc_html_e("Conversions","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                                  <th class="text-center"><?php esc_html_e("Sales","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              </tr>
                          </thead>
                          <tbody>
                          <?php for ($i = 0; $i < count($product_performance); $i++) { ?>
                            <tr>
                              <td class="product-image"><?php echo esc_attr($i+1); ?></td>
                              <td><?php echo esc_attr($product_performance[$i]->productTitle); ?></td>
                              <td class="text-center"><?php echo esc_attr(number_format($product_performance[$i]->clicks, 2) ); ?></td>
                              <td class="text-center"><?php echo esc_attr(number_format($product_performance[$i]->cost, 2) ); ?></td>
                              <td class="text-center"><?php echo esc_attr(number_format($product_performance[$i]->conversions, 2) ); ?></td>
                              <td class="text-center"><?php echo esc_attr(number_format($product_performance[$i]->sales, 2) ); ?></td>
                            </tr>
                          <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div><!-- account-performance-->
                <div class="account-performance">
                  <div class="row">
                    <div class="col-lg-10">
                      <h3 class="title"><?php esc_html_e("Product Partition Performance","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                      <div class="table-section">
                        <table id="productPerformance" class="table dt-responsive nowrap" style="width:100%">
                          <thead>
                            <tr>
                              <th><?php esc_html_e("Campaign","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Product Dimension","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <?php /*<th class="text-center"><?php esc_html_e("Product Dimension Value","enhanced-e-commerce-for-woocommerce-store"); ?></th> */ ?>
                              <th class="text-center"><?php esc_html_e("Clicks","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Cost","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Conversions","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                              <th class="text-center"><?php esc_html_e("Sales","enhanced-e-commerce-for-woocommerce-store"); ?></th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php for ($i = 0; $i < count($product_partition_performance); $i++) { ?>
                            <tr>
                              <td><a href="" class="text-underline"><?php echo esc_attr($product_partition_performance[$i]->compaignName); ?></a></td>
                              <td class="text-center"><?php echo esc_attr($product_partition_performance[$i]->productDimention); ?></td>
                              <?php /*<td class="text-center"><?php echo esc_attr($product_partition_performance[$i]->productDimentionValue); ?></td>*/ ?>
                              <td class="text-center"><?php echo esc_attr(number_format($product_partition_performance[$i]->clicks, 2) ); ?></td>
                              <td class="text-center"><?php echo esc_attr(number_format($product_partition_performance[$i]->cost, 2) ); ?></td>
                              <td class="text-center"><?php echo esc_attr(number_format($product_partition_performance[$i]->conversions, 2) ); ?></td>
                              <td class="text-center"><?php echo esc_attr(number_format($product_partition_performance[$i]->sales, 2) ); ?></td>
                            </tr>			                
                          <?php } ?>
                          </tbody>
                        </table>
                      </div>
             	      </div>
                  </div>
                </div><!-- account-performance -->
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
	  jQuery(".select2").select2();
  });

  jQuery(document).ready(function() {
    jQuery(document).on("click", "#create_a_new_campaign", function(event){
      var el = $(this).attr("id");
      user_tracking_data('click', 'null','shopping_campaigns_page',el);
    });    
  });  

	var ctx = document.getElementById('dailyClick').getContext('2d');
	var dailyClicksData = jQuery.parseJSON('<?php echo (isset($account_performance->dailyClicks)?json_encode($account_performance->dailyClicks):''); ?>');
   
  var labels = [];
  var values = [];
  if(dailyClicksData.length > 0 ){
  	dailyClicksData.forEach(clickData => {
    	labels.push(clickData.date);
    	values.push(clickData.clicks);
    })
  }
  var gradientFill = ctx.createLinearGradient(0, 0, 0, 500);
  gradientFill.addColorStop(0.4, 'rgba(107, 232, 255, 0.9)');
  gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.75)');
  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Clicks',
      	data: values,
        borderColor: '#002BFC',
        pointBorderColor: '#002BFC',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointRadius: 2,
        fill: true,
        backgroundColor: gradientFill,
        borderWidth: 1
      }]
    },
    options: {
        animation: {
            easing: "easeInOutBack"
        },
        plugins:{
            legend:false
        },
        responsive: true,
        scales: {
          y:{
            fontColor: "#ffffff",
            fontStyle: "normal",
            beginAtZero: true,
            maxTicksLimit: 5,
            padding: 30,
            grid:{
              borderWidth:0,
            },
            ticks: {
              stepSize: 1000,
              callback: function(value) {
                 var ranges = [
                    { divider: 1e6, suffix: 'M' },
                    { divider: 1e3, suffix: 'k' }
                 ];
                 function formatNumber(n) {
                    for (var i = 0; i < ranges.length; i++) {
                       if (n >= ranges[i].divider) {
                          return (n / ranges[i].divider).toString() + ranges[i].suffix;
                       }
                    }
                    return n;
                 }
                 return '' + formatNumber(value);
              }
           }
          },
          x:{
              padding: 10,
              fontColor: "#ffffff",
              fontStyle: "normal",
              grid: {
                display:false
              }
          }        
        }
      }
  });  
  
  var ctx = document.getElementById('dailyCost').getContext('2d');
  var dailyCostData = jQuery.parseJSON('<?php echo (isset($account_performance->dailyCost)?json_encode($account_performance->dailyCost):0); ?>');
  var labels = [];
  var values = [];
  if(dailyClicksData.length > 0 ){
  	dailyCostData.forEach(costData => {
      	labels.push(costData.date);
      	values.push(costData.costs);
  	})
  }
  var gradientFill = ctx.createLinearGradient(0, 0, 0, 500);
  gradientFill.addColorStop(0.4, 'rgba(255, 229, 139, 0.9)');
  gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.7)');
  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Cost',
      	data: values,
        borderColor: '#002BFC',
        pointBorderColor: '#002BFC',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointRadius: 2,
        fill: true,
        backgroundColor: gradientFill,
        borderWidth: 1
      }]
    },
    options: {
        animation: {
            easing: "easeInOutBack"
        },
        plugins:{
            legend:false
        },
        responsive: true,
        scales: {
          y:{
            fontColor: "#ffffff",
            fontStyle: "normal",
            beginAtZero: true,
            maxTicksLimit: 5,
            padding: 30,
            grid:{
              borderWidth:0,
            },
            ticks: {
              stepSize: 1000,
              callback: function(value) {
                 var ranges = [
                    { divider: 1e6, suffix: 'M' },
                    { divider: 1e3, suffix: 'k' }
                 ];
                 function formatNumber(n) {
                    for (var i = 0; i < ranges.length; i++) {
                       if (n >= ranges[i].divider) {
                          return (n / ranges[i].divider).toString() + ranges[i].suffix;
                       }
                    }
                    return n;
                 }
                 return '' + formatNumber(value);
              }
           }
          },
          x:{
              padding: 10,
              fontColor: "#ffffff",
              fontStyle: "normal",
              grid: {
                display:false
              }
          }        
        }
      }
  });  
  
  var ctx = document.getElementById('dailyConversions').getContext('2d');
  var dailyConversionsData = jQuery.parseJSON('<?php echo (isset($account_performance->dailyConversions)?json_encode($account_performance->dailyConversions):0); ?>');
  
  var labels = [];
  var values = [];  
  if(dailyClicksData.length > 0 ){
  	dailyConversionsData.forEach(conversionsData => {
      labels.push(conversionsData.date);
      values.push(conversionsData.conversions);
  	})
  }
  var gradientFill = ctx.createLinearGradient(0, 0, 0, 500);
  gradientFill.addColorStop(0.4, 'rgba(110, 245, 197, 0.9)');
  gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.7)');
  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Conversions',
      	data: values,
        borderColor: '#002BFC',
        pointBorderColor: '#002BFC',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointRadius: 2,
        fill: true,
        backgroundColor: gradientFill,
        borderWidth: 1
      }]
    },
    options: {
        animation: {
            easing: "easeInOutBack"
        },
        plugins:{
            legend:false
        },
        responsive: true,
        scales: {
          y:{
            fontColor: "#ffffff",
            fontStyle: "normal",
            beginAtZero: true,
            maxTicksLimit: 5,
            padding: 30,
            grid:{
              borderWidth:0,
            },
            ticks: {
              stepSize: 1000,
              callback: function(value) {
                 var ranges = [
                    { divider: 1e6, suffix: 'M' },
                    { divider: 1e3, suffix: 'k' }
                 ];
                 function formatNumber(n) {
                    for (var i = 0; i < ranges.length; i++) {
                       if (n >= ranges[i].divider) {
                          return (n / ranges[i].divider).toString() + ranges[i].suffix;
                       }
                    }
                    return n;
                 }
                 return '' + formatNumber(value);
              }
           }
          },
          x:{
              padding: 10,
              fontColor: "#ffffff",
              fontStyle: "normal",
              grid: {
                display:false
              }
          }        
        }
      }
  });  
  
  var ctx = document.getElementById('dailySales').getContext('2d');
	var dailySalesData = jQuery.parseJSON('<?php echo (isset($account_performance->dailySales)?json_encode($account_performance->dailySales):0); ?>');

	var labels = [];
	var values = [];
  if(dailyClicksData.length > 0 ){
  	dailySalesData.forEach(salesData => {
    	labels.push(salesData.date);
    	values.push(salesData.sales);
  	})
  }
  var gradientFill = ctx.createLinearGradient(0, 0, 0, 500);
  gradientFill.addColorStop(0.4, 'rgba(153, 170, 255, 0.9)');
  gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.7)');
  var dailyClick = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
       	data: values,
        borderColor: '#002BFC',
        pointBorderColor: '#002BFC',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,
        pointRadius: 2,
        fill: true,
        backgroundColor: gradientFill,
        borderWidth: 1
      }]
    },
    options: {
        animation: {
            easing: "easeInOutBack"
        },
        plugins:{
            legend:false
        },
        responsive: true,
        scales: {
          y:{
            fontColor: "#ffffff",
            fontStyle: "normal",
            beginAtZero: true,
            maxTicksLimit: 5,
            padding: 30,
            grid:{
              borderWidth:0,
            },
            ticks: {
              stepSize: 1000,
              callback: function(value) {
                 var ranges = [
                    { divider: 1e6, suffix: 'M' },
                    { divider: 1e3, suffix: 'k' }
                 ];
                 function formatNumber(n) {
                    for (var i = 0; i < ranges.length; i++) {
                       if (n >= ranges[i].divider) {
                          return (n / ranges[i].divider).toString() + ranges[i].suffix;
                       }
                    }
                    return n;
                 }
                 return '' + formatNumber(value);
              }
           }
          },
          x:{
              padding: 10,
              fontColor: "#ffffff",
              fontStyle: "normal",
              grid: {
                display:false
              }
          }        
        }
      }
  });

	function validateAll() {
		if (jQuery("#customRange1").prop("checked")==true) {
			jQuery("#date_range_form").submit();
		}

		if (jQuery("#customRange2").prop("checked")==true) {
      if(document.getElementById("from_date").value == "" || document.getElementById("to_date").value == "") {
        document.getElementById("errorMessage").classList.remove("hidden");
      } else {
        document.getElementById("errorMessage").classList.add("hidden");
        jQuery("#date_range_form").submit();
      }
		}

    user_tracking_data('click', 'null','shopping_campaigns_page','submit');
  }

  jQuery(".input-daterange input").each(function() {
  	jQuery(this).datepicker({
      todayHighlight: true,
      autoclose: true,
      defaultViewDate: new Date(),
      endDate: new Date(),
      format: "yyyy-mm-dd"
  	}).on("changeDate", changeStartDate);
  });

	function changeStartDate() {
  	var from_date = "";
  	var to_date = "";

  	from_date = jQuery("#from_date").val();
  	to_date = jQuery("#to_date").val();

  	jQuery("#from_date").datepicker("destroy").datepicker({
      todayHighlight: true,
      autoclose: true,
      defaultViewDate: new Date(),
      endDate: to_date == "" ? new Date() : to_date,
     	format: "yyyy-mm-dd"
  	});

  	jQuery("#to_date").datepicker("destroy").datepicker({
      todayHighlight: true,
      autoclose: true,
      defaultViewDate: new Date(),
      endDate: new Date(),
      startDate: from_date == "" ? "" : from_date,
     	format: "yyyy-mm-dd"
  	});
 	}

  function date_range_select() {
    document.getElementById("customRange1").checked = true;
  }

  function deleteCampaign(merchantId, customerId, campaignId, currentRow) {
		var confirm = window.confirm("Are you sure you want to delete campaign?");
		var campaign_name = jQuery("#campaign_name_"+currentRow).val();
		if(confirm) {
			jQuery("#feed-spinner").css("display", "block");
	  	jQuery.post(
	    	tvc_ajax_url,
		    {
	        action: "tvcajax-delete-campaign",
	        merchantId: merchantId,
	        customerId: customerId,
	        campaignId: campaignId,
          campaignDeleteNonce : "<?php echo wp_create_nonce('tvcajax_delete_campaign-nonce'); ?>"
		    },
		    function( response ) {
		    	jQuery("#feed-spinner").css("display", "none");
          //console.log(response);
          var rsp = JSON.parse(response)
          if (rsp.status == "success") {
            var message = campaign_name + " is deleted successfully";
            alert(message);
      			window.location.href = "<?php echo esc_url_raw($this->site_url.'shopping_campaigns_page');?>";
      		} else {
      			var message = rsp.message;
            alert(message);
      		}
		    }
      );
		}
	}

	function updateCampaignStatus(merchantId, customerId, campaignId, budget, budgetId, currentRow) {     
    var campaign_status = jQuery("#customSwitch"+currentRow).prop("checked");
    var campaign_name = jQuery("#campaign_name_"+currentRow).val();
   	jQuery("#feed-spinner").css("display", "block");
    jQuery.post(
      tvc_ajax_url,
      {
        action: "tvcajax-update-campaign-status",
        merchantId: merchantId,
        customerId: customerId,
        campaignId: campaignId,
        campaignName: campaign_name,
        budget: budget,
        budgetId: budgetId,
        campaignStatusNonce : "<?php echo wp_create_nonce('tvcajax-update-campaign-status-nonce'); ?>",
        status: campaign_status == true ? 2 : 3
      },
      function( response ) {
      	jQuery("#feed-spinner").css("display", "none");
        //console.log(response);
        var rsp = JSON.parse(response)
      	if(rsp.status == "success"){
          var message = campaign_name + " status updated successfully";
          alert(message);
  		  }else{
          var message = rsp.message;
          alert(message);
  		  }
      }
    );
	}
</script>
  <?php
  }
}
?>