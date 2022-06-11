<?php
if($one == 'load'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);
	$category_id = Specific::Filter($_POST['category_id']);

	if(!empty($category_id) && is_numeric($category_id) && !empty($post_ids) && is_array($post_ids)){
		$post = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND id NOT IN (?) AND status = "approved" ORDER BY RAND()', $category_id, implode(',', $post_ids))->fetchArray();
		if(!empty($post)){
			$title = $post['title'];
			$url = Specific::Url($post['slug']);
			$post_views = $post['views'];
			$avatar = Specific::Data($post['user_id'], array('avatar'));
			$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
			$fingerprint = Specific::Fingerprint($TEMP['#user']['id']);

			if($dba->query('SELECT COUNT(*) FROM '.T_VIEW.' WHERE post_id = ? AND fingerprint = ?', $post['id'], $fingerprint)->fetchArray(true) == 0){
				$post_views = ($post['views']+1);
				if($dba->query('INSERT INTO '.T_VIEW.' (post_id, fingerprint, created_at) VALUES (?, ?, ?)', $post['id'], $fingerprint, time())->returnStatus()){
					$dba->query('UPDATE '.T_POST.' SET views = ? WHERE id = ?', $post['id'], $post_views);
				};
			}

			$TEMP['title'] = $title;
			$TEMP['category'] = $category['name'];
			$TEMP['category_slug'] = $category['slug'];
			$TEMP['views'] = number_format($post_views);
			$TEMP['post_id'] = $post['id'];
			$TEMP['author_name'] = Specific::Data($post['user_id'], array('username'));
			$TEMP['author_url'] = Specific::ProfileUrl($TEMP['author_name']);
			$TEMP['author_avatar'] = $avatar['avatar_s'];
			$TEMP['thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 'b');
			$TEMP['url'] = $url;
			$TEMP['type'] = $post['type'];

			$TEMP['title_encoded'] = urlencode($title);
			$TEMP['description_encoded'] = urlencode($post['description']);
			$TEMP['url_encoded'] = urlencode($url);
			$TEMP['slug_encoded'] = urlencode($post['slug']);
			$TEMP['published_date'] = date('c', $post['published_at']);
			$TEMP['published_at'] = Specific::DateString($post['published_at']);


			$TEMP['#is_loaded'] = true;

			$post_sources = json_decode($post['post_sources'], true);
			if(!empty($post_sources)){
				$TEMP['#post_sources'] = array();
				if(count($post_sources) > 1){
					foreach ($post_sources as $key => $source) {
						if($key != end(array_keys($post_sources))){
							$TEMP['#post_sources'][] = " <a class='btn-noway color-blue hover-button' href='{$source['source']}' target='_blank'>{$source['name']}</a>";
						}
					}
					$last_source = end($post_sources);
					$TEMP['#post_sources'] = implode(',', $TEMP['#post_sources']);
					$TEMP['#post_sources'] = "<b>{$TEMP['#word']['consulted_sources']}:</b> {$TEMP['#post_sources']} {$TEMP['#word']['and']} <a class='btn-noway color-blue hover-button' href='{$last_source['source']}' target='_blank'>{$last_source['name']}</a>";
				} else {
					$TEMP['#post_sources'] = "<b>{$TEMP['#word']['consulted_source']}:</b> <a class='btn-noway color-blue hover-button' href='{$post_sources[0]['source']}' target='_blank'>{$post_sources[0]['name']}</a>";
				}
			}
			$thumb_sources = json_decode($post['thumb_sources'], true);
			if(!empty($thumb_sources)){
				$TEMP['#thumb_sources'] = array();
				if(count($thumb_sources) > 1){
					foreach ($thumb_sources as $key => $source) {
						if($key != end(array_keys($thumb_sources))){
							$TEMP['#thumb_sources'][] = " <a class='btn-noway color-blue hover-button' href='{$source['source']}' target='_blank'>{$source['name']}</a>";
						}
					}
					$last_source = end($thumb_sources);
					$TEMP['#thumb_sources'] = implode(',', $TEMP['#thumb_sources']);
					$TEMP['#thumb_sources'] = "{$TEMP['#word']['images_taken_from']}: {$TEMP['#thumb_sources']} {$TEMP['#word']['and']} <a class='btn-noway color-blue hover-button' href='{$last_source['source']}' target='_blank'>{$last_source['name']}</a>";
				} else {
					$TEMP['#thumb_sources'] = "{$TEMP['#word']['image_taken_from']}: <a class='btn-noway color-blue hover-button' href='{$thumb_sources[0]['source']}' target='_blank'>{$thumb_sources[0]['name']}</a>";
				}
			}

			$TEMP['#current_url'] = $post['slug'];
			$TEMP['#thumb_sources'] = $post['thumb_sources'];
			$TEMP['#entry_types'] = array();
			$TEMP['#saved'] = $dba->query('SELECT COUNT(*) FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post['id'])->fetchArray(true);

			$entries = $dba->query('SELECT * FROM '.T_ENTRY.' WHERE post_id = ? ORDER BY eorder', $post['id'])->fetchAll();
			foreach ($entries as $key => $entry) {
				$TEMP['#entry_types'][] = $entry['type'];
			}

			$tags = $dba->query('SELECT * FROM '.T_LABEL.' t WHERE (SELECT label_id FROM '.T_TAG.' WHERE post_id = ? AND label_id = t.id) = id', $post['id'])->fetchAll();
			foreach ($tags as $tag) {
				$TEMP['!name'] = $tag['name'];
				$TEMP['!url'] = Specific::Url("{$TEMP['#r_tag']}/{$tag['slug']}");
				$TEMP['tags'] .= Specific::Maket('post/includes/tags');
			}
			Specific::DestroyMaket();

			$related_body = 0;
			foreach ($entries as $key => $entry) {
				$TEMP['!frame'] = $entry['frame'];
				if($entry['type'] == 'text'){
			        $j = 0;
			        $paragraph = explode('</p>', $entry['body']);
			        $paragraph_count = count($paragraph);
			        $paragraph_body = round($paragraph_count/2);
			        $entry['body'] = '';
			        for ($i = 0; $i < $paragraph_count; $i++){
			            if($paragraph_count >= 6 && $i == $paragraph_body && $paragraph_body != ($j+4) && $key == 0){
	                		$realted_bo = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ? AND category_id = ? AND status = "approved" ORDER BY RAND()', $post['id'], $post['category_id'])->fetchArray();
	                		$TEMP['!title'] = $realted_bo['title'];
	                		$TEMP['!url'] = Specific::Url($realted_bo['slug']);
							$TEMP['!thumbnail'] = Specific::GetFile($realted_bo['thumbnail'], 1, 's');
	                    	$entry['body'] .= Specific::Maket('post/includes/related-body');
	                		$related_body = $realted_bo['id'];
			            } else if($i == 2 || $i == ($j+4)){
			                $j = $i;
			                $entry['body'] .= Specific::Maket('post/includes/advertisement-body');
			            }
			            $entry['body'] .= $paragraph[$i];
			        }
				} else if($entry['type'] == 'image'){
					$TEMP['!frame'] = Specific::GetFile($entry['frame'], 3);
				} else if($entry['type'] == 'video'){
					$youtube = preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/", $entry['frame'], $yt_video);
					$vimeo = preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/", $entry['frame'], $vm_video);
					$dailymotion = preg_match("/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/", $entry['frame'], $dm_video);

					if($youtube == true || $vimeo == true || $dailymotion == true){
						if($youtube == true && strlen($yt_video[1]) == 11){
							$TEMP['!frame'] = '<iframe src="https://www.youtube.com/embed/'.$yt_video[1].'" width="100%" height="450" frameborder="0" allowfullscreen></iframe>';
						} else if($vimeo == true){
							$TEMP['!frame'] = '<iframe src="//player.vimeo.com/video/'.$vm_video[1].'" width="100%" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
						} else if($dailymotion == true){
							$TEMP['!frame'] = '<iframe src="//www.dailymotion.com/embed/video/'.$dm_video[2].'" width="100%" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
						}
					}
				}

				$TEMP['!id'] = $entry['id'];
				$TEMP['!title'] = $entry['title'];
				$TEMP['!body'] = $entry['body'];
				$TEMP['!type'] = $entry['type'];
				$TEMP['!eorder'] = $entry['eorder'];
				$TEMP['!esource'] = $entry['esource'];
				$TEMP['entries'] .= Specific::Maket('post/includes/entries');
			}
			Specific::DestroyMaket();

			$relateds = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ? AND id != ? AND status = "approved" ORDER BY RAND() LIMIT 3', $post['id'], $related_body)->fetchAll();

			if(!empty($relateds)){
				foreach ($relateds as $rl) {
					$TEMP['!title'] = $rl['title'];
					$TEMP['!url'] = Specific::Url($rl['slug']);
					$TEMP['!thumbnail'] = Specific::GetFile($rl['thumbnail'], 1, 's');
					$TEMP['!published_at'] = Specific::DateString($rl['published_at']);
					$TEMP['related_bottom'] .= Specific::Maket('post/includes/related-bottom');
				}
				Specific::DestroyMaket();
			}
			$content = Specific::Maket('post/includes/main');
			$noscrapping = Specific::NoScrapping($content, true);
			if($noscrapping['status'] == true){
				$deliver = array(
					'S' => 200,
					'ID' => $post['id'],
					'HT' => $noscrapping['content']
				);
			}
		}
	}
} else if($one == 'save'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);
	$post_id = Specific::Filter($_POST['post_id']);
	if(!empty($post_id) && is_numeric($post_id) && !empty($post_ids) && is_array($post_ids) && in_array($post_id, $post_ids)){
		if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE id = ? AND status = "approved"', $post_id)->fetchArray(true) > 0){
			if($dba->query('SELECT COUNT(*) FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post_id)->fetchArray(true) > 0){
				if($dba->query('DELETE FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post_id)->returnStatus()){
					$deliver = array(
						'S' => 200,
						'AC' => 'delete'
					);
				}
			} else {
				if($dba->query('INSERT INTO '.T_SAVED.' (user_id, post_id, created_at) VALUES (?, ?, ?)', $TEMP['#user']['id'], $post_id, time())->returnStatus()){
					$deliver = array(
						'S' => 200,
						'AC' => 'save'
					);
				}
			}
		}
	}
} else if($one == 'entry'){
	$type = Specific::Filter($_POST['type']);
	$types = array('text', 'image', 'video', 'embed', 'tweet', 'soundcloud', 'facebookpost', 'instagrampost');
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
} else if($one == 'get-frame'){
	$url = Specific::Filter($_POST['url']);
	$type = Specific::Filter($_POST['type']);
	if(!empty($url)){
		if(filter_var($url, FILTER_VALIDATE_URL)){
			if(!empty($type)){
				if(in_array($type, array('tweet', 'soundcloud', 'instagrampost', 'facebookpost'))){
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
							$number = rand(111,999);
							$deliver = array(
								'S' => 200,
								'HT' => '<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="' . $url . '?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:658px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="' . $url . '?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/p/' . $url . '/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by a user</a></p></div></blockquote><script src="//platform.instagram.com/'.$TEMP['#lang'].'/embeds.js"></script>'
							);
						}
					} else {
						$tweet = preg_match('/^https?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/', $url);
						$soundcloud = preg_match('/^(?:(https?):\/\/)?(?:(?:www|m)\.)?(soundcloud\.com|snd\.sc)\/[a-z0-9](?!.*?(-|_){2})[\w-]{1,23}[a-z0-9](?:\/.+)?$/', $url);
						if($tweet == true || $soundcloud == true){
							if($tweet == true && $type == 'tweet'){
								$api = 'https://api.twitter.com/1/statuses/oembed.json?url=';
							} else if($soundcloud == true && $type == 'soundcloud'){
								$api = 'https://soundcloud.com/oembed?format=json&url=';
							}
							$json = Specific::getContentUrl("{$api}{$url}");
							$json = json_decode($json, true);

							if(!isset($json['error']) && !isset($json['errors'])){
								if(!empty($json)){
									if(isset($json['title'])){
										$deliver['TT'] = $json['title'];
									}
									$deliver['S'] = 200;
									$deliver['HT'] = $json['html'];
								}
							}
						}
					}
				} else if($type == 'video'){
					$youtube = preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/", $url, $yt_video);
					$vimeo = preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/", $url, $vm_video);
					$dailymotion = preg_match("/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/", $url, $dm_video);

					if($youtube == true || $vimeo == true || $dailymotion == true){
						if($youtube == true && strlen($yt_video[1]) == 11){
							$html = '<iframe src="https://www.youtube.com/embed/'.$yt_video[1].'" width="100%" height="450" frameborder="0" allowfullscreen></iframe>';
						} else if($vimeo == true){
							$html = '<iframe src="//player.vimeo.com/video/'.$vm_video[1].'" width="100%" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
						} else if($dailymotion == true){
							$html = '<iframe src="//www.dailymotion.com/embed/video/'.$dm_video[2].'" width="100%" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
						}
						$deliver['S'] = 200;
						$deliver['HT'] = $html;
					}
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
} else if($one == 'get-image'){
	$url = Specific::Filter($_POST['url']);
	if(!empty($url)){
		if(filter_var($url, FILTER_VALIDATE_URL)){
			if(exif_imagetype($url) != false){
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
} else if($one == 'delete'){
	$post_id = Specific::Filter($_POST['post_id']);

	if(!empty($post_id) && is_numeric($post_id)){
		$user_id = $dba->query('SELECT user_id FROM '.T_POST.' WHERE id = ?', $post_id)->fetchArray(true);
		if(Specific::IsOwner($user_id)){
			if($dba->query('UPDATE '.T_POST.' SET status = "deleted", deleted_at = ? WHERE id = ?', time(), $post_id)->returnStatus()){
				$_SESSION['post_deleted'] = $post_id;
				$deliver = array(
					'S' => 200,
					'LK' => Specific::Url('?show-alert=deleted_post')
				);
			}
		}
	}
}
?>