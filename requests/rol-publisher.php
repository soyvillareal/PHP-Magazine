<?php
if ($TEMP['#publisher'] === true) {
    if($one == 'create-post'){
    	$empty = array();
		$error = array();

		$title = Specific::Filter($_POST['title']);
		$category = Specific::Filter($_POST['category']);
		$type = Specific::Filter($_POST['type']);
		$description = Specific::Filter($_POST['description']);
		$entries = Specific::Filter($_POST['entries']);
		$entries = html_entity_decode($entries);
		$entries = json_decode($entries, true);

		$recobo = Specific::Filter($_POST['recobo']);
		$recobo = html_entity_decode($recobo);
		$recobo = json_decode($recobo, true);

		$collaborators = Specific::Filter($_POST['collaborators']);
		$collaborators = html_entity_decode($collaborators);
		$collaborators = json_decode($collaborators, true);

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
						'CS' => $entry[0] == 'text' ? '.simditor' : ($entry[0] == 'image' || $entry[0] == 'carousel' ? '.item-placeholder' : '.item-input'),
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
				if(!empty($_FILES['thumbnail'])){
					if($_FILES['thumbnail']['size'] > $TEMP['#settings']['file_size_limit']){
						$error[] = array(
							'EL' => '#post-right .item-placeholder',
							'TX' => str_replace('{$file_size_limit}', Specific::SizeFormat($TEMP['#settings']['file_size_limit']), "*{$TEMP['#word']['file_too_big_maximum_size']}")
						);
					}
				}
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

					if($entry[0] == 'embed'){
						if(!Specific::ValidateUrl($entry[2])){
							$error[] = array(
								'EL' => $key,
								'CS' => '.item_url',
								'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
							);
						}
					}

					if($entry[0] == 'image'){
						if(!empty($_FILES['thumbnail_'.$key])){
							if($_FILES['thumbnail_'.$key]['size'] > $TEMP['#settings']['file_size_limit']){
								$error[] = array(
									'EL' => $key,
									'CS' => '.item-placeholder',
									'TX' => str_replace('{$file_size_limit}', Specific::SizeFormat($TEMP['#settings']['file_size_limit']), "*{$TEMP['#word']['file_too_big_maximum_size']}")
								);
							}
						} else {
							if(!empty($entry[2])){
								$image_err = false;
								$validate_url = Specific::ValidateUrl($entry[2], true);
								$entries[$key][2] = $entry[2] = $validate_url['url'];
								if(!$validate_url['return']){
									$image_err = true;
									$image_errt = "*{$TEMP['#word']['enter_a_valid_url']}";
								} else if(exif_imagetype($entry[2]) == false){
									$image_err = true;
									$image_errt = "*{$TEMP['#word']['download_could_not_completed']}";
								}
								if($image_err){
									$error[] = array(
										'EL' => $key,
										'CS' => '.item-placeholder',
										'TX' => $image_errt
									);
								}
							}
						}
					}

					if($entry[0] == 'carousel'){
						$carousel_id = 'carousel_'.$key.'_1';
						if(empty($_FILES[$carousel_id]) && empty($_POST[$carousel_id])){
							$error[] = array(
								'EL' => $key,
								'CS' => '.content-carrusel',
								'TX' => "*{$TEMP['#word']['must_insert_more_one_image']}"
							);
						} else {
							for ($i=0; $i < (int)$entry[2]; $i++) {
								$carousel_id = 'carousel_'.$key.'_'.$i;
								if($_FILES[$carousel_id]['size'] > $TEMP['#settings']['file_size_limit']){
									$error[] = array(
										'EL' => $key,
										'CS' => '.content-carrusel',
										'TX' => str_replace('{$file_size_limit}', Specific::SizeFormat($TEMP['#settings']['file_size_limit']), "*{$TEMP['#word']['one_file_too_big_maximum_size']}")
									);
									break;
								}
							}
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
						if(preg_match('/^https?:\/\/(mobile.|)twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/', $entry[2]) == false){
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

						if($tiktok_param == true || ($tiktok_param == true && Specific::ValidateUrl($entry[2]))){
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

					if($entry[0] == 'spotify'){
						if(preg_match('/https?:\/\/(?:embed\.|open\.)(?:spotify\.com\/)(?:(track|artist|album|playlist|episode)|user\/([a-zA-Z0-9]+)\/playlist)\/([a-zA-Z0-9]+)|spotify:((track|artist|album|playlist|episode):([a-zA-Z0-9]+)|user:([a-zA-Z0-9]+):playlist:([a-zA-Z0-9]+))/', $entry[2]) == false){
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
								if(!Specific::ValidateUrl($source['source'])){
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
								if(!Specific::ValidateUrl($source['source'])){
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
					$files = array();
					if(!empty($_FILES['thumbnail'])){
						$thumbnail = Specific::UploadImage(array(
							'name' => $_FILES['thumbnail']['name'],
							'tmp_name' => $_FILES['thumbnail']['tmp_name'],
							'size' => $_FILES['thumbnail']['size'],
							'type' => $_FILES['thumbnail']['type'],
							'folder' => 'posts',
						));
						$files['posts'] = $thumbnail['image'];
					} else {
						$thumbnail = Specific::UploadThumbnail(array(
							'media' => $thumbnail,
							'folder' => 'posts'
						));
						$files['posts'] = $thumbnail['image'];
					}
					if(!empty($post_sources)){
						$post_sources = json_encode(array_values($post_sources));
					} else {
						$post_sources = NULL;
					}
					if(!empty($thumb_sources)){
						$thumb_sources = json_encode(array_values($thumb_sources));
					} else {
						$thumb_sources = NULL;
					}

					$status = 'approved';
					if($TEMP['#settings']['approve_posts'] == 'on' && $TEMP['#admin'] == false){
						$status = 'pending';
					}

					if($thumbnail['return']){
						$st_regex = '/<(?:script|style)[^>]*>(.*?)<\/(?:script|style)>/is';
						$published_at = $created_at = time();
						
						$slug = Specific::CreateSlug($title);
						$slugs = $dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE slug = ?', $slug)->fetchArray(true);
						if($slugs > 0){
							$slug = "{$slug}-{$slugs}";
						}
						
						if($action == 'eraser'){
							$published_at = 0;
						}
						$post_id = $dba->query('INSERT INTO '.T_POST.' (user_id, category_id, title, description, slug, thumbnail, post_sources, thumb_sources, type, status, published_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $TEMP['#user']['id'], $category, $title, $description, $slug, $thumbnail['image'], $post_sources, $thumb_sources, $type, $status, $published_at, $created_at)->insertId();
						if($post_id){
							$thumbnail_accept = $carousel_accept = false;
							$arr_trues = array();
							foreach ($entries as $key => $entry) {
								$entry_title = NULL;
								if(!empty($entry[1])){
									$entry_title = $entry[1];
								}
								$entry_source = NULL;
								if(in_array($entry[0], array('text', 'image', 'carousel'))){
									if($entry[0] == 'text'){
										if(isset($entry[3])){
											$entry_source = $entry[3];
										}
									} else if($entry[0] == 'image'){
										if(isset($entry[4])){
											$entry_source = $entry[4];
										}
									} else {
										if(isset($entry[5])){
											$entry_source = $entry[5];
										}
									}
								}


								$entry_source = preg_replace($st_regex, '', $entry_source);
									
								$content_text = NULL;
								$content_frame = NULL;
								if($entry[0] == 'text'){
									$content_text = preg_replace($st_regex, '', $entry[2]);
								} else {
									if($entry[0] == 'image'){
										$thumbnail_id = 'thumbnail_'.$key;
										if(!empty($_FILES[$thumbnail_id])){
											$thumbnail = $_FILES[$thumbnail_id];
											$image = Specific::UploadImage(array(
												'name' => $thumbnail['name'],
												'tmp_name' => $thumbnail['tmp_name'],
												'size' => $thumbnail['size'],
												'type' => $thumbnail['type'],
												'post_id' => $post_id,
												'eorder' => $key,
												'folder' => 'entries'
											));
											$content_frame = $image['image_ext'];
											$files['entries'][] = $image['image_ext'];
										} else if(!empty($entry[2])){
											$image = Specific::UploadThumbnail(array(
												'media' => $entry[2],
												'post_id' => $post_id,
												'eorder' => $key,
												'folder' => 'entries'
											));
											$content_frame = $image['image_ext'];
											$files['entries'][] = $image['image_ext'];
										}
										$thumbnail_accept = $content_frame['return'];
									} else if($entry[0] == 'carousel'){
										$captions = Specific::Filter($_POST['carousel_captions_'.$key]);
										$captions = html_entity_decode($captions);
										$captions = json_decode($captions, true);
										$carousel = array();
										for ($i=0; $i < (int)$entry[2]; $i++) {
											$carousel_id = 'carousel_'.$key.'_'.$i;
											if(!empty($_FILES[$carousel_id])){
												$thumbnail = $_FILES[$carousel_id];
												$image = Specific::UploadImage(array(
													'name' => $thumbnail['name'],
													'tmp_name' => $thumbnail['tmp_name'],
													'size' => $thumbnail['size'],
													'type' => $thumbnail['type'],
													'post_id' => $post_id,
													'eorder' => $key,
													'folder' => 'entries'
												));
												if($image['return']){
													$files['entries'][] = $image['image_ext'];
													$carousel[] = array(
														'image' => $image['image_ext'],
														'caption' => $captions[$i]
													);
												}
											} else if(!empty($_POST[$carousel_id])){
												$image = Specific::UploadThumbnail(array(
													'media' => Specific::Filter($_POST[$carousel_id]),
													'post_id' => $post_id,
													'eorder' => $key,
													'folder' => 'entries'
												));
												if($image['return']){
													$files['entries'][] = $image['image_ext'];
													$carousel[] = array(
														'image' => $image['image_ext'],
														'caption' => $captions[$i]
													);
												}
											}
										}
										if(!empty($carousel)){
											$carousel_accept = true;
											$content_frame = json_encode($carousel);
										}
									} else if(in_array($entry[0], array('tweet', 'soundcloud', 'spotify', 'tiktok'))){
										if($entry[0] == 'tweet'){
											$api = 'https://api.twitter.com/1/statuses/oembed.json?omit_script=1&url=';
										} else if($entry[0] == 'soundcloud'){
											$api = 'https://soundcloud.com/oembed?format=json&url=';
										} else if($entry[0] == 'spotify'){
											$api = 'https://open.spotify.com/oembed?url=';
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
												} else if($entry[0] == 'soundcloud'){
													$src = preg_match('/(?<=src=").*?(?=[\*"])/', $content_frame, $src_frame);
													if($src){
														$content_frame = $src_frame[0];
													}
												}
												$arr_trues[] = true;
											} else {
												$arr_trues[] = false;
											}
										} else {
											$arr_trues[] = false;
										}
									} else if($entry[0] == 'embed'){
										$attrs = Specific::Filter($_POST["embed_{$key}"]);
										$frame = Specific::MaketFrame($entry[2], $attrs);
										$frame = array(
											'url' => $entry[2],
											'attrs' => $frame['attrs']
										);

										$content_frame = json_encode($frame);
									} else {
										$content_frame = $entry[2];
									}
								}
								if(($entry[0] == 'image' && $thumbnail_accept) || ($entry[0] == 'carousel' && $carousel_accept) || !in_array($entry[0], array('image', 'carousel'))){
									if($dba->query('INSERT INTO '.T_ENTRY.' (post_id, type, title, body, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', $post_id, $entry[0], $entry_title, $content_text, $content_frame, $entry_source, $key, $created_at)->returnStatus()){
										$arr_trues[] = true;
									} else {
										$arr_trues[] = false;
									}
								}
							}

							foreach ($tags as $tag) {
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

							if(!empty($recobo)){
								$recount = 0;
								foreach ($recobo as $re) {
									if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE id = ? AND status = "approved"', $re)->fetchArray(true) > 0){
										$dba->query('INSERT INTO '.T_RECOBO.' (post_id, recommended_id, rorder, created_at) VALUES (?, ?, ?, ?)', $post_id, $re, $recount, time());
										$recount++;
									}
								}
							}
							
							if(!empty($collaborators)){
								$cocount = 0;
								foreach ($collaborators as $co) {
									$user = $dba->query('SELECT about, facebook, twitter, instagram, main_sonet, COUNT(*) as count FROM '.T_USER.' WHERE id = ? AND id NOT IN ('.$TEMP['#blocked_users'].') AND status = "active"', $co)->fetchArray();
									if($user['count'] > 0 && !empty($user['about']) && !empty($user[$user['main_sonet']]) && $co != $TEMP['#user']['id']){
										$insert_id = $dba->query('INSERT INTO '.T_COLLABORATOR.' (user_id, post_id, aorder, created_at) VALUES (?, ?, ?, ?)', $co, $post_id, $cocount, time())->insertId();
										if($insert_id){
											Specific::SetNotify(array(
												'user_id' => $co,
												'notified_id' => $insert_id,
												'type' => 'collab',
											));
										}
										$cocount++;
									}
								}
							}

							if(!in_array(false, $arr_trues)){
								if($TEMP['#settings']['approve_posts'] == 'off' || $TEMP['#admin'] == true){
									$followers = $dba->query('SELECT user_id FROM '.T_FOLLOWER.' WHERE profile_id = ?', $TEMP['#user']['id'])->fetchAll();
									foreach($followers as $follow){
										Specific::SetNotify(array(
											'user_id' => $follow['user_id'],
											'notified_id' => $post_id,
											'type' => 'post',
										));
									}
								}

								$deliver = array(
									'S' => 200,
									'LK' => Specific::Url($slug)
								);
							} else {
								if($dba->query('DELETE FROM '.T_POST.' WHERE id = ?', $post_id)->returnStatus()){
									foreach($files as $key => $file){
										if($key == 'posts'){
											unlink("uploads/posts/{$file}-b.jpeg");
											unlink("uploads/posts/{$file}-s.jpeg");
										} else {
											foreach($file as $fi){
												unlink("uploads/entries/{$fi}");
											}
										}
									}
								}
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
    } else if($one == 'edit-post'){
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

		$recobo = Specific::Filter($_POST['recobo']);
		$recobo = html_entity_decode($recobo);
		$recobo = json_decode($recobo, true);

		$collaborators = Specific::Filter($_POST['collaborators']);
		$collaborators = html_entity_decode($collaborators);
		$collaborators = json_decode($collaborators, true);

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
						'CS' => $entry[0] == 'text' ? '.simditor' : ($entry[0] == 'image' || $entry[0] == 'carousel' ? '.item-placeholder' : '.item-input'),
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
					if(!empty($_FILES['thumbnail'])){
						if($_FILES['thumbnail']['size'] > $TEMP['#settings']['file_size_limit']){
							$error[] = array(
								'EL' => '#post-right .item-placeholder',
								'TX' => str_replace('{$file_size_limit}', Specific::SizeFormat($TEMP['#settings']['file_size_limit']), "*{$TEMP['#word']['file_too_big_maximum_size']}")
							);
						}
					}
					if(!in_array($category, $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false))){
						$error[] = array(
							'EL' => '#category',
							'TX' => "*{$TEMP['#word']['oops_error_has_occurred']}"
						);
					}
					foreach ($entries as $key => $entry) {
						$ids[] = (int)end($entry);

						if($entry[0] == 'image'){
							if(!empty($_FILES['thumbnail_'.$key])){
								if($_FILES['thumbnail_'.$key]['size'] > $TEMP['#settings']['file_size_limit']){
									$error[] = array(
										'EL' => $key,
										'CS' => '.item-placeholder',
										'TX' => str_replace('{$file_size_limit}', Specific::SizeFormat($TEMP['#settings']['file_size_limit']), "*{$TEMP['#word']['file_too_big_maximum_size']}")
									);
								}
							} else {
								if(!empty($entry[2])){
									$image_err = false;
									$validate_url = Specific::ValidateUrl($entry[2], true);
									$entries[$key][2] = $entry[2] = $validate_url['url'];
									if(!$validate_url['return']){
										$image_err = true;
										$image_errt = "*{$TEMP['#word']['enter_a_valid_url']}";
									} else if(exif_imagetype($entry[2]) == false){
										$image_err = true;
										$image_errt = "*{$TEMP['#word']['download_could_not_completed']}";
									}
									if($image_err){
										$error[] = array(
											'EL' => $key,
											'CS' => '.item-placeholder',
											'TX' => $image_errt
										);
									}
								}
							}
						}

						if($entry[0] == 'carousel'){
							$carousel_id = 'carousel_'.$key.'_1';
							if(empty($_FILES[$carousel_id]) && empty($_POST[$carousel_id])){
								$error[] = array(
									'EL' => $key,
									'CS' => '.content-carrusel',
									'TX' => "*{$TEMP['#word']['must_insert_more_one_image']}"
								);
							} else {
								for ($i=0; $i < (int)$entry[2]; $i++) {
									$carousel_id = 'carousel_'.$key.'_'.$i;
									if($_FILES[$carousel_id]['size'] > $TEMP['#settings']['file_size_limit']){
										$error[] = array(
											'EL' => $key,
											'CS' => '.content-carrusel',
											'TX' => str_replace('{$file_size_limit}', Specific::SizeFormat($TEMP['#settings']['file_size_limit']), "*{$TEMP['#word']['one_file_too_big_maximum_size']}")
										);
										break;
									}
								}
							}
						}

						if($entry[0] == 'embed'){
							if(!Specific::ValidateUrl($entry[2])){
								$error[] = array(
									'EL' => $key,
									'CS' => '.item_url',
									'TX' => "*{$TEMP['#word']['enter_a_valid_url']}"
								);
							}
						}

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
								if(preg_match('/^https?:\/\/(mobile.|)twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/', $entry[2]) == false){
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

								if($tiktok_param == true || ($tiktok_param == true && Specific::ValidateUrl($entry[2]))){
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

							if($entry[0] == 'spotify'){
								if(preg_match('/https?:\/\/(?:embed\.|open\.)(?:spotify\.com\/)(?:(track|artist|album|playlist|episode)|user\/([a-zA-Z0-9]+)\/playlist)\/([a-zA-Z0-9]+)|spotify:((track|artist|album|playlist|episode):([a-zA-Z0-9]+)|user:([a-zA-Z0-9]+):playlist:([a-zA-Z0-9]+))/', $entry[2]) == false){
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
									if(!Specific::ValidateUrl($source['source'])){
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
									if(!Specific::ValidateUrl($source['source'])){
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
						if(!empty($thumbnail) && !preg_match("/{$TEMP['#site_domain']}/i", $thumbnail)){
							if(!empty($_FILES['thumbnail'])){
								$thumbnail = Specific::UploadImage(array(
									'name' => $_FILES['thumbnail']['name'],
									'tmp_name' => $_FILES['thumbnail']['tmp_name'],
									'size' => $_FILES['thumbnail']['size'],
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
							$thumbnail = array(
								'return' => true,
								'image' => $post['thumbnail']
							);
						}
						if(!empty($post_sources)){
							$post_sources = json_encode(array_values($post_sources));
						} else {
							$post_sources = NULL;
						}
						if(!empty($thumb_sources)){
							$thumb_sources = json_encode(array_values($thumb_sources));
						} else {
							$thumb_sources = NULL;
						}
						if($thumbnail['return']){
							$st_regex = '/<(?:script|style)[^>]*>(.*?)<\/(?:script|style)>/is';
							$slug = $post['slug'];
							$updated_at = $created_at = time();

							if($dba->query('UPDATE '.T_POST.' SET category_id = ?, title = ?, description = ?, thumbnail = ?, post_sources = ?, thumb_sources = ?, type = ?, updated_at = ? WHERE id = ?', $category, $title, $description, $thumbnail['image'], $post_sources, $thumb_sources, $type, $updated_at, $post_id)->returnStatus()){


								$entries_ids = $dba->query('SELECT id FROM '.T_ENTRY.' WHERE post_id = ?', $post_id)->fetchAll(false);

								$del_entries = array_diff($entries_ids, $ids);

								$carousel_accept = false;
								$arr_trues = array();
								foreach ($entries as $key => $entry) {
									$entry_id = (int)end($entry);
									$entry_exists = $dba->query('SELECT eorder, frame FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $entry_id, $post_id)->fetchArray();
									
									$entry_title = NULL;
									if(!empty($entry[1])){
										$entry_title = $entry[1];
									}
									$entry_source = NULL;

									if(in_array($entry[0], array('text', 'image', 'carousel'))){
										if($entry[0] == 'text'){
											if(isset($entry[3])){
												$entry_source = $entry[3];
											}
										} else if($entry[0] == 'image'){
											if(isset($entry[4])){
												$entry_source = $entry[4];
											}
										} else {
											if(isset($entry[5])){
												$entry_source = $entry[5];
											}
										}
									}

									$entry_source = preg_replace($st_regex, '', $entry_source);
										
									$content_text = NULL;
									$content_frame = NULL;
									if($entry[0] == 'text'){
										$content_text = preg_replace($st_regex, '', $entry[2]);
									} else {
										if($entry[0] == 'image'){
											if(!strpos($entry[2], $entry_exists['frame'])){
												$thumbnail_id = 'thumbnail_'.$key;
												if(!empty($_FILES[$thumbnail_id])){
													$thumbnail = $_FILES[$thumbnail_id];
													$image = Specific::UploadImage(array(
														'name' => $thumbnail['name'],
														'tmp_name' => $thumbnail['tmp_name'],
														'size' => $thumbnail['size'],
														'type' => $thumbnail['type'],
														'post_id' => $post_id,
														'eorder' => $key,
														'folder' => 'entries'
													));
													$content_frame = $image['image_ext'];
												} else if(!empty($entry[2])){
													$image = Specific::UploadThumbnail(array(
														'media' => $entry[2],
														'post_id' => $post_id,
														'eorder' => $key,
														'folder' => 'entries'
													));
													$content_frame = $image['image_ext'];
												}

												if($content_frame['return']){
													if(!empty($entry_id) && in_array($entry_id, $entries_ids)){
														$entry_frame = $dba->query('SELECT frame FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $entry_id, $post_id)->fetchArray(true);
														unlink("uploads/entries/{$entry_frame}");
														if($dba->query('UPDATE '.T_ENTRY.' SET frame = ? WHERE id = ?', $content_frame, $entry_id)->returnStatus()){
															$arr_trues[] = true;
														} else {
															$arr_trues[] = false;
														}
													} else {
														if($dba->query('INSERT INTO '.T_ENTRY.' (post_id, type, title, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', $post_id, $entry[0], $entry_title, $content_frame, $entry_source, $key, $created_at)->returnStatus()){
															$arr_trues[] = true;
														} else {
															$arr_trues[] = false;
														}
													}
												}
											}
										} else if($entry[0] == 'carousel'){
											$captions = Specific::Filter($_POST['carousel_captions_'.$key]);
											$captions = html_entity_decode($captions);
											$captions = json_decode($captions, true);
											$carousel = array();

											for ($i=0; $i < (int)$entry[2]; $i++) {
												$carousel_id = 'carousel_'.$key.'_'.$i;

												if(!empty($_FILES[$carousel_id])){
													$thumbnail = $_FILES[$carousel_id];
													$image = Specific::UploadImage(array(
														'name' => $thumbnail['name'],
														'tmp_name' => $thumbnail['tmp_name'],
														'size' => $thumbnail['size'],
														'type' => $thumbnail['type'],
														'post_id' => $post_id,
														'eorder' => $key,
														'folder' => 'entries'
													));
													if($image['return']){
														$carousel[] = array(
															'image' => $image['image_ext'],
															'caption' => $captions[$i]
														);
													}
												} else if(!empty($_POST[$carousel_id])){
													$image = Specific::UploadThumbnail(array(
														'media' => Specific::Filter($_POST[$carousel_id]),
														'post_id' => $post_id,
														'eorder' => $key,
														'folder' => 'entries'
													));
													if($image['return']){
														$carousel[] = array(
															'image' => $image['image_ext'],
															'caption' => $captions[$i]
														);
													}
												}
											}
											if(!empty($carousel)){
												$carousel_accept = true;
												$content_frame = json_encode($carousel);
												if(empty($entry_id)){
													if($dba->query('INSERT INTO '.T_ENTRY.' (post_id, type, title, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', $post_id, $entry[0], $entry_title, $content_frame, $entry_source, $key, $created_at)->returnStatus()){
														$arr_trues[] = true;
													} else {
														$arr_trues[] = false;
													}
												}
											}
										} else {
											if($entry[0] == 'embed'){
												$attrs = Specific::Filter($_POST["embed_{$key}"]);
												$frame = Specific::MaketFrame($entry[2], $attrs);
												$frame = array(
													'url' => $entry[2],
													'attrs' => $frame['attrs']
												);

												$content_frame = json_encode($frame);
											} else if(empty($entry_id)){
												if(in_array($entry[0], array('tweet', 'soundcloud', 'spotify', 'tiktok'))){
													if($entry[0] == 'tweet'){
														$api = 'https://api.twitter.com/1/statuses/oembed.json?omit_script=1&url=';
													} else if($entry[0] == 'soundcloud'){
														$api = 'https://soundcloud.com/oembed?format=json&url=';
													} else if($entry[0] == 'spotify'){
														$api = 'https://open.spotify.com/oembed?url=';
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
															} else if($entry[0] == 'soundcloud'){
																$src = preg_match('/(?<=src=").*?(?=[\*"])/', $content_frame, $src_frame);
																if($src){
																	$content_frame = $src_frame[0];
																}
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
										if(($entry[0] == 'carousel' && $carousel_accept) || $entry[0] != 'carousel'){
											if(!empty($entry_id) && in_array($entry_id, $entries_ids)){
												if($dba->query('UPDATE '.T_ENTRY.' SET title = ?, esource = ?, eorder = ?, updated_at = ? WHERE id = ?', $entry_title, $entry_source, $key, $updated_at, $entry_id)->returnStatus()){
													if($entry[0] == 'carousel'){
														if($dba->query('UPDATE '.T_ENTRY.' SET frame = ? WHERE id = ?', $content_frame, $entry_id)->returnStatus()){
															$carousel = json_decode($entry_exists['frame'], true);
															foreach ($carousel as $car) {
																unlink("uploads/entries/{$car['image']}");
															}
														}
													} else if($entry[0] == 'image' && $key != $entry_exists['eorder']){
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

								}

								if(!empty($del_entries)){
									$del_entries = array_values($del_entries);
									foreach ($del_entries as $delid) {
										$entry = $dba->query('SELECT type, frame FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $delid, $post_id)->fetchArray();
										$dba->query('DELETE FROM '.T_ENTRY.' WHERE id = ? AND post_id = ?', $delid, $post_id);
										if($entry['type'] == 'image'){
											unlink("uploads/entries/{$entry['frame']}");
										} else if($entry['type'] == 'carousel'){
											$carousel = json_decode($entry['frame'], true);
											foreach ($carousel as $car) {
												unlink("uploads/entries/{$car['image']}");
											}
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
								
								$recobo_ids = $dba->query('SELECT recommended_id FROM '.T_RECOBO.' WHERE post_id = ?', $post_id)->fetchAll(false);

								$add_recobo = array_diff($recobo, $recobo_ids);
								$del_recobo = array_diff($recobo_ids, $recobo);

								if(!empty($add_recobo)){
									foreach ($add_recobo as $addre) {
										if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE id = ? AND status = "approved"', $addre)->fetchArray(true) > 0){
											$dba->query('INSERT INTO '.T_RECOBO.' (post_id, recommended_id, created_at) VALUES (?, ?, ?)', $post_id, $addre, time());
										}
									}
								}

								if(!empty($del_recobo)){
									foreach ($del_recobo as $delre) {
										$dba->query('DELETE FROM '.T_RECOBO.' WHERE post_id = ? AND recommended_id = ?', $post_id, $delre);
									}
								}

								foreach ($recobo as $key => $re) {
									$dba->query('UPDATE '.T_RECOBO.' SET rorder = ? WHERE recommended_id = ?', $key, $re);
								}

								$collaborators_ids = $dba->query('SELECT user_id FROM '.T_COLLABORATOR.' WHERE post_id = ?', $post_id)->fetchAll(false);

								$add_collaborators = array_diff($collaborators, $collaborators_ids);
								$del_collaborators = array_diff($collaborators_ids, $collaborators);

								if(!empty($add_collaborators)){
									foreach ($add_collaborators as $addco) {
										$user = $dba->query('SELECT about, facebook, twitter, instagram, main_sonet, COUNT(*) as count FROM '.T_USER.' WHERE id = ? AND id NOT IN ('.$TEMP['#blocked_users'].') AND status = "active"', $addco)->fetchArray();
										if($user['count'] > 0 && !empty($user['about']) && !empty($user[$user['main_sonet']])){
											$insert_id = $dba->query('INSERT INTO '.T_COLLABORATOR.' (user_id, post_id, created_at) VALUES (?, ?, ?)', $addco, $post_id, time())->insertId();

											if($insert_id){
												Specific::SetNotify(array(
													'user_id' => $addco,
													'notified_id' => $insert_id,
													'type' => 'collab',
												));
											}
										}
									}
								}

								if(!empty($del_collaborators)){
									foreach ($del_collaborators as $delco) {
										$collab_id = $dba->query('SELECT id FROM '.T_COLLABORATOR.' WHERE user_id = ? AND post_id = ?', $delco, $post_id)->fetchArray(true);
										if($dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id = ? AND type = "n_collab"', $collab_id)->returnStatus()){
											$dba->query('DELETE FROM '.T_COLLABORATOR.' WHERE id = ?', $collab_id);
										}
									}
								}

								foreach ($collaborators as $key => $co) {
									$dba->query('UPDATE '.T_COLLABORATOR.' SET aorder = ? WHERE user_id = ?', $key, $co);
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
    } else if($one == 'get-tags'){
		$search = Specific::Filter($_POST['search']);
		$tags = Specific::Filter($_POST['tags']);
		$tags = html_entity_decode($tags);
		$tags = json_decode($tags, true);

		if(!empty($search) && count($tags) < $TEMP['#settings']['number_labels']){
			$html = $query = '';
			if(!empty($tags)){
				for ($i=0; $i < count($tags); $i++) { 
					$query .= " AND name <> '{$tags[$i]}'";
				}
			}

			$tags = $dba->query('SELECT * FROM '.T_LABEL.' WHERE name LIKE "%'.$search.'%" '.$query.' LIMIT 5')->fetchAll();
			foreach ($tags as $tag) {
				$html .= '<li class="border-bottom border-grely"><button class="btn_tag btn-noway w-100 padding-l10 padding-5 text-left background-hover animation-ease3s ellipsis-horizontal" type="button">'.$tag['name'].'</button></li>';
			}
			if(!empty($html)){
				$deliver = array(
					'S' => 200,
					'HT' => $html,
					'TG' => $search
				);
			}
		}
	} else if($one == 'get-image'){
		$url = Specific::Filter($_POST['url']);
		if(!empty($url)){
			$validate_url = Specific::ValidateUrl($url, true);
			if($validate_url['return']){
				$url = $validate_url['url'];
				if(exif_imagetype($url) != false){
					if(!strpos(strtolower($url), '.gif')){
						$image = Specific::getContentUrl($url, true);
						$image = base64_encode($image);
						if(!empty($image)){
							$deliver = array(
								'S' => 200,
								'IM' => "data:image/jpeg;base64,$image"
							);
						} else {
							$deliver = array(
								'S' => 400,
								'E' => "*{$TEMP['#word']['download_could_not_completed']}"
							);
						}
					} else {
						$deliver = array(
							'S' => 400,
							'E' => "*{$TEMP['#word']['file_not_supported']}"
						);
					}
				} else {
					$deliver = array(
						'S' => 400,
						'E' => "*{$TEMP['#word']['download_could_not_completed']}"
					);
				}
			} else {
				$deliver = array(
					'S' => 400,
					'E' => "*{$TEMP['#word']['enter_a_valid_url']}"
				);
			}
		} else {
			$deliver = array(
				'S' => 400,
				'E' => "*{$TEMP['#word']['this_field_is_empty']}"
			);
		}
	} else if($one == 'get-frame'){
		$url = Specific::Filter($_POST['url']);
		$type = Specific::Filter($_POST['type']);
		if(!empty($url)){
			$validate_url = Specific::ValidateUrl($url, true);
			if($validate_url['return']){
				$url = $validate_url['url'];
				if(!empty($type)){
					if(in_array($type, array('tweet', 'soundcloud', 'spotify', 'facebookpost', 'instagrampost', 'tiktok'))){
						if($type == 'facebookpost'){
							if(preg_match("/(?:(?:http|https):\/\/)?(?:www\.)?(?:facebook\.com)\/(\d+|[A-Za-z0-9\.]+)\/?/", $url)){
								$deliver = array(
									'S' => 200,
									'FB' => 1,
									'HT' => '<div class="fb-post display-block background-white" data-href="'.$url.'" data-width="100%"></div>'
								);
							}
						} else if($type == 'instagrampost'){
							if(preg_match("/(?:(?:http|https):\/\/)?(?:www\.)?(?:instagram\.com|instagr\.am)\/(?:p|tv|reel)\/([A-Za-z0-9-_\.]+)/", $url)){
                				$TEMP['!omit_script'] = false;
								$TEMP['!url'] = $url;
								$deliver = array(
									'S' => 200,
									'HT' => Specific::Maket('includes/create-edit-post/instagram-blockquote')
								);
							}
						} else {
							$tweet = preg_match('/^https?:\/\/(mobile.|)twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/', $url);
							$soundcloud = preg_match('/^(?:(https?):\/\/)?(?:(?:www|m)\.)?(soundcloud\.com|snd\.sc)\/[a-z0-9](?!.*?(-|_){2})[\w-]{1,23}[a-z0-9](?:\/.+)?$/', $url);

							$spotify = preg_match('/https?:\/\/(?:embed\.|open\.)(?:spotify\.com\/)(?:(track|artist|album|playlist|episode)|user\/([a-zA-Z0-9]+)\/playlist)\/([a-zA-Z0-9]+)|spotify:((track|artist|album|playlist|episode):([a-zA-Z0-9]+)|user:([a-zA-Z0-9]+):playlist:([a-zA-Z0-9]+))/', $url);

							$tiktok_url = preg_match("/(?:http(?:s)?:\/\/)?(?:(?:www)\.(?:tiktok\.com)(?:\/)(?!foryou)(@[a-zA-z0-9]+)(?:\/)(?:video)(?:\/)([\d]+)|(?:m)\.(?:tiktok\.com)(?:\/)(?!foryou)(?:v)(?:\/)?(?=([\d]+)\.html))/", $url, $tk_video_url);

							$tiktok_param = preg_match("/#\/(?P<username>@[a-zA-z0-9]*|.*)(?:\/)?(?:v|video)(?:\/)?(?P<id>[\d]+)/", $url, $tk_video_param);

							if($tiktok_param == true){
								$tiktok_url = true;
								$url = "https://www.tiktok.com/{$tk_video_param['username']}/video/{$tk_video_param['id']}";
							}

							if($tweet == true || $soundcloud == true || $spotify == true || $tiktok_url == true){
								if($tweet == true && $type == 'tweet'){
									$api = 'https://api.twitter.com/1/statuses/oembed.json?url=';
								} else if($soundcloud == true && $type == 'soundcloud'){
									$api = 'https://soundcloud.com/oembed?format=json&url=';
								} else if($spotify == true && $type == 'spotify'){
									$api = 'https://open.spotify.com/oembed?url=';
								} else if($tiktok_url == true && $type == 'tiktok'){
									$api = 'https://www.tiktok.com/oembed?format=json&url=';
								}
								$json = Specific::getContentUrl("{$api}{$url}");
								$json = json_decode($json, true);
								
								if(!isset($json['error']) && !isset($json['errors']) && !isset($json['status_msg'])){
									if(!empty($json)){
										if(isset($json['title'])){
											$deliver['TT'] = $json['title'];
										}
										$deliver['S'] = 200;
										$deliver['HT'] = $json['html'];
									}
								}
							} else {
								$deliver = array(
									'S' => 400,
									'E' => "*{$TEMP['#word']['enter_a_valid_url']}"
								);
							}
						}
					} else if($type == 'video'){
						$frame = Specific::IdentifyFrame($url);
						if($frame['return']){
							$deliver['S'] = 200;
							$deliver['HT'] = $frame['html'];
						} else {
							$deliver = array(
								'S' => 400,
								'E' => "*{$TEMP['#word']['enter_a_valid_url']}"
							);
						}
					} else if($type == 'embed'){
						$attrs = Specific::Filter($_POST['attrs']);
						$frame = Specific::MaketFrame($url, $attrs);

						$deliver['S'] = 200;
						$deliver['HT'] = $frame['html'];
					}
				}
			} else {
				$deliver = array(
					'S' => 400,
					'E' => "*{$TEMP['#word']['enter_a_valid_url']}"
				);
			}
		} else {
			$deliver = array(
				'S' => 400,
				'E' => "*{$TEMP['#word']['this_field_is_empty']}"
			);
		}
	} else if($one == 'entry'){
		$type = Specific::Filter($_POST['type']);
		$types = array(
			'text',
			'image',
			'carousel',
			'video',
			'embed',
			'tweet',
			'soundcloud',
			'spotify',
			'facebookpost',
			'instagrampost',
			'tiktok'
		);
		if(is_numeric($type) && isset($types[$type])){
			$TEMP['#type'] = $types[$type];
			$TEMP['btn_get'] = 'btn_giframe';
			if($TEMP['#type'] == 'video') {
			 	$TEMP['btn_get'] = 'btn_gvideo';
			}
			$deliver = array(
				'S' => 200,
				'TP' => $TEMP['#type'],
				'HT' => Specific::Maket('create-post/includes/entry')
			);
		}
	} else if($one == 'ecp-search'){
		$post_id = Specific::Filter($_POST['post_id']);
		$post_ids = Specific::Filter($_POST['post_ids']);
		$post_ids = html_entity_decode($post_ids);
		$post_ids = json_decode($post_ids);
		$keyword = Specific::Filter($_POST['keyword']);
		if(!empty($keyword)){
			$html = '';
			$query = '';
			if(empty($post_id)){
				$post_id = 0;
			}
			if(!empty($post_ids)){
				$query = ' AND id NOT IN ('.implode(',', $post_ids).')';
			}
			$search_result = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ? AND (title LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%") AND status = "approved"'.$query.' LIMIT 5', $post_id)->fetchAll();

			if(!empty($search_result)){
				foreach ($search_result as $post) {
					$TEMP['!id'] = $post['id'];

					$TEMP['!title'] = $post['title'];
					$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');

					$html .= Specific::Maket("includes/create-edit-post/ecp-search-result");
				}
				Specific::DestroyMaket();

				$deliver = array(
					'S' => 200,
					'HT' => $html
				);
			} else {
				$TEMP['keyword'] = $keyword;
				$html = Specific::Maket("not-found/no-result-for");
				$deliver = array(
					'S' => 400,
					'HT' => $html
				);
			}
		}
	} else if($one == 'push-post'){
		$post_id = Specific::Filter($_POST['post_id']);
		$push_id = Specific::Filter($_POST['push_id']);
		$post_ids = Specific::Filter($_POST['post_ids']);
		$post_ids = html_entity_decode($post_ids);
		$post_ids = json_decode($post_ids);
		$entry_text = Specific::Filter($_POST['entry_text']);
		$entry_text = html_entity_decode($entry_text);
		$entry_text = json_decode($entry_text);

		if(!empty($push_id) && is_numeric($push_id)){

			if(empty($post_id)){
				$post_id = 0;
			}

			$post = $dba->query('SELECT *, COUNT(*) as count FROM '.T_POST.' WHERE id != ? AND id = ? AND status = "approved"', $post_id, $push_id)->fetchArray();

			$error = false;
			$pos = 0;
			foreach ($entry_text as $key => $paragraph) {
				$paragraph = explode('</p>', $paragraph);
				if(count($paragraph) < 6){
					$error = true;
					$pos = $key;
					break;
				}
			}

			if(count($entry_text) > count($post_ids)){
				if($error == false){
					if($post['count'] > 0){
						$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
						$TEMP['!id'] = $post['id'];

						$TEMP['!title'] = $post['title'];
						$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
						$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
						$TEMP['!url'] = Specific::Url($post['slug']);
						$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
						$TEMP['!published_date'] = date('c', $post['published_at']);
						$TEMP['!published_at'] = Specific::DateString($post['published_at']);

						$deliver = array(
							'S' => 200,
							'HT' => Specific::Maket("includes/create-edit-post/recommended-body"),
							'ID' => $post['id']
						);
					}
				} else {
					$deliver = array(
						'S' => 400,
						'E' => "*{$TEMP['#word']['you_must_have_minimum_paragraphs']}",
						'PS' => $pos
					);
				}
			} else {
				$deliver = array(
					'S' => 400,
					'E' => "*{$TEMP['#word']['you_must_add_another_text_input']}"
				);
			}
		}
	} else if($one == 'ecu-search'){
		$user_ids = Specific::Filter($_POST['user_ids']);
		$user_ids = html_entity_decode($user_ids);
		$user_ids = json_decode($user_ids);
		$keyword = Specific::Filter($_POST['keyword']);
		if(!empty($keyword)){
			$html = '';
			$query = '';
			if(!empty($user_ids)){
				$query = ' AND id NOT IN ('.implode(',', $user_ids).')';
			}
			$search_result = $dba->query('SELECT id FROM '.T_USER.' WHERE id != ? AND id NOT IN ('.$TEMP['#blocked_users'].') AND (username LIKE "%'.$keyword.'%" OR email LIKE "%'.$keyword.'%" OR name LIKE "%'.$keyword.'%" OR surname LIKE "%'.$keyword.'%") AND status = "active"'.$query.' LIMIT 5', $TEMP['#user']['id'])->fetchAll();

			if(!empty($search_result)){
				foreach ($search_result as $user) {

					$TEMP['!id'] = $user['id'];
					
					$user = Specific::Data($user['id'], array('username', 'avatar'));
					$TEMP['!collab_name'] = $user['username'];
					$TEMP['!collab_url'] = Specific::ProfileUrl($user['username']);
					$TEMP['!collab_avatar'] = $user['avatar_s'];

					$html .= Specific::Maket("includes/create-edit-post/ecu-search-result");
				}
				Specific::DestroyMaket();

				$deliver = array(
					'S' => 200,
					'HT' => $html
				);
			} else {
				$TEMP['keyword'] = $keyword;
				$html = Specific::Maket("not-found/no-result-for");
				$deliver = array(
					'S' => 400,
					'HT' => $html
				);
			}
		}
	} else if($one == 'push-user'){
		$post_id = Specific::Filter($_POST['post_id']);
		$push_id = Specific::Filter($_POST['push_id']);
		$user_ids = Specific::Filter($_POST['user_ids']);
		$user_ids = html_entity_decode($user_ids);
		$user_ids = json_decode($user_ids);

		if(!empty($push_id) && is_numeric($push_id)){
			$user = $dba->query('SELECT id, about, facebook, twitter, instagram, main_sonet, COUNT(*) as count FROM '.T_USER.' WHERE id = ? AND id NOT IN ('.$TEMP['#blocked_users'].') AND status = "active"', $push_id)->fetchArray();

			$user_id = $user['id'];
			if($user['count'] > 0){
				if(!empty($user['about'])){
					if(!empty($user[$user['main_sonet']])){
						$TEMP['!id'] = $user_id;
				
						$user = Specific::Data($user_id, array('username', 'avatar'));
						$TEMP['!collab_name'] = $user['username'];
						$TEMP['!collab_avatar'] = $user['avatar_s'];

						$deliver = array(
							'S' => 200,
							'HT' => Specific::Maket("includes/create-edit-post/collaborator"),
							'ID' => $user_id
						);
					} else {
						$username = Specific::Data($user_id, array('username'));
						$deliver = array(
							'S' => 400,
							'E' => "*{$username} {$TEMP['#word']['you_need_complete_your']} {$TEMP['#word'][$user['main_sonet']]} {$TEMP['#word']['in_settings']}"
						);
					}
				} else {
					$username = Specific::Data($user_id, array('username'));
					$deliver = array(
						'S' => 400,
						'E' => "*{$username} {$TEMP['#word']['you_need_fill_profile_description']}"
					);
				}
			}
		}
	}
}
?>