	<?php if ( is_search() ): ?>
		<h1 class="page-title">
			<?php $search_count = 0; $search = new WP_Query("s=$s & showposts=-1"); if($search->have_posts()) : while($search->have_posts()) : $search->the_post(); $search_count++; endwhile; endif; echo $search_count;?> 
			<?php _e('Results'); ?>: <span>"<?php echo get_search_query(); ?>"</span>
            <?php echo get_search_form(); ?>
		</h1>
	<?php elseif ( is_author() ): ?>
		<?php $author = get_userdata( get_query_var('author') );?>
		<h1 class="page-title"><?php _e('Author:'); ?> <span><?php echo $author->display_name;?></span></h1>
	<?php elseif ( is_category() ): ?>
		<h1 class="page-title"><?php _e('Category:'); ?> <span><?php echo single_cat_title('', false); ?></span></h1>
	<?php elseif ( is_tag() ): ?>
		<h1 class="page-title"><?php _e('Tag:'); ?> <?php echo single_tag_title('', false); ?></span></h1>
	<?php elseif ( is_day() ): ?>
		<h1 class="page-title"><?php _e('Day:'); ?> <span><?php echo get_the_time('F j, Y'); ?></span></h1>
	<?php elseif ( is_month() ): ?>
		<h1 class="page-title"><?php _e('Month:'); ?> <span><?php echo get_the_time('F Y'); ?></span></h1>
	<?php elseif ( is_year() ): ?>
		<h1 class="page-title"><?php _e('Year:'); ?> <span><?php echo get_the_time('Y'); ?></span></h1>
	<?php elseif ( has_post_format('gallery') ): ?>
		<h1 class="page-title"><span><?php _e('Gallery'); ?></span></h1>
	<?php elseif ( has_post_format('image') ): ?>
		<h1 class="page-title"><span><?php _e('Image'); ?></span></h1>
	<?php elseif ( has_post_format('video') ): ?>
		<h1 class="page-title"><span><?php _e('Video'); ?></span></h1>
	<?php endif; ?>