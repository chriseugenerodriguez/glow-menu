<?php $format = get_post_format(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('group'); ?>>
      <div class="image-container">
        <?php if ( has_post_thumbnail() ) { 
            the_post_thumbnail('blog-post'); 
            $caption = get_post(get_post_thumbnail_id())->post_excerpt;
            if ( isset($caption) && $caption ) echo '<div class="image-caption">'.$caption.'</div>';
          } ?>
      </div>
  <h2> <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a> </h2>
  <!--/.post-title-->
  
  <ul class="post-meta group">
    <li><?php the_category(' / '); ?></li>
  </ul>
  <!--/.post-meta-->
  
  <div class="post-inner">
    <div class="post-content pad">
      <div class="entry"> <?php the_excerpt();?> </div>
      <!--/.entry--> 
      <a class="more-link" href="<?php the_permalink(); ?>" rel="bookmark" ><span><i><?php _e('Read More'); ?></i></span></a> 
      </div>
    <!--/.post-content--> 
  </div>
  <!--/.post-inner--> 
  
</article>