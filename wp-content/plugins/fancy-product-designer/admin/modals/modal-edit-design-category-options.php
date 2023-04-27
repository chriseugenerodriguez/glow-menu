<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-edit-options',
	__('Options', 'radykal'),
	__('Here you can set custom options for all designs in a single category or for every design element separately.', 'radykal')
);

?>

<!-- Only for single designs to set a custom thumbnail -->
<div id="fpd-set-design-thumbnail-wrapper" class="fpd-hidden-design-category">
	<table class="form-table radykal-settings-form">
		<tbody>
			<tr valign="top">
				<th scope="row">
					<?php _e('Design Thumbnail', 'radykal'); ?>
				</th>
				<td class="fpd-clearfix">
					<div id="fpd-set-design-thumbnail">+</div>
					<a href="#" id="fpd-remove-design-thumbnail" class="fpd-icon-btn"><i class="dashicons dashicons-minus"></i></a>
				</td>
			</tr>
		</tbody>
	</table>
	<br />
</div>

<form>
	<p>
		<label>
			<input type="checkbox" value="1" name="enabled" /> <strong><?php _e('Enable Options', 'radykal'); ?></strong>
		</label>
	</p>
	<table class="form-table radykal-settings-form">
		<tbody>
			<?php

			radykal_output_option_item( array(
					'id' => 'x',
					'title' => __('X-Position', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'y',
					'title' => __('Y-Position', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'z',
					'title' => __('Z-Position', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '-1',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> -1,
						'step' 	=> 1
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'scale',
					'title' => __('Scale', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number'
				)
			);

			radykal_output_option_item( array(
					'id' => 'minScaleLimit',
					'title' => __('Minimum Scale Limit', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '0.01',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0.01,
						'step' 	=> 0.01
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'resizeToW',
					'title' => __('Resize To Width', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number'
				)
			);

			radykal_output_option_item( array(
					'id' => 'resizeToH',
					'title' => __('Resize To Height', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number'
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Scale Mode', 'radykal' ),
					'id' 		=> 'scaleMode',
					'default'	=> 'fit',
					'type' 		=> 'select',
					'options'	=>  array(
						'fit' => __('Fit', 'radykal'),
						'cover' => __('Cover', 'radykal')
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'price',
					'title' => __('Price', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '0',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				)
			);

			/*radykal_output_option_item( array(
					'id' => 'svgColors',
					'title' => __('Color Picker for SVG images', 'radykal'),
					'default'	=> 'no',
					'type' 		=> 'checkbox',
					'relations' =>  array(
						'colors' => false
					)
				)
			);*/

			radykal_output_option_item( array(
					'id' => 'colors',
					'title' => __('Color(s)', 'radykal'),
					'class' 	=> 'large-text',
					'default'	=> '',
					'type' 		=> 'text'
				)
			);

			radykal_output_option_item( array(
					'id' => 'colorLinkGroup',
					'title' => __('Color Link Group', 'radykal'),
					'class' 	=> 'large-text',
					'default'	=> '',
					'type' 		=> 'text'
				)
			);

			radykal_output_option_item( array(
					'id' => 'replace',
					'title' => __('Replace', 'radykal'),
					'class' 	=> 'large-text',
					'default'	=> '',
					'type' 		=> 'text'
				)
			);

			radykal_output_option_item( array(
					'id' => 'sku',
					'title' => __('SKU', 'radykal'),
					'class' 	=> 'large-text fpd-hidden-design-category',
					'default'	=> '',
					'type' 		=> 'text'
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Use another element as bounding box?', 'radykal' ),
					'id' 		=> 'bounding_box_control',
					'class'		=> 'fpd-bounding-box-control',
					'default'	=> 'no',
					'type' 		=> 'checkbox',
					'relations' =>  array(
						'bounding_box_by_other' => true,
						'bounding_box_x' => false,
						'bounding_box_y' => false,
						'bounding_box_width' => false,
						'bounding_box_height' => false,
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'bounding_box_x',
					'title' => __('Bounding Box X-Position', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'bounding_box_y',
					'title' => __('Bounding Box Y-Position', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'bounding_box_width',
					'title' => __('Bounding Box Width', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'bounding_box_height',
					'title' => __('Bounding Box Height', 'radykal'),
					'css' 		=> 'width:70px;',
					'default'	=> '',
					'type' 		=> 'number',
					'custom_attributes' => array(
						'min' 	=> 0,
						'step' 	=> 1
					)
				)
			);

			radykal_output_option_item( array(
					'id' => 'bounding_box_by_other',
					'title' => __('Bounding Box Target', 'radykal'),
					'class' 	=> 'large-text',
					'default'	=> '',
					'type' 		=> 'text',

				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Bounding Box Mode', 'radykal' ),
					'id' 		=> 'boundingBoxMode',
					'default'	=> 'inside',
					'type' 		=> 'select',
					'options'	=>  FPD_Settings_General::get_bounding_box_modi()
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Auto-Center', 'radykal' ),
					'id' 		=> 'autoCenter',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Draggable', 'radykal' ),
					'id' 		=> 'draggable',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Rotatable', 'radykal' ),
					'id' 		=> 'rotatable',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Resizable', 'radykal' ),
					'id' 		=> 'resizable',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Removable', 'radykal' ),
					'id' 		=> 'removable',
					'default'	=> '1',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Auto-Select', 'radykal' ),
					'id' 		=> 'autoSelect',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Stay On Top', 'radykal' ),
					'id' 		=> 'topped',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Uni-Scaling Unlockable', 'radykal' ),
					'id' 		=> 'uniScalingUnlockable',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);

			radykal_output_option_item( array(
					'title' => __( 'Replace In All Views', 'radykal' ),
					'id' 		=> 'replaceInAllViews',
					'default'	=> '0',
					'type' 		=> 'radio',
					'options'   => array(
						'1'	 => __( 'Yes', 'radykal' ),
						'0'	 => __( 'No', 'radykal' ),
					)
				)
			);
			?>

		</tbody>
	</table>
</form>
<?php
	FPD_Admin_Modal::output_footer(
		__('Set', 'radykal')
	);
?>
<script type="text/javascript">

	//scripts to use it for fpd and mspc
	jQuery(document).ready(function($) {

		var mediaUploader = null,
			$modalWrapper = $('#fpd-modal-edit-options');

		//enable/disable form
		$('#fpd-modal-edit-options').on('change', 'input[name="enabled"]', function() {

			var $this = $(this),
				$allInputs = $this.parents('.fpd-modal-content:first').find('table').find('input,select');

			if($this.is(':checked')) {
				$this.val(1);
				$allInputs.attr('disabled', false);
			}
			else {
				$this.val(0);
				$allInputs.attr('disabled', true);
			}

		});

		//set the thumbnail for a design in the modal parameters form
		$modalWrapper.on('click', '#fpd-set-design-thumbnail', function(evt) {

			if (mediaUploader) {
	            mediaUploader.open();
	            return;
	        }

	        mediaUploader = wp.media({
	            title: 'Choose Thumbnail',
	            multiple: true
	        });

			mediaUploader.on('select', function() {

				var imgUrl = mediaUploader.state().get('selection').toJSON()[0].url;

				$('#fpd-set-design-thumbnail').css('background-image', 'url('+imgUrl+')')
				.data('thumbnail', imgUrl);

	        });

	        mediaUploader.open();

	        evt.preventDefault();

		});

		$modalWrapper.on('click', '#fpd-remove-design-thumbnail', function(evt) {

			evt.preventDefault();
			$('#fpd-set-design-thumbnail').css('background-image', 'none')
			.data('thumbnail', '');

		});

	});

</script>