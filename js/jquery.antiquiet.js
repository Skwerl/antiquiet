var filters_json = {};
var filters_postflag = false;
var filters_customized = false;
var author_filter = false;

var currentPage = 1;
var scrollActive = true;

var formDefault = "";
var selectedItem = false;

function update_filters() {
	filters_json = {};
	$(".category_cloud > div.cat_toggle").each(function(index) {
		var trail = $(this).attr("id").split("-");
		if ($(this).hasClass("disabled")) { active = false; } else { active = true; }
		filters_json[trail[1].substr(0,1)+trail[2]] = Array($(this).find("span").html(), active);
	});
	if (filters_customized == true) {
		$.cookies.set("saved_filters",filters_json);
	}
}

function filter_articles() {
	$("#article_loader > div.article_wrapper").hide();
	var classes_to_show = Array();
	var classes_to_hide = Array();
	for (var key in filters_json) {
		var interpreted = interpret_filter(key, filters_json[key]);
		if (interpreted) {
			if (interpreted.display) { classes_to_show.push(interpreted.class); }
			else { classes_to_hide.push(interpreted.class); }
		}
	}
	for (var i=0; i < classes_to_show.length; i++) { $("#article_loader > div.article_wrapper."+classes_to_show[i]).show(); }
	for (var i=0; i < classes_to_hide.length; i++) { $("#article_loader > div.article_wrapper."+classes_to_hide[i]).hide(); }
	if (filters_postflag != false) {
		$("#article_loader > div.article_wrapper").hide();
		$("#article_loader > div.article_wrapper."+filters_postflag).show();
		for (var i=0; i < classes_to_hide.length; i++) { $("#article_loader > div.article_wrapper."+classes_to_hide[i]).hide(); }
	}
	if (author_filter != false) {
		$("#article_loader > div.article_wrapper").hide();
		$("#article_loader > div.article_wrapper.author_"+author_filter).show();
		for (var i=0; i < classes_to_hide.length; i++) { $("#article_loader > div.article_wrapper."+classes_to_hide[i]).hide(); }
	}
	hint_hidden();
}

function interpret_filter(key, obj) {
	var output = new Object;
	var display = obj[1];
	switch(key.substr(0,1)) {
		case "c":
			if (display == false) { return false; } 
			key = "cat_"+key.substr(1);
			break;
		case "g":
			if (display == true) { return false; } 
			key = "gen_"+key.substr(1);
			break;
		case "q":
		//	if (key.substr(1,1) == 'a') { key = "art_"+key.substr(2); }
		//	if (key.substr(1,1) == 't') { key = "tag_"+key.substr(2); }
			break;
		default:
			if (key == "xnsfw") { key = "postflag_nsfw"; }
	}
	output.class = key;
	output.display = display;
	return output;
}

function load_more_articles(flush,query) {
	query = typeof(query) != 'undefined' ? query : false;
	flush = typeof(flush) != 'undefined' ? flush : false;
	if (query && $(".cat_toggle.query").size() > 0) { query = JSON.stringify(filters_json); }
	else { query = 0; }
	if (flush) { currentPage = 1; scrollActive = true; } 
	var params = { "paged": currentPage, "post_flag": filters_postflag, "author_filter": author_filter, "post_filters": query };
	$.post(aq_ajax_path+"ajax.articles.php", params, function(data) {
		if ($.trim(data) != "") {
			if (flush) {
				$("#article_loader").css({
					overflow: "hidden",
					height: function() { return $(this).height(); }
				}).html("");
			}
			$("#article_loader").append($(data).fadeIn());
			$("#article_loader").trigger("contentload");
		} else {
			scrollActive = false;
			$("#scroller").fadeOut(function() {
				$(this).after($("<div/>").addClass("clear"));
			});
		}
		filter_articles();
	});
}

function hint_hidden() {
	var hidden_articles = $("div.article_wrapper:hidden").size();
	if (hidden_articles > 0) {
		var and_text = false;
		if (hidden_articles == 1) { and_text = "is"; }
		if (hidden_articles == 2) { and_text = "and 1 other article is"; }
		if (hidden_articles >= 3) { and_text = "and "+(hidden_articles-1)+" other articles are"; }
		var first_article = $("div.article_wrapper:hidden").first();
		$("#hidden_counter").find("span.recent a").html(first_article.find(".text .title").html()).attr("href",first_article.find(".text a").attr("href"));
		$("#hidden_counter").find("span.count").html(and_text);
		$("#hidden_counter").show();
	} else {
		$("#hidden_counter").find("span.recent a").html("Title").attr("href","#");
		$("#hidden_counter").find("span.count").html("and 0 other articles are");
		$("#hidden_counter").hide();
	}
}

function search_feedback(feedback) {
	$("#search_feedback").html(feedback).stop().fadeIn("fast").delay(500).fadeOut(1000);
}

