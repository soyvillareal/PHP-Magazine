<?php 
$tokenu = Functions::Filter($_GET['tokenu']);
$TEMP['#descode'] = Functions::Filter($_GET[$RUTE['#p_insert']]);
if ($TEMP['#loggedin'] === true || $TEMP['#settings']['2check'] == 'off' || empty($tokenu)) {
	header("Location: " . Functions::Url());
	exit();
}

$_2check = $dba->query('SELECT expires, COUNT(*) as count FROM '.T_TOKEN.' WHERE 2check = ?', $tokenu)->fetchArray();

$page = Functions::ValidateToken($_2check['expires'], '2check') || $_2check['count'] == 0 || (strlen($TEMP['#descode']) != 6 && !empty($TEMP['#descode'])) ? 'invalid-auth' : 'check-code';

$TEMP['title'] = $TEMP['#word']['2check'];
$TEMP['type'] = '2check';
$TEMP['token'] = $tokenu;
$TEMP['url'] = Functions::Url($RUTE['#r_2check']);
if(!empty($_GET[$RUTE['#p_insert']])){
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
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("auth/$page/content");
?>