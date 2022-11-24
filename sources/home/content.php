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

$home_ids = array();

$TEMP['#breaking_news'] = $dba->query('SELECT * FROM '.T_POST.' p WHERE (SELECT post_id FROM '.T_BREAKING.' WHERE post_id = p.id AND ? < expiration_at) = id AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', time())->fetchArray();

$TEMP['#show_breaking'] = false;
if(!empty($TEMP['#breaking_news']) && $TEMP['#breaking_news']['id'] != $_COOKIE['breaking_news']){
	if(!empty($_COOKIE['breaking_news'])){
		unset($_COOKIE['breaking_news']);
	}
	$TEMP['#show_breaking'] = true;
	$TEMP['breaking_id'] = $TEMP['#breaking_news']['id'];
	$TEMP['breaking_title'] = $TEMP['#breaking_news']['title'];
	$TEMP['breaking_url'] = Functions::Url($TEMP['#breaking_news']['slug']);
	$home_ids[] = $TEMP['#breaking_news']['id'];
}

$TEMP['#main'] = Functions::MainPosts();

$TEMP['#limit_main'] = 15;
$TEMP['#has_main_left'] = false;
$TEMP['#has_main_right'] = false;
$TEMP['#has_main_two'] = false;

if(count($TEMP['#main']) > 0){
	$widget = Functions::GetWidget('htop');
	if($widget['return']){
		$TEMP['advertisement_htad'] = $widget['html'];
	}
}

if(count($TEMP['#main']) >= $TEMP['#limit_main']){
	foreach ($TEMP['#main'] as $key => $post) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
		$TEMP['!id'] = $post['id'];
		$TEMP['!type'] = $post['type'];

		$TEMP['!title'] = $post['title'];
		$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
		$TEMP['!category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");
		$TEMP['!published_date'] = date('c', $post['published_at']);
		$TEMP['!url'] = Functions::Url($post['slug']);
		if($key < 6){
			$TEMP['#has_main_left'] = true;
			$TEMP['!description'] = $post['description'];
			$TEMP['!thumbnail'] = Functions::GetFile($post['thumbnail'], 1, 's');
			$TEMP['!published_at'] = Functions::DateString($post['published_at']);
			$TEMP['main_left_one'] .= Functions::Build('home/includes/main-left-one');
		}
		if($key == 6){
			$TEMP['#has_main_right'] = true;
			$TEMP['#main_right_type'] = $post['type'];

			$TEMP['main_right_id'] = $post['id'];
			$TEMP['main_right_title'] = $post['title'];
			$TEMP['main_right_category'] = $TEMP['#word']["category_{$category['name']}"];
			$TEMP['main_right_category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");
			$TEMP['main_right_description'] = $post['description'];
			$TEMP['main_right_url'] = Functions::Url($post['slug']);
			$TEMP['main_right_thumbnail'] = Functions::GetFile($post['thumbnail'], 1, 'b');
			$TEMP['main_right_published_date'] = date('c', $post['published_at']);
			$TEMP['main_right_published_at'] = Functions::DateString($post['published_at']);
		}
		if($key >= 7){
			$TEMP['#has_main_two'] = true;
			$TEMP['main_two'] .= Functions::Build('home/includes/main-two');
		}
		$home_ids[] = $post['id'];
	}
	Functions::DestroyBuild();

	$TEMP['#recommended'] = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY published_at ASC LIMIT 5')->fetchAll();

	if(!empty($TEMP['#recommended'])){
		foreach ($TEMP['#recommended'] as $post) {
			$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
			$TEMP['!id'] = $post['id'];
			$TEMP['!type'] = $post['type'];

			$TEMP['!title'] = $post['title'];
			$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
			$TEMP['!category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");
			$TEMP['!title'] = $post['title'];
			$TEMP['!url'] = Functions::Url($post['slug']);
			$TEMP['!thumbnail'] = Functions::GetFile($post['thumbnail'], 1, 's');
			$TEMP['!published_date'] = date('c', $post['published_at']);
			$TEMP['!published_at'] = Functions::DateString($post['published_at']);
			$TEMP['recommended_posts'] .= Functions::Build('home/includes/recommended-posts');
			$home_ids[] = $post['id'];
		}
		Functions::DestroyBuild();
	}

	$entry = $dba->query('SELECT c.frame, c.post_id FROM '.T_POST.' a INNER JOIN '.T_ENTRY.' c ON a.id = c.post_id WHERE c.type = "video" AND a.id NOT IN ('.implode(',', $home_ids).') AND a.user_id NOT IN ('.$TEMP['#blocked_users'].') AND a.status = "approved" ORDER BY a.views ASC, c.eorder DESC')->fetchArray();

	if(!empty($entry)){
		$TEMP['#video'] = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ?', $entry['post_id'])->fetchArray();

		$category = $dba->query('SELECT id, name, slug FROM '.T_CATEGORY.' WHERE id = ?', $TEMP['#video']['category_id'])->fetchArray();
		$TEMP['category'] = $TEMP['#word']["category_{$category['name']}"];
		$TEMP['category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");


		$TEMP['title'] = $TEMP['#video']['title'];
		$TEMP['description'] = $TEMP['#video']['description'];
		$TEMP['thumbnail'] = Functions::GetFile($TEMP['#video']['thumbnail'], 1, 'b');
		$TEMP['published_date'] = date('c', $TEMP['#video']['published_at']);
		$TEMP['published_at'] = Functions::DateString($TEMP['#video']['published_at']);
		$TEMP['url'] = Functions::Url($TEMP['#video']['slug']);

		$home_ids[] = $TEMP['#video']['id'];
		$main_recommended_videos = Loads::RecommendedVideos($home_ids);

		$TEMP['#main_recommended_videos'] = $main_recommended_videos['main_recommended_videos'];
		$TEMP['main_recommended_videos'] = $main_recommended_videos['main_recommended_videos_html'];
		$home_ids = $main_recommended_videos['home_ids'];

		$frame = Functions::IdentifyFrame($entry['frame'], true);
		$TEMP['frame'] = $frame['html'];
	}


	$TEMP['id'] = $post['id'];

	$last_posts = Loads::LastPosts(8, $home_ids);

	$TEMP['#last_posts_one'] = $last_posts['last_posts_one'];
	$TEMP['#last_posts_two'] = $last_posts['last_posts_two'];
	$TEMP['last_posts_one'] = $last_posts['last_posts_one_html'];
	$TEMP['last_posts_two'] = $last_posts['last_posts_two_html'];
	$TEMP['more_posts'] = Functions::Build('home/includes/more-posts');

	$TEMP['home_ids'] = implode(',', $last_posts['home_ids']);
} else {
	$last_posts = Loads::LastPosts($TEMP['#limit_main']);

	if(!empty($TEMP['#main'])){
		$TEMP['last_posts'] = $last_posts['last_posts_one_html'];
	}
}

$show_alert = Functions::Filter($_GET[$ROUTE['#p_show_alert']]);
$TEMP['#show_alert'] = false;
if($TEMP['#loggedin'] == true){
	if($show_alert == $ROUTE['#p_deleted_post']){
		$TEMP['#show_alert'] = true;
	}
}

$TEMP['#page']         = 'home';
$TEMP['#title']        = $TEMP['#settings']['title'].' - '.$TEMP['#word']['the_best_digital_magazine'];
$TEMP['#description']  = $TEMP['#settings']['description'];
$TEMP['#keywords']      = $TEMP['#settings']['keywords'];
$TEMP['#content']      = Functions::Build('home/content');
?>