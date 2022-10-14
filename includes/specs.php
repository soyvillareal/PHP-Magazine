<?php
if (!version_compare(PHP_VERSION, '7.4.0', '>=')) {
    exit("Required PHP_VERSION >= 7.4.0 , Your PHP_VERSION is : " . PHP_VERSION . "\n");
}

$conn = @new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

$ServerFails = array();
if (mysqli_connect_errno()) {
    $ServerFails[] = 'Failed to connect to MySQL: ' . mysqli_connect_error();
}
if (!function_exists('curl_version')) {
    $ServerFails[] = "cURL: Not installed (NOT OK)";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
	$ServerFails[] = "GD Library: Not installed (NOT OK)";
}
if(!extension_loaded('mbstring')) {
    $ServerFails[] = "mbstring: Not installed (NOT OK)";
}
if(!function_exists('mail')) {
    $ServerFails[] = "Mail: Not installed (NOT OK)";
}
if(!extension_loaded('openssl')){
	$ServerFails[] = "OpenSSL: Not Installed (NOT OK)";
}

if(!empty($ServerFails)){
	foreach ($ServerFails as $value) {
        echo "<center><h2 style='color:red;'>" . $value . "</h2></center>";
    }
    die();
}

$conn->set_charset('utf8mb4');
$dba = new db($conn);
error_reporting(0);
@ini_set('max_execution_time', 0);
date_default_timezone_set('UTC');
session_start();
?>