<?php

if (!isset($content_width)) $content_width = 626;
if (!class_exists('htmlparser_class')) { require_once(dirname(__FILE__).'/class/htmlparser.php'); }

function custom_js_scripts() {
    wp_deregister_script('jquery');
    wp_enqueue_script('json-api', get_bloginfo('stylesheet_directory').'/js/json2.min.js');
    wp_enqueue_script('jquery', get_bloginfo('stylesheet_directory').'/js/jquery.min.js');
    wp_enqueue_script('jquery-ui-custom', get_bloginfo('stylesheet_directory').'/js/jquery-ui-1.8.17.custom.min.js');
    wp_enqueue_script('jquery-cookies', get_bloginfo('stylesheet_directory').'/js/jquery.cookies.2.2.0.min.js');
    wp_enqueue_script('jquery-tipsy', get_bloginfo('stylesheet_directory').'/js/jquery.tipsy.js');
    wp_enqueue_script('jquery-antiquiet', get_bloginfo('stylesheet_directory').'/js/jquery.antiquiet.js');
	if (is_single()) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'custom_js_scripts');

function custom_css_styles() {
	wp_enqueue_style('font-arvo', get_bloginfo('stylesheet_directory').'/font/Arvo.css');
	wp_enqueue_style('jquery-tipsy', get_bloginfo('stylesheet_directory').'/js/jquery.tipsy.css');
	if (is_single()) {
		wp_enqueue_style('aristo', get_bloginfo('stylesheet_directory').'/js/Aristo/Aristo.css');	
	}
}
add_action('wp_enqueue_scripts', 'custom_css_styles');

if (false === ($permanent_categories = get_transient('permanent-categories'))) {
	$permanent_categories = array();
	$parent = get_term_by('slug','exclusive','category');
	$children = get_categories(array('child_of'=>$parent->term_id));
	$permanent_categories[] = $parent->term_id;
	foreach ($children as $child) { $permanent_categories[] = $child->term_id; }
	set_transient('permanent-categories', $permanent_categories);
}

function aq_the_classes($post_id) {
	global $wpdb;
	if (false === ($classes = get_transient('post-classes-post-'.$post_id))) {
		$post_categories = aq_get_categories($post_id);
		$post_category_parents = aq_get_parents($post_id);
		$post_tags = aq_get_tags($post_id);
		$post_artists = aq_get_artists($post_id);
		$post_genres = aq_get_genres($post_id);
		$author_id = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d",$post_id)); 		
		$author_url = get_author_posts_url($author_id); 
		$author_url_parts = array_filter(explode('/',$author_url));
		$author_slug = 'author_'.array_pop($author_url_parts);
		$classes = implode(' ', array_merge(array_keys($post_categories), array_keys($post_category_parents), array_keys($post_tags), array_keys($post_artists), array_keys($post_genres))).' '.$author_slug;
		set_transient('post-classes-post-'.$post_id, $classes);
	}
	echo $classes;
}

function aq_the_pages($top=true) {
	echo aq_get_pages($top);
}
function aq_get_pages($top=true) {
	global $post;
	$type = 'number';
	$all_link = '<a href="'.get_permalink().'?all=true" class="all_link"><span>All</span></a>';
	$divider_top = '<div class="divider clear">&nbsp;</div>';
	$divider_bottom = $divider_top;
	if ($top == false) {
		$divider_bottom = '';
		$all_link = '';
		$type = 'next';
	} else {
		$divider_top = '';
	}
	$page_links = wp_link_pages(array(
		'before' => $divider_top.'<div class="page-links"><span class="header">Pages:</span>',
		'after' => $all_link.'</div>'.$divider_bottom,
		'link_before' => '<span>',
		'link_after' => '</span>',
		'next_or_number' => $type,
		'nextpagelink' => 'Next',
		'previouspagelink' => 'Previous',
		'echo' => '0'
	));
	return $page_links;
}

