<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_UI_Layout_Composer') ) {

	class FPD_UI_Layout_Composer {

		public static function get_default_json_url() {

			return FPD_PLUGIN_DIR.'/assets/json/default_ui_layout.json';

		}

		public function output() {

			//FPD_UI_Layout_Composer::reset_to_default('default');

			$selected_layout_id = 'default';

			if( isset($_POST['fpd_selected_layout']) ) {

				check_admin_referer( 'fpd_save_layout' );

				$selected_layout_id =  sanitize_key($_POST['fpd_selected_layout']);

				if( $_POST['fpd_method'] == 'delete' ) {

					$selected_layout_id = 'default';

				}
			}

			$selected_layout = FPD_UI_Layout_Composer::get_layout($selected_layout_id);
			if(is_null($selected_layout)) {
				$selected_layout = FPD_UI_Layout_Composer::reset_to_default('default');
			}
			$all_layouts = FPD_UI_Layout_Composer::get_layouts();

			?>
			<div class="wrap" id="fpd-ui-layout-composer">

				<h2 class="fpd-clearfix">
					<?php _e('UI &amp; Layout Composer', 'radykal'); ?>
					<?php fpd_admin_display_version_info(); ?>
				</h2>
				<p class="description"><?php _e('With the composer you can easily customize and create own user interfaces for the product designer.', 'radykal'); ?></p>

				<div id="fpd-composer-head" class="fpd-panel fpd-clearfix">

					<select name="fpd_ui_layouts">
						<?php
						foreach($all_layouts as $layout) {

							$name = json_decode(stripslashes($layout->option_value), true);
							$name = $name['name'];
							$slug = str_replace('fpd_ui_layout_', '', $layout->option_name);
							echo '<option value="'.$slug.'" '.selected($slug, $selected_layout_id, false).'>'.$name.'</option>';

						}
						?>
					</select>
					<a href="#" class="add-new-h2" id="fpd-save-layout"><?php _e('Save', 'radykal'); ?></a>
					<a href="#" class="add-new-h2" id="fpd-delete-layout"><?php _e('Delete', 'radykal'); ?></a>
					<a href="#" class="add-new-h2" id="fpd-reset-to-default-layout"><?php _e('Reset To Default', 'radykal'); ?></a>
					<a href="#" class="add-new-h2" id="fpd-save-as-new-layout"><?php _e('Save As New', 'radykal'); ?></a>


				</div><!-- #fpd-ui-layout-composer -->
				<style type="text/css" id="fpd-preview-styles"></style>
				<div id="fpd-composer-product-designer-wrap" class="fpd-panel" style="padding-bottom: 60px;">
					<h3><?php _e('Preview', 'radykal'); ?></h3>
					<script type="text/javascript">

						var initial_opts = {};
						try {
							initial_opts = JSON.parse(JSON.stringify(<?php echo json_encode($selected_layout); ?>));
						}
						catch(evt) {
							initial_opts = {};
							console.log('Is not a valid JSON');
						}

					</script>
					<div id="fpd-product-designer-preview" class="<?php echo $selected_layout['container_classes']; ?>">
						<div class="fpd-product" title="Front" data-thumbnail="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>"  data-producttitle="Product One">
							<img title="demo-shirt" src="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>" data-parameters='{"autoCenter": true, "draggable": true}' />
							<div class="fpd-product" title="Back" data-thumbnail="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>">
								<img  title="demo-shirt" src="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>" data-parameters='{"autoCenter": true, "draggable": true}' />
							</div>
						</div>
						<div class="fpd-product" title="Front" data-thumbnail="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>" data-producttitle="Product Two">
							<img title="demo-shirt" src="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>" data-parameters='{"autoCenter": true, "draggable": true}' />
							<div class="fpd-product" title="Back" data-thumbnail="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>">
								<img  title="demo-shirt" src="<?php echo plugins_url('/admin/img/shirt-preview.png', FPD_PLUGIN_ADMIN_DIR); ?>" data-parameters='{"autoCenter": true, "draggable": true}' />
							</div>
						</div>
					</div>

				</div><!-- #fpd-composer-product-designer-wrap -->

				<?php require_once(FPD_PLUGIN_ADMIN_DIR.'/views/html-ui-layout-composer-toolbar.php'); ?>

				<form class="radykal-hidden" method="post">
					<?php wp_nonce_field( 'fpd_save_layout'); ?>
					<input type="text" name="fpd_selected_layout" value="" />
					<input type="text" name="fpd_method" value="" />
					<textarea name="fpd_ui_layout" class="large-text"></textarea>
				</form>

			</div>
			<?php

		}

		public static function get_layouts() {

			global $wpdb;
			return $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s", "fpd_ui_layout_%") );

		}

		public static function get_layout( $id='' ) {

			if( get_option('fpd_ui_layout_'.$id) !== false ) {
				return json_decode( stripslashes(get_option('fpd_ui_layout_'.$id)), true);
			}
			else if( get_option('fpd_ui_layout_default') !== false ) {
				return json_decode( stripslashes(get_option('fpd_ui_layout_default') ), true);
			}
			else {

				$default_layout = file_get_contents(self::get_default_json_url());
				$default_layout = json_encode(json_decode($default_layout));
				update_option('fpd_ui_layout_default', $default_layout);

				return json_decode($default_layout, true);

			}

		}

		public static function get_css_from_layout( $layout ) {

			if( isset($layout->css) && !empty($layout->css) ) {

				return $layout->css;

			}
			else {

				if( is_array($layout) )
					$layout = json_decode(json_encode($layout)); //convert array to stdclass

				$primary_color = @$layout->css_colors && @$layout->css_colors->primary_color ? $layout->css_colors->primary_color : '#000000';
				$secondary_color = @$layout->css_colors && @$layout->css_colors->secondary_color ? $layout->css_colors->secondary_color : '#27ae60';
				$css_result = FPD_UI_Layout_Composer::parse_css('@primaryColor: '.$primary_color.'; @secondaryColor: '.$secondary_color.';');

				if( !is_array($css_result) ) {

					$layout_id = sanitize_key($layout->name);
					$layout->css = $css_result;
					update_option( 'fpd_ui_layout_'.$layout_id, addslashes(json_encode($layout)) );

					return $css_result;

				}
				else {
					return '';
				}
			}

		}

		public static function parse_css( $parse_str='' ) {

			if( !class_exists('Less_Parser') )
				require_once(FPD_PLUGIN_ADMIN_DIR.'/inc/less/Less.php');

			$less_file = FPD_PLUGIN_ADMIN_DIR.'/less/colors.less';
			try {

				$options = array( 'compress'=>true );
				$parser = new Less_Parser($options);
				$parser->parseFile( $less_file );
				$parser->parse( $parse_str );
				return $parser->getCss(); //save ins json

			}
			catch(Exception $e){

				return array(
					'message' => $e->getMessage(),
					'type'    => 'error'
				);

			}

		}

		public static function reset_to_default( $id='' ) {

			$default_layout = file_get_contents(self::get_default_json_url());
			$default_layout = json_decode(stripslashes($default_layout), true);

			if( get_option('fpd_ui_layout_'.$id) !== false ) {

				$old_layout = get_option('fpd_ui_layout_'.$id);
				$old_layout = json_decode( stripslashes($old_layout), true );
				$default_layout['name'] = empty($old_layout['name']) ? 'Default' : $old_layout['name'];

			}

			update_option('fpd_ui_layout_'.$id, json_encode($default_layout));

			return $default_layout;

		}

	}

}

?>