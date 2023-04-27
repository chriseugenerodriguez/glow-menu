<?php $meta = get_post_custom($post->ID); ?>
<?php if ( has_post_format( 'gallery' ) ): // Gallery ?>
  <?php $images = alx_post_images();  if ( !empty($images) ): ?>
  <script type="text/javascript">
			// Check if first slider image is loaded, and load flexslider on document ready
			jQuery(document).ready(function(){
						jQuery('#owl-<?php echo the_ID(); ?>').owlCarousel({
							nav: false,
							items: 1,
							dots: true,
							loop: false,
							autoplay: false,
							responsive: {
								600:{
							            items:1
							        }
							}
						});
			});
		</script>
	<?php endif; ?>
    <div class="owl-carousel" id="owl-<?php the_ID(); ?>">
        <?php foreach ( $images as $image ): ?>
        <div>
          <?php $imageid = wp_get_attachment_image_src($image->ID,'blog'); ?>
          <img src="<?php echo $imageid[0]; ?>" alt="<?php echo $image->post_title; ?>">
          <?php if ( $image->post_excerpt ): ?>
          <div class="image-caption"><?php echo $image->post_excerpt; ?></div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
<?php if ( has_post_format( 'image' ) ): // Image ?>
  <div class="image-container">
    <?php if ( has_post_thumbnail() ) {	
			 the_post_thumbnail('blog');
				$caption = get_post(get_post_thumbnail_id())->post_excerpt;
				if ( isset($caption) && $caption ) echo '<div class="image-caption">'.$caption.'</div>';	
	}; ?>
  </div>
<?php endif; ?>

<?php if ( has_post_format( 'video' ) ): // Video ?>
  <?php 
			if ( isset($meta['_video_url'][0]) && !empty($meta['_video_url'][0]) ) {
				global $wp_embed;
				$video = $wp_embed->run_shortcode('[embed]'.$meta['_video_url'][0].'[/embed]');
				echo $video;
			} elseif ( isset($meta['_video_embed_code'][0]) && !empty($meta['_video_embed_code'][0]) ) {
				echo $meta['_video_embed_code'][0];
			}
		?>
<?php endif; ?>

