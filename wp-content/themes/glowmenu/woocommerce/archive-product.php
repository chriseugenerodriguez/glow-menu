<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); ?>
<div class="container-fluid content-header">
	<?php
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>
		<div class="col-lg-6">
			<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
				<h1 class="uppercase page-title"><?php woocommerce_page_title(); ?></h1>
			<?php endif; ?>

					<?php
						/**
						 * woocommerce_archive_description hook.
						 *
						 * @hooked woocommerce_taxonomy_archive_description - 10
						 * @hooked woocommerce_product_archive_description - 10
						 */
						do_action( 'woocommerce_archive_description' );
					?>

		</div>	
		<div class="col-lg-6">
			<div class="inputs-section hidden-md visible-lg">
		      <div class="field-text white buttoned">
		        <input id="menu-search" type="text" placeholder="Search designs..." value="" name="theme_name" hidefocus="true" style="outline: medium none;">
		      	<i class="fa fa-search"></i>
				</div>
		    </div>
	    </div>
</div>
<main class="container-fluid littlepaddingtop clearfix">
		
	<aside class="col-xl-1 col-lg-2 col-md-2 col-sm-2 xs-12 sidebar">
		<span class="visible-xs hidden-sm hidden-md filter">
			<h3>Select Filters</h3> 
		  	
		  	<?php get_template_part('inc/filter'); ?>
		</span>
  		
  		<div class="filter-wrapper">
  			<div class="filter-exit">';
  				<?php get_template_part('inc/exit'); ?>
  			</div>

    		<?php dynamic_sidebar( 'WooCommerce Sidebar Widget Area' ); ?>
    	
    	</div>
    </aside> 

	<div class="col-xl-11 col-lg-10 col-md-10 col-sm-10 col-xs-12 product-wrapper">
		
		<?php if ( have_posts() ) : ?>

			<div id="pageandresult">
				<?php
					/**
					 * woocommerce_before_shop_loop hook.
					 *
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
					do_action( 'woocommerce_before_shop_loop' );
				?>
			</div>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

		<?php
			/**
			 * woocommerce_after_main_content hook.
			 *
			 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
			 */
			do_action( 'woocommerce_after_main_content' );
		?>
	
	</div>

</main>


<?php get_footer( 'shop' ); ?>