<?php

$release_art = get_post_meta($post->ID, 'release-cover', true);
$release_title = get_post_meta($post->ID, 'release-title', true);
$release_date = get_post_meta($post->ID, 'release-date', true);
$release_label = get_post_meta($post->ID, 'release-label', true);

$tracklist = get_post_meta($post->ID, 'release-tracklist', true);
$rating = get_post_meta($post->ID, 'release-rating', true);

if (!empty($release_title)) {


#$release_date = date($date_format,strtotime($release_date));


	$artists = get_the_terms($post->ID,'artist');
	$artist = array('name'=>$artists[0]->name,'id'=>$artists[0]->term_id);

	$artist_data = get_artist_data($artist['name']);
	$artist_page = $artist_data['url'];
	
	if (!empty($artist_page) && $artist_page != get_permalink($post->ID)) {
		$artist_display = '<a href="'.$artist_page.'">'.$artist['name'].'</a>';
	} else {
		$artist_display = $artist['name'];
	}

	?><div class="divider">&nbsp;</div>
	<div class="album_detail">

		<h2 class="arvo"><span class="artist"><?php echo $artist_display; ?></span></h1>
		<h1 class="arvo"><span class="headline"><a href="<?php the_permalink(); ?>"><?php echo $release_title; ?></a></span></h1>

		<div class="image"><?php echo wp_get_attachment_image($release_art, array(275,275)); ?></div><?php
		if (!empty($rating)) { ?><div class="meta rating"><?php aq_see_stars($rating,'big'); ?></div><?php }
		if (!empty($release_date)) { ?><div class="meta released">Released: <?php echo $release_date; ?></div><?php }
		if (!empty($release_label)) { ?><div class="meta label">Label: <?php echo $release_label; ?></div><?php }

		aq_format_tracklist($tracklist);
		
	?></div><?php
				
}

?>