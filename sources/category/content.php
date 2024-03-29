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

$category = Functions::Filter($_GET['category']);

if(empty($category)){
	header("Location: " . Functions::Url('404'));
	exit();
}

$category = $dba->query('SELECT * FROM '.T_CATEGORY.' WHERE slug = ?', $category)->fetchArray();

if(empty($category)){
	header("Location: " . Functions::Url('404'));
	exit();
}

$TEMP['#page'] = 'category';

$category_load = Loads::Category($category['id']);

$category_name = $TEMP['#word']["category_{$category['name']}"];

$TEMP['category_name'] = $category_name;
$TEMP['category_slug'] = $category['slug'];
$TEMP['catag_id'] = $category['id'];
$TEMP['posts_result'] = $category_load['html'];

if($category_load['return']){
	$widget = Functions::GetWidget('horizposts');
	if($widget['return']){
		$TEMP['posts_result'] .= $widget['html'];
	}

	$widget = Functions::GetWidget('aside');
	if($widget['return']){
		$TEMP['content_aad'] = $widget['html'];
	}

	$query = '';
	if(!empty($category_load['catag_ids'])){
		$query = ' AND id NOT IN ('.implode(',', $category_load['catag_ids']).')';
	}
	$TEMP['catag_ids'] = implode(',', $category_load['catag_ids']);
}

$TEMP['#related_cat'] = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' ORDER BY RAND() DESC LIMIT 5')->fetchAll();

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



$TEMP['#title']       = $category_name . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("category/content");
?>