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
if ($TEMP['#loggedin'] === true || empty($tokenu)) {
	header("Location: " . Functions::Url());
	exit();
}


$reset_password = $dba->query('SELECT expires FROM '.T_TOKEN.' WHERE reset_password = ?', $tokenu)->fetchArray();

$page = Functions::ValidateToken($reset_password['expires'], 'reset_password') || empty($reset_password) ? 'invalid-auth' : 'reset-password';

$TEMP['tokenu'] = $tokenu;

$TEMP['#page']        = 'reset-password';
$TEMP['#title']       = $TEMP['#word']['change_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("auth/{$page}/content");
?>