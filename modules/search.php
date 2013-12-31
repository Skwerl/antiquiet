<div id="search_box" class="sidebar_module">

	<div class="header arvo">Search</div>

	<?php 

	$querystring = esc_attr(apply_filters('the_search_query', get_search_query()));
	if (empty($querystring)) { $querystring = ''; }

	?>
	
	<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
		<div>
			<input type="text" name="s" id="s" value="<?php echo $querystring; ?>"
			onblur="if (this.value == '') { this.value = '<?php echo $searchstring; ?>'; }"
			onfocus="if (this.value == '<?php echo $searchstring; ?>') { this.value = ''; }" />
			<input type="submit" id="searchsubmit" value="Suck It" />
		</div>
	</form>
	
</div>