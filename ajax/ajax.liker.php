<?php

require_once('ajax.enabler.php');

if (!wp_verify_nonce($_POST['auth_key'], 'comment-liker-nonce')) {

	echo json_encode(array('no' => 'Sorry, could not validate your vote. No shenanigans.'));

} else {

	$user_id = false;
	$wp_user = wp_get_current_user();
	
	if (!empty($wp_user->ID)) { $user_id = $wp_user->ID; }
	elseif (!empty($_POST['fb_user_id'])) { $user_id = 'fb_'.$_POST['fb_user_id']; }
	
	if (empty($user_id)) {
		echo json_encode(array('no' => 'You must be logged in to rate comments.'));
	} else {

		$comment_karma = get_comment_meta($_POST['comment_id'], 'comment_karma', true);
		if (empty($comment_karma)) { $comment_karma = '[[],[]]'; }
		$votes_array = json_decode($comment_karma, true);
		$votes_key = $_POST['direction'];
		if ($votes_key == "0" || $votes_key == "1") {
			foreach ($votes_array as $key => $val) { $votes_array[$key] = array_values(array_diff($val, array($user_id))); }
			$votes_array[$votes_key][] = $user_id;
			update_comment_meta($_POST['comment_id'], 'comment_karma', json_encode($votes_array));
			delete_transient('comment-karma-post-'.$_POST['wp_post_id']);
			/*	// down the line, maybe we can implement user karma.
				// as is, we'd be leaving out facebook users, which isn't cool.
			$commenter_id = get_comment($_POST['comment_id'])->user_id;
			if (!empty($commenter_id)) {
				$increment = (intval($votes_key)*2)-1;
				// add $increment to user's karma score	here...		
			}
			*/
			echo json_encode(array('ok' => array(sizeof($votes_array[0]),sizeof($votes_array[1]))));
		} else {
			echo json_encode(array('no' => 'Sorry, I\'m not really into Pokémon.'));
		}

	}
}

?>