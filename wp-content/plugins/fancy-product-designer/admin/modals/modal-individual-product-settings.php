<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-individual-product-settings',
	__('Individual Settings', 'radykal'),
	sprintf(
		__('Here you can set individual product designer settings for this product. That allows to use different settings from the <a href="%s">main settings</a>.', 'radykal'),
		esc_url_raw( admin_url('admin.php?page=fpd_settings') )
	)
);

?>

<div class="radykal-tabs">

	<div class="radykal-tabs-nav">
		<a href="general-options" class="current"><?php _e('General', 'radykal'); ?></a>
		<a href="image-options"><?php _e('Image Options', 'radykal'); ?></a>
		<a href="custom-text-options"><?php _e('Custom Text Options', 'radykal'); ?></a>
		<?php if( get_post_type($post) === 'product'): ?>
		<a href="woocommerce-options"><?php _e('WooCommerce', 'radykal'); ?></a>
		<?php endif; ?>
		<?php do_action( 'fpd_ips_tabs_end', $post ); ?>
	</div>

	<div class="radykal-tabs-content">

		<div data-id="general-options" class="current">
			<table class="form-table radykal-form-settings">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('UI Layout', 'radykal'); ?></label></th>
						<td>
							<select name="product_designer_ui_layout" class="radykal-select2" style="width: 100%;">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<?php
									//get all created categories
									$ui_layouts = FPD_Settings_General::get_saved_ui_layouts();
									foreach($ui_layouts as $key => $value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Open Product Designer in...', 'radykal'); ?></label></th>
						<td>
							<select name="product_designer_visibility" style="width: 100%;">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<?php
									//get all created categories
									$visibilities = FPD_Settings_General::get_product_designer_visibilities();
									foreach($visibilities as $key => $value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Main Bar Position', 'radykal'); ?></label></th>
						<td>
							<select name="main_bar_position" style="width: 100%;">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<?php
									//get all created categories
									$mb_positions = FPD_Settings_General::get_main_bar_positions();
									foreach($mb_positions as $key => $value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Design Categories', 'radykal'); ?></label></th>
						<td>
							<select class="radykal-select2" name="design_categories[]" multiple data-placeholder="<?php _e('All Categories', 'radykal'); ?>" style="width: 100%;">
								<?php
									//get all created categories
									$categories = get_terms( 'fpd_design_category', array(
									 	'hide_empty' => false,
									 	'parent' => 0
									));

									foreach($categories as $category) {

										$parent_cat_str = get_category_parents($category->term_id, false, '%#%');
										$parent_cats_array = explode('%#%', $parent_cat_str);
										$level = max(sizeof($parent_cats_array) - 2, 0);

										echo '<option value="'.$category->term_id.'">'.str_repeat("- ", $level).$category->name.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Available Fonts', 'radykal'); ?></label></th>
						<td>
							<select class="radykal-select2" name="font_families[]" multiple data-placeholder="<?php _e('All Fonts', 'radykal'); ?>" style="width: 100%;">
								<?php
									//get all created categories
									$fonts = FPD_Fonts::get_enabled_fonts();
									foreach($fonts as $key => $font) {
										echo '<option value="'.$font.'">'.$font.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Background', 'radykal'); ?></label></th>
						<td>
							<label><input type="radio" name="background_type" value="image" checked="checked" /> <?php _e('Image', 'radykal'); ?></label>
							<label><input type="radio" name="background_type" value="color" /> <?php _e('Color', 'radykal'); ?></label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"> </th>
						<td >
							<button class="button button-secondary" id="fpd-set-background-image"><?php _e('Set Image', 'radykal'); ?></button>
							<input type="hidden" value="<?php echo plugins_url('/img/grid.png', dirname(__FILE__)); ?>" name="background_image">
							<img src="" alt="Background Image" id="fpd-background-image-preview" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"> </th>
						<td><input type="text" name="background_color" value="#ffffff" class="radykal-color-picker" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Replace Initial Elements', 'radykal'); ?></label></th>
						<td>
							<select name="replace_initial_elements">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0"><?php _e( 'No', 'radykal' ); ?></option>
								<option value="1"><?php _e( 'Yes', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Color Prices for Images', 'radykal'); ?></label></th>
						<td>
							<select name="enable_image_color_prices">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0"><?php _e( 'No', 'radykal' ); ?></option>
								<option value="1"><?php _e( 'Yes', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Color Prices for Texts', 'radykal'); ?></label></th>
						<td>
							<select name="enable_text_color_prices">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0"><?php _e( 'No', 'radykal' ); ?></option>
								<option value="1"><?php _e( 'Yes', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Hide Dialog On Add', 'radykal'); ?></label></th>
						<td>
							<select name="hide_dialog_on_add">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0"><?php _e( 'No', 'radykal' ); ?></option>
								<option value="1"><?php _e( 'Yes', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>

		</div><!-- general -->

		<div data-id="image-options">
			<h4><?php _e('Common Image', 'radykal'); ?></h4>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('Price', 'radykal'); ?></label></th>
						<td><input type="number" min="0" step="0.01" name="designs_parameter_price" placeholder="<?php echo fpd_get_option('fpd_designs_parameter_price'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Replace', 'radykal'); ?></label></th>
						<td><input type="text" name="designs_parameter_replace" placeholder="<?php echo get_option('fpd_designs_parameter_replace'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Replace In All Views', 'radykal'); ?></label></th>
						<td>
							<select name="designs_parameter_replaceInAllViews">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0"><?php _e( 'No', 'radykal' ); ?></option>
								<option value="1"><?php _e( 'Yes', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Bounding Box', 'radykal'); ?></label></th>
						<td>
							<select name="designs_parameter_bounding_box_control">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0" data-class="custom-bb"><?php _e( 'Custom Bounding Box', 'radykal' ); ?></option>
								<option value="1" data-class="target-bb"><?php _e( 'Use another element as bounding box', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Bounding Box Mode', 'radykal'); ?></label></th>
						<td>
							<select name="designs_parameter_boundingBoxMode">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<?php

								$bb_modi = FPD_Settings_Default_Element_Options::get_bounding_box_modi();
								foreach($bb_modi as $key => $value) {
									echo '<option value="'.$key.'">'.$value.'</option>';
								}

								?>
							</select>
						</td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box X-Position', 'radykal'); ?></label></th>
						<td><input type="number" name="designs_parameter_bounding_box_x" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_x'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Y-Position', 'radykal'); ?></label></th>
						<td><input type="number" name="designs_parameter_bounding_box_y" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_y'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Width', 'radykal'); ?></label></th>
						<td><input type="number" name="designs_parameter_bounding_box_width" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_width'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Height', 'radykal'); ?></label></th>
						<td><input type="number" name="designs_parameter_bounding_box_height" min="0" step="1" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_height'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="target-bb">
						<th scope="row"><label><?php _e('Bounding Box Target', 'radykal'); ?></label></th>
						<td><input type="text" name="designs_parameter_bounding_box_by_other" placeholder="<?php echo get_option('fpd_designs_parameter_bounding_box_by_other'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Hide Filters', 'radykal'); ?></label></th>
						<td><input type="checkbox" name="designs_parameter_filters" value=" "></td>
					</tr>
				</tbody>
			</table>
			<h4><?php _e('Custom Image', 'radykal'); ?></h4>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('Minimum Width', 'radykal'); ?></label></th>
						<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_minW" placeholder="<?php echo fpd_get_option('fpd_uploaded_designs_parameter_minW'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Minimum Height', 'radykal'); ?></label></th>
						<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_minH" placeholder="<?php echo fpd_get_option('fpd_uploaded_designs_parameter_minH'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Maximum Width', 'radykal'); ?></label></th>
						<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_maxW" placeholder="<?php echo fpd_get_option('fpd_uploaded_designs_parameter_maxW'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Maximum Height', 'radykal'); ?></label></th>
						<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_maxH" placeholder="<?php echo fpd_get_option('fpd_uploaded_designs_parameter_maxH'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Resize To Width', 'radykal'); ?></label></th>
						<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_resizeToW" placeholder="<?php echo fpd_get_option('fpd_uploaded_designs_parameter_resizeToW'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Resize To Height', 'radykal'); ?></label></th>
						<td><input type="number" min="1" step="1" name="uploaded_designs_parameter_resizeToH" placeholder="<?php echo fpd_get_option('fpd_uploaded_designs_parameter_resizeToH'); ?>" value=""></td>
					</tr>
				</tbody>
			</table>
		</div><!-- image options -->

		<div data-id="custom-text-options">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('Price', 'radykal'); ?></label></th>
						<td><input type="number" min="0" step="0.01" name="custom_texts_parameter_price" placeholder="<?php echo fpd_get_option('fpd_custom_texts_parameter_price'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Colors', 'radykal'); ?></label></th>
						<td><input type="text" name="custom_texts_parameter_colors" value="" placeholder="<?php echo fpd_get_option('fpd_custom_texts_parameter_colors'); ?>"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Default Color', 'radykal'); ?></label></th>
						<td><input type="text" name="custom_texts_parameter_fill" value="" placeholder="<?php echo fpd_get_option('fpd_custom_texts_parameter_fill'); ?>"></td>
					<tr valign="top">
						<th scope="row"><label><?php _e('Replace', 'radykal'); ?></label></th>
						<td><input type="text" name="custom_texts_parameter_replace" placeholder="<?php echo get_option('fpd_custom_texts_parameter_replace'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Replace In All Views', 'radykal'); ?></label></th>
						<td>
							<select name="custom_texts_parameter_replaceInAllViews">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0"><?php _e( 'No', 'radykal' ); ?></option>
								<option value="1"><?php _e( 'Yes', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Bounding Box', 'radykal'); ?></label></th>
						<td>
							<select name="custom_texts_parameter_bounding_box_control">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="0" data-class="custom-bb"><?php _e( 'Custom Bounding Box', 'radykal' ); ?></option>
								<option value="1" data-class="target-bb"><?php _e( 'Use another element as bounding box', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box X-Position', 'radykal'); ?></label></th>
						<td><input type="number" name="custom_texts_parameter_bounding_box_x" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_x'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Y-Position', 'radykal'); ?></label></th>
						<td><input type="number" name="custom_texts_parameter_bounding_box_y" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_y'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Width', 'radykal'); ?></label></th>
						<td><input type="number" name="custom_texts_parameter_bounding_box_width" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_width'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="custom-bb">
						<th scope="row"><label><?php _e('Bounding Box Height', 'radykal'); ?></label></th>
						<td><input type="number" name="custom_texts_parameter_bounding_box_height" min="0" step="1" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_height'); ?>" value=""></td>
					</tr>
					<tr valign="top" class="target-bb">
						<th scope="row"><label><?php _e('Bounding Box Target', 'radykal'); ?></label></th>
						<td><input type="text" name="custom_texts_parameter_bounding_box_by_other" placeholder="<?php echo get_option('fpd_custom_texts_parameter_bounding_box_by_other'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Bounding Box Mode', 'radykal'); ?></label></th>
						<td>
							<select name="custom_texts_parameter_boundingBoxMode">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<?php

								$bb_modi = FPD_Settings_Default_Element_Options::get_bounding_box_modi();
								foreach($bb_modi as $key => $value) {
									echo '<option value="'.$key.'">'.$value.'</option>';
								}

								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Maximum Characters	', 'radykal'); ?></label></th>
						<td><input type="number" min="0" step="1" name="custom_texts_parameter_maxLength" placeholder="<?php echo fpd_get_option('fpd_custom_texts_parameter_maxLength'); ?>" value=""></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Maximum Lines	', 'radykal'); ?></label></th>
						<td><input type="number" min="0" step="1" name="custom_texts_parameter_maxLines" placeholder="<?php echo fpd_get_option('fpd_custom_texts_parameter_maxLines'); ?>" value=""></td>
					</tr>
				</tbody>
			</table>
		</div><!-- custom text options -->

		<div data-id="woocommerce-options">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('Product Designer Position', 'radykal'); ?></label></th>
						<td>
							<select name="placement">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<?php
									//get all created categories
									foreach(FPD_Settings_WooCommerce::get_product_designer_positions() as $key => $value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Hide Product Image', 'radykal'); ?></label></th>
						<td>
							<select name="hide_product_image">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'radykal' ); ?></option>
								<option value="no"><?php _e( 'No', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Fullwidth Summary', 'radykal'); ?></label></th>
						<td>
							<select name="fullwidth_summary">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'radykal' ); ?></option>
								<option value="no"><?php _e( 'No', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Get A Quote', 'radykal'); ?></label></th>
						<td>
							<select name="get_quote">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'radykal' ); ?></option>
								<option value="no"><?php _e( 'No', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Customize Button: Variation Needed', 'radykal'); ?></label></th>
						<td>
							<select name="wc_customize_variation_needed">
								<option value=""><?php _e( 'Use Option From Main Settings', 'radykal' ); ?></option>
								<option value="yes"><?php _e( 'Yes', 'radykal' ); ?></option>
								<option value="no"><?php _e( 'No', 'radykal' ); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- wc options -->

		<?php do_action( 'fpd_ips_tabs_content_end' ); ?>

	</div>

<?php
	FPD_Admin_Modal::output_footer(
		__('Set', 'radykal')
	);
?>

<script type="text/javascript">

	jQuery(document).ready(function($) {

		var $modalWrapper = $('#fpd-modal-individual-product-settings'),
			mediaUploader = null;


		//SETTINGS

		$('#fpd-change-settings').click(function(evt) {

			evt.preventDefault();

			openModal($modalWrapper.removeClass('hidden'));

			radykalFillForm($modalWrapper, $('[name="fpd_product_settings"]').val());

			$modalWrapper.find('select').change();
			$modalWrapper.find('[name="background_color"], [name="background_type"]:checked').change();
			$modalWrapper.find('#fpd-background-image-preview').attr('src', $modalWrapper.find('[name="background_image"]').val());

		});

		//background type switcher
		$modalWrapper.find('[name="background_type"]').change(function() {

			if(this.value == 'image') {
				$modalWrapper.find('[name="background_image"]').parents('tr:first').show();
				$modalWrapper.find('[name="background_color"]').parents('tr:first').hide();
			}
			else {
				$modalWrapper.find('[name="background_image"]').parents('tr:first').hide();
				$modalWrapper.find('[name="background_color"]').parents('tr:first').show();
			}

		});

		//bounding box switcher
		$('[name="designs_parameter_bounding_box_control"], [name="custom_texts_parameter_bounding_box_control"]').change(function() {

			var $this = $(this),
				$tbody = $this.parents('tbody');

			$tbody.find('.custom-bb, .target-bb').hide().addClass('no-serialization');
			if(this.value != '') {
				$tbody.find('.'+$this.find(":selected").data('class')).show().removeClass('no-serialization');
			}


		});

		$('#fpd-set-background-image').click(function(evt) {

			evt.preventDefault();

			if (mediaUploader) {
	            mediaUploader.open();
	            return;
	        }

	        mediaUploader = wp.media({
	            title: '<?php _e( 'Choose a background image', 'radykal' ); ?>',
	            multiple: false
	        });

	        mediaUploader.on('select', function() {

				backgroundImage = mediaUploader.state().get('selection').toJSON()[0].url;
				$modalWrapper.find('[name="background_image"]').val(backgroundImage);
				$modalWrapper.find('#fpd-background-image-preview').attr('src', backgroundImage);

			});

			mediaUploader.open();

		});

		$modalWrapper.on('click', '.fpd-save-admin-modal', function(evt) {

			evt.preventDefault();

			var $formFields = $modalWrapper.find('input[type="number"],input[type="text"],input[type="hidden"],input[type="radio"]:checked,select,input[type="checkbox"]:checked').filter(':not(.no-serialization)'),
				serializedObj = fpdSerializeObject($formFields);
				serializedStr = JSON.stringify(serializedObj);

			$('[name="fpd_product_settings"]').val(serializedStr);

			closeModal($modalWrapper);

		});

	});

</script>