<?php
if($one == 'load_more'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);

	if(!empty($post_ids) && is_array($post_ids)){
		$last_posts = Load::LastPosts(8, $post_ids);
		$TEMP['#last_posts_one'] = $last_posts['last_posts_one'];
		$TEMP['#last_posts_two'] = $last_posts['last_posts_two'];
		
		if($TEMP['#last_posts_one'] == true || $TEMP['#last_posts_two'] == true){
			if($last_posts['last_posts_one'] == true){
				$TEMP['last_posts_one'] = $last_posts['last_posts_one_html'];
			}
			if($last_posts['last_posts_two'] == true){
				$TEMP['last_posts_two'] = $last_posts['last_posts_two_html'];
			}
			$deliver = array(
				'S' => 200,
				'IDS' => $last_posts['home_ids'],
				'HT' => Specific::Maket('home/includes/more-posts')
			);
		}
	}
} else if($one == 'load_videos'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);

	if(!empty($post_ids) && is_array($post_ids)){
		$main_recommended_videos = Load::RecommendedVideos($post_ids);
		$TEMP['#main_recommended_videos'] = $main_recommended_videos['main_recommended_videos'];
		
		if($TEMP['#main_recommended_videos'] == true){
			$deliver = array(
				'S' => 200,
				'IDS' => $main_recommended_videos['home_ids'],
				'HT' => $main_recommended_videos['main_recommended_videos_html']
			);
		}
	}
} else if($one == 'load_main_left'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);

	if(!empty($post_ids) && is_array($post_ids)){

		$main_left = Specific::MainPosts($post_ids);

		if(!empty($main_left)){
			foreach ($main_left as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Specific::Url("{$RUTE['#r_category']}/{$category['slug']}");
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!url'] = Specific::Url($post['slug']);

				$TEMP['!description'] = $post['description'];
				$TEMP['!thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_at'] = Specific::DateString($post['published_at']);
				$html .= Specific::Maket('home/includes/main-left-one');

				$post_ids[] = $post['id'];
			}
			Specific::DestroyMaket();

			$deliver = array(
				'S' => 200,
				'IDS' => $post_ids,
				'HT' => $html
			);
		}
	}
}
?>