function aq_the_subjects($post_id,$link=true) {
	$subject_override = get_post_meta($post_id, 'subject_override', true);
	if (!empty($subject_override)) {
		$subjects[] = $subject_override;
	} else {
		$subjects = array();
		$artist_tags = get_the_terms($post_id,'artist');
		if (!empty($artist_tags)) {
			foreach($artist_tags as $artist) {
				if ($link) {
					$subjects[] = '<a href="/artist/'.$artist->slug.'">'.$artist->name.'</a>';
				} else {
					$subjects[] = $artist->name;
				}
			} 
		}
	}
	$post_categories = get_the_category($post_id);
	if (!empty($post_categories)) {
		foreach($post_categories as $cat) {
			$cat_link = str_replace('/./','/',get_category_link($cat->cat_ID));
			if ($link) {
				$subjects[] = '<a href="'.$cat_link.'">'.$cat->name.'</a>';
			} else {
				$subjects[] = $cat->name;
			}
		}
	}
	if (empty($subjects)) {
		echo 'Miscellaneous';
	} else {
		echo implode(', ',$subjects);
	}
}

function aq_the_categories($post_id) {
	echo implode(', ',array_values(aq_get_categories($post_id)));
}
function aq_get_categories($post_id) {
	$categories = array();
	$get_cats = get_the_category($post_id); 
	if ($get_cats) { foreach($get_cats as $category) { $categories['cat_'.$category->term_id] = $category->cat_name; } }
	return $categories;
}

function aq_the_parents($post_id) {
	echo implode(', ',array_values(aq_get_parents($post_id)));
}
function aq_get_parents($post_id) {
	$categories = array();
	$get_cats = get_the_category($post_id); 
	if ($get_cats) {
		foreach($get_cats as $category) {
			if (!empty($category->category_parent)) {
				$parent = get_category($category->category_parent);
				$categories['cat_'.$parent->term_id] = $parent->cat_name;
			}
		}
	}
	return $categories;
}

function aq_the_tags($post_id) {
	echo implode(', ',array_values(aq_get_tags($post_id)));
}
function aq_get_tags($post_id) {
	$tags = array();
	$get_tags = get_the_tags($post_id);
	if ($get_tags) { foreach($get_tags as $tag) { $tags['tag_'.$tag->term_id] = $tag->name; } }
	return $tags;
}

function aq_the_artists($post_id) {
	echo implode(', ',array_values(aq_get_artists($post_id)));
}
function aq_get_artists($post_id) {
	$artists = array();
	$get_artists = get_the_terms($post_id,'artist');
	if ($get_artists) { foreach($get_artists as $artist) { $artists['art_'.$artist->term_id] = $artist->name; } }
	return $artists;
}

function aq_the_genres($post_id) {
	echo implode(', ',array_values(aq_get_genres($post_id)));
}
function aq_get_genres($post_id) {
	$genres = array();
	$get_genres = get_the_terms($post_id,'genre');
	if ($get_genres) { foreach($get_genres as $genre) { $genres['gen_'.$genre->term_id] = $genre->name; } }
	return $genres;
}

function aq_the_authors() {
#	if (function_exists('coauthors_links')) { coauthors_links(); }
#	else { the_author(); }
	echo aq_get_authors();
}
function aq_get_authors() {
	$display_name = get_the_author_meta('display_name');
	$author_url = get_author_posts_url(get_the_author_meta('ID')); 
	$author_url = str_replace('/author/', '/#/author/', $author_url);
	return '<a href="'.$author_url.'">'.$display_name.'</a>';
}

