<?php

$label = Functions::Filter($_GET['label']);

if(empty($label)){
	header("Location: " . Functions::Url('404'));
	exit();
}

$label = $dba->query('SELECT * FROM '.T_LABEL.' WHERE slug = ?', $label)->fetchArray();

if(empty($label)){
	header("Location: " . Functions::Url('404'));
	exit();
}

$TEMP['#page'] = 'tags';

$label_load = Loads::Tag($label['id']);

$TEMP['tag'] = ucwords($label['name']);
$TEMP['catag_id'] = $label['id'];
$TEMP['posts_result'] = $label_load['html'];

if($label_load['return']){
	$widget = Functions::GetWidget('horizposts');
	if($widget['return']){
		$TEMP['posts_result'] .= $widget['html'];
	}
	$widget = Functions::GetWidget('aside');
	if($widget['return']){
		$TEMP['content_aad'] = $widget['html'];
	}
	$query = '';
	if(!empty($label_load['catag_ids'])){
		$query = ' AND id NOT IN ('.implode(',', $label_load['catag_ids']).')';
	}
	$TEMP['catag_ids'] = implode(',', $label_load['catag_ids']);
}

$TEMP['#related_cat'] = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' ORDER BY RAND() DESC LIMIT 5')->fetchAll();

if(!empty($TEMP['#related_cat'])){
	foreach ($TEMP['#related_cat'] as $rlc) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $rlc['category_id'])->fetchArray();
		$TEMP['!type'] = $rlc['type'];

		$TEMP['!key'] += 1;
		$TEMP['!title'] = $rlc['title'];
		$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
		$TEMP['!category_slug'] = Functions::Url("{$RUTE['#r_category']}/{$category['slug']}");
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


$TEMP['#title']       = $TEMP['tag'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("tags/content");
?>