<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://multidots.com/
 * @since      1.0.0
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Shipping_Display_Mode
 * @subpackage Woo_Shipping_Display_Mode/admin
 * @author     Multidots <inquiry@multidots.in>
 */
class Woo_Shipping_Display_Mode_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Shipping_Display_Mode_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Shipping_Display_Mode_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
/*		wp_dequeue_style( 'wp-jquery-ui-dialog' ); 
		wp_dequeue_style('jquery-ui-style-css');
		wp_deregister_style('jquery-ui-style-css');*/
		if( !empty( $_GET['page']) && $_GET['page'] === 'woocommerce-shipping-display-mode' ){
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
		}
		wp_enqueue_style( 'wp-pointer' );
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-shipping-display-mode-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0	
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Shipping_Display_Mode_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Shipping_Display_Mode_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		//wp_deregister_script( 'jquery-ui' );
		
  	
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-shipping-display-mode-admin.js', array( 'jquery','jquery-ui-dialog'), $this->version, false );
		wp_enqueue_script( 'wp-pointer' );

	}
	
	public function woo_shipping_admin_init_own(){
		require_once 'partials/woo-shipping-display-mode-admin-display.php';
		$admin = new WC_Settings_Shipping_Display_Mode_Methods();
    }
    
    public function wp_add_plugin_userfn() {
    	$email_id= $_POST['email_id'];
    	$log_url = $_SERVER['HTTP_HOST'];
    	$cur_date = date('Y-m-d');
    	$url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/wp-add-plugin-users.php';
    	$response = wp_remote_post( $url, array('method' => 'POST',
    	'timeout' => 45,
    	'redirection' => 5,
    	'httpversion' => '1.0',
    	'blocking' => true,
    	'headers' => array(),
    	'body' => array('user'=>array('plugin_id' => '14','user_email'=>$email_id,'plugin_site' => $log_url,'status' => 1,'activation_date'=>$cur_date)),
    	'cookies' => array()));
		update_option('wsdm_plugin_notice_shown', 'true');
    }
    
    public function hide_subscribe_wsdmfn() {
    	$email_id= $_POST['email_id'];
    	update_option('wsdm_plugin_notice_shown', 'true');
    } 
    
    // Function For Welcome page to plugin 
    
    public function welcome_shipping_display_mode_screen_do_activation_redirect (){ 
    	
    	if (!get_transient('_welcome_screen_shipping_display_mode_activation_redirect_data')) {
			return;
		}
		
		// Delete the redirect transient
		delete_transient('_welcome_screen_shipping_display_mode_activation_redirect_data');

		// if activating from network, or bulk
		if (is_network_admin() || isset($_GET['activate-multi'])) {
			return;
		}
		// Redirect to extra cost welcome  page
		wp_safe_redirect(add_query_arg(array('page' => 'woocommerce-shipping-display-mode&tab=about'), admin_url('index.php')));
    } 
    
    
    public function welcome_pages_screen_shipping_display_mode() {
		add_dashboard_page(
		'Woocommerce-Shipping-Method-Display-Style Dashboard', 'Woocommerce Shipping Method Display Style', 'read', 'woocommerce-shipping-display-mode', array(&$this, 'welcome_screen_content_shipping_display_mode')
		);
	}
	
	public function welcome_screen_content_shipping_display_mode() {
        ?>
        
         <div class="wrap about-wrap">
            <h1 style="font-size: 2.1em;"><?php printf(__('Welcome to Woocommerce Shipping Method Display Style', 'woo-shipping-display-mode')); ?></h1>

            <div class="about-text woocommerce-about-text">
        <?php
        $message = '';
        printf(__('%s WooCommerce shipping method display style plugin provides a configuration to display shipping methods as Radio button or select box on the checkout page.', 'woo-shipping-display-mode'), $message, $this->version);
        ?>
                <img class="version_logo_img" src="<?php echo plugin_dir_url(__FILE__) . 'images/woo-shipping-display-mode.png'; ?>">
            </div>

        <?php
        $setting_tabs_wc = apply_filters('woo_shipping_display_mode_setting_tab', array("about" => "Overview", "other_plugins" => "Checkout our other plugins"));
        $current_tab_wc = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';
        $aboutpage = isset($_GET['page'])
        ?>
            <h2 id="woo-extra-cost-tab-wrapper" class="nav-tab-wrapper">
            <?php
            foreach ($setting_tabs_wc as $name => $label)
            echo '<a  href="' . home_url('wp-admin/index.php?page=woocommerce-shipping-display-mode&tab=' . $name) . '" class="nav-tab ' . ( $current_tab_wc == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
            ?>
            </h2>

                <?php
                foreach ($setting_tabs_wc as $setting_tabkey_wc => $setting_tabvalue) {
                	switch ($setting_tabkey_wc) {
                		case $current_tab_wc:
                			do_action('woocommerce_shipping_display_mode_' . $current_tab_wc);
                			break;
                	}
                }
                ?>
            <hr />
            <div class="return-to-dashboard">
                <a href="<?php echo home_url('/wp-admin/admin.php?page=wc-settings&tab=shipping_mode'); ?>"><?php _e('Go to Woocommerce Shipping Method Display Style Settings', 'woo-shipping-display-mode'); ?></a>
            </div>
        </div>
        
       
	<?php }
    
	/**
     * Extra flate rate overview welcome page content function
     *
     */
	public function woocommerce_shipping_display_mode_about() {
		//do_action('my_own');
		$current_user = wp_get_current_user();

    	?>
        <div class="changelog">
            </br>
           	<style type="text/css">
				p.shipping_display_mode_overview {max-width: 100% !important;margin-left: auto;margin-right: auto;font-size: 15px;line-height: 1.5;}
			</style>  
            <div class="changelog about-integrations">
                <div class="wc-feature feature-section col three-col">
                    <div>
                        <p class="shipping_display_mode_overview"><?php _e('WooCommerce Shipping Method Display Style plugin provides you an interface in WooCommerce setting section from admin side. As you know WooCommerce has removed choose shipping display mode option from 2.5.1 version. So, this plugin provides these features, admin can choose shipping display modes like radio option or select option for shipping display mode.', 'woo-checkout-fields'); ?></p>
                        
                        <p class="shipping_display_mode_overview"><?php _e('This Plugin is useful when you are using 2.5.2 or higher version of Woocommerce and you have to use more than 20 or 30 or more then that Shipping Method at that time you do not have forced to display all shipping method only using radio button, but you have also chosen a select option for display shipping mode and by choosing select option shipping method is displayed in the drop down box so you can avoid lengthy listing of shipping method.', 'woo-checkout-fields'); ?></p>
                        
                        <p class="shipping_display_mode_overview">We have added <strong> Select shipping mode</strong> option for default shipping method.</p>  
                        
                    </div>
                    
                </div>
            </div>
        </div>
        
        <?php	
        	global $wpdb;
        	$current_user =  wp_get_current_user();
		  if (!get_option('wsdm_plugin_notice_shown')) {
   			 echo '<div id="wsdm_dialog" title="Basic dialog"> <p> Subscribe for latest plugin update and get notified when we update our plugin and launch new products for free! </p> <p><input type="text" id="txt_user_sub_wsdm" class="regular-text" name="txt_user_sub_wsdm" value="'.$current_user->user_email.'"></p></div>';
   			
		?>
             
        <script type="text/javascript">

        jQuery( document ).ready(function() {
        	jQuery( "#wsdm_dialog" ).dialog({
        		modal: true, title: 'Subscribe Now', zIndex: 10000, autoOpen: true,
        		width: '500', resizable: false,
        		position: {my: "center", at:"center", of: window },
        		dialogClass: 'dialogButtons',
        		buttons: {
        			Yes: function () {
        				
        				var email_id = jQuery('#txt_user_sub_wsdm').val();

        				var data = {
        				'action': 'add_plugin_user_wsdm',
        				'email_id': email_id
        				};

        				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        				jQuery.post(ajaxurl, data, function(response) {
        					jQuery('#wsdm_dialog').html('<h2>You have been successfully subscribed');
        					jQuery(".ui-dialog-buttonpane").remove();
        				});

        				
        			},
        			No: function () {
        					var email_id = jQuery('#txt_user_sub_wsdm').val();

        				var data = {
        				'action': 'hide_subscribe_wsdm',
        				'email_id': email_id
        				};

        				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        				jQuery.post(ajaxurl, data, function(response) {
        					        					
        				});
        				
        				jQuery(this).dialog("close");
        				
        			}
        		},
        		close: function (event, ui) {
        			jQuery(this).remove();
        		}
        	});
        	jQuery("div.dialogButtons .ui-dialog-buttonset button").addClass("button-primary woocommerce-save-button");
        	jQuery("div.dialogButtons .ui-dialog-buttonpane .ui-button").css("width","80px");
        	jQuery("div.dialogButtons .ui-dialog-buttonpane .ui-button").css("margin-right","14px");
        	jQuery("div.dialogButtons .ui-dialog-buttonset button").removeClass("ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only");
        	
        });
        </script>
        <?php
        
        } 
        
        ?>
        
        
	<?php  }
 
	
	public function woocommerce_shipping_display_mode_other_plugins() { 
		global $wpdb;
         $url = 'http://www.multidots.com/store/wp-content/themes/business-hub-child/API/checkout_other_plugin.php';
    	 $response = wp_remote_post( $url, array('method' => 'POST',
    	'timeout' => 45,
    	'redirection' => 5,
    	'httpversion' => '1.0',
    	'blocking' => true,
    	'headers' => array(),
    	'body' => array('plugin' => 'advance-flat-rate-shipping-method-for-woocommerce'),
    	'cookies' => array()));
    	
    	$response_new = array();
    	$response_new = json_decode($response['body']);
		$get_other_plugin = maybe_unserialize($response_new);
		
		$paid_arr = array();
		?>

        <div class="plug-containter">
        	<div class="paid_plugin">
        	<h3>Paid Plugins</h3>
	        	<?php foreach ($get_other_plugin as $key=>$val) { 
	        		if ($val['plugindesc'] =='paid') {?>
	        			
	        			
	        		   <div class="contain-section">
	                <div class="contain-img"><img src="<?php echo $val['pluginimage']; ?>"></div>
	                <div class="contain-title"><a target="_blank" href="<?php echo $val['pluginurl'];?>"><?php echo $key;?></a></div>
	            </div>	
	        			
	        			
	        		<?php }else {
	        			
	        			$paid_arry[$key]['plugindesc']= $val['plugindesc'];
	        			$paid_arry[$key]['pluginimage']= $val['pluginimage'];
	        			$paid_arry[$key]['pluginurl']= $val['pluginurl'];
	        			$paid_arry[$key]['pluginname']= $val['pluginname'];
	        		
	        	?>
	        	
	         
	            <?php } }?>
           </div>
           <?php if (isset($paid_arry) && !empty($paid_arry)) {?>
           <div class="free_plugin">
           	<h3>Free Plugins</h3>
                <?php foreach ($paid_arry as $key=>$val) { ?>  	
	            <div class="contain-section">
	                <div class="contain-img"><img src="<?php echo $val['pluginimage']; ?>"></div>
	                <div class="contain-title"><a target="_blank" href="<?php echo $val['pluginurl'];?>"><?php echo $key;?></a></div>
	            </div>
	            <?php } }?>
           </div>
          
        </div>

    <?php
	} 
	
	public function custom_shipping_display_mode_pointers_footer() {
	    $admin_pointers = custom_shipping_display_mode_admin_pointers();
	    ?>
	    <script type="text/javascript">
	        /* <![CDATA[ */
	        ( function($) {
	            <?php
	            foreach ( $admin_pointers as $pointer => $array ) {
	               if ( $array['active'] ) {
	                  ?>
	            $( '<?php echo $array['anchor_id']; ?>' ).pointer( {
	                content: '<?php echo $array['content']; ?>',
	                position: {
	                    edge: '<?php echo $array['edge']; ?>',
	                    align: '<?php echo $array['align']; ?>'
	                },
	                close: function() {
	                    $.post( ajaxurl, {
	                        pointer: '<?php echo $pointer; ?>',
	                        action: 'dismiss-wp-pointer'
	                    } );
	                }
	            } ).pointer( 'open' );
	            <?php
	         }
	      }
	      ?>
	        } )(jQuery);
	        /* ]]> */
	    </script>
	<?php
	}
	
	
	/**
	 * remove menu in deshboard
	 * 
	 */
	
	public function adjust_the_wp_menu() {
		remove_submenu_page('index.php', 'woocommerce-shipping-display-mode');
	}
	
}

function custom_shipping_display_mode_admin_pointers() {
    
	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
    $version = '1_0'; // replace all periods in 1.0 with an underscore
    $prefix = 'custom_shipping_display_mode_pointers' . $version . '_';

    $new_pointer_content = '<h3>' . __( 'Welcome to Woocommerce Shipping Method Display Style' ) . '</h3>';
    $new_pointer_content .= '<p>' . __( 'WooCommerce shipping method display style plugin provides a configuration to display shipping methods as Radio button or select box on the checkout page.' ) . '</p>';

    return array(
        $prefix . 'shipping_display_mode_notice_view' => array(
            'content' => $new_pointer_content,
            'anchor_id' => '#toplevel_page_woocommerce',
            'edge' => 'left',
            'align' => 'left',
            'active' => ( ! in_array( $prefix . 'shipping_display_mode_notice_view', $dismissed ) )
        )
    );

}