function aq_the_thumbnail($post_id, $size='thumbnail') {
	echo aq_get_thumbnail($post_id, $size);
}
function aq_get_thumbnail($post_id, $size='thumbnail') {
	global $wpdb;
	global $_wp_additional_image_sizes;
	$default = get_bloginfo('stylesheet_directory').'/images/fpo-150x150-1.jpg';
	$feature_image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
	if (!empty($feature_image)) {
		return $feature_image[0];
	} else {
		foreach (get_intermediate_image_sizes() as $s) {
			if (isset($_wp_additional_image_sizes[$s])) {
				$width = intval($_wp_additional_image_sizes[$s]['width']);
				$height = intval($_wp_additional_image_sizes[$s]['height']);
			} else {
				$width = get_option($s.'_size_w');
				$height = get_option($s.'_size_h');
			}
			if ($s == $size) { break; }
		}
		$magic_thumb = magic_thumbnails($post_id,1,1,$width,$height);
		return $magic_thumb['src'];
	}
}

function aq_format_tracklist($tracklist) {
	if (!empty($tracklist)) {
		$c = true;
		$index = 0;
		$tracks = explode("\n", $tracklist);
		array_filter_and_trim($tracks);
		$output = '<table class="tracklist" border="0" cellspacing="0" cellpadding="0">';
		foreach ($tracks as $track) {
			if (true == $track = aq_clean_track($track)) {
				$index++;
				$class = ($c = !$c) ? 'even' : 'odd';
				$output .= '<tr>
					<td class="'.$class.' index">'.$index.'.</td>
					<td class="'.$class.' track">'.$track.'</td>
				</tr>';
			}
		}
		$output .= '</table>';
		echo $output;
	}
}
function aq_clean_track($track) {
	if (preg_match('/^Disc\s+?\d+/i', $track)) { return false; }
	$track = trim(preg_replace('/^(\d+\.\s+?)/i', '', $track));
	if (empty($track)) { return false; }
	return $track;
}

function aq_see_stars($rating, $style='small') {
	echo aq_get_stars($rating, $style);
}
function aq_get_stars($rating,$style='small') {
	$stars = '<div class="stars '.$style.'">';
	$star_on = '<div class="star"><img src="'.get_bloginfo('stylesheet_directory').'/images/star-on-'.$style.'.png" /></div>';
	$star_off = '<div class="star"><img src="'.get_bloginfo('stylesheet_directory').'/images/star-off-'.$style.'.png" /></div>';
	$star_half = '<div class="star"><img src="'.get_bloginfo('stylesheet_directory').'/images/star-half-'.$style.'.png" /></div>';
	$indexer = $rating;
	for ($counter=1; $counter<=5; $counter++) {
		if ($indexer >= 1) {
			$stars .= $star_on;
		} elseif ($indexer > 0) {
			$stars .= $star_half;
		} else {
			$stars .= $star_off;
		}
		$indexer--;
	}
	$stars .= '</div>';
	return $stars;
}

function get_artist_data($artist) {
	$return = array();
	$artist = get_term_by('name',$artist,'artist',ARRAY_A);
	$artist_page_args = array('numberposts' => 1,
		'post_type' => 'artist-page',
		'tax_query' => array(array('taxonomy'=>'artist',
			'field' => 'id',
			'terms' => $artist['term_id']
		))
	);
	$artist_page = get_posts($artist_page_args);
	if (!empty($artist_page)) {
		$return['name'] = $artist['name'];
		$return['debut'] = get_post_meta($artist_page[0]->ID, 'debut', true);
		$return['location'] = get_post_meta($artist_page[0]->ID, 'location', true);
		$return['status'] = get_post_meta($artist_page[0]->ID, 'status', true);
		$return['url'] = get_permalink($artist_page[0]->ID);
		$return['post_id'] = $artist_page[0]->ID;
		$return['term_id'] = $artist['term_id'];
		return $return;
	}
	return false;
}

function get_internal_links($content, $domains) {
	$return = array();
	if (!empty($content)) {
		$dom = new DOMDocument;
		@$dom->loadHTML($content);
		$nodes = $dom->getElementsByTagName('a');
		foreach ($nodes as $node) {
			$pattern = implode('|',$domains);
			if (preg_match("/{$pattern}/i", $node->getAttribute('href'))) {
				$post_url = $node->getAttribute('href');
				$post_id = url_to_postid($post_url);
				if (!empty($post_id) && get_post_type($post_id)=='post') {
					$return[$post_id] = array(
						'title' => get_the_title($post_id),
						'url' => get_permalink($post_id),
						'id' => $post_id
					);
				}
			}
		}
		krsort($return);
	}
	return $return;
}

