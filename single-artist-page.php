<?php get_header(); ?>

<div id="content_wrapper" class="artist">
	<div id="article_wrapper">

		<div class="column left">
			<div class="article_body">
				<?php while (have_posts()) { the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<h1 class="arvo"><span class="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span></h1>

						<?php
						
						$artist_name = get_the_title();
						$artist_full_status = array();
						$artist_post_history = array();
						$artist_debut = get_post_meta($post->ID, 'debut', true);
						$artist_location = get_post_meta($post->ID, 'location', true);
						$artist_status = get_post_meta($post->ID, 'status', true);
						if (!empty($artist_debut)) {
							$artist_debut = 'Debuted '.$artist_debut;
							$artist_full_status[] = $artist_debut;
						}
						if (!empty($artist_status)) {
							$artist_status = 'Status: '.$artist_status;
							$artist_full_status[] = $artist_status;
						}
						$genres = wp_get_post_terms($post->ID, 'genre', array('fields' => 'names'));

						$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
						if (!empty($image[0])) {
							echo '<div class="image" style="width:'.$image[1].'px; height:'.$image[2].'px;"><img src="'.$image[0].'" /></div>';
						}

						if (!empty($genres)) { ?><div class="meta genre"><?php echo implode(', ',$genres); ?></div><?php }
						if (!empty($artist_location)) { ?><div class="meta location"><?php echo $artist_location; ?></div><?php }
						if (!empty($artist_full_status)) { ?><div class="meta status"><?php echo implode(', ',$artist_full_status); ?></div><?php }

						the_content();
						
						?>

					</article>

					<?php
					
					$main_post = $post;

					$artist_ids = array();
					$artists = get_the_terms($post->ID,'artist');
					foreach ($artists as $artist) { $artist_ids[] = $artist->term_id; }

					/* get albums */

					$aargs = array(
						'numberposts' => -1,
						'tax_query' => array(array('taxonomy'=>'artist','field'=>'id','terms'=>$artist_ids)),
						'post_type' => 'album-page',
						'meta_key' => 'release-date',
						'orderby' => meta_value
					);
					$album_posts = get_posts($aargs);
					if (!empty($album_posts)) {
						?><div class="divider clear">&nbsp;</div>
						<div id="main_content" class="album_list">
							<h2 class="header arvo"><?php echo $artist_name; ?> Albums</h2><?php
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
						'tax_query' => array(array('taxonomy'=>'artist','field'=>'id','terms'=>$artist_ids)),
						'post_type' => 'post'
					);
					$artist_posts = get_posts($pargs);
					if (!empty($artist_posts)) {
						?><div class="divider clear">&nbsp;</div>
						<div id="main_content" class="static_list">
							<h2 class="header arvo"><?php echo $artist_name; ?> Articles</h2><?php
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

		</div>

		<div class="clear"></div>

	</div>
</div>

<?php get_footer(); ?>