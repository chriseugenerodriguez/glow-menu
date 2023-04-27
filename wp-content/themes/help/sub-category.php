<?php get_header(); ?>
<div class="container">
  <nav class="sub-nav">
    <?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div class="breadcrumbs">','</div>');} ?>
    <?php echo do_shortcode('[do_widget id=search_live_widget-3]'); ?>
  </nav>

	<?php the_archive_title( '<h1 class="page-header">', '</h1>' );?>
	<ul class="archive-list">
	<?php if (have_posts()) : ?>

	<?php 
		$categories = get_the_category();
		$category_id = $categories[0]->cat_ID;

		$args = array('category' => $category_id, 'post_type' =>  'post' ); 
    	$postslist = get_posts( $args );    
    	foreach ($postslist as $post) :  setup_postdata($post); 
    ?>  
    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li> 
 
	<?php endforeach; 
	wp_reset_postdata();?> 

	<?php endif; ?>
	</ul>

</div>
<?php get_footer(); ?>