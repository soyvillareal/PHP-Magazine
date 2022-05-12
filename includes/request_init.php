<?php
$TEMP = array();
$TEMP['#site_url'] = $site_url;
$TEMP['#settings'] = Specific::Settings();
$TEMP['#loggedin'] = Specific::Logged();
if ($TEMP['#loggedin'] == true) {
    $TEMP['#user'] = Specific::Data(null, 4);
}
$TEMP['#word'] = Specific::Words();
$TEMP['#token_session'] = Specific::TokenSession();
if (isset($_SESSION['_LOGIN_TOKEN'])) {
    if (empty($_COOKIE['_LOGIN_TOKEN'])) {
        setcookie("_LOGIN_TOKEN", $_SESSION['_LOGIN_TOKEN'], time() + 315360000, "/");
    }
}

$lang = array(
    'es' => 'es_ES',
    'en' => 'en_US'
);
$TEMP['#lang'] = $lang[$TEMP['#settings']['language']];
$TEMP['#admin'] = Specific::Admin();
$TEMP['#moderator'] = Specific::Moderator();
$TEMP['#publisher'] = Specific::Publisher();
?>