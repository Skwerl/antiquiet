<!--
<div class="ad_160x600">
	<?php include(TEMPLATEPATH.'/ads/gam_160x600.php'); ?>
</div>
//-->

<div id="comments">

	<?php if (post_password_required()) { ?>
		<p class="nopassword"><?php _e('This post is password protected. Enter the password to view any comments.', 'aq6'); ?></p></div>
	<?php return; } ?>
	
	<?php if (have_comments()) { ?>
	
		<div id="comments-title">
			<?php printf(_n('One comment', '%1$s comments', get_comments_number(), 'aq6'), number_format_i18n( get_comments_number()), '<span>'.get_the_title().'</span>'); ?>
		</div>
	
		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) { ?>
	
		<nav id="comment-nav-above">
			<div class="assistive-text"><?php _e('Comment navigation', 'aq6'); ?></div>
			<div class="nav-previous"><?php previous_comments_link(__('&larr; Older Comments', 'aq6')); ?></div>
			<div class="nav-next"><?php next_comments_link(__('Newer Comments &rarr;', 'aq6')); ?></div>
		</nav>
	
		<?php } ?>
	
		<ol class="commentlist">
			<?php wp_list_comments(array('reply_text' => '<span class="pretty parent">Reply</span>')); ?>
		</ol>
	
		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) { ?>
	
		<nav id="comment-nav-below">
			<div class="assistive-text"><?php _e( 'Comment navigation', 'aq6' ); ?></div>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'aq6' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'aq6' ) ); ?></div>
		</nav>
		
		<?php } elseif (!comments_open() && ! is_page() && post_type_supports(get_post_type(), 'comments')) { ?>
			<p class="nocomments"><?php _e( 'Comments are closed.', 'aq6' ); ?></p>
		<?php } ?>
	
	<?php } ?>
	
	<?php comment_form(); ?>
	
	<?php
	
	$post_id = $post->ID;
	$comment_karma_array = array();

	if (false === ($cached_karma = get_transient('comment-karma-post-'.$post_id))) {
		$ratings = $wpdb->get_results("
			SELECT $wpdb->commentmeta.comment_id, $wpdb->commentmeta.meta_value
			FROM $wpdb->commentmeta
			INNER JOIN $wpdb->comments on $wpdb->comments.comment_id=$wpdb->commentmeta.comment_id
			WHERE $wpdb->commentmeta.meta_key='comment_karma' 
			AND $wpdb->comments.comment_post_id = $post_id 
			AND $wpdb->comments.comment_approved = 1
			GROUP BY comment_id
		");
		foreach ($ratings as $set) {
			$split = json_decode($set->meta_value);
			$comment_karma_array[$set->comment_id] = array(count($split[0]), count($split[1]));
		}
		set_transient('comment-karma-post-'.$post_id, $comment_karma_array);
		$cached_karma = $comment_karma_array;
	}

	?>

</div>

<script type="text/javascript">

var karma_json = <?php echo json_encode($cached_karma); ?>;

$(document).ready(function() {

	$(".comment-meta").after($("<div/>").addClass("comment-text"));
	$(".comment-meta").after($("<div/>").addClass("comment-header"));
	$(".comment-author").each(function() { $(this).appendTo($(this).parent().find(".comment-header")); });
	$(".comment-meta").each(function() { $(this).appendTo($(this).parent().find(".comment-header")); });
	$(".comment-body p").each(function() { $(this).appendTo($(this).parent().find(".comment-text")); });
	$("div.reply").each(function() { $(this).parent().find(".comment-author").before($(this)); });
	$("span.says").remove();

	$("<div/>").addClass("likebutton voteup").html("").appendTo(".comment-header").attr("title","Like").tipsy();
	$("<div/>").addClass("likebutton votedn").html("").appendTo(".comment-header").attr("title","Dislike").tipsy();
	$(".likebutton").each(function() {
		var comment_id = $(this).closest("li").attr("id").substr(8);
		if (karma_json[comment_id]) {
			$(this).parent().find(".votedn").html(karma_json[comment_id][0]);
			$(this).parent().find(".voteup").html(karma_json[comment_id][1]);
		} else {
			$(this).html("0");
		}
	});
	$(".likebutton.voteup").click(function() { comment_vote(1,this); });
	$(".likebutton.votedn").click(function() { comment_vote(0,this); });

	$("p.logged-in-as a").last().html("Logout");

	if ($("li.comment").size() > 0) { $("#reply-title").before($("<div/>").addClass("divider")); }
	
	$(".form-allowed-tags").before($("<a/>").html("HTML Allowed").addClass("tags-info").click(function() {
		$(this).hide();
		$(".form-allowed-tags").show();
	})).click(function() {
		$(this).hide();
		$("a.tags-info").show();
	});

	$(".pretty.parent").removeClass("pretty parent").parent().addClass("pretty button");
	$("#cancel-comment-reply-link").addClass("pretty button");
	$("#submit").button().after($("#cancel-comment-reply-link"));
	$(".pretty.button").button();

	$("#cancel-comment-reply-link span.ui-button-text").html("Cancel Reply");

});

function comment_vote(d,obj) {
	$.post("<?php bloginfo('stylesheet_directory'); ?>/ajax/ajax.liker.php", {
		auth_key: "<?php echo wp_create_nonce('comment-liker-nonce'); ?>",
		comment_id: $(obj).closest("li").attr("id").substr(8),
		fb_user_id: $("input[name=sfc_user_id]").val(),
		wp_post_id: "<?php echo $post_id; ?>",
		direction: d
	}, function(data) {
		if (data.no) {
			alert(data.no);
		} else {
			$(obj).parent().find(".votedn").html(data.ok[0]);
			$(obj).parent().find(".voteup").html(data.ok[1]);
		}
	}, "json");
}

</script>