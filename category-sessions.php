<?php get_header(); ?>

<div id="content_wrapper">
	<div id="article_wrapper">

		<div class="column left">
			<div class="article_body">
				<article>
					<?php
					
					$category = get_the_category();
					$category_name = $category[0]->name;

					?>
					<h1 class="arvo"><span class="headline"><?php echo $category_name; ?></span></h1>

					<div class="divider">&nbsp;</div>

					<div class="bigbanner"><a href="/exclusive/sessions/2014/01/mike-doughty-plays-soul-coughing/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2014/01/doughty-big.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2013/08/pos-antiquiet-session/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2013/07/pos-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2013/05/antiquiet-sessions-various-cruelties/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2013/05/vc-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2013/01/walking-papers/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2013/01/wp-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2012/11/open-hand-antiquiet-session/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2012/11/openhand-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2012/10/jeff-the-brotherhood-antiquiet-sessions/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2012/10/jtb-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2012/08/8mm-bedroom-session/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2012/08/8mm-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2012/05/dirty-ghosts-antiquiet-session/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2012/08/dirtyghosts-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2012/04/alain-johannes-antiquiet-sessions/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2012/08/alain-big-banner.jpg" />
					</a></div>

					<div class="bigbanner"><a href="/exclusive/sessions/2012/04/dead-sara-acoustic/">
						<img src="http://cdn.antiquiet.com/wp-content/uploads/2012/08/deadsara-big-banner.jpg" />
					</a></div>

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