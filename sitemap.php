<?php
require_once('./assets/init.php');

$sapi_type = php_sapi_name();
if((substr($sapi_type, 0, 3) == 'cli' || empty($_SERVER['REMOTE_ADDR'])) && (time() > (intval($TEMP['#settings']['last_sitemap']) + 39600) || !file_exists("sitemaps"))){
	Functions::Sitemap();
}
?>