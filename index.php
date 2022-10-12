<?php
require_once('./includes/autoload.php');

$page = 'home/content.php';
$one = $_GET['one'];
if (isset($one)) {
    if(!empty($_GET['page'])){
        $page = $one.'/'.$_GET['page'].'/content.php'; 
    } else {
        $page = $one.'/content.php';
    }
}



foreach($TEMP['#languages'] as $lang){
    $language = $dba->query('SELECT * FROM '.T_LANGUAGE.' WHERE lang = ?', $lang)->fetchArray();

    if($TEMP['#language'] == $lang){
        $TEMP['dir'] = $language['dir'];
    }

    $TEMP['!lang'] = $lang;
    $TEMP['!lang_name'] = $TEMP['#word']["lang_{$language['name']}"];

    $lang_url = "{$RUTE['#p_language']}={$lang}";
    if(strpos($_SERVER['REQUEST_URI'], '?') !== false){
        $lang_regex = "/{$RUTE['#p_language']}=(.+?)[^*]/";
        if(preg_match($lang_regex, $_SERVER['REQUEST_URI']) > 0){
            $lang_url = preg_replace($lang_regex, "{$RUTE['#p_language']}={$lang}", $_SERVER['REQUEST_URI']);
        }
    } else {
        $http = 'https://';
        if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on'){
            $http = 'http://';
        }
        $lang_url = "{$http}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}?{$lang_url}";
    }

    $TEMP['!lang_url'] = $lang_url;

    $TEMP['languages'] .= Specific::Maket('includes/wrapper/languages');
}


$TEMP['#return_url'] = $TEMP['#site_url'];
$return = Specific::Filter($_GET[$RUTE['#p_return']]);
if(!empty($return)){
    $TEMP['#return_url'] = $return;
    $TEMP['#return_param'] = "?{$RUTE['#p_return']}=$return";
}

if (isset($_COOKIE['_SAVE_SESSION'])) {
    if($dba->query('SELECT COUNT(*) FROM '.T_SESSION.' WHERE token = ?', Specific::Filter($_COOKIE['_SAVE_SESSION']))->fetchArray(true) > 0){
        $_COOKIE['_LOGIN_TOKEN'] = $_SESSION['_LOGIN_TOKEN'] = $_COOKIE['_SAVE_SESSION'];
    }
}
if($TEMP['#loggedin'] === true){
    if ($TEMP['#user']['status'] != 'active') {
        if (isset($_COOKIE['_LOGIN_TOKEN'])) {
            setcookie('_LOGIN_TOKEN', null, -1,'/');
            if (isset($_COOKIE['_SAVE_SESSION'])) {
                setcookie('_SAVE_SESSION', null, -1, '/');
            }
        }
        session_destroy();
    }
    if(!empty($_COOKIE['_LOGIN_TOKEN'])){
        if($_COOKIE['_LOGIN_TOKEN'] != $_SESSION['_LOGIN_TOKEN']){
            unset($_COOKIE['_LOGIN_TOKEN']);
            header("Location: ".Specific::ReturnUrl());
            exit();
        }
    }
}

$TEMP['#pages'] = $dba->query('SELECT * FROM '.T_PAGE)->fetchAll();
$TEMP['#categories'] = $dba->query('SELECT * FROM '.T_CATEGORY)->fetchAll();
if($one != 'amp'){
    $users = $dba->query('SELECT * FROM '.T_USER.' WHERE role = "publisher" OR role = "moderator" OR role = "admin"')->fetchAll();
    $TEMP['#users'] = array();
    foreach ($users as $user){
        $TEMP['#users'][] = Specific::Data($user, 3);
    }
}

if (file_exists("./sources/{$page}")) {
    require_once("./sources/{$page}");
} else {
    if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE slug = ?', $one)->fetchArray(true) > 0){
        require_once("./sources/post/content.php");
    } else {
        require_once("./sources/404/content.php");
    }
}
    

$TEMP['global_title'] = $TEMP['#title'];
$TEMP['global_description'] = $TEMP['#description'];
$TEMP['global_keywords'] = $TEMP['#keyword'];
$TEMP['year_now'] = date('Y');
$TEMP['time'] = Specific::DateFormat(time(), 'complete');
$TEMP['content'] = $TEMP['#content'];

$maket = 'wrapper';
if($one == 'amp'){
    $maket = 'amp-wrapper';
    $TEMP['style_fonts'] = Specific::Maket('amp/styles/style.fonts');
    $TEMP['style_general'] = Specific::Maket('amp/styles/style.general');
    $TEMP['style_header'] = Specific::Maket('amp/styles/style.header');
    $TEMP['style_post'] = Specific::Maket('amp/styles/style.post');
    $TEMP['style_apostb'] = Specific::Maket('amp/styles/style.post.related-bottom');
    $TEMP['style_apostc'] = Specific::Maket('amp/styles/style.post.comments');
} else {
    $notifies = Specific::Notifies();

    $TEMP['#wpnotifications'] = $notifies['count_notifications'];
    $TEMP['#wpmessages'] = $notifies['count_messages'];

    if($notifies['return']){
        $TEMP['notifications'] = $notifies['count_text'];
    }
}
$content = Specific::Maket($maket);
$HTMLFormatter = Specific::HTMLFormatter($content);
echo $HTMLFormatter['content'];
$dba->close();
unset($TEMP);
unset($RUTE);
?>