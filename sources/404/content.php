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

header("HTTP/1.0 404 Not Found");

$TEMP['#random_posts'] = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY RAND() ASC LIMIT 4')->fetchAll();

if(!empty($TEMP['#random_posts'])){
	foreach ($TEMP['#random_posts'] as $post) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
		$TEMP['!id'] = $post['id'];
		$TEMP['!type'] = $post['type'];

		$TEMP['!title'] = $post['title'];
		$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
		$TEMP['!category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");
		$TEMP['!url'] = Functions::Url($post['slug']);
		$TEMP['!thumbnail'] = Functions::GetFile($post['thumbnail'], 1, 's');
		$TEMP['!published_date'] = date('c', $post['published_at']);
		$TEMP['!published_at'] = Functions::DateString($post['published_at']);

		$TEMP['random_posts'] .= Functions::Build('404/includes/random-posts');
		$home_ids[] = $post['id'];
	}
	Functions::DestroyBuild();
}

$TEMP['#page'] = '404';
$TEMP['#title'] = '404 - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords'] = $TEMP['#settings']['keywords'];

$TEMP['#content'] = Functions::Build('404/content');
?>