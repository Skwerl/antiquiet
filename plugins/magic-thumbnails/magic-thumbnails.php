<?php
/*
Plugin Name: Magic Thumbnails
Plugin URI: http://www.antiquiet.com/
Description: Magic Thumbnails can take a specified image (or automatically find one in a specified post), and generate a thumbnail at any size or shape, using WordPress' built-in image resizing functions.
Version: 3.5
Author: Skwerl
Author URI: http://iamskwerl.com/
*/

require_once(ABSPATH.'wp-admin/includes/image.php');
if (!class_exists('htmlparser_class')) { require_once('htmlParser.php'); }

// Snag remote images in there via Embedly first...
add_action('publish_post', 'fetch_images');
function fetch_images($post_ID) {	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;	
	}
	if (!current_user_can('edit_post', $post_ID)) {
		return;
	}
	remove_action('publish_post', 'fetch_images');		
	$post = get_post($post_ID);   
	$first_image = '';
	if (preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches)) {
		$first_image = $matches[1][0];
	}
	if (!empty($first_image)) {	
		if (strpos(strtolower($first_image),'antiquiet.com') === false) {
			$get = wp_remote_get($first_image);
			$type = wp_remote_retrieve_header($get, 'content-type');
			$mirror = wp_upload_bits(rawurldecode(basename($first_image)), '', wp_remote_retrieve_body($get));
			$attachment = array(
				'post_title'=> basename($first_image),
				'post_mime_type' => $type
			);
			$attach_id = wp_insert_attachment($attachment, $mirror['file'], $post_ID);
			$filepath = get_attached_file($attach_id,false);
			$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
			wp_update_attachment_metadata($attach_id, $attach_data);
			set_post_thumbnail($post_ID, $attach_id);
			$updated = str_replace($first_image, $mirror['url'], $post->post_content);
			wp_update_post(array('ID'=>$post_ID, 'post_content'=>$updated));
			add_action('publish_post', 'fetch_images');		
		}
	}
}

class magicThumbnail {

	function generate_thumb($post_id,$index,$width,$height,$crop=true) {
		$return = $this->resize_image_object($this->get_image_object($post_id,$index),$width,$height,$crop);
		return $return;
	}

	function get_image_object($post_id,$index,$custom='Thumbnail') {
		global $wpdb;	
		$parser = new htmlparser_class;
		$override = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '$custom' AND $wpdb->postmeta.post_id = '$post_id';");
		if (!empty($override[0]->meta_value) && ($index == 1)) {
			$attribArr = array();
			$attribArr['src'] = $override[0]->meta_value;
			return $attribArr;
		} else {
			$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$post_id';");
			$parser->InsertHTML($post->post_content);
			$parser->Parse();
			$result = $parser->GetElements($htmlCode);
			$attribArr = $parser->getTagResource('img'); 
			if ($attribArr == false) {
				if (class_exists('wordTubeClass')) {
					require_once('wordTube.php');
					return get_image_object_from_wordtube($post->post_content,$index);
				} else {
					return false;							
				}
			} else {
				if ($index == 0) {
					$target = 1;
				} else {
					if ($index < 0) {				
						$target = sizeof($attribArr)+$index;
					} else {
						$target = $index-1;
					}
				}
				return $attribArr[$target];
			}
		}
	}
	
	function resize_image_object($image,$width,$height,$crop=false) {
		if ($image) {
			$attrs = array();
			$parser = new htmlparser_class;
			$file_data = $parser->linkAnalyzer($image['src']);
			$attr_data = $parser->linkAnalyzer($image);		
			$attrs['filepath'] = $_SERVER['DOCUMENT_ROOT'].$file_data['path'];
			foreach ($attr_data['url'] as $attr => $value) {
				$attrs[$attr] = $value;
			}

			$raw_file = preg_replace('/(-(\d{2,4})x(\d{2,4}))?((.jpg)|(.jpeg)|(.gif)|(.png))/','',$attrs['filepath']);

			if (file_exists($raw_file.'.jpg')) {
				$source_file = $raw_file.'.jpg';
				$destination = $raw_file.'-'.$width.'x'.$height.'.jpg';
			} elseif (file_exists($raw_file.'.jpeg')) {
				$source_file = $raw_file.'.jpeg';
				$destination = $raw_file.'-'.$width.'x'.$height.'.jpg';
			} elseif (file_exists($raw_file.'.gif')) {
				$source_file = $raw_file.'.gif';
				$destination = $raw_file.'-'.$width.'x'.$height.'.gif';				
			} elseif (file_exists($raw_file.'.png')) {
				$source_file = $raw_file.'.png';
				$destination = $raw_file.'-'.$width.'x'.$height.'.png';				
			} else {
				$source_file = $attrs['filepath'];
				$destination = '/null/null';				
			}
			
			if (file_exists($destination)) {
				$image = $destination;
			} else {
				$image = image_resize($source_file, $width, $height, $crop);			
			}
			
			if (is_string($image)) {
				preg_match('/(.*)(\/wp-content\/.*\/)(.*)/',$image,$match);
				$image = get_bloginfo('url').$match[2].$match[3];
				$attrs['resized'] = $image;
				return $attrs;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function resize_image_file($image,$width,$height,$crop=false) {
		if (empty($image)) { $image = plugins_url('default/default.jpg',__FILE__); }
		$attribArr = array();
		$attribArr['src'] = $image;
		$return = $this->resize_image_object($attribArr,$width,$height,$crop);
		return $return;
	}

}

// 1.x / 2.x backwards compatibility
function magic_thumbnails($post_id,$index,$resize=1,$target_width=100,$target_height=75,$overrides='',$html_attrs='') {	
	$overrides = ''; // deprecated
	$html_attrs = ''; // deprecated
	$thumb = new magicThumbnail;
	if ($r = $thumb->generate_thumb($post_id,$index,$target_width,$target_height)) {
		$src = $r['resized']; $alt = $r['alt']; $title = $r['title']; $class = $r['class'];
	} else {
		global $wpdb;
		$find = $thumb->get_image_object($post_id,$index); $src = $find['src'];
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$src'";
		$id = $wpdb->get_var($query);
		if (!empty($id)) {
			$image = wp_get_attachment_image_src($id, array($target_width,$target_height));
			$src = $image[0];
			$alt = $find['alt'];
			$title = $find['title'];
			$class = $find['class'];
		} else {
			$default = plugins_url('default/default.jpg',__FILE__);
			$default = $thumb->resize_image_file($default,$target_width,$target_height,true);
			$src = $default['resized'];
			$alt = ''; $title = ''; $class = '';
		}
	}
	$tag = '<img src="'.$src.'" class="'.$class.'" width="'.$target_width.'" height="'.$target_height.'" alt="'.$alt.'" title="'.$title.'" />';
	$return = array('tag'=>$tag, 'src'=>$src);
	//print_r($return);
	return $return;
}

?>