<?php
if($one == 'fetch'){
	$typing = Specific::Filter($_POST['typing']);
	$type = Specific::Filter($_POST['type']);
	
	if(in_array($type, array('first', 'last', 'users'))){
		$profile_id = Specific::Filter($_POST['profile_id']);

		if(!empty($profile_id) && is_numeric($profile_id)){
			$messages_ids = Specific::Filter($_POST['messages_ids']);
			$messages_ids = html_entity_decode($messages_ids);
			$messages_ids = json_decode($messages_ids);

			$messages = Load::Messages($profile_id, $messages_ids, $type);

			if($messages['return']){
				$deliver['S'] = 200;
				$deliver['HTM'] = $messages['html'];
				$deliver['MIDS'] = $messages['messages_ids'];
			}
		}

		if(in_array($type, array('last', 'users'))){
			$keyword = Specific::Filter($_POST['keyword']);
			$last_cupdate = Specific::Filter($_POST['last_cupdate']);
			$profiles_ids = Specific::Filter($_POST['profiles_ids']);
			$profiles_ids = html_entity_decode($profiles_ids);
			$profiles_ids = json_decode($profiles_ids);

			$chats = Load::Chats($profiles_ids, $type, $keyword, $last_cupdate);

			if($chats['return']){
				$deliver['S'] = 200;
				$deliver['HTU'] = $chats['html'];
				$deliver['LCU'] = $chats['last_cupdate'];
				$deliver['UIDS'] = $chats['profiles_ids'];
			}
			if($type == 'last'){

				if(in_array($typing, array('true', 'false'))){
					if($typing == 'true'){
						if($dba->query('SELECT COUNT(*) FROM '.T_TYPING.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $profile_id)->fetchArray(true) == 0){
							$dba->query('INSERT INTO '.T_TYPING.' (user_id, profile_id, created_at) VALUES (?, ?, ?)', $TEMP['#user']['id'], $profile_id, time());
						}
					} else {
						$dba->query('DELETE FROM '.T_TYPING.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $profile_id);
					}

					if($dba->query('SELECT COUNT(*) FROM '.T_TYPING.' WHERE user_id = ? AND profile_id = ?', $profile_id, $TEMP['#user']['id'])->fetchArray(true) > 0){
						$user = Specific::Data($profile_id, array(
							'username',
							'name',
							'surname',
							'avatar'
						));
						$TEMP['username'] = $user['username'];
						$TEMP['avatar_s'] = $user['avatar_s'];
						$deliver['HTT'] = Specific::Maket('messages/includes/dot');
					}
				}

				$update_users = array();

				foreach($profiles_ids as $key => $profile_id){
					$last_message = Specific::LastMessage($profile_id);
					
					if($last_message['return']){
						$update_users[$key]['TX'] = $last_message['text'];
						$update_users[$key]['CA'] = $last_message['created_at'];
						$update_users[$key]['LU'] = $last_message['unseen'];
						$update_users[$key]['EL'] = '.content_pnuser[data-id='.$profile_id.']';
					}
				}

				if(!empty($update_users)){
					$deliver['S'] = 200;
					$deliver['US'] = $update_users;
				}


				$fs = 0;
				foreach ($messages_ids as $key => $message_id) {
					$message = $dba->query('SELECT user_id, deleted_at FROM '.T_MESSAGE.' WHERE id = ?', $message_id)->fetchArray();
					$messafis = $dba->query('SELECT * FROM '.T_MESSAFI.' WHERE message_id = ?', $message_id)->fetchAll();
					
					if($message['deleted_at'] != 0){
						$TEMP['!id'] = $message_id;
						$maket = 'deleted-outgoing';
						if(!Specific::IsOwner($message['user_id'])){
							$maket = 'deleted-incoming';
						}

						$deleted_messages[$key]['ID'] = $message_id;
						$deleted_messages[$key]['HTG'] = Specific::Maket("messages/includes/{$maket}");
					}

					if(!empty($messafis)){
						foreach ($messafis as $messafi) {
							if($messafi['deleted_at'] != 0){
								$deleted_ftype = 'file';
								if(in_array(pathinfo($messafi['name'], PATHINFO_EXTENSION), array('jpeg', 'jpg', 'png', 'gif'))){
									$deleted_ftype = 'image';
								}

								$TEMP['!fi_id'] = $messafi['id'];
								$TEMP['!fi_type'] = $deleted_ftype;

								$deleted_files[$fs]['ID'] = $messafi['id'];
								$deleted_files[$fs]['TP'] = $deleted_ftype;
								$fi_maket = 'deleted-outfile';
								if(!Specific::IsOwner($message['user_id'])){
									$fi_maket = 'deleted-infile';
								}
								$deleted_files[$fs]['HTF'] = Specific::Maket("messages/includes/{$fi_maket}");
								$fs++;
							}
						}
					}
				}
				Specific::DestroyMaket();

				if(!empty($deleted_messages) || !empty($deleted_files)){
					$deliver['S'] = 200;
					if(!empty($deleted_messages)){
						$deliver['MS'] = $deleted_messages;
					}
					if(!empty($deleted_files)){
						$deliver['FS'] = $deleted_files;
					}
				}
			}
		}
	}
} else if($one == 'send'){
	$profile_id = Specific::Filter($_POST['profile_id']);
	$answered_id = Specific::Filter($_POST['answered_id']);
	$count_files = Specific::Filter($_POST['count_files']);
	$text = Specific::Filter($_POST['text']);
	$type = Specific::Filter($_POST['type']);

	if(!empty($profile_id) && is_numeric($profile_id) && (!empty(trim($text)) || $count_files > 0) && is_numeric($count_files) && in_array($type, array('text', 'file', 'image')) && !in_array($profile_id, Specific::BlockedUsers(false))){

		$user = $dba->query('SELECT shows, COUNT(*) as count FROM '.T_USER.' WHERE id = ?', $profile_id)->fetchArray();
		if($user['count'] > 0){
			$user = Specific::Data($user, 3);
			if($user['shows']['messages'] == 'on' && $TEMP['#user']['shows']['messages'] == 'on'){
				$created_at = $updated_at = time();
				$TEMP['#messafi'] = false;
				$TEMP['#has_image'] = false;
				$TEMP['#has_file'] = false;

				if($count_files > 0){
					if(empty(trim($text))){
						$text = NULL;
					}
				}
				
				$chat = $dba->query('SELECT *, COUNT(*) as count FROM '.T_CHAT.' WHERE (user_id = ? AND profile_id = ?) OR (profile_id = ? AND user_id = ?)', $TEMP['#user']['id'], $profile_id, $TEMP['#user']['id'], $profile_id)->fetchArray();

				$chat_id = $chat['id'];
				if($chat['count'] > 0){
					$dba->query('UPDATE '.T_CHAT.' SET updated_at = ? WHERE id = ?', $updated_at, $chat_id);
				} else {
					$chat_id = $dba->query('INSERT INTO '.T_CHAT.' (user_id, profile_id, updated_at, created_at) VALUES (?, ?, ?, ?)', $TEMP['#user']['id'], $profile_id, $updated_at, $created_at)->insertId();
				}

				if(!empty($chat_id)){
					$message_exists = $dba->query('SELECT COUNT(*) FROM '.T_MESSAGE.' WHERE chat_id = ? AND ((user_id = ? AND deleted_fuser = 0) OR (profile_id = ? AND deleted_fprofile = 0))', $chat_id, $TEMP['#user']['id'], $TEMP['#user']['id'])->fetchArray(true);

					$insert_id = $dba->query('INSERT INTO '.T_MESSAGE.' (chat_id, user_id, profile_id, text, created_at) VALUES (?, ?, ?, ?, ?)', $chat_id, $TEMP['#user']['id'], $profile_id, $text, $created_at)->insertId();

					if($insert_id){
						$dba->query('DELETE FROM '.T_TYPING.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $profile_id);


						if($count_files > 0){
							$TEMP['#messafi'] = true;
							$files = array();
							for ($i=0; $i < $count_files; $i++) {
								$messafi = $_FILES["file_{$i}"];
								if(!empty($messafi)){
									$file = Specific::UploadMessagefi(array(
										'name' => $messafi['name'],
										'tmp_name' => $messafi['tmp_name'],
										'size' => $messafi['size'],
										'type' => $messafi['type'],
										'message_id' => $insert_id
									));
									if($file['return']){
										$files[] = "('{$messafi['name']}', {$insert_id}, '{$file['file']}', {$messafi['size']}, {$created_at})";
									}
								}
							}
							$dba->query('INSERT INTO '.T_MESSAFI.' (name, message_id, file, size, created_at) VALUES '.implode(',', $files));
						}

						if(!empty($answered_id) && is_numeric($answered_id)){
							if(($type == 'text' && $dba->query('SELECT COUNT(*) FROM '.T_MESSAGE.' WHERE id = ? AND chat_id = ?', $answered_id, $chat_id)->fetchArray(true) > 0) || (in_array($type, array('file', 'image')) && $dba->query('SELECT COUNT(*) FROM '.T_MESSAFI.' WHERE id = ?', $answered_id)->fetchArray(true) > 0)){
								if($dba->query('SELECT COUNT(*) FROM '.T_MESSAAN.' WHERE message_id = ? AND answered_id = ? AND type = ?', $insert_id, $answered_id, $type)->fetchArray(true) == 0){
									if($dba->query('INSERT INTO '.T_MESSAAN.' (message_id, answered_id, type, created_at) VALUES (?, ?, ?, ?)', $insert_id, $answered_id, $type, $created_at)->returnStatus()){
										$TEMP['!ans_deleted'] = false;
										if($type == 'text'){
											$answered = $dba->query('SELECT * FROM '.T_MESSAGE.' WHERE id = ?', $answered_id)->fetchArray();

											$ans_pid = $answered['profile_id'];
											$user_id = $answered['user_id'];
											$TEMP['!ans_text'] = Specific::TextFilter($answered['text'], false);
										} else {
											$amessafi = $dba->query('SELECT f.*, m.user_id, m.profile_id FROM '.T_MESSAFI.' f INNER JOIN '.T_MESSAGE.' m WHERE f.id = ? AND m.id = f.message_id', $answered_id)->fetchArray();
											
											$ans_pid = $amessafi['profile_id'];
											$user_id = $amessafi['user_id'];
											$TEMP['!fi_aname'] = $amessafi['name'];
											$TEMP['!fi_asize'] = Specific::SizeFormat($amessafi['size']);
											if($type == 'image'){
												$TEMP['!fi_aurl'] = Specific::Url("uploads/messages/{$amessafi['file']}");
											}
										}

										$ans_user = Specific::Data($user_id, array('username', 'name', 'surname'));
										$TEMP['!type'] = 'answered';
										$TEMP['!ans_type'] = $type;
										$TEMP['!ans_title'] = "{$TEMP['#word']['you_responded_to']} {$ans_user['username']}";
										if($ans_pid == $profile_id){
											$TEMP['!ans_title'] = $TEMP['#word']['you_replied_own_message'];
										}

									}
								}
							}
						}


						$messafis = $dba->query('SELECT * FROM '.T_MESSAFI.' WHERE message_id = ?', $insert_id)->fetchAll();
						if(!empty($messafis)){
							$TEMP['#messafi'] = true;
							foreach ($messafis as $messafi) {
								$TEMP['!fi_id'] = $messafi['id'];
								$TEMP['!fi_name'] = $messafi['name'];
								$TEMP['!fi_url'] = Specific::Url("uploads/messages/{$messafi['file']}");
								$TEMP['!fi_size'] = Specific::SizeFormat($messafi['size']);

								if(in_array(pathinfo($messafi['name'], PATHINFO_EXTENSION), array('jpeg', 'jpg', 'png', 'gif'))){
									$TEMP['#has_image'] = true;
									$TEMP['!images'] .= Specific::Maket("messages/includes/outimage");
								} else {
									$TEMP['#has_file'] = true;
									$TEMP['!files'] .= Specific::Maket("messages/includes/outfile");
								}
							}
						}

						$TEMP['!id'] = $insert_id;
						$TEMP['!text'] = Specific::TextFilter($text);
						$TEMP['!created_at'] = Specific::DateString($created_at);

						$deliver['S'] = 200;
						$deliver['CID'] = $chat_id;
						$deliver['MID'] = $insert_id;

						$deliver['CO'] = $message_exists == 0;

						$deliver['HTM'] = Specific::Maket('messages/includes/outgoing');
						$deliver['TX'] = "{$TEMP['#word']['you']}: ".Specific::TextFilter($text, false);
						if($text == NULL){
							$deliver['TX'] = "{$TEMP['#word']['you']}: {$TEMP['#word']['attached_file']}";
						}
						$deliver['CA'] = Specific::DateString($created_at);
						$deliver['EL'] = '.content_pnuser[data-id='.$profile_id.']';
					}
				}
			}
		}
	}
} else if($one == 'search-users'){
	$keyword = Specific::Filter($_POST['keyword']);

	if(!empty($keyword)){
		$users = $dba->query('SELECT * FROM '.T_USER.' WHERE id <> ? AND id NOT IN ('.$TEMP['#blocked_users'].') AND (name LIKE "%'.$keyword.'%" OR surname LIKE "%'.$keyword.'%" OR username LIKE "%'.$keyword.'%") AND status = "active" LIMIT 10', $TEMP['#user']['id'])->fetchAll();

		if(!empty($users)){
			foreach ($users as $user) {
				$user = Specific::Data($user, 3);

				$TEMP['!user'] = $user['user'];
				$TEMP['!avatar_s'] = $user['avatar_s'];
				$TEMP['!username'] = $user['username'];
				
				$html .= Specific::Maket('messages/includes/search-user');
			}

			$deliver = array(
				'S' => 200,
				'HT' => $html
			);
		} else {
			$TEMP['keyword'] = $keyword;
			$deliver = array(
				'S' => 204,
				'HT' => Specific::Maket('not-found/no-result-for')
			);
		}
	}
} else if($one == 'search-chats'){
	$keyword = Specific::Filter($_POST['keyword']);
	$profile_id = Specific::Filter($_POST['profile_id']);
	$profiles_ids = Specific::Filter($_POST['profiles_ids']);
	$profiles_ids = html_entity_decode($profiles_ids);
	$profiles_ids = json_decode($profiles_ids);

	$chats = Load::Chats($profiles_ids, 'first', $keyword);
	if($chats['return']){
		$deliver['S'] = 200;
		$deliver['KW'] = $keyword;
		$deliver['HTU'] = $chats['html'];
		$deliver['UIDS'] = $chats['profiles_ids'];
	} else if(!empty($keyword)){
		$TEMP['keyword'] = $keyword;
		$deliver['S'] = 204;
		$deliver['HT'] = Specific::Maket('not-found/no-result-for');
	}
} else if($one == 'delete-my-typings'){
	$profile_id = Specific::Filter($_POST['profile_id']);

	if(!empty($profile_id) && is_numeric($profile_id)){
		$delete_my_typings = Specific::DeleteMyTypings($profile_id);
		if($delete_my_typings['return']){
			$deliver = array(
				'S' => 200,
				'DL' => $delete_my_typings['delete_dot'],
				'TPS' => $delete_my_typings['typings']
			);
		}
	}
} else if($one == 'delete-both-message'){
	$id = Specific::Filter($_POST['id']);
	$for = Specific::Filter($_POST['for']);
	$type = Specific::Filter($_POST['type']);

	if(!empty($id) && is_numeric($id) && in_array($type, array('text', 'file', 'image')) && in_array($for, array('all', 'me'))){
		$deleted_at = time();
		if($type == 'text'){
			$message = $dba->query('SELECT user_id, COUNT(*) as count FROM '.T_MESSAGE.' WHERE id = ? AND profile_id NOT IN ('.$TEMP['#blocked_users'].')', $id)->fetchArray();

			if($message['count'] > 0){
				$query = 'deleted_fprofile = ?';
				if(Specific::IsOwner($message['user_id'])){
					$query = 'seen = 1, deleted_at = ?';
					if($for == 'me'){
						$query = 'deleted_fuser = ?';
					}
				}

				if($dba->query('UPDATE '.T_MESSAGE." SET {$query} WHERE id = ?", $deleted_at, $id)->returnStatus()){
					$TEMP['!id'] = $id;
					$deliver = array(
						'S' => 200,
						'HT' => Specific::Maket('messages/includes/deleted-outgoing'),
						'DA' => false
					);
				}
			}
		} else {
			$messafi = $dba->query('SELECT m.id, m.text, m.user_id, f.message_id, COUNT(f.id) as count FROM '.T_MESSAFI.' f INNER JOIN '.T_MESSAGE.' m WHERE f.id = ? AND m.id = f.message_id AND m.profile_id NOT IN ('.$TEMP['#blocked_users'].')', $id)->fetchArray();

			if($messafi['count'] > 0){
				$query = 'deleted_fprofile = ?';
				if(Specific::IsOwner($messafi['user_id'])){
					$query = 'deleted_at = ?';
					if($for == 'me'){
						$query = 'deleted_fuser = ?';
						$deliver['DA'] = false;
						if($messafi['text'] == NULL && $dba->query('SELECT COUNT(*) FROM '.T_MESSAAN.' WHERE message_id = ?', $messafi['message_id'])->fetchArray(true) > 0 && $dba->query('SELECT COUNT(*) FROM '.T_MESSAFI.' WHERE message_id = ? AND deleted_fuser = 0', $messafi['message_id'])->fetchArray(true) == 1){
							$deliver['DA'] = true;
						}
					}
				}

				if($dba->query('UPDATE '.T_MESSAFI." SET {$query} WHERE id = ?", $deleted_at, $id)->returnStatus()){
					if($dba->query('UPDATE '.T_MESSAGE.' SET seen = 1 WHERE id = ?', $messafi['id'])->returnStatus()){
						$TEMP['!fi_id'] = $id;
						$TEMP['!fi_type'] = $type;

						$deliver['S'] = 200;
						$deliver['HT'] = Specific::Maket('messages/includes/deleted-outfile');
					}
				}
			}
		}
	}
} else if($one == 'delete-me-message'){
	$id = Specific::Filter($_POST['id']);
	$type = Specific::Filter($_POST['type']);

	if(!empty($id) && is_numeric($id) && in_array($type, array('text', 'file', 'image'))){
		$deleted_at = time();
		if($type == 'text'){
			$message = $dba->query('SELECT user_id, COUNT(*) as count FROM '.T_MESSAGE.' WHERE id = ? AND profile_id NOT IN ('.$TEMP['#blocked_users'].')', $id)->fetchArray();

			if($message['count'] > 0){
				$query = 'deleted_fprofile = ?';
				if(Specific::IsOwner($message['user_id'])){
					$query = 'deleted_fuser = ?';
				}

				if($dba->query('UPDATE '.T_MESSAGE." SET {$query} WHERE id = ?", $deleted_at, $id)->returnStatus()){
					$deliver = array(
						'S' => 200,
						'DA' => false
					);
				}
			}
		} else {
			$messafi = $dba->query('SELECT m.user_id, m.text, f.message_id, COUNT(f.id) as count FROM '.T_MESSAFI.' f INNER JOIN '.T_MESSAGE.' m WHERE f.id = ? AND m.id = f.message_id AND m.profile_id NOT IN ('.$TEMP['#blocked_users'].')', $id)->fetchArray();

			if($messafi['count'] > 0){
				$query = 'deleted_fprofile = ?';
				if(Specific::IsOwner($messafi['user_id'])){
					$query = 'deleted_fuser = ?';
				}

				if($dba->query('UPDATE '.T_MESSAFI." SET {$query} WHERE id = ?", $deleted_at, $id)->returnStatus()){
					$deliver['S'] = 200;
					$deliver['DA'] = false;
					if($messafi['text'] == NULL && $dba->query('SELECT COUNT(*) FROM '.T_MESSAAN.' WHERE message_id = ?', $messafi['message_id'])->fetchArray(true) > 0 && $dba->query("SELECT COUNT(*) FROM ".T_MESSAFI." WHERE message_id = ? AND {$query}", $messafi['message_id'], 0)->fetchArray(true) == 0){
						$deliver['DA'] = true;
					}
				}
			}
		}

	}
} else if($one == 'delete-chat'){
	$chat_id = Specific::Filter($_POST['chat_id']);

	if(!empty($chat_id) && is_numeric($chat_id)){
		$message = $dba->query('SELECT user_id, profile_id, COUNT(*) as count FROM '.T_MESSAGE.' WHERE (user_id = ? OR profile_id = ?) AND chat_id = ? AND profile_id NOT IN ('.$TEMP['#blocked_users'].')', $TEMP['#user']['id'], $TEMP['#user']['id'], $chat_id)->fetchArray();
		if($message['count'] > 0){
			$deleted_at = time();
			if($dba->query('UPDATE '.T_MESSAGE.' SET deleted_fuser = ? WHERE user_id = ? AND chat_id = ?', $deleted_at, $TEMP['#user']['id'], $chat_id)->returnStatus()){
				if($dba->query('UPDATE '.T_MESSAGE.' SET deleted_fprofile = ? WHERE profile_id = ? AND chat_id = ?', $deleted_at, $TEMP['#user']['id'], $chat_id)->returnStatus()){
					$deliver['S'] = 200;
				}
			}
		}
	}
}
?>