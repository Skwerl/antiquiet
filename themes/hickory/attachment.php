<?php get_header(); ?>
			
	<?php if (have_posts()) { while (have_posts()) { the_post(); ?>
					
	<div class="content<?php if(vp_metabox('hickory_post.post_size') == 'full_post') : ?> fullpost<?php else : ?> sidebar<?php endif; ?>">
	
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<div class="post-entry">

				<p class="attachment"><?php echo wp_get_attachment_image($post->ID, 'large'); ?></p>

				<?php if(vp_metabox('hickory_post.show_share_buttons') == 1) : ?>
				<?php else : ?>
				<div class="post-share">
					
					<span class="share-text">
						<?php _e('Share', 'hickory'); ?>
					</span>
					
					<span class="share-item">
						<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="Check out this article: <?php the_title(); ?> - <?php the_permalink(); ?>" data-dnt="true">Tweet</a>
						<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
					</span>
					
					<span class="share-item">
						<iframe src="//www.facebook.com/plugins/like.php?locale=en_US&amp;href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;width=250&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>
					</span>
					<span class="share-item google">
						<div class="g-plusone" data-size="medium" data-href="<?php the_permalink(); ?>"></div>
					</span>
					<?php
						// try getting featured image
						$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
						if( ! $featured_img )
						{
							$featured_img = '';
						}
						else
						{
							$featured_img = $featured_img[0];
						}
					?>
					<span class="share-item">
						<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>
							&amp;media=<?php echo $featured_img; ?>
							&amp;description=<?php echo urlencode(get_the_title()); ?>" 
							class="pin-it-button" 
							count-layout="horizontal">
							<img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" />
						</a>
					</span>
					
				</div>
				<?php endif; ?>

			</div>
			
		</div>
	
	</div>

<?php }} ?>

<?php get_sidebar(); ?>
			
<?php get_footer(); ?>