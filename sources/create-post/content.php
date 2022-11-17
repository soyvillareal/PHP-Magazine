<?php

if($TEMP['#loggedin'] == false) {
    header("Location: ".Functions::Url($ROUTE['#r_login']));
    exit();
}

if ($TEMP['#publisher'] == false) {
    header("Location: ".Functions::Url('404'));
    exit();
}

$TEMP['#type'] = 'text';

$TEMP['entry_text'] = Functions::Build("create-post/includes/entry");

$TEMP['#page']        = 'create-post';
$TEMP['#title']       = $TEMP['#word']['create_post'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("create-post/content");
?>