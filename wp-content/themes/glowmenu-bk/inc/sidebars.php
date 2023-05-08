<?php 

//Function for widget sidebars
if ( function_exists('register_sidebar') )
    register_sidebars(1,array(
	    'name' => 'Side Menu',
        'before_widget' => '<div class="widget-box clearfix %1$s">',
        'after_widget' => '</div>',
    ));
if ( function_exists('register_sidebar') )
    register_sidebars(1,array(
	    'name' => 'Footer Menu',
        'before_widget' => '<div class="widget-box clearfix %1$s">',
        'after_title' => '<svg class="arrow-down" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 17.7"><path fill-rule="evenodd" clip-rule="evenodd" d="M.5 3.9l13 12.9 13-12.9L23.6 1l-10 10-10-10L.5 3.9z"></svg></h2>',
        'after_widget' => '</div>',
    ));	 
	 

if ( function_exists('register_sidebar') )
    register_sidebars(1,array(
        'name' => 'WooCommerce Sidebar Widget Area',
        'before_widget' => '<div class="widget-box clearfix %1$s">',
        'after_widget' => '</div>',
    ));  

?>
