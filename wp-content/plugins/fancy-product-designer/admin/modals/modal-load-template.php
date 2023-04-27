<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-load-template',
	__('Load a template', 'radykal'),
	''
);

?>

<?php

	$no_templates_info = '<p>'.__('No templates created. You can create a template via the action bar in a product list item.', 'radykal').'</p>';

	if( fpd_table_exists(FPD_TEMPLATES_TABLE) ) {
		$templates = $wpdb->get_results("SELECT * FROM ".FPD_TEMPLATES_TABLE." ORDER BY title ASC");

		if(sizeof($templates) == 0)
			echo $no_templates_info;

		echo '<ul class="fpd-modal-list">';
		foreach($templates as $template) {
			// double quotes required
			echo FPD_Admin_Manage_Products::get_template_link_html($template->ID, $template->title);
		}
		echo '</ul>';

	}
	else {
		echo $no_templates_info;
	}


?>
</ul>
<?php

	FPD_Admin_Modal::output_footer();

?>
