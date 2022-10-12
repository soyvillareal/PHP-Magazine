<?php
$tokenu = Specific::Filter($_GET['tokenu']);
$TEMP['#descode'] = Specific::Filter($_GET[$RUTE['#p_insert']]);
if ($TEMP['#loggedin'] == false){
    header("Location: " . Specific::Url('login'));
    exit();
} else if ($TEMP['#settings']['verify_email'] == 'off' || empty($tokenu)){
    header("Location: " . Specific::Url('404'));
    exit();
}

$change_email = $dba->query('SELECT expires, COUNT(*) as count FROM '.T_TOKEN.' WHERE change_email = ?', $tokenu)->fetchArray();

$page = Specific::ValidateToken($change_email['expires'], 'change_email') || $change_email['count'] == 0 || empty($TEMP['#user']['new_email']) || (strlen($TEMP['#descode']) != 6 && !empty($TEMP['#descode'])) ? 'invalid-auth' : 'check-code';

$TEMP['title'] = $TEMP['#word']['check_your_email'];
$TEMP['type'] = 'change_email';
$TEMP['token'] = $tokenu;
$TEMP['url'] = Specific::Url($RUTE['#r_change_email']);
if(!empty($_GET[$RUTE['#p_insert']])){
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

$TEMP['#content']     = Specific::Maket("auth/{$page}/content");
?>