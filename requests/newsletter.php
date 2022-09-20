<?php
if($one == 'subscribe'){
	$catrues = array();
	$categories = $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false);

	$email = Specific::Filter($_POST['email']);
	$type = Specific::Filter($_POST['type']);
	$frequency = Specific::Filter($_POST['frequency']);
	$popular = Specific::Filter($_POST['popular']);
	$popular = json_decode($popular);
	$cats = Specific::Filter($_POST['cats']);
	$cats = html_entity_decode($cats);
	$cats = json_decode($cats, true);
	$populars = array(
		false => 'off',
		true => 'on'
	);
	
	if(!empty($type) && !empty($frequency) && in_array($popular, array_keys($populars))){
		if(!empty($email)){

			$popular = $populars[$popular];
			
			if(!empty($cats)){
				foreach ($cats as $cat) {
					if(!in_array($cat, $categories)){
						$catrues[] = false;
					}
				}
			}
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
				$newsletter = $dba->query('SELECT status, COUNT(*) as count FROM '.T_NEWSLETTER.' WHERE email = ?', $email)->fetchArray(true);
				if($newsletter['count'] == 0 || ($newsletter['count'] > 0 && $newsletter['status'] == 'disabled')){
					if(in_array($type, array('all', 'personalized')) && in_array($frequency, array('now', 'daily', 'weekly')) && !in_array(false, $catrues)){
						$slug = Specific::RandomKey(12, 16);
						if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE slug = ?', $slug)->fetchArray(true) > 0){
							$slug = Specific::RandomKey(12, 16);
						}
						if($newsletter['count'] == 0 || ($newsletter['count'] > 0 && $dba->query('DELETE FROM '.T_NEWSLETTER.' WHERE email = ?', $email)->returnStatus())){
							if($type == 'all'){
								if($dba->query('INSERT INTO '.T_NEWSLETTER.' (slug, email, frequency, created_at) VALUES (?, ?, ?, ?)', $slug, $email, $type, time())->returnStatus()){
									$deliver = array(
										'S' => 200,
										'M' => $TEMP['#word']['you_have_successfully_subscribed']
									);
								}
							} else {
								$newsletter_id = $dba->query('INSERT INTO '.T_NEWSLETTER.' (slug, email, frequency, popular, created_at) VALUES (?, ?, ?, ?, ?)', $slug, $email, $frequency, $popular, time())->insertId();
								if($newsletter_id){
									foreach ($cats as $cat) {
										$dba->query('INSERT INTO '.T_NEWSCATE.' (newsletter_id, category_id, created_at) VALUES (?, ?, ?)', $newsletter_id, $cat, time());
									}
									$deliver = array(
										'S' => 200,
										'M' => $TEMP['#word']['you_have_successfully_subscribed']
									);
								}
							}
						}
							
					}
				} else {
					$deliver = array(
						'S' => 400,
						'E' => "*{$TEMP['#word']['this_email_is_already_subscribed']}"
					);
				}
			} else {
				$deliver = array(
					'S' => 400,
					'E' => "*{$TEMP['#word']['enter_a_valid_email']}"
				);
			}
		} else {
			$deliver = array(
				'S' => 400,
				'E' => "*{$TEMP['#word']['this_field_is_empty']}"
			);
		}
	}
} else if($one == 'update'){
	$catrues = array();
	$categories = $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false);
	
	$slug = Specific::Filter($_POST['slug']);
	$type = Specific::Filter($_POST['type']);
	$frequency = Specific::Filter($_POST['frequency']);
	$popular = Specific::Filter($_POST['popular']);
	$popular = json_decode($popular);
	$cats = Specific::Filter($_POST['cats']);
	$cats = html_entity_decode($cats);
	$cats = json_decode($cats, true);
	$populars = array(
		false => 'off',
		true => 'on'
	);
	
	if(!empty($type) && !empty($frequency) && in_array($popular, array_keys($populars))){
		if(!empty($cats)){
			foreach ($cats as $cat) {
				if(!in_array($cat, $categories)){
					$catrues[] = false;
				}
			}
		}
		$newsletter = $dba->query('SELECT id, COUNT(*) as count FROM '.T_NEWSLETTER.' WHERE slug = ?', $slug)->fetchArray();

		if($newsletter['count'] > 0){
			if(in_array($type, array('all', 'personalized')) && in_array($frequency, array('now', 'daily', 'weekly')) && !in_array(false, $catrues)){
				
				$popular = $populars[$popular];

				if($type == 'all'){
					$frequency = $type;
					$popular = 'off';
				} else {
					$newscate_ids = $dba->query('SELECT category_id FROM '.T_NEWSCATE.' WHERE newsletter_id = ?', $newsletter['id'])->fetchAll(false);

					$add_cats = array_diff($cats, $newscate_ids);
					$del_cats = array_diff($newscate_ids, $cats);

					if(!empty($add_cats)){
						foreach ($add_cats as $addcat) {
							if($dba->query('SELECT COUNT(*) FROM '.T_CATEGORY.' WHERE id = ? AND status = "enabled"', $addcat)->fetchArray(true) > 0){
								$dba->query('INSERT INTO '.T_NEWSCATE.' (newsletter_id, category_id, created_at) VALUES (?, ?, ?)', $newsletter['id'], $addcat, time());
							}
						}
					}

					if(!empty($del_cats)){
						foreach ($del_cats as $delcat) {
							$dba->query('DELETE FROM '.T_NEWSCATE.' WHERE newsletter_id = ? AND category_id = ?', $newsletter['id'], $delcat);
						}
					}
				}
				if($dba->query('UPDATE '.T_NEWSLETTER.' SET frequency = ?, popular = ?, updated_at = ? WHERE id = ?', $frequency, $popular, time(), $newsletter['id'])->returnStatus()){
					$deliver = array(
						'S' => 200,
						'M' => $TEMP['#word']['newsletter_updated_success']
					);
				}
			}
		}
	}
} else if($one == 'unsubscribe'){
	$slug = Specific::Filter($_POST['slug']);
	$reason = Specific::Filter($_POST['reason']);

	if(!empty($slug) && strlen(strip_tags($reason)) <= 500){
		if(empty($reason)){
			$reason = NULL;
		}
		if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE slug = ?', $slug)->fetchArray(true) > 0){
			if($dba->query('UPDATE '.T_NEWSLETTER.' SET status = "disabled", reason = ? WHERE slug = ?', $reason, $slug)->returnStatus()){
				$deliver = array(
					'S' => 200,
					'M' => $TEMP['#word']['have_been_very_successful']
				);
			}
		}
	}
}
?>