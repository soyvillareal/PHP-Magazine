<?php
if($one == 'subscribe'){
	$catrues = array();
	$types = array('all', 'personalized');
	$frequencies = array('now', 'daily', 'weekly');
	$populars = array('off', 'on');
	$categories = $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false);

	$email = Specific::Filter($_POST['email']);
	$type = Specific::Filter($_POST['type']);
	$frequency = Specific::Filter($_POST['frequency']);
	$popular = Specific::Filter($_POST['popular']);
	$category = Specific::Filter($_POST['category']);
	$category = html_entity_decode($category);
	$category = json_decode($category, true);

	if(!empty($email) && !empty($type) && !empty($frequency) && !empty($popular)){
		if(!empty($category)){
			foreach ($category as $cat) {
				if(!in_array($cat, $categories)){
					$catrues[] = false;
				}
			}
		}
		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE email = ?', $email)->fetchArray(true) == 0){
				if(in_array($type, $types) && in_array($frequency, $frequencies) && in_array($popular, $populars) && !in_array(false, $catrues)){
					$slug = Specific::RandomKey(12, 16);
					if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE slug = ?', $slug)->fetchArray(true) > 0){
						$slug = Specific::RandomKey(12, 16);
					}
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
			} else {
				$deliver = array(
					'S' => 400,
					'E' => $TEMP['#word']['this_email_is_already_subscribed'],
					'EX' => 1
				);
			}
		}
	}
}
?>