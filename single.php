<?php get_header(); ?>

<div id="content_wrapper">
	<div id="article_wrapper">

		<div class="column left">
			<div class="article_body">
				<?php while (have_posts()) { the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

						<h3><span class="date">By <?php the_author_posts_link(); ?> at <?php the_time('g:i A l, F jS Y'); ?></span></h3>

						<?php

						$updated_date = get_post_meta($post->ID, 'postmeta_updated_date', true);
						$updated_time = get_post_meta($post->ID, 'postmeta_updated_time', true);
						$date_string = $updated_date;
						$date_format = 'l, F jS Y';

						if (!empty($updated_time)) {
							$date_string .= ' '.$updated_time;
							$date_format = 'g:i A l, F jS Y';
						}

						if (!empty($updated_date)) {
							?><h4><span class="date">Updated <?php echo date($date_format,strtotime($date_string)); ?></span></h4><?php
						}
						
						?>

						<div class="divider">&nbsp;</div>

						<h1 class="arvo"><span class="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span></h1>

						<h2 class="arvo">
							<span class="subjects"><?php aq_the_subjects($post->ID); ?></span>
						</h2>						

						<div class="divider">&nbsp;</div>

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

						$main_post_id = $post->ID;

						?>

						<?php include(TEMPLATEPATH.'/modules/articlemeta.php'); ?>

					</article>

					<?php

					$category_to_check = get_term_by('name', 'reviews', 'category');
					if (in_category('reviews') || post_is_in_descendant_category($category_to_check->term_id)) {
						include(TEMPLATEPATH.'/modules/album.php');
					}

					include(TEMPLATEPATH.'/modules/previously.php');

					include(TEMPLATEPATH.'/modules/zerg.php');

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

			<?php include(TEMPLATEPATH.'/modules/recent.php'); ?>

			<?php #include(TEMPLATEPATH.'/modules/sponsor.php'); ?>

		</div>

		<div class="clear"></div>

	</div>
</div>

<?php get_footer(); ?>