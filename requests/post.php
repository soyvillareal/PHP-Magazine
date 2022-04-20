<?php
if($one == 'load'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = json_decode($post_ids);
	$category_id = Specific::Filter($_POST['category_id']);

	if(!empty($category_id) && is_numeric($category_id) && !empty($post_ids) && is_array($post_ids)){
		$post = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND id NOT IN (?) AND status = "approved" ORDER BY RAND()', $category_id, implode(',', $post_ids))->fetchArray();
		if(!empty($post)){
			$title = $post['title'];
			$url = Specific::Url($post['slug']);
			$post_views = $post['views'];
			$avatar = Specific::Data($post['user_id'], array('avatar'));
			$fingerprint = Specific::Fingerprint($TEMP['#user']['id']);

			if($dba->query('SELECT COUNT(*) FROM '.T_VIEW.' WHERE post_id = ? AND fingerprint = ?', $post['id'], $fingerprint)->fetchArray(true) == 0){
				$post_views = ($post['views']+1);
				if($dba->query('INSERT INTO '.T_VIEW.' (post_id, fingerprint, created_at) VALUES (?, ?, ?)', $post['id'], $fingerprint, time())->returnStatus()){
					$dba->query('UPDATE '.T_POST.' SET views = ? WHERE id = ?', $post['id'], $post_views);
				};
			}

			$TEMP['title'] = $title;
			$TEMP['category'] = $dba->query('SELECT name FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray(true);
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
			$TEMP['#current_url'] = $post['slug'];
			$TEMP['#thumb_source'] = $post['thumb_source'];
			$TEMP['#entry_types'] = array();
			$TEMP['#saved'] = $dba->query('SELECT COUNT(*) FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post['id'])->fetchArray(true);

			$entries = $dba->query('SELECT * FROM '.T_ENTRY.' WHERE post_id = ?', $post['id'])->fetchAll();
			foreach ($entries as $key => $entry) {
				if($entry['type'] == 'video' && strpos($entry['frame'], 'facebook')){
					$entry['type'] = 'facebookvideo';
				}
				$TEMP['#entry_types'][] = $entry['type'];
			}

			$tags = $dba->query('SELECT * FROM '.T_LABEL.' t WHERE (SELECT label_id FROM '.T_TAG.' WHERE post_id = ? AND label_id = t.id) = id', $post['id'])->fetchAll();
			foreach ($tags as $tag) {
				$TEMP['!name'] = $tag['name'];
				$TEMP['!url'] = Specific::Url("{$TEMP['#r_tag']}/{$tag['slug']}");
				$TEMP['tags'] .= Specific::Maket('post/includes/tags');
			}
			Specific::DestroyMaket();

			$dont = -1;
			$related_body = 0;
			foreach ($entries as $key => $entry) {
				if($key != $dont){
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
						$new_key = $key + 1;
						$next_entry = $entries[$new_key];
						if($next_entry['type'] == 'image'){
							$TEMP['!next_title'] = $next_entry['title'];
							$TEMP['!next_frame'] = Specific::GetFile($next_entry['frame'], 3);
							$TEMP['!next_source'] = $next_entry['source'];
							$dont = $new_key;
						}
						$TEMP['!frame'] = Specific::GetFile($entry['frame'], 3);
					} else if($entry['type'] == 'instagram'){
						$TEMP['!rand_one'] = rand(1, 9999);
						$TEMP['!rand_two'] = rand();
					}

					$TEMP['!id'] = $entry['id'];
					$TEMP['!title'] = $entry['title'];
					$TEMP['!body'] = $entry['body'];
					$TEMP['!order'] = $entry['order'];
					$TEMP['!type'] = $entry['type'];
					$TEMP['!source'] = $entry['source'];
					$TEMP['entries'] .= Specific::Maket('post/includes/entries');
				}
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
}
?>