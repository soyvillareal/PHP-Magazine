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

if ($TEMP['#loggedin'] === true) {
	header("Location: " . Functions::Url());
	exit();
}

$TEMP['#page']        = 'forgot-password';
$TEMP['#title']       = $TEMP['#word']['reset_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build('auth/forgot-password/content');
?>