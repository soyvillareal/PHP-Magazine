<?php

$slug = Specific::Filter($_GET['slug']);
$post = $dba->query('SELECT * FROM '.T_POST.' WHERE slug = ?', $slug)->fetchArray();
if(empty($post)){
	header("Location: " . Specific::Url('404'));
	exit();
}

if(!Specific::IsOwner($post['user_id'])){
	if($TEMP['#moderator'] == false && $post['status'] != 'approved'){
		header("Location: " . Specific::Url('404'));
		exit();
	}
} else if($post['status'] == 'deleted'){
	header("Location: " . Specific::Url('404'));
	exit();
}

if(!in_array($post['user_id'], Specific::BlockedUsers(false)) || Specific::IsOwner($post['user_id'])){
	$post_load = Load::Post($post, false, true);
	$TEMP['main'] = $post_load['html'];

	$widget = Specific::GetWidget('ptop', 'amp');
	if($widget['return']){
		$TEMP['advertisement_ptad'] = $widget['html'];
	}

	$TEMP['#keyword']      = implode(',', $post_load['keywords']);
	$TEMP['#content']      = Specific::Maket('amp/content');
} else {
	$TEMP['#content']      = Specific::Maket('includes/post-amp/locked');
}


$TEMP['#page']         = 'amp-post';
$TEMP['#title']        = $post['title'];
$TEMP['#description']  = $post['description'];
?>