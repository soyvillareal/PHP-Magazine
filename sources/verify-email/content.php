<?php
$tokenu = Specific::Filter($_GET['tokenu']);
$TEMP['#descode'] = Specific::Filter($_GET[$TEMP['#p_insert']]);
if ($TEMP['#loggedin'] === true || $TEMP['#settings']['verify_email'] == 'off' || empty($tokenu)) {
	header("Location: " . Specific::Url());
	exit();
}

$user = $dba->query('SELECT id, status FROM '.T_USER.' u WHERE (SELECT user_id FROM '.T_TOKEN.' WHERE verify_email = ? AND user_id = u.id) = id', $tokenu)->fetchArray();

$page = empty($user) || $user['status'] == 'active' || (strlen($TEMP['#descode']) != 6 && !empty($TEMP['#descode'])) ? 'invalid-auth' : 'check-code';


$TEMP['title'] = $TEMP['#word']['check_your_email'];
$TEMP['type'] = 'verify_email';
$TEMP['token'] = $tokenu;
$TEMP['url'] = Specific::Url($TEMP['#r_verify_email']);
if(!empty($_GET[$TEMP['#p_insert']])){
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
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];
$TEMP['#content'] = Specific::Maket("auth/$page/content");
?>