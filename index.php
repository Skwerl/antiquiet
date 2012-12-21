<?php get_header(); ?>
	
<div id="hero_wrapper">
	<div id="feature_panel" class="shadow">
		<?php

		$panel_index = 0;
		$feature_thumbs = array();
		$feature_panels = array();
		query_posts('meta_key=featurepanel_show&meta_value=1&posts_per_page=10');

		remove_filter('get_the_excerpt', 'strip_excerpts');

		while (have_posts()) {
			the_post();
			$panel_index++;			
			$bigger_image = aq_get_thumbnail($post->ID, 'feature-panel');
			$little_image = aq_get_thumbnail($post->ID, 'itsy-bitsy');
			$feature_thumbs[] = '<div class="thumbnail shadow"><a href="#panel-'.$panel_index.'"><img src="'.$little_image.'" /></a></div>';
			$feature_panels[] = '<div id="panel-'.$panel_index.'" class="panel" href="'.get_permalink().'">
					<div class="text">
						<span class="title arvo">'.get_the_title().'</span>
						<span class="subhead"><span class="category">'.implode(', ',aq_get_categories($post->ID)).':</span> '.get_the_excerpt().'</span>
					</div>
					<div class="fadeout">&nbsp;</div>
					<div class="image"><img src="'.$bigger_image.'" /></div>
				</div>';
		}

		// regularly scheduled programming...
		add_filter('get_the_excerpt', 'strip_excerpts');
		
		?>
		<div class="thumbnails"><?php echo implode('',$feature_thumbs); ?></div>
		<div class="panels"><?php echo implode('',$feature_panels); ?></div>
	</div>
	<div class="ad_300x250"><?php include(TEMPLATEPATH.'/ads/gam_300x250.php'); ?></div>
	<div class="promo_300x100"><?php include(TEMPLATEPATH.'/ads/gam_300x100.php'); ?></div>
</div>

<div id="filter_bar_wrapper">
	<div id="filter_bar">
		<div class="header arvo">We Are Your Music Filter</div>
		<div id="filter_buttons" class="filters">
			<div class="cat_bubble postflag_audio tip" title="Audio"></div>
			<div class="cat_bubble postflag_video tip" title="Video"></div>
			<div class="cat_bubble postflag_photos tip" title="Photos"></div>
			<div class="cat_bubble postflag_live tip" title="Live"></div>
			<div class="cat_bubble postflag_tv tip" title="TV"></div>
			<div class="cat_bubble postflag_album tip" title="Albums"></div>
			<div class="cat_bubble postflag_download tip" title="Downloads"></div>
			<div class="cat_bubble postflag_nsfw tip" title="NSFW"></div>
			<div class="cat_bubble rss tip" title="Get RSS"></div>
			<div class="cat_bubble reset tip" title="Reset All"></div>
		</div>
		<div class="category_cloud">
			<?php aq_build_filters(true); ?>
		</div>
		<div class="divider">&nbsp;</div>
		<div id="custom_filter">
			<div class="header arvo">Custom Search</div>
			<div class="form"><form><input type="text" name="query" id="query" class="smart" title="Artist or Keyword" /></form></div>
			<div id="add_filter" class="cat_toggle stuck add"><span>Find</span></div>
			<div id="add_block" class="cat_toggle stuck kill"><span>Block</span></div>
		</div>
		<div class="clear">&nbsp;</div>
		<div id="search_feedback" class="arvo">...</div>
	</div>
</div>
<div id="filter_bar_shadow">&nbsp;</div>

<div id="content_wrapper" class="clear">
	<div id="main_content" class="column left">
		<div id="hidden_counter"><span class="recent"><a href="#">Title</a></span> <span class="count">and 0 other articles are</span> hidden. <a href="javascript:void(0);" class="why">Why?</a></div>
		<div id="article_loader">
			<?php include_once('ajax/ajax.articles.php'); ?>
		</div>
		<div id="scroller"><div class="label">&nbsp;</div></div>
	</div>

	<div id="right_sidebar" class="column right">

		<?php include('modules/available.php'); ?>

		<?php include('modules/facebucket.php'); ?>

		<?php include('modules/twatter.php'); ?>

		<?php include('modules/footerish.php'); ?>

	</div>

	<div class="clear"></div>

</div>

<?php include('js/navigator.php'); ?>

<?php get_footer(); ?>