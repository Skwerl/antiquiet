<?php

require_once('ajax.enabler.php');

$query = $_GET['q'];
$tax_a_prep = array();
$tax_t_prep = array();

$results = $wpdb->get_results($wpdb->prepare("SELECT t.name, t.slug, t.term_id, tt.taxonomy as type FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE (tt.taxonomy = 'post_tag' || tt.taxonomy = 'artist') AND t.name LIKE (%s)", '%'.like_escape($query).'%'));

foreach ($results as $result) {
	$image = false;
	if ($result->type == 'artist') {
		if (false === ($artist_image = get_transient('artist-image-'.$result->term_id))) {
			$artist_page_args = array('numberposts' => 1,
				'post_type' => 'artist-page',
				'tax_query' => array(array('taxonomy'=>'artist',
					'field' => 'id',
					'terms' => $result->term_id
				))
			);
			$artist_page = get_posts($artist_page_args);
			$artist_page = $artist_page[0]->ID;
			$artist_image = wp_get_attachment_image_src(get_post_thumbnail_id($artist_page), 'artist-thumb');
			$artist_image = $artist_image[0]; 
			set_transient('artist-image-'.$result->term_id, $artist_image);
		}
		$image = $artist_image;
	}
	$result->image = $image;
	switch ($result->type) {
		case 'artist': $tax_a_prep[$result->name] = $result; break;
		case 'post_tag': $tax_t_prep[$result->name] = $result; break;
	}
}

$results = array_values(array_merge($tax_t_prep, $tax_a_prep));
function reorder_by_smart_slug($a, $b) {
	return strcmp(smart_slug($a->name), smart_slug($b->name));
} usort($results, 'reorder_by_smart_slug');

$output = array();
foreach ($results as $result) {
	switch ($result->type) {
		case 'artist': $type = 'a'; break;
		case 'post_tag': $type = 't'; break;
	}
	$output[] = array(
		'id' => $type.$result->term_id,
		'name' => $result->name,
		'slug' => $result->slug,
		'type' => $result->type,
		'img' => $result->image
	);
}

echo json_encode($output);	

?>