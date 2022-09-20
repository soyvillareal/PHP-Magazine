<?php

if ($TEMP['#loggedin'] == false) {
    header("Location: ".Specific::Url($TEMP['#r_login']));
    exit();
}

$post_id = Specific::Filter($_GET['post_id']);

if (empty($post_id)){
    header("Location: ".Specific::Url('404'));
    exit();
}

$post = $dba->query('SELECT * FROM '.T_POST.' WHERE id = ?', $post_id)->fetchArray();

if (empty($post) && !Specific::IsOwner($post['user_id'])){
    header("Location: ".Specific::Url('404'));
    exit();
}

$TEMP['#post_title'] = $post['title'];
$TEMP['#category_id'] = $post['category_id'];
$TEMP['#post_description'] = $post['description'];

$TEMP['#thumbnail'] = Specific::GetFile($post['thumbnail'], 1, 's');
$TEMP['#type'] = $post['type'];

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
    $TEMP['!id'] = $entry['id'];
    $TEMP['#type'] = $entry['type'];
    $TEMP['!title'] = $entry['title'];
    $TEMP['!source'] = $entry['source'];
    if($entry['type'] == 'text'){
        $TEMP['!body'] = $entry['body'];
    } else {
        if($entry['type'] == 'image'){
            $TEMP['!image'] = Specific::GetFile($entry['frame'], 3);
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
        } else if($entry['type'] == 'embed'){
            $frame = json_decode($entry['frame'], true);
            $TEMP['!url'] = $frame['url'];
            $TEMP['!attrs'] = $frame['attrs'];

            $frame = Specific::MaketFrame($frame['url'], $frame['attrs']);
            $TEMP['!frame'] = $frame['html'];
        } else if($entry['type'] == 'soundcloud'){
            $TEMP['!frame'] = '<iframe width="100%" height="400" scrolling="no" frameborder="no" src="'.$entry['frame'].'"></iframe>';
        } else if($entry['type'] == 'facebookpost'){
            $TEMP['!frame'] = '<div class="fb-post display-block background-white" data-href="'. $entry['frame'] .'" data-width="100%"></div>';
        } else if($entry['type'] == 'instagrampost'){
            $TEMP['!omit_script'] = true;
            $TEMP['!url'] = $entry['frame'];
            $TEMP['!frame'] = Specific::Maket('includes/create-edit-post/instagram-blockquote');
        } else {
            $TEMP['!frame'] = $entry['frame'];

            $frame = Specific::IdentifyFrame($entry['frame']);
            if($frame['return']){
                $TEMP['!frame'] = $frame['html'];
                $TEMP['!type_frame'] = $frame['type'];
            }
        }
    }

    $TEMP['entries'] .= Specific::Maket("edit-post/includes/entry");
}
Specific::DestroyMaket();

$TEMP['#recobo'] = $dba->query('SELECT * FROM '.T_POST.' INNER JOIN '.T_RECOBO.' ON '.T_POST.'.id = '.T_RECOBO.'.recommended_id AND post_id = ? ORDER BY rorder ASC', $post_id)->fetchAll();

$recobo_ids = array();
if(!empty($TEMP['#recobo'])){
    foreach ($TEMP['#recobo'] as $re) {
        $category = $dba->query('SELECT name, slug FROM '.T_CATEGORY.' WHERE id = ?', $re['category_id'])->fetchArray();
        $TEMP['!id'] = $re['recommended_id'];

        $TEMP['!title'] = $re['title'];
        $TEMP['!category'] = $category['name'];
        $TEMP['!category_slug'] = $category['slug'];
        $TEMP['!url'] = Specific::Url($re['slug']);
        $TEMP['!thumbnail'] = Specific::GetFile($re['thumbnail'], 1, 's');
        $TEMP['!published_date'] = date('c', $re['published_at']);
        $TEMP['!published_at'] = Specific::DateString($re['published_at']);

        $TEMP['recobo'] .= Specific::Maket("includes/create-edit-post/recommended-body");
        $recobo_ids[] = $re['recommended_id'];
    }
    Specific::DestroyMaket();
}

$TEMP['#collaborators'] = $dba->query('SELECT * FROM '.T_COLLABORATOR.' WHERE post_id = ? ORDER BY aorder ASC', $post_id)->fetchAll();
$collaborators_ids = array();
if(!empty($TEMP['#collaborators'])){
    foreach ($TEMP['#collaborators'] as $au) {
        $user = Specific::Data($au['user_id'], array('username', 'avatar'));

        $TEMP['!id'] = $au['user_id'];
        $TEMP['!collab_name'] = $user['username'];
        $TEMP['!collab_avatar'] = $user['avatar_s'];

        $TEMP['collaborators'] .= Specific::Maket("includes/create-edit-post/collaborator");
        $collaborators_ids[] = $au['user_id'];
    }
    Specific::DestroyMaket();
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

$TEMP['#content']     = Specific::Maket("edit-post/content");
?>