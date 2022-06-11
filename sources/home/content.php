<?php

$TEMP['#main'] = $dba->query('SELECT * FROM '.T_POST.' ORDER BY published_at ASC LIMIT 15')->fetchAll();
$news_main = array();

if(!empty($TEMP['#main'])){
	foreach ($TEMP['#main'] as $key => $post) {
		$TEMP['!title'] = $post['title'];
		$TEMP['!url'] = Specific::Url($post['slug']);
		if($key < 6){
			$TEMP['!description'] = $post['description'];
			$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
			$TEMP['!published_at'] = Specific::DateString($post['published_at']);
			$TEMP['main_left_one'] .= Specific::Maket('home/includes/main-left-one');
		}
		if($key == 6){
			$TEMP['main_right_title'] = $post['title'];
			$TEMP['main_right_description'] = $post['description'];
			$TEMP['main_right_url'] = Specific::Url($post['slug']);
			$TEMP['main_right_thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 'b');
			$TEMP['main_right_published_at'] = Specific::DateString($post['published_at']);
		}
		if($key >= 7){
			$TEMP['main_two'] .= Specific::Maket('home/includes/main-two');
		}
		$news_main[] = $post['id'];
	}
	Specific::DestroyMaket();
}

$TEMP['#recommended'] = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $news_main).') ORDER BY published_at ASC LIMIT 5')->fetchAll();
$recommended_news = array();

if(!empty($TEMP['#recommended'])){
	foreach ($TEMP['#recommended'] as $post) {
		$TEMP['!title'] = $post['title'];
		$TEMP['!url'] = Specific::Url($post['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
		$TEMP['!published_at'] = Specific::DateString($post['published_at']);
		$TEMP['recommended_news'] .= Specific::Maket('home/includes/recommended-news');
		$recommended_news[] = $post['id'];
	}
	Specific::DestroyMaket();
}

$TEMP['#last_news'] = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $news_main).') AND id NOT IN ('.implode(',', $recommended_news).') ORDER BY published_at ASC LIMIT 20')->fetchAll();

if(!empty($TEMP['#last_news'])){
	foreach ($TEMP['#last_news'] as $post) {
		$TEMP['!title'] = $post['title'];
		$TEMP['!url'] = Specific::Url($post['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
		$TEMP['!published_at'] = Specific::DateString($post['published_at']);
		$TEMP['last_news'] .= Specific::Maket('home/includes/last-news');
	}
	Specific::DestroyMaket();
}

$show_alert = Specific::Filter($_GET['show-alert']);
$TEMP['#show_alert'] = false;
if($show_alert == 'deleted_post'){
	if(isset($_SESSION['post_deleted'])){
		unset($_SESSION['post_deleted']);
		$TEMP['#show_alert'] = true;
	}
}

$TEMP['#page']         = 'home';
$TEMP['#title']        = $TEMP['#settings']['title'].' - '.$TEMP['#word']['latest_news_colombia_world'];
$TEMP['#description']  = $TEMP['#settings']['description'];
$TEMP['#keyword']      = $TEMP['#settings']['keyword'];
$TEMP['#content']      = Specific::Maket('home/content');
?>