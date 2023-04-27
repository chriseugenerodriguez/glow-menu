<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings') ) {

	class FPD_Settings {

		public static $radykal_settings;

		public function __construct() {

			add_action( 'init', array( &$this, 'init') );
			add_action( 'radykal_save_options', array( &$this, 'options_saved') );

		}

		public function init() {

			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-general-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-default-element-options-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-labels-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-fonts-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-advanced-color-settings.php');
			require_once(FPD_PLUGIN_DIR.'/inc/settings/class-wc-settings.php');

			add_action( 'radykal_settings_header_end', array(&$this, 'header_end'), 10 );
			add_action( 'radykal_before_options_save', array(&$this, 'before_options_saved') );

			//create new settings instance
			$tabs = array(
				'general' => __('General', 'radykal'),
				'default_element_options' => __('Default Element Options', 'radykal'),
				'labels' => __('Labels', 'radykal'),
				'fonts' => __('Fonts', 'radykal'),
				'advanced_colors' => __('Advanced Color Config.', 'radykal'),
			);

			if( function_exists('get_woocommerce_currency') ) {
				$tabs['woocommerce'] = __('WooCommerce', 'radykal');
			}

			$tabs = apply_filters( 'fpd_settings_tabs', $tabs );

			self::$radykal_settings = new Radykal_Settings( array(
					'page_id' => 'fpd_settings',
					'tabs' => $tabs
				)
			);

			//first add blocks
			$blocks = apply_filters('fpd_settings_blocks', array(
				'general' => array(
					'general-product-designer' => __('Product Designer', 'radykal'),
					'custom-images' => __('Custom Image Uploads', 'radykal'),
					'sharing' => __('Design Share', 'radykal'),
					'qr-code' => __('QR-Code', 'radykal'),
					'troubleshooting' => __('Troubleshooting', 'radykal'),
					'misc' => __('Miscellaneous', 'radykal'),
				),
				'default_element_options' => array(
					'default-image-options' => __('Image Options', 'radykal'),
					'default-custom-image-options' => __('Custom Image Options', 'radykal'),
					'default-custom-text-options' => __('Custom Text Options', 'radykal'),
					'default-common-options' => __('Common Options', 'radykal'),
				),
				'fonts' => array(
					'fonts' => __('Fonts for the typeface dropdown', 'radykal'),
				),
				'advanced_colors' => array(
					'hex-names' => __('Define names for your hexadecimal colors.', 'radykal'),
					'color-prices' => __('Set own prices for your hexadecimal colors.', 'radykal'),
				),
				'woocommerce' => array(
					'wc-product-page' => __('Product Page &amp; Cart', 'radykal'),
					'wc-catalog-listing' => __('Catalog Listing', 'radykal'),
					'wc-global-product-designer' => __('Global Product Designer', 'radykal'),
				)
			));

			self::$radykal_settings->add_blocks($blocks);

			self::$radykal_settings->add_blocks_description(array(
				'default-image-options' => __('The default options for custom uploaded images, Facebook/Instagram photos and Designs.', 'radykal'),
				'default-custom-image-options' => __('The default options for uploaded images by the customer.', 'radykal'),
				'default-custom-text-options' => __('The default options for added texts by the customer.', 'radykal'),
			));

			//add general settings
			$general_options = FPD_Settings_General::get_options();
			self::$radykal_settings->add_block_options( 'general-product-designer', $general_options['general-product-designer']);
			self::$radykal_settings->add_block_options( 'custom-images', $general_options['custom-images']);
			self::$radykal_settings->add_block_options( 'sharing', $general_options['sharing']);
			self::$radykal_settings->add_block_options( 'qr-code', $general_options['qr-code']);
			self::$radykal_settings->add_block_options( 'troubleshooting', $general_options['troubleshooting']);
			self::$radykal_settings->add_block_options( 'misc', $general_options['misc']);

			//add default element options settings
			$default_element_options = FPD_Settings_Default_Element_Options::get_options();
			self::$radykal_settings->add_block_options( 'default-image-options', $default_element_options['default-image-options']);
			self::$radykal_settings->add_block_options( 'default-custom-image-options', $default_element_options['default-custom-image-options']);
			self::$radykal_settings->add_block_options( 'default-custom-text-options', $default_element_options['default-custom-text-options']);
			self::$radykal_settings->add_block_options( 'default-common-options', $default_element_options['default-common-options']);

			//add fonts settings
			$fonts_options = FPD_Settings_Fonts::get_options();
			self::$radykal_settings->add_block_options( 'fonts', $fonts_options['fonts']);

			//add advanced color settings
			$advanced_color_options = FPD_Settings_Advanced_Colors::get_options();
			self::$radykal_settings->add_block_options( 'hex-names', $advanced_color_options['hex-names']);
			self::$radykal_settings->add_block_options( 'color-prices', $advanced_color_options['color-prices']);

			//add wc settings
			$wc_options = FPD_Settings_WooCommerce::get_options();
			self::$radykal_settings->add_block_options( 'wc-product-page', $wc_options['wc-product-page']);
			self::$radykal_settings->add_block_options( 'wc-catalog-listing', $wc_options['wc-catalog-listing']);
			self::$radykal_settings->add_block_options( 'wc-global-product-designer', $wc_options['wc-global-product-designer']);

			//$fonts_options = FPD_Settings_Fonts::get_options();
			self::$radykal_settings->add_block_options( 'plus', $fonts_options['fonts']);

			do_action( 'fpd_block_options_end' );

		}

		public function header_end( $page_id ) {

			if( isset($_GET['tab']) && $_GET['tab'] === 'labels' ) {

				//get active languages from WPML
				$languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc&skip_missing=0' );

				if( isset($_GET['lang_code']) ) //get lang code from url
					$current_lang_code = $_GET['lang_code'];
				else if($languages) { //get first lang code from wpml languages
					$first = reset($languages);
					$current_lang_code = $first['language_code'];
				}
				else { //get locale code
					$current_lang_code = FPD_Settings_Labels::get_current_lang_code();
				}

				echo '<p class="description">'.__('You can translate all labels in the frontend.', 'radykal').'</p>';

				//output all WPML languages in sub menu
				if (!empty($languages) && sizeof($languages) > 0 ):
				?>
				<ul class="subsubsub">
					<?php
					foreach($languages as $key => $language) {
						echo '<li><a class="'.($key == $current_lang_code ? 'current' : '').'" href="'.add_query_arg( 'lang_code', $key).'"><img src="'.$languages[$key]['country_flag_url'].'" />'.$languages[$key]['translated_name'].'</a></li>';
					}
					?>
				</ul>
				<br class="clear" />
				<?php
				endif;

				//FPD_Settings_Labels::update_all_languages();
				$default_lang = FPD_Settings_Labels::get_default_lang();
				$current_lang = FPD_Settings_Labels::get_current_lang($current_lang_code);

				$textarea_keys = array(
					'uploaded_image_size_alert',
					'not_supported_device_info',
					'info_content'
				);

				foreach($default_lang as $section => $fields) {

					//PLUS
					if($section == 'plus' && !class_exists('Fancy_Product_Designer_Plus'))
						continue;

					$section_title = $section === 'misc' ? 'Miscellaneous' : ucfirst($section);
					echo '<h3>'.$section_title.'</h3>';
					echo '<table class="form-table" id="'.$section.'">';
					foreach($fields as $key => $value) {

						$trans_val = isset($current_lang[$section][$key]) ? $current_lang[$section][$key] : $value;
						?>
						<tr>
							<th scope="row"><?php echo str_replace('_', ' ', $key); ?></th>
							<td class="radykal-option-type--text">
								<?php if ( in_array($key, $textarea_keys) ): ?>
								<textarea id="<?php echo $key; ?>" name="<?php echo $key; ?>" rows="3" class="large-text"><?php echo $trans_val; ?></textarea>
								<span class="description"><?php _e('HTML Tags Supported', 'radykal'); ?></span>
								<?php else: ?>
								<input type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo $trans_val; ?>" class="large-text" />
								<?php endif; ?>
							</td>
						</tr>
						<?php
					}
					echo '</table>';

				}
				?>

				<input type="hidden" name="fpd_translation_str" class="large-text" />
				<input type="hidden" name="fpd_lang_code" value="<?php echo $current_lang_code; ?>" />
				<script>

				jQuery(document).ready(function() {

					jQuery('[name="radykal_save_options_fpd_settings"]').click(function(evt) {

						var json = {};

						jQuery('#radykal-options-form-fpd_settings table').each(function(i, table) {

							var $table = jQuery(table);
							json[table.id] = {};

							$table.find('input, textarea').each(function(j, field) {

								json[table.id][field.name] = field.value.replace(/(?:\r\n|\r|\n)/g, '<br />');;

							});

						});

						jQuery('[name="fpd_translation_str"]').val(JSON.stringify(json))
						.parent('form').submit();

						evt.preventDefault();

					});

				});

				</script>

				<?php

			}

		}

		public function before_options_saved( $page_id ) {

			if( isset($_POST['fpd_translation_str']) && isset($_POST['fpd_lang_code']) ) {

				check_admin_referer( $page_id.'_nonce' );

				if( isset($_POST['radykal_reset_options_'.$page_id]) ) {
					FPD_Settings_Labels::reset($_POST['fpd_lang_code']);
				}
				else {
					update_option('fpd_lang_'.$_POST['fpd_lang_code'], stripslashes($_POST['fpd_translation_str']) );
				}

			}

		}

		public function options_saved( $tab ) {


			if( $tab == 'general') {

				if( is_multisite() && isset($_POST['fpd_envato_purchase_code']) ) {

					if( empty($_POST['fpd_envato_purchase_code']) )
						delete_site_option( 'fpd_envato_purchase_code' );
					else
						update_site_option( 'fpd_envato_purchase_code', $_POST['fpd_envato_purchase_code']);
				}

			}

		}
	}
}

new FPD_Settings();
?>