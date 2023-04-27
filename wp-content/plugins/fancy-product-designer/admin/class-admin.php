<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin') ) {

	class FPD_Admin {

		public static $ajax_nonce;
		public static $admin_notice = null;

		public function __construct() {

			require_once( FPD_PLUGIN_ADMIN_DIR.'/fpd-admin-functions.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-modal.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-template.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-manage-products.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-designs.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR.'/class-admin-ajax.php');
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-scripts-styles.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR . '/class-admin-menus.php' );
			require_once( FPD_PLUGIN_ADMIN_DIR.'/class-admin-ui-layout-composer.php' );


			add_action( 'admin_init', array( &$this, 'init_admin' ) );
			add_action( 'add_meta_boxes', array( &$this, 'add_custom_box' ) );
			add_action( 'save_post', array( &$this,'update_custom_meta_fields' ) );
			add_action( 'admin_notices',  array( &$this, 'display_admin_notices' ) );
			add_filter( 'upload_mimes', array( &$this, 'allow_svg_upload') );

			add_action( 'woocommerce_duplicate_product', array( &$this, 'duplicate_fancy_product' ), 10, 2 );
			add_action( 'admin_footer-post.php', array( &$this, 'add_modal' ) );
			add_action( 'admin_footer-post-new.php', array( &$this, 'add_modal' ) );

		}

		public function init_admin() {

			//FPD_Admin_Ajax::export_product();

			self::$ajax_nonce = wp_create_nonce( 'fpd_ajax_nonce' );

			//add capability to administrator
			$role = get_role( 'administrator' );
			$role->add_cap( Fancy_Product_designer::CAPABILITY );

			if ( get_option('fpd_plugin_activated', false) ) {

				delete_option('fpd_plugin_activated');
				wp_redirect( esc_url_raw( admin_url('admin.php?page=fancy_product_designer&info=activated') ) );
				exit;

		    }

		    //delete sharing images
			if( intval(fpd_get_option('fpd_sharing_cache_days')) !== 0 && get_transient('fpd_check_shares_dir') === false ) {

				$cache_days_in_sec = intval(fpd_get_option('fpd_sharing_cache_days')) * DAY_IN_SECONDS;
			    $share_dir = FPD_WP_CONTENT_DIR . '/uploads/fpd_shares/';
			    $files_in_share_dir = glob($share_dir.'*');

			    if( is_array($files_in_share_dir) ) {

				     $dirs = array_filter($files_in_share_dir, 'is_dir');

				    foreach($dirs as $dir) {
					    $time = strtotime(basename($dir)); //folder date in seconds
					    $seconds = time() - $time; //past seconds
					    if($seconds > $cache_days_in_sec) {
						    $this->delete_files($dir);
					    }
				    }

			    }

			    set_transient('fpd_check_shares_dir', 'yes', DAY_IN_SECONDS);

			}

			//ui&layout composer actions
			if( isset($_POST['fpd_selected_layout']) ) {

				check_admin_referer( 'fpd_save_layout' );

				$selected_layout_id =  sanitize_key($_POST['fpd_selected_layout']);

				if( $_POST['fpd_method'] == 'save' || $_POST['fpd_method'] == 'save-new' ) {

					$saved_layout = json_decode(stripslashes($_POST['fpd_ui_layout']));
					$primary_color = @$saved_layout->css_colors && @$saved_layout->css_colors->primary_color ? $saved_layout->css_colors->primary_color : '#000000';
					$secondary_color = @$saved_layout->css_colors && @$saved_layout->css_colors->secondary_color ? $saved_layout->css_colors->secondary_color : '#27ae60';
					$css_result = FPD_UI_Layout_Composer::parse_css('@primaryColor: '.$primary_color.'; @secondaryColor: '.$secondary_color.';');

					if( is_array($css_result) ) {//error while parsing

						FPD_Admin::set_notice( $css_result );

					}
					else { //successful parsing

						FPD_Admin::set_notice( array(
							'message' => __('Layout saved.', 'radykal'),
							'type'    => 'updated'
						));

						$saved_layout->css = $css_result;
						update_option( 'fpd_ui_layout_'.$selected_layout_id, addslashes(json_encode($saved_layout)) );

					}

				}
				else if( $_POST['fpd_method'] == 'delete' ) {

					delete_option( 'fpd_ui_layout_'.$selected_layout_id );
					$selected_layout_id = 'default';

				}
				else if( $_POST['fpd_method'] == 'reset' ) {

					FPD_UI_Layout_Composer::reset_to_default($selected_layout_id);

				}

			}

		}

		//add meta box in the post and page
		public function add_custom_box() {

			add_meta_box(
				'fpd-meta-box',
				__('Fancy Product Designer', 'radykal'),
				array( &$this, 'output_meta_box'),
				'post',
				'side'
			);

			add_meta_box(
				'fpd-meta-box',
				__('Fancy Product Designer', 'radykal'),
				array( &$this, 'output_meta_box'),
				'page',
				'side'
			);


			$custom_post_types = get_post_types( array(
				'public' => true,
				'_builtin' => false
			));

			foreach($custom_post_types as $custom_post_type) {

				add_meta_box(
					'fpd-meta-box',
					__('Fancy Product Designer', 'radykal'),
					array( &$this, 'output_meta_box'),
					$custom_post_type,
					'side'
				);

			}

		}

		public function output_meta_box() {

			global $wpdb, $post;

			$custom_fields = get_post_custom($post->ID);

			require_once(FPD_PLUGIN_ADMIN_DIR.'/views/html-admin-meta-box.php');

		}

		public function update_custom_meta_fields( $post_id )	{

			$post_type = get_post_type( $post_id );

			//disable autosave,so custom fields will not be empty
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		        return $post_id;

			//prevents custom metas to get deleted when saving via quick edit
		    if ( $post_type === 'product' && isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce'))
				return $post_id;

			if ($post_type === 'product' &&  isset($_POST['bulk_edit']))
				return $post_id;

			if(isset($_POST["fpd_product_settings"]))
				update_post_meta( $post_id, 'fpd_product_settings', htmlentities($_POST['fpd_product_settings']) );

			if(isset($_POST["fpd_source_type"]))
				update_post_meta( $post_id, 'fpd_source_type', $_POST['fpd_source_type'] );

			update_post_meta( $post_id, 'fpd_products', isset($_POST["fpd_products"]) ? $_POST['fpd_products'] : '' );
			update_post_meta( $post_id, 'fpd_product_categories', isset($_POST["fpd_product_categories"]) ? $_POST['fpd_product_categories'] : '' );

		}

		public function display_admin_notices() {

			global $woocommerce;

			if( FPD_Admin::$admin_notice ) {

				?>
				<div class="<?php echo FPD_Admin::$admin_notice['type']; ?>">
			        <p><strong><?php echo FPD_Admin::$admin_notice['message']; ?></strong></p>
			    </div>

			    <?php

				FPD_Admin::unset_notice();

			}

			if( function_exists('get_woocommerce_currency') && version_compare($woocommerce->version, '2.1', '<') ): ?>
			<div class="error">
		        <p><?php _e( 'Please update WooCommerce to the latest version! Fancy Product Designer only works with version 2.1 or newer.', 'radykal' ); ?></p>
		    </div>
			<?php endif;

			if( !extension_loaded('gd') || !function_exists('gd_info') ): ?>
			<div class="error">
		        <p><?php _e( 'GD library is not installed on your web server. If you do not know how to install GD library, please ask your server provider!', 'radykal' ); ?></p>
		    </div>
			<?php endif;

			if( version_compare(phpversion(), '5.4', '<') ): ?>
			<div class="error">
		        <p><?php _e( 'Fancy Product Designer requires a minimum PHP version of 5.4!', 'radykal' ); ?></p>
		    </div>
			<?php endif;

		}

		public function allow_svg_upload( $svg_mime ) {

			$svg_mime['svg'] = 'image/svg+xml';
			return $svg_mime;

		}

		//duplicate fancy products, all views will be available in the duplicated product
		public function duplicate_fancy_product( $new_id, $post ) {

			$custom_fields = get_post_custom($post->ID);

			if(isset($custom_fields["fpd_product_categories"]))
				update_post_meta( $new_id, 'fpd_product_categories', maybe_unserialize($custom_fields['fpd_product_categories'][0]) );

			if(isset($custom_fields["fpd_products"]))
				update_post_meta( $new_id, 'fpd_products', maybe_unserialize($custom_fields['fpd_products'][0]) );

			if(isset($custom_fields["fpd_source_type"]))
				update_post_meta( $new_id, 'fpd_source_type', $custom_fields['fpd_source_type'][0] );

			if(isset($custom_fields["fpd_product_settings"]))
				update_post_meta( $new_id, 'fpd_product_settings', $custom_fields['fpd_product_settings'][0] );

		}

		public function add_modal() {

			global $post;

			$screen = get_current_screen();
			if($screen->post_type !== 'shop_order')
				require_once(FPD_PLUGIN_ADMIN_DIR.'/modals/modal-individual-product-settings.php');

		}

		public static function set_notice( $notice ) {

			FPD_Admin::$admin_notice = $notice;

		}

		public static function unset_notice() {

			FPD_Admin::$admin_notice = null;

		}

		private function delete_files($dir) {

		    if (!file_exists($dir)) {
		        return true;
		    }

		    if (!is_dir($dir)) {
		        return unlink($dir);
		    }

		    foreach (scandir($dir) as $item) {

		        if ($item == '.' || $item == '..') {
		            continue;
		        }

		        if (!$this->delete_files($dir . DIRECTORY_SEPARATOR . $item)) {
		            return false;
		        }

		    }

		    return rmdir($dir);

		}
	}
}

new FPD_Admin();

?>