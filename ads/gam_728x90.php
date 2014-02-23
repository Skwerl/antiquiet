<?php if (has_tag('festivalfever') || has_category('festivals')) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("728x90_sponsor");
	</script>
<?php } elseif (is_home()) { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("728x90_home");
	</script>
<?php } elseif (is_nsfw()) { ?>
	<div class="nsfw_ad_block" style="width: 728px; height: 90px;">
		<div>I am not an advertisement because this content makes wholesome people uncomfortable.</div>
	</div>
<?php } else { ?>
	<script type="text/javascript">
	  GA_googleFillSlot("728x90_ros");
	</script>
<?php } ?>