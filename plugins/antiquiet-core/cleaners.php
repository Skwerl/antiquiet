<?php

add_filter('get_the_excerpt', function($text) {
	$text = strip_shortcodes($text);
	$text = strip_tags($text);
	return $text;
}, 99);

add_filter( 'the_content', function($content) {
	$content = str_replace('<p>&nbsp;</p>', '', $content);
	return $content;
}, 99);

?>