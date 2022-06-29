<?php
$empty = array();
$error = array();

$title = Specific::Filter($_POST['title']);
$category = Specific::Filter($_POST['category']);
$type = Specific::Filter($_POST['type']);
$description = Specific::Filter($_POST['description']);
$entries = Specific::Filter($_POST['entries']);
$entries = html_entity_decode($entries);
$entries = json_decode($entries, true);


$post_sources = Specific::Filter($_POST['post_sources']);
$post_sources = html_entity_decode($post_sources);
$post_sources = json_decode($post_sources, true);

$thumb_sources = Specific::Filter($_POST['thumb_sources']);
$thumb_sources = html_entity_decode($thumb_sources);
$thumb_sources = json_decode($thumb_sources, true);

$thumbnail = !empty($_FILES['thumbnail']) ? $_FILES['thumbnail'] : Specific::Filter($_POST['thumbnail']);
$tags = Specific::Filter($_POST['tags']);
$action = Specific::Filter($_POST['action']);

if(empty($title)){
	$empty[] = array(
		'EL' => '#title',
		'TX' => "*{$TEMP['#word']['this_field_is_empty']}"
	);
}
if(empty($description)){
	$empty[] = array(
		'EL' => '#description',
		'TX' => "*{$TEMP['#word']['this_field_is_empty']}"
	);
} 
if(count($entries) == 0){
	$empty[] = array(
		'EL' => '.btn_aentry',
		'SW' => 0,
		'TX' => "*{$TEMP['#word']['you_create_least_entry']}"
	);
} else {
	foreach ($entries as $key => $entry) {
		if(empty($entries[$key][2])){
			$empty[] = array(
				'EL' => $key,
				'CS' => $entry[0] == 'text' ? '.simditor' : ($entry[0] == 'image' ? '.item-placeholder' : '.item-input'),
				'TX' => "*{$TEMP['#word']['this_field_is_empty']}"
			);
		}
	}
}
if(empty($thumbnail)){
	$empty[] = array(
		'EL' => '#post-right .item-placeholder',
		'TX' => "*{$TEMP['#word']['this_field_is_empty']}"
	);
}
if(empty($tags)){
	$empty[] = array(
		'EL' => '#content-tags',
		'TX' => "*{$TEMP['#word']['this_field_is_empty']}"
	);
}

if(!empty($post_sources)){
	$empty_positions = array();
	foreach($post_sources as $key => $source) {
		if(empty($source['name']) && !empty($source['source'])){
			$empty_positions[] = $key;
		}
		if(empty($source['name']) && empty($source['source'])){
			unset($post_sources[$key]);
		}
	}
	if(!empty($empty_positions)){
		$empty[] = array(
			'EL' => '.post_sources',
			'PS' => $empty_positions,
			'CT' => 0,
			'FD' => 1,
			'SW' => 0,
			'TX' => "*{$TEMP['#word']['some_fields_empty']}"
		);
	}
}
	
if(!empty($thumb_sources)){
	$empty_positions = array();
	foreach($thumb_sources as $key => $source) {
		if(empty($source['name']) && !empty($source['source'])){
			$empty_positions[] = $key;
		}
		if(empty($source['name']) && empty($source['source'])){
			unset($thumb_sources[$key]);
		}
	}
	if(!empty($empty_positions)){
		$empty[] = array(
			'EL' => '.post_sources',
			'PS' => $empty_positions,
			'CT' => 1,
			'FD' => 1,
			'SW' => 0,
			'TX' => "*{$TEMP['#word']['some_fields_empty']}"
		);
	}
}

