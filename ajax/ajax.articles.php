<?php

require_once('ajax.enabler.php');

if ($limit) { $ppp = $limit; }
else { $ppp = intval(get_query_var('posts_per_page')); }

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$offset = ($paged-1)*$ppp;

$args = array();
$args['numberposts'] = 20;
$args['offset'] = $offset;
$filter_array = array();
if (!empty($_POST['post_filters'])) {
	$custom_queries = json_decode(stripslashes($_POST['post_filters']));
	if (!empty($custom_queries)) {
		$tag_set = array();
		$artist_set = array();
		foreach ($custom_queries as $key => $filter_obj) {
			if (!empty($filter_obj[1])) {
				if (substr($key,0,1) == 'q') {
					if (substr($key,1,1) == 't') { $filter_array['tags'][] = substr($key,2); }	
					if (substr($key,1,1) == 'a') { $filter_array['artists'][] = substr($key,2); }	
				}
			}
		}
	}
	$args['tag__in'] = $filter_array['tags'];
	$args['tax_query'] = array(array('taxonomy' => 'artist', 'field' => 'id', 'terms' => $filter_array['artists']));
}

$get_dem_posts = get_posts($args);

foreach ($get_dem_posts as $post) {
	setup_postdata($post);
	$post_flags = aq_get_flags($post->ID);
	?><div class="article_wrapper <?php aq_the_classes($post->ID); ?>">
		<div class="thumbnail"><a href="<?php the_permalink() ?>"><img src="<?php aq_the_thumbnail($post->ID); ?>" /></a></div>
		<div class="text"><a href="<?php the_permalink() ?>">
			<span class="date"><?php the_time('l, F jS Y'); ?></span>
			<span class="bands"><?php aq_the_subjects($post->ID, false); ?></span>
			<span class="title arvo"><?php the_title(); ?></span>
			<span class="subhead"><span class="category"><?php aq_the_categories($post->ID); ?>:</span> <?php echo aq_clean_periods(get_the_excerpt()); ?></span>
			<?php if (!empty($post_flags)) {
				echo '<div class="filters">';
				foreach ($post_flags as $flag) { echo '<div class="cat_bubble article '.$flag.'"></div>'; }					
				echo '</div>';
			} ?>
		</a></div>
		<div class="divider">&nbsp;</div>
	</div><?php
}

?>