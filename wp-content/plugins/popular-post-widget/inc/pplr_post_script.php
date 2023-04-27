<?php

function popular_posts_scripts() {
	wp_enqueue_style( "stylesheet", plugins_url( "style.css", __FILE__ ), FALSE );
}

add_action( 'wp_enqueue_scripts', 'popular_posts_scripts' );