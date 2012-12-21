<?php

$category = get_the_category(); 
$category_url = get_home_url().'/#/'.$category[0]->slug.'/';

header('HTTP/1.1 301 Moved Permanently'); 
header('Location: '.$category_url);

?>