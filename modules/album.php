<?php

$release_art = get_post_meta($post->ID, 'release-cover', true);
$release_title = get_post_meta($post->ID, 'release-title', true);
$release_date = get_post_meta($post->ID, 'release-date', true);
$release_label = get_post_meta($post->ID, 'release-label', true);

$tracklist = get_post_meta($post->ID, 'release-tracklist', true);
$rating = get_post_meta($post->ID, 'release-rating', true);

if (!empty($release_title)) {

	$release_date = date('j/m/Y',strtotime($release_date));

	$artists = get_the_terms($post->ID,'artist');
	$artist = array('name'=>$artists[0]->name,'slug'=>$artists[0]->slug);
	$artist_display = '<a href="/artist/'.$artist['slug'].'">'.$artist['name'].'</a>';

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