<?php get_header(); ?>

<div id="content_wrapper">
	<div id="article_wrapper">

		<div class="column left">
			<div class="article_body">
				<?php while (have_posts()) { the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<h3><span class="date">By <?php the_author_posts_link(); ?> at <?php the_time('g:i A l, F jS Y'); ?></span></h3>

						<div class="divider">&nbsp;</div>

						<h1 class="arvo"><span class="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span></h1>

						<h2 class="arvo">
							<span class="subjects"><?php aq_the_subjects($post->ID); ?></span>
						</h2>						

						<?php
						
						global $wp_query;
						if (isset($wp_query->query_vars['all'])) {
							$page = $numpages+1;
							aq_the_pages();
							echo apply_filters('the_content', $post->post_content);
							aq_the_pages(false);
						} else {
							aq_the_pages();
							the_content();
							aq_the_pages(false);
						}

						?>
					</article>

					<?php
					
					$main_post = $post;
					$main_post_id = $post->ID;
					
					$category_to_check = get_term_by('name', 'reviews', 'category');
					if (in_category('reviews') || post_is_in_descendant_category($category_to_check->term_id)) {

						if (false === ($album_id = get_transient('album-for-review-'.$post->ID))) {
							$artists = get_the_terms($post->ID,'artist');
							foreach ($artists as $artist) { $artist_ids[] = $artist->term_id; }
							$albums = get_posts(array(
								'numberposts' => -1,
								'tax_query' => array(array('taxonomy'=>'artist','field'=>'id','terms'=>$artist_ids)),
								'post_type' => 'album-page'
							));
							foreach ($albums as $post) {
								setup_postdata($post);
								$review = get_post_meta($post->ID, 'review-url', true);
								$review_id = url_to_postid($review);
								if ($review_id == $main_post_id) {
									$album_id = $post->ID;
									break;
								}
							}
							set_transient('album-for-review-'.$post->ID, $album_id);
						}
												
						if (!empty($album_id)) {
							echo '<div class="divider">&nbsp;</div>';
							$review_page = true;
							include(TEMPLATEPATH.'/modules/album.php');
						}

					}

					include(TEMPLATEPATH.'/modules/previously.php');

					include(TEMPLATEPATH.'/modules/zerg.php');

					$post = $main_post;
					setup_postdata($post);

					?>

					<div class="divider clear">&nbsp;</div>

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

			<?php include(TEMPLATEPATH.'/modules/recent.php'); ?>

			<?php include(TEMPLATEPATH.'/modules/sponsor.php'); ?>

		</div>

		<div class="clear"></div>

	</div>
</div>

<?php get_footer(); ?>