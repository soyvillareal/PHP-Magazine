<?php

$keyword = Specific::Filter($_GET[$TEMP['#p_keyword']]);
$date = Specific::Filter($_GET[$TEMP['#p_date']]);
$category = Specific::Filter($_GET[$TEMP['#p_category']]);
$author = Specific::Filter($_GET[$TEMP['#p_author']]);
$sort = Specific::Filter($_GET[$TEMP['#p_sort']]);

$TEMP['#type_date'] = 'all';
$TEMP['type_ndate'] = $TEMP['#word']['all_'];
if(in_array($date, array('today', 'this_week', 'this_month', 'this_year'))){
	$TEMP['#type_date'] = $date;
	$TEMP['type_ndate'] = $TEMP['#word'][$date];
} else {
	$date = 'all';
}

$TEMP['#type_category'] = 'all';
$TEMP['type_ncategory'] = $TEMP['#word']['all'];
if(!empty($category) && is_numeric($category)){
	if($category != 'all'){
		$category = $dba->query('SELECT name, id, COUNT(*) as count FROM '.T_CATEGORY.' WHERE id = ?', $category)->fetchArray();
		if($category['count'] > 0){
			$TEMP['#type_category'] = $category['id'];
			$TEMP['type_ncategory'] = $category['name'];
		}
	}
} else {
	$category = 'all';
}

$TEMP['#type_author'] = 'all';
$TEMP['type_nauthor'] = $TEMP['#word']['all_'];
if(!empty($author) && is_numeric($author)){
	if($author != 'all'){
		$user_exists = $dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND (role = "publisher" OR role = "moderator" OR role = "admin")', $author)->fetchArray(true);
		if($user_exists > 0){
			$user = Specific::Data($author, array('username', 'name', 'surname'));
			$TEMP['#type_author'] = $author;
			$TEMP['type_nauthor'] = $user['fullname'];
		}
	}
} else {
	$author = 'all';
}

$TEMP['#type_sort'] = 'newest';
$TEMP['type_nsort'] = $TEMP['#word']['newest'];
if(in_array($sort, array('newest', 'oldest', 'views'))){
	$TEMP['#type_sort'] = $sort;
	$TEMP['type_nsort'] = $TEMP['#word'][$sort];
} else {
	$sort = 'newest';
}


$search_load = Load::Search(array(
	'keyword' => $keyword,
	'date' => $date,
	'category' => $category,
	'author' => $author,
	'sort' => $sort
));

$TEMP['news_result'] = $search_load['html'];
$TEMP['search_info'] = $search_load['info'];

$query = '';
if(!empty($search_load['search_ids'])){
	$query = ' AND id NOT IN ('.implode(',', $search_load['search_ids']).')';
}

$related_cat = $dba->query('SELECT * FROM '.T_POST.' WHERE status = "approved" ORDER BY RAND() DESC LIMIT 5')->fetchAll();

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