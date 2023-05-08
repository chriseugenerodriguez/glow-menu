<?php get_header(); ?>
<?php global $post;?>
  <div class="content">
    <article <?php post_class(); ?>>   
       <?php while ( have_posts() ): the_post(); ?>
             
        <div class="featured-image paddingtop littlepaddingbottom text-center" style="background-image: url('<?php echo the_post_thumbnail_url(); ?>');">
          <div class="container">
            <h1><?php the_title(); ?></h1>
            <ul class="post-meta">
              <li><?php echo get_the_author(); ?></li>
              <li><?php the_time('j M, Y'); ?></li>
            </ul>
          </div>
        </div>
        <div class="post-inner"> 
          <div class="post-content clearfix" itemprop="articleBody">
            <?php the_content(); ?>

            <?php if(is_attachment()): ?>
              <p class='resolutions'> Downloads: 
                <?php
                  $images = array();
                  $image_sizes = get_intermediate_image_sizes();
                  array_unshift( $image_sizes, 'full' );
                  foreach( $image_sizes as $image_size ) {
                    $image = wp_get_attachment_image_src( get_the_ID(), $image_size );
                    $name = $image_size . ' (' . $image[1] . 'x' . $image[2] . ')';
                    $images[] = '<a href="' . $image[0] . '">' . $name . '</a>';
                  }
                  echo implode( ' | ', $images );
                ?>
              </p>
            <?php endif; ?>
          </div>

          <?php endwhile; // end of the loop. ?>
          <?php echo get_the_tag_list('<div class="entry-tags"><span class="tags-title">Tags:</span> ',' ','</div>'); ?>
        
          <section id="email-cta">
            <?php echo do_shortcode('[mc4wp_form id="214"]'); ?>
          </section>

          <section id="author">
            <?php echo get_avatar(get_the_author_meta('user_email'),'128'); ?>
            <p> <span class="bold">About the Author:</span> <span><?php the_author_meta('description'); ?></span></p>
          </section>  

          <section id="related-posts">
            <?php include('inc/related-posts.php'); ?>
          </section>

          <?php comments_template(); ?>
      
        </div>


    </article> 

  </div>
<?php get_footer(); ?>