function is_nsfw() {
	global $post;
	if (is_tag('nsfw')) {
		return true;
	}
	if (has_tag('nsfw', $post->ID)) {
		return true;
	}
	if (has_tag('nsfw', $post->post_parent)) {
		return true;
	}
	return false;
}

// auto artist linking

function order_by_name($a, $b) {
    return strcmp($a['name'], $b['name']);
}
function find_artists($post_id) {
	if (false === ($found_artists = get_transient('found-artists-'.$post_id))) {
		$found_artists = array();
		$unique_artists = array();
		$main_artists = get_the_terms($post_id,'artist');
		$tagged_artists = get_the_terms($post_id,'post_tag');
		if (!is_array($main_artists)) { $main_artists = array(); }
		if (!is_array($tagged_artists)) { $tagged_artists = array(); }
		$all_tags = array_merge($main_artists, $tagged_artists);
		if (!empty($all_tags)) {
			foreach($all_tags as $tag) {
				$found_artist = get_artist_data($tag->name);
				if ($found_artist && !in_array($tag->name, $unique_artists)) {				
					$found_artists[] = $found_artist;
					$unique_artists[] = $tag->name;
				}
			} 
		}
		usort(array_unique($found_artists), "order_by_name");	
		set_transient('found-artists-'.$post_id, $found_artists);
	}
	return $found_artists;
}
function hotlink_artist($match) {
	$artist_name = preg_replace('/(&#8217;|\')s?$/','',$match[0]);	
	$artist_data = get_artist_data($artist_name);
	if (empty($artist_data)) { $artist_data = get_artist_data('The '.$artist_name); }
	$artist_page = $artist_data['url'];
	return '<a href="'.$artist_page.'" class="special">'.$match[0].'</a>';
}
function fix_artist_links_in_links($content) {
	$xhtml = new DOMDocument;
	@$xhtml->loadHTML($content);
	$replacement = $xhtml->createDocumentFragment();
	$parser = new DOMXPath($xhtml);
	$query = $parser->query('//a//a[@class="special"]')->item(0);
	if (!empty($query->nodeValue)) {
		$replacement->appendXML('<span class="special">'.$query->nodeValue.'</span>');
		$query->parentNode->replaceChild($replacement, $query);
		$content = $xhtml->saveXml($xhtml->documentElement);
	}
	return $content;
}
function link_artist_names($content) {
	global $post;
	$artist_regexes = array();
	$found_artists = find_artists($post->ID);
	if ($found_artists) {
		foreach ($found_artists as $artist) {
			$artist_regex = $artist['name'];
			if (preg_match('/^The\s/',$artist['name'])) { $artist_regex = preg_replace('/^The\s/','(The )?',$artist['name']); }
			$artist_regexes[] = $artist_regex.'((&#8217;|\')s)?';
		}
		$regex = '/('.str_replace('/','\/',implode('|', $artist_regexes)).')(?![^<]*<\/(a|img)>)/';
		$content = preg_replace_callback($regex, 'hotlink_artist', $content);
		$content = fix_artist_links_in_links($content);
	}
	return $content;
}
#add_filter('the_content', 'link_artist_names',20);

// miscellaneous overrides...

function hide_from_lists($query) {
	if (!is_single()) {
		$tag = $query->get('tag');
		if ($tag != 'nsfw') {
			$nsfw_tags = array(2314);
			$query->set('tag', $query->get('tag'));
			$query->set('tag_slug__in', $query->get('tag_slug__in'));
			$query->set('tag__not_in',$nsfw_tags);
		}
	}
	if (!is_single() && !is_admin()) {
		$cats_string = array();
		$cats_string[] = $query->get('cat');
		$cats_string[] = '-2582'; // Hide "Secret" Posts
		$query->set('cat',implode(',',$cats_string));
	}
}
add_action('pre_get_posts', 'hide_from_lists');

