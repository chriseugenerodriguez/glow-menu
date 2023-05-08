<?php
/**
 * @since      4.0.2
 * Description: Conversios Onboarding page, It's call while active the plugin
 */
if ( ! class_exists( 'Conversios_Header' ) ) {
	class Conversios_Header extends TVC_Admin_Helper{
		protected $site_url;
		protected $conversios_site_url;
		protected $subscription_data;		
		public function __construct( ){
			$this->site_url = "admin.php?page=";
			$this->conversios_site_url = $this->get_conversios_site_url();
			$this->subscription_data = $this->get_user_subscription_data();			
			add_action('add_conversios_header',array($this, 'before_start_header'));
			add_action('add_conversios_header',array($this, 'header_notices'));
			add_action('add_conversios_header',array($this, 'conversios_header'));
			add_action('add_conversios_header',array($this, 'header_menu'));
			add_action('add_conversios_header',array($this, 'custom_feedback_form'));
		}	
		
		/**
     * before start header section
     *
     * @since    4.1.4
     */
		public function before_start_header(){
			?>
			<div class="bodyrightpart conversios-body-part">
			<?php
		}
		/**
     * header notices section
     *
     * @since    4.1.4
     */
		public function header_notices(){
			if ( !is_network_admin() && !is_plugin_active_for_network( 'woocommerce/woocommerce.php') && !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )){ 
				?>
				<div class="errormsgtopbx claimalert">
	      	<div class="errmscntbx">
	          <div class="errmsglft">
	             <span class="errmsgicon"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/error-white-icon.png'); ?>" alt="error" /></span>
	          </div>
	          <div class="erralertrigt">
	          	<h6><?php echo esc_html_e("Hey,","enhanced-e-commerce-for-woocommerce-store"); ?></h6>
	            <p><?php echo esc_html_e("It seems WooCommerce plugin is not active on your wp-admin. Conversios.io - Google Analytics and Google Shopping plugin required.","enhanced-e-commerce-for-woocommerce-store"); ?> </p>
	          </div>
	       </div>
	  		</div>
				<?php
			}else{				
					?>
					<!--- Promotion box start -->
				  <div class="promobandtop">
				    <div class="container-fluid">
				      <div class="row">
				          <div class="promoleft">
			              <div class="promobandmsg">
			                <?php esc_html_e("Level up your ecommerce business by using your own Google Tag Manager to integrate complete GA4, FB, Google Ads, Tiktok and more..!!!","enhanced-e-commerce-for-woocommerce-store"); ?>
			              </div>
				          </div>
				          <div class="promoright">
		                <div class="prmoupgrdbtn">
		                    <a target="_blank" href="<?php echo $this->get_conv_pro_link("top_bar", "", "linkonly"); ?>" class="upgradebtn"><?php esc_html_e("Upgrade","enhanced-e-commerce-for-woocommerce-store"); ?></a>
		                </div>
				          </div>
				      </div>
				    </div>
				  </div>
				  <!--- Promotion box end -->
					<?php				
				echo esc_attr($this->call_tvc_site_verified_and_domain_claim());
			}	
		}
		/**
     * header section
     *
     * @since    4.1.4
     */
		public function conversios_header(){
			$plan_name = esc_html__("Free Plan","enhanced-e-commerce-for-woocommerce-store");
			?>
			<!-- header start -->
		  <header class="header">
	      <div class="hedertop">
          <div class="row align-items-center">
            <div class="hdrtpleft">
              <div class="brandlogo">
                  <a target="_blank" href="<?php echo esc_url_raw($this->conversios_site_url); ?>"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/logo.png'); ?>" alt="" /></a>
              </div>
              <div class="hdrcntcbx">
                  <?php printf("%s <span><a href=\"mailto:info@conversios.io\">info@conversios.io</a></span>",esc_html_e("For any query, contact us on","conversios")); ?>
              </div>
            </div>
            <div class="hdrtpright">
               <div class="hustleplanbtn">
                  <a href="<?php echo esc_url_raw($this->site_url.'conversios-account'); ?>"><button class="cvrs-btn greenbtn"><?php echo esc_attr($plan_name); ?></button></a>
              </div>
            </div>
            <div class="hdrcntcbx mblhdrcntcbx">
                <?php printf("%s <span><a href=\"tel:+1 (415) 968-6313\">+1 (415) 968-6313</a></span>",esc_html_e("For any query, contact us at","enhanced-e-commerce-for-woocommerce-store")); ?>
            </div>
          </div>
	      </div>
		  </header>
		  <!-- header end -->
			<?php
		}

		/* add active tab class */
	  protected function is_active_menu($page=""){
	      if($page!="" && isset($_GET['page']) && sanitize_text_field($_GET['page']) == $page){
	          return "active";
	      }
	      return ;
	  }
	  public function conversios_menu_list(){
	  	//slug => arra();
	  	$conversios_menu_list = array();
	  	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
             require_once( ABSPATH . '/wp-admin/includes/woocommerce.php' );
        }
        if ( is_plugin_active_for_network( 'woocommerce/woocommerce.php') || in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
		  	$conversios_menu_list = array(
		  		'conversios' => array(
		  			'title'=>'Dashboard',
		  			'icon'=>esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/conversios-menu.png"),
		  			'acitve_icon'=>esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/active-conversios-menu.png")
		  			),
		  		'conversios-google-analytics'=>array('title'=>esc_html__('Pixel Settings','enhanced-e-commerce-for-woocommerce-store')),
		  		'conversios-google-shopping-feed'=>array('title'=>esc_html__('Product Feed','enhanced-e-commerce-for-woocommerce-store'), 'slug' =>'conversios-google-shopping-feed&tab=sync_product_page'),
		  		'conversios-pmax'=>array('title'=>esc_html__('Performance Max','enhanced-e-commerce-for-woocommerce-store'),
		  			'icon'=>esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/conversios-google-ads-menu.png"),
		  			'acitve_icon'=>esc_url_raw(ENHANCAD_PLUGIN_URL."/admin/images/active-conversios-google-ads-menu.png")
		  		),
		  		 );
		  }	  	
	  		$conversios_menu_list['conversios-pricings'] = array('title'=>esc_html__('Free Vs Pro','enhanced-e-commerce-for-woocommerce-store'),'icon'=>'');
	  	  	return apply_filters('conversios_menu_list', $conversios_menu_list, $conversios_menu_list);
	  }
		/**
     * header menu section
     *
     * @since    4.1.4
     */
		public function header_menu(){
			$menu_list = $this->conversios_menu_list();
			if(!empty($menu_list)){
				?>
				<div class="navinfowrap">
      	  <div class="navinfotopnav">
            <ul>
						<?php
						foreach ($menu_list as $key => $value) {
							if(isset($value['title']) && $value['title']){
								$is_active = $this->is_active_menu($key);
								$icon = "";
								if(!isset($value['icon']) && !isset($value['acitve_icon'])){
									$icon = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/'.esc_attr($key).'-menu.png');					
									if($is_active == 'active'){
										$icon = esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/'.esc_attr($is_active).'-'.esc_attr($key).'-menu.png');
									}
								}else{
									$icon = (isset($value['icon']))?$value['icon']:((isset($value['acitve_icon']))?esc_attr($value['acitve_icon']):"");
									if($is_active == 'active' && isset($value['acitve_icon'])){
										$icon =$value['acitve_icon'];
									}
								}
								$menu_url = $this->site_url.$key;
								if(isset($value['slug']) && $value['slug'] ){
									$menu_url = $this->site_url.$value['slug'];
								}
								if($key == "conversios-google-shopping-feed"){
									?>
								<li class="<?php echo esc_attr($is_active);  ?>">
									<div class="con-dropdown">
			              <a href="<?php echo esc_url_raw($menu_url); ?>" class="dropbtn">
			              	<?php if($icon != ""){?>
			                <span class="navinfoicon"><img src="<?php echo esc_url_raw($icon); ?>" /></span>
			              	<?php } ?>
			                <span class="navinfonavtext"><?php echo esc_attr($value['title']); ?></span>
			              </a>
		              	<div class="con-dropdown-content">
	              		  <a href="<?php echo esc_url_raw("admin.php?page=conversios-google-shopping-feed&tab=sync_product_page"); ?>">Product Feed</a>
	              			<a href="<?php echo esc_url_raw("admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page"); ?>">Shopping Configuration</a>
	              		</div>		              	
		              </div>
			          </li>
								<?php	
								}else if($key == "conversios-google-analytics"){
									?>
								<li class="<?php echo esc_attr($is_active);  ?>">
		              <a href="<?php echo esc_url_raw($menu_url); ?>">
		              	<?php if($icon != ""){?>
		                <span class="navinfoicon"><img src="<?php echo esc_url_raw($icon); ?>" /></span>
		              <?php } ?>
		                <span class="navinfonavtext"><?php echo esc_attr($value['title']); ?></span><span class="con_new_features">New</span>
		              </a>
			          </li>
								<?php	
								}else if($key == "conversios-pricings"){
									?>
								<li class="<?php echo esc_attr($is_active);  ?>">
		              <a href="<?php echo esc_url_raw($menu_url); ?>">
		              	<?php if($icon != ""){?>
		                <span class="navinfoicon"><img src="<?php echo esc_url_raw($icon); ?>" /></span>
		              <?php } ?>
		                <span class="navinfonavtext"><?php echo esc_attr($value['title']); ?></span><span class="con_new_features">Festive Offer</span>
		              </a>
			          </li>
								<?php	
								}else{
									?>
								<li class="<?php echo esc_attr($is_active);  ?>">
		              <a href="<?php echo esc_url_raw($menu_url); ?>">
		              	<?php if($icon != ""){?>
		                <span class="navinfoicon"><img src="<?php echo esc_url_raw($icon); ?>" /></span>
		              <?php } ?>
		                <span class="navinfonavtext"><?php echo esc_attr($value['title']); ?></span>
		              </a>
			          </li>
								<?php	
								}
								
							}
						}?>
						</ul>
						<div class="rate_us">
							<a href="https://wordpress.org/support/plugin/enhanced-e-commerce-for-woocommerce-store/reviews/?rate=5#rate-response" target="_blank"><span>Rate Us!</span><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/rate-us.png'); ?>" /></a>
						</div>
					</div>
				</div>	
				<?php
			}			
		}
		/**
		* custom_feedback_form
		*
		* @since    4.6.4
		*/
		public function custom_feedback_form(){
			if(isset($_GET['page']) && sanitize_text_field($_GET['page']) === "conversios"){ 
				$this->TVC_Admin_Helper = new TVC_Admin_Helper();
				$customerId = sanitize_text_field($this->TVC_Admin_Helper->get_api_customer_id());
				$subscriptionId =  sanitize_text_field($this->TVC_Admin_Helper->get_subscriptionId());  
				?>
				<div id="feedback-form-wrapper">
				  <div id="feedback_record_btn">
						<button type="button" class="feedback_btn btn-11">
						  <?php esc_html_e("Feedback","enhanced-e-commerce-for-woocommerce-store"); ?>
						</button>
				  </div>
					<div id="feedback_form_modal" class="pp-modal whitepopup">
				    <div class="sycnprdct-ppcnt">
				      <div class="ppwhitebg pp-content upgradsbscrptnpp-cntr" style="max-width: 545px !important;">
				        <div class="ppclsbtn absltpsclsbtn clsbtntrgr">
				          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/close-white.png'); ?>" alt="">
				        </div>
				        <div class="upgradsbscrptnpp-hdr">
				          <h5><?php esc_html_e("Your feedback is valuable","enhanced-e-commerce-for-woocommerce-store"); ?></h5>
				        </div>
				        <div class="ppmodal-body">
				          <form id="customer_feedback_form">
				            <div class="feedback-form-group">
				              <label class="feedback_que_label" for="feedback_question_one"><?php esc_html_e("The Conversios plugin helps me to get more insight about business which helps me to take business decisions? *","enhanced-e-commerce-for-woocommerce-store"); ?></label>
				              <div class="rating-input-wrapper">
				                <label class="feedback_label"><input name="feedback_question_one" type="radio" value="1" /><span class="feedback_options"><?php esc_html_e("Strongly Agree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_one" value="2" /><span class="feedback_options"><?php esc_html_e("Agree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_one" value="3" /><span class="feedback_options"><?php esc_html_e("Disagree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_one" value="4" /><span class="feedback_options"><?php esc_html_e("Strongly Disagree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				              </div>
				            </div>
				            <div class="feedback-form-group">
				              <label class="feedback_que_label" for="feedback_question_two"><?php esc_html_e("The Conversios plugin helps me to simplified the Google Ads and Google Merchant Centre? *","enhanced-e-commerce-for-woocommerce-store"); ?></label>
				              <div class="rating-input-wrapper">
				                <label class="feedback_label"><input type="radio" name="feedback_question_two" value="1" /><span class="feedback_options"><?php esc_html_e("Strongly Agree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_two" value="2" /><span class="feedback_options"><?php esc_html_e("Agree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_two" value="3" /><span class="feedback_options"><?php esc_html_e("Disagree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_two" value="4" /><span class="feedback_options"><?php esc_html_e("Strongly Disagree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				              </div>
				            </div>
				            <div class="feedback-form-group feedback_txtarea_div">
				              <label class="feedback_que_label" for="feedback_question_three"><?php esc_html_e("You are satisfied with the Conversion plugin? *","enhanced-e-commerce-for-woocommerce-store"); ?></label>
				              <div class="rating-input-wrapper">
				                <label class="feedback_label"><input type="radio" name="feedback_question_three" value="1" /><span class="feedback_options"><?php esc_html_e("Strongly Agree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_three" value="2" /><span class="feedback_options"><?php esc_html_e("Agree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_three" value="3" /><span class="feedback_options"><?php esc_html_e("Disagree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				                <label class="feedback_label"><input type="radio" name="feedback_question_three" value="4" /><span class="feedback_options"><?php esc_html_e("Strongly Disagree","enhanced-e-commerce-for-woocommerce-store"); ?></span></label>
				              </div>
				            </div>
				            <div class="feedback-form-group feedback_txtarea_div">
				              <label class="feedback_que_label" for="feedback_description"><?php esc_html_e("How could we make the Conversios plugin better for you?","enhanced-e-commerce-for-woocommerce-store"); ?></label>
				              <textarea class="feedback_txtarea" id="feedback_description" onkeyup="feedback_charcountupdate(this.value)" rows="5" maxlength="300"></textarea><span id="charcount"></span>
				            </div>
				            <button id="submit_wooplugin_feedback" type="submit" class="blueupgrdbtn"><?php esc_html_e("Submit","enhanced-e-commerce-for-woocommerce-store"); ?></button>
				          </form> 
				        </div>              
				      </div>
				    </div>
					</div>
				</div> 
				<script>
					function feedback_charcountupdate(str) {
						let lng = str.length;document.getElementById("charcount").innerHTML ='('+lng+'/300)';
					}
				jQuery(document).ready(function() {
					feedback_charcountupdate(jQuery('#feedback_description').val());
					
					jQuery("#feedback_record_btn").click(function(){
						setTimeout(() => { jQuery("#feedback_form_modal").addClass("showpopup"); }, 500);
					});
					jQuery( "#customer_feedback_form" ).submit(function( event ) {
					  event.preventDefault();
						let val_que_one=jQuery('input[name="feedback_question_one"]:checked').val();
						if(val_que_one == "" || val_que_one == undefined ){ 
							tvc_helper.tvc_alert("error","","Please answer the required questions"); return false;}
						let val_que_two=jQuery('input[name="feedback_question_two"]:checked').val();
						if(val_que_two == "" || val_que_two == undefined ){ return false;}
						let val_que_three=jQuery('input[name="feedback_question_three"]:checked').val();
						if(val_que_three == "" || val_que_three == undefined ){ return false;}
						let feedback_description= jQuery('#feedback_description').val();
						let customer_id="<?php echo $customerId; ?>";
						let subscription_id="<?php echo $subscriptionId; ?>";
					  let formdata = {
					      action: "tvc_call_add_customer_feedback",
					      que_one: val_que_one,   
					      que_two: val_que_two,   
					      que_three: val_que_three,
					      subscription_id: subscription_id,
					      customer_id: customer_id,   
					      feedback_description: feedback_description,
					      conv_customer_feed_nonce_field: "<?php echo wp_create_nonce('conv_customer_feed_nonce_field_save'); ?>"
					    };
					    $.ajax({
					      type: "POST",
					      dataType: "json",
					      url: tvc_ajax_url,
					      data: formdata,
					      beforeSend: function(){
					        //tvc_helper.loaderSection(true);
					        jQuery("#customer_feedback_form").addClass("is_loading");
					      },
					      success: function(response){
					      	//console.log("response",response);
					        if (response?.error === false) {          
					          tvc_helper.tvc_alert("success","",response.message);
					        }else{
					          tvc_helper.tvc_alert("error","",response.message);
					        }
					        jQuery("#customer_feedback_form").removeClass("is_loading");
					        jQuery("#feedback_form_modal").removeClass("showpopup");
					      }
					    });
					  });
					});   
				</script>
			<?php 
			}
		}
		
	}
}
new Conversios_Header();