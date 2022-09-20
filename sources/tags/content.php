<?php

$label = Specific::Filter($_GET['label']);

if(empty($label)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$label = $dba->query('SELECT * FROM '.T_LABEL.' WHERE slug = ?', $label)->fetchArray();

if(empty($label)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$TEMP['#page']        = 'tags';

$label_load = Load::Tag($label['id']);

$TEMP['tag'] = ucwords($label['name']);
$TEMP['catag_id'] = $label['id'];
$TEMP['posts_result'] = $label_load['html'];

$query = '';
if(!empty($label_load['catag_ids'])){
	$query = ' AND id NOT IN ('.implode(',', $label_load['catag_ids']).')';
}

$related_cat = $dba->query('SELECT * FROM '.T_POST.' WHERE status = "approved"'.$query.' ORDER BY RAND() DESC LIMIT 5')->fetchAll();

if(!empty($related_cat)){
	foreach ($related_cat as $rlc) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $rlc['category_id'])->fetchArray();
		$TEMP['!type'] = $rlc['type'];

		$TEMP['!key'] += 1;
		$TEMP['!title'] = $rlc['title'];
		$TEMP['!category'] = $category['name'];
		$TEMP['!category_slug'] = $category['slug'];
		$TEMP['!url'] = Specific::Url($rlc['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($rlc['thumbnail'], 1, 's');
		$TEMP['!published_date'] = date('c', $rlc['published_at']);
		$TEMP['!published_at'] = Specific::DateString($rlc['published_at']);
		$TEMP['related_aside'] .= Specific::Maket('includes/search-post-profile-category-tag/related-aside');
	}
	Specific::DestroyMaket();
}

$TEMP['catag_ids'] = implode(',', $label_load['catag_ids']);
$TEMP['form_newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/includes/form-newsletter');
$TEMP['newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/newsletter');


$TEMP['#title']       = $TEMP['tag'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("tags/content");
?>