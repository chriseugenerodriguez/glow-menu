<?php get_header(); ?>
<div class="help-bg">
  <div class="container">
    <div class="col col_8 paddingtop"><?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<p id="breadcrumbs">','</p>');} ?></div>
    <div class="col col_4 paddingtop"><?php echo do_shortcode('[do_widget id=search_live_widget-2]'); ?></div>
  </div>
</div>
<div class="littlemargintop content clearfix">
<div class="container">
  <section class="col col_8 littlepaddingtop littlepaddingbottom">
    <div class="content">
      <?php while ( have_posts() ): the_post(); ?>
      <article <?php post_class('group'); ?>>
        <h1><?php the_title(); ?></h1>
        <div class="entry container guide-list">
        <?php the_content(); ?>
        </div>
        <!--/.entry--> 
      </article>
      <?php endwhile; ?>
    </div>
    <!--/.pad--> 
  </section>
  <article class="sidebar col col_4 littlepaddingtop littlepaddingbottom">
  <?php echo get_sidebar('Docs & FAQ');?>
  </article>
</div></div>
<!--/.content-->
<?php get_footer(); ?>
