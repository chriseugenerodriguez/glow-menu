<?php

/*
Plugin Name: Custom Login glowmenu
Description: Login screen for all sites
Version:     1.0
Text Domain: custom-login-glowmenu
 */

function themeslug_enqueue_style() {
	wp_enqueue_style( 'core', plugin_dir_url( __FILE__ ) . 'custom-login/custom-login.css', false ); 
}

function themeslug_enqueue_script() {
	wp_enqueue_script( 'jquery-js', 'https://code.jquery.com/jquery-3.0.0.min.js', false );
	wp_enqueue_script( 'my-js', plugin_dir_url( __FILE__ ) . 'custom-login/login.js', false );

}


add_action( 'login_enqueue_scripts', 'themeslug_enqueue_style', 10 );
add_action( 'login_enqueue_scripts', 'themeslug_enqueue_script', 1 );

?>