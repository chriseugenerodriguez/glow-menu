<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-edit-product-options',
	__('Product Options', 'radykal'),
	''
);

?>

<table class="form-table">
	<tbody>

		<?php

		radykal_output_option_item( array(
				'id' => 'stage_width',
				'title' => 'Canvas Width',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas width from UI Layout', 'radykal'),
			)
		);

		radykal_output_option_item( array(
				'id' => 'stage_height',
				'title' => 'Canvas Height',
				'type' => 'number',
				'class' => 'large-text',
				'placeholder' => __('Canvas height from UI Layout', 'radykal')
			)
		);

		do_action( 'fpd_product_options_form_end' );

		?>

	</tbody>
</table>

<?php
	FPD_Admin_Modal::output_footer(
		__('Set', 'radykal')
	);
?>
