<?php

// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

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
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("create-post/content");
?>