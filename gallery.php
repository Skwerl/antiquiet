<?php

function aq_post_gallery($output, $attr) {

    global $post, $wp_locale;
    static $instance = 0;
    $instance++;

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby']) {
			unset($attr['orderby']);
		}
	}

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'dl',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 4,
        'size'       => 'thumbnail',
        'include'    => '',
        'exclude'    => ''
   ), $attr));

    $id = intval($id);
	if ('RAND' == $order) {
		$orderby = 'none';
	}

    if (!empty($include)) {
		$include = preg_replace('/[^0-9,]+/', '', $include);
		$_attachments = get_posts(array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
		$attachments = array();
		foreach ($_attachments as $key => $val) {
			$attachments[$val->ID] = $_attachments[$key];
		}
    } elseif (!empty($exclude)) {
        $exclude = preg_replace('/[^0-9,]+/', '', $exclude);
        $attachments = get_children(array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    } else {
        $attachments = get_children(array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby));
    }

    if (empty($attachments)) {
        return '';
	}

    if (is_feed()) {
		$output = "\n";
		foreach ($attachments as $att_id => $attachment) {
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		}
		return $output;
	}

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $output = apply_filters('gallery_style', "
        <style type='text/css'>
            #{$selector} {
				margin-left: -6px;
				margin-bottom: -6px;
				width: 632px;
            }
            #{$selector} .gallery-item {
			/*	width: {$itemwidth}%;	*/
				float: {$float};
				margin-bottom: 6px;
				margin-left: 6px;
				text-align: center;
			}
            #{$selector} img {
                border: 1px solid #000;
            }
            #{$selector} .gallery-caption {
                margin-left: 0;
            }
        </style>
        <div id='$selector' class='gallery galleryid-{$id}'>");
    $i = 0;
    foreach ($attachments as $id => $attachment) {
        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);
        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
            <{$icontag} class='gallery-icon'>
                $link
            </{$icontag}>";
        if ($captiontag && trim($attachment->post_excerpt)) {
            $output .= "
                <{$captiontag} class='gallery-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
        }
        $output .= "</{$itemtag}>";
        if ($columns > 0 && ++$i % $columns == 0) {
            $output .= '<br style="clear: both" />';
    	}
    }
    $output .= "
            <br style='clear: both;' />
        </div>\n";
    return $output;

}

add_filter('post_gallery', 'aq_post_gallery', 10, 2);

?>