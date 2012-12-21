<?php

$found_artists = find_artists($post->ID);

foreach ($found_artists as $artist) {
	if (!empty($artist['post_id'])) {
		$artist_full_status = array();
		$artist_post_history = array();
		$artist_debut = $artist['debut'];
		$artist_location = $artist['location'];
		$artist_status = $artist['status'];
		if (!empty($artist_debut)) {
			$artist_debut = 'Debuted '.$artist_debut;
			$artist_full_status[] = $artist_debut;
		}
		if (!empty($artist_status)) {
			$artist_status = 'Status: '.$artist_status;
			$artist_full_status[] = $artist_status;
		}
		$genres = wp_get_post_terms($artist['post_id'], 'genre', array('fields' => 'names'));
		$album_args = array('numberposts' => -1,
			'tax_query' => array(array('taxonomy'=>'artist','field'=>'id','terms'=>$artist['term_id'])),
			'post_type' => 'album-page'
		);
		$album_count = sizeof(get_posts($album_args));
		if (!empty($album_count)) {
			if ($album_count > 1) { $album_count = $album_count.' Albums'; }
			else { $album_count = $album_count.' Album'; }
			$artist_post_history[] = $album_count;
		}
		$posts_args = array('numberposts' => -1,
			'tax_query' => array(array('taxonomy'=>'artist','field'=>'id','terms'=>$artist['term_id'])),
			'post_type' => 'post'
		);
		$posts_count = sizeof(get_posts($posts_args));
		if (!empty($posts_count)) {
			if ($posts_count > 1) { $posts_count = $posts_count.' Posts'; }
			else { $posts_count = $posts_count.' Post'; }
			$artist_post_history[] = $posts_count;
		}
		?><div class="artist_teaser">
			<a href="<?php echo $artist['url']; ?>">
				<div class="image"><img src="<?php aq_the_thumbnail($artist['post_id']); ?>" /></div>
				<div class="header arvo"><?php echo $artist['name']; ?></div>
				<?php if (!empty($genres)) { ?><div class="meta genre"><?php echo implode(', ',$genres); ?></div><?php } ?>
				<?php if (!empty($artist_location)) { ?><div class="meta location"><?php echo $artist_location; ?></div><?php } ?>
				<?php if (!empty($artist_full_status)) { ?><div class="meta status"><?php echo implode(', ',$artist_full_status); ?></div><?php } ?>
				<?php if (!empty($artist_post_history)) { ?><div class="meta posts"><?php echo implode(', ',$artist_post_history); ?></div><?php } ?>
			</a>
		</div><?php
	}
}

?>