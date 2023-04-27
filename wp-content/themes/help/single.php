<?php get_header(); ?>
<div class="container">
  <nav class="sub-nav">
    <?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div class="breadcrumbs">','</div>');} ?>
    <?php echo do_shortcode('[do_widget id=search_live_widget-3]'); ?>
  </nav>

  <div class="row">
    <div class="col_9 col">
        <div class="content">
          <?php while ( have_posts() ): the_post(); ?>
            <h1><?php the_title(); ?></h1>
            <div class="entry">
              <?php the_content(); ?>
            </div>
          <?php endwhile; ?>
          <div class="date">Updated on <?php echo the_date();?></div>
          <div class="article-vote">
            <span class="article-vote-question">Was this article helpful?</span>
            <?php echo do_shortcode('[thumbs-rating-buttons]'); ?>
          </div>
        </div> 
    </div>

    <div class="col_3 col sidebar">
      <?php if ( function_exists( "get_yuzo_related_posts" ) ) { get_yuzo_related_posts(); } ?>
    </div>
  </div>

</div>

<?php get_footer(); ?>