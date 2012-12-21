<?php $post = $main_post; ?>

<?php if (in_category(array('shows','sessions','aqu'))) { ?>

<div style="margin-top: 8px;">
	<!-- begin Antiquiet Tag: Medium Rectangle, 300x250 -->
	<iframe src="http://cdn.antiquiet.com/ads/backup/300x250_pass_2.html" width="300" height="250" scrolling="no" marginwidth="0" marginheight="0" frameborder="0" vspace="0" hspace="0"></iframe>
	<!-- end Antiquiet Tag: Medium Rectangle, 300x250 -->
</div>

<?php } else { ?>

<div style="margin-top: 8px;">
	<script type="text/javascript">
	  GA_googleFillSlot("300x250_home");
	</script>
</div>

<?php } ?>