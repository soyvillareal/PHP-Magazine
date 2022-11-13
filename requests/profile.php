<?php
if($one == 'follow'){
	$user_id = Functions::Filter($_POST['user_id']);

	if(!empty($user_id) && is_numeric($user_id) && !in_array($user_id, Functions::BlockedUsers(false))){
		if(!Functions::IsOwner($user_id) && $dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND status = "active"', $user_id)->fetchArray(true) > 0){
			$updated = false;
			if($dba->query('SELECT COUNT(*) FROM '.T_FOLLOWER.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $user_id)->fetchArray(true) > 0){
				if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_FOLLOWER.' WHERE user_id = ? AND profile_id = ?) = notified_id', $TEMP['#user']['id'], $user_id)->returnStatus()){
					if($dba->query('DELETE FROM '.T_FOLLOWER.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $user_id)->returnStatus()){
						$updated = true;

						$deliver['S'] = 200;
						$deliver['T'] = 'follow';
						$deliver['L'] = $TEMP['#word']['follow'];
					}
				}
			} else {
				$insert_id = $dba->query('INSERT INTO '.T_FOLLOWER.' (user_id, profile_id, created_at) VALUES (?, ?, ?)', $TEMP['#user']['id'], $user_id, time())->insertId();
				if($insert_id){
					$updated = true;

					$deliver['S'] = 200;
					$deliver['T'] = 'following';
					$deliver['L'] = $TEMP['#word']['following'];

					Functions::SetNotify(array(
						'user_id' => $user_id,
						'notified_id' => $insert_id,
						'type' => 'followers',
					));
				}
			}

			if($updated){
				$user = Functions::Data($user_id);

				if($user['shows']['followers'] == 'on'){
					$followers = Functions::Followers($user_id);
					
					if($followers['number'] > 0){
						$deliver['TX'] = $followers['text'];
					}
				}
			}

		}
	}
} else if($one == 'load-posts'){
	$profile_ids = Functions::Filter($_POST['profile_ids']);
	$profile_ids = html_entity_decode($profile_ids);
	$profile_ids = json_decode($profile_ids);
	$user_id = Functions::Filter($_POST['user_id']);

	if(!empty($user_id) && is_numeric($user_id)){
		$profile_load = Loads::Profile($user_id, $profile_ids);

		if($profile_load['return']){
			$widget = Functions::GetWidget('horizposts');
			if($widget['return']){
				$profile_load['html'] .= $widget['html'];
			}
			$deliver = array(
				'S' => 200,
				'HT' => $profile_load['html'],
				'IDS' => $profile_load['profile_ids']
			);
		}
	}
} else if($one == 'block'){
	$profile_id = Functions::Filter($_POST['profile_id']);

	if(!empty($profile_id) && is_numeric($profile_id) && $TEMP['#settings']['blocked_users'] == 'on'){
		$role = $dba->query('SELECT role FROM '.T_USER.' WHERE id = ?', $profile_id)->fetchArray(true);
		if(!Functions::IsOwner($profile_id) && in_array($role, array('publisher', 'viewer'))){
			if($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND status = "active"', $profile_id)->fetchArray(true) > 0){
				if($dba->query('SELECT COUNT(*) FROM '.T_BLOCK.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $profile_id)->fetchArray(true) == 0){
					if($dba->query('INSERT INTO '.T_BLOCK.' (user_id, profile_id, created_at) VALUES (?, ?, ?)', $TEMP['#user']['id'], $profile_id, time())->returnStatus()){
						$deliver['S'] = 200;
					}
				} else {
					if($dba->query('DELETE FROM '.T_BLOCK.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $profile_id)->returnStatus()){
						$deliver['S'] = 200;
					}
				}
			}
		}
	}
}
?>