<?php 
$tokenu = Specific::Filter($_GET['tokenu']);
$TEMP['#descode'] = Specific::Filter($_GET[$TEMP['#p_insert']]);
if ($TEMP['#loggedin'] === true || $TEMP['#settings']['2check'] == 'off' || empty($tokenu)) {
	header("Location: " . Specific::Url());
	exit();
}

$page = $dba->query('SELECT COUNT(*) FROM '.T_TOKEN.' WHERE 2check = ?', $tokenu)->fetchArray(true) == 0 || (strlen($TEMP['#descode']) != 6 && !empty($TEMP['#descode'])) ? 'invalid-auth' : 'check-code';

$TEMP['title'] = $TEMP['#word']['2check'];
$TEMP['type'] = '2check';
$TEMP['token'] = $tokenu;
$TEMP['url'] = Specific::Url($TEMP['#r_2check']);
if(!empty($_GET[$TEMP['#p_insert']])){
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

$TEMP['#content']     = Specific::Maket("auth/$page/content");
?>