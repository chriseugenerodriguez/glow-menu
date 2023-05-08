<?php
/*
 * Plugin Name: Popular Post Widget
 * Plugin URI: https://mostafizshamim.com/popular-post-widget/
 * Description: This is a simple widget plugin to show most popular posts of your WordPress website based on views.
 * Author: Mostafiz Shamim
 * Author URI: https://mostafizshamim.com
 * Version: 1.0.1
 * License: GPL2
**/

/*

    Copyright (C) 2016  Mostafiz  (mostafizsh@gmail.com) All rights reserved

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once('inc/pplr_post_script.php');
/*
 * set post views
 */
function set_popular_post_widget_views($postID) {
	$count_key = 'views';
	$count = get_post_meta($postID, $count_key, true);
	if($count==''){
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
	}else{
		$count++;
		update_post_meta($postID, $count_key, $count);
	}
}

/*
 * track post views
 */
function track_popular_post_widget_views ($post_id) {
	if ( !is_single() ) return;
	if ( empty ( $post_id) ) {
		global $post;
		$post_id = $post->ID;
	}
	set_popular_post_widget_views($post_id);
}
add_action( 'wp_head', 'track_popular_post_widget_views');


/**
 * define class to register widget
 */
class popular_post_widget extends WP_Widget {

	function __construct() {
		parent::__construct( 'popular_post_widget', 'Popular Posts By Mostafiz', array(
			'description' => 'The widget is to Show your most popular posts based on views'
		) );
	}


	//function to show front-end of the posts
	function widget( $args, $instance ) {

		$title = $instance['title'];


		echo $args['before_widget'];

		if ( isset( $title ) || ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}


		// The Query
		$plr_posts = new WP_Query( array(
			"posts_per_page" => 5,
			"post_type" => "post",
			"meta_key" => "views",
			"orderby" => "meta_value_num",
			"order" => "DESC",
			"ignore_sticky_posts" => true
		) );

		// The Loop
		if ( $plr_posts->have_posts() ) {
			echo '<ul>';
			while ( $plr_posts->have_posts() ) {
				$plr_posts->the_post();
				echo '<li>';
				echo '<a href="'.get_the_permalink().'">';
				echo  get_the_title();
				echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
		} else {
			echo 'Click or visit a post of your website to show it as popular post';
		}


		echo $args['after_widget'];
	}

	function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		?>

		<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
		<br>
		<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>"
		       value="<?php if ( isset( $title ) ) {
			       echo $title;
		       } ?>" id="<?php echo $this->get_field_id( 'title' ); ?>">
		<br>
		<br>

		<?php
	}
}


//register widget
function register_popular_post_widget() {
	register_widget( 'popular_post_widget' );
}

add_action( 'widgets_init', 'register_popular_post_widget' );


 