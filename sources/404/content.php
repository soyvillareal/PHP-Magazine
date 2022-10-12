<?php 
header("HTTP/1.0 404 Not Found");

$TEMP['#random_posts'] = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY RAND() ASC LIMIT 4')->fetchAll();

if(!empty($TEMP['#random_posts'])){
	foreach ($TEMP['#random_posts'] as $post) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
		$TEMP['!id'] = $post['id'];
		$TEMP['!type'] = $post['type'];

		$TEMP['!title'] = $post['title'];
		$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
		$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
		$TEMP['!url'] = Specific::Url($post['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
		$TEMP['!published_date'] = date('c', $post['published_at']);
		$TEMP['!published_at'] = Specific::DateString($post['published_at']);

		$TEMP['random_posts'] .= Specific::Maket('404/includes/random-posts');
		$home_ids[] = $post['id'];
	}
	Specific::DestroyMaket();
}

$TEMP['#page'] = '404';
$TEMP['#title'] = '404 - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword'] = $TEMP['#settings']['keyword'];

$TEMP['#content'] = Specific::Maket('404/content');
?>