<?php

FPD_Admin_Modal::output_header(
	'fpd-modal-manage-categories',
	__('Manage Categories', 'radykal'),
	''
);

?>

<p class="description"><?php _e('Categories will be sorted alphabetically in the product designer!', 'radykal'); ?></p>

<ul id="fpd-design-categories-list">
<?php

	$categories = get_terms('fpd_design_category', array(
		'hide_empty' => false
	));

	$category_hierarchy = array();
	fpd_sort_terms_hierarchicaly($categories, $category_hierarchy);
	self::category_loop($category_hierarchy);

?>
</ul>
<button class="button-primary" id="fpd-add-new-design-category"><?php _e('Add new category', 'radykal'); ?></button>
<div class="fpd-ui-blocker"></div>

<?php
	FPD_Admin_Modal::output_footer();
?>