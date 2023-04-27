<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-load-demo',
	__('Download an example demo and import the zip', 'radykal'),
	''
);

?>

<ul class="fpd-modal-list">
<?php

	//load demos in a list
	$demo_url = FPD_PLUGIN_DIR . '/assets/json/demos.json';
	$json = fpd_admin_get_file_content($demo_url);
	$json = json_decode($json);

	if( $json ) {

		foreach($json as $key => $value) {
			echo '<li><a href="'.$value.'" class="button-primary">'.__('Download', 'radykal').'</a>'.$key.'</li>';
		}

	}

?>
</ul>

<?php
	FPD_Admin_Modal::output_footer();
?>