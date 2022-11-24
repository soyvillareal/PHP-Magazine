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
if ($TEMP['#loggedin'] === true || $TEMP['#settings']['verify_email'] == 'off' || empty($tokenu)) {
	header("Location: " . Functions::Url());
	exit();
}


$verify_email = $dba->query('SELECT user_id, expires FROM '.T_TOKEN.' WHERE verify_email = ?', $tokenu)->fetchArray();

$page = Functions::ValidateToken($verify_email['expires'], 'verify_email') || empty($verify_email) || $dba->query('SELECT status FROM '.T_USER.' WHERE id = ?', $verify_email['user_id'])->fetchArray(true) == 'active' || (strlen($TEMP['#descode']) != 6 && !empty($TEMP['#descode'])) ? 'invalid-auth' : 'check-code';


$TEMP['title'] = $TEMP['#word']['check_your_email'];
$TEMP['type'] = 'verify_email';
$TEMP['token'] = $tokenu;
$TEMP['url'] = Functions::Url($ROUTE['#r_verify_email']);
if(!empty($_GET[$ROUTE['#p_insert']])){
	$TEMP['desone'] = substr($TEMP['#descode'], 0, 1);
	$TEMP['destwo'] = substr($TEMP['#descode'], 1, 1);
	$TEMP['desthree'] = substr($TEMP['#descode'], 2, 1);
	$TEMP['desfour'] = substr($TEMP['#descode'], 3, 1);
	$TEMP['desfive'] = substr($TEMP['#descode'], 4, 1);
	$TEMP['dessix'] = substr($TEMP['#descode'], 5, 1);
}

$TEMP['#page']        = 'verify-email';
$TEMP['#title']       = $TEMP['#word']['verify_your_account'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];
$TEMP['#content'] = Functions::Build("auth/{$page}/content");
?>