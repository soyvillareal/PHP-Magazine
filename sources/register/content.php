<?php

$TEMP['#page']        = 'register';
$TEMP['#title']       = $TEMP['#word']['register'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['social_buttons'] = Functions::Build('auth/includes/social-buttons');
$TEMP['#content']     = Functions::Build("auth/register/content");
?>