<?php

$category = Specific::Filter($_GET['category']);

if(empty($category)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$category = $dba->query('SELECT * FROM '.T_CATEGORY.' WHERE slug = ?', $category)->fetchArray();

if(empty($category)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$TEMP['#page'] = 'category';

$category_load = Load::Category($category['id']);

$category_name = $TEMP['#word']["category_{$category['name']}"];

$TEMP['category_name'] = $category_name;
$TEMP['category_slug'] = strtolower($category_name);
$TEMP['catag_id'] = $category['id'];
$TEMP['posts_result'] = $category_load['html'];

$widget = Specific::GetWidget('horizposts');
if($widget['return']){
	$TEMP['posts_result'] .= $widget['html'];
}

$widget = Specific::GetWidget('aside');
if($widget['return']){
	$TEMP['content_aad'] = $widget['html'];
}

$query = '';
if(!empty($category_load['catag_ids'])){
	$query = ' AND id NOT IN ('.implode(',', $category_load['catag_ids']).')';
}

$TEMP['#related_cat'] = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' ORDER BY RAND() DESC LIMIT 5')->fetchAll();

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

$TEMP['catag_ids'] = implode(',', $category_load['catag_ids']);
$TEMP['form_newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/includes/form-newsletter');
$TEMP['newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/newsletter');



$TEMP['#title']       = $category_name . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("category/content");
?>