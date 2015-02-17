jQuery(document).ready(function($) {

	"use strict";
	
	function lazy_update_post_title() {
	
		if ($("input#lazy_title_on").prop("checked")) {

			var lazy_title_artist = $("input#lazy_title_artist").val();
			var lazy_title_song = '\''+$("input#lazy_title_song").val()+'\'';
			var separator = '';

			if (lazy_title_artist != '' && lazy_title_song != '') { separator = ', '; }
			var lazy_title = lazy_title_artist+separator+lazy_title_song;

			$("input#title").val(lazy_title);
		
		}
		
	}

	$("input#lazy_title_on").change(function() { lazy_update_post_title(); });
	$("input#lazy_title_artist").keyup(function() { lazy_update_post_title(); });
	$("input#lazy_title_song").keyup(function() { lazy_update_post_title(); });

});