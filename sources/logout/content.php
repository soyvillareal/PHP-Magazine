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

if (!empty($_SESSION['_LOGIN_TOKEN'])) {
    $dba->query('DELETE FROM '.T_SESSION.' WHERE token = ?', Functions::Filter($_SESSION['_LOGIN_TOKEN']));
}
if (isset($_COOKIE['_LOGIN_TOKEN'])) {
    $dba->query('DELETE FROM '.T_SESSION.' WHERE token = ?', Functions::Filter($_COOKIE['_LOGIN_TOKEN']));
    setcookie('_LOGIN_TOKEN', null, -1, '/');
}
if (isset($_COOKIE['_SAVE_SESSION'])) {
    setcookie('_SAVE_SESSION', null, -1, '/');
}
session_destroy();
header("Location: {$TEMP['#return_url']}");
exit();
?>