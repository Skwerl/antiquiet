<?php

function get_aq_posts($limit=false, $tags=false, $artists=false) {

	#echo '<pre>';

	#print_r($tags);
	#print_r($artists);
	
	#echo '</pre>';


	if ($limit) { $ppp = $limit; }
	else { $ppp = intval(get_query_var('posts_per_page')); }

	$paged = get_query_var('paged') ? get_query_var('paged') : 1;
	$offset = ($paged-1)*$ppp;

	$args = array();
	$args['numberposts'] = 20;
	$args['offset'] = $offset;

	$get_dem_posts = get_posts($args);

	return $get_dem_posts;

}

?>