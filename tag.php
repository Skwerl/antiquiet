<?php

$tag_url = get_home_url().'/#/tag/'.$wp_query->query_vars['tag'].'/';

header('HTTP/1.1 301 Moved Permanently'); 
header('Location: '.$tag_url);

?>