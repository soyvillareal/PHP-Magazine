<?php

$slug = Functions::Filter($_GET['slug']);
$post = $dba->query('SELECT * FROM '.T_POST.' WHERE slug = ? AND status = "approved" AND published_at <> 0', $slug)->fetchArray();
if(empty($post)){
	header("Location: " . Functions::Url('404'));
	exit();
}

if(!in_array($post['user_id'], Functions::BlockedUsers(false)) || Functions::IsOwner($post['user_id'])){
	$post_load = Loads::Post($post, false, true);
	$TEMP['main'] = $post_load['html'];

	$widget = Functions::GetWidget('ptop', 'amp');
	if($widget['return']){
		$TEMP['advertisement_ptad'] = $widget['html'];
	}

	$TEMP['#keyword']      = implode(',', $post_load['keywords']);
	$TEMP['#content']      = Functions::Build('amp/content');
} else {
	$TEMP['#content']      = Functions::Build('includes/post-amp/locked');
}


$TEMP['#page']         = 'amp-post';
$TEMP['#title']        = $post['title'];
$TEMP['#description']  = $post['description'];
?>