<?php
$ids = array();
$empty = array();
$error = array();


$post_id = Specific::Filter($_POST['post_id']);
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

if(!empty($post_id)){
	$post = $dba->query('SELECT slug, thumbnail, COUNT(*) as count FROM '.T_POST.' WHERE id = ? AND user_id = ?', $post_id, $TEMP['#user']['id'])->fetchArray();

	if($post['count'] > 0){
		if(empty($empty)){
			if(!in_array($category, $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false))){
				$error[] = array(
					'EL' => '#category',
					'TX' => "*{$TEMP['#word']['oops_error_has_occurred']}"
				);
			}
			foreach ($entries as $key => $entry) {
				$ids[] = (int)end($entry);

				if(!((bool)$entry[3])){
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
				if(!empty($thumbnail) && !strpos($thumbnail, $TEMP['#site_url'])){
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
					if($thumbnail['return']){
						unlink("uploads/posts/{$post['thumbnail']}-s.jpeg");
						unlink("uploads/posts/{$post['thumbnail']}-b.jpeg");
					}
				} else {
					$thumbnail['return'] = true;
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

				if($thumbnail['return']){
					$slug = $post['slug'];
					$updated_at = $created_at = time();

					if($dba->query('UPDATE '.T_POST.' SET category_id = ?, title = ?, description = ?, thumbnail = ?, post_sources = ?, thumb_sources = ?, type = ?, updated_at = ? WHERE id = ?', $category, $title, $description, $thumbnail['image'], $post_sources, $thumb_sources, $type, $updated_at, $post_id)->returnStatus()){


						$entries_ids = $dba->query('SELECT id FROM '.T_ENTRY.' WHERE post_id = ?', $post_id)->fetchAll(false);

						$del_entries = array_diff($entries_ids, $ids);

						$arr_trues = array();

						foreach ($entries as $key => $entry) {
							$entry_id = (int)end($entry);
							$entry_exists = $dba->query('SELECT eorder, frame FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $entry_id, $post_id)->fetchArray();

							$entry_title = NULL;
							if(!empty($entry[1])){
								$entry_title = $entry[1];
							}
							$entry_source = NULL;
							if(!in_array($entry[0], array('image', 'embed'))){
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
									if(!strpos($entry[2], $entry_exists['frame'])){
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
											$content_frame = $content_frame['image_ext'];
										} else {
											$content_frame = Specific::UploadThumbnail(array(
												'media' => $entry[2],
												'post_id' => $post_id,
												'eorder' => $key,
												'folder' => 'entries'
											));
											$content_frame = $content_frame['image_ext'];
										}

										if(!empty($entry_id) && in_array($entry_id, $entries_ids)){
											if($content_frame['return']){
												$entry_frame = $dba->query('SELECT frame FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $entry_id, $post_id)->fetchArray(true);
												unlink("uploads/entries/{$entry_frame}");
												if($dba->query('UPDATE '.T_ENTRY.' SET frame = ? WHERE id = ?', $content_frame, $entry_id)->returnStatus()){
													$arr_trues[] = true;
												} else {
													$arr_trues[] = false;
												}
											}
										} else {
											if($dba->query('INSERT INTO '.T_ENTRY.' (post_id, type, title, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', $post_id, $entry[0], $entry_title, $content_frame, $entry_source, $key, $created_at)->returnStatus()){
												$arr_trues[] = true;
											} else {
												$arr_trues[] = false;
											}
										}
									}
								} else {
									if($entry[0] == 'embed'){
										$content_frame = $entry[2];
									} else if(empty($entry_id)){
										if($entry[0] == 'tweet' || $entry[0] == 'soundcloud'){
											if($entry[0] == 'tweet'){
												$api = 'https://api.twitter.com/1/statuses/oembed.json?omit_script1&url=';
											} else if($entry[0] == 'soundcloud'){
												$api = 'https://soundcloud.com/oembed?format=json&url=';
											}
											$json = Specific::getContentUrl("{$api}{$entry[2]}");
											$json = json_decode($json, true);

											if(!isset($json['error']) && !isset($json['errors'])){
												if(!empty($json)){
													$content_frame = $json['html'];
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

										if($dba->query('INSERT INTO '.T_ENTRY.' (post_id, type, title, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', $post_id, $entry[0], $entry_title, $content_frame, $entry_source, $key, $created_at)->returnStatus()){
											$arr_trues[] = true;
										} else {
											$arr_trues[] = false;
										}
									}
								}
							}

							if(in_array($entry[0], array('text', 'embed'))){
								if(!empty($entry_id) && in_array($entry_id, $entries_ids)){
									if($dba->query('UPDATE '.T_ENTRY.' SET title = ?, body = ?, frame = ?, esource = ?, eorder = ?, updated_at = ? WHERE id = ?', $entry_title, $content_text, $content_frame, $entry_source, $key, $updated_at, $entry_id)->returnStatus()){
										$arr_trues[] = true;
									} else {
										$arr_trues[] = false;
									}
								} else {
									if($dba->query('INSERT INTO '.T_ENTRY.' (post_id, type, title, body, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $post_id, $entry[0], $entry_title, $content_text, $content_frame, $entry_source, $key, $created_at)->returnStatus()){
										$arr_trues[] = true;
									} else {
										$arr_trues[] = false;
									}
								}
							} else {
								if(!empty($entry_id) && in_array($entry_id, $entries_ids)){
									if($dba->query('UPDATE '.T_ENTRY.' SET title = ?, esource = ?, eorder = ?, updated_at = ? WHERE id = ?', $entry_title, $entry_source, $key, $updated_at, $entry_id)->returnStatus()){
										if($entry[0] == 'image' && $key != $entry_exists['eorder']){
											$ext_frame = "uploads/entries/";
											$old_frame = $entry_exists['frame'];
											$new_frame = str_replace("{$post_id}-{$entry_exists['eorder']}-", "{$post_id}-{$key}-", $old_frame);
											if($dba->query('UPDATE '.T_ENTRY.' SET frame = ? WHERE id = ?', $new_frame, $entry_id)->returnStatus()){
												rename("{$ext_frame}{$old_frame}", "{$ext_frame}{$new_frame}");
											}
										}
										$arr_trues[] = true;
									} else {
										$arr_trues[] = false;
									}
								}
							}
						}

						if(!empty($del_entries)){
							$del_entries = array_values($del_entries);
							foreach ($del_entries as $delid) {
								$entry = $dba->query('SELECT type, frame FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $delid, $post_id)->fetchArray();
								$dba->query('DELETE FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $delid, $post_id);
								if($entry['type'] == 'image'){
									unlink("uploads/entries/{$entry['frame']}");
								}
							}
						}

						$tags_names = $dba->query('SELECT name FROM '.T_LABEL.' l WHERE (SELECT label_id FROM '.T_TAG.' WHERE post_id = ? AND label_id = l.id) = id', $post_id)->fetchAll(false);


						$del_tags = array_diff($tags_names, $tags);
						$add_tags = array_diff($tags, $tags_names);

						if(!empty($add_tags)){
							foreach ($add_tags as $key => $tag) {
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
						}

						if(!empty($del_tags)){
							foreach ($del_tags as $tag) {
								$dba->query('DELETE FROM '.T_TAG.' WHERE post_id = ? AND (SELECT id FROM '.T_LABEL.' WHERE name = ?) = label_id', $post_id, $tag);
							}
						}

						if(!in_array(false, $arr_trues)){
							$deliver = array(
								'S' => 200,
								'LK' => Specific::Url($slug)
							);
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
}
?>