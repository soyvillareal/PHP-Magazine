<?php
if (!empty($_SESSION['_LOGIN_TOKEN'])) {
    $dba->query('DELETE FROM '.T_SESSION.' WHERE token = ?', Specific::Filter($_SESSION['_LOGIN_TOKEN']));
}
if (isset($_COOKIE['_LOGIN_TOKEN'])) {
    $dba->query('DELETE FROM '.T_SESSION.' WHERE token = ?', Specific::Filter($_COOKIE['_LOGIN_TOKEN']));
    setcookie('_LOGIN_TOKEN', null, -1, '/');
}
if (isset($_COOKIE['_SAVE_SESSION'])) {
    setcookie('_SAVE_SESSION', null, -1, '/');
}
session_destroy();
header("Location: {$TEMP['#return_url']}");
exit();
?>