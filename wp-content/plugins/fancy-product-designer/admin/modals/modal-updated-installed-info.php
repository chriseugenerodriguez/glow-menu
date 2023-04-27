<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-updated-installed-info',
	'',
	'',
	'fpd-admin-modal-visible'
);

?>

<iframe src="<?php echo $info_url; ?>" frameborder="0"></iframe>

<?php
	FPD_Admin_Modal::output_footer();
?>