function remove_menus() {
	global $menu;
	$restricted = array(__('Links'));
	end ($menu);
	while (prev($menu)) {
		$value = explode(' ', $menu[key($menu)][0]);
		if (in_array($value[0] != NULL ? $value[0] : "", $restricted)) { unset($menu[key($menu)]); }
	}
}
add_action('admin_menu', 'remove_menus');

add_theme_support('automatic-feed-links');
add_theme_support('post-thumbnails', array('post','artist-page','album-page'));

add_image_size('itsy-bitsy',27,27,true);
add_image_size('artist-thumb',75,75,true);
add_image_size('feature-panel',660,358,true);

function prioritize_tops($cat, $cats, $post) {
	if (in_category('music', $post)) {
		$cat = get_category_by_slug('music');
	}
	if (in_category('movies', $post)) {
		$cat = get_category_by_slug('movies');
	}
	return $cat;
}
add_action('post_link_category', 'prioritize_tops', 10, 3);

function float_spotify_embeds1($content) {
	$replace = preg_replace_callback(
		'/<iframe.*src=\"(http[s]?:\/\/embed\.spotify\.com.*)\".*width=\"(\d+)\".*height=\"(\d+)\".*><\/iframe>/',
		function ($matches) {
			if ($matches[2] < 626) { // Only float small playlists...
				$return = '<div class="spotify_embed" style="width: '.$matches[2].'px; height: '.$matches[3].'px;">'.$matches[0].'</div>';			
			} else { $return = $matches[0]; }
			return $return;
		}, $content
	);
	if ($content != $replace) { // If we've floated something, add a clearing div:
		$content = $replace;
		$content .= '<div class="clearing"></div>';
	}
	return $content;
}
function float_spotify_embeds2($content) {
	$replace = preg_replace_callback(
		'/<iframe.*src=\"(http[s]?:\/\/embed\.spotify\.com.*)\".*height=\"(\d+)\".*width=\"(\d+)\".*><\/iframe>/',
		function ($matches) {
			if ($matches[2] < 626) { // Only float small playlists...
				$return = '<div class="spotify_embed" style="width: '.$matches[3].'px; height: '.$matches[2].'px;">'.$matches[0].'</div>';			
			} else { $return = $matches[0]; }
			return $return;
		}, $content
	);
	if ($content != $replace) { // If we've floated something, add a clearing div:
		$content = $replace;
		$content .= '<div class="clearing"></div>';
	}
	return $content;
}
add_filter('the_content', 'float_spotify_embeds1',11);
add_filter('the_content', 'float_spotify_embeds2',12);

#add_filter('oembed_result','twitter_no_width',10,3);
function twitter_no_width($html, $url, $args) {
	if (false !== strpos($url, 'twitter.com')) {
		$html = str_replace('width="626"','width="200"',$html);
	}
	return $html;
}

function suppress_dead_spaces($content) {
	$content = preg_replace('/&nbsp;/', ' ', $content);
	return $content;
}
add_filter('the_content', 'suppress_dead_spaces',10);

function strip_excerpts($content) {
	$excerpt_length = 430;
	$content = strip_shortcodes($content);
	$content = strip_tags($content,'<i><em>');
	if (strlen($content) > $excerpt_length) {
		$content = trim(substr($content, 0, $excerpt_length));
		$content .= '...';
	}
	$content .= '</i></em>'; // Hack because people are dumb!
	return $content;
}  
add_filter('the_excerpt', 'strip_excerpts');
add_filter('get_the_excerpt', 'strip_excerpts');

//function custom_excerpt_length($length) { return 24; }
//add_filter('excerpt_length', 'custom_excerpt_length', 999);

