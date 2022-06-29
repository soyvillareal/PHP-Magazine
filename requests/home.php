<?php
if($one == 'load_more'){
	$post_ids = Specific::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);

	if(!empty($post_ids) && is_array($post_ids)){
		$last_news = Load::LastNews($post_ids);
		$TEMP['#last_news_one'] = $last_news['last_news_one'];
		$TEMP['#last_news_two'] = $last_news['last_news_two'];
		
		if($TEMP['#last_news_one'] == true || $TEMP['#last_news_two'] == true){
			if($last_news['last_news_one'] == true){
				$TEMP['last_news_one'] = $last_news['last_news_one_html'];
			}
			if($last_news['last_news_two'] == true){
				$TEMP['last_news_two'] = $last_news['last_news_two_html'];
			}
			$deliver = array(
				'S' => 200,
				'IDS' => $last_news['home_ids'],
				'HT' => Specific::Maket('home/includes/more-news')
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

		$main_left = Specific::MainNews($post_ids);

		if(!empty($main_left)){
			foreach ($main_left as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!category'] = $category['name'];
				$TEMP['!category_slug'] = $category['slug'];
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