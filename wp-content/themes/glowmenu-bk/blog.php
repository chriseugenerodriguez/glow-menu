<?php /* Template Name: Blog */ ?>
<?php get_header(); ?>

<section class="header-title paddingtop paddingbottom text-center">
	<h1><?php the_title(); ?></h1>
</section>  
<section class="container-fluid">
  <div class="paddingtop littlepaddingbottom">
    <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;?>
    <?php query_posts(array('posts_per_page'=> 12, 'paged'=>$paged)); ?>
    <?php if ($wp_query->have_posts()) : ?>
      <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
      <?php get_template_part('inc/post'); ?>
      <?php endwhile; ?>
    <?php echo pagination(); ?>
    <?php endif; ?>
  </div>
  <!--/.pad-->
  </section>
<!--/.content-->

<?php get_footer(); ?>