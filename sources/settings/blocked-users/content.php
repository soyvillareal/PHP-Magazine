<?php
if ($TEMP['#loggedin'] == false) {
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

$blocked_users = $dba->query("SELECT * FROM ".T_BLOCK." b WHERE user_id = {$TEMP['#user_id']} AND (SELECT status FROM ".T_USER." WHERE id = b.profile_id) = 'active' ORDER BY id DESC LIMIT ? OFFSET ?", 10, 1)->fetchAll();
$TEMP['#total_pages'] = $dba->totalPages;

if (!empty($blocked_users)) {
    foreach ($blocked_users as $blocked) {
        $user = Functions::Data($blocked['profile_id'], array(
            'username',
            'name',
            'surname'
        ));

        $TEMP['!id'] = $blocked['profile_id'];
        $TEMP['!name'] = $user['username'];
        $TEMP['!created_at'] = Functions::DateFormat($blocked['created_at']);

        $TEMP['blocked_users'] .= Functions::Build("settings/blocked-users/includes/users");
    }
    Functions::DestroyBuild();
} else {
    $TEMP['blocked_users'] = Functions::Build("not-found/no-result");
}

$TEMP['#page']        = 'blocked-users';
$TEMP['#title']       = $TEMP['#word']['blocked_users'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("settings/blocked-users/content");
?>