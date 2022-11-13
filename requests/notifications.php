<?php
if($TEMP['#loggedin'] == true){
	if($one == 'get-count'){
		$notifies = Functions::Notifies();

		if($notifies['return']){
			$deliver = array(
				'S' => 200,
				'CT' => $notifies['count_text'],
				'CN' => $notifies['count_notifications'],
				'CM' => $notifies['count_messages'],
			);
		}
	} else if($one == 'get-content'){
		$notify_ids = Functions::Filter($_POST['notify_ids']);
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
			        $post = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', $notify['notified_id'])->fetchArray();
			        
			        if(!empty($post)){
			        	$has_notification = true;
				        $TEMP['!title'] = $post['title'];
				        $TEMP['!username'] = Functions::Data($post['user_id'], array('username'));

				        $TEMP['!thumbnail'] = Functions::GetFile($post['thumbnail'], 1, 's');
				    }
			    } else if(in_array($notify['type'], array('n_collab', 'n_preact', 'n_creact', 'n_rreact'))){
			    	if($notify['type'] == 'n_collab'){
			        	$post = $dba->query('SELECT * FROM '.T_POST.' p WHERE (SELECT post_id FROM '.T_COLLABORATOR.' WHERE id = ? AND post_id = p.id) = id AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', $notify['notified_id'])->fetchArray();
				    	
				    	if(!empty($post)){
			        		$has_notification = true;
					    	$user = Functions::Data($post['user_id']);
					       	$TEMP['!username'] = $user['username'];
					       	$TEMP['!image'] = $user['avatar_s'];
					        $TEMP['!title'] = $TEMP['#word']['added_collaborator_one_posts'];
					    }
				    } else if(in_array($notify['type'], array('n_preact', 'n_creact', 'n_rreact'))){
				    	$reaction = $dba->query('SELECT * FROM '.T_REACTION.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].')', $notify['notified_id'])->fetchArray();

				    	if(!empty($reaction)){
			        		$has_notification = true;
					    	$user = Functions::Data($reaction['user_id']);
					    	$post_id = $reaction['reacted_id'];
					    	if(in_array($notify['type'], array('n_creact', 'n_rreact'))){
					    		$t_query = 'SELECT post_id FROM '.T_COMMENTS.' WHERE id = ?';
								$params = "?{$RUTE['#p_comment_id']}={$reaction['reacted_id']}";
					    		if($notify['type'] == 'n_rreact'){
									$params = "?{$RUTE['#p_reply_id']}={$reaction['reacted_id']}";
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
			       			$TEMP['!username'] = $user['username'];
			       			$TEMP['!image'] = $user['avatar_s'];
					        $TEMP['!title'] = "{$type} {$place}";
					    }
				    }
			    } else if(in_array($notify['type'], array('n_pcomment', 'n_ucomment'))){
				    $comment = $dba->query('SELECT * FROM '.T_COMMENTS.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].')', $notify['notified_id'])->fetchArray();
				    
				    if(!empty($comment)){
			        	$has_notification = true;
				    	$params = "?{$RUTE['#p_comment_id']}={$notify['notified_id']}";
					    $user = Functions::Data($comment['user_id']);
					    $post = $dba->query('SELECT slug FROM '.T_POST.' WHERE id = ? AND status = "approved"', $comment['post_id'])->fetchArray();

					    $TEMP['!title'] = $TEMP['#word']['commented_one_your_posts'];
					    if($notify['type'] == 'n_ucomment'){
					    	$TEMP['!title'] = $TEMP['#word']['he_mentioned_comment'];
					    }
				       	$TEMP['!username'] = $user['username'];
				       	$TEMP['!image'] = $user['avatar_s'];
				    }
			    } else if(in_array($notify['type'], array('n_preply', 'n_ureply'))){
			    	$user_id = $dba->query('SELECT user_id FROM '.T_REPLY.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].')', $notify['notified_id'])->fetchArray(true);
			    	if(!empty($user_id)){
			        	$has_notification = true;
			    		$params = "?{$RUTE['#p_reply_id']}={$notify['notified_id']}";
				    	$post_id = $dba->query('SELECT post_id FROM '.T_COMMENTS.' WHERE (SELECT comment_id FROM '.T_REPLY.' WHERE id = ?) = id', $notify['notified_id'])->fetchArray();

					    $user = Functions::Data($user_id);
					    $post = $dba->query('SELECT slug FROM '.T_POST.' WHERE id = ? AND status = "approved"', $post_id)->fetchArray();

					    $TEMP['!title'] = $TEMP['#word']['has_replied_comment'];
					    if($notify['type'] == 'n_ureply'){
					    	$TEMP['!title'] = $TEMP['#word']['he_mentioned_comment'];
					    }
				       	$TEMP['!username'] = $user['username'];
				       	$TEMP['!image'] = $user['avatar_s'];
				    }
			    } else if($notify['type'] == 'n_followers'){
			    	$user_id = $dba->query('SELECT user_id FROM '.T_FOLLOWER.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].')', $notify['notified_id'])->fetchArray(true);
					
			    	if(!empty($user_id)){
			        	$has_notification = true;
					    $user = Functions::Data($user_id);

					    $TEMP['!title'] = $TEMP['#word']['has_started_following'];
				       	$TEMP['!username'] = $user['username'];
				       	$TEMP['!image'] = $user['avatar_s'];

					    $slug = "{$RUTE['#r_user']}/{$user['username']}";
					}
			    }
			    if($has_notification && (!empty($post) || $notify['type'] == 'n_followers')){
			    	if(!empty($post)){
			    		$slug = "{$post['slug']}{$params}";
			    	}
				    $TEMP['!url'] = Functions::Url($slug);
				    $TEMP['!created_date'] = date('c', $notify['created_at']);
				    $TEMP['!created_at'] = Functions::DateString($notify['created_at']);
				    $html .= Functions::Build('includes/wrapper/notification');
				}
			}
			Functions::DestroyBuild();
			if(!empty($html)){
				if($dba->query('UPDATE '.T_NOTIFICATION.' SET seen = 1 WHERE user_id = ?', $TEMP['#user']['id'])->returnStatus()){
					$deliver = array(
						'S' => 200,
						'HT' => $html
					);
				}
			}
		}

	} if($one == 'settings'){
		$values = Functions::Filter($_POST['values']);
		$values = html_entity_decode($values);
		$values = json_decode($values, true);

		if(!empty($values)){
			$insettings = array('post', 'followers', 'collab', 'react', 'pcomment', 'preply', 'mention');

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
						if($val != 'mention'){
							$settings[] = $val;
						} else {
							$settings[] = 'ucomment';
							$settings[] = 'ureply';
						}
					}
				}
			}
			if($dba->query('UPDATE '.T_USER.' SET notifications = ? WHERE id = ? AND status = "active"', json_encode($settings), $TEMP['#user']['id'])->returnStatus()){
				$deliver['S'] = 200;
			}
		}
	}
}
?>