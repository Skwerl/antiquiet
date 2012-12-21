<?php

/*
	this is just for the admin theme options page.
	it's a bit of a hack, with the span & {} shit... but it gets the job done.
	by the way, this was pulled mostly from wp-admin/admin-ajax.php.
*/

require_once('ajax.enabler.php');

if (isset( $_GET['tax'])) {
	$taxonomy = sanitize_key($_GET['tax']);
	$tax = get_taxonomy($taxonomy);
	if (!$tax) { die; }
} else {
	die;
}

$s = stripslashes($_GET['q']);

if (false !== strpos($s, ',')) {
	$s = explode( ',', $s );
	$s = $s[count( $s ) - 1];
}
$s = trim($s);
if (strlen($s) < 2) {
	die;
}

$results = $wpdb->get_results($wpdb->prepare("SELECT t.name, t.term_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.name LIKE (%s)", $taxonomy, '%'.like_escape($s).'%'));

foreach ($results as $result) {
	echo $result->name.' <span class="term_id">{'.$result->term_id.'}</span>'."\n";
}

?>