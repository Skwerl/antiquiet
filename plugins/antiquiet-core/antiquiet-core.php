<?php

/*
Plugin Name: Antiquiet Core
Description: Does some magical things...
Version:     1.1
Author:      Skwerl
Text Domain: ntqt_core

*/

//require_once(ABSPATH.'wp-admin/includes/image.php');
if (!class_exists('htmlparser_class')) { require_once('class-htmlparser.php'); }

function aq_featured_image_update($post_id, $attachment_id) {
	// I know there's a WP function for this, but this is quicker & cleaner.
	global $wpdb;
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id = $post_id AND meta_key = '_thumbnail_id'");
	$wpdb->insert($wpdb->postmeta, array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attachment_id));
}

function aq_set_featured_image($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
	if (!current_user_can('edit_post', $post_id)) { return; }
	remove_action('publish_post', 'aq_set_featured_image');		
	$post = get_post($post_id);   
	$first_image = '';
	if (preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches)) {
		$first_image = $matches[1][0];
	}
	if (!empty($first_image)) {	
		$get = wp_remote_get($first_image);
		$type = wp_remote_retrieve_header($get, 'content-type');
		$mirror = wp_upload_bits(rawurldecode(basename($first_image)), '', wp_remote_retrieve_body($get));
		$attachment = array(
			'post_title'=> basename($first_image),
			'post_mime_type' => $type
		);
		$attach_id = wp_insert_attachment($attachment, $mirror['file'], $post_id);
		$filepath = get_attached_file($attach_id,false);
		$attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
		wp_update_attachment_metadata($attach_id, $attach_data);
		aq_featured_image_update($post_id, $attach_id);
	}
	return $attach_id;
}

add_filter('get_post_metadata', function ($value, $post_id, $meta_key, $single) {

	static $is_recursing = false;

	$post_status = get_post_status($post_id);
	if ($post_status == 'publish') {

		if (!$is_recursing && $meta_key === '_thumbnail_id') {
			$is_recursing = true;
			$value = get_post_thumbnail_id($post_id);
			$is_recursing = false;
			
			if (empty($value)) {
	
				if (wp_attachment_is_image($post_id)) {
	
					$value = $post_id;
	
				} else {
	
					global $wpdb;	
					$parser = new htmlparser_class;
		
					$post = $wpdb->get_row("SELECT post_content FROM $wpdb->posts WHERE ID = '$post_id';");
					$parser->InsertHTML($post->post_content);
					$parser->Parse();
					$result = $parser->GetElements($htmlCode);
					$images = $parser->getTagResource('img');
					/* Example:
					(
						[class] => alignnone size-large wp-image-57295
						[src] => http://cdn.antiquiet.com/wp-content/uploads/2014/06/jroddy_bandshot2-626x334.jpg
						[alt] => jroddy_bandshot2
						[width] => 626
						[height] => 334
					)
					*/
					if ($images == false) {
						$value = false;
					} else {
						$value = false;
						$first_image = $images[0];
						if (isset($first_image['class'])) {
							preg_match('/\swp-image-(\d+)/', $first_image['class'], $matches);
							if (!empty($matches)) {
								$value = $matches[1];
								aq_featured_image_update($post_id, $value);
							}
						} else {
							//$value = aq_set_featured_image($post_id);
						}
					}
		
					if (empty($value)) {
						$age = get_post_time('Y', false, $post_id);
						if ($age > 2011) {
							$value = '59996';
						} else {
							$value = '59997';
						}
					}
				
				}
	
			}
	
			if (!$single) {
				$value = array($value);
			}
	
		}

	}

	return $value;

}, 10, 4);

//add_action('publish_post', 'aq_set_featured_image');

function use_lazy_title($title, $post_id = null) {
	if (!is_admin()) {
		$lazy_title_on = get_post_meta($post_id, 'lazy_title_on', true);
		if (!empty($lazy_title_on)) {
			$lazy_artist = get_post_meta($post_id, 'lazy_title_artist', true);
			$lazy_song = '\''.get_post_meta($post_id, 'lazy_title_song', true).'\'';
			$separator = ''; if (!empty($lazy_artist) && !empty($lazy_song)) { $separator = ', '; }
			$title = $lazy_artist.$separator.$lazy_song;
		}
	}
    return $title;
}
add_filter('the_title', 'use_lazy_title', 10, 2);

function aq_admin_load_scripts($hook) {
	if ($hook != 'post.php') { return; }
	wp_enqueue_script('aq-edit-js', plugins_url('antiquiet-core/js/post.js', dirname(__FILE__)));
}
add_action('admin_enqueue_scripts', 'aq_admin_load_scripts');

$tv_schedules_cat_id = get_cat_ID('TV Schedules');
if (!empty($tv_schedules_cat_id)) {
	add_filter('pre_get_posts', function($query) {
		if ($query->is_front) {
			$tv_schedules_cat_id = get_cat_ID('TV Schedules');
			$query->set('cat', '-'.$tv_schedules_cat_id);
		}
		return $query;
	});
}

function add_tagged_to_artist_page($query) {
    if ($query->is_tax('artist') && $query->is_main_query()) {
		$artist_term = get_query_var('term');
        $query->set('tax_query',
            array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => $artist_term,
                    'operator' => 'IN'
                )
            )
        );
    }
    return $query;
}
add_filter('pre_get_posts', 'add_tagged_to_artist_page');

function enable_tags_on_attachments() {
	register_taxonomy_for_object_type('post_tag', 'attachment');
}
add_action('init', 'enable_tags_on_attachments');

function extend_nsfw_tag_to_attachments($post_id) {
	if (wp_is_post_revision($post_id)) {
		return;
	}
	if (has_tag('nsfw', $post_id)) {
		$attachments =& get_children("post_parent=$post_id&post_type=attachment");
		foreach ((array)$attachments as $attachment_id => $attachment) {
			wp_set_post_tags($attachment_id, 'nsfw', true);
		}
	}
}
add_action('save_post', 'extend_nsfw_tag_to_attachments');

add_shortcode('divider', 'render_divider');
function render_divider($attr, $content = null) {
	return '<div class="divider">&nbsp;</div>';
}

function add_fluence_field($user) { ?>
	<h3>Fluence.io Integration</h3>
	<table class="form-table">
		<tr>
			<th><label for="twitter">Fluence Username</label></th>
			<td>
				<input type="text" name="fluence" id="fluence" value="<?php echo esc_attr(get_the_author_meta('fluence', $user->ID)); ?>" class="regular-text" /><br />
				<span class="description">Please enter your Fluence username.</span>
			</td>
		</tr>
	</table>
<?php }
add_action('show_user_profile', 'add_fluence_field');
add_action('edit_user_profile', 'add_fluence_field');

function save_fluence_field($user_id) {
	if (!current_user_can('edit_user', $user_id))
		return false;

	update_usermeta($user_id, 'fluence', $_POST['fluence']);
}
add_action('personal_options_update', 'save_fluence_field');
add_action('edit_user_profile_update', 'save_fluence_field');

require_once('cleaners.php');

?>