<?php if (has_tag('festivalfever') || has_category('festivals')) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("300x250_sponsor");
	</script>
<?php } elseif (is_home()) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("300x250_home");
	</script>
<?php } elseif (is_nsfw()) { ?>
	<div class="nsfw_ad_block" style="width: 300px; height: 250px;">
		<div>I am not an advertisement because this content makes wholesome people uncomfortable.</div>
	</div>
<?php } else { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("300x250_ros");
	</script>
<?php } ?>

<script type="text/javascript">		
	$(document).ready(function() {
		$(".ad_300x250").each(function(i,j) {
			var medrecHeight = $(this).height();
			if (medrecHeight < 600) {
				$(".ad_300x250").height(250);
			} else {
				$(".ad_300x250").height(600);
			}
		});
	});
</script>