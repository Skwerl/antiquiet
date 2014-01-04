<?php

die();

require_once('ajax.enabler.php');

global $wpdb;

$aargs = array(
	'numberposts' => -1,
	'post_type' => 'album-page',
	'meta_key' => 'release-date'
);
$album_posts = get_posts($aargs);

foreach ($album_posts as $post) {

	$release_title = get_the_title($post->ID);	

	$rating = get_post_meta($post->ID, 'release-rating', true);	
	$release_date = get_post_meta($post->ID, 'release-date', true);	
	$tracklist = get_post_meta($post->ID, 'release-tracklist', true);
	$release_amazon = get_post_meta($post->ID, 'release-amazon', true);
	$release_itunes = get_post_meta($post->ID, 'release-itunes', true);
	$release_label = get_post_meta($post->ID, 'release-label', true);

	$review = get_post_meta($post->ID, 'review-url', true);
	$review_id = url_to_postid($review);

	$album_cover = get_post_thumbnail_id($post->ID);
	$fullsizepath = get_attached_file(album_cover);

	if (!empty($review_id)) {

		echo 'Updating '.$review_id.': '.$review.'<br/>';

		$metadata = wp_generate_attachment_metadata($album_cover, $fullsizepath);
		wp_update_attachment_metadata($album_cover, $metadata);

		$cover_attachment = get_post($album_cover);
		$cover_attachment->post_parent = $review_id;
		$move = wp_update_post($cover_attachment);
		add_post_meta($review_id, 'release-cover', $album_cover, true);

		add_post_meta($review_id, 'release-date', $release_date, true);
		add_post_meta($review_id, 'release-title', $release_title, true);
		add_post_meta($review_id, 'release-rating', $rating, true);
		add_post_meta($review_id, 'release-tracklist', $tracklist, true);
		add_post_meta($review_id, 'release-amazon', $release_amazon, true);
		add_post_meta($review_id, 'release-itunes', $release_itunes, true);
		add_post_meta($review_id, 'release-label', $release_label, true);

	}

}

?>