<?php 
$input = Specific::Filter($_POST['input']);
$type = Specific::Filter($_POST['type']);
$page = Specific::Filter($_POST['page']);
$dates = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
$re_password = Specific::Filter($_POST['re-password']);
if($type == 're-password'){
	$re_password = Specific::Filter($_POST['password']);
}
if(!empty($input) && !empty($type)){
	if ($dba->query('SELECT COUNT(*) FROM user WHERE dni = "'.$input.'"')->fetchArray() > 0 && $type == 'dni') {
	    $error = $TEMP['#word']['document_already_exists'];
	} else if (!preg_match('/^[0-9]/', $input) && $type == 'dni') {
	    $error = $TEMP['#word']['invalid_document_characters'];
	} else if ($dba->query('SELECT COUNT(*) FROM user WHERE email = "'.$input.'"')->fetchArray() > 0 && ($type == 'email' || ($type == 'settings-email' && $TEMP['#user']['email'] != $input))) {
	    $error = $TEMP['#word']['email_exists'];
	} else if (!filter_var($input, FILTER_VALIDATE_EMAIL) && ($type == 'email' || $type == 'settings-email')) {
	    $error = $TEMP['#word']['email_invalid_characters'];
	} else if($TEMP['#user']['password'] != sha1($input) && $type == 'current-password') {
	    $error = $TEMP['#word']['current_password_dont_match'];
	} else if (strlen($input) < 4 && $type == 'password') {
	    $error = $TEMP['#word']['password_is_short'];
	} else if ($input != $re_password && !empty($re_password) && ($type == 'password' || $type == 're-password')) {
	    $error = $TEMP['#word']['password_not_match'];
	} else if((strlen($input) > 2 && $type == 'day') || (!in_array($input, $dates) && $type == 'month') || (strlen($input) > 4 && $type == 'year')){
	    $error = $TEMP['#word']['please_enter_valid_date'];
	}

	$deliver['status'] = 200;
	if(isset($error)){
		$deliver = array(
		    'status' => 302,
		    'error' => "*$error"
		);
	}
}
?>