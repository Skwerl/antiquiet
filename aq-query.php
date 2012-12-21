<?php

function get_aq_posts($limit=false, $tags=false, $artists=false) {

	echo '<pre>';

	print_r($tags);
	print_r($artists);
	
	echo '</pre>';


	if ($limit) { $ppp = $limit; }
	else { $ppp = intval(get_query_var('posts_per_page')); }

	$paged = get_query_var('paged') ? get_query_var('paged') : 1;
	$offset = ($paged-1)*$ppp;

	$args = array();
	$args['numberposts'] = 20;
	$args['offset'] = $offset;

	$get_dem_posts = get_posts($args);

	return $get_dem_posts;


/*
	global $wpdb;
	global $permanent_categories;
	$permanent_categories = array(); // Will have to re-think "permanent" categories. Disabled here for now.
	
	$exclude_taxonomies = array();
	$include_taxonomies = array();
	$post_ids_exclude = array();
	$post_ids_include = array();

	$exclude_taxonomies = $taxonomy_filters[0];
	$include_taxonomies = $taxonomy_filters[1];
	
	if (isset($exclude_taxonomies['category'])) {
		$exclude_children = array();
		foreach ($exclude_taxonomies['category'] as $category) {
			$children = get_term_children($category, 'category');
			$exclude_children = array_merge($exclude_children, $children);	
		}
		$exclude_taxonomies['category'] = array_merge($exclude_taxonomies['category'], $exclude_children);
	}
	if (isset($include_taxonomies['category'])) {
		$include_children = array();
		foreach ($include_taxonomies['category'] as $category) {
			$children = get_term_children($category, 'category');
			$include_children = array_merge($include_children, $children);	
		}
		$include_taxonomies['category'] = array_merge($include_taxonomies['category'], $include_children);
	}
	
	if (!empty($exclude_taxonomies['category'])) {
		if (!is_array($include_taxonomies['category'])) { $include_taxonomies['category'] = array($include_taxonomies['category']); }
		if (!is_array($exclude_taxonomies['category'])) { $exclude_taxonomies['category'] = array($exclude_taxonomies['category']); }
		$include_taxonomies['category'] = array_diff($include_taxonomies['category'],$exclude_taxonomies['category']);
	}

	if (isset($exclude_taxonomies['genre'])) {
		$exclude_children = array();
		foreach ($exclude_taxonomies['genre'] as $genre) {
			$children = get_term_children($genre, 'genre');
			$exclude_children = array_merge($exclude_children, $children);	
		}
		$exclude_taxonomies['genre'] = array_merge($exclude_taxonomies['genre'], $exclude_children);
	}
	if (isset($include_taxonomies['genre'])) {
		$include_children = array();
		foreach ($include_taxonomies['genre'] as $genre) {
			$children = get_term_children($genre, 'genre');
			$include_children = array_merge($include_children, $children);	
		}
		$include_taxonomies['genre'] = array_merge($include_taxonomies['genre'], $include_children);
	}
	
	if (isset($exclude_taxonomies['tag'])) {
		$tags_to_exclude = $exclude_taxonomies['tag'];
		$post_ids = $wpdb->get_col("SELECT p.ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'artist' OR tt.taxonomy = 'post_tag') INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id AND t.term_id IN (".implode(',',$tags_to_exclude).")");
		$post_ids_exclude = array_merge($post_ids_exclude, $post_ids);
	}
	
	if (isset($exclude_taxonomies['query'])) {
		$temp = array();
		foreach ($exclude_taxonomies['query'] as $item) {
			$item_id = substr($item,1);
			$item_type = substr($item,0,1);
			if (is_numeric($item_id)) { array_push($temp, $item_id); }
		}
		$post_ids = $wpdb->get_col("SELECT p.ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'artist' OR tt.taxonomy = 'post_tag') INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id AND t.term_id IN (".implode(',',$temp).")"); $temp = $post_ids;
		$post_ids_exclude = $temp;
	}
	if (isset($include_taxonomies['query'])) {
		$temp = array();
		foreach ($include_taxonomies['query'] as $item) {
			$item_id = substr($item,1);
			$item_type = substr($item,0,1);
			if (is_numeric($item_id)) { array_push($temp, $item_id); }
		}
		$post_ids = $wpdb->get_col("SELECT p.ID FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND (tt.taxonomy = 'artist' OR tt.taxonomy = 'post_tag') INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id AND t.term_id IN (".implode(',',$temp).")"); $temp = $post_ids;
		$post_ids_include = $temp;
		$permanent_categories = array();
	}
	
	$show_nsfw = false;
	if (@in_array('nsfw', $include_taxonomies['xustom'])) { $show_nsfw = true; }
	if (@in_array('nsfw', $exclude_taxonomies['xustom'])) { $show_nsfw = false; }
	
	if ($limit) { $ppp = $limit; }
	else { $ppp = intval(get_query_var('posts_per_page')); }
	
	$paged = get_query_var('paged') ? get_query_var('paged') : 1;
	$offset = ($paged-1)*$ppp;
	
	$post_flag = get_query_var('post_flag') ? get_query_var('post_flag') : false;
	
	$querystr = "SELECT * FROM $wpdb->posts AS p";
	if ($post_flag && $post_flag != 'false') { $querystr .= "
			INNER JOIN  $wpdb->postmeta as postmeta 
				ON p.ID = postmeta.post_id
				AND postmeta.meta_key = '".$post_flag."'
				AND postmeta.meta_value = '1'
	"; }
	$querystr .= "
			INNER JOIN $wpdb->term_relationships AS cat_rel
				ON p.ID = cat_rel.object_id
			INNER JOIN $wpdb->term_taxonomy AS cat_tax
				ON cat_rel.term_taxonomy_id = cat_tax.term_taxonomy_id
				AND cat_tax.taxonomy = 'category'
			INNER JOIN $wpdb->terms AS category
				ON cat_tax.term_id = category.term_id
			LEFT JOIN $wpdb->term_relationships AS genre_rel
				ON p.ID = genre_rel.object_id
			LEFT JOIN $wpdb->term_taxonomy AS genre_tax
				ON genre_rel.term_taxonomy_id = genre_tax.term_taxonomy_id
				AND genre_tax.taxonomy = 'genre'
			LEFT JOIN $wpdb->terms AS genre
				ON genre_tax.term_id = genre.term_id
	";
	if (!$show_nsfw) { $querystr .= "
			LEFT JOIN wp_postmeta AS nsfw_meta
				ON p.ID = nsfw_meta.post_id
				AND nsfw_meta.meta_key = 'postflag_nsfw'
	"; }
	$querystr .= "
		WHERE (
			category.term_id IN (".@implode(',',@array_merge($permanent_categories, $include_taxonomies['category'])).") AND genre.term_id IN (".@implode(',',$include_taxonomies['genre']).")
			OR (category.term_id IN (".@implode(',',@array_merge($permanent_categories, $include_taxonomies['category'])).") AND p.ID NOT IN (
				SELECT ID FROM $wpdb->posts AS has_genre 
				LEFT JOIN $wpdb->term_relationships AS rel 
					ON has_genre.ID = rel.object_id 
				LEFT JOIN $wpdb->term_taxonomy AS tax 
					ON rel.term_taxonomy_id = tax.term_taxonomy_id 
					AND tax.taxonomy = 'genre' 
				WHERE has_genre.post_status = 'publish' 
				AND has_genre.post_type = 'post' 
				AND has_genre.post_date < NOW() 
				AND tax.term_id IS NOT NULL 			
			))
		)
		AND p.post_status = 'publish'
		AND p.post_type = 'post'
		AND p.post_date < NOW()
	";
	if (!empty($post_ids_exclude)) { $querystr .= "
		AND p.ID NOT IN (".implode(',',$post_ids_exclude).")
	"; }
	if (!empty($post_ids_include)) { $querystr .= "
		AND p.ID IN (".implode(',',$post_ids_include).")
	"; }
	if (!$show_nsfw) { $querystr .= "
		AND nsfw_meta.meta_value IS NULL
	"; }
	$querystr .= "
		GROUP BY p.ID
		ORDER BY p.post_date DESC
		LIMIT $ppp OFFSET $offset	
	";

	switch($output) {
		case 'JUST_IDS':
			$return = array();
			foreach ($wpdb->get_results($querystr, ARRAY_A) as $result) { $return[] = $result['ID']; }
			break;
		default:
			$return = $wpdb->get_results($querystr, OBJECT);
			break;
	}
		
	return $return;
*/

}

?>