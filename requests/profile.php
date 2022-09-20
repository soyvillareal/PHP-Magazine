<?php
if($one == 'follow'){
	$user_id = Specific::Filter($_POST['user_id']);

	if(!empty($user_id) && is_numeric($user_id)){
		if($TEMP['#user']['id'] != $user_id && $dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ?', $user_id)->fetchArray(true) > 0){
			if($dba->query('SELECT COUNT(*) FROM '.T_FOLLOWER.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $user_id)->fetchArray(true) > 0){
				if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE (SELECT id FROM '.T_FOLLOWER.' WHERE user_id = ? AND profile_id = ?) = notified_id', $TEMP['#user']['id'], $user_id)->returnStatus()){
					if($dba->query('DELETE FROM '.T_FOLLOWER.' WHERE user_id = ? AND profile_id = ?', $TEMP['#user']['id'], $user_id)->returnStatus()){
						$deliver = array(
							'S' => 200,
							'T' => 'follow',
							'L' => $TEMP['#word']['follow']
						);
					}
				}
			} else {
				$insert_id = $dba->query('INSERT INTO '.T_FOLLOWER.' (user_id, profile_id, created_at) VALUES (?, ?, ?)', $TEMP['#user']['id'], $user_id, time())->insertId();
				if($insert_id){
					$deliver = array(
						'S' => 200,
						'T' => 'following',
						'L' => $TEMP['#word']['following']
					);
					Specific::SetNotify(array(
						'user_id' => $user_id,
						'notified_id' => $insert_id,
						'type' => 'followers',
					));
				}
			}
		}
	}
} else if($one == 'load-posts'){
	$profile_ids = Specific::Filter($_POST['profile_ids']);
	$profile_ids = html_entity_decode($profile_ids);
	$profile_ids = json_decode($profile_ids);
	$user_id = Specific::Filter($_POST['user_id']);

	if(!empty($user_id) && is_numeric($user_id)){
		$profile_load = Load::Profile($user_id, $profile_ids);

		if($profile_load['return']){
			$deliver = array(
				'S' => 200,
				'HT' => $profile_load['html'],
				'IDS' => $profile_load['profile_ids']
			);
		}
	}
}
?>