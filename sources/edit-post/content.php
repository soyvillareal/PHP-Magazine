<?php

if ($TEMP['#loggedin'] == false) {
    header("Location: ".Functions::Url($RUTE['#r_login']));
    exit();
}

if ($TEMP['#publisher'] == false) {
    header("Location: ".Functions::Url('404'));
    exit();
}

$post_id = Functions::Filter($_GET['post_id']);

if (empty($post_id)){
    header("Location: ".Functions::Url('404'));
    exit();
}

$post = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ?', $post_id)->fetchArray();

if (empty($post) || (!Functions::IsOwner($post['user_id']) && $TEMP['#moderator'] == false)){
    header("Location: ".Functions::Url('404'));
    exit();
}

$TEMP['#post_title'] = $post['title'];
$TEMP['#published_at'] = $post['published_at'];
$TEMP['#category_id'] = $post['category_id'];
$TEMP['#post_description'] = $post['description'];

$TEMP['#thumbnail'] = Functions::GetFile($post['thumbnail'], 1, 's');
$TEMP['#post_type'] = $post['type'];

$TEMP['#tags'] = $dba->query('SELECT * FROM '.T_LABEL.' l WHERE (SELECT label_id FROM '.T_TAG.' WHERE post_id = ? AND label_id = l.id) = id', $post_id)->fetchAll();

$TEMP['#post_sources'] = json_decode($post['post_sources'], true);
$TEMP['psources_count'] = count($TEMP['#post_sources'])+1;
if(!empty($TEMP['#post_sources'])){
    $TEMP['first_pname'] = $TEMP['#post_sources'][0]['name'];
    $TEMP['first_psource'] = $TEMP['#post_sources'][0]['source'];
    unset($TEMP['#post_sources'][0]);
}

$TEMP['#thumb_sources'] = json_decode($post['thumb_sources'], true);
$TEMP['tsources_count'] = count($TEMP['#thumb_sources'])+1;
if(!empty($TEMP['#thumb_sources'])){
    $TEMP['first_tname'] = $TEMP['#thumb_sources'][0]['name'];
    $TEMP['first_tsource'] = $TEMP['#thumb_sources'][0]['source'];
    unset($TEMP['#thumb_sources'][0]);
}

$entries = $dba->query('SELECT * FROM '.T_ENTRY.' WHERE post_id = ? ORDER BY eorder', $post_id)->fetchAll();

$TEMP['#entry_types'] = array();
foreach ($entries as $entry) {
    $TEMP['#entry_types'][] = $entry['type'];
}

$max_cimages = $carousel_json = array();
foreach ($entries as $entry) {
    $TEMP['#type'] = $entry['type'];
    
    $TEMP['!id'] = $entry['id'];
    $TEMP['!title'] = $entry['title']; 
    $TEMP['!esource'] = $entry['esource'];
    if($entry['type'] == 'text'){
        $TEMP['!body'] = $entry['body'];
    } else {
        if($entry['type'] == 'image'){
            $TEMP['!image'] = Functions::GetFile($entry['frame'], 3);
        } else if($entry['type'] == 'carousel'){
            $carousel = json_decode($entry['frame'], true);

            foreach ($carousel as $key => $car) {
                $carousel[$key]['image'] = Functions::GetFile($car['image'], 3);
            }

            $TEMP['!max_cimages'] = count($carousel);
            $TEMP['!frame'] = $carousel[0]['image'];
            $TEMP['!caption'] = $carousel[0]['caption'];

            $TEMP['!images'] = $carousel;
            $max_cimages[] = count($carousel);
            $carousel_json[] = json_encode($carousel);
        } else if($entry['type'] == 'embed'){
            $frame = json_decode($entry['frame'], true);
            $TEMP['!url'] = $frame['url'];
            $TEMP['!attrs'] = $frame['attrs'];

            $frame = Functions::BuildFrame($frame['url'], $frame['attrs']);
            $TEMP['!frame'] = $frame['html'];
        } else if($entry['type'] == 'soundcloud'){
            $TEMP['!sc_url'] = $entry['frame'];
            $TEMP['!frame'] = Functions::Build('includes/load-edit/soundcloud');;
        } else if($entry['type'] == 'facebookpost'){
            $TEMP['!fb_url'] = $entry['frame'];
            $TEMP['!frame'] = Functions::Build('includes/load-publisher-edit/facebook-post');
        } else if($entry['type'] == 'instagrampost'){
            $TEMP['!omit_script'] = true;
            $TEMP['!url'] = $entry['frame'];
            $TEMP['!frame'] = Functions::Build('includes/create-edit-post/instagram-blockquote');
        } else {
            $TEMP['!frame'] = $entry['frame'];

            $frame = Functions::IdentifyFrame($entry['frame']);
            if($frame['return']){
                $TEMP['!frame'] = $frame['html'];
                $TEMP['!type_frame'] = $frame['type'];
            }
        }
    }

    $TEMP['entries'] .= Functions::Build("edit-post/includes/entry");
}
Functions::DestroyBuild();

$TEMP['#recobo'] = $dba->query('SELECT * FROM '.T_POST.' INNER JOIN '.T_RECOBO.' ON '.T_POST.'.id = '.T_RECOBO.'.recommended_id AND post_id = ? ORDER BY rorder ASC', $post_id)->fetchAll();

$recobo_ids = array();
if(!empty($TEMP['#recobo'])){
    foreach ($TEMP['#recobo'] as $re) {
        $category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $re['category_id'])->fetchArray();
        $TEMP['!id'] = $re['recommended_id'];

        $TEMP['!title'] = $re['title'];
        $TEMP['!category'] = $TEMP['#word']["category_{$category['name']}"];
        $TEMP['!category_slug'] = Functions::Url("{$RUTE['#r_category']}/{$category['slug']}");
        $TEMP['!url'] = Functions::Url($re['slug']);
        $TEMP['!thumbnail'] = Functions::GetFile($re['thumbnail'], 1, 's');
        $TEMP['!published_date'] = date('c', $re['published_at']);
        $TEMP['!published_at'] = Functions::DateString($re['published_at']);

        $TEMP['recobo'] .= Functions::Build("includes/create-edit-post/recommended-body");
        $recobo_ids[] = $re['recommended_id'];
    }
    Functions::DestroyBuild();
}

$TEMP['#collaborators'] = $dba->query('SELECT * FROM '.T_COLLABORATOR.' WHERE post_id = ? ORDER BY aorder ASC', $post_id)->fetchAll();
$collaborators_ids = array();
if(!empty($TEMP['#collaborators'])){
    foreach ($TEMP['#collaborators'] as $au) {
        $user = Functions::Data($au['user_id'], array('username', 'avatar'));

        $TEMP['!id'] = $au['user_id'];
        $TEMP['!collab_name'] = $user['username'];
        $TEMP['!collab_avatar'] = $user['avatar_s'];

        $TEMP['collaborators'] .= Functions::Build("includes/create-edit-post/collaborator");
        $collaborators_ids[] = $au['user_id'];
    }
    Functions::DestroyBuild();
}

$TEMP['#post_id'] = $post_id;
$TEMP['recobo_ids'] = implode(',', $recobo_ids);
$TEMP['collaborators_ids'] = implode(',', $collaborators_ids);

$TEMP['max_cimages'] = implode(',', $max_cimages);
$TEMP['carousel_json'] = implode(',', $carousel_json);

$TEMP['#page']        = 'edit-post';
$TEMP['#title']       = $TEMP['#word']['edit_post'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("edit-post/content");
?>