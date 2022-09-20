<?php
if($one == 'load'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);
	$category_id = Specific::Filter($_POST['category_id']);

	if(!empty($category_id) && is_numeric($category_id) && !empty($post_ids) && is_array($post_ids)){
		$post = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND id NOT IN ('.implode(',', $post_ids).') AND status = "approved" ORDER BY RAND()', $category_id)->fetchArray();
		if(!empty($post)){
			$post_load = Load::Post($post, true);
			$html = $post_load['html'];

			if($post_load['return']){
				$HTMLFormatter = Specific::HTMLFormatter($html, true);
				if($HTMLFormatter['status'] == true){
					$deliver = array(
						'S' => 200,
						'ID' => $post['id'],
						'HT' => $HTMLFormatter['content']
					);
				}
			}
		}
	}
} else if($one == 'save'){
	$save_post = Specific::SavePost($_POST['post_id']);

	if($save_post['return']){
		$deliver = $save_post['data'];
	}
} else if($one == 'delete'){
	$post_id = Specific::Filter($_POST['post_id']);

	if(!empty($post_id) && is_numeric($post_id)){
		$user_id = $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ?', $post_id)->fetchArray(true);
		if(Specific::IsOwner($user_id)){
			if($dba->query('UPDATE '.T_POST.' SET status = "deleted", deleted_at = ? WHERE id = ?', time(), $post_id)->returnStatus()){
				$_SESSION['post_deleted'] = $post_id;
				$deliver = array(
					'S' => 200,
					'LK' => Specific::Url("?{$TEMP['#p_show_alert']}=deleted_post")
				);
			}
		}
	}
} else if($one == 'report'){
	$error = false;
	$reported_id = Specific::Filter($_POST['reported_id']);
	$type = Specific::Filter($_POST['type']);
	$place = Specific::Filter($_POST['place']);
	$description = Specific::Filter($_POST['description']);

	if(!empty($reported_id) && is_numeric($reported_id) && in_array($place, array('post', 'comment', 'reply'))){
		if(strlen($description) > 500){
			$error = true;
		}
		if($place == 'post'){
			if(!in_array($type, array('r_spam', 'r_none', 'rp_writing', 'rp_thumbnail', 'rp_copyright'))){
				$error = true;
			}
			$post = $dba->query('SELECT *, COUNT(*) as count FROM '.T_POST.' WHERE id = ? AND status = "approved"', $reported_id)->fetchArray();
			if($post['count'] == 0){
				$error = true;
			}

			if(Specific::IsOwner($post['user_id'])){
				$error = true;
			}
		} else if(in_array($place, array('comment', 'reply'))){
			if(!in_array($type, array('r_spam', 'r_none', 'rc_offensive', 'rc_abusive', 'rc_disagree', 'rc_marketing'))){
				$error = true;
			}
			$t_query = T_COMMENTS;
			if($place == 'reply'){
				$t_query = T_REPLY;
			}
			$comment = $dba->query('SELECT *, COUNT(*) as count FROM '.$t_query.' WHERE id = ?', $reported_id)->fetchArray();
			if($comment['count'] == 0){
				$error = true;
			}

			if(Specific::IsOwner($comment['user_id'])){
				$error = true;
			}
		}

		if($error == false){
			if($dba->query('SELECT COUNT(*) FROM '.T_REPORT.' WHERE user_id = ? AND reported_id = ? AND place = ?', $TEMP['#user']['id'], $reported_id, $place)->fetchArray(true) == 0){
				if(empty($description)){
					$description = NULL;
				}
				if($dba->query('INSERT INTO '.T_REPORT.' (user_id, reported_id, type, place, description, created_at) VALUES (?, ?, ?, ?, ?, ?)', $TEMP['#user']['id'], $reported_id, $type, $place, $description, time())){
					$deliver['S'] = 200;
				}
			} else {
				$deliver = array(
					'S' => 400,
					'E' => "*{$TEMP['#word']['have_already_reported_post']}"
				);
			}
		}
	}
} else if($one == 'load-comments'){
	$post_id = Specific::Filter($_POST['post_id']);
	$sort_by = Specific::Filter($_POST['sort_by']);
	$comment_ids = Specific::Filter($_POST['comment_ids']);
	$comment_ids = html_entity_decode($comment_ids);
	$comment_ids = json_decode($comment_ids);

	if(!empty($post_id) && is_numeric($post_id) && !empty($comment_ids) && in_array($sort_by, array('recent', 'oldest', 'featured', 'answered'))){
		$comments = Specific::Comments($post_id, $sort_by, $comment_ids);
		if($comments['return']){
			$deliver = array(
				'S' => 200,
				'HT' => $comments['html']
			);
		}
	}
} else if($one == 'load-replies'){
	$comment_id = Specific::Filter($_POST['comment_id']);
	$sort_by = Specific::Filter($_POST['sort_by']);
	$reply_ids = Specific::Filter($_POST['reply_ids']);
	$reply_ids = html_entity_decode($reply_ids);
	$reply_ids = json_decode($reply_ids);

	if(!empty($comment_id) && is_numeric($comment_id) && !empty($reply_ids) && in_array($sort_by, array('recent', 'oldest', 'featured', 'answered'))){
		$replies = Specific::Replies($comment_id, $sort_by, $reply_ids);

		if($replies['return']){
			$deliver = array(
				'S' => 200,
				'HT' => $replies['html']
			);
		}
	}
} else if($one == 'sort-comments'){
	$post_id = Specific::Filter($_POST['post_id']);
	$sort_by = Specific::Filter($_POST['sort_by']);

	if(!empty($post_id) && is_numeric($post_id) && in_array($sort_by, array('recent', 'oldest', 'featured', 'answered'))){
		$html = '';
		$comments = Specific::Comments($post_id, $sort_by);
		if($comments['return']){
			$html .= $comments['html'];
		}

		if(!empty($html)){
			$deliver = array(
				'S' => 200,
				'HT' => $html
			);
		}
	}
}
?>