<?php
$tokenu = Specific::Filter($_GET['tokenu']);
if ($TEMP['#loggedin'] === true || empty($tokenu)) {
	header("Location: " . Specific::Url());
	exit();
}


$reset_password = $dba->query('SELECT expires, COUNT(*) as count FROM '.T_TOKEN.' WHERE reset_password = ?', $tokenu)->fetchArray();

$page = Specific::ValidateToken($reset_password['expires'], 'reset_password') || $reset_password['count'] == 0 ? 'invalid-auth' : 'reset-password';

$TEMP['tokenu'] = $tokenu;

$TEMP['#page']        = 'reset-password';
$TEMP['#title']       = $TEMP['#word']['change_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("auth/{$page}/content");
?>