<?php
if($TEMP['#loggedin'] == true){
	if($one == 'get-count'){
		$notifications = $dba->query('SELECT COUNT(*) FROM '.T_NOTIFICATION.' WHERE user_id = ? AND seen = 0', $TEMP['#user']['id'])->fetchArray(true);

		if($notifications > 0){
			$deliver = array(
				'S' => 200,
				'CT' => $notifications <= 9 ? $notifications : "9+"
			);
		}
	} else if($one == 'get-content'){
		$notify_ids = Specific::Filter($_POST['notify_ids']);
		$notify_ids = html_entity_decode($notify_ids);
		$notify_ids = json_decode($notify_ids, true);

		$query = '';
		if(!empty($notify_ids)){
			$query = ' AND id NOT IN ('.implode(',', $notify_ids).')';
		}
		$notifications = $dba->query('SELECT * FROM '.T_NOTIFICATION.' WHERE user_id = ?'.$query.' ORDER BY created_at DESC LIMIT 10', $TEMP['#user']['id'])->fetchAll();

		if(!empty($notifications)){
			foreach ($notifications as $key => $notify) {
				$params = '';
				$TEMP['!id'] = $notify['id'];
				$TEMP['!type'] = $notify['type'];
				$TEMP['!seen'] = $notify['seen'];
			    if($notify['type'] == 'n_post'){
			        $post = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ? AND status = "approved"', $notify['notified_id'])->fetchArray();
			        $TEMP['!title'] = $post['title'];
			        $TEMP['!username'] = Specific::Data($post['user_id'], array('username'));

			        $TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
			    } else if(in_array($notify['type'], array('n_collab', 'n_preact', 'n_creact', 'n_rreact'))){
			    	if($notify['type'] == 'n_collab'){
			        	$post = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ? AND status = "approved"', $notify['notified_id'])->fetchArray();
				    	$user = Specific::Data($post['user_id']);

				        $TEMP['!title'] = $TEMP['#word']['added_collaborator_one_posts'];

				    } else if(in_array($notify['type'], array('n_preact', 'n_creact', 'n_rreact'))){
				    	$reaction = $dba->query('SELECT * FROM '.T_REACTION.' WHERE id = ?', $notify['notified_id'])->fetchArray();

				    	$user = Specific::Data($reaction['user_id']);
				    	$post_id = $reaction['reacted_id'];
				    	if(in_array($notify['type'], array('n_creact', 'n_rreact'))){
				    		$t_query = 'SELECT post_id FROM '.T_COMMENTS.' WHERE id = ?';
				    		if($notify['type'] == 'n_rreact'){
				    			$t_query = 'SELECT post_id FROM '.T_COMMENTS.' c WHERE (SELECT comment_id FROM '.T_REPLY.' WHERE id = ? AND comment_id = c.id) = id';
				    		}
				    		$post_id = $dba->query($t_query, $reaction['reacted_id'])->fetchArray(true);
				    	}

				    	$post = $dba->query('SELECT slug FROM '.T_POST.' WHERE id = ? AND status = "approved"', $post_id)->fetchArray();

				    	$type = $TEMP['#word']['liked_it'];
				    	if($reaction['type'] == 'dislike'){
				    		$type = $TEMP['#word']['dont_like_him'];
				    	}
				    	$place = $TEMP['#word']['one_of_your_posts'];
				    	if($reaction['place'] == 'comment'){
				    		$place = $TEMP['#word']['one_of_your_comments'];
				    	} else if($reaction['place'] == 'reply'){
				    		$place = $TEMP['#word']['one_of_your_answers'];
				    	}
				        $TEMP['!title'] = "{$type} {$place}";

				    }
			       	$TEMP['!username'] = $user['username'];
			       	$TEMP['!image'] = $user['avatar_s'];
			    } else if($notify['type'] == 'n_comment'){
			    	$params = "?{$TEMP['#p_comment_id']}={$notify['notified_id']}";
				    $comment = $dba->query('SELECT * FROM '.T_COMMENTS.' WHERE id = ?', $notify['notified_id'])->fetchArray();
				    $user = Specific::Data($comment['user_id']);
				    $post = $dba->query('SELECT slug FROM '.T_POST.' WHERE id = ? AND status = "approved"', $comment['post_id'])->fetchArray();

				    $TEMP['!title'] = "Ha comentado una de tus publicaciones";
			       	$TEMP['!username'] = $user['username'];
			       	$TEMP['!image'] = $user['avatar_s'];
			    } else if(in_array($notify['type'], array('n_preply', 'n_ureply'))){
			    	$params = "?{$TEMP['#p_reply_id']}={$notify['notified_id']}";
			    	$user_id = $dba->query('SELECT user_id FROM '.T_REPLY.' WHERE id = ?', $notify['notified_id'])->fetchArray(true);
			    	$post_id = $dba->query('SELECT post_id FROM '.T_COMMENTS.' WHERE (SELECT comment_id FROM '.T_REPLY.' WHERE id = ?) = id', $notify['notified_id'])->fetchArray();

				    $user = Specific::Data($user_id);
				    $post = $dba->query('SELECT slug FROM '.T_POST.' WHERE id = ? AND status = "approved"', $post_id)->fetchArray();

				    $TEMP['!title'] = $TEMP['#word']['has_replied_comment'];
				    if($notify['type'] == 'n_ureply'){
				    	$TEMP['!title'] = $TEMP['#word']['he_mentioned_comment'];
				    }
			       	$TEMP['!username'] = $user['username'];
			       	$TEMP['!image'] = $user['avatar_s'];
			    } else if($notify['type'] == 'n_followers'){
			    	$user_id = $dba->query('SELECT user_id FROM '.T_FOLLOWER.' WHERE id = ?', $notify['notified_id'])->fetchArray(true);

				    $user = Specific::Data($user_id);

				    $TEMP['!title'] = $TEMP['#word']['has_started_following'];
			       	$TEMP['!username'] = $user['username'];
			       	$TEMP['!image'] = $user['avatar_s'];

				    $slug = "{$TEMP['#r_user']}/{$user['username']}";
			    }
			    if(!empty($post) || $notify['type'] == 'n_followers'){
			    	if(!empty($post)){
			    		$slug = "{$post['slug']}{$params}";
			    	}
				    $TEMP['!url'] = Specific::Url($slug);
				    $TEMP['!created_date'] = date('c', $notify['created_at']);
				    $TEMP['!created_at'] = Specific::DateString($notify['created_at']);
				    $html .= Specific::Maket('includes/wrapper/notification');
				}
			}
			Specific::DestroyMaket();
			if($dba->query('UPDATE '.T_NOTIFICATION.' SET seen = 1 WHERE user_id = ?', $TEMP['#user']['id'])->returnStatus()){
				$deliver = array(
					'S' => 200,
					'HT' => $html
				);
			}
		}

	} if($one == 'settings'){
		$values = Specific::Filter($_POST['values']);
		$values = html_entity_decode($values);
		$values = json_decode($values, true);

		if(!empty($values)){
			$insettings = array('followers', 'followed', 'collab', 'react', 'comment', 'preply', 'ureply');
			$categories = $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false);
			$insettings = array_merge($insettings, $categories);

			$settings = array();
			foreach ($values as $key => $value) {
				if($value['value'] == 'on'){
					$val = $value['name'];
					$category = explode('_', $value['name']);
					if(count($category) > 1){
						$val = $category[1];
					}
					if(in_array($val, $insettings)){
						$settings[] = $val;
					}
				}
			}
			if($dba->query('UPDATE '.T_USER.' SET notifications = ? WHERE id = ?', json_encode($settings), $TEMP['#user']['id'])->returnStatus()){
				$deliver['S'] = 200;
			}
		}
	}
}
?>