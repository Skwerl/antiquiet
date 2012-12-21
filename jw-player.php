<?php

if (function_exists('jwplayer_init')) {

	function swap_embed($match) {
		$good_code = '<iframe src=\"'.get_bloginfo('url').'/embed/?media='.$match[3].'\" width=\"560\" height=\"315\" frameborder=\"0\" scrolling=\"no\"><\/iframe>';
		$replaced = '"'.$good_code.'"'.$match[2].',"mediaid":"'.$match[3].'"';
		return $replaced;
	}
	function fix_jw_embed($content) {
		$player_location = str_replace('/','\/',get_bloginfo('url').'/wp-content/uploads/jw-player-plugin-for-wordpress/player/');
		$player_redirect = str_replace('/','\/','http://cdn.antiquiet.com/jw-player/player/');
		$skin_location = str_replace('/','\/',get_bloginfo('url').'/wp-content/plugins/jw-player-plugin-for-wordpress/skins/');
		$skin_redirect = str_replace('/','\/','http://cdn.antiquiet.com/jw-player/skins/');
		$media_location = str_replace('/','\/',get_bloginfo('url').'/wp-content/uploads/');
		$media_redirect = str_replace('/','\/','http://cdn.antiquiet.com/wp-content/uploads/');
		$regex = '/\"(\[FIXME\])\"(,.*)?,\"mediaid\":\"(\d{1,})\"/i';
		$content = preg_replace_callback($regex, 'swap_embed', $content);
		$content = str_replace($player_location, $player_redirect, $content);
		$content = str_replace($skin_location, $skin_redirect, $content);
		$content = str_replace($media_location, $media_redirect, $content);
		return $content;
	}
	add_filter('the_content', 'fix_jw_embed', 999);

	function better_jwplayer_wp_head() {
		/* JW PLAYER CODE FROM JWMEDIAFUNCTIONS.PHP, JWPLAYER_WP_HEAD FUNCTION */
		global $post;
		if (!(is_single() || is_page()) || !get_option(LONGTAIL_KEY . "facebook")) return;
		$config_values = array();
		$attachment = null;
		$settings = array();
		$meta_header_id = get_post_meta($post->ID, LONGTAIL_KEY . "fb_headers_id", true);
		$meta_header_config = get_post_meta($post->ID, LONGTAIL_KEY . "fb_headers_config", true);
		if (empty($meta_header_id)) {
			return;
		} else if (is_numeric($meta_header_id)) {
			$attachment = get_post($meta_header_id);
			$title = $attachment->post_title;
			$description = $attachment->post_content;
			$thumbnail = get_post_meta($meta_header_id, LONGTAIL_KEY . "thumbnail_url", true);
			if (!isset($thumbnail) || $thumbnail == null || $thumbnail == "") {
				$image_id = get_post_meta($meta_header_id, LONGTAIL_KEY . "thumbnail", true);
				if (isset($image_id)) {
					$image_attachment = get_post($image_id);
					$thumbnail = !empty($image_attachment) ? $image_attachment->guid : "";
				}
			}
			$settings[] = "file=" . $attachment->guid;
		} else {
			$title = $post->post_title;
			$description = $post->post_excerpt;
			$thumbnail = "";
			$settings[] = "file=$meta_header_id";
		}
		if (!empty($meta_header_config) && $meta_header_config != "") {
			LongTailFramework::setConfig($meta_header_config);
		} else {
			LongTailFramework::setConfig(get_option(LONGTAIL_KEY . "default"));
		}
		$config_values = LongTailFramework::getConfigValues();
		$width = $config_values["width"];
		$height = $config_values["height"];
		foreach ($config_values as $key => $value) {
			$settings[] = "$key=$value";
		}
		$settings_string = htmlspecialchars(implode("&", $settings));
		$facebook_url = LongTailFramework::getPlayerURL();
		if ($settings_string) { $facebook_url .= "?$settings_string"; }
		$output = "";
	#	$output .= "<meta property='og:type' content='movie' />";
		$output .= "<meta property='og:video:width' content='$width' />";
		$output .= "<meta property='og:video:height' content='$height' />";
		$output .= "<meta property='og:video:type' content='application/x-shockwave-flash' />";
	#	$output .= "<meta property='og:title' content='" . htmlspecialchars($title) . "' />";
	#	$output .= "<meta property='og:description' content='" . htmlspecialchars($description) . "' />";
	#	$output .= "<meta property='og:image' content='$thumbnail' />";
		$output .= "<meta property='og:video' content='$facebook_url' />";
		/* END OF JW PLAYER CODE */
		$output = preg_replace('/&amp;sharing.code=\[FIXME\]/i', '', $output);
		echo $output;
	}

	remove_action("wp_head", "jwplayer_wp_head");
	add_action("wp_head", "better_jwplayer_wp_head");

}

?>