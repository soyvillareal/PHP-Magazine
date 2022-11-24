<?php

// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

$error = false;
$reported_id = Functions::Filter($_POST['reported_id']);
$type = Functions::Filter($_POST['type']);
$place = Functions::Filter($_POST['place']);
$description = Functions::Filter($_POST['description']);

if(!empty($reported_id) && is_numeric($reported_id) && in_array($place, array('user', 'post', 'comment', 'reply'))){
	if(mb_strlen(strip_tags($description), "UTF8") > $TEMP['#settings']['max_words_report']){
		$error = true;
	}
	if($place == 'user'){
		if(!in_array($type, array('r_spam', 'r_none', 'ru_hate', 'ru_picture', 'ru_copyright'))){
			$error = true;
		}
		if($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND status = "active"', $reported_id)->fetchArray(true) == 0){
			$error = true;
		}
		if(Functions::IsOwner($reported_id)){
			$error = true;
		}

		if($error == false){
			$E = $TEMP['#word']['have_already_reported_user'];
		}
	} else if($place == 'post'){
		if(!in_array($type, array('r_spam', 'r_none', 'rp_writing', 'rp_thumbnail', 'rp_copyright'))){
			$error = true;
		}
		$post = $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', $reported_id)->fetchArray();
		if(empty($post)){
			$error = true;
		}

		if(Functions::IsOwner($post['user_id'])){
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
		$comment = $dba->query('SELECT * FROM '.$t_query.' WHERE id = ?', $reported_id)->fetchArray();
		if(empty($comment)){
			$error = true;
		}

		if(Functions::IsOwner($comment['user_id'])){
			$error = true;
		}

		if($error == false){
			$E = $TEMP['#word']['have_already_reported_comment'];
		}
	}

	if($error == false){
		if($dba->query('SELECT COUNT(*) FROM '.T_REPORT.' WHERE user_id = ? AND reported_id = ? AND place = ? AND status = "unanswered"', $TEMP['#user']['id'], $reported_id, $place)->fetchArray(true) == 0){
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