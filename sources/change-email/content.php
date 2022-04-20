<?php
$tokenu = Specific::Filter($_GET['tokenu']);
$TEMP['#descode'] = Specific::Filter($_GET[$TEMP['#p_insert']]);
if ($TEMP['#loggedin'] == false){
    header("Location: " . Specific::Url('login'));
    exit();
} else if ($TEMP['#settings']['verify_email'] != 'on' || empty($TEMP['#user']['new_email'])){
    header("Location: " . Specific::Url('404'));
    exit();
}

$page = empty($TEMP['#user']['new_email']) && (strlen($TEMP['#descode']) != 6 && !empty($TEMP['#descode'])) ? 'invalid-auth' : 'check-code';

$TEMP['title'] = $TEMP['#word']['check_your_email'];
$TEMP['type'] = 'change_email';
$TEMP['token'] = $tokenu;
if(!empty($_GET[$TEMP['#p_insert']])){
	$TEMP['desone'] = substr($TEMP['#descode'], 0, 1);
	$TEMP['destwo'] = substr($TEMP['#descode'], 1, 1);
	$TEMP['desthree'] = substr($TEMP['#descode'], 2, 1);
	$TEMP['desfour'] = substr($TEMP['#descode'], 3, 1);
	$TEMP['desfive'] = substr($TEMP['#descode'], 4, 1);
	$TEMP['dessix'] = substr($TEMP['#descode'], 5, 1);
}

$TEMP['#page']        = 'change-email';
$TEMP['#title']       = $TEMP['#word']['check_your_email'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("auth/$page/content");
?>