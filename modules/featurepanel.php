<div id="feature_panel" class="shadow">
	<?php

	$panel_index = 0;
	$feature_thumbs = array();
	$feature_panels = array();

	remove_filter('get_the_excerpt', 'strip_excerpts');

	query_posts('meta_key=featurepanel_show&meta_value=1&posts_per_page=10');
	while (have_posts()) {
		the_post();
		$panel_index++;			
		$bigger_image = aq_get_thumbnail($post->ID, 'feature-panel');
		$little_image = aq_get_thumbnail($post->ID, 'itsy-bitsy');
		$feature_thumbs[] = '<div class="thumbnail shadow"><a href="#panel-'.$panel_index.'"><img src="'.$little_image.'" /></a></div>';
		$feature_panels[] = '<div id="panel-'.$panel_index.'" class="panel" href="'.get_permalink().'">
				<div class="text">
					<span class="title arvo">'.get_the_title().'</span>
					<span class="subhead"><span class="category">'.implode(', ',aq_get_categories($post->ID)).':</span> '.get_the_excerpt().'</span>
				</div>
				<div class="fadeout">&nbsp;</div>
				<div class="image"><img src="'.$bigger_image.'" /></div>
			</div>';
	}

	// regularly scheduled programming...
	add_filter('get_the_excerpt', 'strip_excerpts');
	
	?>
	<div class="thumbnails"><?php echo implode('',$feature_thumbs); ?></div>
	<div class="panels"><?php echo implode('',$feature_panels); ?></div>
</div>