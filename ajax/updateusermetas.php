<?php

die();

require_once('ajax.enabler.php');

global $wpdb;

$roles = array('administrator', 'author', 'contributor');

foreach ($roles as $role) {

	$this_role = "'[[:<:]]".$role."[[:>:]]'";
	$query = "SELECT * FROM $wpdb->users WHERE ID = ANY (SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'wp_capabilities' AND meta_value RLIKE $this_role) ORDER BY user_nicename ASC";
	$users_of_this_role = $wpdb->get_results($query);
	if ($users_of_this_role) {
		foreach($users_of_this_role as $user) {
			$user_id = $user->ID;
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'manageedit-postcolumnshidden', 'a:2:{i:0;s:4:"tags";i:1;s:0:"";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'metaboxhidden_post', 'a:5:{i:0;s:13:"trackbacksdiv";i:1;s:10:"postcustom";i:2;s:7:"slugdiv";i:3;s:12:"sharing_meta";i:4;s:5:"aiosp";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'meta-box-order_post', 'a:3:{s:4:"side";s:98:"authordiv,submitdiv,updated,postimagediv,categorydiv,tagsdiv-artist,tagsdiv-post_tag,feature-panel";s:6:"normal";s:101:"trackbacksdiv,postexcerpt,postcustom,custom-subject,album-review,commentstatusdiv,slugdiv,commentsdiv";s:8:"advanced";s:18:"sharing_meta,aiosp";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_post', '2'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_mini-post', '2'));
			echo $user->ID.' metas added.<br/>';
		}
	}

}

?>