<?php if (is_home()) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("300x250_home");
	</script>
<?php } elseif (is_nsfw()) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("300x250_ratedr");
	</script>
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