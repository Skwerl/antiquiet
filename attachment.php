<?php get_header(); ?>

<div id="content_wrapper">
	<div id="article_wrapper">

		<div class="column left">
			<div class="article_body">
				<?php while (have_posts()) { the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<h1 class="arvo"><span class="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span></h1>
						<h2 class="arvo"><span class="headline">From: <a href="<?php echo get_permalink($post->post_parent); ?>"><?php echo get_the_title($post->post_parent); ?></a></span></h1>

						<?php
						
						$all_attachments = array_values(get_children(array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID')));
						
						foreach ($all_attachments as $k => $attachment) { if ($attachment->ID == $post->ID) { break; } }
						$next = $k+1;
						$prev = $k-1;
						$last = count($all_attachments)-1;
						
						if (count($all_attachments) > 1) {
							if (isset($all_attachments[$prev])) {
								$prev_attachment_url = get_attachment_link($all_attachments[$prev]->ID);
							} else {
								$prev_attachment_url = get_attachment_link($all_attachments[$last]->ID);
							}
							if (isset($all_attachments[$next])) {
								$next_attachment_url = get_attachment_link($all_attachments[$next]->ID);
							} else {
								$next_attachment_url = get_attachment_link($all_attachments[0]->ID);
							}
						} else {
							$prev_attachment_url = wp_get_attachment_url();
							$next_attachment_url = wp_get_attachment_url();
						}

						$dimensions = '';
						$wp_image_meta = wp_get_attachment_metadata();
						$image_width = $wp_image_meta['sizes']['large']['width'];
						$image_height = $wp_image_meta['sizes']['large']['height'];

						// fix old upload weirdness
						$actual_file = wp_get_attachment_image_src($post->ID, 'large');
						@$actual_size = getimagesize($actual_file[0]);
						if ($actual_size) {
							if (empty($image_width)) { $image_width = $actual_size[0]; }
							if (empty($image_height)) { $image_height = $actual_size[1]; }
							if ($actual_size[0] != $image_width || $actual_size[1] != $image_height) {
								$ratio = min($image_width/$actual_size[0], $image_height/$actual_size[1]);
								echo $ratio;
								$image_width = floor($ratio * $image_width);
								$image_height = floor($ratio * $image_height);
							}
							if ($image_width > 626) {
								$ratio = 626/$image_width;
								$image_width = floor($ratio * $image_width);
								$image_height = floor($ratio * $image_height);
							}
							$dimensions = 'width: '.$image_width.'px; height: '.$image_height.'px;';
						}
						
						?>

						<div class="big-image" style="<?php echo $dimensions; ?>">
							
							<div class="arrow left"><a href="<?php echo $prev_attachment_url; ?>"></a></div>
							<div class="arrow right"><a href="<?php echo $next_attachment_url; ?>"></a></div>
							
							<p class="attachment"><?php echo wp_get_attachment_image($post->ID, 'large'); ?></p>

							<div class="download"><a href="<?php echo wp_get_attachment_url($post->ID); ?>" class="tip" title="Download Image"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/download.png" /></a></div>

							<div class="pinterest"><a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink($post->post_parent)); ?>&media=<?php echo urlencode(wp_get_attachment_url($post->ID)); ?>&description=<?php echo urlencode(get_the_title()); ?>" class="pin-it-button" count-layout="vertical"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>

						</div>

						<script type="text/javascript">
						
							$(document).ready(function() {
								$(".arrow a").hide().append("<div />");
								$(".arrow").hover(
									function() { $(this).find("a").fadeIn("fast"); },
									function() { $(this).find("a").fadeOut("fast"); }
								);
							});

						</script>

						<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>

					</article>
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