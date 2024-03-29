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

$TEMP['#user'] = $dba->query('SELECT * FROM '.T_USER.' u WHERE (SELECT user_id FROM '.T_TOKEN.' WHERE unlink_email = ? AND user_id = u.id) = id', $tokenu)->fetchArray();

$page = empty($TEMP['#user']) || $TEMP['#user']['status'] == 'active' ? 'invalid-auth' : 'unlink-mail';

$TEMP['token'] = $tokenu;
$TEMP['verify_email'] = $dba->query('SELECT verify_email FROM '.T_TOKEN.' WHERE user_id = ?', $TEMP['#user']['id'])->fetchArray(true);
$TEMP['email'] = $TEMP['#user']['email'];
$TEMP['id'] = $TEMP['#user']['id'];

if($TEMP['#user']['status'] == 'pending'){
	$TEMP['title'] = $TEMP['#word']['didnt_create_this_account'];
} else {
	$TEMP['title'] = $TEMP['#word']['the_account_been_deactivated'];
}

$TEMP['#page']        = 'unlink-mail';
$TEMP['#title']       = $TEMP['#word']['didnt_create_this_account'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];
$TEMP['#content']     = Functions::Build("auth/$page/content");
?>