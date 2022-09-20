<?php
$post_id = Specific::Filter($_POST['post_id']);

if(!empty($post_id) && is_numeric($post_id)){
	if($dba->query('DELETE FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post_id)->returnStatus()){
		$deliver['S'] = 200;
	}
}
?>