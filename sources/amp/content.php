<?php

$slug = Specific::Filter($_GET['slug']);
$post = $dba->query('SELECT * FROM '.T_POST.' WHERE slug = ? AND status = "approved"', $slug)->fetchArray();
if(empty($post)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$post_load = Load::Post($post, false, true);
$TEMP['main'] = $post_load['html'];


$TEMP['#page']         = 'amp-post';
$TEMP['#title']        = $post['title'];
$TEMP['#description']  = $post['description'];
$TEMP['#keyword']      = implode(',', $post_load['keywords']);
$TEMP['#content']      = Specific::Maket('amp/content');
?>