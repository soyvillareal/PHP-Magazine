<?php

// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

$slug = Functions::Filter($_GET['one']);
$post = $dba->query('SELECT * FROM '.T_POST.' WHERE slug = ?', $slug)->fetchArray();
if(empty($post)){
	header("Location: " . Functions::Url('404'));
	exit();
}

if(!Functions::IsOwner($post['user_id'])){
	if($TEMP['#moderator'] == false && ($post['status'] != 'approved' || $post['published_at'] == 0)){
		header("Location: " . Functions::Url('404'));
		exit();
	}
} else if($TEMP['#moderator'] == false && $post['status'] == 'deleted'){
	header("Location: " . Functions::Url('404'));
	exit();
}

$TEMP['#og_type'] = 'article';

if(!in_array($post['user_id'], Functions::BlockedUsers(false)) || Functions::IsOwner($post['user_id'])){

	$post_load = Loads::Post($post);
	$TEMP['main'] = $post_load['html'];

	$post_load['post_ids'][] = $post['id'];

	$widget = Functions::GetWidget('ptop');
	if($widget['return']){
		$TEMP['advertisement_ptad'] = $widget['html'];
	}

	$widget = Functions::GetWidget('aside');
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
			$TEMP['!category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");
			$TEMP['!url'] = Functions::Url($rlc['slug']);
			$TEMP['!thumbnail'] = Functions::GetFile($rlc['thumbnail'], 1, 's');
			$TEMP['!published_date'] = date('c', $rlc['published_at']);
			$TEMP['!published_at'] = Functions::DateString($rlc['published_at']);
			$TEMP['related_aside'] .= Functions::Build('includes/search-post-profile-category-tag/related-aside');
		}
		Functions::DestroyBuild();
	}

	$TEMP['form_newsletter'] = Functions::Build('includes/search-post-profile-category-tag/includes/form-newsletter');
	$TEMP['newsletter'] = Functions::Build('includes/search-post-profile-category-tag/newsletter');
	$TEMP['#keywords']      = implode(',', $post_load['keywords']);
	$TEMP['#content']      = Functions::Build('post/content');

} else {
	$TEMP['#content']      = Functions::Build('includes/post-amp/locked');
}


$TEMP['#page']         = 'post';
$TEMP['#title']        = $post['title'].' - '.$TEMP['#settings']['title'];
$TEMP['#description']  = $post['description'];
?>