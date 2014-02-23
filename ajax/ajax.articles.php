<?php

require_once('ajax.enabler.php');

////////////* PASS STUFF TO THE AJAX LOOP */////////////////////////////////////////////////////////

$cat = $_POST['cat'];
if (empty($cat)) {
	$cat = get_query_var('cat') ? get_query_var('cat') : false;
}

$tag = $_POST['tag'];
if (empty($tag)) {
	$tag = get_query_var('tag') ? get_query_var('tag') : false;
}

$author = $_POST['author'];
if (empty($author)) {
	$author = get_query_var('author') ? get_query_var('author') : false;
}

$tax = $_POST['tax'];
if (empty($tax)) {
	$tax = get_query_var('taxonomy') ? get_query_var('taxonomy') : false;
}

$term = $_POST['term'];
if (empty($term)) {
	$term = get_query_var('term') ? get_query_var('term') : false;
}

$search = $_POST['search'];
if (empty($search)) {
	$search = get_query_var('s') ? get_query_var('s') : false;
}

if ($limit) { $ppp = $limit; }
else { $ppp = intval(get_query_var('posts_per_page')); }

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$offset = ($paged-1)*$ppp;

$args = array();
if (empty($search)) {
	$args['cat'] = $cat;
	$args['tag'] = $tag;
	$args['author'] = $author;
	if ($tax != $term) {
		$args['taxonomy'] = $tax;
		$args['term'] = $term;
	}
} else {
	$args['s'] = $search;
}

$args['numberposts'] = 20;
$args['offset'] = $offset;

$get_dem_posts = get_posts($args);

/*////////////////////////////////////////////////////////////////////////////////////////////////*/

foreach ($get_dem_posts as $post) {
	setup_postdata($post);
	if (get_cat_ID('reviews') == $args['cat']) {
		if (!in_category('movies')) {
			$release_title = get_post_meta($post->ID, 'release-title', true);		
			$release_art = get_post_meta($post->ID, 'release-cover', true);
			if (!empty($release_art)) { $thumbnail_image = wp_get_attachment_image($release_art, array(150,150)); }
			else { $thumbnail_image = '<img src="'.aq_get_thumbnail($post->ID).'" />'; }
			$rating = get_post_meta($post->ID, 'release-rating', true);
			?><div class="article_wrapper reviews <?php aq_the_classes($post->ID); ?>">
				<div class="thumbnail"><a href="<?php the_permalink() ?>"><?php echo $thumbnail_image; ?></a></div>
				<div class="text"><a href="<?php the_permalink() ?>">
					<span class="artist"><?php aq_the_subjects($post->ID, false, false); ?></span>
					<span class="album"><?php echo $release_title; ?></span>
					<span class="stars"><?php aq_see_stars($rating,'small'); ?></span>
					<span class="title arvo"><?php the_title(); ?></span>
					<span class="date"><?php echo get_the_author(); ?> at <?php the_time('g:i A l, F jS Y'); ?></span>
					<span class="blurb"><p><?php echo get_the_excerpt(); ?></p></span>
				</a></div>
			</div><?php
		}
	} else {
		$thumbnail_image = '<img src="'.aq_get_thumbnail($post->ID).'" />';
		?><div class="article_wrapper <?php aq_the_classes($post->ID); ?>">
			<div class="thumbnail"><a href="<?php the_permalink() ?>"><?php echo $thumbnail_image; ?></a></div>
			<div class="text"><a href="<?php the_permalink() ?>">
				<span class="subjects"><?php aq_the_subjects($post->ID, false); ?></span>
				<span class="title arvo"><?php the_title(); ?></span>
				<span class="date"><?php echo get_the_author(); ?> at <?php the_time('g:i A l, F jS Y'); ?></span>
				<span class="blurb"><p><?php echo get_the_excerpt(); ?></p></span>
			</a></div>
		</div><?php
	}
}

?>