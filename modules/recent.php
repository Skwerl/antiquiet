<div id="recent_posts" class="sidebar_module">
	<div class="header arvo">Fresh Posts</div>
	<div class="recent_teaser">
		<?php
		
		rewind_posts();
		$args = array();
		$args['showposts'] = 5;
		$args['post__not_in'] = array($main_post_id);
		query_posts($args);
		while (have_posts()) { the_post();

			?><div class="cropper"><a href="<?php the_permalink(); ?>">
				<div class="image"><img src="<?php aq_the_thumbnail($post->ID); ?>" /></div>
				<div class="text">
					<div class="meta title arvo"><span class="category"><?php aq_the_categories($post->ID); ?>:</span> <?php the_title(); ?></div>
				</div>
			</a></div>
			<div class="clear">&nbsp;</div><?php
		
		}
		
		?>
	</div>
</div>