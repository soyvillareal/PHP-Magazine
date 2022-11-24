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

if($one == 'normal-search'){
	$keyword = Functions::Filter($_POST['keyword']);
	$date = Functions::Filter($_POST['date']);
	$category = Functions::Filter($_POST['category']);
	$author = Functions::Filter($_POST['author']);
	$sort = Functions::Filter($_POST['sort']);

	$search_load = Loads::Search(array(
		'keyword' => $keyword,
		'date' => $date,
		'category' => $category,
		'author' => $author,
		'sort' => $sort
	));

	$url = Functions::Url("{$ROUTE['#r_search']}");
	if(!empty($keyword)){
		$url .= "?{$ROUTE['#p_keyword']}={$keyword}";
	}

	if(!empty($date)){
		$param = !empty($keyword)? '&' : '?';
		$url .= "{$param}{$ROUTE['#p_date']}={$date}";
	}

	if(!empty($category)){
		$param = !empty($keyword) || !empty($date)  ? '&' : '?';
		$url .= "{$param}{$ROUTE['#p_category']}={$category}";
	}

	if(!empty($author)){
		$param = !empty($keyword) || !empty($date) || !empty($category)  ? '&' : '?';
		$url .= "{$param}{$ROUTE['#p_author']}={$author}";
	}

	if(!empty($sort)){
		$param = !empty($keyword) || !empty($date) || !empty($category) || !empty($author)  ? '&' : '?';
		$url .= "{$param}{$ROUTE['#p_sort']}={$sort}";
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
	$search_ids = Functions::Filter($_POST['search_ids']);
	$search_ids = html_entity_decode($search_ids);
	$search_ids = json_decode($search_ids);
	$keyword = Functions::Filter($_POST['keyword']);
	$date = Functions::Filter($_POST['date']);
	$category = Functions::Filter($_POST['category']);
	$author = Functions::Filter($_POST['author']);
	$sort = Functions::Filter($_POST['sort']);

	$search_load = Loads::Search(array(
		'keyword' => $keyword,
		'date' => $date,
		'category' => $category,
		'author' => $author,
		'sort' => $sort
	), $search_ids);

	if($search_load['return']){
		$widget = Functions::GetWidget('horizposts');
		if($widget['return']){
			$search_load['html'] .= $widget['html'];
		}
		$deliver = array(
			'S' => 200,
			'HT' => $search_load['html'],
			'IDS' => $search_load['search_ids']
		);
	}
}
?>