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
		$TEMP['!key'] += 1;
		$TEMP['!title'] = $rlc['title'];
		$TEMP['!url'] = Specific::Url($rlc['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($rlc['thumbnail'], 1, 's');
		$TEMP['related_aside'] .= Specific::Maket('includes/related-aside');
	}
	Specific::DestroyMaket();
}

$TEMP['newsletter'] = Specific::Maket('includes/newsletter');

$TEMP['#page']         = 'post';
$TEMP['#title']        = $post['title'];
$TEMP['#description']  = $post['description'];
$TEMP['#keyword']      = implode(',', $post_load['keywords']);
$TEMP['#content']      = Specific::Maket('post/content');
?>