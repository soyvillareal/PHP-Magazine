<?php
require_once('./assets/init.php');

if(!Functions::BrowserSupport()){
    if(!isset($_COOKIE['not_supported'])){
        header("Location: " . Functions::Url($RUTE['#r_compatibility']));
        exit();
    }
} else {
    setcookie('not_supported', null, -1, '/');
}

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

    $TEMP['!lang'] = $lang;
    $TEMP['!lang_name'] = $language['name'];

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

    $TEMP['languages'] .= Functions::Build('includes/wrapper/languages');
}

$TEMP['#return_url'] = $TEMP['#site_url'];
$return = Functions::Filter($_GET[$RUTE['#p_return']]);
if(!empty($return)){
    $TEMP['#return_url'] = $return;
    $TEMP['#return_param'] = "?{$RUTE['#p_return']}=$return";
}

if (isset($_COOKIE['_SAVE_SESSION'])) {
    if($dba->query('SELECT COUNT(*) FROM '.T_SESSION.' WHERE token = ?', Functions::Filter($_COOKIE['_SAVE_SESSION']))->fetchArray(true) > 0){
        $_COOKIE['_LOGIN_TOKEN'] = $_SESSION['_LOGIN_TOKEN'] = $_COOKIE['_SAVE_SESSION'];
    }
}

$TEMP['#darkmode'] = (bool)$_COOKIE['darkmode'];
if($TEMP['#loggedin'] === true){
    $TEMP['#darkmode'] = (bool)$TEMP['#user']['darkmode'];
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
            header("Location: ".Functions::ReturnUrl());
            exit();
        }
    }
}

if($TEMP['#settings']['switch_mode'] == 'off' || ($TEMP['#loggedin'] === false && !isset($_COOKIE['darkmode']))){
    if($TEMP['#settings']['theme_mode'] == 'night'){
        $TEMP['#darkmode'] = true;
    } else if($TEMP['#settings']['theme_mode'] == 'ligth'){
        $TEMP['#darkmode'] = false;
    }
}

$TEMP['#pages'] = $dba->query('SELECT * FROM '.T_PAGE)->fetchAll();
$TEMP['#categories'] = $dba->query('SELECT * FROM '.T_CATEGORY)->fetchAll();
if($one != 'amp'){
    $users = $dba->query('SELECT * FROM '.T_USER.' WHERE role = "publisher" OR role = "moderator" OR role = "admin"')->fetchAll();
    $TEMP['#users'] = array();
    foreach ($users as $user){
        $TEMP['#users'][] = Functions::Data($user, 3);
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

$TEMP['#show_index_palette'] = false;
if($TEMP['#admin'] == true && $TEMP['#settings']['show_palette'] == 'on'){
    $TEMP['#show_index_palette'] = true;
    $show_palette = Functions::Filter($_GET[$RUTE['#r_show_palette']]);
    $TEMP['#show_palette'] = json_decode($show_palette);

    Functions::BuildIndexPalette();
    Functions::BuildIndexPalette('dark');
}

$TEMP['global_title'] = $TEMP['#title'];
$TEMP['global_description'] = $TEMP['#description'];
$TEMP['global_keywords'] = $TEMP['#keyword'];
$TEMP['year_now'] = date('Y');
$TEMP['time'] = Functions::DateFormat(time(), 'complete');
$TEMP['content'] = $TEMP['#content'];


$build = 'wrapper';
if($one == 'amp'){
    $build = 'amp-wrapper';
    $TEMP['style_fonts'] = Functions::Build('amp/styles/style.fonts');
    $TEMP['style_palette'] = Functions::Build('amp/styles/style.palette');
    $TEMP['style_general'] = Functions::Build('amp/styles/style.general');
    
    if($TEMP['#dir'] == 'rtl'){
        $TEMP['style_rtl_general'] = Functions::Build('amp/styles/style.rtl.general');
    }

    if($TEMP['#settings']['switch_mode'] == 'on' || $TEMP['#settings']['theme_mode'] == 'night'){
        $TEMP['style_dark_general'] = Functions::Build('amp/styles/style.dark.general');
    }

    $TEMP['style_header'] = Functions::Build('amp/styles/style.header');
    $TEMP['style_post'] = Functions::Build('amp/styles/style.post');
    $TEMP['style_apostb'] = Functions::Build('amp/styles/style.post.related-bottom');
    $TEMP['style_apostc'] = Functions::Build('amp/styles/style.post.comments');
} else {
    $notifies = Functions::Notifies();

    $TEMP['#wpnotifications'] = $notifies['count_notifications'];
    $TEMP['#wpmessages'] = $notifies['count_messages'];

    if($notifies['return']){
        $TEMP['notifications'] = $notifies['count_text'];
    }
}

$content = Functions::Build($build);
echo Functions::HTMLFormatter($content);
$dba->close();
unset($TEMP);
unset($RUTE);
?>