<?php

/*
Plugin Name: Antiquiet Core
Description: Does some magical things...
Version:     1.0
Author:      Skwerl
Text Domain: ntqt_core

*/

//require_once(ABSPATH.'wp-admin/includes/image.php');
if (!class_exists('htmlparser_class')) { require_once('class-htmlparser.php'); }

function aq_featured_image_update($post_id, $attachment_id) {
	// I know there's a WP function for this, but this is quicker & cleaner.
	global $wpdb;
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = $post_id AND meta_key = '_thumbnail_id'");
	$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attachment_id));
}

function aq_set_featured_image($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
	if (!current_user_can('edit_post', $post_id)) { return; }
	remove_action('publish_post', 'aq_set_featured_image');		
	$post = get_post($post_id);   
	$first_image = '';
	if (preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches)) {
		$first_image = $matches[1][0];
	}
	if (!empty($first_image)) {	
		$get = wp_remote_get($first_image);
		$type = wp_remote_retrieve_header($get, 'content-type');
		$mirror = wp_upload_bits(rawurldecode(basename($first_image)), '', wp_remote_retrieve_body($get));
		$attachment = array(
			'post_title'=> basename($first_image),
			'post_mime_type' => $type
		);
		$attach_id = wp_insert_attachment($attachment, $mirror['file'], $post_id);
		$filepath = get_attached_file($attach_id,false);
		$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
		wp_update_attachment_metadata($attach_id, $attach_data);
		aq_featured_image_update($post_id, $attach_id);
	}
	return $attach_id;
}

add_filter('get_post_metadata', function ($value, $post_id, $meta_key, $single) {

	static $is_recursing = false;

	if (!$is_recursing && $meta_key === '_thumbnail_id') {
		$is_recursing = true;
		$value = get_post_thumbnail_id($post_id);
		$is_recursing = false;
		
		if (empty($value)) {

			if (wp_attachment_is_image($post_id)) {

				$value = $post_id;

			} else {

				global $wpdb;	
				$parser = new htmlparser_class;
	
				$post = $wpdb->get_row("SELECT post_content FROM $wpdb->posts WHERE ID = '$post_id';");
				$parser->InsertHTML($post->post_content);
				$parser->Parse();
				$result = $parser->GetElements($htmlCode);
				$images = $parser->getTagResource('img');
				/* Example:
				(
					[class] => alignnone size-large wp-image-57295
					[src] => http://cdn.antiquiet.com/wp-content/uploads/2014/06/jroddy_bandshot2-626x334.jpg
					[alt] => jroddy_bandshot2
					[width] => 626
					[height] => 334
				)
				*/
				if ($images == false) {
					$value = false;
				} else {
					$value = false;
					$first_image = $images[0];
					if (isset($first_image['class'])) {
						preg_match('/\swp-image-(\d+)/', $first_image['class'], $matches);
						if (!empty($matches)) {
							$value = $matches[1];
							aq_featured_image_update($post_id, $value);
						}
					} else {
						//$value = aq_set_featured_image($post_id);
					}
				}
	
				if (empty($value)) {
					// Default?
					$value = 57387;
				}
			
			}

		}

		if (!$single) {
			$value = array($value);
		}


	}

	return $value;

}, 10, 4);

//add_action('publish_post', 'aq_set_featured_image');

require_once('cleaners.php');

?>