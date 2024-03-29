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

$username = Functions::Filter($_GET['username']);

if(empty($username)){
	header("Location: " . Functions::Url('404'));
	exit();
}

$user = $dba->query('SELECT * FROM '.T_USER.' WHERE username = ? AND status = "active"', $username)->fetchArray();

if(empty($user)){
	header("Location: " . Functions::Url('404'));
	exit();
}

$TEMP['#profile'] = $user = Functions::Data($user, 3);

$query = '';

$profile_load = Loads::Profile($user['id']);
$TEMP['posts_result'] = $profile_load['html'];

if($profile_load['return']){
	$widget = Functions::GetWidget('horizposts');
	if($widget['return']){
		$TEMP['posts_result'] .= $widget['html'];
	}

	$widget = Functions::GetWidget('aside');
	if($widget['return']){
		$TEMP['content_aad'] = $widget['html'];
	}
	
	if(!empty($profile_load['profile_ids'])){
		$query = ' AND id NOT IN ('.implode(',', $profile_load['profile_ids']).')';
	}
}



$TEMP['#related_cat'] = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' ORDER BY RAND() DESC LIMIT 5')->fetchAll();

if(!empty($TEMP['#related_cat'])){
	foreach ($TEMP['#related_cat'] as $rlc) {
		$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $rlc['category_id'])->fetchArray();
		$TEMP['!type'] = $rlc['type'];

		$TEMP['!key'] += 1;
		$TEMP['!title'] = $rlc['title'];
		$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
		$TEMP['!category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");
		$TEMP['!url'] = Functions::Url($rlc['slug']);
		$TEMP['!thumbnail'] = Functions::GetFile($rlc['thumbnail'], 1, 's');
		$TEMP['!published_date'] = date('c', $rlc['published_at']);
		$TEMP['!published_at'] = Functions::DateString($rlc['published_at']);
		$TEMP['related_aside'] .= Functions::Build('includes/search-post-profile-category-tag/related-aside');
	}
	Functions::DestroyBuild();
}

$TEMP['user'] = $username;
$TEMP['username'] = $user['username'];
$TEMP['gender_txt'] = $user['gender_txt'];
$TEMP['avatar_b'] = $user['avatar_b'];
$TEMP['profile_ids'] = implode(',', $profile_load['profile_ids']);

$TEMP['#user_id'] = $user['id'];
$TEMP['#birthday_format'] = $user['birthday_format'];
$TEMP['#contact_email'] = $user['contact_email'];
$TEMP['#twitter'] = $user['twitter'];
$TEMP['#instagram'] = $user['instagram'];
$TEMP['#facebook'] = $user['facebook'];
$TEMP['#about'] = $user['about'];
$TEMP['#profile_blocked'] = in_array($user['id'], Functions::BlockedUsers(false));
$TEMP['#profile_ids'] = $profile_load['profile_ids'];

$followers = Functions::Followers($user['id']);

$TEMP['#followers'] = $followers['number'];
$TEMP['followers'] = $followers['text'];

$TEMP['#type'] = 'follow';
$TEMP['btn_ftext'] = $TEMP['#word']['follow'];

if($dba->query('SELECT COUNT(*) FROM '.T_FOLLOWER.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $user['id'])->fetchArray(true) > 0){
	$TEMP['#type'] = 'following';
	$TEMP['btn_ftext'] = $TEMP['#word']['following'];
}

$TEMP['form_newsletter'] = Functions::Build('includes/search-post-profile-category-tag/includes/form-newsletter');
$TEMP['newsletter'] = Functions::Build('includes/search-post-profile-category-tag/newsletter');



$TEMP['#page']        = 'user';
$TEMP['#title']       = $user['username'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("user/content");
?>