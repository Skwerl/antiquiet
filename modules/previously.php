<?php

if (in_category('sessions')) {
	$previously = array();
	$category = get_the_category(); 
	$category_posts = get_posts(array('numberposts'=>-1, 'category'=>$category[0]->cat_ID));
	foreach($category_posts as $other) {
		$previously[$other->ID] = array('url'=>get_permalink($other->ID), 'title'=>get_the_title($other->ID));
	}
	$header_text = 'All '.$category[0]->cat_name;
} else {
	if (false === ($previously = get_transient('previous-posts-post-'.$post->ID))) {
		$previously = get_internal_links(get_the_content(), array('antiquiet.com'));
		set_transient('previous-posts-post-'.$post->ID, $previously);
	}
	$header_text = 'Previously...';
}

if (!empty($previously)) {
	?><div class="previously">
		<div class="divider clear">&nbsp;</div>
		<h2 class="arvo"><?php echo $header_text; ?></h2>
		<ul><?php
			foreach($previously as $link) {
				echo '<li><a href="'.$link['url'].'">'.$link['title'].'</a></li>';
			}
		?></ul>
	</div><?php
}

?>