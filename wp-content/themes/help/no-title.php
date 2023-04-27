<?php /* Template Name: No Title */ ?>
<?php get_header(); ?>

<div class="container">
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
    <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
</div>
<?php get_footer(); ?>
