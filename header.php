<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php
	global $page, $paged;
	$site_description = get_bloginfo('description', 'display');
	if ($site_description && (is_home() || is_front_page())) {
		echo $site_description;
	} else {
		$category = get_the_category();
		$category_name = $category[0]->name;
		if ($category_name == 'Shows') { $category_name = 'Show Reviews'; }
		if ($category_name == 'Antiquiet Sessions') { $category_name = 'Sessions'; }
		if (is_category()) {
			echo $category_name;					
		} else {
			the_title();
		}
	}
	if ($paged >= 2 || $page >= 2) echo ' | '.sprintf(__('Page %s', 'aq6'), max($paged, $page));
	echo ' @ '; bloginfo('name');
	if (!is_category()) { echo ' '.$category_name; }
?></title>

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2869291-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

<script type="text/javascript" src="//pagead2.googlesyndication.com/pagead/js/google_top_exp.js"></script>

<!-- Begin comScore Tag -->
<script type="text/javascript">
  var _comscore = _comscore || [];
  _comscore.push({ c1: "2", c2: "6665296" });
  (function() {
    var s = document.createElement("script"), el = document.getElementsByTagName("script")[0]; s.async = true;
    s.src = (document.location.protocol == "https:" ? "https://sb" : "http://b") + ".scorecardresearch.com/beacon.js";
    el.parentNode.insertBefore(s, el);
  })();
</script>
<!-- End comScore Tag -->

<script type="text/javascript" src="http://partner.googleadservices.com/gampad/google_service.js">
</script>
<script type="text/javascript">
	GS_googleAddAdSenseService("ca-pub-2746694815475031");
	GS_googleEnableAllServices();
</script>
<script type="text/javascript">
	GA_googleAddSlot("ca-pub-2746694815475031", "728x90_home");
	GA_googleAddSlot("ca-pub-2746694815475031", "300x250_home");
	GA_googleAddSlot("ca-pub-2746694815475031", "728x90_ros");
	GA_googleAddSlot("ca-pub-2746694815475031", "300x250_ros");
	GA_googleAddSlot("ca-pub-2746694815475031", "728x90_ratedr");
	GA_googleAddSlot("ca-pub-2746694815475031", "300x250_ratedr");
	GA_googleAddSlot("ca-pub-2746694815475031", "300x100"); 
	GA_googleAddSlot("ca-pub-2746694815475031", "1x1_home"); 
	GA_googleAddSlot("ca-pub-2746694815475031", "1x1_ros");
</script>

<?php if (is_category('sessions')) { ?>
	<script type="text/javascript">
		GA_googleAddAttr("Sessions", "1");
		GA_googleAddAttr("Level", "3");
	</script>
<?php } else { ?>
	<script type="text/javascript">
		GA_googleAddAttr("Level", "1");
	</script>
<?php } ?>

<script type="text/javascript">
	GA_googleFetchAds();
</script>

<script type="text/javascript">
	var aq_ajax_path = "<?php echo get_stylesheet_directory_uri().'/ajax/'; ?>";
	var aq_ajax_cat = "<?php echo get_query_var('cat'); ?>";
	var aq_ajax_tag = "<?php echo get_query_var('tag'); ?>";
	var aq_ajax_author = "<?php echo get_query_var('author'); ?>";
	var aq_ajax_search = "<?php echo get_search_query(); ?>";
	var aq_ajax_tax = false;
	var aq_ajax_term = false;
</script>

<?php wp_head(); ?>

</head>

<body <?php body_class($class); ?>>

<div id="everything_wrapper" class="shadow">

	<div id="header_wrapper">
		<div class="logo"><a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/aq-logo.jpg" /></a></div>
		<div class="ad_728x90"><?php include(TEMPLATEPATH.'/ads/gam_728x90.php'); ?></div>
	</div>