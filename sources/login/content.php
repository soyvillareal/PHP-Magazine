<?php
if ($TEMP['#loggedin'] === true) {
	header("Location: " . Specific::Url());
	exit();
}

$TEMP['#page']          = 'login';
$TEMP['#title']         = $TEMP['#word']['login'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description']   = $TEMP['#settings']['description'];
$TEMP['#keyword']       = $TEMP['#settings']['keyword'];

$TEMP['social_buttons'] = Specific::Maket('auth/includes/social-buttons');
$TEMP['#content']       = Specific::Maket('auth/login/content');
?>