<?php

if ($TEMP['#loggedin'] == false) {
    header("Location: ".Specific::Url($TEMP['#r_login']));
    exit();
}


$TEMP['#type'] = 'text';

$TEMP['entry_text'] = Specific::Maket("create-post/includes/entry");



$recommended_bo = $dba->query('SELECT * FROM '.T_POST.' WHERE id = 6')->fetchArray();
$TEMP['title'] = $recommended_bo['title'];
$TEMP['url'] = Specific::Url($recommended_bo['slug']);
$TEMP['thumbnail'] = Specific::GetFile($recommended_bo['thumbnail'], 1, 's');

$TEMP['recommended_body'] = Specific::Maket("create-post/includes/recommended-body");

$TEMP['#page']        = 'create-post';
$TEMP['#title']       = $TEMP['#word']['create_post'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("create-post/content");
?>