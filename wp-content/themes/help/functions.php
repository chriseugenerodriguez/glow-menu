<?php

function customize_og_author_metatag( $metatags ) {
    unset($metatags[1]);
    return $metatags;

}
add_filter( 'amt_schemaorg_metadata_head', 'customize_og_author_metatag', 10, 1 );

// WP ENQUEUE SCRIPTS / STYLES
function  main_scripts() {	

		wp_register_style( 'stylesheet', get_stylesheet_directory_uri(). '/style.css', '', 'all');
		wp_register_style( 'main-css', get_stylesheet_directory_uri(). '/dist/styles/main.css', '', 'all');
		wp_register_style( 'font-awesome', get_stylesheet_directory_uri(). '/assets/fonts/css/font-awesome.min.css', '', 'all');
		
		wp_enqueue_style('stylesheet');
		wp_enqueue_style('main-css');
		wp_enqueue_style('font-awesome');
				

	wp_register_script( 'jquery-plugins', get_template_directory_uri() . '/assets/scripts/plugins.js', array( 'jquery' ) );
	wp_register_script( 'jquery-init', get_template_directory_uri() . '/dist/scripts/main.js', array( 'jquery' ) );

	wp_enqueue_script( 'jquery-plugins' );
	wp_enqueue_script( 'jquery-init' );
}
add_action( 'wp_head', 'main_scripts' );

// Enable shortcode/
add_filter('the_content', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');

// add ie conditional html5 shim to header
function add_ie_html5_shim () {
	echo '<!--[if lt IE 9]>';
	echo '<script src="'. get_template_directory_uri() .'/assets/js/html5.js"></script>';
	echo '<![endif]-->';
}
add_action('wp_head', 'add_ie_html5_shim');	

// add ie 6-8 conditional selectivizr to header
function add_ie_selectivizr () {
	echo '<!--[if (gte IE 6)&(lte IE 8)]>';
	echo '<script src="'. get_template_directory_uri() .'/assets/js/selectivizr-min.js"></script>';
	echo '<![endif]-->';
}
add_action('wp_head', 'add_ie_selectivizr');	

// This theme uses wp_nav_menu() in one location.
register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'premiumwd' ),
));

// Enable Post Thumbnail 
add_theme_support( 'post-thumbnails' );

// Add Image Size 
add_image_size( 'attachment-shop_catalog',  690, 1109, true ); 

// Custom Excerpt
function custom_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more($more) {
		   global $post;
		return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

// Remove WordPress V.
function wpbeginner_remove_version() {
	return '';
}
add_filter('the_generator', 'wpbeginner_remove_version');

// Remove Query Strings
function _remove_script_version( $src ){
	$parts = explode( '?', $src );
	return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 ); 

// Defer parsing of JavaScript
function defer_parsing_of_js ( $url ) {
	if ( FALSE === strpos( $url, '.js' ) ) {
		return $url;
	}
	if ( strpos( $url, 'jquery.js' ) ) {
		return $url;
	}
	return "$url' defer ";
}
add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );

function remove_menus(){
	remove_menu_page( 'edit-comments.php' );          //Comments
}
add_action( 'admin_menu', 'remove_menus' );

function redirect() {
  if ( is_page('my-account') && !is_user_logged_in() ) {
      wp_redirect( home_url('/login') );
      die();
  }
}
add_action( 'wp', 'redirect' );


add_filter( 'get_the_archive_title', function ($title) {
    if ( is_category() ) {
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;
        }
    return $title;
});

function wpd_subcategory_template( $template ) {
    $cat = get_queried_object();
    if( 0 < $cat->category_parent )
        $template = locate_template( 'sub-category.php' );
    return $template;
}
add_filter( 'category_template', 'wpd_subcategory_template' );