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

$tokenu = Functions::Filter($_GET['tokenu']);
$TEMP['#descode'] = Functions::Filter($_GET[$ROUTE['#p_insert']]);
if ($TEMP['#loggedin'] === true || $TEMP['#settings']['2check'] == 'off' || empty($tokenu)) {
	header("Location: " . Functions::Url());
	exit();
}

$_2check = $dba->query('SELECT expires FROM '.T_TOKEN.' WHERE 2check = ?', $tokenu)->fetchArray();

$page = Functions::ValidateToken($_2check['expires'], '2check') || empty($_2check) || (strlen($TEMP['#descode']) != 6 && !empty($TEMP['#descode'])) ? 'invalid-auth' : 'check-code';

$TEMP['title'] = $TEMP['#word']['2check'];
$TEMP['type'] = '2check';
$TEMP['token'] = $tokenu;
$TEMP['url'] = Functions::Url($ROUTE['#r_2check']);
if(!empty($_GET[$ROUTE['#p_insert']])){
	$TEMP['desone'] = substr($TEMP['#descode'], 0, 1);
	$TEMP['destwo'] = substr($TEMP['#descode'], 1, 1);
	$TEMP['desthree'] = substr($TEMP['#descode'], 2, 1);
	$TEMP['desfour'] = substr($TEMP['#descode'], 3, 1);
	$TEMP['desfive'] = substr($TEMP['#descode'], 4, 1);
	$TEMP['dessix'] = substr($TEMP['#descode'], 5, 1);
}

$TEMP['#page']        = '2check';
$TEMP['#title']       = $TEMP['#word']['2check'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("auth/$page/content");
?>