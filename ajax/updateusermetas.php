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
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'metaboxhidden_post', 'a:4:{i:0;s:13:"trackbacksdiv";i:1;s:10:"postcustom";i:2;s:7:"slugdiv";i:3;s:12:"sharing_meta";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'meta-box-order_post', 'a:3:{s:4:"side";s:99:"authordiv,submitdiv,postimagediv,feature-panel,categorydiv,genrediv,tagsdiv-artist,tagsdiv-post_tag";s:6:"normal";s:99:"postexcerpt,trackbacksdiv,postcustom,post-flags,custom-subject,commentstatusdiv,slugdiv,commentsdiv";s:8:"advanced";s:12:"sharing_meta";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'metaboxhidden_artist-page', 'a:3:{i:0;s:11:"postexcerpt";i:1;s:10:"postcustom";i:2;s:7:"slugdiv";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'meta-box-order_artist-page', 'a:3:{s:4:"side";s:60:"submitdiv,postimagediv,tagsdiv-artist,genrediv,pageparentdiv";s:6:"normal";s:74:"postexcerpt,artist-details,postcustom,commentstatusdiv,commentsdiv,slugdiv";s:8:"advanced";s:0:"";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'metaboxhidden_album-page', 'a:3:{i:0;s:11:"postexcerpt";i:1;s:10:"postcustom";i:2;s:7:"slugdiv";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'meta-box-order_album-page', 'a:3:{s:4:"side";s:46:"submitdiv,postimagediv,tagsdiv-artist,genrediv";s:6:"normal";s:73:"album-details,postexcerpt,postcustom,commentstatusdiv,commentsdiv,slugdiv";s:8:"advanced";s:0:"";}'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_post', '2'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_mini-post', '2'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_artist-page', '2'));
			$wpdb->query($wpdb->prepare("INSERT INTO $wpdb->usermeta (user_id, meta_key, meta_value) VALUES (%d, %s, %s)", $user_id, 'screen_layout_album-page', '2'));
			echo $user->ID.' metas added.<br/>';
		}
	}

}

?>