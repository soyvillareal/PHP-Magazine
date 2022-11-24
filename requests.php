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

if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'){
	header("Location: " . Functions::Url('home'));
	exit();
}

$deliver = array();
$one = Functions::Filter($_GET['one']);
$token = Functions::Filter($_POST['token']);
if (!empty($_GET['token'])) {
	$token = Functions::Filter($_GET['token']);
}

if (empty($token) || $token != $_SESSION['_LOGIN_TOKEN']) {
	$deliver = array(
		'S' => 400,
		'E' => "*{$TEMP['#word']['invalid_request']}"
	);
} else if (!empty($_GET['request-name'])) {
	$req = Functions::Filter($_GET['request-name']);
	if (file_exists('./requests/'.$req.'.php')) {
		require_once('./requests/'.$req.'.php');
	} else {
		$deliver = array(
			'S' => 404,
			'E' => "*{$TEMP['#word']['request_not_found']}"
		);
	}
}

if(empty($deliver)){
	$deliver = array(
		'S' => 400,
		'E' => "*{$TEMP['#word']['oops_error_has_occurred']}"
	);
}

header('Content-Type: application/json');
echo json_encode($deliver);
exit();
?>