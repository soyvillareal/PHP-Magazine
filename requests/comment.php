<?php
if($TEMP['#loggedin'] == true){
	if($one == 'comment'){
		$post_id = Functions::Filter($_POST['post_id']);
		$text = Functions::Filter($_POST['text']);

		if(!empty($post_id) && is_numeric($post_id) && !empty(trim($text)) && mb_strlen(strip_tags(html_entity_decode($text)), "UTF8") <= $TEMP['#settings']['max_words_comments']){

			$post = $dba->query('SELECT user_id, COUNT(*) as count FROM '.T_POST.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', $post_id)->fetchArray();
			if($post['count'] > 0){
				$created_at = time();
				$insert_id = $dba->query('INSERT INTO '.T_COMMENTS.' (user_id, post_id, text, created_at) VALUES (?, ?, ? ,?)', $TEMP['#user']['id'], $post_id, $text, $created_at)->insertId();

				if(!empty($insert_id)){
					$comment = Functions::BuildComment(array(
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

					Functions::SetNotify(array(
						'user_id' => $post['user_id'],
						'notified_id' => $insert_id,
						'type' => 'pcomment',
					));

					Functions::ToMention(array(
						'text' => $text,
						'user_id' => $post['user_id'],
						'insert_id' => $insert_id
					));

				}
			}
		}
	} else if($one == 'reply'){
		$comment_id = Functions::Filter($_POST['comment_id']);
		$text = Functions::Filter($_POST['text']);

		if(!empty($comment_id) && is_numeric($comment_id) && !empty(trim($text)) && mb_strlen(strip_tags(html_entity_decode($text)), "UTF8") <= $TEMP['#settings']['max_words_comments']){
			if($dba->query('SELECT COUNT(*) FROM '.T_POST.' p WHERE (SELECT post_id FROM '.T_COMMENTS.' WHERE id = ? AND post_id = p.id) = id AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', $comment_id)->fetchArray(true) > 0){
				$created_at = time();
				$insert_id = $dba->query('INSERT INTO '.T_REPLY.' (user_id, comment_id, text, created_at) VALUES (?, ?, ? ,?)', $TEMP['#user']['id'], $comment_id, $text, $created_at)->insertId();

				if(!empty($insert_id)){
					$reply = Functions::BuildReply(array(
						'id' => $insert_id,
						'user_id' => $TEMP['#user']['id'],
						'comment_id' => $comment_id,
						'text' => $text,
						'created_at' => $created_at
					), 'new');

					$count_replies = $dba->query('SELECT COUNT(*) FROM '.T_REPLY.' WHERE comment_id = ?', $comment_id)->fetchArray(true);

					$deliver = array(
						'S' => 200,
						'HT' => $reply['html'],
						'HC' => $count_replies
					);

					$user_id = $dba->query('SELECT user_id FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray(true);

					Functions::SetNotify(array(
						'user_id' => $user_id,
						'notified_id' => $insert_id,
						'type' => 'preply',
					));

					Functions::ToMention(array(
						'text' => $text,
						'user_id' => $user_id,
						'insert_id' => $insert_id
					), 'reply');

				}
			}
		}
	} else if($one == 'pin'){
		$comment_id = Functions::Filter($_POST['comment_id']);

		if(!empty($comment_id) && is_numeric($comment_id)){
			$comment = $dba->query('SELECT *, 1 as pinned FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray();
			if(!empty($comment)){
				$post = $dba->query('SELECT *, COUNT(*) as count FROM '.T_POST.' WHERE id = ? AND status = "approved"', $comment['post_id'])->fetchArray();
				if($post['count'] > 0){
					if(Functions::IsOwner($post['user_id'])){
						$comment_old = $dba->query('SELECT *, 0 as pinned, COUNT(*) as count FROM '.T_COMMENTS.' WHERE pinned = 1')->fetchArray();
						if($comment_old['count'] > 0 && $comment_old['id'] != $comment_id){
							$dba->query('UPDATE '.T_COMMENTS.' SET pinned = 0 WHERE id = ?', $comment_old['id']);
							$deliver['HI'] = '.content_comment[data-id='.$comment_old['id'].']';
							$comment_old = Functions::BuildComment($comment_old);
							$deliver['HO'] = $comment_old['html'];
						}
						if($dba->query('UPDATE '.T_COMMENTS.' SET pinned = 1 WHERE id = ?', $comment_id)->returnStatus()){
							$comment = Functions::BuildComment($comment);
							$deliver['S'] = 200;
							$deliver['HT'] = $comment['html'];
						}
					}
				}
			}
		}
	} else if($one == 'unpin'){
		$comment_id = Functions::Filter($_POST['comment_id']);

		if(!empty($comment_id) && is_numeric($comment_id)){
			$comment = $dba->query('SELECT *, 0 as pinned FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray();
			if(!empty($comment)){
				$user_id = $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ?', $comment['post_id'])->fetchArray(true);
				if(Functions::IsOwner($user_id)){
					if($dba->query('UPDATE '.T_COMMENTS.' SET pinned = 0 WHERE pinned = 1')->returnStatus()){
						$deliver['HE'] = '.content_comment[data-id='.$comment['id'].']';
						$comment = Functions::BuildComment($comment);
						$deliver['S'] = 200;
						$deliver['HT'] = $comment['html'];
					}
				}
			}
		}
	} else if($one == 'delete'){
		$comment_id = Functions::Filter($_POST['comment_id']);
		$type = Functions::Filter($_POST['type']);

		if(!empty($comment_id) && is_numeric($comment_id) && in_array($type, array('comment', 'reply'))){
			if($type == 'comment'){
				$comment = $dba->query('SELECT *, COUNT(*) as count FROM '.T_COMMENTS.' WHERE id = ?', $comment_id)->fetchArray();
				if($comment['count'] > 0){
					$user_id = $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ? AND status = "approved"', $comment['post_id'])->fetchArray(true);

					if(Functions::IsOwner($comment['user_id']) || Functions::IsOwner($user_id)){
						if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id = ? AND (type = "n_pcomment" OR type = "n_ucomment")', $comment_id)->returnStatus()){
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

					$user_id = $dba->query('SELECT user_id FROM '.T_POST.' WHERE (SELECT post_id FROM '.T_COMMENTS.' WHERE id = ? AND status = "approved") = id', $reply['comment_id'])->fetchArray(true);

					if(Functions::IsOwner($reply['user_id']) || Functions::IsOwner($user_id)){
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