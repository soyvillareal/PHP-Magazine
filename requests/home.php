<?php

// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

if($one == 'load_more'){
	$post_ids = Functions::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);

	if(!empty($post_ids) && is_array($post_ids)){
		$last_posts = Loads::LastPosts(8, $post_ids);
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
				'HT' => Functions::Build('home/includes/more-posts')
			);
		}
	}
} else if($one == 'load_videos'){
	$post_ids = Functions::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);

	if(!empty($post_ids) && is_array($post_ids)){
		$main_recommended_videos = Loads::RecommendedVideos($post_ids);
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
	$post_ids = Functions::Filter($_POST['post_ids']);
	$post_ids = html_entity_decode($post_ids);
	$post_ids = json_decode($post_ids);

	if(!empty($post_ids) && is_array($post_ids)){

		$main_left = Functions::MainPosts($post_ids);

		if(!empty($main_left)){
			foreach ($main_left as $post) {
				$category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
				$TEMP['!id'] = $post['id'];
				$TEMP['!type'] = $post['type'];

				$TEMP['!title'] = $post['title'];
				$TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
				$TEMP['!category_slug'] = Functions::Url("{$ROUTE['#r_category']}/{$category['slug']}");
				$TEMP['!published_date'] = date('c', $post['published_at']);
				$TEMP['!url'] = Functions::Url($post['slug']);

				$TEMP['!description'] = $post['description'];
				$TEMP['!thumbnail'] = Functions::GetFile($post['thumbnail'], 1, 's');
				$TEMP['!published_at'] = Functions::DateString($post['published_at']);
				$html .= Functions::Build('home/includes/main-left-one');

				$post_ids[] = $post['id'];
			}
			Functions::DestroyBuild();

			$deliver = array(
				'S' => 200,
				'IDS' => $post_ids,
				'HT' => $html
			);
		}
	}
}
?>