<?php
if ($TEMP['#loggedin'] === false) {
    header("Location: ".Specific::ReturnUrl());
    exit();
}

$user_sessions = $dba->query('SELECT * FROM session WHERE user_id = '.$TEMP['#user']['id'].' ORDER BY id DESC LIMIT ? OFFSET ?', 10, 1)->fetchAll();
$TEMP['#total_pages'] = $dba->totalPages;

if (!empty($user_sessions)) {
    foreach ($user_sessions as $value) {
        $TEMP['!id'] = $value['id'];
        $session = Specific::GetSessions($value);
        $TEMP['!ip'] = $session['ip'];
        $TEMP['!browser'] = $session['browser'];
        $TEMP['!platform'] = $session['platform'];
        $TEMP['!created_at'] = Specific::DateFormat($value['created_at']);

        $TEMP['sessions'] .= Specific::Maket("settings/logins/includes/sessions");
    }
    Specific::DestroyMaket();
} else {
    $TEMP['sessions'] = Specific::Maket("not-found/sessions");
}

$TEMP['#page']        = 'logins';
$TEMP['#title']       = $TEMP['#word']['logins'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("settings/logins/content");
?>