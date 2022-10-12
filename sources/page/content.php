<?php
if (!in_array($_GET['slug'], array($RUTE['#r_terms_of_use'], $RUTE['#r_habeas_data'], $RUTE['#r_about_us']))){
	header("Location: " . Specific::Url('404'));
	exit();
}

$pages = array(
	$RUTE['#r_terms_of_use'] => 'terms_of_use',
	$RUTE['#r_habeas_data'] => 'habeas_data',
	$RUTE['#r_about_us'] => 'about_us'
);

$slug = Specific::Filter($_GET['slug']);
$type = $pages[$slug];

$page = $dba->query('SELECT * FROM '.T_PAGE.' WHERE slug = ?', $type)->fetchArray();

if($page['status'] == 'disabled'){
	header("Location: " . Specific::Url('404'));
	exit();
}

$TEMP['title'] = $TEMP['#word']["page_{$page['slug']}"];
$TEMP['#updated_at'] = $page['updated_at'];
if($TEMP['#updated_at'] != 0){
	$TEMP['updated_at'] = Specific::DateFormat($TEMP['#updated_at']);
}
$TEMP['created_at'] = Specific::DateFormat($page['created_at']);
$TEMP['html'] = htmlspecialchars_decode($page['text']);

$TEMP['#page']   	   = $type;
$TEMP['#title'] 	   = $TEMP['#word']["page_{$page['slug']}"] . ' - ' . $TEMP['title'];
$TEMP['#description']  = $page['description'];
$TEMP['#keyword']      = $page['keywords'];

$TEMP['#content'] = Specific::Maket("page/content");