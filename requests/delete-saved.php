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

$post_id = Functions::Filter($_POST['post_id']);

if(!empty($post_id) && is_numeric($post_id)){
	if($dba->query('DELETE FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post_id)->returnStatus()){
		$deliver['S'] = 200;
	}
}
?>