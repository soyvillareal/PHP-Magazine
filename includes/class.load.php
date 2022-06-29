<?php
class Load {
	public static function LastNews($home_ids = array()){
		global $dba, $TEMP;

		$data = array(
			'last_news_one' => false,
			'last_news_two' => false,
			'last_news_one_html' => '',
			'last_news_two_html' => '',
			'home_ids' => $home_ids
		);

		$last_news_one_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') ORDER BY published_at ASC LIMIT 10')->fetchAll();

		if(!empty($last_news_one_sql)){
			$data['last_news_one'] = true;
			foreach ($last_news_one_sql as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);
				$data['last_news_one_html'] .= Specific::Maket('home/includes/last-news-one');
				$home_ids[] = $post['id'];
			}
			Specific::DestroyMaket();
		}

		$last_news_two_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') ORDER BY RAND() ASC LIMIT 5')->fetchAll();

		if(!empty($last_news_two_sql)){
			$data['last_news_two'] = true;
			$category_left = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $last_news_two_sql[0]['category_id'])->fetchArray();
			$TEMP['#type_left'] = $last_news_two_sql[0]['type'];

			$TEMP['id_left'] = $last_news_two_sql[0]['id'];
			$TEMP['title_left'] = $last_news_two_sql[0]['title'];
			$TEMP['description_left'] = $last_news_two_sql[0]['description'];
			$TEMP['category_left'] = $category_left['name'];
			$TEMP['category_slug_left'] = $category_left['slug'];
			$TEMP['url_left'] = Specific::Url($last_news_two_sql[0]['slug']);
			$TEMP['thumbnail_left'] = Specific::GetFile($last_news_two_sql[0]['thumbnail'], 1, 's');
			$TEMP['published_date_left'] = date('c', $last_news_two_sql[0]['published_at']);
			$TEMP['published_at_left'] = Specific::DateString($last_news_two_sql[0]['published_at']);
			$home_ids[] = $last_news_two_sql[0]['id'];

			$category_right = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $last_news_two_sql[1]['category_id'])->fetchArray();
			$TEMP['#type_right'] = $last_news_two_sql[1]['type'];

			$TEMP['id_right'] = $last_news_two_sql[1]['id'];
			$TEMP['title_right'] = $last_news_two_sql[1]['title'];
			$TEMP['description_right'] = $last_news_two_sql[1]['description'];
			$TEMP['category_right'] = $category_right['name'];
			$TEMP['category_slug_right'] = $category_right['slug'];
			$TEMP['url_right'] = Specific::Url($last_news_two_sql[1]['slug']);
			$TEMP['thumbnail_right'] = Specific::GetFile($last_news_two_sql[1]['thumbnail'], 1, 's');
			$TEMP['published_date_right'] = date('c', $last_news_two_sql[1]['published_at']);
			$TEMP['published_at_right'] = Specific::DateString($last_news_two_sql[1]['published_at']);
			$home_ids[] = $last_news_two_sql[1]['id'];

			unset($last_news_two_sql[0]);
			unset($last_news_two_sql[1]);
			$last_news_two_sql = array_values($last_news_two_sql);

			foreach ($last_news_two_sql as $post) {
				$category_middle = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id_middle'] = $post['id'];
				$TEMP['!type_middle'] = $post['type'];

				$TEMP['!title_middle'] = $post['title'];
				$TEMP['!description_middle'] = $post['description'];
				$TEMP['!category_middle'] = $category_middle['name'];
				$TEMP['!category_slug_middle'] = $category_middle['slug'];
				$TEMP['!url_middle'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail_middle'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date_middle'] = date('c', $post['published_at']);
				$TEMP['!published_at_middle'] = Specific::DateString($post['published_at']);

				$TEMP['last_news_three'] .= Specific::Maket('home/includes/last-news-middle');
				$home_ids[] = $post['id'];
			}
			Specific::DestroyMaket();
			$data['last_news_two_html'] .= Specific::Maket('home/includes/last-news-two');
			$data['home_ids'] = $home_ids;
		}

		return $data;
	}


	public static function RecommendedVideos($home_ids = array()){
		global $dba, $TEMP;

		$data = array(
			'main_recommended_videos' => false,
			'main_recommended_videos_html' => '',
			'home_ids' => $home_ids
		);

		$main_recommended_videos_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') AND type = "video" ORDER BY RAND() ASC LIMIT 18')->fetchAll();

		if(!empty($main_recommended_videos_sql)){
			$data['main_recommended_videos'] = true;
			foreach ($main_recommended_videos_sql as $post) {
				$category_middle = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!category'] = $category_middle['name'];
				$TEMP['!category_slug'] = $category_middle['slug'];
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);

				$data['main_recommended_videos_html'] .= Specific::Maket('home/includes/main-recommended-videos');
				$home_ids[] = $post['id'];
			}
			Specific::DestroyMaket();
			$data['home_ids'] = $home_ids;
		}

		return $data;
	}

	public static function Post($post = array(), $is_loaded = false){
		global $dba, $TEMP;

		if(!empty($post)){
			$post_ids = array();

			$title = $post['title'];
			$url = Specific::Url($post['slug']);
			$post_views = $post['views'];
			$avatar = Specific::Data($post['user_id'], array('avatar'));
			$category = $dba->query('SELECT id, name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
			$fingerprint = Specific::Fingerprint($TEMP['#user']['id']);

			if($dba->query('SELECT COUNT(*) FROM '.T_VIEW.' WHERE post_id = ? AND fingerprint = ?', $post['id'], $fingerprint)->fetchArray(true) == 0){
				$post_views = ($post['views']+1);
				if($dba->query('INSERT INTO '.T_VIEW.' (post_id, fingerprint, created_at) VALUES (?, ?, ?)', $post['id'], $fingerprint, time())->returnStatus()){
					$dba->query('UPDATE '.T_POST.' SET views = ? WHERE id = ?', $post_views, $post['id']);
				};
			}


			$TEMP['title'] = $title;
			$TEMP['category'] = $category['name'];
			$TEMP['category_slug'] = $category['slug'];
			$TEMP['views'] = number_format($post_views);
			$TEMP['post_id'] = $post['id'];
			$TEMP['category_id'] = $category['id'];
			$TEMP['author_name'] = Specific::Data($post['user_id'], array('username'));
			$TEMP['author_url'] = Specific::ProfileUrl($TEMP['author_name']);
			$TEMP['author_avatar'] = $avatar['avatar_s'];
			$TEMP['thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 'b');
			$TEMP['url'] = $url;

			$TEMP['title_encoded'] = urlencode($title);
			$TEMP['description_encoded'] = urlencode($post['description']);
			$TEMP['url_encoded'] = urlencode($url);
			$TEMP['slug_encoded'] = urlencode($post['slug']);
			$TEMP['published_date'] = date('c', $post['published_at']);
			$TEMP['updated_date'] = date('c', $post['updated_at']);
			$TEMP['published_at'] = Specific::DateString($post['published_at']);


			$TEMP['#is_owner'] = Specific::IsOwner($post['user_id']);
			$TEMP['#is_loaded'] = $is_loaded;
			$TEMP['#type'] = $post['type'];

			$post_sources = json_decode($post['post_sources'], true);
			if(!empty($post_sources)){
				$TEMP['#post_sources'] = array();
				if(count($post_sources) > 1){
					foreach ($post_sources as $key => $source) {
						if($key != end(array_keys($post_sources))){
							$TEMP['#post_sources'][] = " <a class='btn-noway color-blue hover-underline' href='{$source['source']}' target='_blank'>{$source['name']}</a>";
						}
					}
					$last_source = end($post_sources);
					$TEMP['#post_sources'] = implode(',', $TEMP['#post_sources']);
					$TEMP['#post_sources'] = "<b>{$TEMP['#word']['consulted_sources']}:</b> {$TEMP['#post_sources']} {$TEMP['#word']['and']} <a class='btn-noway color-blue hover-underline' href='{$last_source['source']}' target='_blank'>{$last_source['name']}</a>";
				} else {
					$TEMP['#post_sources'] = "<b>{$TEMP['#word']['consulted_source']}:</b> <a class='btn-noway color-blue hover-underline' href='{$post_sources[0]['source']}' target='_blank'>{$post_sources[0]['name']}</a>";
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
			$TEMP['#entry_types'] = array();
			$TEMP['#saved'] = $dba->query('SELECT COUNT(*) FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post['id'])->fetchArray(true);

			$entries = $dba->query('SELECT * FROM '.T_ENTRY.' WHERE post_id = ? ORDER BY eorder', $post['id'])->fetchAll();
			foreach ($entries as $key => $entry) {
				$TEMP['#entry_types'][] = $entry['type'];
			}

			$tags = $dba->query('SELECT * FROM '.T_LABEL.' t WHERE (SELECT label_id FROM '.T_TAG.' WHERE post_id = ? AND label_id = t.id) = id', $post['id'])->fetchAll();
			$keywords = array();
			foreach ($tags as $tag) {
				$keywords[] = $tag['name'];

				$TEMP['!name'] = $tag['name'];
				$TEMP['!url'] = Specific::Url("{$TEMP['#r_tag']}/{$tag['slug']}");
				$TEMP['tags'] .= Specific::Maket('post/includes/tags');
			}
			Specific::DestroyMaket();

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
	                		$recommended_bo = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ? AND category_id = ? AND status = "approved" ORDER BY RAND()', $post['id'], $post['category_id'])->fetchArray();
	                		$TEMP['!title'] = $recommended_bo['title'];
	                		$TEMP['!url'] = Specific::Url($recommended_bo['slug']);
							$TEMP['!thumbnail'] = Specific::GetFile($recommended_bo['thumbnail'], 1, 's');
	                    	$entry['body'] .= Specific::Maket('post/includes/recommended-body');
	                		$post_ids[] = $recommended_bo['id'];
			            } else if($i == 2 || $i == ($j+4)){
			                $j = $i;
			                $entry['body'] .= Specific::Maket('post/includes/advertisement-body');
			            }
			            $entry['body'] .= $paragraph[$i];
			        }
				} else if($entry['type'] == 'image'){
					$TEMP['!frame'] = Specific::GetFile($entry['frame'], 3);
				} else if($entry['type'] == 'video'){
					$frame = Specific::IdentifyFrame($entry['frame']);
					$TEMP['!frame'] = $frame['html'];
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

			$noin = '';
			if(!empty($post_ids)){
				$noin = ' AND id NOT IN ('.implode(',', $post_ids).')';
			}
			$relateds = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ?'.$noin.' AND status = "approved" ORDER BY RAND() LIMIT 3', $post['id'])->fetchAll();
			if(!empty($relateds)){
				foreach ($relateds as $rl) {
					$TEMP['!title'] = $rl['title'];
					$TEMP['!url'] = Specific::Url($rl['slug']);
					$TEMP['!thumbnail'] = Specific::GetFile($rl['thumbnail'], 1, 's');
					$TEMP['!published_at'] = Specific::DateString($rl['published_at']);
					$TEMP['related_bottom'] .= Specific::Maket('post/includes/related-bottom');
					$post_ids[] = $rl['id'];
				}
				Specific::DestroyMaket();
			}

			$html = Specific::Maket('post/includes/main');

			return array(
				'return' => true,
				'html' => $html,
				'keywords' => $keywords,
				'post_ids' => $post_ids
			);
		}

		return array(
			'return' => false
		);
	}


	public static function Search($data = array(), $search_ids = array()){
		global $dba, $TEMP;

		$keyword = $data['keyword'];
		$date = $data['date'];
		$category = $data['category'];
		$author = $data['author'];
		$sort = $data['sort'];
		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'info' => '',
			'search_ids' => $search_ids
		);


		if(!empty($search_ids)){
			$query = ' AND id NOT IN ('.implode(',', $search_ids).')';
		}

		if(!empty($keyword)){
			$query .= ' AND (title LIKE "%'.$keyword.'%" OR description LIKE "%'.$keyword.'%")';
		}

		if(in_array($date, array('all', 'today', 'this_week', 'this_month', 'this_year'))){
			if ($date == 'today') {
				$time = time()-(60*60*24);
				$query .= " AND created_at >= ".$time;
			} else if ($date == 'this_week') {
				$time = time()-(60*60*24*7);
				$query .= " AND created_at >= ".$time;
			} else if ($date == 'this_month') {
				$time = time()-(60*60*24*30);
				$query .= " AND created_at >= ".$time;
			} else if ($date == 'this_year') {
				$time = time()-(60*60*24*365);
				$query .= " AND created_at >= ".$time;
			}
		}
				
		if(in_array($category, $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false)) && $category != 'all'){
			$query .= " AND category_id = {$category}";
		}

		if(in_array($author, $dba->query('SELECT id FROM '.T_USER.' WHERE role = "publisher" OR role = "moderator" OR role = "admin"')->fetchAll(false)) && $author != 'all'){
			$query .= " AND user_id = {$author}";
		}
					
		if(in_array($sort, array('newest', 'oldest', 'views'))){
			if($sort == 'newest'){
				$query .= " ORDER BY published_at DESC";
			} else if($sort == 'oldest'){
				$query .= " ORDER BY published_at ASC";
			} else if($sort == 'views'){
				$query .= " ORDER BY views DESC";
			}
		}

		$search_result = $dba->query('SELECT * FROM '.T_POST.' WHERE status = "approved"'.$query.' LIMIT 10')->fetchAll();

		if(!empty($search_result)){
			$search_count = $dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE status = "approved"'.$query)->fetchArray(true);
			foreach ($search_result as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);

				$html .= Specific::Maket("includes/news-result");
				$search_ids[] = $post['id'];
			}
			Specific::DestroyMaket();

			$data = array(
				'return' => true,
				'html' => $html,
				'info' => "<b>{$search_count}</b> {$TEMP['#word']['results_related_to']} ".'<b>"'.$keyword.'"</b>',
				'search_ids' => $search_ids
			);
		} else {
			$TEMP['keyword'] = $keyword;
			$not_found = !empty($keyword) ? 'no-result-for' : 'no-result';
			$html = Specific::Maket("not-found/{$not_found}");

			$data = array(
				'return' => false,
				'html' => $html
			);
		}

		return $data;
	}

}
?>