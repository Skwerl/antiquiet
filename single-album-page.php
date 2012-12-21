<?php get_header(); ?>

<div id="content_wrapper" class="artist album">
	<div id="article_wrapper">

		<div class="column left">
			<div class="article_body">
				<?php while (have_posts()) { the_post(); ?>
					<?php

					$album_id = $post->ID;

					include(TEMPLATEPATH.'/modules/album.php');
					
					$main_post = $post;
					$review_post = false;

					/* get review */

					$review_id = url_to_postid($review);
					if (!empty($review_id) && is_numeric($review_id)) {
						$post = get_post($review_id);
						setup_postdata($post);
						$review_post = $post;
						?><div class="divider clear">&nbsp;</div>
						<div id="main_content" class="static_list">
							<h2 class="header arvo">Antiquiet Review</h2>
								<div class="article_wrapper <?php aq_the_classes($post->ID); ?>">
									<div class="thumbnail"><a href="<?php the_permalink() ?>"><img src="<?php aq_the_thumbnail($post->ID, 'artist-thumb'); ?>" /></a></div>
									<div class="text"><a href="<?php the_permalink() ?>">
										<span class="title arvo"><?php the_title(); ?></span>
										<span class="subhead"><span class="category"><?php aq_the_categories($post->ID); ?>:</span> <?php echo get_the_excerpt(); ?></span>
									</a></div>
								</div>
								<div class="clear shim">&nbsp;</div>
						</div><?php
					}

					/* get albums */

					$aargs = array(
						'numberposts' => -1,
						'tax_query' => array(array('taxonomy'=>'artist','field'=>'id','terms'=>$artist['id'])),
						'post_type' => 'album-page',
						'meta_key' => 'release-date',
						'orderby' => meta_value
					);
					$album_posts = get_posts($aargs);
					if (!empty($album_posts)) {
						?><div class="divider clear">&nbsp;</div>
						<div id="main_content" class="album_list">
							<h2 class="header arvo"><?php echo $artist['name']; ?> Albums</h2><?php
							foreach ($album_posts as $post) {
								setup_postdata($post);
								$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
								if (!empty($image[0])) {
									?><div class="album_cover <?php aq_the_classes($post->ID); ?>">
										<div class="thumbnail"><a href="<?php the_permalink() ?>"><img src="<?php echo $image[0]; ?>" /></a></div>
									</div><?php
								}
							}
							?><div class="clear shim">&nbsp;</div>
						</div><?php
					}

					/* get posts */

					$pargs = array(
						'numberposts' => -1,
						'tax_query' => array(array('taxonomy'=>'artist','field'=>'id','terms'=>$artist['id'])),
						'post_type' => 'post'
					);
					$artist_posts = get_posts($pargs);
					if (!empty($artist_posts)) {
						?><div class="divider clear">&nbsp;</div>
						<div id="main_content" class="static_list">
							<h2 class="header arvo"><?php echo $artist['name']; ?> Articles</h2><?php
							foreach ($artist_posts as $post) {
								setup_postdata($post);
								?><div class="article_wrapper <?php aq_the_classes($post->ID); ?>">
									<div class="thumbnail"><a href="<?php the_permalink() ?>"><img src="<?php aq_the_thumbnail($post->ID, 'artist-thumb'); ?>" /></a></div>
									<div class="text"><a href="<?php the_permalink() ?>">
										<span class="date"><?php the_time('l, F jS Y'); ?></span>
										<span class="title arvo"><?php the_title(); ?></span>
										<span class="subhead"><span class="category"><?php aq_the_categories($post->ID); ?>:</span> <?php echo get_the_excerpt(); ?></span>
									</a></div>
								</div>
								<div class="clear shim">&nbsp;</div><?php
							}
						?></div><?php
					}

					$post = $main_post;
						
					?>

					<div class="divider">&nbsp;</div>
					<div id="comments_wrapper">
						<?php comments_template(); ?>
					</div>

				<?php } ?>
			</div>
		</div>

		<div class="column right">

			<div class="ad_300x250"><?php include(TEMPLATEPATH.'/ads/gam_300x250.php'); ?></div>

			<?php include(TEMPLATEPATH.'/modules/sharing.php'); ?>

			<div class="promo_300x100"><?php include(TEMPLATEPATH.'/ads/gam_300x100.php'); ?></div>

			<?php include(TEMPLATEPATH.'/modules/artists.php'); ?>

		</div>

		<div class="clear"></div>

	</div>
</div>

<?php get_footer(); ?>