<?php
if ($TEMP['#loggedin'] === true) {
	header("Location: " . Functions::Url());
	exit();
}

$TEMP['#page']          = 'login';
$TEMP['#title']         = $TEMP['#word']['login'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description']   = $TEMP['#settings']['description'];
$TEMP['#keyword']       = $TEMP['#settings']['keyword'];

$TEMP['social_buttons'] = Functions::Build('auth/includes/social-buttons');
$TEMP['#content']       = Functions::Build('auth/login/content');
?>