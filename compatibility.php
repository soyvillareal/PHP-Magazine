<?php 
require_once('./assets/init.php');

$browsers = array(
    array(
        'name' => 'Google Chrome',
        'link' => 'https://www.google.com/chrome/index.html',
        'logo' => Functions::GetFile('images/browsers/chrome.png', 2)
    ),
    array(
        'name' => 'Microsoft Edge',
        'link' => 'https://www.microsoft.com/edge',
        'logo' => Functions::GetFile('images/browsers/edgium.png', 2)
    ),
    array(
        'name' => 'Opera',
        'link' => 'https://www.opera.com/',
        'logo' => Functions::GetFile('images/browsers/opera.png', 2)
    ),
    array(
        'name' => 'Mozilla Firefox',
        'link' => 'https://www.mozilla.org/firefox/new/',
        'logo' => Functions::GetFile('images/browsers/firefox.png', 2)
    )
);

foreach ($browsers as $key => $browser) {
    $TEMP['!class'] = '';
    $TEMP['!name'] = $browser['name'];
    $TEMP['!link'] = $browser['link'];
    $TEMP['!logo'] = $browser['logo'];

    if($key == 1){
        $TEMP['!class'] = ' second';
    } else if($key == end(array_keys($browsers))){
        $TEMP['!class'] = ' last';
    }

    $TEMP['browsers'] .= Functions::Build('includes/compatibility/browsers');
}
Functions::DestroyBuild();

$TEMP['title'] = $TEMP['#word']['browser_up_date'];
$TEMP['description'] = str_replace('{$settings->title}', $TEMP['#settings']['title'], $TEMP['#word']['can_use_latest_features']);

if(!Functions::BrowserSupport()){
    if(!isset($_COOKIE['not_supported'])){
        setcookie("not_supported", true, time() + 315360000, "/");
    }
    $TEMP['title'] = $TEMP['#word']['please_update_browser'];
    $TEMP['description'] = str_replace('{$settings->title}', $TEMP['#settings']['title'], $TEMP['#word']['browser_isnt_supported_anymore']);
} else {
    setcookie('not_supported', null, -1, '/');
}

$TEMP['#title'] = $TEMP['#word']['browser_not_supported']. ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords'] = $TEMP['#settings']['keyword'];

echo Functions::Build('compatibility');
?>