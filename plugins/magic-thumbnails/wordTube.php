<?php

function get_image_object_from_wordtube($content,$index) {
	
	global $wordTube, $wordTubeShortcodes;
	$content = $wordTubeShortcodes->convert_shortcode($content);
	$video = '';
	$search = '/\[media id=(\d+)(?: width=)?(\d+)?(?: height=)?(\d+)?\]/';

	preg_match_all($search, $content, $matches);
	$shortcodes = array_shift($matches);

	if ($index == 0) {
		$target = 0;
	} else {
		if ($index < 0) {				
			$target = sizeof($matches[0])+$index;
		} else {
			$target = $index-1;
		}
	}

	$media = $wordTube->GetVidByID($matches[0][$target]);
	if ($media) {
		$attribArr['src'] = $media->image;
		return $attribArr;		
	} else {
		return false;
	}					

}

?>