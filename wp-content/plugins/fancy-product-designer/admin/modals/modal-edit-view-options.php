<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-edit-view-options',
	__('View Options', 'radykal'),
	__('Here you can adjust the options for a single view. This allows, among other things to use different prices in different views.', 'radykal')
);

?>

<table class="form-table radykal-settings-form">
	<tbody>

		<?php

		radykal_output_option_item( array(
				'id' => 'stage_width',
				'title' => 'Canvas Width',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas width from UI-Layout/Product Options', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'id' => 'stage_height',
				'title' => 'Canvas Height',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas height from UI-Layout/Product Options', 'radykal')
			)
		);

		radykal_output_option_item( array(
				'id' => 'designs_parameter_price',
				'title' => 'Custom Image Price',
				'type' => 'number',
				'placeholder' => fpd_get_option( 'fpd_designs_parameter_price' ),
				'description' => __('This price will be used for custom added images.', 'radykal')
			)
		);

		radykal_output_option_item( array(
				'id' => 'custom_texts_parameter_price',
				'title' => 'Custom Text Price',
				'type' => 'number',
				'placeholder' => fpd_get_option( 'fpd_custom_texts_parameter_price' ),
				'description' => __('This price will be used for custom added images.', 'radykal')
			)
		);

		radykal_output_option_item( array(
				'id' => 'max_price',
				'title' => 'Maximum View Price',
				'type' => 'number',
				'placeholder' => -1,
				'description' => __('The maximum price that will be charged for the view. -1 will disable this option.', 'radykal')
			)
		);

		radykal_output_option_item( array(
				'type' => 'section_title',
				'title' => 'Disable certain custom media types',
			)
		);

		radykal_output_option_item( array(
				'id' => 'disable_image_upload',
				'title' => 'Disable Image Upload',
				'type' => 'checkbox',
				'default' => 'no'
			)
		);

		radykal_output_option_item( array(
				'id' => 'disable_custom_text',
				'title' => 'Disable Custom Text',
				'type' => 'checkbox',
				'default' => 'no'
			)
		);

		radykal_output_option_item( array(
				'id' => 'disable_designs',
				'title' => 'Disable Designs',
				'type' => 'checkbox',
				'default' => 'no'
			)
		);

		?>

	</tbody>
</table>

<?php
	FPD_Admin_Modal::output_footer(
		__('Set', 'radykal')
	);
?>