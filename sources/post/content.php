<?php

$slug = Specific::Filter($_GET['one']);
$post = $dba->query('SELECT * FROM '.T_POST.' WHERE slug = ? AND status = "approved"', $slug)->fetchArray();
if(empty($post)){
	header("Location: " . Specific::Url('404'));
	exit();
}

$title = $post['title'];
$keywords = array();
$url = Specific::Url($post['slug']);
$post_views = $post['views'];
$avatar = Specific::Data($post['user_id'], array('avatar'));
$category = $dba->query('SELECT id, name FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray();
$fingerprint = Specific::Fingerprint($TEMP['#user']['id']);

if($dba->query('SELECT COUNT(*) FROM '.T_VIEW.' WHERE post_id = ? AND fingerprint = ?', $post['id'], $fingerprint)->fetchArray(true) == 0){
	$post_views = ($post['views']+1);
	if($dba->query('INSERT INTO '.T_VIEW.' (post_id, fingerprint, created_at) VALUES (?, ?, ?)', $post['id'], $fingerprint, time())->returnStatus()){
		$dba->query('UPDATE '.T_POST.' SET views = ? WHERE id = ?', $post_views, $post['id']);
	};
}

$TEMP['title'] = $title;
$TEMP['category'] = $category['name'];
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

$TEMP['#is_loaded'] = false;
$TEMP['#type'] = $post['type'];
$TEMP['#thumb_source'] = $post['thumb_source'];
$TEMP['#entry_types'] = array();
$TEMP['#saved'] = $dba->query('SELECT COUNT(*) FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post['id'])->fetchArray(true);
$TEMP['#categories'] = $dba->query('SELECT id, name FROM '.T_CATEGORY)->fetchAll();

$entries = $dba->query('SELECT * FROM '.T_ENTRY.' WHERE post_id = ?', $post['id'])->fetchAll();
foreach ($entries as $key => $entry) {
	$TEMP['#entry_types'][] = $entry['type'];
}

$tags = $dba->query('SELECT * FROM '.T_LABEL.' t WHERE (SELECT label_id FROM '.T_TAG.' WHERE post_id = ? AND label_id = t.id) = id', $post['id'])->fetchAll();
foreach ($tags as $tag) {
	$keywords[] = $tag['name'];

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
$related_bottom = array();
if(!empty($relateds)){
	foreach ($relateds as $rl) {
		$TEMP['!title'] = $rl['title'];
		$TEMP['!url'] = Specific::Url($rl['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($rl['thumbnail'], 1, 's');
		$TEMP['!published_at'] = Specific::DateString($rl['published_at']);
		$TEMP['related_bottom'] .= Specific::Maket('post/includes/related-bottom');
		$related_bottom[] = $rl['id'];
	}
	Specific::DestroyMaket();
}

$related_cat = $dba->query('SELECT * FROM '.T_POST.' WHERE id != ? AND id != ? AND category_id = ? AND id NOT IN (?) AND status = "approved" ORDER BY views DESC LIMIT 5', $post['id'], $related_body, $post['category_id'], implode(',', $related_bottom))->fetchAll();
if(!empty($related_cat)){
	foreach ($related_cat as $rlc) {
		$TEMP['!key'] += 1;
		$TEMP['!title'] = $rlc['title'];
		$TEMP['!url'] = Specific::Url($rlc['slug']);
		$TEMP['!thumbnail'] = Specific::GetFile($rlc['thumbnail'], 1, 's');
		$TEMP['related_aside'] .= Specific::Maket('post/includes/related-aside');
	}
	Specific::DestroyMaket();
}


$TEMP['newsletter'] = Specific::Maket('post/includes/newsletter');
$TEMP['main']      = Specific::Maket('post/includes/main');

$TEMP['#page']         = 'post';
$TEMP['#title']        = $post['title'];
$TEMP['#description']  = $post['description'];
$TEMP['#keyword']      = implode(',', $keywords);
$TEMP['#content']      = Specific::Maket('post/content');
?>