<?php

require_once('ajax.enabler.php');

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

if ($limit) { $ppp = $limit; }
else { $ppp = intval(get_query_var('posts_per_page')); }

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$offset = ($paged-1)*$ppp;

$args = array();
$args['cat'] = $cat;
$args['tag'] = $tag;
$args['author'] = $author;
$args['numberposts'] = 20;
$args['offset'] = $offset;
if (!empty($taxonomy) && !empty($term)) {
	$args['taxonomy'] = $tax;
	$args['term'] = $term;
}
$get_dem_posts = get_posts($args);

foreach ($get_dem_posts as $post) {
	setup_postdata($post);
	?><div class="article_wrapper <?php aq_the_classes($post->ID); ?>">
		<div class="thumbnail"><a href="<?php the_permalink() ?>"><img src="<?php aq_the_thumbnail($post->ID); ?>" /></a></div>
		<div class="text"><a href="<?php the_permalink() ?>">
			<span class="subjects"><?php aq_the_subjects($post->ID, false); ?></span>
			<span class="title arvo"><?php the_title(); ?></span>
			<span class="date"><?php echo get_the_author(); ?> at <?php the_time('g:i A l, F jS Y'); ?></span>
			<span class="blurb"><?php echo get_the_excerpt(); ?></span>
		</a></div>
	</div><?php
}

?>