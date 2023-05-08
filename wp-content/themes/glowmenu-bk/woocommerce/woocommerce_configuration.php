<?php

// Adds theme support for woocommerce 
add_theme_support('woocommerce');

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

add_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 20 );

function menu_desc(){
	$host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	if($host == 'glowmenus.com/menus/') {
		echo '<div class="term-description">';
		echo '<p>Our beautifully-designed menus come with a customizeable user interface. Every menu is just a starting point—you can style it to look any way you want.</p>';
		echo '</div>';
	}
}
add_action('woocommerce_archive_description', 'menu_desc', 10);


function woocommerce_template_loop_second_product_thumbnail() {
	$uri = $_SERVER['REQUEST_URI'];
	$url = 'http://' . $uri;
	if (strpos($url, "menus") == true ) {
		global $product, $woocommerce;
			  $attachment_ids = $product->get_gallery_attachment_ids();
			if ( $attachment_ids ) {
				$secondary_image_id = $attachment_ids['0'];
				echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'secondary-image attachment-shop-catalog' ) );
			}
		}
	}
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_second_product_thumbnail', 11 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
	

remove_action('woocommerce_pagination', 'woocommerce_pagination', 10);
function woocommerce_pagination() {
    pagination();      
}
add_action( 'woocommerce_pagination', 'woocommerce_pagination', 10);

function woocommerce_quantity_input_args( $args, $product ) {
	if ( is_singular( 'product' ) ) {
		$args['input_value'] 	= 20;	// Starting value (we only want to affect product pages, not cart)
	}
	$args['max_value'] 	= 200; 	// Maximum value
	$args['min_value'] 	= 20;   	// Minimum value
	$args['step'] 		= 10;    // Quantity steps
	return $args;
}
// Simple products
add_filter( 'woocommerce_quantity_input_args', 'woocommerce_quantity_input_args', 10, 2 );



function hide_coupon_field_on_checkout( $enabled ) {
 
	if ( is_checkout() ) {
		$enabled = false;
	}
 
	return $enabled;
}
add_filter( 'woocommerce_coupons_enabled', 'hide_coupon_field_on_checkout' );


// Menu Category URL fix
add_filter('rewrite_rules_array', function( $rules ) 
{
	$new_rules = array(
	'menus/([^/]*?)/page/([0-9]{1,})/?$' => 'index.php?product_cat=$matches[1]&paged=$matches[2]',
	'menus/([^/]*?)/?$' => 'index.php?product_cat=$matches[1]',
	);
	return $new_rules + $rules;
});



function custom_pre_get_posts_query( $q ) {

	if ( ! $q->is_main_query() ) return;
	if ( ! $q->is_post_type_archive() ) return;
	
	if ( ! is_admin() && is_shop() ) {

		$q->set( 'tax_query', array(array(
			'taxonomy' => 'product_cat',
			'field' => 'slug',
			'terms' => array( 'custom-menus' ), // Don't display products in the knives category on the shop page
			'operator' => 'NOT IN'
		)));
	
	}

	remove_action( 'pre_get_posts', 'custom_pre_get_posts_query' );

}	
add_action( 'pre_get_posts', 'custom_pre_get_posts_query' );

 
function remove_sidebar_product_pages() {
	if (is_product()) {
		remove_action('woocommerce_sidebar','woocommerce_get_sidebar',10);
	}
}
add_action( 'wp', 'remove_sidebar_product_pages' );


function redirection_emptycart(){
    global $woocommerce;

    if( is_cart() && WC()->cart->cart_contents_count == 0){
        wp_redirect( home_url() ); 
        exit;
    }
}
add_action("template_redirect", 'redirection_emptycart');

// Remove Product Description
function remove_product_editor() {
  remove_post_type_support( 'product', 'editor' );
}
add_action( 'init', 'remove_product_editor' );


// Security Fix Customize Page 
function redirect_to_specific_page() {
	$host = $_SERVER['HTTP_HOST'];
	$uri = $_SERVER['REQUEST_URI'];
	$url = 'http://' . $host . $uri;
	if ( !is_user_logged_in() && strpos($url, "start_customizing") !== false ) {
		wp_redirect( strtok($uri,'?') ); 
		exit;
	}

	// redirect pdp for custom menus
	if ( (strpos($url, "start_customizing") == false && strpos($url, "/menus/custom-menus/") == true) && (strpos($url, "cart_item_key") == false) ) {
		
		wp_redirect('http://' . $host .'/menus/custom-menus/'); 
		exit;
	}	
}
// add_action( 'template_redirect', 'redirect_to_specific_page' );

// Add box fee to shipping amount
function endo_handling_fee() {
     global $woocommerce;
 
     if ( is_admin() && ! defined( 'DOING_AJAX' ) )
          return;
 
     $fee = 1.50;
     $woocommerce->cart->add_fee( 'Handling Fee', $fee, true, 'standard' );
}
add_action( 'woocommerce_cart_calculate_fees','endo_handling_fee' );


function pdp_get_started(){

        if (!is_user_logged_in()) {
        	$script = '<script>
        	jQuery(document).ready(function($){
        		$(".apple-pay-button, .apple-pay-button-checkout-separator, .apple-pay-button-wrapper, .single-product #theme-wrapper .product .product_info #fpd-start-customizing-button").remove();
        		$(".single-product #theme-wrapper .product .product_info .login-form").insertAfter(".single-product #theme-wrapper .product .product_info .cart .quantity")
        	});
        	</script>';
            echo "<a href='#' class='button login-form'>Get Started</a>";
        	echo $script;
        }
}
// add_action('woocommerce_single_product_summary', 'pdp_get_started', 30);


function disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99 ); ?>