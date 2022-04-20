<?php
if (empty($_GET['two']) || !isset($_GET['two'])) {
	header("Location: " . Specific::Url());
	exit();
}
if (!in_array($_GET['two'], array('terms-of-use', 
								  'privacy-policy', 
								  'cookies-policy', 
								  'about-us'))) {
	header("Location: " . Specific::Url('404'));
	exit();
}

$type = Specific::Filter($_GET['two']);
$TEMP['#pages'] = Specific::GetPages();

if($TEMP['#pages']['active'][$type] == 0){
	header("Location: " . Specific::Url('404'));
	exit();
}

$TEMP['title'] = $TEMP['#word'][str_replace('-', '_', $type)];

$content = 'content';
if($type == 'banned'){
	$content = 'banned';
}

$TEMP['#page']   	   = $type;
$TEMP['#title'] 	   = $TEMP['#settings']['title'] . ' - ' . $TEMP['title'];
$TEMP['#description']  = $TEMP['#settings']['description'];
$TEMP['#keyword']      = $TEMP['#settings']['keyword'];

$TEMP['html'] = $TEMP['#pages']['page'][$type]['html'];
$TEMP['hexco'] = $TEMP['#pages']['page'][$type]['hexco'];
$TEMP['#content'] = Specific::Maket("pages/content");