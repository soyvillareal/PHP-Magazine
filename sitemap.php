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

require_once('./assets/init.php');

$sapi_type = php_sapi_name();
if((substr($sapi_type, 0, 3) == 'cli' || empty($_SERVER['REMOTE_ADDR'])) && (time() > (intval($TEMP['#settings']['last_sitemap']) + 39600) || !file_exists("sitemaps"))){
	Functions::Sitemap();
}
?>