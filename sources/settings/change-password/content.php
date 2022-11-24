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

if ($TEMP['#loggedin'] === false) {
    header("Location: ".Functions::ReturnUrl());
    exit();
}
$TEMP['#user_id'] = $TEMP['#user']['id'];
if($TEMP['#moderator'] == true){
    $user_id = Functions::Filter($_GET[$ROUTE['#p_user_id']]);
    if(!empty($user_id)){
        $TEMP['#user_id'] = $user_id;
        $TEMP['#param'] = "?{$ROUTE['#p_user_id']}={$user_id}";
    }
}

$TEMP['#page']        = 'change-password';
$TEMP['#title']       = $TEMP['#word']['change_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("settings/change-password/content");
?>