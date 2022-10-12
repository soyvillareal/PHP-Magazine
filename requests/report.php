<?php
$error = false;
$reported_id = Specific::Filter($_POST['reported_id']);
$type = Specific::Filter($_POST['type']);
$place = Specific::Filter($_POST['place']);
$description = Specific::Filter($_POST['description']);

if(!empty($reported_id) && is_numeric($reported_id) && in_array($place, array('user', 'post', 'comment', 'reply'))){
	if(strlen($description) > 500){
		$error = true;
	}
	if($place == 'user'){
		if(!in_array($type, array('r_spam', 'r_none', 'ru_hate', 'ru_picture', 'ru_copyright'))){
			$error = true;
		}
		$user = $dba->query('SELECT id, COUNT(*) as count FROM '.T_USER.' WHERE id = ? AND status = "active"', $reported_id)->fetchArray();
		if($user['count'] == 0){
			$error = true;
		}
		if(Specific::IsOwner($user['id'])){
			$error = true;
		}

		if($error == false){
			$E = $TEMP['#word']['have_already_reported_user'];
		}
	} else if($place == 'post'){
		if(!in_array($type, array('r_spam', 'r_none', 'rp_writing', 'rp_thumbnail', 'rp_copyright'))){
			$error = true;
		}
		$post = $dba->query('SELECT user_id, COUNT(*) as count FROM '.T_POST.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', $reported_id)->fetchArray();
		if($post['count'] == 0){
			$error = true;
		}

		if(Specific::IsOwner($post['user_id'])){
			$error = true;
		}

		if($error == false){
			$E = $TEMP['#word']['have_already_reported_post'];
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

		if($error == false){
			$E = $TEMP['#word']['have_already_reported_comment'];
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
				'E' => "*{$E}"
			);
		}
	}
}
?>