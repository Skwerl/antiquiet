var currentPage = 1;
var scrollActive = true;

function load_more_articles(flush,query) {
	var params = {
		"cat": aq_ajax_cat,
		"paged": currentPage
	};
	$.post(aq_ajax_path+"ajax.articles.php", params, function(data) {
		if ($.trim(data) != "") {
			$("#article_loader").append($(data).fadeIn());
			$("#article_loader").trigger("contentload");
		} else {
			scrollActive = false;
			$("#scroller").fadeOut(function() {
				$(this).after($("<div/>").addClass("clear"));
			});
		}
	});
}

function search_feedback(feedback) {
	$("#search_feedback").html(feedback).stop().fadeIn("fast").delay(500).fadeOut(1000);
}

var tipsy_params = { delayIn: 700, delayOut: 0, gravity: "n", offset: -5 }; 

$(document).ready(function() {

	if (!$("body").hasClass("single")) {

		$("#article_loader").bind("contentload", function(e) {
			$("#article_loader").css({ overflow: "visible", height: "auto" });
			$("#scroller").removeClass("active").fadeIn();
		});
	
		var scrollTimer = false;
	
		$("#feature_panel .panel").click(function(e) {
			window.location = $(this).attr("href");
		});
	
		$("#feature_panel .thumbnail a").click(function(e) {
			e.preventDefault();
			$(".active").removeClass("active");
			$(this).parent().addClass("active");
			var nextThumb = $(this).parent().next().find("a");
			var firstThumb = $("#feature_panel .thumbnail:first-child a");
			var panelIndex = $("#feature_panel .thumbnail").index($(this).parent());
			var panelToPos = 0-(panelIndex*358);
			$("#feature_panel div.panels").animate({ top: panelToPos+"px" }, 500, "easeInBack");
			if (scrollTimer) clearTimeout(scrollTimer);
			scrollTimer = setTimeout(function() {
				if (nextThumb.size() > 0) nextThumb.click();
				else firstThumb.click();
			}, 8000);
		});
		
		$("#feature_panel .thumbnail:first-child a").click();
	
		$("#scroller").on("click", function(e) {
			currentPage++;
			$(this).addClass("active");
			load_more_articles(false,true);
		});
		$(window).scroll(function() {
			if (scrollActive && ($(window).scrollTop() == $(document).height()-$(window).height())) {
				$("#scroller").trigger("click");
			}
		});
	
		$("#article_loader").trigger("contentload");

	}

	$(".wp-caption").width(function() { return $(this).find("img").width(); });
	$(".wp-caption-text").html(function() { return $(this).html().replace("{","<span>").replace("}","</span>"); }).find("span").addClass("source");

	$(".tip").tipsy(tipsy_params);

});