var tipsy_params = { delayIn: 700, delayOut: 0, gravity: "n", offset: -5 }; 

$(document).ready(function() {

	if ($("body").hasClass("home")) {
	
		$("#filter_bar .divider").height($("#filter_bar").height()+2);	
		
		$("#filter_bar").bind("initfilters", function(e) {
			$(".cat_bubble.active").css("backgroundPosition", function(index) {
				var curPos = $(this).css("backgroundPosition").split(" ");
				return curPos[0]+" -32px";
			});
			update_filters();
		});
	
		$("#article_loader").bind("contentload", function(e) {
			$("#article_loader").css({ overflow: "visible", height: "auto" });
			$(".cat_bubble.article").css("backgroundPosition", function(index) {
				var curPos = $(this).css("backgroundPosition").split(" ");
				return curPos[0]+" -64px";
			}).attr("title", function() {
				var flagClass = this.className.match(/postflag_(\w+)/);
				if (typeof($("#filter_buttons ."+flagClass[0]).attr("original-title")) != 'undefined') {
					return $("#filter_buttons ."+flagClass[0]).attr("original-title");
				} else {
					return $("#filter_buttons ."+flagClass[0]).attr("title");
				}
			}).tipsy(tipsy_params);
			$("#scroller").removeClass("active").fadeIn();
			filter_articles();
		});

		$("#filter_bar .cat_bubble").click(function(e) {
			if ($(this).hasClass("rss")) {
				console.log("do rss");
				//window.location = '/feed/?post_flag='+filters_postflag+"&post_filters="+JSON.stringify(filters_json);
			} else if ($(this).hasClass("reset")) {
				$.get(aq_ajax_path+"ajax.filters.php", function(data) {
					$("#filter_bar .cat_bubble.pressed").removeClass("pressed").css("backgroundPosition", function(index) {
						var curPos = $(this).css("backgroundPosition").split(" ");
						return curPos[0]+" 0px";
					});
					$(".category_cloud").html(data);
					filters_postflag = false;
					filters_customized = true;
					update_filters();
					filter_articles();
					load_more_articles(true);			
				});
			} else {
				if ($(this).hasClass("pressed")) {
					$(this).removeClass("pressed");
					filters_postflag = false;
				} else {
					$("#filter_bar .cat_bubble.pressed").removeClass("pressed");
					$(this).addClass("pressed");
					filters_postflag = this.className.match(/postflag_(\w+)/)[0];
				}
				$("#filter_bar .cat_bubble").css("backgroundPosition", function(index) {
					var curPos = $(this).css("backgroundPosition").split(" ");
					if ($(this).hasClass("pressed")) {
						return curPos[0]+" -32px";
					} else {
						return curPos[0]+" 0px";
					}
				});
				update_filters();
				filter_articles();
			}
		});

		function filter_single_click(e) {
			if ($(this).hasClass("query")) {
				$(this).fadeOut(function() {
					var isblock = $(this).hasClass("disabled");
					$(this).remove();
					filters_customized = true;
					update_filters();
					if (!isblock || $(".cat_toggle.query").size() == 0) {
						load_more_articles(true,true);					
					} else {
						filter_articles();
					}
				});
			} else {
				if ($(this).hasClass("stuck") == false) {
					if (e.altKey || e.metaKey) {
						$(".cat_toggle").addClass("disabled");
						$(this).removeClass("disabled");
					} else {
						$(this).toggleClass("disabled");
					}
				}
				filters_customized = true;
				update_filters();
				filter_articles();
			}
		}
		
		function filter_double_click(e) {
			if ($(this).hasClass("stuck") == false) {
				$(".cat_toggle[rel="+$(this).attr("rel")+"]").addClass("disabled");
				$(this).removeClass("disabled");
			}
			filters_customized = true;
			update_filters();
			filter_articles();
		}
			
		$("#filter_bar .category_cloud").delegate(".cat_toggle", "click", function(e) {
			var that = this;
			filter_single_click.call(that, e);
			setTimeout(function() {
				var dblclick = parseInt($(that).data("double"),10);
				if (dblclick > 0) {
					$(that).data("double", dblclick-1);
				}
			}, 100);
		}).delegate(".cat_toggle", "dblclick", function(e) {
			$(this).data('double', 2);
			filter_double_click.call(this, e);
		});
		
		$("#add_filter").click(function() {
			$("#query").focus();
			if ($("#query").val() != $("#query").attr("title")) {
				if (typeof(selectedItem.key) != 'undefined') {
					$("<div/>").attr("id","taxonomy-query-"+selectedItem.key).addClass("cat_toggle query").attr("rel","query").append("<span>"+$("#query").val()+"<\/span>").hide().appendTo(".category_cloud").fadeIn();
					$("#query").val($("#query").attr("title")).removeClass("focus").blur();
					filters_customized = true;
					update_filters();
					filter_articles();
					load_more_articles(true,true);
					search_feedback("Query Added");
				} else {
					search_feedback("No Results");
				}
			}
			selectedItem = false;
		});
	
		$("#add_block").click(function() {
			$("#query").focus();
			if ($("#query").val() != $("#query").attr("title")) {
				if (typeof(selectedItem.key) != 'undefined') {
					$("<div/>").attr("id","taxonomy-query-"+selectedItem.key).addClass("cat_toggle query disabled").attr("rel","query").append("<span>"+$("#query").val()+"<\/span>").hide().appendTo(".category_cloud").fadeIn();
					$("#query").val($("#query").attr("title")).removeClass("focus").blur();		
					filters_customized = true;
					update_filters();
					filter_articles();
					if (selectedItem.key.substr(0,1) == "t") { search_feedback("Tag Blocked"); }
					if (selectedItem.key.substr(0,1) == "a") { search_feedback("Artist Blocked"); }
				} else {
					search_feedback("No Results");
				}
			}
			selectedItem = false;
		});
	
		$("#query").val($("#query").attr("title"));
	
		$(".category_cloud").disableSelection();
	
		$("input.smart").focus(function() {
			if ($(this).val() == $(this).attr("title")) {
				formDefault = $(this).val();
				$(this).val("");
				$(this).addClass("focus");
			}
		});
		$("input.smart").blur(function() {
			if ($(this).val() == "") {
				$(this).val(formDefault);
				$(this).removeClass("focus");			
			}
		});
	
		$("#custom_filter .form input").keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$("#add_filter").click();
			}
		});
	
		$("#query").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: aq_ajax_path+"ajax.query.php",
					dataType: "json",
					data: { "q": request.term },
					success: function(data) {
						response($.map(data, function(row) {
							var html = '';
							html += '<div class="image '+row.type+'">';
							if (row.img) { html += '<img src="'+row.img+'" />'; }
							html += '<\/div>';
							html += '<div class="text">'+row.name+'<\/div>';
							return { key: row.id, value: row.name, label: html }
						}));
					}
				});
			},
			html: true,
			minLength: 2,
			selectFirst: true,			
			focus: function(event, ui) {
				$(".focused").removeClass("focused");
				$("#ui-active-menuitem").parent().addClass("focused");
			},
			select: function(event, ui) {
				if (ui.item) {
					selectedItem = ui.item;
				} else {
					selectedItem = false;
					return false;
				}
			},
			open: function(event, ui) {
				var yPos = Number($(".ui-autocomplete").css("top").substr(0,$(".ui-autocomplete").css("top").length-2));
				var xPos = Number($(".ui-autocomplete").css("left").substr(0,$(".ui-autocomplete").css("left").length-2));
				$(".ui-autocomplete").css("top", (yPos+5)+"px");
				$(".ui-autocomplete").css("left", (xPos+1)+"px");
			},
			close: function(event, ui) {
				if ($("#query").val() != selectedItem.value) {
					$("#query").val("").blur();
				}
			}
		});
	
		$(".ui-autocomplete").addClass("shadow");
	
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

		$("#filter_bar").trigger("initfilters");
		$("#article_loader").trigger("contentload");

	}
	
	$("article .postflags .cat_bubble ").css("backgroundPosition", function(index) {
		var curPos = $(this).css("backgroundPosition").split(" ");
		return curPos[0]+" -64px";
	});

	$(".wp-caption").width(function() { return $(this).find("img").width(); });
	$(".wp-caption-text").html(function() { return $(this).html().replace("{","<span>").replace("}","</span>"); }).find("span").addClass("source");

	$(".tip").tipsy(tipsy_params);

});

/* jQuery UI Autocomplete Extensions by Scott González */

(function($) {
	var proto = $.ui.autocomplete.prototype, initSource = proto._initSource;
	function filter(array, term) {
		var matcher = new RegExp($.ui.autocomplete.escapeRegex(term), "i");
		return $.grep(array, function(value) {
			return matcher.test($("<div>").html(value.label||value.value||value).text());
		});
	}
	$.extend(proto, {
		_initSource: function() {
			if (this.options.html && $.isArray(this.options.source)) {
				this.source = function(request, response) {
					response(filter(this.options.source, request.term));
				};
			} else {
				initSource.call(this);
			}
		},
		_renderItem: function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append($("<a/>")[this.options.html ? "html" : "text"](item.label)).appendTo(ul);
		}
	});
})(jQuery);

(function($) {
	$(".ui-autocomplete-input").live("autocompleteopen", function() {
		var autocomplete = $(this).data("autocomplete");
		var menu = autocomplete.menu;
		if (!autocomplete.options.selectFirst) {
			return;
		}
		menu.activate($.Event({ type: "mouseenter" }), menu.element.children().first() );
	});
})(jQuery);