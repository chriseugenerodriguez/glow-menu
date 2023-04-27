<?php /* Template Name: 404 */ ?>
<?php get_header(); ?> 

<div class="container">
<section class="twelve columns center">
<?php while ( have_posts() ): the_post(); ?>
<h1 class="post-title pad">
  <?php _e('404') ?>
</h1>
<div class="post-inner">
<div class="post-content pad">
<div class="entry">
<div class="icon-exclamation"> <i class="fa fa-exclamation-circle"></i></div>
<div id="error-message">
  <?php _e('Nothing Found') ?>
</div>
<p>
  <?php _e( 'It looks like nothing was found at this location. Maybe try a search?' ); ?>
</p>
<?php get_search_form(); ?>
<?php endwhile; ?>
</section>

<!--/.content-->

<?php get_footer(); ?>
