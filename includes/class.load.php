<?php
class Load {
	public static function LastPosts($limit, $home_ids = array()){
		global $dba, $TEMP, $RUTE;

		$data = array(
			'last_posts_one' => false,
			'last_posts_two' => false,
			'last_posts_one_html' => '',
			'last_posts_two_html' => '',
			'home_ids' => $home_ids
		);

		$query = '';
		if(!empty($home_ids)){
			$query = ' AND id NOT IN ('.implode(',', $home_ids).')';
		}
		
		$widget = Specific::GetWidget('hload');
		if($widget['return']){
			$TEMP['advertisement_hlad'] = $widget['html'];
		}

		$last_posts_one_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].')'.$query.' AND status = "approved" ORDER BY published_at ASC LIMIT '.$limit)->fetchAll();

		if(!empty($last_posts_one_sql)){
			$data['last_posts_one'] = true;
			foreach ($last_posts_one_sql as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);
				$data['last_posts_one_html'] .= Specific::Maket('home/includes/last-posts-one');
				$home_ids[] = $post['id'];
			}
			Specific::DestroyMaket();
		}

		if(count($last_posts_one_sql) >= 8){
			$last_posts_two_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].')'.$query.' AND status = "approved" ORDER BY RAND() ASC LIMIT 5')->fetchAll();

			if(!empty($last_posts_two_sql)){
				$data['last_posts_two'] = true;
				$category_left = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $last_posts_two_sql[0]['category_id'])->fetchArray();
				$TEMP['#type_left'] = $last_posts_two_sql[0]['type'];

				$TEMP['id_left'] = $last_posts_two_sql[0]['id'];
				$TEMP['title_left'] = $last_posts_two_sql[0]['title'];
				$TEMP['description_left'] = $last_posts_two_sql[0]['description'];
				$TEMP['category_left'] = $TEMP['#word']["category_{$category_left['name']}"];
				$TEMP['category_slug_left'] = Specific::Url("{$RUTE['#r_category']}/{$category_left['slug']}");
				$TEMP['url_left'] = Specific::Url($last_posts_two_sql[0]['slug']);
				$TEMP['thumbnail_left'] = Specific::GetFile($last_posts_two_sql[0]['thumbnail'], 1, 's');
				$TEMP['published_date_left'] = date('c', $last_posts_two_sql[0]['published_at']);
				$TEMP['published_at_left'] = Specific::DateString($last_posts_two_sql[0]['published_at']);
				$home_ids[] = $last_posts_two_sql[0]['id'];

				$category_right = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $last_posts_two_sql[1]['category_id'])->fetchArray();
				$TEMP['#type_right'] = $last_posts_two_sql[1]['type'];

				$TEMP['id_right'] = $last_posts_two_sql[1]['id'];
				$TEMP['title_right'] = $last_posts_two_sql[1]['title'];
				$TEMP['description_right'] = $last_posts_two_sql[1]['description'];
				$TEMP['category_right'] = $TEMP['#word']["category_{$category_right['name']}"];
				$TEMP['category_slug_right'] = Specific::Url("{$RUTE['#r_category']}/{$category_right['slug']}");
				$TEMP['url_right'] = Specific::Url($last_posts_two_sql[1]['slug']);
				$TEMP['thumbnail_right'] = Specific::GetFile($last_posts_two_sql[1]['thumbnail'], 1, 's');
				$TEMP['published_date_right'] = date('c', $last_posts_two_sql[1]['published_at']);
				$TEMP['published_at_right'] = Specific::DateString($last_posts_two_sql[1]['published_at']);
				$home_ids[] = $last_posts_two_sql[1]['id'];

				unset($last_posts_two_sql[0]);
				unset($last_posts_two_sql[1]);
				$last_posts_two_sql = array_values($last_posts_two_sql);

				foreach ($last_posts_two_sql as $post) {
					$category_middle = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
					$TEMP['!id_middle'] = $post['id'];
					$TEMP['!type_middle'] = $post['type'];

					$TEMP['!title_middle'] = $post['title'];
					$TEMP['!description_middle'] = $post['description'];
					$TEMP['!category_middle'] = $TEMP['#word']["category_{$category_middle['name']}"];
					$TEMP['!category_slug_middle'] = Specific::Url("{$RUTE['#r_category']}/{$category_middle['slug']}");
					$TEMP['!url_middle'] = Specific::Url($post['slug']);
					$TEMP['!thumbnail_middle'] = Specific::GetFile($post['thumbnail'], 1, 's');
					$TEMP['!published_date_middle'] = date('c', $post['published_at']);
					$TEMP['!published_at_middle'] = Specific::DateString($post['published_at']);

					$TEMP['last_posts_three'] .= Specific::Maket('home/includes/last-posts-middle');
					$home_ids[] = $post['id'];
				}
				Specific::DestroyMaket();
				$data['last_posts_two_html'] .= Specific::Maket('home/includes/last-posts-two');
			}
			$data['home_ids'] = $home_ids;
		}

		return $data;
	}


	public static function RecommendedVideos($home_ids = array()){
		global $dba, $TEMP, $RUTE;

		$data = array(
			'main_recommended_videos' => false,
			'main_recommended_videos_html' => '',
			'home_ids' => $home_ids
		);

		$main_recommended_videos_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') AND type = "video" AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY RAND() ASC LIMIT 18')->fetchAll();

		if(!empty($main_recommended_videos_sql)){
			$data['main_recommended_videos'] = true;
			foreach ($main_recommended_videos_sql as $post) {
				$category_middle = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category_middle['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category_middle['slug']}");
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

	public static function Post($post = array(), $is_loaded = false, $is_amp = false){
		global $dba, $TEMP, $RUTE;

		if(!empty($post)){
			$post_ids = array();
			$root = 'post';
			$TEMP['#is_amp'] = $is_amp;
			if($is_amp){
                $nt_posts = $dba->query('SELECT * FROM '.T_POST.' WHERE id <> ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" LIMIT 12', $post['id'])->fetchAll();
				if(!empty($nt_posts)){
					$next_page = array();
					foreach ($nt_posts as $nt_post) {
						$title = substr($nt_post['title'], 0, 75);
						$next_page[] = array(
							'image' => Specific::GetFile($nt_post['thumbnail'], 1, 's'),
							'title' => "{$title}...",
							'url' => Specific::Url("amp/{$nt_post['slug']}")
						);
					}
					$TEMP['#next_page'] = json_encode($next_page);
				}
				$root = 'amp';
				$TEMP['amp_url'] = Specific::Url("amp/{$post['slug']}");
			}

			$title = $post['title'];
			$url = Specific::Url($post['slug']);
			$post_views = $post['views'];
			$avatar = Specific::Data($post['user_id'], array('avatar'));
			$category = $dba->query('SELECT id, name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();


			$user_id = 0;
			$fingerprint = Specific::Fingerprint();
			$view_exists = $dba->query('SELECT COUNT(*) FROM '.T_VIEW.' WHERE fingerprint = ? AND post_id = ?', $fingerprint, $post['id'])->fetchArray(true);

			if($TEMP['#loggedin'] == true){
				$fingerprint = NULL;
				$user_id = $TEMP['#user']['id'];
				$view_exists = $dba->query('SELECT COUNT(*) FROM '.T_VIEW.' WHERE user_id = ? AND post_id = ?', $user_id, $post['id'])->fetchArray(true);
			}
			if($view_exists == 0){
				$post_views = ($post['views']+1);
				if($dba->query('INSERT INTO '.T_VIEW.' (user_id, post_id, fingerprint, created_at) VALUES (?, ?, ?, ?)', $user_id, $post['id'], $fingerprint, time())->returnStatus()){
					$dba->query('UPDATE '.T_POST.' SET views = ? WHERE id = ?', $post_views, $post['id']);
				};
			}


			$TEMP['title'] = $title;
			$TEMP['category'] = $TEMP['#word']["category_{$category['name']}"];
			$TEMP['category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
			$TEMP['views'] = Specific::NumberShorten($post_views);
			$TEMP['likes'] = Specific::NumberShorten($post['likes']);
			$TEMP['dislikes'] = Specific::NumberShorten($post['dislikes']);
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
			$TEMP['published_date'] = date('c', $post['published_at']);
			$TEMP['published_at'] = Specific::DateString($post['published_at']);
			$TEMP['#update_date'] = $post['updated_at'];
			$TEMP['updated_date'] = date('c', $TEMP['#update_date']);
			$TEMP['updated_at'] = Specific::DateString($post['updated_at']);

			$TEMP['#likes_active'] = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"', $TEMP['#user']['id'], $post['id'])->fetchArray(true);
			$TEMP['#dislikes_active'] = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"', $TEMP['#user']['id'], $post['id'])->fetchArray(true);
			$TEMP['#is_owner'] = Specific::IsOwner($post['user_id']);
			$TEMP['#is_loaded'] = $is_loaded;
			$TEMP['#published_at'] = $post['published_at'];
			$TEMP['#status'] = $post['status'];
			$TEMP['#type'] = $post['type'];

			$post_sources = json_decode($post['post_sources'], true);
			if(!empty($post_sources)){
				$TEMP['#post_sources'] = array();
				if(count($post_sources) > 1){
					foreach ($post_sources as $key => $source) {
						if($key != end(array_keys($post_sources))){
							$TEMP['#post_sources'][] = " <span class='display-inline-block'><a class='btn-noway color-blue hover-underline' href='{$source['source']}' target='_blank'>{$source['name']}</a></span>";
						}
					}
					$last_source = end($post_sources);
					$last_rtl_psource = $last_ltr_psource = '';
					if($TEMP['#dir'] == 'rtl'){
						$last_rtl_psource = " {$TEMP['#word']['and']}";
					} else {
						$last_ltr_psource = "{$TEMP['#word']['and']} ";
					}
					$TEMP['#post_sources'] = implode(',', $TEMP['#post_sources']);
					$TEMP['#post_sources'] = "<span class='color-black font-georgia font-low font-bold'>{$TEMP['#word']['consulted_sources']}:</span> {$TEMP['#post_sources']} <span class='display-inline-block'>{$last_ltr_psource}<a class='btn-noway color-blue hover-underline' href='{$source['source']}' target='_blank'>{$source['name']}</a>{$last_rtl_psource}</span>";
				} else {
					$TEMP['#post_sources'] = "<span class='color-black font-georgia font-low font-bold'>{$TEMP['#word']['consulted_source']}:</span> <span class='display-inline-block'><a class='btn-noway color-blue hover-underline' href='{$post_sources[0]['source']}' target='_blank'>{$post_sources[0]['name']}</a></span>";
				}
			}

			$thumb_sources = json_decode($post['thumb_sources'], true);
			if(!empty($thumb_sources)){
				$TEMP['#thumb_sources'] = array();
				if(count($thumb_sources) > 1){
					foreach ($thumb_sources as $key => $source) {
						if($key != end(array_keys($thumb_sources))){
							$TEMP['#thumb_sources'][] = " <span class='display-inline-block'><a class='btn-noway color-blue hover-button' href='{$source['source']}' target='_blank'>{$source['name']}</a></span>";
						}
					}
					$last_source = end($thumb_sources);
					$last_rtl_tsource = $last_ltr_tsource = '';
					if($TEMP['#dir'] == 'rtl'){
						$last_rtl_tsource = " {$TEMP['#word']['and']}";
					} else {
						$last_ltr_tsource = "{$TEMP['#word']['and']} ";
					}
					$TEMP['#thumb_sources'] = implode(',', $TEMP['#thumb_sources']);
					$TEMP['#thumb_sources'] = "<span class='color-wwhite font-georgia font-low font-bold'>{$TEMP['#word']['images_taken_from']}:</span> {$TEMP['#thumb_sources']} <span class='display-inline-block'>{$last_ltr_tsource}<a class='btn-noway color-blue hover-button' href='{$last_source['source']}' target='_blank'>{$last_source['name']}</a>{$last_rtl_tsource}</span>";
				} else {
					$TEMP['#thumb_sources'] = "<span class='color-wwhite font-georgia font-low font-bold'>{$TEMP['#word']['image_taken_from']}:</span> <span class='display-inline-block'><a class='btn-noway color-blue hover-button' href='{$thumb_sources[0]['source']}' target='_blank'>{$thumb_sources[0]['name']}</a></span>";
				}
			}

			$TEMP['#current_url'] = $post['slug'];
			$TEMP['#entry_types'] = array();
			$TEMP['#saved'] = $dba->query('SELECT COUNT(*) FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post['id'])->fetchArray(true);
			$TEMP['#count_comments'] = $dba->query('SELECT COUNT(*) FROM '.T_COMMENTS.' WHERE post_id = ?', $post['id'])->fetchArray(true);

			$entries = $dba->query('SELECT * FROM '.T_ENTRY.' WHERE post_id = ? ORDER BY eorder', $post['id'])->fetchAll();
			foreach ($entries as $key => $entry) {
				$TEMP['#entry_types'][] = $entry['type'];
			}

			$tags = $dba->query('SELECT * FROM '.T_LABEL.' t WHERE (SELECT label_id FROM '.T_TAG.' WHERE post_id = ? AND label_id = t.id) = id', $post['id'])->fetchAll();
			$keywords = array();
			foreach ($tags as $tag) {
				$keywords[] = $tag['name'];

				$TEMP['!name'] = $tag['name'];
				$TEMP['!url'] = Specific::Url("{$RUTE['#r_tag']}/{$tag['slug']}");
				$TEMP['tags'] .= Specific::Maket('includes/post-amp/tags');
			}
			Specific::DestroyMaket();

			$max_cimages = $carousel_json = array();
			foreach ($entries as $key => $entry) {
				$TEMP['!frame'] = $entry['frame'];
				$TEMP['!id'] = $entry['id'];
				$TEMP['!title'] = $entry['title'];
				$TEMP['!esource'] = $entry['esource'];
				$TEMP['!type'] = $entry['type'];
				$TEMP['!eorder'] = $entry['eorder'];

				if($entry['type'] == 'text'){
			        $j = 0;
			        $paragraph = explode('</p>', $entry['body']);
			        $paragraph_count = count($paragraph);
			        $paragraph_body = round($paragraph_count/2);
			        $entry['body'] = '';

			        for ($i = 0; $i < $paragraph_count; $i++){
			            if($paragraph_count >= 6 && $i == $paragraph_body && $paragraph_body != ($j+4)){
	                		if($dba->query('SELECT COUNT(*) FROM '.T_RECOBO.' WHERE post_id = ?', $post['id'])->fetchArray(true)){
								$query = '';
								if(!empty($post_ids)){
									$query = ' AND recommended_id NOT IN ('.implode(',', $post_ids).')';
								}
								$recobo = $dba->query('SELECT * FROM '.T_RECOBO.' WHERE post_id = ?'.$query.' ORDER BY rorder DESC', $post['id'])->fetchArray();

								$recommended_bo = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ? AND status = "approved"', $recobo['recommended_id'])->fetchArray();
							} else {
								$recommended_bo = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ? AND category_id = ? AND status = "approved" ORDER BY RAND()', $post['id'], $post['category_id'])->fetchArray();
							}

							if(!empty($recommended_bo)){
								$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $recommended_bo['category_id'])->fetchArray();

		                		$TEMP['!re_title'] = $recommended_bo['title'];
								$TEMP['!re_category'] = $TEMP['#word']["category_{$category['name']}"];
								$TEMP['!re_category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
		                		$TEMP['!re_url'] = Specific::Url($recommended_bo['slug']);
								$TEMP['!re_thumbnail'] = Specific::GetFile($recommended_bo['thumbnail'], 1, 's');
								$TEMP['!re_published_date'] = date('c', $recommended_bo['published_at']);
								$TEMP['!re_published_at'] = Specific::DateString($recommended_bo['published_at']);
		                    	$entry['body'] .= Specific::Maket('includes/post-amp/recommended-body');
		                		$post_ids[] = $recommended_bo['id'];
		                	}
			            } else if($i == 2 || $i == ($j+4)){
			                $j = $i;
			                $widget = Specific::GetWidget('pbody', $root);
			                if($widget['return']){
			                	$entry['body'] .= $widget['html'];
			                }
			            }
			            $entry['body'] .= $paragraph[$i];
			        }
				} else if($entry['type'] == 'image'){
					$TEMP['!frame'] = Specific::GetFile($entry['frame'], 3);
				} else if($entry['type'] == 'carousel'){
					if(!empty($entry['frame'])){
						$carousel = json_decode($entry['frame'], true);
						foreach ($carousel as $key => $car) {
							$carousel[$key]['image'] = Specific::GetFile($car['image'], 3);
						}
					} else {
						$carousel = array(
							array(
								'image' => Specific::GetFile(NULL, 3),
								'caption' => 'Upss error'
							)
						);
					}

					$TEMP['!max_cimages'] = count($carousel);
					$TEMP['!frame'] = $carousel[0]['image'];
					$TEMP['!caption'] = $carousel[0]['caption'];

					$TEMP['!images'] = $carousel;
					$max_cimages[] = count($carousel);
					$carousel_json[] = json_encode($carousel);

					$TEMP['!carousel'] = Specific::Maket("{$root}/includes/carousel");
				} else if($entry['type'] == 'video'){
					$frame = Specific::IdentifyFrame($entry['frame'], false, $is_amp);
					$TEMP['!frame'] = $frame['html'];
				} else if($entry['type'] == 'embed'){
					$frame = json_decode($entry['frame'], true);
					$frame = Specific::MaketFrame($frame['url'], $frame['attrs'], true, $is_amp);

					$TEMP['!frame'] = $frame['html'];
				} else if($entry['type'] == 'instagrampost'){
					$TEMP['!omit_script'] = true;
					$TEMP['!url'] = $entry['frame'];
                	$TEMP['!frame'] = Specific::Maket('includes/create-edit-post/instagram-blockquote');
				} else if($entry['type'] == 'soundcloud'){
					if($is_amp){
						$soundcloud = preg_match('/tracks\/(.*?)(?:&|$)/s', urldecode($entry['frame']), $sc_frame);
						$TEMP['!frame'] = '<amp-soundcloud height="300" layout="fixed-height" data-trackid="'.$sc_frame[1].'" data-visual="true"></amp-soundcloud>';
					} else {
						$TEMP['!sc_url'] = $entry['frame'];
						$TEMP['!frame'] = Specific::Maket('includes/load-edit/soundcloud');;
					}
				} else if($entry['type'] == 'facebookpost'){
					if($is_amp){
						$TEMP['!frame'] = '<amp-facebook width="552" height="310" layout="responsive" data-href="'.$entry['frame'].'"></amp-facebook>';
					} else {
						$TEMP['!margin'] = true;
						$TEMP['!fb_url'] = $entry['frame'];
						$TEMP['!frame'] = Specific::Maket('includes/load-publisher-edit/facebook-post');
					}
				}

				if($is_amp){
					if($entry['type'] == 'tweet'){
						$twitter = preg_match('/[href="]*(?:https?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/([^\/\?]+))[^"]/', $entry['frame'], $tw_frame);
						$TEMP['!frame'] = '<amp-twitter width=390 height=50 layout="responsive" data-tweetid="'.$tw_frame[2].'"></amp-twitter>';
					} else if($entry['type'] == 'instagrampost'){
						$instagram = preg_match('/(?:(?:http|https):\/\/)?(?:www\.)?(?:instagram\.com|instagr\.am)\/(?:p|tv|reel)\/([A-Za-z0-9-_\.]+)/', $entry['frame'], $ig_frame);
						$TEMP['!frame'] = '<amp-instagram class="instagram-media display-block background-white border-rlow margin-b10" data-shortcode="'.$ig_frame[1].'" width="400" height="400" layout="responsive"></amp-instagram>';
					} else if($entry['type'] == 'tiktok'){
						$tiktok = preg_match('/(?<=cite=").*?(?=[\*"])/', $entry['frame'], $tk_frame);
						$TEMP['!frame'] = '<amp-tiktok width="325" height="575" data-src="'.$tk_frame[0].'" layout="responsive"></amp-tiktok>';
					} else if($entry['type'] == 'spotify'){
						$src = preg_match('/(?<=src=").*?(?=[\*"])/', $entry['frame'], $src_frame);
						$TEMP['!frame'] = '<amp-iframe class="border-r12px" width="100" height="100" frameborder="0" src="'.$src_frame[0].'" layout="responsive" sandbox="allow-scripts allow-same-origin"></amp-iframe>';
					}
				}

				$TEMP['!body'] = $entry['body'];

				$TEMP['entries'] .= Specific::Maket("includes/post-amp/entries");
			}
			Specific::DestroyMaket();

			$TEMP['#collaborators'] = $dba->query('SELECT user_id FROM '.T_COLLABORATOR.' WHERE post_id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') ORDER BY aorder ASC', $post['id'])->fetchAll();

			foreach ($TEMP['#collaborators'] as $au) {
				$user = Specific::Data($au['user_id'], array('username', 'avatar', 'about', 'facebook', 'twitter', 'instagram', 'main_sonet'));

				$TEMP['!id'] = $au['user_id'];
				$TEMP['!collab_name'] = $user['username'];
				$TEMP['!collab_url'] = Specific::ProfileUrl($user['username']);
				$TEMP['!collab_avatar'] = $user['avatar_s'];
				$TEMP['!about'] = $user['about'];
				$TEMP['!main_sonet'] = $user['main_sonet'];
				$TEMP['!social_media'] = $user[$TEMP['!main_sonet']];
				$TEMP['!url_csocial'] = "https://{$TEMP['!main_sonet']}.com/@{$TEMP['!social_media']}";
				if(in_array($TEMP['!main_sonet'], array('facebook', 'instagram'))){
					$TEMP['!url_csocial'] = "https://{$TEMP['!main_sonet']}.com/{$TEMP['!social_media']}";
				}

				$TEMP['collaborators'] .= Specific::Maket("includes/post-amp/collaborator");
			}
			Specific::DestroyMaket();

			$noin = '';
			if(!empty($post_ids)){
				$noin = ' AND id NOT IN ('.implode(',', $post_ids).')';
			}
			$TEMP['#relateds'] = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ?'.$noin.' AND status = "approved" ORDER BY RAND() LIMIT 3', $post['id'])->fetchAll();
			if(!empty($TEMP['#relateds'])){
				foreach ($TEMP['#relateds'] as $rl) {
					$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $rl['category_id'])->fetchArray();

					$TEMP['!title'] = $rl['title'];
					$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
					$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
					$TEMP['!url'] = Specific::Url($rl['slug']);
					$TEMP['!thumbnail'] = Specific::GetFile($rl['thumbnail'], 1, 's');
					$TEMP['!published_date'] = date('c', $rl['published_at']);
					$TEMP['!published_at'] = Specific::DateString($rl['published_at']);
					$TEMP['related_bottom'] .= Specific::Maket('includes/post-amp/related-bottom');
					$post_ids[] = $rl['id'];
				}
				Specific::DestroyMaket();
			}

			$TEMP['cusername'] = $TEMP['#word']['user_without_login'];
			$TEMP['avatar_cs'] = Specific::Url('/themes/default/images/users/default-holder-s.jpeg');
			if($TEMP['#loggedin'] == true){
				$TEMP['cusername'] = $TEMP['#user']['username'];
				$TEMP['avatar_cs'] = $TEMP['#user']['avatar_s'];
			}
			$comment_ids = array();

			$TEMP['#has_cfeatured'] = false;
			$TEMP['#featured_cid'] = Specific::Filter($_GET[$RUTE['#p_comment_id']]);
			$featured_comment = Specific::FeaturedComment($TEMP['#featured_cid']);
			if($featured_comment['return']){
				$TEMP['comments'] .= $featured_comment['html'];
				$comment_ids[] = $TEMP['#featured_cid'];
				$TEMP['#has_cfeatured'] = true;
			}

			$TEMP['#has_rfeatured'] = false;
			$TEMP['#featured_rid'] = Specific::Filter($_GET[$RUTE['#p_reply_id']]);
			$featured_reply = Specific::FeaturedComment($TEMP['#featured_rid'], 'reply');
			if($featured_reply['return']){
				$TEMP['comments'] .= $featured_reply['html'];
				$comment_ids[] = $featured_reply['id'];
				$TEMP['#has_rfeatured'] = true;
			}

			$comments = Specific::Comments($post['id'], 'recent', $comment_ids);
			if($comments['return']){
				$TEMP['comments'] .= $comments['html'];
			}
			

			$TEMP['max_cimages'] = implode(',', $max_cimages);
			$TEMP['carousel_json'] = implode(',', $carousel_json);

			$html = Specific::Maket("{$root}/includes/main");

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
		global $dba, $TEMP, $RUTE;

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

		if(in_array($date, array($RUTE['#p_all'], $RUTE['#p_today'], $RUTE['#p_this_week'], $RUTE['#p_this_month'], $RUTE['#p_this_year']))){
			if ($date == $RUTE['#p_today']) {
				$time = strtotime("00:00:00 today");
				$query .= " AND created_at >= ".$time;
			} else if ($date == $RUTE['#p_this_week']) {
				$time = strtotime("-7 days");
				$query .= " AND created_at >= ".$time;
			} else if ($date == $RUTE['#p_this_month']) {
				$time = strtotime("00:00:00 first day of this month");
				$query .= " AND created_at >= ".$time;
			} else if ($date == $RUTE['#p_this_year']) {
				$time = strtotime("00:00:00 first day of January");
				$query .= " AND created_at >= ".$time;
			}
		}
				
		if(in_array($category, $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false)) && $category != $RUTE['#p_all']){
			$query .= " AND category_id = {$category}";
		}

		if(in_array($author, $dba->query('SELECT id FROM '.T_USER.' WHERE role = "publisher" OR role = "moderator" OR role = "admin"')->fetchAll(false)) && $author != $RUTE['#p_all']){
			$query .= " AND user_id = {$author}";
		}
					
		if(in_array($sort, array($RUTE['#p_newest'], $RUTE['#p_oldest'], $RUTE['#p_views']))){
			if($sort == $RUTE['#p_newest']){
				$query .= " ORDER BY published_at DESC";
			} else if($sort == $RUTE['#p_oldest']){
				$query .= " ORDER BY published_at ASC";
			} else if($sort == $RUTE['#p_views']){
				$query .= " ORDER BY views DESC";
			}
		}

		$search_result = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' LIMIT 10')->fetchAll();

		if(!empty($search_result)){
			$search_count = $dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE status = "approved"'.$query)->fetchArray(true);
			foreach ($search_result as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);

				$html .= Specific::Maket("includes/search-profile-category-tag/posts");
				$search_ids[] = $post['id'];
			}
			Specific::DestroyMaket();

			$data = array(
				'return' => true,
				'html' => $html,
				'info' => '<span class="display-inline-block color-black font-bold">'.$search_count.'</span> '.$TEMP['#word']['results_related_to'].' <span class="display-inline-block color-black font-bold">"'.$keyword.'"</span>',
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

	public static function Profile($user_id, $profile_ids = array()){
		global $dba, $TEMP, $RUTE;

		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'profile_ids' => $profile_ids
		);

		if(!empty($profile_ids)){
			$query = ' AND id NOT IN ('.implode(',', $profile_ids).')';
		}

		if($TEMP['#moderator'] == false){
			if(!Specific::IsOwner($user_id)){
				$query .= ' AND status = "approved"';
			} else {
				$query .= ' AND status <> "deleted"';
			}
		}

		$posts = $dba->query('SELECT * FROM '.T_POST.' p WHERE user_id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND (SELECT status FROM '.T_USER.' WHERE id = p.user_id) = "active"'.$query.' ORDER BY created_at DESC LIMIT 10', $user_id)->fetchAll();

		if(!empty($posts)){
			foreach ($posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);

				$html .= Specific::Maket("includes/search-profile-category-tag/posts");
				$profile_ids[] = $post['id'];
			}
			Specific::DestroyMaket();

			$data = array(
				'return' => true,
				'html' => $html,
				'profile_ids' => $profile_ids
			);
		} else {
			$data = array(
				'return' => false,
				'html' => Specific::Maket("not-found/no-result")
			);
		}
		return $data;
	}

	public static function Category($category_id, $category_ids = array()){
		global $dba, $TEMP, $RUTE;

		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'catag_ids' => $category_ids
		);


		if(!empty($category_ids)){
			$query = ' AND id NOT IN ('.implode(',', $category_ids).')';
		}

		$posts = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' ORDER BY created_at DESC LIMIT 10', $category_id)->fetchAll();

		if(!empty($posts)){
			foreach ($posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
				$TEMP['!author_name'] = Specific::Data($post['user_id'], array('username'));
				$TEMP['!author_url'] = Specific::ProfileUrl($TEMP['!author_name']);
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);

				$html .= Specific::Maket("includes/search-profile-category-tag/posts");
				$category_ids[] = $post['id'];
			}
			Specific::DestroyMaket();

			$data = array(
				'return' => true,
				'html' => $html,
				'catag_ids' => $category_ids
			);
		} else {
			$data = array(
				'return' => false,
				'html' => Specific::Maket("not-found/no-result")
			);
		}
		return $data;
	}

	public static function Tag($label_id, $label_ids = array()){
		global $dba, $TEMP, $RUTE;

		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'catag_ids' => $label_ids
		);


		if(!empty($label_ids)){
			$query = ' AND id NOT IN ('.implode(',', $label_ids).')';
		}

		$posts = $dba->query('SELECT * FROM '.T_POST.' p WHERE (SELECT post_id FROM '.T_TAG.' WHERE label_id = ? AND post_id = p.id) = id AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' ORDER BY created_at DESC LIMIT 10', $label_id)->fetchAll();

		if(!empty($posts)){
			foreach ($posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);

				$html .= Specific::Maket("includes/search-profile-category-tag/posts");
				$label_ids[] = $post['id'];
			}
			Specific::DestroyMaket();

			$data = array(
				'return' => true,
				'html' => $html,
				'catag_ids' => $label_ids
			);
		} else {
			$data = array(
				'return' => false,
				'html' => Specific::Maket("not-found/no-result")
			);
		}
		return $data;
	}

	public static function Save($save_ids = array()){
		global $dba, $TEMP, $RUTE;

		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'save_ids' => $save_ids
		);


		if(!empty($save_ids)){
			$query = ' AND id NOT IN ('.implode(',', $save_ids).')';
		}

		$save_posts = $dba->query('SELECT * FROM '.T_POST.' p WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND (SELECT post_id FROM '.T_SAVED.' WHERE user_id = ? AND post_id = p.id AND status = "approved") = id'.$query.' ORDER BY created_at DESC LIMIT 8', $TEMP['#user']['id'])->fetchAll();

		if(!empty($save_posts)){
			foreach ($save_posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");


				$TEMP['!author_name'] = Specific::Data($post['user_id'], array('username'));
				$TEMP['!author_url'] = Specific::ProfileUrl($TEMP['!author_name']);


				$TEMP['!url'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);

				$html .= Specific::Maket("save/includes/save-posts");
				$save_ids[] = $post['id'];
			}
			Specific::DestroyMaket();

			$data = array(
				'return' => true,
				'html' => $html,
				'save_ids' => $save_ids
			);
		} else {
			$data = array(
				'return' => false,
				'html' => Specific::Maket("not-found/no-result")
			);
		}
		return $data;
	}

	public static function Chats($profiles_ids = array(), $type = 'first', $keyword = '', $last_cupdate = 0){
		global $dba, $TEMP;

		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'profiles_ids' => $profiles_ids,
			'last_cupdate' => $last_cupdate
		);

		if(!empty($keyword)){
			$query = ' AND ((SELECT id FROM '.T_USER.' WHERE (name LIKE "%'.$keyword.'%" OR surname LIKE "%'.$keyword.'%" OR username LIKE "%'.$keyword.'%") AND id <> '.$TEMP['#user']['id'].' AND id = c.profile_id) = c.profile_id OR ((SELECT id FROM '.T_USER.' WHERE (name LIKE "%'.$keyword.'%" OR surname LIKE "%'.$keyword.'%" OR username LIKE "%'.$keyword.'%") AND id <> '.$TEMP['#user']['id'].' AND id = c.user_id) = c.user_id))';
		}

		if(!empty($profiles_ids)){
			$profiles_str = implode(',', $profiles_ids);
			if($type == 'last'){
				$query .= ' AND updated_at > '.$last_cupdate;
			}
			$query .= ' AND profile_id NOT IN ('.$profiles_str.') AND user_id NOT IN ('.$profiles_str.')';
		}

		$chats = $dba->query('SELECT id, user_id, profile_id, updated_at FROM '.T_CHAT.' c WHERE (user_id = ? OR profile_id = ?) AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND profile_id NOT IN ('.$TEMP['#blocked_users'].') AND (SELECT MAX(chat_id) FROM '.T_MESSAGE.' WHERE ((user_id = ? AND deleted_fuser = 0) OR (profile_id = ? AND deleted_fprofile = 0)) AND chat_id = c.id) = id'.$query.' ORDER BY updated_at DESC LIMIT 10', $TEMP['#user']['id'], $TEMP['#user']['id'], $TEMP['#user']['id'], $TEMP['#user']['id'])->fetchAll();

		if(!empty($chats)){
			foreach ($chats as $key => $chat) {
				$chats_html = Specific::Chat($chat);
				$html .= $chats_html['html'];
				$user_id = $chat['user_id'];
				if(Specific::IsOwner($chat['user_id'])){
					$user_id = $chat['profile_id'];
				}
				if($key == count($chats)-1){
					$last_cupdate = $chat['updated_at'];
				}
				$profiles_ids[] = $user_id;
			}
			Specific::DestroyMaket();


			$data = array(
				'return' => true,
				'html' => $html,
				'profiles_ids' => $profiles_ids,
				'last_cupdate' => $last_cupdate
			);
		}
		return $data;
	}

	public static function Messages($user, $messages_ids = array(), $type = 'last'){
		global $dba, $TEMP;


		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'messages_ids' => $messages_ids
		);

		if(!empty($messages_ids)){
			$query = ' AND id NOT IN ('.implode(',', $messages_ids).')';
		}

		if(!is_array($user)){
			$user = Specific::Data($user);
			if(!empty($messages_ids)){
				if($type == 'first'){
					$query .= ' AND id < '.$messages_ids[0];
				} else {
					$query .= ' AND id > '.end($messages_ids);
				}
			}
		}

		$messages = $dba->query('SELECT * FROM '.T_MESSAGE." m WHERE user_id NOT IN ({$TEMP['#blocked_users']}) AND profile_id NOT IN ({$TEMP['#blocked_users']}) AND ((user_id = {$TEMP['#user']['id']} AND deleted_fuser = 0) OR (profile_id = {$TEMP['#user']['id']} AND deleted_fprofile = 0) OR (SELECT COUNT(id) FROM ".T_MESSAFI." WHERE message_id = m.id AND deleted_fuser = 0) > 0) AND (SELECT id FROM ".T_CHAT." WHERE ((user_id = {$TEMP['#user']['id']} AND profile_id = {$user['id']}) OR (user_id = {$user['id']} AND profile_id = {$TEMP['#user']['id']}) AND id = m.chat_id)) = chat_id".$query." ORDER BY id ASC LIMIT ? OFFSET ?", 20, 'reverse')->fetchAll();

		if(!empty($messages)){
			$update_ids = array();
			foreach ($messages as $message) {
				$TEMP['!files'] = '';
				$TEMP['!images'] = '';
				$TEMP['#deleted_fuser'] = $message['deleted_fuser'];
				$TEMP['#deleted_fprofile'] = $message['deleted_fprofile'];
				$TEMP['#messafi'] = false;
				$TEMP['#has_image'] = false;
				$TEMP['#has_file'] = false;
				$TEMP['#msg_out'] = true;
				if($message['user_id'] == $user['id']){
					$TEMP['#msg_out'] = false;
				}
				$TEMP['!created_at'] = Specific::DateString($message['created_at']);

				$messafis = $dba->query('SELECT f.* FROM '.T_MESSAFI.' f INNER JOIN '.T_MESSAGE.' m WHERE f.message_id = ? AND m.id = f.message_id AND ((m.user_id = ? AND f.deleted_fuser = 0) OR (m.profile_id = ? AND f.deleted_fprofile = 0))', $message['id'], $TEMP['#user']['id'], $TEMP['#user']['id'])->fetchAll();
				if(!empty($messafis)){
					$TEMP['#messafi'] = true;
					foreach ($messafis as $messafi) {
						if($messafi['deleted_at'] == 0){
							$TEMP['!fi_id'] = $messafi['id'];
							$TEMP['!fi_name'] = $messafi['name'];
							$TEMP['!fi_url'] = Specific::Url("uploads/messages/{$messafi['file']}");
							$TEMP['!fi_size'] = Specific::SizeFormat($messafi['size']);

							if(in_array(pathinfo($messafi['name'], PATHINFO_EXTENSION), array('jpeg', 'jpg', 'png', 'gif'))){
								$TEMP['#has_image'] = true;
								$img_maket = 'outimage';
								if($message['user_id'] == $user['id']){
									$img_maket = 'inimage';
								}
								$TEMP['!images'] .= Specific::Maket("messages/includes/{$img_maket}");
							} else {
								$TEMP['#has_file'] = true;
								$fi_maket = 'outfile';
								if($message['user_id'] == $user['id']){
									$fi_maket = 'infile';
								}
								$TEMP['!files'] .= Specific::Maket("messages/includes/{$fi_maket}");
							}
						} else {
							$TEMP['!fi_id'] = $messafi['id'];
							$deleted_maket = 'deleted-outfile';
							if($message['user_id'] == $user['id']){
								$deleted_maket = 'deleted-infile';
							}
							if(in_array(pathinfo($messafi['name'], PATHINFO_EXTENSION), array('jpeg', 'jpg', 'png', 'gif'))){
								$TEMP['#has_image'] = true;
								$TEMP['!fi_type'] = 'image';
								$TEMP['!images'] .= Specific::Maket("messages/includes/{$deleted_maket}");
							} else {
								$TEMP['#has_file'] = true;
								$TEMP['!fi_type'] = 'file';
								$TEMP['!files'] .= Specific::Maket("messages/includes/{$deleted_maket}");
							}
						}
					}
				}

				if(($message['text'] == NULL && !empty($messafis)) || $message['text'] != NULL){
					$TEMP['!id'] = $message['id'];
					$TEMP['!text'] = Specific::TextFilter($message['text']);
					$TEMP['!type'] = 'normal';

					$messaan = $dba->query('SELECT answered_id, type, COUNT(*) as count FROM '.T_MESSAAN.' a WHERE message_id = ?', $message['id'])->fetchArray();

					if($messaan['count'] > 0){
						$has_answer = false;
						$TEMP['!ans_deleted'] = false;
						if($messaan['type'] == 'text'){
							$answered = $dba->query('SELECT * FROM '.T_MESSAGE.' WHERE id = ? AND ((user_id = ? AND deleted_fuser = 0) OR (profile_id = ? AND deleted_fprofile = 0))', $messaan['answered_id'], $TEMP['#user']['id'], $TEMP['#user']['id'])->fetchArray();
							if(!empty($answered)){
								$has_answer = true;
								$ans_pid = $answered['profile_id'];
								$user_id = $ans_uid = $answered['user_id'];
								if(Specific::IsOwner($answered['user_id'])){
									$user_id = $answered['profile_id'];
								}

								if($answered['deleted_at'] == 0){
									$TEMP['!ans_id'] = $answered['id'];
									$TEMP['!ans_text'] = Specific::TextFilter($answered['text'], false);
								} else {
									$TEMP['!ans_deleted'] = true;
									$TEMP['!ans_deleted_word'] = $TEMP['#word']['message_was_deleted'];
								}
							}
						} else {
							$amessafi = $dba->query('SELECT f.*, m.user_id, m.profile_id FROM '.T_MESSAFI.' f INNER JOIN '.T_MESSAGE.' m WHERE f.id = ? AND m.id = f.message_id AND ((m.user_id = ? AND f.deleted_fuser = 0) OR (m.profile_id = ? AND f.deleted_fprofile = 0))', $messaan['answered_id'], $TEMP['#user']['id'], $TEMP['#user']['id'])->fetchArray();

							if(!empty($amessafi)){
								$has_answer = true;
								$ans_pid = $amessafi['profile_id'];
								$user_id = $ans_uid = $amessafi['user_id'];
								if(Specific::IsOwner($amessafi['user_id'])){
									$user_id = $amessafi['profile_id'];
								}

								if($amessafi['deleted_at'] == 0){

									$TEMP['!fi_aid'] = $amessafi['id'];
									$TEMP['!fi_aname'] = $amessafi['name'];
									$TEMP['!fi_asize'] = Specific::SizeFormat($amessafi['size']);
									if($messaan['type'] == 'image'){
										$TEMP['!fi_aurl'] = Specific::Url("uploads/messages/{$amessafi['file']}");
									}
								} else {
									$TEMP['!ans_deleted'] = true;
									$TEMP['!ans_deleted_word'] = $TEMP['#word']['deputy_file_deleted'];
								}
							}
						}

						if($has_answer){
							$ans_user = Specific::Data($user_id, array('username', 'name', 'surname', 'status'));
							$TEMP['!type'] = 'answered';
							$TEMP['!ans_type'] = $messaan['type'];
							$TEMP['!answered_id'] = $messaan['answered_id'];

							$you_responded_to = "{$TEMP['#word']['you_responded_to']} {$ans_user['username']}";
							if($ans_user['status'] == 'deleted'){
								$you_responded_to = "{$TEMP['#word']['you_responded_to']} {$TEMP['#word']['user']}";
							}
							$TEMP['!ans_title'] = $you_responded_to;

							if($ans_pid == $message['profile_id']){
								if(Specific::IsOwner($ans_uid)){
									$TEMP['!ans_title'] = $TEMP['#word']['you_replied_own_message'];
								} else {
									$TEMP['!ans_title'] = $TEMP['#word']['replied_his_own_message'];
								}
							} else if($message['user_id'] == $user['id']){
								$TEMP['!ans_title'] = "{$ans_user['username']} {$TEMP['#word']['answered_you']}";
							}
						}
					}

					if($message['deleted_at'] == 0){
						$maket = 'outgoing';
						if($message['user_id'] == $user['id']){
							$TEMP['!avatar_s'] = $user['avatar_s'];
							$TEMP['!username'] = $user['username'];
							$maket = 'incoming';
						}
						if(Specific::IsOwner($message['profile_id'])){
							$update_ids[] = $message['id'];
						}
					} else {
						$maket = 'deleted-outgoing';
						if($message['user_id'] == $user['id']){
							$maket = 'deleted-incoming';
						}
					}
					$html .= Specific::Maket("messages/includes/{$maket}");
					$messages_ids[] = $message['id'];
				}
			}
			Specific::DestroyMaket();
			if(!empty($update_ids)){
				$dba->query('UPDATE '.T_MESSAGE.' SET seen = 1 WHERE id IN ('.implode(',', $update_ids).') AND seen = 0');
			}
			$data = array(
				'return' => true,
				'html' => $html,
				'messages_ids' => $messages_ids
			);
		}
		return $data;
	}


	public static function Sitemap($params = array(), $sitemap_ids = array()){
		global $dba, $TEMP, $RUTE;


		$html = '';
		$query = '';

		$data = array(
			'return' => false,
			'html' => $html,
			'sitemap_ids' => $sitemap_ids
		);

		if(!empty($sitemap_ids)){
			$query = ' AND id NOT IN ('.implode(',', $sitemap_ids).')';
		}

		if(in_array($params['date'], array($RUTE['#p_today'], $RUTE['#p_yesterday'], $RUTE['#p_this_week'], $RUTE['#p_last_week']))){

			$today = strtotime("00:00:00 today");

			$yesterday_start = $today;
			$yesterday_end = strtotime("-1 days");

			$this_week = strtotime("-7 days");

			$last_week_start = strtotime("last sunday", strtotime("-1 week"));
			$last_week_end = strtotime("this sunday", strtotime("-1 week"));

			if($params['date'] == $RUTE['#p_yesterday']){
				$query .= " AND created_at >= {$yesterday_start} AND created_at <= {$yesterday_end}";
			} else if($params['date'] == $RUTE['#p_this_week']){
				$query .= ' AND created_at >= '.$this_week;
			} else if($params['date'] == $RUTE['#p_last_week']){
				$last_week_start = $last_week_start;
				$last_week_end = $last_week_end;

				$query .= " AND created_at >= {$last_week_start} AND created_at <= {$last_week_end}";
			} else {
				$query .= ' AND created_at >= '.$today;
			}
		} else {
			$month = date('F', mktime(0, 0, 0, $params['month'], 10));
			$first_hour = strtotime("00:00:00 {$params['day']} {$month} {$params['year']}");
			$last_hour = strtotime("23:59:59 {$params['day']} {$month} {$params['year']}");

			$query .= "AND created_at >= {$first_hour} AND created_at <= {$last_hour}";
		}


		$posts = $dba->query('SELECT id, title, slug FROM '.T_POST.' WHERE status = "approved"'.$query.' ORDER BY created_at DESC LIMIT 50')->fetchAll();


		if(!empty($posts)){
			foreach ($posts as $post) {
				$TEMP['!title'] = $post['title'];
				$TEMP['!url'] = Specific::Url($post['slug']);

				$html .= Specific::Maket('sitemap/includes/post');
				$sitemap_ids[] = $post['id'];
			}
			$data = array(
				'return' => true,
				'html' => $html,
				'sitemap_ids' => $sitemap_ids
			);
		}

		return $data;
	}
}
?>