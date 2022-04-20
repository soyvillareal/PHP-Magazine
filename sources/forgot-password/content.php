<?php
if ($TEMP['#loggedin'] === true) {
	header("Location: " . Specific::Url());
	exit();
}

$TEMP['#page']        = 'forgot-password';
$TEMP['#title']       = $TEMP['#word']['reset_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket('auth/forgot-password/content');
?>