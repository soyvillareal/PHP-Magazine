<?php
if($one == 'subscribe'){
	$catrues = array();
	$categories = $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false);

	$email = Specific::Filter($_POST['email']);
	$type = Specific::Filter($_POST['type']);
	$frequency = Specific::Filter($_POST['frequency']);
	$popular = Specific::Filter($_POST['popular']);
	$category = Specific::Filter($_POST['category']);
	$category = html_entity_decode($category);
	$category = json_decode($category, true);
	
	if(!empty($type) && !empty($frequency)){
		if(!empty($email)){
			if(empty($popular)){
				$popular = 'off';
			}
			if(!empty($category)){
				foreach ($category as $cat) {
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
								if($dba->query('INSERT INTO '.T_NEWSLETTER.' (slug, email, frequency, popular, categories, created_at) VALUES (?, ?, ?, ?, ?, ?)', $slug, $email, $frequency, $popular, implode(',', $category), time())->returnStatus()){
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
	$type = Specific::Filter($_POST['type']);
	$frequency = Specific::Filter($_POST['frequency']);
	$popular = Specific::Filter($_POST['popular']);
	$popular = json_decode($popular);
	$category = Specific::Filter($_POST['category']);
	$category = html_entity_decode($category);
	$category = json_decode($category, true);
	$populars = array(false => 'off', true => 'on');
	
	if(!empty($type) && !empty($frequency) && in_array($popular, array_keys($populars))){
		if(!empty($category)){
			foreach ($category as $cat) {
				if(!in_array($cat, $categories)){
					$catrues[] = false;
				}
			}
		}
		if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE email = ?', $TEMP['#user']['email'])->fetchArray(true) > 0){
			if(in_array($type, array('all', 'personalized')) && in_array($frequency, array('now', 'daily', 'weekly')) && !in_array(false, $catrues)){
						$categories = !empty($category) ? implode(',', $category) : NULL;
				$popular = $populars[$popular];
				if($type == 'all'){
					$frequency = $type;
					$popular = 'off';
					$categories = NULL;
				}
				if($dba->query('UPDATE '.T_NEWSLETTER.' SET frequency = ?, popular = ?, categories = ?, updated_at = ? WHERE email = ?', $frequency, $popular, $categories, time(), $TEMP['#user']['email'])->returnStatus()){
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