<?php get_header(); ?>

<div class="container">
  <nav class="sub-nav">
    <?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div class="breadcrumbs">','</div>');} ?>
    <?php echo do_shortcode('[do_widget id=search_live_widget-3]'); ?>
  </nav>

  <?php the_archive_title( '<h1 class="page-header">', '</h1>' );?>

    <?php
      $category = get_the_category();
      $cat_id = $category[0]->parent;   
      $args = array('parent' => $cat_id);
      $categories = get_categories( array( 'child_of' => $cat_id ) ); 
    ?>
  <div class="section-tree">

    <?php if (have_posts()) : 

      foreach ( $categories as $category ){

          echo '<section class="section">';
          echo '<h3><a href="'.$category->slug.'">'. $category->name . '</a></h3>';
          echo '<ul>';

          $sub_args = array(

            'posts_per_page'=> 5,
            'type'          => 'post',
            'category'        => $category->term_id, // get child categories
            'orderby'       => 'name',
            'order'         => 'ASC',
            'hierarchical'  => 1,
            'pad_counts'    => 0
          );

          $posts = get_posts( $sub_args );

          foreach ( $posts as $post ){
            setup_postdata( $post ); ?>

            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

          <?  }

          echo '</ul>';
          echo '</section>';          
      } 
    ?>

  <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>