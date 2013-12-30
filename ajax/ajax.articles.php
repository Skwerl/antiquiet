<?php

require_once('ajax.enabler.php');

if ($limit) { $ppp = $limit; }
else { $ppp = intval(get_query_var('posts_per_page')); }

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$offset = ($paged-1)*$ppp;

$args = array();
$args['numberposts'] = 20;
$args['offset'] = $offset;
$get_dem_posts = get_posts($args);

foreach ($get_dem_posts as $post) {
	setup_postdata($post);
	?><div class="article_wrapper <?php aq_the_classes($post->ID); ?>">
		<div class="thumbnail"><a href="<?php the_permalink() ?>"><img src="<?php aq_the_thumbnail($post->ID); ?>" /></a></div>
		<div class="text"><a href="<?php the_permalink() ?>">
			<span class="date"><?php the_time('l, F jS Y'); ?></span>
			<span class="bands"><?php aq_the_subjects($post->ID, false); ?></span>
			<span class="title arvo"><?php the_title(); ?></span>
			<span class="subhead"><span class="category"><?php aq_the_categories($post->ID); ?>:</span> <?php echo aq_clean_periods(get_the_excerpt()); ?></span>
		</a></div>
		<div class="divider">&nbsp;</div>
	</div><?php
}

?>