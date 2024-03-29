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

$keyword = Functions::Filter($_GET[$ROUTE['#p_keyword']]);
$date = Functions::Filter($_GET[$ROUTE['#p_date']]);
$category = Functions::Filter($_GET[$ROUTE['#p_category']]);
$author = Functions::Filter($_GET[$ROUTE['#p_author']]);
$sort = Functions::Filter($_GET[$ROUTE['#p_sort']]);

$TEMP['#type_date'] = $ROUTE['#p_all'];
$TEMP['type_ndate'] = $TEMP['#word']['all_'];
if(in_array($date, array($ROUTE['#p_today'], $ROUTE['#p_this_week'], $ROUTE['#p_this_month'], $ROUTE['#p_this_year']))){
	$TEMP['#type_date'] = $date;
	if($date == $ROUTE['#p_today']){
		$TEMP['type_ndate'] = $TEMP['#word']['today'];
	} else if($date == $ROUTE['#p_this_week']){
		$TEMP['type_ndate'] = $TEMP['#word']['this_week'];
	} else if($date == $ROUTE['#p_this_month']){
		$TEMP['type_ndate'] = $TEMP['#word']['this_month'];
	} else if($date == $ROUTE['#p_this_year']){
		$TEMP['type_ndate'] = $TEMP['#word']['this_year'];
	}
} else {
	$date = $ROUTE['#p_all'];
}

$TEMP['#type_category'] = $ROUTE['#p_all'];
$TEMP['type_ncategory'] = $TEMP['#word']['all_'];
if(!empty($category) && is_numeric($category)){
	if($category != $ROUTE['#p_all']){
		$cat = $dba->query('SELECT name, id FROM '.T_CATEGORY.' WHERE id = ?', $category)->fetchArray();
		if(!empty($cat)){
			$TEMP['#type_category'] = $cat['id'];
			$TEMP['type_ncategory'] = $TEMP['#word']["category_{$cat['name']}"];
		}
	}
} else {
	$category = $ROUTE['#p_all'];
}

$TEMP['#type_author'] = $ROUTE['#p_all'];
$TEMP['type_nauthor'] = $TEMP['#word']['all_'];
if(!empty($author) && is_numeric($author)){
	if($author != $ROUTE['#p_all']){
		$user_exists = $dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND (role = "publisher" OR role = "moderator" OR role = "admin")', $author)->fetchArray(true);
		if($user_exists > 0){
			$user = Functions::Data($author, array('username', 'name', 'surname'));
			$TEMP['#type_author'] = $author;
			$TEMP['type_nauthor'] = $user['username'];
		}
	}
} else {
	$author = $ROUTE['#p_all'];
}

$TEMP['#type_sort'] = $ROUTE['#p_newest'];
$TEMP['type_nsort'] = $TEMP['#word']['newest'];
if(in_array($sort, array($ROUTE['#p_newest'], $ROUTE['#p_oldest'], $ROUTE['#p_views']))){
	$TEMP['#type_sort'] = $sort;
	if($sort == $ROUTE['#p_oldest']){
		$TEMP['type_nsort'] = $TEMP['#word']['oldest'];
	} else if($sort == $ROUTE['#p_views']){
		$TEMP['type_nsort'] = $TEMP['#word']['views'];
	}
} else {
	$sort = $ROUTE['#p_newest'];
}


$search_load = Loads::Search(array(
	'keyword' => $keyword,
	'date' => $date,
	'category' => $category,
	'author' => $author,
	'sort' => $sort
));
$TEMP['posts_result'] = $search_load['html'];
$TEMP['search_info'] = $search_load['info'];

if($search_load['return']){
	$widget = Functions::GetWidget('horizposts');
	if($widget['return']){
		$TEMP['posts_result'] .= $widget['html'];
	}
	$widget = Functions::GetWidget('aside');
	if($widget['return']){
		$TEMP['content_aad'] = $widget['html'];
	}
	$query = '';
	if(!empty($search_load['search_ids'])){
		$query = ' AND id NOT IN ('.implode(',', $search_load['search_ids']).')';
	}
	$TEMP['search_ids'] = implode(',', $search_load['search_ids']);
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

$TEMP['form_newsletter'] = Functions::Build('includes/search-post-profile-category-tag/includes/form-newsletter');
$TEMP['newsletter'] = Functions::Build('includes/search-post-profile-category-tag/newsletter');

$TEMP['#keyword_search'] = $keyword;

$TEMP['#show_sinfo'] = false;
if(empty($keyword) || $search_load['return'] == false){
	$TEMP['#show_sinfo'] = true;
}

$TEMP['#page']        = 'search';
$TEMP['#title']       = $TEMP['#word']['search'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("search/content");
?>