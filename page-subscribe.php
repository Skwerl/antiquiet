<?php get_header(); ?>

<div id="content_wrapper">
	<div id="article_wrapper">

		<div class="column left">
			<div class="article_body">
				<article>

					<h1 class="arvo"><span class="headline">Friends of Antiquiet</span></h1>

					<div class="divider">&nbsp;</div>

					<?php include(TEMPLATEPATH.'/modules/mailchimp.php'); ?>

				</article>

			</div>
		</div>
		
		<div class="column right">

			<div class="ad_300x250"><?php include(TEMPLATEPATH.'/ads/gam_300x250.php'); ?></div>

			<div class="promo_300x100"><?php include(TEMPLATEPATH.'/ads/gam_300x100.php'); ?></div>

		</div>

		<div class="clear"></div>

	</div>
</div>

<?php get_footer(); ?>