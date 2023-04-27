<?php get_header(); ?>
<?php /* Template Name: Title w/ Subtitle  */ ?>

<?php if(is_cart() || is_checkout() || is_account_page()){
$pbottom = 'littlepaddingbottom';
} else { 
$pbottom = 'paddingbottom';
} ?>

<section class="header-title paddingtop <?php echo $pbottom ?> text-center">
	<h3><?php echo get_post_meta(get_the_ID(), 'subtitle', true);?></h3>
	<h1><?php the_title(); ?></h1>
</section>  

<div class="clearfix">  
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
    <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
</div>
<!--/.content-->

<?php get_footer(); ?>