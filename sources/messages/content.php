<?php

if($TEMP['#loggedin'] == false){
	header("Location: " . Specific::ReturnUrl());
	exit();
}

$profiles_ids = array();
$TEMP['#profile_id'] = 0;
$TEMP['#users_exists'] = false;
$TEMP['#messages_exists'] = false;
$TEMP['#username'] = Specific::Filter($_GET['username']);
if(!empty($TEMP['#username'])){
	$user = $dba->query('SELECT * FROM '.T_USER.' WHERE username = ?', $TEMP['#username'])->fetchArray();
	
	if(Specific::IsOwner($user['id']) || in_array($user['id'], Specific::BlockedUsers(false))){
		header("Location: " . Specific::Url($RUTE['#r_messages']));
		exit();
	}
	if(empty($user)){
		header("Location: " . Specific::Url('404'));
		exit();
	}

	$dba->query('DELETE FROM '.T_TYPING.' WHERE user_id = ? AND profile_id = ?', $user['id'], $TEMP['#user']['id']);

	$user = Specific::Data($user, 3);
	$TEMP['#role'] = $user['role'];
	$TEMP['#user_deleted'] = $user['status'] != 'deleted';
	$TEMP['#profile_id'] = $user['id'];
	
	if($user['status'] == 'deleted'){
		$TEMP['slug'] = '#';
		$TEMP['username'] = $TEMP['#word']['user'];
		$TEMP['avatar_s'] = Specific::Url('/themes/default/images/users/default-holder-s.jpeg');
	} else {
		$TEMP['slug'] = $user['slug'];
		$TEMP['username'] = $user['username'];
		$TEMP['avatar_s'] = $user['avatar_s'];
	}


	$messages = Load::Messages($user);
	$chats_html = Specific::Chat(array(
		'user_id' => $TEMP['#user']['id'],
		'profile_id' => $user['id']
	));
	
	$TEMP['#chat_id'] = $chats_html['chat_id'];

	$profiles_ids[] = $user['id'];

	if($messages['return']){
		$TEMP['#messages_exists'] = true;
		$TEMP['messages'] = $messages['html'];
		$TEMP['messages_ids'] = implode(',', $messages['messages_ids']);
	} else {
		$TEMP['messages'] = Specific::Maket('not-found/messages');
	}
} else {
	$TEMP['messages'] = Specific::Maket('not-found/messages');
}


$chats = Load::Chats($profiles_ids);

if($chats['return']){
	$chats_html['html'] .= $chats['html'];
}

if(!empty($chats_html['html'])){
	$TEMP['#users_exists'] = true;
	$TEMP['chats'] = $chats_html['html'];
} else {
	$TEMP['chats'] = Specific::Maket('not-found/messages');
}

$TEMP['last_cupdate'] = $chats['last_cupdate'];
$TEMP['profiles_ids'] = implode(',', $chats['profiles_ids']);
$TEMP['file_size_limit'] = Specific::SizeFormat($TEMP['#settings']['file_size_limit']);

$TEMP['#page'] 		  = 'messages';
$TEMP['#title']       = $TEMP['#word']['messages'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("messages/content");
?>