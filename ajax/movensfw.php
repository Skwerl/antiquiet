<?php

//die();

require_once('ajax.enabler.php');

global $wpdb;

$args = array(
	'numberposts' => -1,
	'meta_key' => 'postflag_nsfw',
	'meta_value' => '1'
);
$nsfw_posts = get_posts($args);

foreach ($nsfw_posts as $post) {

	wp_set_post_tags($post->ID, array('NSFW'), true);
	echo get_permalink($post->ID).'<br/>';

}

?>