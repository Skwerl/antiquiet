<?php

$slugs_to_ids = array();
$all_categories = get_categories();
foreach ($all_categories as $category) { $slugs_to_ids[$category->slug] = $category->term_id; }
$slugs_to_ids = json_encode($slugs_to_ids);

?>

<script type="text/javascript">

	$(document).ready(function() {
		var catIDs = <?php echo $slugs_to_ids; ?>;
		var urlParts = window.location.hash.split("/");
		if (urlParts[0] == "#") {
			var catID = catIDs[urlParts[1].toLowerCase()];
			if (typeof(catID) != "undefined") {
				$(".cat_toggle.category").addClass("disabled");
				$("#taxonomy-category-"+catID).removeClass("disabled");
				filters_customized = false;
				filter_articles();
			} else {
				if (urlParts[1].toLowerCase() == "author") {
					author_filter = urlParts[2];
					filters_customized = false;
					filter_articles();
				}
			}
		}
	});

</script>