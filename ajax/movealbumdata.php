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

//print_r($album_posts);
/*
    [0] => WP_Post Object
        (
            [ID] => 54651
            [post_author] => 3
            [post_date] => 2013-10-27 07:25:51
            [post_date_gmt] => 2013-10-27 14:25:51
            [post_content] => 
            [post_title] => Melophobia
            [post_excerpt] => 
            [post_status] => publish
            [comment_status] => open
            [ping_status] => closed
            [post_password] => 
            [post_name] => melophobia
            [to_ping] => 
            [pinged] => 
            [post_modified] => 2013-10-27 07:27:22
            [post_modified_gmt] => 2013-10-27 14:27:22
            [post_content_filtered] => 
            [post_parent] => 0
            [guid] => http://antiquiet.staging.wpengine.com/?post_type=album-page&#038;p=54651
            [menu_order] => 0
            [post_type] => album-page
            [post_mime_type] => 
            [comment_count] => 0
            [filter] => raw
            [post_category] => 0
        )
*/

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

	if (!empty($review_id)) {
		echo 'Updating '.$review_id.': '.$review.'<br/>';
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