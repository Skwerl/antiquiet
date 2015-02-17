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

	function resize_insets() {
		if ($("body").hasClass("single")) {	
			$("div.wp-caption img").each(function(index) {
				var target_width = $(this).width();
				console.log(target_width);
				$(this).closest(".wp-caption").width(target_width);
			});	
		}
	}

	$(window).resize(function() {
		resize_embeds();
		resize_insets();
	});
	
	resize_embeds();
	resize_insets();

});