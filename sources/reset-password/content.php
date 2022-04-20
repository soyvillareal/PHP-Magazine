<?php
$tokenu = Specific::Filter($_GET['tokenu']);
if ($TEMP['#loggedin'] === true || empty($tokenu)) {
	header("Location: " . Specific::Url());
	exit();
}

$page = $dba->query('SELECT COUNT(*) FROM '.T_TOKEN.' WHERE reset_password = ?', $tokenu)->fetchArray(true) == 0 ? 'invalid-auth' : 'reset-password';

$TEMP['tokenu'] = $tokenu;

$TEMP['#page']        = 'reset-password';
$TEMP['#title']       = $TEMP['#word']['change_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("auth/$page/content");
?>