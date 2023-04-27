<?php get_header(); ?>
<?php /* Template Name: No Title  */ ?>

<div class="clearfix grey">  
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
    <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
</div>
<!--/.content-->

<?php get_footer(); ?>