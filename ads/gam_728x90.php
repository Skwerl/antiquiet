<?php if (is_home()) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("728x90_home");
	</script>
<?php } elseif (is_nsfw()) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("728x90_ratedr");
	</script>
<?php } else { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("728x90_ros");
	</script>
<?php } ?>