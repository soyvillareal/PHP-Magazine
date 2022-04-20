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

$return = Specific::Filter($_GET[$TEMP['#p_return']]);
$TEMP['#return_url'] = $TEMP['#site_url'];
if(!empty($return)){
    $TEMP['#return_url'] = Specific::Url($return);
    $TEMP['#return_param'] = "?{$TEMP['#p_return']}=$return";
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
    $TEMP['profile_username'] = $TEMP['#user']['username'];
    $TEMP['profile_slug'] = Specific::ProfileUrl($TEMP['#user']['username']);
    $TEMP['profile_avatar'] = $TEMP['#user']['avatar_s'];
}
if (file_exists("./sources/$page")) {
    require_once("./sources/$page");
} else {
    if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE slug = ?', $one)->fetchArray(true) > 0){
        require_once("./sources/post/content.php");
    } else {
        require_once("./sources/404/content.php");
    }
}
$TEMP['#pages'] = $dba->query('SELECT * FROM '.T_PAGE)->fetchAll();
$TEMP['#categories'] = $dba->query('SELECT name, slug, footer FROM '.T_CATEGORY)->fetchAll();

$TEMP['global_title'] = $TEMP['#title'];
$TEMP['global_description'] = $TEMP['#description'];
$TEMP['global_keywords'] = $TEMP['#keyword'];
$TEMP['year_now'] = date('Y');
$TEMP['time'] = Specific::DateFormat(time(), true);
$TEMP['content'] = $TEMP['#content'];
$content = Specific::Maket('wrapper/content');
$noscrapping = Specific::NoScrapping($content);
echo $noscrapping['content'];
$dba->close();
unset($TEMP);
?>