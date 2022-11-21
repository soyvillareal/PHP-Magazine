<?php
$slug = Functions::Filter($_GET['slug']);
if(!empty($slug)){
    $newsletter = $dba->query('SELECT * FROM '.T_NEWSLETTER.' WHERE slug = ?', $slug)->fetchArray();
    if (empty($newsletter) || $newsletter['status'] == 'disabled') {
        header("Location: ".Functions::Url('404'));
        exit();
    }
}
$TEMP['#newsletter_showhead'] = $TEMP['#loggedin'] == true && !empty($newsletter) && $newsletter['email'] == $TEMP['#user']['email'];
$TEMP['#frequency'] = 'all';

$TEMP['title'] = $TEMP['#word']['subscribe_to_our_newsletters'];
$TEMP['subtitle'] = $TEMP['#word']['so_that_you_well_informed_we_invite'];
$TEMP['button'] = $TEMP['#word']['subscribe'];
$TEMP['aria_button'] = $TEMP['#word']['subscribe_the_newsletter'];

$TEMP['#count_exists'] = true;
$TEMP['#newsletter_exists'] = false;
if(!empty($newsletter)){
    $TEMP['#newsletter_exists'] = true;

    $TEMP['title'] = $TEMP['#word']['you_are_subscribed'];
    $TEMP['subtitle'] = $TEMP['#word']['currently_receive_best_information_newsletter'];
    $TEMP['email'] = $newsletter['email'];
    $TEMP['button'] = $TEMP['#word']['save'];
    $TEMP['aria_button'] = $TEMP['#word']['save_newsletter_settings'];
    $TEMP['slug'] = $slug;

    $TEMP['#frequency'] = $newsletter['frequency'];
    $TEMP['#popular'] = $newsletter['popular'];
    $TEMP['#cats'] = $dba->query('SELECT category_id FROM '.T_NEWSCATE.' WHERE newsletter_id = ?', $newsletter['id'])->fetchAll(false);
    $TEMP['#count_exists'] = count($TEMP['#cats']) < count($TEMP['#categories']);
}

$TEMP['#page']        = 'newsletter';
$TEMP['#title']       = $TEMP['#word']['newsletter_settings'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("newsletter/content");
?>