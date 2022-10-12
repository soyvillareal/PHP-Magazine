<?php
if ($TEMP['#loggedin'] == false) {
    header("Location: ".Specific::ReturnUrl());
    exit();
}
$TEMP['#user_id'] = $TEMP['#user']['id'];
if($TEMP['#moderator'] == true){
    $user_id = Specific::Filter($_GET[$RUTE['#p_user_id']]);
    if(!empty($user_id)){
        $TEMP['#user_id'] = $user_id;
        $TEMP['#param'] = "?{$RUTE['#p_user_id']}={$user_id}";
    }
}

$blocked_users = $dba->query("SELECT * FROM ".T_BLOCK." b WHERE user_id = {$TEMP['#user_id']} AND (SELECT status FROM ".T_USER." WHERE id = b.profile_id) = 'active' ORDER BY id DESC LIMIT ? OFFSET ?", 10, 1)->fetchAll();
$TEMP['#total_pages'] = $dba->totalPages;

if (!empty($blocked_users)) {
    foreach ($blocked_users as $blocked) {
        $user = Specific::Data($blocked['profile_id'], array(
            'username',
            'name',
            'surname'
        ));

        $TEMP['!id'] = $blocked['profile_id'];
        $TEMP['!name'] = $user['username'];
        $TEMP['!created_at'] = Specific::DateFormat($blocked['created_at']);

        $TEMP['blocked_users'] .= Specific::Maket("settings/blocked-users/includes/users");
    }
    Specific::DestroyMaket();
} else {
    $TEMP['blocked_users'] = Specific::Maket("not-found/no-result");
}

$TEMP['#page']        = 'blocked-users';
$TEMP['#title']       = $TEMP['#word']['blocked_users'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("settings/blocked-users/content");
?>