if(in_array($action, array('post', 'eraser'))){
	if(empty($empty)){
		if(!in_array($category, $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false))){
			$error[] = array(
				'EL' => '#category',
				'TX' => "*{$TEMP['#word']['oops_error_has_occurred']}"
			);
		}
		foreach ($entries as $key => $entry) {
			if($entry[0] == 'video'){
				if(preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/", $entry[2]) == false && preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/", $entry[2]) == false && preg_match("/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/", $entry[2]) == false){
					$error[] = array(
						'EL' => $key,
						'CS' => '.item-input',
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			}

			if($entry[0] == 'facebookpost'){
				if(preg_match("/(?:(?:http|https):\/\/)?(?:www\.)?(?:facebook\.com)\/(\d+|[A-Za-z0-9\.]+)\/?/", $entry[2]) == false){
					$error[] = array(
						'EL' => $key,
						'CS' => '.item-input',
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			}

			if($entry[0] == 'instagrampost'){
				if(preg_match("/(?:(?:http|https):\/\/)?(?:www\.)?(?:instagram\.com|instagr\.am)\/(?:p|tv|reel)\/([A-Za-z0-9-_\.]+)/", $entry[2]) == false){
					$error[] = array(
						'EL' => $key,
						'CS' => '.item-input',
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			}

			if($entry[0] == 'tweet'){
				if(preg_match('/^https?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/', $entry[2]) == false){
					$error[] = array(
						'EL' => $key,
						'CS' => '.item-input',
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			}

			if($entry[0] == 'tiktok'){
				$tiktok_url = preg_match("/(?:http(?:s)?:\/\/)?(?:(?:www)\.(?:tiktok\.com)(?:\/)(?!foryou)(@[a-zA-z0-9]+)(?:\/)(?:video)(?:\/)([\d]+)|(?:m)\.(?:tiktok\.com)(?:\/)(?!foryou)(?:v)(?:\/)?(?=([\d]+)\.html))/", $entry[2], $tk_video_url);
				$tiktok_param = preg_match("/#\/(?P<username>@[a-zA-z0-9]*|.*)(?:\/)?(?:v|video)(?:\/)?(?P<id>[\d]+)/", $entry[2], $tk_video_param);

				if($tiktok_param == true || ($tiktok_param == true && filter_var($entry[2], FILTER_VALIDATE_URL))){
					$tiktok_url = true;
					$entries[$key][2] = "https://www.tiktok.com/{$tk_video_param['username']}/video/{$tk_video_param['id']}";
				}

				if($tiktok_url == false){
					$error[] = array(
						'EL' => $key,
						'CS' => '.item-input',
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			}

			if($entry[0] == 'soundcloud'){
				if(preg_match('/^(?:(https?):\/\/)?(?:(?:www|m)\.)?(soundcloud\.com|snd\.sc)\/[a-z0-9](?!.*?(-|_){2})[\w-]{1,23}[a-z0-9](?:\/.+)?$/', $entry[2]) == false){
					$error[] = array(
						'EL' => $key,
						'CS' => '.item-input',
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			}
		}

		if(!in_array($type, array('normal', 'video'))){
			$error[] = array(
				'EL' => '#type',
				'TX' => "*{$TEMP['#word']['oops_error_has_occurred']}"
			);
		}

		if(!empty($post_sources)){
			$error_positions = array();
			if(count($post_sources) < $TEMP['#settings']['number_of_fonts']){
				foreach($post_sources as $key => $source) {
					if(!empty($source['name']) && !empty($source['source'])){
						if(!filter_var($source['source'], FILTER_VALIDATE_URL)){
							$error_positions[] = $key;
						}
					}
				}
				if(!empty($error_positions)){
					$error[] = array(
						'EL' => '.post_sources',
						'PS' => $error_positions,
						'CT' => 0,
						'FD' => 2,
						'SW' => 0,
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			} else {
				$error[] = array(
					'EL' => '.post_sources',
					'CT' => 0,
					'SW' => 0,
					'TX' => "*{$TEMP['#word']['oops_error_has_occurred']}"
				);
			}	
		}

		if(!empty($thumb_sources)){
			$error_positions = array();
			if(count($thumb_sources) < $TEMP['#settings']['number_of_fonts']){
				foreach($thumb_sources as $key => $source) {
					if(!empty($source['name']) && !empty($source['source'])){
						if(!filter_var($source['source'], FILTER_VALIDATE_URL)){
							$error_positions[] = $key;
						}
					}
				}
				if(!empty($error_positions)){
					$error[] = array(
						'EL' => '.post_sources',
						'PS' => $error_positions,
						'CT' => 1,
						'FD' => 2,
						'SW' => 0,
						'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
					);
				}
			} else {
				$error[] = array(
					'EL' => '.post_sources',
					'CT' => 1,
					'SW' => 0,
					'TX' => "*{$TEMP['#word']['oops_error_has_occurred']}"
				);
			}	
		}

		$tags = explode(',', $tags);
		if(count($tags) > $TEMP['#settings']['number_labels']){
			$error[] = array(
				'EL' => '#content-tags',
				'TX' => "*{$TEMP['#word']['oops_error_has_occurred']}"
			);
		}

		if(empty($error)){
			if(!empty($_FILES['thumbnail'])){
				$thumbnail = Specific::UploadImage(array(
					'name' => $_FILES['thumbnail']['name'],
					'tmp_name' => $_FILES['thumbnail']['tmp_name'],
					'type' => $_FILES['thumbnail']['type'],
					'folder' => 'posts',
				));
			} else {
				$thumbnail = Specific::UploadThumbnail(array(
					'media' => $thumbnail,
					'folder' => 'posts'
				));
			}
			if(!empty($post_sources)){
				$post_sources = json_encode($post_sources);
			} else {
				$post_sources = NULL;
			}
			if(!empty($thumb_sources)){
				$thumb_sources = json_encode($thumb_sources);
			} else {
				$thumb_sources = NULL;
			}

			$status = 'approved';
			if($TEMP['#settings']['approve_posts'] == 'on' && !Specific::Admin()){
				$status = 'pending';
			}

			if($thumbnail['return']){
				$slug = Specific::CreateSlug($title);
				$published_at = $created_at = time();
				if($action == 'eraser'){
					$published_at = 0;
				}
				$post_id = $dba->query('INSERT INTO '.T_POST.' (user_id, category_id, title, description, slug, thumbnail, post_sources, thumb_sources, type, status, published_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $TEMP['#user']['id'], $category, $title, $description, $slug, $thumbnail['image'], $post_sources, $thumb_sources, $type, $status, $published_at, $created_at)->insertId();
				if($post_id){
					$arr_trues = array();
					foreach ($entries as $key => $entry) {
						$entry_title = NULL;
						if(!empty($entry[1])){
							$entry_title = $entry[1];
						}
						$entry_source = NULL;
						if($entry[0] != 'image'){
							if(isset($entry[3])){
								$entry_source = $entry[3];
							}
						} else {
							$entry_source = $entry[4];
						}
							
						$content_text = NULL;
						$content_frame = NULL;
						if($entry[0] == 'text'){
							$content_text = $entry[2];
						} else {
							if($entry[0] == 'image'){
								if(!empty($_FILES['thumbnail_'.$key])){
									$thumbnail = $_FILES['thumbnail_'.$key];
									$content_frame = Specific::UploadImage(array(
										'name' => $thumbnail['name'],
										'tmp_name' => $thumbnail['tmp_name'],
										'type' => $thumbnail['type'],
										'post_id' => $post_id,
										'eorder' => $key,
										'folder' => 'entries'
									));
								} else {
									$content_frame = Specific::UploadThumbnail(array(
										'media' => $entry[2],
										'post_id' => $post_id,
										'eorder' => $key,
										'folder' => 'entries'
									));
								}
								$content_frame = $content_frame['image_ext'];
							} else if($entry[0] == 'tweet' || $entry[0] == 'soundcloud' || $entry[0] == 'tiktok'){
								if($entry[0] == 'tweet'){
									$api = 'https://api.twitter.com/1/statuses/oembed.json?omit_script1&url=';
								} else if($entry[0] == 'soundcloud'){
									$api = 'https://soundcloud.com/oembed?format=json&url=';
								} else if($entry[0] == 'tiktok'){
									$api = 'https://www.tiktok.com/oembed?format=json&url=';
								}
								$json = Specific::getContentUrl("{$api}{$entry[2]}");
								$json = json_decode($json, true);

								if(!isset($json['error']) && !isset($json['errors']) && !isset($json['status_msg'])){
									if(!empty($json)){
										$content_frame = $json['html'];
										if($entry[0] == 'tiktok'){
											$content_frame = preg_replace('/(\s+)?(?:<script [^>]*><\/script>)/', '', $content_frame);
										}
										$arr_trues[] = true;
									} else {
										$arr_trues[] = false;
									}
								} else {
									$arr_trues[] = false;
								}
							} else {
								$content_frame = $entry[2];
							}
						}
							
						if($dba->query('INSERT INTO '.T_ENTRY.' (post_id, type, title, body, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $post_id, $entry[0], $entry_title, $content_text, $content_frame, $entry_source, $key, $created_at)->returnStatus()){
							$arr_trues[] = true;
						} else {
							$arr_trues[] = false;
						}
					}

					foreach ($tags as $key => $tag) {
						$label = $dba->query('SELECT id, COUNT(*) as count FROM '.T_LABEL.' WHERE name = ?', $tag)->fetchArray();
						if($label['count'] > 0){
							$label_id = $label['id'];
						} else {
							$label_id = $dba->query('INSERT INTO '.T_LABEL.' (name, slug, created_at) VALUES (?, ?, ?)', $tag, Specific::CreateSlug($tag), time())->insertId();
							if($label_id){
								$arr_trues[] = true;
							} else {
								$arr_trues[] = false;
							}
						}
						if($dba->query('INSERT INTO '.T_TAG.' (post_id, label_id, created_at) VALUES (?, ?, ?)', $post_id, $label_id, time())->returnStatus()){
							$arr_trues[] = true;
						} else {
							$arr_trues[] = false;
						}
					}

					if(!in_array(false, $arr_trues)){
						$deliver = array(
							'S' => 200,
							'LK' => Specific::Url($slug)
						);
					} else {
						$dba->query('DELETE FROM '.T_POST.' WHERE id = ?', $post_id);
					}
				}
			}
		} else {
			$deliver = array(
				'S' => 400,
				'E' => $error
			);
		}
	} else {
		$deliver = array(
			'S' => 400,
			'E' => $empty
		);
	}
}
?>