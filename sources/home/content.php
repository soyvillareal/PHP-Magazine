<?php
$home_ids = array();

$TEMP['#breaking_news'] = $dba->query('SELECT * FROM '.T_POST.' p WHERE (SELECT post_id FROM '.T_BREAKING.' WHERE post_id = p.id AND ? < expiration_at) = id', time())->fetchArray();

$TEMP['#show_breaking'] = false;
if(!empty($TEMP['#breaking_news']) && $TEMP['#breaking_news']['id'] != $_COOKIE['breaking_news']){
	if(!empty($_COOKIE['breaking_news'])){
		unset($_COOKIE['breaking_news']);
	}
	$TEMP['#show_breaking'] = true;
	$TEMP['breaking_id'] = $TEMP['#breaking_news']['id'];
	$TEMP['breaking_title'] = $TEMP['#breaking_news']['title'];
	$TEMP['breaking_url'] = Specific::Url($TEMP['#breaking_news']['slug']);
	$home_ids[] = $TEMP['#breaking_news']['id'];
}

$TEMP['#main'] = Specific::MainNews();

if(!empty($TEMP['#main'])){
	foreach ($TEMP['#main'] as $key => $post) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
		$TEMP['!id'] = $post['id'];
		$TEMP['!type'] = $post['type'];

		$TEMP['!title'] = $post['title'];
		$TEMP['!category'] = $category['name'];
		$TEMP['!category_slug'] = $category['slug'];
		$TEMP['!published_date'] = date('c', $post['published_at']);
		$TEMP['!url'] = Specific::Url($post['slug']);
		if($key < 6){
			$TEMP['!description'] = $post['description'];
			$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
			$TEMP['!published_at'] = Specific::DateString($post['published_at']);
			$TEMP['main_left_one'] .= Specific::Maket('home/includes/main-left-one');
		}
		if($key == 6){
			$TEMP['#main_right_type'] = $post['type'];

			$TEMP['main_right_id'] = $post['id'];
			$TEMP['main_right_title'] = $post['title'];
			$TEMP['main_right_category'] = $category['name'];
			$TEMP['main_right_category_slug'] = $category['slug'];
			$TEMP['main_right_description'] = $post['description'];
			$TEMP['main_right_url'] = Specific::Url($post['slug']);
			$TEMP['main_right_thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 'b');
			$TEMP['main_right_published_date'] = date('c', $post['published_at']);
			$TEMP['main_right_published_at'] = Specific::DateString($post['published_at']);
		}
		if($key >= 7){
			$TEMP['main_two'] .= Specific::Maket('home/includes/main-two');
		}
		$home_ids[] = $post['id'];
	}
	Specific::DestroyMaket();
}

$TEMP['#recommended'] = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') ORDER BY published_at ASC LIMIT 5')->fetchAll();

if(!empty($TEMP['#recommended'])){
	foreach ($TEMP['#recommended'] as $post) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
		$TEMP['!id'] = $post['id'];
		$TEMP['!type'] = $post['type'];

		$TEMP['!title'] = $post['title'];
		$TEMP['!category'] = $category['name'];
		$TEMP['!category_slug'] = $category['slug'];
		$TEMP['!title'] = $post['title'];
		$TEMP['!url'] = Specific::Url($post['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
		$TEMP['!published_date'] = date('c', $post['published_at']);
		$TEMP['!published_at'] = Specific::DateString($post['published_at']);
		$TEMP['recommended_news'] .= Specific::Maket('home/includes/recommended-news');
		$home_ids[] = $post['id'];
	}
	Specific::DestroyMaket();
}

$entry = $dba->query('SELECT frame, post_id FROM '.T_POST.' INNER JOIN '.T_ENTRY.' ON '.T_POST.'.id = '.T_ENTRY.'.post_id AND '.T_ENTRY.'.type = "video" AND '.T_POST.'.id NOT IN ('.implode(',', $home_ids).') ORDER BY '.T_POST.'.views ASC, '.T_ENTRY.'.eorder DESC')->fetchArray();
$TEMP['#video'] = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ?', $entry['post_id'])->fetchArray();

$category = $dba->query('SELECT id, name, slug FROM '.T_CATEGORY.' WHERE id = ?', $TEMP['#video']['category_id'])->fetchArray();

$TEMP['id'] = $post['id'];
$TEMP['title'] = $TEMP['#video']['title'];
$TEMP['description'] = $TEMP['#video']['description'];
$TEMP['category'] = $category['name'];
$TEMP['category_slug'] = $category['slug'];
$TEMP['thumbnail'] = Specific::GetFile($TEMP['#video']['thumbnail'], 1, 'b');
$TEMP['published_date'] = date('c', $TEMP['#video']['published_at']);
$TEMP['published_at'] = Specific::DateString($TEMP['#video']['published_at']);
$TEMP['url'] = Specific::Url($TEMP['#video']['slug']);
$home_ids[] = $TEMP['#video']['id'];

$frame = Specific::IdentifyFrame($entry['frame'], true);
$TEMP['frame'] = $frame['html'];

$main_recommended_videos = Load::RecommendedVideos($home_ids);

$TEMP['#main_recommended_videos'] = $main_recommended_videos['main_recommended_videos'];
$TEMP['main_recommended_videos'] = $main_recommended_videos['main_recommended_videos_html'];


$last_news = Load::LastNews($home_ids);

$TEMP['#last_news_one'] = $last_news['last_news_one'];
$TEMP['#last_news_two'] = $last_news['last_news_two'];
$TEMP['last_news_one'] = $last_news['last_news_one_html'];
$TEMP['last_news_two'] = $last_news['last_news_two_html'];
$TEMP['more_news'] = Specific::Maket('home/includes/more-news');

$TEMP['home_ids'] = implode(',', $last_news['home_ids']);

$show_alert = Specific::Filter($_GET[$TEMP['#show_alert']]);
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