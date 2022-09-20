<?php

$keyword = Specific::Filter($_GET[$TEMP['#p_keyword']]);
$date = Specific::Filter($_GET[$TEMP['#p_date']]);
$category = Specific::Filter($_GET[$TEMP['#p_category']]);
$author = Specific::Filter($_GET[$TEMP['#p_author']]);
$sort = Specific::Filter($_GET[$TEMP['#p_sort']]);

$TEMP['#type_date'] = $TEMP['#p_all'];
$TEMP['type_ndate'] = $TEMP['#word']['all_'];
if(in_array($date, array($TEMP['#p_today'], $TEMP['#p_this_week'], $TEMP['#p_this_month'], $TEMP['#p_this_year']))){
	$TEMP['#type_date'] = $date;
	if($date == $TEMP['#p_today']){
		$TEMP['type_ndate'] = $TEMP['#word']['today'];
	} else if($date == $TEMP['#p_this_week']){
		$TEMP['type_ndate'] = $TEMP['#word']['this_week'];
	} else if($date == $TEMP['#p_this_month']){
		$TEMP['type_ndate'] = $TEMP['#word']['this_month'];
	} else if($date == $TEMP['#p_this_year']){
		$TEMP['type_ndate'] = $TEMP['#word']['this_year'];
	}
} else {
	$date = $TEMP['#p_all'];
}

$TEMP['#type_category'] = $TEMP['#p_all'];
$TEMP['type_ncategory'] = $TEMP['#word']['all_'];
if(!empty($category) && is_numeric($category)){
	if($category != $TEMP['#p_all']){
		$cat = $dba->query('SELECT name, id, COUNT(*) as count FROM '.T_CATEGORY.' WHERE id = ?', $category)->fetchArray();
		if($cat['count'] > 0){
			$TEMP['#type_category'] = $cat['id'];
			$TEMP['type_ncategory'] = $cat['name'];
		}
	}
} else {
	$category = $TEMP['#p_all'];
}

$TEMP['#type_author'] = $TEMP['#p_all'];
$TEMP['type_nauthor'] = $TEMP['#word']['all_'];
if(!empty($author) && is_numeric($author)){
	if($author != $TEMP['#p_all']){
		$user_exists = $dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND (role = "publisher" OR role = "moderator" OR role = "admin")', $author)->fetchArray(true);
		if($user_exists > 0){
			$user = Specific::Data($author, array('username', 'name', 'surname'));
			$TEMP['#type_author'] = $author;
			$TEMP['type_nauthor'] = $user['username'];
		}
	}
} else {
	$author = $TEMP['#p_all'];
}

$TEMP['#type_sort'] = $TEMP['#p_newest'];
$TEMP['type_nsort'] = $TEMP['#word']['newest'];
if(in_array($sort, array($TEMP['#p_newest'], $TEMP['#p_oldest'], $TEMP['#p_views']))){
	$TEMP['#type_sort'] = $sort;
	if($sort == $TEMP['#p_oldest']){
		$TEMP['type_nsort'] = $TEMP['#word']['oldest'];
	} else if($sort == $TEMP['#p_views']){
		$TEMP['type_nsort'] = $TEMP['#word']['views'];
	}
} else {
	$sort = $TEMP['#p_newest'];
}


$search_load = Load::Search(array(
	'keyword' => $keyword,
	'date' => $date,
	'category' => $category,
	'author' => $author,
	'sort' => $sort
));

$TEMP['posts_result'] = $search_load['html'];
$TEMP['search_info'] = $search_load['info'];

$query = '';
if(!empty($search_load['search_ids'])){
	$query = ' AND id NOT IN ('.implode(',', $search_load['search_ids']).')';
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

$TEMP['form_newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/includes/form-newsletter');
$TEMP['newsletter'] = Specific::Maket('includes/search-post-profile-category-tag/newsletter');

$TEMP['#keyword_search'] = $keyword;

$TEMP['#show_sinfo'] = false;
if(empty($keyword) || $search_load['return'] == false){
	$TEMP['#show_sinfo'] = true;
}

$TEMP['search_ids'] = implode(',', $search_load['search_ids']);

$TEMP['#page']        = 'search';
$TEMP['#title']       = $TEMP['#word']['search'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("search/content");
?>