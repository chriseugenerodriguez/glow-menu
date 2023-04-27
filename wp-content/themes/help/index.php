<?php get_header(); ?>

    <?php if ( have_posts() ) : ?>
    <div class="content">
      <?php while ( have_posts() ): the_post(); ?>
      <?php the_content(); ?>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
  <!--/.pad-->

<?php get_footer(); ?>
