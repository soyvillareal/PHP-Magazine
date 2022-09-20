<?php

$slug = Specific::Filter($_GET['one']);
$post = $dba->query('SELECT * FROM '.T_POST.' WHERE slug = ? AND status = "approved"', $slug)->fetchArray();
if(empty($post)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$post_load = Load::Post($post);
$TEMP['main'] = $post_load['html'];

$post_load['post_ids'][] = $post['id'];

$related_cat = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND id NOT IN ('.implode(',', $post_load['post_ids']).') AND status = "approved" ORDER BY views DESC LIMIT 5', $post['category_id'])->fetchAll();

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

$TEMP['form_newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/includes/form-newsletter');
$TEMP['newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/newsletter');

$TEMP['#page']         = 'post';
$TEMP['#title']        = $post['title'];
$TEMP['#description']  = $post['description'];
$TEMP['#keyword']      = implode(',', $post_load['keywords']);
$TEMP['#content']      = Specific::Maket('post/content');
?>