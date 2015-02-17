jQuery(document).ready(function($) {

	"use strict";
	
	function resize_embeds() {
		if ($("body").hasClass("single")) {	
			$("iframe.youtube-player").each(function(index) {
				var target_height = $(this).height()+5;
				$(this).parent().height(target_height);
				$(this).parent().parent().height(target_height);
			});	
		}
	}

	$(window).resize(function() {
		resize_embeds();
	});
	
	resize_embeds();

});