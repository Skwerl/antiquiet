<?php

if (isset($album_id)) {

	$artists = get_the_terms($post->ID,'artist');
	$artist = array('name'=>$artists[0]->name,'id'=>$artists[0]->term_id);

	$artist_data = get_artist_data($artist['name']);
	$artist_page = $artist_data['url'];
	
	if (!empty($artist_page) && $artist_page != get_permalink($post->ID)) {
		$artist_display = '<a href="'.$artist_page.'">'.$artist['name'].'</a>';
	} else {
		$artist_display = $artist['name'];
	}

	?><div class="album_detail">

		<h2 class="arvo"><span class="artist"><?php echo $artist_display; ?></span></h1>
		<h1 class="arvo"><span class="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span></h1>

		<?php
		
		$date_format = 'Y';
		if (isset($review_page)) { $date_format = 'n/d/y'; }
		
		$release_date = get_post_meta($post->ID, 'release-date', true);
		$release_date = date($date_format,strtotime($release_date));

		$release_label = get_post_meta($post->ID, 'release-label', true);
		$tracklist = get_post_meta($post->ID, 'release-tracklist', true);
		$genres = wp_get_post_terms($post->ID, 'genre', array('fields' => 'names'));

		$review = get_post_meta($post->ID, 'review-url', true);
		$rating = get_post_meta($post->ID, 'release-rating', true);

		$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'medium');
		if (!empty($image[0])) {
			?><a href="<?php the_permalink(); ?>"><div class="image" style="width: <?php echo $image[1]; ?>px; height:<?php echo $image[2]; ?>px;"><img src="<?php echo $image[0]; ?>" /></div></a><?php
		}
		if (!empty($rating)) { ?><div class="meta rating"><?php aq_see_stars($rating,'big'); ?></div><?php }
		if (!empty($genres)) { ?><div class="meta genre"><?php echo implode(', ',$genres); ?></div><?php }
		if (!empty($release_date)) { ?><div class="meta released">Released: <?php echo $release_date; ?></div><?php }
		if (!empty($release_label)) { ?><div class="meta label">Label: <?php echo $release_label; ?></div><?php }

		aq_format_tracklist($tracklist);
		
	?></div><?php
				
}

?>