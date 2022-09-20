<?php

$username = Specific::Filter($_GET['username']);

if(empty($username)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$user = $dba->query('SELECT * FROM '.T_USER.' WHERE username = ?', $username)->fetchArray();

if(empty($user)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$TEMP['#page'] 		  = 'messages';
$TEMP['#title']       = $TEMP['#word']['messages'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("messages/content");
?>