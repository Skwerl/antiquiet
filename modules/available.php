<?php

$today = date('Y-m-d');

$available_query = "SELECT *, pm2.meta_value as review
	FROM $wpdb->posts AS p
	INNER JOIN  $wpdb->postmeta as pm1 
		ON p.ID = pm1.post_id
		AND pm1.meta_key = 'release-date'
		AND pm1.meta_value <= '{$today}'
	INNER JOIN  $wpdb->postmeta as pm2 
		ON p.ID = pm2.post_id
		AND pm2.meta_key = 'review-url'
		AND pm2.meta_value != ''
	WHERE p.post_status = 'publish'
	AND p.post_type = 'album-page'
	AND p.post_date < NOW()
	GROUP BY p.ID
	ORDER BY pm1.meta_value DESC
	LIMIT 0,5
";

$albums = $wpdb->get_results($available_query, OBJECT);

?>

<div id="available_now" class="sidebar_module">
	<div class="header arvo">Available Now</div>
	<?php
	
	foreach ($albums as $post) {

		$review_url = $post->review;
		$review_id = url_to_postid($review_url);
		$review = get_post($review_id);
		setup_postdata($review);
		$authors = get_the_author();

		setup_postdata($post);
		$artists = get_the_terms($post->ID,'artist');
		$artist = array('name'=>$artists[0]->name,'id'=>$artists[0]->term_id);
		$cover = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'thumbnail');
		$rating = get_post_meta($post->ID, 'release-rating', true);

		?><div class="album_teaser">
			<a href="<?php echo $review_url; ?>">
				<div class="image"><img src="<?php echo $cover[0]; ?>" /></div>
				<div class="text">
					<div class="meta artist arvo"><?php echo $artist['name']; ?></div>
					<div class="meta album arvo"><?php the_title(); ?></div>
					<div class="meta author">Reviewed by <?php echo $authors; ?></div>
					<div class="divider">&nbsp;</div>
					<?php aq_see_stars($rating, 'small'); ?>
				</div>
			</a>
			<div class="clear">&nbsp;</div>
		</div><?php

	}
	
	?>
</div>