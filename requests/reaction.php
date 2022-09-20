<?php
if($TEMP['#loggedin'] == true){
	if($one == 'post-reaction'){
		$post_id = Specific::Filter($_POST['post_id']);
		$type = Specific::Filter($_POST['type']);

		if(!empty($post_id) && in_array($type, array('like', 'dislike'))){
			$post = $dba->query('SELECT *, COUNT(*) as count FROM '.T_POST.' WHERE id = ? AND status = "approved"', $post_id)->fetchArray();
			if($post['count'] > 0){
				if($type == 'like'){
					if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"', $TEMP['#user']['id'], $post_id)->fetchArray(true) > 0){
						$likes = ($post['likes']-1);
						if($dba->query('UPDATE '.T_POST.' SET likes = ? WHERE id = ?', $likes, $post_id)->returnStatus()){
							if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post") = notified_id AND type = "n_preact"', $TEMP['#user']['id'], $post_id)->returnStatus()){
								if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"', $TEMP['#user']['id'], $post_id)->returnStatus()){
									$deliver = array(
										'S' => 200,
										'DB' => '.btn_plike',
										'CR' => $likes
									);
								}
							}
						}
					} else {
						if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"', $TEMP['#user']['id'], $post_id)->fetchArray(true) > 0){
							$dislikes = ($post['dislikes']-1);
							if($dba->query('UPDATE '.T_POST.' SET dislikes = ? WHERE id = ?', $dislikes, $post_id)->returnStatus()){
								if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post") = notified_id AND type = "n_preact"', $TEMP['#user']['id'], $post_id)->returnStatus()){
									if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"', $TEMP['#user']['id'], $post_id)->returnStatus()){
										$deliver['DO'] = '.btn_pdislike';
										$deliver['CO'] = $dislikes;
									}
								}
							}
						}
						$likes = ($post['likes']+1);
						if($dba->query('UPDATE '.T_POST.' SET likes = ? WHERE id = ?', $likes, $post_id)->returnStatus()){
							$insert_id = $dba->query('INSERT INTO '.T_REACTION.' (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "like", "post", ?)', $TEMP['#user']['id'], $post_id, time())->insertId();
							if($insert_id){
								$deliver['S'] = 200;
								$deliver['AB'] = '.btn_plike';
								$deliver['CR'] = $likes;
							}
						}
					}
				} else {
					if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"', $TEMP['#user']['id'], $post_id)->fetchArray(true) > 0){
						$dislikes = ($post['dislikes']-1);
						if($dba->query('UPDATE '.T_POST.' SET dislikes = ? WHERE id = ?', $dislikes, $post_id)->returnStatus()){
							if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post") = notified_id AND type = "n_preact"', $TEMP['#user']['id'], $post_id)->returnStatus()){
								if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"', $TEMP['#user']['id'], $post_id)->returnStatus()){
									$deliver = array(
										'S' => 200,
										'DB' => '.btn_pdislike',
										'CR' => $dislikes
									);
								}
							}
						}
					} else {
						if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"', $TEMP['#user']['id'], $post_id)->fetchArray(true) > 0){
							$likes = ($post['likes']-1);
							if($dba->query('UPDATE '.T_POST.' SET likes = ? WHERE id = ?', $likes, $post_id)->returnStatus()){
								if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post") = notified_id AND type = "n_preact"', $TEMP['#user']['id'], $post_id)->returnStatus()){
									if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"', $TEMP['#user']['id'], $post_id)->returnStatus()){
										$deliver['DO'] = '.btn_plike';
										$deliver['CO'] = $likes;
									}
								}
							}
						}
						$dislikes = ($post['dislikes']+1);
						if($dba->query('UPDATE '.T_POST.' SET dislikes = ? WHERE id = ?', $dislikes, $post_id)->returnStatus()){
							$insert_id = $dba->query('INSERT INTO '.T_REACTION.' (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "dislike", "post", ?)', $TEMP['#user']['id'], $post_id, time())->insertId();
							if($insert_id){
								$deliver['S'] = 200;
								$deliver['AB'] = '.btn_pdislike';
								$deliver['CR'] = $dislikes;
							}
						}
					}
				}
				if(!empty($insert_id)){
					Specific::SetNotify(array(
						'user_id' => $post['user_id'],
						'notified_id' => $insert_id,
						'type' => 'preact',
					));
				}
			}
		}
	} else if($one == 'comment-reaction'){
		$comment_id = Specific::Filter($_POST['comment_id']);
		$type = Specific::Filter($_POST['type']);
		$treact = Specific::Filter($_POST['treact']);

		if(!empty($comment_id) && in_array($type, array('like', 'dislike')) && in_array($treact, array('comment', 'reply'))){

			$n_react = 'creact';
			$t_query = T_COMMENTS;
			if($treact == 'reply'){
				$n_react = 'rreact';
				$t_query = T_REPLY;
			}

			$n_creact = "n_{$n_react}";
			$comment = $dba->query('SELECT *, COUNT(*) as count FROM '.$t_query.' WHERE id = ?', $comment_id)->fetchArray();
			
			$likes = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE reacted_id = ? AND type = "like" AND place = ?', $comment_id, $treact)->fetchArray(true);
			$dislikes = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE reacted_id = ? AND type = "dislikes" AND place = ?', $comment_id, $treact)->fetchArray(true);

			if($comment['count'] > 0){
				if($type == 'like'){
					if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->fetchArray(true) > 0){
						if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?) = notified_id AND type = ?', $TEMP['#user']['id'], $comment_id, $treact, $n_creact)->returnStatus()){
							if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->returnStatus()){
								$deliver = array(
									'S' => 200,
									'DB' => '.btn_clike',
									'CR' => $likes > 0 ? ($likes-1) : 0
								);
							}
						}
					} else {
						if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->fetchArray(true) > 0){
							if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?) = notified_id AND type = ?', $TEMP['#user']['id'], $comment_id, $treact, $n_creact)->returnStatus()){
								if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->returnStatus()){
									$deliver['DO'] = '.btn_cdislike';
									$deliver['CO'] = $dislikes > 0 ? ($dislikes-1) : 0;
								}
							}
						}
						$insert_id = $dba->query('INSERT INTO '.T_REACTION.' (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "like", ?, ?)', $TEMP['#user']['id'], $comment_id, $treact, time())->insertId();
						if($insert_id){
							$deliver['S'] = 200;
							$deliver['AB'] = '.btn_clike';
							$deliver['CR'] = ($likes+1);
						}
					}
				} else {
					if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->fetchArray(true) > 0){
						if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?) = notified_id AND type = ?', $TEMP['#user']['id'], $comment_id, $treact, $n_creact)->returnStatus()){
							if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->returnStatus()){
								$deliver = array(
									'S' => 200,
									'DB' => '.btn_cdislike',
									'CR' => $dislikes > 0 ? ($dislikes-1) : 0
								);
							}
						}
					} else {
						if($dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->fetchArray(true) > 0){
							if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?) = notified_id AND type = ?', $TEMP['#user']['id'], $comment_id, $treact, $n_creact)->returnStatus()){
								if($dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?', $TEMP['#user']['id'], $comment_id, $treact)->returnStatus()){
									$deliver['DO'] = '.btn_clike';
									$deliver['CO'] = $likes > 0 ? ($likes-1) : 0;
								}
							}
						}

						$insert_id = $dba->query('INSERT INTO '.T_REACTION.' (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "dislike", ?, ?)', $TEMP['#user']['id'], $comment_id, $treact, time())->insertId();
						if($insert_id){
							$deliver['S'] = 200;
							$deliver['AB'] = '.btn_cdislike';
							$deliver['CR'] = ($dislikes+1);
						}
					}
				}

				if(!empty($insert_id)){
					Specific::SetNotify(array(
						'user_id' => $dba->query('SELECT user_id FROM '.$t_query.' WHERE (SELECT reacted_id FROM '.T_REACTION.' WHERE id = ?) = id', $insert_id)->fetchArray(true),
						'notified_id' => $insert_id,
						'type' => $n_react,
					));
				}
			}
		}
	}
}
?>