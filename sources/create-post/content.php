<?php

if ($TEMP['#loggedin'] == false) {
    header("Location: ".Specific::Url($TEMP['#r_login']));
    exit();
}

$TEMP['#type'] = 'text';

$TEMP['entry_text'] = Specific::Maket("create-post/includes/entry");

$TEMP['#page']        = 'create-post';
$TEMP['#title']       = $TEMP['#word']['create_post'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("create-post/content");
?>