<?php
if($one == 'load'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);
	$category_id = Specific::Filter($_POST['category_id']);

	if(!empty($category_id) && is_numeric($category_id) && !empty($post_ids) && is_array($post_ids)){
		$post = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND id NOT IN ('.implode(',', $post_ids).') AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY RAND()', $category_id)->fetchArray();
		if(!empty($post)){
			$post_load = Load::Post($post, true);
			$html = $post_load['html'];

			if($post_load['return']){
				$deliver = array(
					'S' => 200,
					'ID' => $post['id'],
					'HT' => Specific::HTMLFormatter($html)
				);
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
		$post = $dba->query('SELECT user_id, COUNT(*) as count FROM '.T_POST.' WHERE id = ? AND status <> "deleted"', $post_id)->fetchArray();
		if($post['count'] > 0 && (Specific::IsOwner($post['user_id']) || $TEMP['#moderator'] == true)){
			if($dba->query('UPDATE '.T_POST.' SET status = "deleted", deleted_at = ? WHERE id = ?', time(), $post_id)->returnStatus()){
				$deliver = array(
					'S' => 200,
					'LK' => Specific::Url("?{$RUTE['#p_show_alert']}={$RUTE['#p_deleted_post']}")
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