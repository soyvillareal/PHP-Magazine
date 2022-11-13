<?php
if ($TEMP['#loggedin'] === false) {
    header("Location: ".Functions::ReturnUrl());
    exit();
}
$TEMP['#user_id'] = $TEMP['#user']['id'];
if($TEMP['#moderator'] == true){
    $user_id = Functions::Filter($_GET[$RUTE['#p_user_id']]);
    if(!empty($user_id)){
        $TEMP['#user_id'] = $user_id;
        $TEMP['#param'] = "?{$RUTE['#p_user_id']}={$user_id}";
    }
}

$user_sessions = $dba->query("SELECT * FROM ".T_SESSION." WHERE user_id = {$TEMP['#user_id']} ORDER BY id DESC LIMIT ? OFFSET ?", 10, 1)->fetchAll();
$TEMP['#total_pages'] = $dba->totalPages;

if (!empty($user_sessions)) {
    foreach ($user_sessions as $value) {
        $TEMP['!id'] = $value['id'];
        $session = Functions::GetSessions($value);
        $TEMP['!ip'] = $session['ip'];
        $TEMP['!browser'] = $session['browser'];
        $TEMP['!platform'] = $session['platform'];
        $TEMP['!created_at'] = Functions::DateFormat($value['created_at']);

        $TEMP['sessions'] .= Functions::Build("settings/logins/includes/sessions");
    }
    Functions::DestroyBuild();
} else {
    $TEMP['sessions'] = Functions::Build("not-found/no-result");
}

$TEMP['#page']        = 'logins';
$TEMP['#title']       = $TEMP['#word']['logins'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("settings/logins/content");
?>