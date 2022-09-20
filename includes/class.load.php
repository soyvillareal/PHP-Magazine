<?php
class Load {
	public static function LastPosts($home_ids = array()){
		global $dba, $TEMP;

		$data = array(
			'last_posts_one' => false,
			'last_posts_two' => false,
			'last_posts_one_html' => '',
			'last_posts_two_html' => '',
			'home_ids' => $home_ids
		);

		$last_posts_one_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') AND status = "approved" ORDER BY published_at ASC LIMIT 8')->fetchAll();

		if(!empty($last_posts_one_sql)){
			$data['last_posts_one'] = true;
			foreach ($last_posts_one_sql as $post) {
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
				$data['last_posts_one_html'] .= Specific::Maket('home/includes/last-posts-one');
				$home_ids[] = $post['id'];
			}
			Specific::DestroyMaket();
		}

		$last_posts_two_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') AND status = "approved" ORDER BY RAND() ASC LIMIT 5')->fetchAll();

		if(!empty($last_posts_two_sql)){
			$data['last_posts_two'] = true;
			$category_left = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $last_posts_two_sql[0]['category_id'])->fetchArray();
			$TEMP['#type_left'] = $last_posts_two_sql[0]['type'];

			$TEMP['id_left'] = $last_posts_two_sql[0]['id'];
			$TEMP['title_left'] = $last_posts_two_sql[0]['title'];
			$TEMP['description_left'] = $last_posts_two_sql[0]['description'];
			$TEMP['category_left'] = $category_left['name'];
			$TEMP['category_slug_left'] = $category_left['slug'];
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
			$TEMP['category_right'] = $category_right['name'];
			$TEMP['category_slug_right'] = $category_right['slug'];
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
				$TEMP['!category_middle'] = $category_middle['name'];
				$TEMP['!category_slug_middle'] = $category_middle['slug'];
				$TEMP['!url_middle'] = Specific::Url($post['slug']);
				$TEMP['!thumbnail_middle'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_date_middle'] = date('c', $post['published_at']);
				$TEMP['!published_at_middle'] = Specific::DateString($post['published_at']);

				$TEMP['last_posts_three'] .= Specific::Maket('home/includes/last-posts-middle');
				$home_ids[] = $post['id'];
			}
			Specific::DestroyMaket();
			$data['last_posts_two_html'] .= Specific::Maket('home/includes/last-posts-two');
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

		$main_recommended_videos_sql = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $home_ids).') AND type = "video" AND status = "approved" ORDER BY RAND() ASC LIMIT 18')->fetchAll();

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

	public static function Post($post = array(), $is_loaded = false, $is_amp = false){
		global $dba, $TEMP;

		if(!empty($post)){
			$post_ids = array();
			$root = 'post';
			$TEMP['#is_amp'] = $is_amp;
			if($is_amp){
                $nt_posts = $dba->query('SELECT * FROM '.T_POST.' WHERE id <> ? AND status = "approved" LIMIT 12', $post['id'])->fetchAll();
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
			$TEMP['category'] = $category['name'];
			$TEMP['category_slug'] = $category['slug'];
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
				$TEMP['!url'] = Specific::Url("{$TEMP['#r_tag']}/{$tag['slug']}");
				$TEMP['tags'] .= Specific::Maket('includes/post-amp/tags');
			}
			Specific::DestroyMaket();

			$max_cimages = $carousel_json = array();
			foreach ($entries as $key => $entry) {
				$TEMP['!frame'] = $entry['frame'];
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

								$recommended_bo = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ?', $recobo['recommended_id'])->fetchArray();
							} else {
								$recommended_bo = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ? AND category_id = ? AND status = "approved" ORDER BY RAND()', $post['id'], $post['category_id'])->fetchArray();
							}

							if(!empty($recommended_bo)){
								$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $recommended_bo['category_id'])->fetchArray();

		                		$TEMP['!title'] = $recommended_bo['title'];
								$TEMP['!category'] = $category['name'];
								$TEMP['!category_slug'] = $category['slug'];
		                		$TEMP['!url'] = Specific::Url($recommended_bo['slug']);
								$TEMP['!thumbnail'] = Specific::GetFile($recommended_bo['thumbnail'], 1, 's');
								$TEMP['!published_date'] = date('c', $recommended_bo['published_at']);
								$TEMP['!published_at'] = Specific::DateString($recommended_bo['published_at']);
		                    	$entry['body'] .= Specific::Maket('includes/post-amp/recommended-body');
		                		$post_ids[] = $recommended_bo['id'];
		                	}
			            } else if($i == 2 || $i == ($j+4)){
			                $j = $i;
			                $entry['body'] .= Specific::Maket("{$root}/includes/advertisement-body");
			            }
			            $entry['body'] .= $paragraph[$i];
			        }
				} else if($entry['type'] == 'image'){
					$TEMP['!frame'] = Specific::GetFile($entry['frame'], 3);
				} else if($entry['type'] == 'carousel'){
					$carousel = json_decode($entry['frame'], true);

					foreach ($carousel as $key => $car) {
						$carousel[$key]['image'] = Specific::GetFile($car['image'], 3);
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
						$TEMP['!frame'] = '<iframe width="100%" height="400" scrolling="no" frameborder="no" src="'.$entry['frame'].'"></iframe>';
					}
				} else if($entry['type'] == 'facebookpost'){
					if($is_amp){
						$TEMP['!frame'] = '<amp-facebook width="552" height="310" layout="responsive" data-href="'.$entry['frame'].'"></amp-facebook>';
					} else {
						$TEMP['!frame'] = '<div class="fb-post display-block margin-b15" data-href="'.$entry['frame'].'" data-width="100%"></div>';
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

				$TEMP['!id'] = $entry['id'];
				$TEMP['!title'] = $entry['title'];
				$TEMP['!body'] = $entry['body'];
				$TEMP['!type'] = $entry['type'];
				$TEMP['!eorder'] = $entry['eorder'];
				$TEMP['!esource'] = $entry['esource'];
				$TEMP['entries'] .= Specific::Maket("includes/post-amp/entries");
			}
			Specific::DestroyMaket();

			$TEMP['#collaborators'] = $dba->query('SELECT user_id FROM '.T_COLLABORATOR.' WHERE post_id = ? ORDER BY aorder ASC', $post['id'])->fetchAll();

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
					$TEMP['!category'] = $category['name'];
					$TEMP['!category_slug'] = $category['slug'];
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
			$TEMP['#featured_cid'] = Specific::Filter($_GET[$TEMP['#p_comment_id']]);
			$featured_comment = Specific::FeaturedComment($TEMP['#featured_cid']);
			if($featured_comment['return']){
				$TEMP['comments'] .= $featured_comment['html'];
				$comment_ids[] = $TEMP['#featured_cid'];
				$TEMP['#has_cfeatured'] = true;
			}

			$TEMP['#has_rfeatured'] = false;
			$TEMP['#featured_rid'] = Specific::Filter($_GET[$TEMP['#p_reply_id']]);
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

		if(in_array($date, array($TEMP['#p_all'], $TEMP['#p_today'], $TEMP['#p_this_week'], $TEMP['#p_this_month'], $TEMP['#p_this_year']))){
			if ($date == $TEMP['#p_today']) {
				$time = time()-(60*60*24);
				$query .= " AND created_at >= ".$time;
			} else if ($date == $TEMP['#p_this_week']) {
				$time = time()-(60*60*24*7);
				$query .= " AND created_at >= ".$time;
			} else if ($date == $TEMP['#p_this_month']) {
				$time = time()-(60*60*24*30);
				$query .= " AND created_at >= ".$time;
			} else if ($date == $TEMP['#p_this_year']) {
				$time = time()-(60*60*24*365);
				$query .= " AND created_at >= ".$time;
			}
		}
				
		if(in_array($category, $dba->query('SELECT id FROM '.T_CATEGORY)->fetchAll(false)) && $category != $TEMP['#p_all']){
			$query .= " AND category_id = {$category}";
		}

		if(in_array($author, $dba->query('SELECT id FROM '.T_USER.' WHERE role = "publisher" OR role = "moderator" OR role = "admin"')->fetchAll(false)) && $author != $TEMP['#p_all']){
			$query .= " AND user_id = {$author}";
		}
					
		if(in_array($sort, array($TEMP['#p_newest'], $TEMP['#p_oldest'], $TEMP['#p_views']))){
			if($sort == $TEMP['#p_newest']){
				$query .= " ORDER BY published_at DESC";
			} else if($sort == $TEMP['#p_oldest']){
				$query .= " ORDER BY published_at ASC";
			} else if($sort == $TEMP['#p_views']){
				$query .= " ORDER BY views DESC";
			}
		}

		$search_result = $dba->query('SELECT * FROM '.T_POST.' WHERE status = "approved"'.$query.' LIMIT 10')->fetchAll();

		if(!empty($search_result)){
			$search_count = $dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE status = "approved"'.$query)->fetchArray(true);
			foreach ($search_result as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];
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

	public static function Profile($user_id, $profile_ids = array()){
		global $dba, $TEMP;

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

		$posts = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id = ? AND status = "approved"'.$query.' ORDER BY created_at DESC LIMIT 10', $user_id)->fetchAll();

		if(!empty($posts)){
			foreach ($posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];
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
		global $dba, $TEMP;

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

		$posts = $dba->query('SELECT * FROM '.T_POST.' WHERE category_id = ? AND status = "approved"'.$query.' ORDER BY created_at DESC LIMIT 10', $category_id)->fetchAll();

		if(!empty($posts)){
			foreach ($posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];
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
		global $dba, $TEMP;

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

		$posts = $dba->query('SELECT * FROM '.T_POST.' p WHERE (SELECT post_id FROM '.T_TAG.' WHERE label_id = ? AND post_id = p.id) = id AND status = "approved"'.$query.' ORDER BY created_at DESC LIMIT 10', $label_id)->fetchAll();

		if(!empty($posts)){
			foreach ($posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];
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
		global $dba, $TEMP;

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

		$save_posts = $dba->query('SELECT * FROM '.T_POST.' p WHERE (SELECT post_id FROM '.T_SAVED.' WHERE user_id = ? AND post_id = p.id AND status = "approved") = id'.$query.' ORDER BY created_at DESC LIMIT 8', $TEMP['#user']['id'])->fetchAll();

		if(!empty($save_posts)){
			foreach ($save_posts as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!description'] = $post['description'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];


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

}
?>