<?php
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
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("settings/change-password/content");
?>