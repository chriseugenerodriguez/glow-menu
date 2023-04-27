<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Admin_Scripts_Styles') ) {

	class FPD_Admin_Scripts_Styles {

		public function __construct() {

			add_action( 'init', array( &$this, 'register'), 20 );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles_scripts' ), 100 );
			add_action( 'admin_head-fancy-product-designer_page_fpd_ui_layout_composer', array( &$this, 'print_css_string' ), 100 );

		}

		public function register() {

			//register radykal css files
			wp_register_style( 'radykal-admin', plugins_url('/css/radykal-admin.css', __FILE__) );
			wp_register_style( 'radykal-select2', plugins_url('css/select2.min.css', __FILE__), false, '4.0.3' );
			wp_register_style( 'radykal-tagsmanager', plugins_url('/css/tagmanager.css', __FILE__) );
			wp_register_style( 'radykal-tooltipster', plugins_url('/css/tooltipster.css', __FILE__) );
			wp_register_style( 'fpd-admin-icon-font', plugins_url('/css/icon-font.css', __FILE__), false, Fancy_Product_Designer::VERSION );
			wp_register_style( 'fpd-admin', plugins_url('/css/admin.css', __FILE__), array(
				'radykal-tooltipster'
			), Fancy_Product_Designer::VERSION );
			wp_register_style( 'fpd-admin-jquery-ui', "//code.jquery.com/ui/1.9.1/themes/flick/jquery-ui.css", false, '1.9.1' );

			//register radykal js files
			wp_register_script( 'radykal-ace-editor', plugins_url('/js/ace-min/ace.js', __FILE__), array('jquery'), '1.2.6' );
			wp_register_script( 'radykal-select2', plugins_url('/js/select2.min.js', __FILE__), array( 'jquery' ), '4.0.3' );
			wp_register_script( 'radykal-select-sortable', plugins_url('/js/selectSortable.js', __FILE__), array( 'jquery' ), '1.0.0' );
			wp_register_script( 'radykal-tagsmanager', plugins_url('/js/tagmanager.js', __FILE__) );
			wp_register_script( 'radykal-tooltipster', plugins_url('/js/jquery.tooltipster.min.js', __FILE__) );
			wp_register_script( 'radykal-admin', plugins_url('/js/radykal-admin.js', __FILE__) );
			wp_register_script( 'fpd-admin', plugins_url('/js/admin.js', __FILE__), array(
				'jquery',
				'radykal-tooltipster'
			), Fancy_Product_Designer::VERSION );
			wp_register_script( 'fpd-order-viewer', plugins_url('/js/order-viewer.js', __FILE__), array(
			    	'radykal-admin',
					'jquery-fpd',
					'fpd-admin',
			), Fancy_Product_Designer::VERSION);

		}

		public function print_css_string() {

			//get css (colors)
			$selected_layout_id =  isset($_POST['fpd_selected_layout']) ? sanitize_key($_POST['fpd_selected_layout']) : 'default';
			$ui_layout = FPD_UI_Layout_Composer::get_layout($selected_layout_id);
			$css_str = FPD_UI_Layout_Composer::get_css_from_layout($ui_layout);

			if( !empty($css_str) )
				echo '<style type="text/css">'.$css_str.'</style>';

		}

		public function enqueue_styles_scripts( $hook ) {

			global $post;

			 wp_localize_script( 'fpd-admin', 'fpd_admin_opts', array(
					'adminAjaxUrl' => admin_url('admin-ajax.php'),
					'ajaxNonce' => FPD_Admin::$ajax_nonce,
					'enterTitlePrompt' => __('Please enter a title!', 'radykal'),
					'tryAgain' => __('Something went wrong. Please try again!', 'radykal'),
					'addToLibrary' => __('Add imported image source to media library?', 'radykal'),
					'remove' => __('Do you really want to delete the item?', 'radykal'),
					'chooseThumbnail' => __('Choose Thumbnail', 'radykal'),
					'dialogCancel' => __('Cancel', 'radykal'),
					'dialogAlertButton' => __('Got It', 'radykal'),
					'dialogConfirmButton' => __('Yes', 'radykal'),
					'dialogConfirmCancel' => __('No', 'radykal'),
					'dialogPromptButton' => __('Okay', 'radykal'),
				)
			);

			//woocommerce post types
		    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

		        if( 'shop_order' === $post->post_type ) {

					wp_enqueue_style( 'jquery-fpd' );
					wp_enqueue_style( 'fpd-admin-icon-font' );
					wp_enqueue_style( 'radykal-admin' );
					wp_enqueue_style( 'fpd-admin' );

					wp_enqueue_script( 'fpd-order-viewer');

		        }
		        else {

			        wp_enqueue_style( 'wp-color-picker' );
		        	wp_enqueue_style( 'radykal-admin' );
					wp_enqueue_style( 'fpd-admin' );

					wp_enqueue_script( 'wp-color-picker' );
					wp_enqueue_script( 'radykal-admin' );
					wp_enqueue_script( 'fpd-admin' );

					if( !wp_script_is('select2') && !wp_script_is('select2-avada-js') ) {
						wp_enqueue_style( 'radykal-select2' );
						wp_enqueue_script( 'radykal-select2' );
					}
					wp_enqueue_script( 'radykal-select-sortable' );

		        }
		    }

			//manage fancy products
		    if( $hook == 'toplevel_page_fancy_product_designer' ) {

			    wp_enqueue_media();
			    wp_enqueue_style( 'fpd-admin-icon-font' );
			    wp_enqueue_style( 'radykal-admin' );
			    wp_enqueue_style( 'fpd-admin' );

				wp_enqueue_script( 'radykal-admin' );
				wp_enqueue_script( 'fpd-admin' );
			    wp_enqueue_script( 'fpd-manage-fancy-products', plugins_url('/js/manage-fancy-products.js', __FILE__), array(
			    	'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-fpd'
				), Fancy_Product_Designer::VERSION);

				$imports_dir_url = wp_upload_dir();
				$imports_dir_url = $imports_dir_url['baseurl'] . '/fpd-imports';
			    wp_localize_script( 'fpd-manage-fancy-products', 'fpd_fancy_products_opts', array(
						'selectProduct' => __('Please select a Product first to assign the category!', 'radykal'),
						'nothingToExport' => __('This product does not contain any views!', 'radykal'),
						'noJSON' => __('Sorry, but the selected file is not a valid JSON object. Are you sure you have selected the correct file to import?', 'radykal'),
						'chooseThumbnail' => __('Choose a thumbnail', 'radykal'),
						'importedFileStored' => __('If no, the images are stored in: ', 'radykal'). '<br /><i>'.$imports_dir_url.'</i>',
					)
				);

			}

			//product builder
		    if( $hook == 'fancy-product-designer_page_fpd_product_builder' ) {

		    	wp_enqueue_media();

				wp_enqueue_style( 'fpd-admin-jquery-ui' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_style( 'radykal-select2' );
				wp_enqueue_style( 'radykal-tagsmanager' );
				wp_enqueue_style( 'fpd-admin-icon-font' );
				wp_enqueue_style( 'radykal-admin' );
				wp_enqueue_style( 'fpd-admin' );
				wp_enqueue_style( 'jquery-fpd-static' );

				wp_register_script( 'fpd-product-builder', plugins_url('/js/product-builder.js', __FILE__), array(
					'jquery-ui-core',
					'jquery-ui-mouse',
					'jquery-ui-sortable',
					'jquery-ui-spinner',
					'jquery-ui-widget',
					'jquery-ui-slider',
					'wp-color-picker',
					'radykal-tagsmanager',
					'radykal-select2',
					'radykal-admin',
					'fpd-admin',
					'jquery-fpd'
				), Fancy_Product_Designer::VERSION );

				wp_localize_script( 'fpd-product-builder', 'fpd_product_builder_opts', array(
						'adminUrl' => admin_url(),
						'originX' => fpd_get_option('fpd_common_parameter_originx'),
						'originY' => fpd_get_option('fpd_common_parameter_originy'),
						'paddingControl' => fpd_get_option('fpd_padding_controls'),
						'defaultFont' => get_option('fpd_font') ? get_option('fpd_font') : 'Arial',
						'enterTitlePrompt' => __('Enter a title for the element', 'radykal'),
						'chooseElementImageTitle' => __( 'Choose an element image', 'radykal' ),
						'set' => __( 'Set', 'radykal' ),
						'enterYourText' => __( 'Enter your text.', 'radykal' ),
						'removeElement' => __('Remove element?', 'radykal'),
						'notChanged' => __('You have not saved your changes!', 'radykal'),
						'changeImageSource' => __('Change Image Source', 'radykal'),
						'loading' => __('Loading', 'radykal'),
						'uploadZonesTopped' => fpd_get_option('fpd_uploadZonesTopped'),
						'enabled_fonts' => FPD_Fonts::to_json(FPD_Fonts::get_enabled_fonts()),
						'mask_svg_alert' => __( 'The image is not a SVG, you can only use SVG as view mask!', 'radykal' ),
					)
				);

				wp_enqueue_script( 'fpd-product-builder' );

		    }

		    //ui & layout composer
		    if( $hook == 'fancy-product-designer_page_fpd_ui_layout_composer' ) {

				wp_enqueue_style( 'fpd-admin' );
				wp_enqueue_style( 'jquery-fpd' );

				wp_enqueue_script( 'fpd-ui-layout-composer', plugins_url('/admin/js/ui-layout-composer.js', FPD_PLUGIN_ADMIN_DIR), array(
					'wp-color-picker',
					'fpd-admin',
					'jquery-fpd',
				), Fancy_Product_Designer::VERSION );

				//ui-layout composer toolbar
				wp_enqueue_style( 'fpd-ui-layout-composer-toolbar' );
				wp_enqueue_script( 'fpd-ui-layout-composer-toolbar' );

				wp_localize_script( 'fpd-ui-layout-composer', 'fpd_ui_layout_composer_opts', array(
						'fpd_ui_layout_composer_opts' => __('The default layout can not be deleted!', 'radykal'),
						'overwrite_info' => __('The current selected layout will be overwritten!', 'radykal'),
						'delete_info' => __('Delete selected layout?', 'radykal'),
						'reset_default_info' => __('Are your sure to reset the current selected layout to default?', 'radykal'),
						'info_action_tooltip' => __('The text can be changed in the Labels settings!', 'radykal'),
						'general_plugin_opts' => array(
							'templatesDirectory' => plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ),
							'facebookAppId' =>  fpd_get_option('fpd_facebook_app_id'),
							'instagramClientId' =>  fpd_get_option('fpd_instagram_client_id'),
							'instagramRedirectUri' =>  fpd_get_option('fpd_instagram_redirect_uri'),
							'langJSON' => json_decode(FPD_Settings_Labels::get_labels_object_string(), true)
						),
					)
				);

			}

		    //fancy design categories
		    if( $hook == 'edit-tags.php' && isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'fpd_design_category' ) {

				wp_enqueue_media();
			    wp_enqueue_style( 'radykal-admin' );
			    wp_enqueue_script( 'radykal-admin' );

		    }

			//manage designs
		    if( $hook == 'fancy-product-designer_page_fpd_manage_designs') {

		    	wp_enqueue_media();

		    	wp_enqueue_style( 'radykal-select2' );
		    	wp_enqueue_style( 'fpd-admin-icon-font' );
				wp_enqueue_style( 'radykal-admin' );
		    	wp_enqueue_style( 'fpd-admin' );

		    	wp_enqueue_script( 'radykal-select2' );
				wp_enqueue_script( 'radykal-admin' );
		    	wp_enqueue_script( 'fpd-admin' );
		    	wp_enqueue_script( 'fpd-nestedSortable', plugins_url('/js/jquery.mjs.nestedSortable.js', __FILE__), array( 'jquery', 'jquery-ui-sortable'), Fancy_Product_Designer::VERSION );
		    	wp_enqueue_script( 'fpd-manage-fancy-designs', plugins_url('/js/manage-fancy-designs.js', __FILE__), false, Fancy_Product_Designer::VERSION );
		    	wp_localize_script( 'fpd-manage-fancy-designs', 'fpd_fancy_designs_opts', array(
						'chooseDesign' => __('Choose a Design Image', 'radykal'),
						'adminUrl' => admin_url(),
					)
				);

		    }

		    //shortcode orders
		    if( $hook == 'fancy-product-designer_page_fpd_orders' ) {

				wp_enqueue_style( 'jquery-fpd' );
				wp_enqueue_style( 'fpd-admin-icon-font' );
				wp_enqueue_style( 'radykal-admin' );
				wp_enqueue_style( 'fpd-admin' );

				wp_enqueue_script( 'fpd-order-viewer');

	        }

			//settings
			if( $hook == 'fancy-product-designer_page_fpd_settings') {

				wp_enqueue_media();

				wp_enqueue_style( 'radykal-select2' );
				wp_enqueue_style( 'radykal-admin' );
				wp_enqueue_style( 'fpd-admin' );
				wp_enqueue_style( 'wp-color-picker' );

				wp_enqueue_script( 'radykal-ace-editor' );
				wp_enqueue_script( 'radykal-select2' );
				wp_enqueue_script( 'radykal-select-sortable' );
				wp_enqueue_script( 'radykal-admin' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'fpd-admin' );

			}

			//js vars for order viewer
			wp_localize_script( 'fpd-order-viewer', 'fpd_order_viewer', array(
					'order_id' 					=> isset($_GET['post']) ? $_GET['post'] : 0,
					'templates_dir' 			=> plugins_url('/assets/templates/', FPD_PLUGIN_ROOT_PHP ),
					'enabled_fonts' 			=> FPD_Fonts::to_json(FPD_Fonts::get_enabled_fonts()),
					'loading_data_text' 		=> __( 'Loading data...', 'radykal' ),
					'order_data_error_text' 	=> __( 'Order data could not be loaded. Please try again!', 'radykal' ),
					'svg_bitmap_text' 			=> __( 'You cannot create an SVG file from a bitmap, you can only do this by using a text element or another SVG image file', 'radykal' ),
					'image_creation_fail_text' 	=> __( 'Image creation failed. Please try again!', 'radykal' ),
					'no_element_text' 			=> __('No element selected!', 'radykal'),
					'no_width_text' 			=> __( 'No width has been entered. Please set one!', 'radykal' ),
					'no_height_text' 			=> __( 'No height has been entered. Please set one!', 'radykal' ),
					'pdf_creation_fail_text' 	=> __( 'PDF creation failed - There is too much data being sent. To fix this please increase the WordPress memory limit in your php.ini file. You could export a single view or use the JPEG image format! ', 'radykal' ),
					'json_parse_error_text' 	=> __('JSON could not be parsed. Go to wp-content/fancy_products_orders/pdfs and check if a PDF has been generated.'),
					'no_fp_select_text' 		=> __( 'No Product is selected. Please load one from the Order Items table!', 'radykal' ),
					'popup_block_text' 			=> __( 'Your Pop-Up Blocker is enabled so the image will be opened in a new window. Please choose to allow this website in your pop-up blocker!', 'radykal' ),
					'load_order_error_text' 	=> __( 'Could not load order item image. Please try again!', 'radykal' ),
				)
			);

		}
	}
}

new FPD_Admin_Scripts_Styles();

?>