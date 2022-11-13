<?php 

require_once('./includes/autoload.php');

header("Content-type:text/xml");

$TEMP['#type'] = Functions::Filter($_GET['type']);
$TEMP['#get'] = Functions::Filter($_GET['get']);

if(!in_array($TEMP['#type'], array($RUTE['#r_user'], $RUTE['#r_category'])) || empty($TEMP['#get'])){
    header("Location: " . Functions::Url('404'));
	exit();
}

$get = ucfirst($TEMP['#get']);

$query = " AND (SELECT id FROM ".T_USER." WHERE username = '{$TEMP['#get']}' AND id = p.user_id) = user_id";
if($TEMP['#type'] == $RUTE['#r_user']){

    $user = $dba->query('SELECT username, about, avatar FROM '.T_USER.' WHERE username = ?', $TEMP['#get'])->fetchArray();

    $user = Functions::Data($user, 3);
    
    $TEMP['title'] = $user['username'];
    $TEMP['description'] = $user['about'];

    $TEMP['rss_image'] = $user['avatar_b'];
    $TEMP['link'] = $user['slug'];

} else if($TEMP['#type'] == $RUTE['#r_category']){
    $TEMP['title'] = "{$TEMP['#settings']['title']} - {$get}";
    $TEMP['description'] = $TEMP['#settings']['description'];
    $TEMP['rss_image'] = Functions::GetFile('images/logo-light.png', 2);
    $TEMP['link'] = $TEMP['#site_url'];

    $category_name = $dba->query('SELECT word FROM '.T_WORD." WHERE {$TEMP['#language']} = '{$TEMP['#get']}'")->fetchArray(true);
    $category_name = str_replace('category_', '', $category_name);

    $query = " AND (SELECT id FROM ".T_CATEGORY." WHERE name = '{$category_name}' AND id = p.category_id) = category_id";
}


$posts = $dba->query("SELECT * FROM ".T_POST." p WHERE status = 'approved'{$query} ORDER BY created_at DESC LIMIT 12")->fetchAll();


if(!empty($posts)){
    foreach($posts as $key => $post){
        $TEMP['!author'] = $get;
        $TEMP['!category'] = $get;
        if($TEMP['#type'] == $RUTE['#r_user']){
            $TEMP['!category'] = $dba->query('SELECT name FROM '.T_CATEGORY.' WHERE id = ?', $post['category_id'])->fetchArray(true);
        }

        if($TEMP['#type'] == $RUTE['#r_category']){
            $user = Functions::Data($post['user_id'], array(
                'username',
                'name',
                'surname'
            ));
            $TEMP['!author'] = $user['username'];
        }

        $TEMP['!title'] = $post['title'];
        $TEMP['!pub_date'] = date('D, d M Y H:i:s +0000', $post['created_at']);
        $TEMP['!url'] = Functions::Url($post['slug']);
        $TEMP['!description'] = $post['description'];
        if($TEMP['#get'] == $RUTE['#r_category'] && $TEMP['#settings']['system_comments'] == 'on'){
            $TEMP['!count_comments'] = $dba->query('SELECT COUNT(*) FROM '.T_COMMENTS.' WHERE post_id = ?', $post['id'])->fetchArray(true);
        }

        if($key == 0){
            
            $TEMP['pub_date'] = date('D, d M Y H:i:s +0000', $post['created_at']);
            $TEMP['last_build_date'] = date('D, d M Y H:i:s +0000', $post['updated_at']);
        }

        
        $TEMP['items'] .= Functions::Build('includes/rss-wrapper/items', 'xml');
    }
}

echo Functions::Build('rss-wrapper', 'xml');
$dba->close();
unset($TEMP);
unset($RUTE);
?>