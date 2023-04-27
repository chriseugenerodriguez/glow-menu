<?php get_header(); ?>
<?php /* Template Name: Pricing  */ ?>

<section class="header-title text-center paddingtop littlepaddingbottom">
  <h1>Simple, Flexible Pricing</h1>
  <h3>Customize visual aesthetics to see price.</h3>
</section>  
  <nav class="scrollbar">
     <div class="container">
      <div class="col-md-12">
         <ul class="scrollbar__features left col-md-5">
            <li><a href="#quantity">Quanity</a></li>
            <li><a href="#features">Features</a></li>
         </ul>
         
         <div class="right">
            <span class="scrollbar__estimated">Estimated Cost: <span class="scrollbar__estimated__cost">$0</span></span>
            <a class="scrollbar__button" href="/menus">Get Started</a>
         </div>
      </div>
     </div>
  </nav>
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

  <div class="container">
    <?php the_content(); ?>
  </div>

    <?php endwhile; // end of the loop. ?>
<!--/.content-->

<?php get_footer(); ?>