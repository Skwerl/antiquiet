<?php get_header(); ?>
	
<div id="hero_wrapper">
	<div id="feature_panel" class="shadow">
		<?php

		$panel_index = 0;
		$feature_thumbs = array();
		$feature_panels = array();

		remove_filter('get_the_excerpt', 'strip_excerpts');

		query_posts('meta_key=featurepanel_show&meta_value=1&posts_per_page=10');
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