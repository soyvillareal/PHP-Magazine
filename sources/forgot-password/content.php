<?php
if ($TEMP['#loggedin'] === true) {
	header("Location: " . Functions::Url());
	exit();
}

$TEMP['#page']        = 'forgot-password';
$TEMP['#title']       = $TEMP['#word']['reset_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build('auth/forgot-password/content');
?>