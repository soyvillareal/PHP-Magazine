<?php
if($one == 'normal-search'){
	$keyword = Specific::Filter($_POST['keyword']);
	$date = Specific::Filter($_POST['date']);
	$category = Specific::Filter($_POST['category']);
	$author = Specific::Filter($_POST['author']);
	$sort = Specific::Filter($_POST['sort']);

	$search_load = Load::Search(array(
		'keyword' => $keyword,
		'date' => $date,
		'category' => $category,
		'author' => $author,
		'sort' => $sort
	));

	$url = Specific::Url("{$TEMP['#r_search']}");
	if(!empty($keyword)){
		$url .= "?{$TEMP['#p_keyword']}={$keyword}";
	}

	if(!empty($date)){
		$param = !empty($keyword)? '&' : '?';
		$url .= "{$param}{$TEMP['#p_date']}={$date}";
	}

	if(!empty($category)){
		$param = !empty($keyword) || !empty($date)  ? '&' : '?';
		$url .= "{$param}{$TEMP['#p_category']}={$category}";
	}

	if(!empty($author)){
		$param = !empty($keyword) || !empty($date) || !empty($category)  ? '&' : '?';
		$url .= "{$param}{$TEMP['#p_author']}={$author}";
	}

	if(!empty($sort)){
		$param = !empty($keyword) || !empty($date) || !empty($category) || !empty($author)  ? '&' : '?';
		$url .= "{$param}{$TEMP['#p_sort']}={$sort}";
	}

	$deliver['KW'] = $keyword;
	$deliver['DT'] = $date;
	$deliver['CT'] = $category;
	$deliver['AT'] = $author;
	$deliver['ST'] = $sort;
	$deliver['URL'] = $url;
	if($search_load['return']){
		$deliver['S'] = 200;
		$deliver['HT'] = $search_load['html'];
		$deliver['IO'] = $search_load['info'];
		$deliver['IDS'] = $search_load['search_ids'];
	} else {
		$deliver['S'] = 400;
		$deliver['HT'] = $search_load['html'];
	}
} else if($one == 'table-search'){
	$search_ids = Specific::Filter($_POST['search_ids']);
	$search_ids = html_entity_decode($search_ids);
	$search_ids = json_decode($search_ids);
	$keyword = Specific::Filter($_POST['keyword']);
	$date = Specific::Filter($_POST['date']);
	$category = Specific::Filter($_POST['category']);
	$author = Specific::Filter($_POST['author']);
	$sort = Specific::Filter($_POST['sort']);

	$search_load = Load::Search(array(
		'keyword' => $keyword,
		'date' => $date,
		'category' => $category,
		'author' => $author,
		'sort' => $sort
	), $search_ids);

	if($search_load['return']){
		$deliver = array(
			'S' => 200,
			'HT' => $search_load['html'],
			'IDS' => $search_load['search_ids']
		);
	}
}
?>