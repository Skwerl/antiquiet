<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php add_filter('show_admin_bar', '__return_false'); ?>
<script type="text/javascript" src="http://p.jwpcdn.com/6/5/jwplayer.js?ver=3.8.1"></script>
</head>
<body style="margin: 0px; background-color: #000;">

<style type="text/css">
	p {
		margin: 0px;
		padding: 0px;
	}
</style>

	<?php

	$parent = get_post_field('post_parent', $_GET['media']);
	$video_url = get_permalink($parent);
	if ($video_url == get_permalink()) { $video_url = get_bloginfo('url'); }

	$content = '[jwplayer mediaid="'.$_GET['media'].'" width="560" height="315"]';
	$formatted = apply_filters('the_content', $content);
	echo $formatted;

	?>

</body>
</html>