function add_aq_vars($public_query_vars) {
	$public_query_vars[] = 'post_flag';
	$public_query_vars[] = 'post_filters';
	$public_query_vars[] = 'all';
	return $public_query_vars;
}
add_filter('query_vars', 'add_aq_vars');

function the_category_filter($list,$sep=', ') {
	return strip_tags($list);
}
add_filter('the_category','the_category_filter');

function comment_count($count) {
	global $id;
	$comments = get_approved_comments($id);
	$comment_count = 0;
	foreach($comments as $comment) { if ($comment->comment_type == "") { $comment_count++; } }
	return $comment_count;
}
add_filter('get_comments_number', 'comment_count', 0);

add_shortcode('divider', 'render_divider');
function render_divider($attr, $content = null) {
	return '<div class="divider">&nbsp;</div>';
}

// kill auto-embedded videos on facebook

add_filter('sfc_base_meta','kill_sfc_video', 11, 2);
function kill_sfc_video($og, $post) {	
	unset($og['og:video']);
	unset($og['og:video:height']);
	unset($og['og:video:width']);
	unset($og['og:video:type']);
	return $og;
}

// extend user profile

function modify_user_contact_methods($user_contact) {
	$user_contact['twitter'] = __('Twitter Username'); 
	return $user_contact;
}
add_filter('user_contactmethods', 'modify_user_contact_methods');

// fix jw player embeds and stuff

require_once(dirname(__FILE__).'/jw-player.php');

function deprecate_wordtube() {
	// fixes a conflict with new html5 player.
	if (is_single()) {
		$post_date = strtotime(get_the_date('Y-m-d'));
		$wordtube_death = strtotime('2012-04-01');
		if ($post_date >= $wordtube_death) {
			wp_deregister_script('wordtube_stats');
		}
	}
}
add_action('wp_print_scripts', 'deprecate_wordtube', 100);

// it's 2012, why is wordpress still injecting css?

require_once(dirname(__FILE__).'/gallery.php');

#add_shortcode('wp_caption', 'fixed_img_caption_shortcode');
#add_shortcode('caption', 'fixed_img_caption_shortcode');
function fixed_img_caption_shortcode($attr, $content = null) {
	$output = apply_filters('img_caption_shortcode', '', $attr, $content);
	if ($output != '') return $output;
	extract(shortcode_atts(array(
		'id'=> '',
		'align'	=> 'alignnone',
		'width'	=> '',
		'caption' => ''), $attr));
	if (1 > (int) $width || empty($caption)) return $content;
	if ($id ) $id = 'id="'.esc_attr($id).'" ';
	return '<div '.$id.'class="wp-caption '.esc_attr($align).'">'.do_shortcode($content).'<p class="wp-caption-text">'.$caption.'</p></div>';
}

// what what, nitpicking in my butt

function my_admin_head() { echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/wp-admin.css" />'; } add_action('admin_head', 'my_admin_head');

function set_default_admin_view($user_id) {
	global $wpdb;
	$user = get_userdata($user_id);
	if ($user->user_level) {
		$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'manageedit-postcolumnshidden', 'a:2:{i:0;s:4:"tags";i:1;s:0:"";}'));
		$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'metaboxhidden_post', 'a:5:{i:0;s:13:"trackbacksdiv";i:1;s:10:"postcustom";i:2;s:7:"slugdiv";i:3;s:12:"sharing_meta";i:4;s:5:"aiosp";}'));
		$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'meta-box-order_post', 'a:3:{s:4:"side";s:98:"authordiv,submitdiv,updated,postimagediv,categorydiv,tagsdiv-artist,tagsdiv-post_tag,feature-panel";s:6:"normal";s:101:"trackbacksdiv,postexcerpt,postcustom,custom-subject,album-review,commentstatusdiv,slugdiv,commentsdiv";s:8:"advanced";s:18:"sharing_meta,aiosp";}'));
		$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_post', '2'));
		$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_mini-post', '2'));
	}
} add_action('user_register', 'set_default_admin_view');

// clear post transient caches on post update

