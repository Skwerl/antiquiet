<?php

add_action('admin_init', 'theme_options_init');
add_action('admin_menu', 'theme_options_add_page');

function theme_options_init() {
	register_setting('theme_options', 'aq_theme_options', 'theme_options_validate');
}

function theme_options_add_page() {
	$page = add_theme_page(__('Theme Options', 'aq6' ), __('Theme Options', 'aq6'), 'edit_theme_options', 'theme_options', 'theme_options_do_page');
	add_action('admin_print_styles-'.$page, 'theme_options_js');
}

function theme_options_js() {
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
    wp_register_script('json-api', get_bloginfo('stylesheet_directory').'/js/json2.min.js');
	wp_enqueue_script('json-api'); 
	wp_enqueue_script('suggest');
}

function theme_options_do_page() {

	if (!isset($_REQUEST['settings-updated'])) {
		$_REQUEST['settings-updated'] = false;
	}

	?><div class="wrap">
		<?php screen_icon(); echo '<h2>'.get_current_theme().' '.__('Theme Options', 'aq6').'</h2>'; ?>

		<?php if (false !== $_REQUEST['settings-updated']): ?>
		<div class="updated fade"><p><strong><?php _e('Options saved', 'aq6'); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields('theme_options'); ?>
			<?php $options = get_option('aq_theme_options'); ?>

			<div id="theme_options_content">

				<h2>Homepage Filters</h2>

				<div class="column">
					<h3>Categories:</h3>
					<div class="draggable">
						<?php
	
						$terms = get_terms('category','orderby=count&order=desc&hide_empty=0');
						foreach ($terms as $term) {
							echo '<div id="source-taxonomy-category-'.$term->term_id.'" class="cat_toggle category" rel="category"><span>'.$term->name.'</span></div>';
						}
						
						?>
					</div>
				</div>


				<div class="column">
					<h3>Genres:</h3>
					<div class="draggable">
						<?php
	
						$terms = get_terms('genre','orderby=count&order=desc&hide_empty=0');
						foreach ($terms as $term) {
							echo '<div id="source-taxonomy-genre-'.$term->term_id.'" class="cat_toggle genre" rel="genre"><span>'.$term->name.'</span></div>';
						}
						
						?>
					</div>
				</div>

				<div class="column">
					<h3>Special:</h3>
					<div class="draggable">
						<div id="source-taxonomy-xustom-nsfw" class="cat_toggle xustom" rel="xustom"><span>NSFW</span></div>					
					</div>
				</div>

				<div class="column">
					<div id="tag-input-wrapper">
						<h3>Tags:</h3>
						<input id="tag-input" type="text" />
						<div class="draggable"></div>
					</div>
				</div>


				<h3>Preview:</h3>

				<div id="toilet"></div>
				<div id="filter_bar_wrapper">
					<div id="filter_bar">
						<div class="category_cloud">
							<?php
							
							$filters_array = json_decode($options['default_filters']);
				
							foreach ($filters_array as $key => $data) {
								$type = substr($key,0,1);
								$txid = substr($key,1);
								switch ($type) {
									case 'c': $rel = 'category'; break;
									case 'g': $rel = 'genre'; break;
									case 'q': $rel = 'query'; break;
									case 't': $rel = 'tag'; break;
									case 'x': $rel = 'xustom'; break;
									default: $rel = '';
								}
								$toggle_name = $data[0];
								$toggle_state = empty($data[1]) ? 'disabled' : '';
								if (!empty($toggle_name)) {
									echo '<div id="taxonomy-'.$rel.'-'.$txid.'" class="cat_toggle '.$toggle_state.' '.$rel.'" rel="'.$rel.'"><span>'.$toggle_name.'</span></div>';
								}
							}
							
							?>
						</div>
						<div class="clear">&nbsp;</div>
					</div>
				</div>
			</div>

			<input id="filters_json_field" type="hidden" name="aq_theme_options[default_filters]" value='<?php esc_attr_e($options["default_filters"]); ?>' />

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'aq6' ); ?>" />
			</p>
		</form>

		<script type="text/javascript">
			
			var filters_json = <?php echo $options['default_filters']; ?>;
			
			jQuery(document).ready(function($) {
								
				$(".category_cloud").disableSelection();

				$(".column .draggable > div").draggable({
					zIndex: 2,
					appendTo: "body",
					helper: "clone",
					connectToSortable: '.category_cloud',
				});

				$(".category_cloud").sortable({
					appendTo: "body",
					helper: "clone",
					connectWith: $("#toilet"),
					receive: function(event,ui) {
						$(".category_cloud .ui-draggable").removeClass("ui-draggable").attr("id", ui.item[0].id.substr("7"));
					},
					update: function(event,ui) {
						update_filter_form();
					},
					remove: function(event, ui) { 
						ui.item.remove();
					}
				});

				$("#toilet").sortable();

				$("#filter_bar").delegate(".cat_toggle", "click", function(e) {
					$(this).toggleClass("disabled");
					update_filter_form();					
				});

				function update_filter_form() {
					filters_json = {};
					$(".category_cloud > div.cat_toggle").each(function(index) {
						var trail = $(this).attr("id").split("-");
						if ($(this).hasClass("disabled")) { active = false; } else { active = true; }
						filters_json[trail[1].substr(0,1)+trail[2]] = Array($(this).find("span").html(), active);
					});	
					$("#filters_json_field").val(JSON.stringify(filters_json));
					console.log(JSON.stringify(filters_json));
				}

				$("#tag-input").suggest("<?php echo get_bloginfo('stylesheet_directory'); ?>/ajax/ajax.tags.php?tax=post_tag", { multiple: false });
				$("#tag-input").keypress(function(e) {
				$("#tag-input-wrapper .draggable").disableSelection();
					if (e.which == 13) {
						e.preventDefault();
						var tag_id = /{(\d+)}/.exec($(this).val());
						var tag_name = $(this).val().replace(/{\d+}/,"");
						if (tag_id != null) {
							$("#tag-input-wrapper .draggable").append('<div id="source-taxonomy-tag-'+tag_id[1]+'" class="cat_toggle tag" rel="tag"><span>'+tag_name+'<\/span><\/div>');
							refresh_tag_drag();
						}
						$(this).val("");
					}
				});
				
				function refresh_tag_drag() {
					$("#tag-input-wrapper .draggable > div").draggable({
						zIndex: 2,
						appendTo: "body",
						helper: "clone",
						connectToSortable: '.category_cloud',
					});
				}

			});		

		</script>

		<style type="text/css">

			#theme_options_content h3 {
				clear: both;
			}

			.cat_toggle {
				color: #FFF;
				font-weight: bold;
				text-transform: uppercase;
				float: left;
				padding: 3px 8px 2px 14px;
				margin-right: 3px;
				margin-bottom: 3px;
				border-radius: 4px;
				-moz-border-radius: 4px;
				background-color: #4A0602;
				background-image: url("<?php bloginfo('stylesheet_directory'); ?>/images/check-sprite.png");
				background-repeat: no-repeat;
				background-position: 4px 2px;
				cursor: pointer;
			}
			
			.cat_toggle.genre { background-color: #6A0701; }
			.cat_toggle.query { background-color: #8B0903; }
			
			.cat_toggle.disabled {
				background-position: 4px -15px;
			}
			.cat_toggle.disabled span {
				filter:alpha(opacity=40);
				-moz-opacity:0.4;
				-khtml-opacity: 0.4;
				opacity: 0.4;
			}
			
			.cat_toggle.stuck.add { background-position: 4px 0px; }
			.cat_toggle.stuck.kill { background-position: 4px -17px; }

			.column {
				float: left;
				margin-bottom: 20px;
				margin-right: 45px;
			}

			.column .cat_toggle {
				clear: both;
			}
			
			#tag-input-wrapper input {
				margin-bottom: 16px;
			}

			#filter_bar_wrapper {
				width: 640px;
				margin-top: 9px;
				border-top: 1px solid #511000;
				border-bottom: 1px solid #880402;
				background-color: #BB0E03;
			}
			#filter_bar {
				width: 640px;
				border-top: 1px solid #D30E05;
				border-bottom: 1px solid #D31104;
				background: #BB0E03;
				background: -webkit-gradient(linear, left top, left bottom, from(#9C0A02), to(#BB0E03));
				background: -moz-linear-gradient(top,  #9C0A02,  #BB0E03);
				filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#9C0A02', endColorstr='#BB0E03');
			}
			#filter_bar .cat_toggle {
				float: none;
				display: inline-block;
			}
			#filter_bar .header {
				float: left;
				margin-right: 10px;
				color: #F5D148;
				font-size: 14px;
				padding-top: 15px;
				padding-bottom: 14px;
				padding-left: 9px;
			}
			
			#filter_bar .category_cloud {
				width: 640px;
				margin-top: 16px;
				margin-left: 14px;
			}

			#filter_bar .clear {
				clear: both;
				height: 10px;
			}
			
			#toilet {
				position: absolute;
				width: 100px;
				height: 100px;
				margin-left: 640px;
				background-image: url("<?php bloginfo('stylesheet_directory'); ?>/images/toilet.jpg");
				background-position: center center;
				background-repeat: no-repeat;
			}
		
			.ui-sortable-placeholder {
				height: 7px !important;
			}
			
			.ac_results span.term_id {
				display: none;
			}
		
		</style>

	</div><?php

}

function theme_options_validate($input) {
//	$input['sometextarea'] = wp_filter_post_kses($input['sometextarea']);
	return $input;
}

?>