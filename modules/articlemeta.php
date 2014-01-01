<div class="divider">&nbsp;</div>

<div class="articlemeta">
	
	<?php
	
	$author_twitter = get_the_author_meta('twitter', $userID);
	if (!empty($author_twitter)) {
		echo '<p class="author_twitter"><a class="twitter-follow-button" href="https://twitter.com/'.$author_twitter.'" data-show-count="false" data-size="large">Follow @'.$author_twitter.'</a></p>';
		?><script type="text/javascript">
			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
		</script><?php
	}
	
	$articlemeta_artists = array();
	$articlemeta_artists_tags = get_the_terms($post->ID,'artist');
	if (!empty($articlemeta_artists_tags)) {
		foreach($articlemeta_artists_tags as $articlemeta_artist) {
			$articlemeta_artists[] = '<a href="/artist/'.$articlemeta_artist->slug.'">'.$articlemeta_artist->name.'</a>';
		} 
		$articlemeta_artists = implode(', ',$articlemeta_artists);							
		?><p><span>Artists:<span> <?php echo $articlemeta_artists; ?></p><?php
	}

	$articlemeta_categories = array();
	$articlemeta_categories_cats = get_the_category($post->ID);	
	if (!empty($articlemeta_categories_cats)) {
		foreach($articlemeta_categories_cats as $articlemeta_category) {
			$articlemeta_category_url = str_replace('/./','/',get_category_link($articlemeta_category->cat_ID));
			$articlemeta_categories[] = '<a href="'.$articlemeta_category_url.'">'.$articlemeta_category->name.'</a>';
		}
		$articlemeta_categories = implode(', ',$articlemeta_categories);							
		?><p><span>Categories:<span> <?php echo $articlemeta_categories; ?></p><?php
	}

	$articlemeta_tags = array();
	$articlemeta_tags_tags = get_the_tags($post->ID);
	if (!empty($articlemeta_tags_tags)) {
		foreach($articlemeta_tags_tags as $articlemeta_tag) {
			$articlemeta_tags[] = '<a href="/tag/'.$articlemeta_tag->slug.'">'.$articlemeta_tag->name.'</a>';
		} 
		$articlemeta_tags = implode(', ',$articlemeta_tags);							
		?><p><span>Tags:<span> <?php echo $articlemeta_tags; ?></p><?php
	}

	?>

</div>