function delete_post_transients($post_id) {
	delete_transient('post-flags-post-'.$post_id);
	delete_transient('post-classes-post-'.$post_id);
	delete_transient('found-artists-'.$post_id);
	delete_transient('previous-posts-post-'.$post_id);
	delete_transient('album-for-review-'.$post_id);
	delete_transient('permanent-categories');
	if (get_post_type($post_id) == 'artist-page') {
		$artists = get_the_terms($post_id,'artist');
		if (!empty($artists)) {
			foreach ($artists as $artist) {
				delete_transient('artist-image-'.$artist->term_id);
			}
		}
	}
} add_action('save_post', 'delete_post_transients');

if (!function_exists('post_is_in_descendant_category')) {
	function post_is_in_descendant_category($cats, $_post=null) {
		foreach ((array)$cats as $cat) {
			$descendants = get_term_children((int)$cat, 'category');
			if ($descendants && in_category($descendants, $_post)) {
				return true;
			}
		}
		return false;
	}
}

function deprecated_shortcode($atts) {
	return false;
}
add_shortcode('MYPLAYLIST', 'deprecated_shortcode');
add_shortcode('MEDIA', 'deprecated_shortcode');

// ancient chinese utilities

function smart_slug($input) {
	$slug = $input;
	$slug = charset_decode_utf_8($slug);
	$slug = convert_decoded_utf_8($slug);
	$slug = strtolower(trim(stripslashes($slug)));
	$slug = str_replace(array('.',',','?','(',')','\'','/','"'),'',$slug);
	$slug = str_replace(array(' ','&'),array('-','and'),$slug);
	$slug = ereg_replace('^the-','',$slug);
	return $slug;
}

function charset_decode_utf_8($string) { 
    if ((!ereg("[\200-\237]", $string)) && (!ereg("[\241-\377]", $string))) { 
		return $string;
	} else {
		$string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e", "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'", $string); 
		$string = preg_replace("/([\300-\337])([\200-\277])/e", "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'", $string); 
		return $string;
    }
} 

function convert_decoded_utf_8($str) {
	$html_entities = array (
		'&#225;' => 'a',
		'&#226;' => 'a',
		'&#224;' => 'a',
		'&#229;' => 'a',
		'&#227;' => 'a',
		'&#228;' => 'a',
		'&#194;' => 'a',
		'&#192;' => 'a',
		'&#197;' => 'a',
		'&#195;' => 'a',
		'&#196;' => 'a',
		'&#230;' => 'ae',
		'&#198;' => 'Ae',
		'&#231;' => 'c',
		'&#199;' => 'C',
		'&#233;' => 'e',
		'&#234;' => 'e',
		'&#201;' => 'E',
		'&#202;' => 'E',
		'&#200;' => 'E',
		'&#239;' => 'i',
		'&#237;' => 'i',
		'&#238;' => 'i',
		'&#246;' => 'o',
		'&#243;' => 'o',
		'&#245;' => 'o',
		'&#244;' => 'o',
		'&#248;' => 'o',
		'&#207;' => 'I',
		'&#205;' => 'I',
		'&#206;' => 'I',
		'&#214;' => 'O',
		'&#211;' => 'O',
		'&#213;' => 'O',
		'&#212;' => 'O',
		'&#216;' => 'O',
		'&#251;' => 'u',
		'&#249;' => 'u',
		'&#252;' => 'u',
		'&#217;' => 'U',
		'&#220;' => 'U',
		'&#253;' => 'y',
		'&#255;' => 'y',
		'&#221;' => 'Y',
		'&#376;' => 'Y'
	);
	foreach ($html_entities as $key => $value) {
		$str = str_replace($key, $value, $str);
	}
	return $str;
}

function str_trim_value(&$value) { 
	if (is_string($value)) {
		$value = trim($value);
	}
}
function array_filter_and_trim($arr) { 
	array_walk($arr, 'str_trim_value'); 
	return array_filter($arr); 
}

?>