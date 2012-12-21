<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php add_filter('show_admin_bar', '__return_false'); ?>
<script type="text/javascript" src="http://cdn.antiquiet.com/jw-player/player/jwplayer.js"></script>
</head>
<body style="margin: 0px; background-color: #000;">
	<?php

	$parent = get_post_field('post_parent', $_GET['media']);
	$video_url = get_permalink($parent);
	if ($video_url == get_permalink()) { $video_url = get_bloginfo('url'); }

	$atts = array(
		'width' => '560',
		'height' => '315',
		'stretching' => 'fill',
		'controlbar' => 'bottom',
		'mediaid' => $_GET['media'],
		'sharing.link' => $video_url,
		'plugins' => 'gapro-1,sharing-3',
	#	'plugins' => 'gapro-1,sharing-3,ltas',
	#	'ltas.cc' => 'kcibarzelnafsna',
		'autostart' => false,
		'logo.file' => 'http://cdn.antiquiet.com/jw-player/player/antiquiet.png',
		'logo.link'=> 'http://www.antiquiet.com',
		'logo.position' => 'bottom-right',
		'logo.over' => '1',
		'logo.out' => '0.8',
		'logo.timeout' => '8'
	);
	echo fix_jw_embed(jwplayer_handler($atts));
	
	?>
</body>
</html>