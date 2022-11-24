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

if($TEMP['#loggedin'] == false){
	header("Location: " . Functions::ReturnUrl());
	exit();
}

$profiles_ids = array();
$TEMP['#profile_id'] = 0;
$TEMP['#chat_id'] = 0;
$TEMP['#users_exists'] = false;
$TEMP['#messages_exists'] = false;
$TEMP['#enable_messages'] = true;
$TEMP['#username'] = Functions::Filter($_GET['username']);
if(!empty($TEMP['#username'])){
	$user = $dba->query('SELECT * FROM '.T_USER.' WHERE username = ?', $TEMP['#username'])->fetchArray();
	
	if(Functions::IsOwner($user['id']) || in_array($user['id'], Functions::BlockedUsers(false))){
		header("Location: " . Functions::Url($ROUTE['#r_messages']));
		exit();
	}
	if(empty($user)){
		header("Location: " . Functions::Url('404'));
		exit();
	}

	Functions::DeleteMyTypings();

	$user = Functions::Data($user, 3);
	$TEMP['#role'] = $user['role'];
	$TEMP['#user_deleted'] = $user['status'] != 'deleted';
	$TEMP['#profile_id'] = $user['id'];
	
	if($user['shows']['messages'] == 'off' || $TEMP['#user']['shows']['messages'] == 'off'){
		$TEMP['#enable_messages'] = false;
	}
	
	if($user['status'] == 'deleted'){
		$TEMP['slug'] = '#';
		$TEMP['username'] = $TEMP['#word']['user'];
		$TEMP['avatar_s'] = Functions::Url('/themes/default/images/users/default-holder-s.jpeg');
	} else {
		$TEMP['slug'] = $user['slug'];
		$TEMP['username'] = $user['username'];
		$TEMP['avatar_s'] = $user['avatar_s'];
	}

	$messages = Loads::Messages($user);
	$chats_html = Functions::Chat(array(
		'user_id' => $TEMP['#user']['id'],
		'profile_id' => $user['id']
	));
	
	if(!empty($chats_html['chat_id'])){
		$TEMP['#chat_id'] = $chats_html['chat_id'];
	}

	$profiles_ids[] = $user['id'];

	if($messages['return']){
		$TEMP['#messages_exists'] = true;
		$TEMP['messages'] = $messages['html'];
		$TEMP['messages_ids'] = implode(',', $messages['messages_ids']);
	} else {
		$TEMP['messages'] = Functions::Build('not-found/messages');
	}
} else {
	$TEMP['messages'] = Functions::Build('not-found/messages');
}


$chats = Loads::Chats($profiles_ids);

if($chats['return']){
	$chats_html['html'] .= $chats['html'];
}

if(!empty($chats_html['html'])){
	$TEMP['#users_exists'] = true;
	$TEMP['chats'] = $chats_html['html'];
} else {
	$TEMP['chats'] = Functions::Build('not-found/messages');
}

$TEMP['last_cupdate'] = $chats['last_cupdate'];
$TEMP['profiles_ids'] = implode(',', $chats['profiles_ids']);
$TEMP['file_size_limit'] = Functions::SizeFormat($TEMP['#settings']['file_size_limit']);

$TEMP['#page'] 		  = 'messages';
$TEMP['#title']       = $TEMP['#word']['messages'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("messages/content");
?>