<?php

$slug = Specific::Filter($_GET['one']);
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

	$TEMP['#is_approved'] = false;
	if($post['status'] == 'approved'){
		$TEMP['#is_approved'] = true;
	}
	$post_load = Load::Post($post);
	$TEMP['main'] = $post_load['html'];

	$post_load['post_ids'][] = $post['id'];

	$widget = Specific::GetWidget('ptop');
	if($widget['return']){
		$TEMP['advertisement_ptad'] = $widget['html'];
	}

	$widget = Specific::GetWidget('aside');
	if($widget['return']){
		$TEMP['content_aad'] = $widget['html'];
	}

	$TEMP['#related_cat'] = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND id NOT IN ('.implode(',', $post_load['post_ids']).') AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY views DESC LIMIT 5', $post['category_id'])->fetchAll();

	if(!empty($TEMP['#related_cat'])){
		foreach ($TEMP['#related_cat'] as $rlc) {
			$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $rlc['category_id'])->fetchArray();
			$TEMP['!type'] = $rlc['type'];
			
			$TEMP['!key'] += 1;
			$TEMP['!title'] = $rlc['title'];
			$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
			$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
			$TEMP['!url'] = Specific::Url($rlc['slug']);
			$TEMP['!thumbnail'] = Specific::GetFile($rlc['thumbnail'], 1, 's');
			$TEMP['!published_date'] = date('c', $rlc['published_at']);
			$TEMP['!published_at'] = Specific::DateString($rlc['published_at']);
			$TEMP['related_aside'] .= Specific::Maket('includes/search-post-profile-category-tag/related-aside');
		}
		Specific::DestroyMaket();
	}

	$TEMP['form_newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/includes/form-newsletter');
	$TEMP['newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/newsletter');
	$TEMP['#keyword']      = implode(',', $post_load['keywords']);
	$TEMP['#content']      = Specific::Maket('post/content');

} else {
	$TEMP['#content']      = Specific::Maket('includes/post-amp/locked');
}


$TEMP['#page']         = 'post';
$TEMP['#title']        = $post['title'];
$TEMP['#description']  = $post['description'];
?>