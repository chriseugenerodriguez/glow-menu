<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function fpd_admin_display_version_info( $display_shortcode=false ) {

	$shortcode_button = '';
	if($display_shortcode)
		$shortcode_button = '<a href="#" class="button-secondary" id="fpd-shortcode-builder">'.__('Shortcodes', 'radykal').'</a>';

	echo '<div class="fpd-header-right">'.$shortcode_button.'<a href="http://support.fancyproductdesigner.com" target="_blank" class="button-primary">'.__('Support Center', 'radykal').'</a></div>';

}

function fpd_admin_get_file_content( $file ) {

	$result = false;
	if( function_exists('curl_exec') ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $file);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$result = curl_exec($ch);
		curl_close($ch);
	}

	//if curl does not work, use file_get_contents
	if( $result == false && function_exists('file_get_contents') ) {
		$result = @file_get_contents($file);
	}

	if($result !== false) {
		return $result;
	}
	else {
		return false;
	}

}

function fpd_admin_upload_image_to_wp( $name, $base64_image, $add_to_library = true ) {

	//upload to wordpress
	$upload = wp_upload_bits( $name, null, base64_decode($base64_image) );

	//add to media library
	if( $add_to_library && isset($upload['url']) ) {
		media_sideload_image( $upload['url'], 0 );
	}

	return $upload['error'] === false ? $upload['url'] : false;

}

function fpd_admin_get_all_fancy_products() {

	global $wpdb;

	if( fpd_table_exists(FPD_PRODUCTS_TABLE) ) {

		$products = $wpdb->get_results("SELECT * FROM ".FPD_PRODUCTS_TABLE." ORDER BY ID ASC");
		$products_arr = array();

		foreach($products as $product) {
			$products_arr[$product->ID] = $product->title;
		}

		return $products_arr;

	}
	else
		return array();

}

function fpd_admin_get_all_fancy_product_categories() {

	global $wpdb;

	if( fpd_table_exists(FPD_CATEGORIES_TABLE) ) {

		$categories = $wpdb->get_results("SELECT * FROM ".FPD_CATEGORIES_TABLE." ORDER BY ID ASC");
		$categories_arr = array();

		foreach($categories as $category) {
			$categories_arr[$category->ID] = $category->title;
		}

		return $categories_arr;

	}
	else
		return array();

}

function fpd_admin_delete_directory( $dir ) {

	$iterator = new RecursiveDirectoryIterator($dir);
	foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {

		if($file->getFilename() == '.' || $file->getFilename() == '..')
			continue;

		if ($file->isDir()) {
			@rmdir($file->getPathname());
	 	}
	 	else {
	    	@unlink($file->getPathname());
	 	}

   }

   @rmdir($dir);

}

?>