<?php
if($TEMP['#loggedin'] == true){
	if($one == 'comment'){
		$post_id = Specific::Filter($_POST['post_id']);
		$text = Specific::Filter($_POST['text']);

		if(!empty($post_id) && is_numeric($post_id) && !empty($text) && strlen($text) <= 1000){
			$created_at = time();
			$insert_id = $dba->query('INSERT INTO '.T_COMMENTS.' (user_id, post_id, text, created_at) VALUES (?, ?, ? ,?)', $TEMP['#user']['id'], $post_id, $text, $created_at)->insertId();

			if(!empty($insert_id)){
				$comment = Specific::CommentMaket(array(
					'id' => $insert_id,
					'user_id' => $TEMP['#user']['id'],
					'post_id' => $post_id,
					'text' => $text,
					'created_at' => $created_at
				));
				$deliver = array(
					'S' => 200,
					'HT' => $comment['html'],
					'CC' => $dba->query('SELECT COUNT(*) FROM '.T_COMMENTS.' WHERE post_id = ?', $post_id)->fetchArray(true)
				);

				Specific::SetNotify(array(
					'user_id' => $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ?', $post_id)->fetchArray(true),
					'notified_id' => $insert_id,
					'type' => 'comment',
				));
			}
		}
	} else if($one == 'reply'){
		$comment_id = Specific::Filter($_POST['comment_id']);
		$text = Specific::Filter($_POST['text']);

		if(!empty($comment_id) && is_numeric($comment_id) && !empty($text) && strlen($text) <= 1000){

			$created_at = time();
			$insert_id = $dba->query('INSERT INTO '.T_REPLY.' (user_id, comment_id, text, created_at) VALUES (?, ?, ? ,?)', $TEMP['#user']['id'], $comment_id, $text, $created_at)->insertId();

			if(!empty($insert_id)){
				$reply = Specific::ReplyMaket(array(
					'id' => $insert_id,
					'user_id' => $TEMP['#user']['id'],
					'comment_id' => $comment_id,
					'text' => $text,
					'created_at' => $created_at
				), $comment_id, 'new');

				$count_replies = $dba->query('SELECT COUNT(*) FROM '.T_REPLY.' WHERE comment_id = ?', $comment_id)->fetchArray(true);

				$deliver = array(
					'S' => 200,
					'HT' => $reply['html'],
					'HC' => $count_replies
				);

				$user_id = $dba->query('SELECT user_id FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray(true);
				Specific::SetNotify(array(
					'user_id' => $user_id,
					'notified_id' => $insert_id,
					'type' => 'preply',
				));

				$username_exists = preg_match_all('/@([a-zA-Z0-9]+)/i', $text, $username);
				if($username_exists > 0){
					for ($i=0; $i < $username_exists; $i++) {
						$user = $dba->query('SELECT id, COUNT(*) as count FROM '.T_USER.' WHERE username = ? AND status = "active"', $username[1][$i])->fetchArray();
						if($user['count'] > 0){
							if($user['id'] != $user_id){
								if($dba->query('SELECT COUNT(*) FROM '.T_REPLY.' WHERE user_id = ? AND comment_id = ?', $user['id'], $comment_id)->fetchArray(true) > 0 || $user['id'] == $dba->query('SELECT user_id FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray(true)){
									Specific::SetNotify(array(
										'user_id' => $user['id'],
										'notified_id' => $insert_id,
										'type' => 'ureply',
									));
								}
							}
						}
					}
				}
			}
		}
	} else if($one == 'pin'){
		$comment_id = Specific::Filter($_POST['comment_id']);

		if(!empty($comment_id) && is_numeric($comment_id)){
			$comment = $dba->query('SELECT *, 1 as pinned FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray();
			if(!empty($comment)){
				$post = $dba->query('SELECT *, COUNT(*) as count FROM '.T_POST.' WHERE id = ?', $comment['post_id'])->fetchArray();
				if($post['count'] > 0){
					if(Specific::IsOwner($post['user_id'])){
						$comment_old = $dba->query('SELECT *, 0 as pinned, COUNT(*) as count FROM '.T_COMMENTS.' WHERE pinned = 1')->fetchArray();
						if($comment_old['count'] > 0 && $comment_old['id'] != $comment_id){
							$dba->query('UPDATE '.T_COMMENTS.' SET pinned = 0 WHERE id = ?', $comment_old['id']);
							$deliver['HI'] = '.content_comment[data-id='.$comment_old['id'].']';
							$comment_old = Specific::CommentMaket($comment_old);
							$deliver['HO'] = $comment_old['html'];
						}
						if($dba->query('UPDATE '.T_COMMENTS.' SET pinned = 1 WHERE id = ?', $comment_id)->returnStatus()){
							$comment = Specific::CommentMaket($comment);
							$deliver['S'] = 200;
							$deliver['HT'] = $comment['html'];
						}
					}
				}
			}
		}
	} else if($one == 'unpin'){
		$comment_id = Specific::Filter($_POST['comment_id']);

		if(!empty($comment_id) && is_numeric($comment_id)){
			$comment = $dba->query('SELECT *, 0 as pinned FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray();
			if(!empty($comment)){
				$user_id = $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ?', $comment['post_id'])->fetchArray(true);
				if(Specific::IsOwner($user_id)){
					if($dba->query('UPDATE '.T_COMMENTS.' SET pinned = 0 WHERE pinned = 1')->returnStatus()){
						$deliver['HE'] = '.content_comment[data-id='.$comment['id'].']';
						$comment = Specific::CommentMaket($comment);
						$deliver['S'] = 200;
						$deliver['HT'] = $comment['html'];
					}
				}
			}
		}
	} else if($one == 'delete'){
		$comment_id = Specific::Filter($_POST['comment_id']);
		$type = Specific::Filter($_POST['type']);

		if(!empty($comment_id) && is_numeric($comment_id) && in_array($type, array('comment', 'reply'))){
			if($type == 'comment'){
				$comment = $dba->query('SELECT *, COUNT(*) as count FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray();
				if($comment['count'] > 0){
					if(Specific::IsOwner($comment['user_id']) || $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ?', $comment['post_id'])->fetchArray(true) == $TEMP['#user']['id']){
						if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id = ? AND type = "n_comment"', $comment_id)->returnStatus()){
							if($dba->query('DELETE FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->returnStatus()){
								$deliver = array(
									'S' => 200,
									'DL' => '.content_comment[data-id='.$comment_id.']'
								);
							}
						}
					}
				}
			} else {
				$reply = $dba->query('SELECT *, COUNT(*) as count FROM '.T_REPLY.' WHERE id = ?', $comment_id)->fetchArray();
				if($reply['count'] > 0){
					if(Specific::IsOwner($reply['user_id']) || $dba->query('SELECT user_id FROM '.T_POST.' WHERE (SELECT post_id FROM '.T_COMMENTS.' WHERE id = ?) = id', $reply['comment_id'])->fetchArray(true) == $TEMP['#user']['id']){
						if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id = ? AND (type = "n_preply" OR type = "n_ureply")', $comment_id)->returnStatus()){
							if($dba->query('DELETE FROM '.T_REPLY.' WHERE id = ?', $comment_id)->returnStatus()){
								$deliver = array(
									'S' => 200,
									'DL' => '.content_reply[data-id='.$comment_id.']'
								);
							}
						}
					}
				}
			}
		}
	}
}
?>