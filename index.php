<?php get_header(); ?>

<div id="content_wrapper">

	<div id="main_content" class="column left">

		<div id="hero_wrapper">		
			<?php if (is_home()) { include('modules/featurepanel.php'); } ?>
		</div>

		<div id="article_list">
			
			<div class="list_header">

				<?php if (is_home()) { ?><div class="header arvo">Latest Articles</div><?php } ?>
				<?php if (is_category()) { ?><div class="header arvo">All in <?php single_cat_title(); ?></div><?php } ?>
				<?php if (is_tag()) { ?><div class="header arvo">Articles Tagged: <?php single_cat_title(); ?></div><?php } ?>
				<?php if (is_author()) { ?><div class="header arvo">All by <?php the_author(); ?></div><?php } ?>

				<?php if (is_tax()) {
					$custom_tax = $wp_query->get_queried_object();
					$artist_name = $custom_tax->name;
					?><div class="header arvo">Articles About: <?php echo $artist_name; ?></div>
					<script type="text/javascript">
						aq_ajax_tax = "<?php echo $custom_tax->taxonomy; ?>";					
						aq_ajax_term = "<?php echo $custom_tax->slug; ?>";					
					</script><?php
				} ?>

			</div>

			<div class="divider">&nbsp;</div>

			<div id="article_loader">
				<?php include_once('ajax/ajax.articles.php'); ?>
			</div>
			<div id="scroller"><div class="label">&nbsp;</div></div>

		</div>

	</div>

	<div id="right_sidebar" class="column right">
	
		<div class="ad_300x250"><?php include(TEMPLATEPATH.'/ads/gam_300x250.php'); ?></div>
		
		<div class="promo_300x100"><?php include(TEMPLATEPATH.'/ads/gam_300x100.php'); ?></div>

		<div class="sidebar_modules">
	
			<?php include('modules/facebucket.php'); ?>
		
			<?php include('modules/twatter.php'); ?>
		
			<?php include('modules/footerish.php'); ?>
		
		</div>
	
	</div>

	<div class="clear"></div>

</div>

<?php include('js/navigator.php'); ?>

<?php get_footer(); ?>