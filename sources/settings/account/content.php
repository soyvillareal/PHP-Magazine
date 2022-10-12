<?php
if ($TEMP['#loggedin'] === false) {
    header("Location: ".Specific::ReturnUrl());
    exit();
}
$TEMP['#profile'] = $TEMP['data'] = $TEMP['#user'];
if($TEMP['#moderator'] == true){
    $user_id = Specific::Filter($_GET[$RUTE['#p_user_id']]);
    if(!empty($user_id) && $TEMP['#user']['id'] != $user_id){
        $TEMP['#profile'] = $TEMP['data'] = Specific::Data($user_id);
        $TEMP['#param'] = "?{$RUTE['#p_user_id']}={$user_id}";
    }
}

$TEMP['email'] = !empty($TEMP['#profile']['new_email']) ? $TEMP['#profile']['new_email'] : $TEMP['#profile']['email'];
$TEMP['name'] = !empty($TEMP['#profile']['name']) ? $TEMP['#profile']['name'] : $TEMP['#word']['name'];
$TEMP['surname'] = !empty($TEMP['#profile']['surname']) ? $TEMP['#profile']['surname'] : $TEMP['#word']['surname'];
$TEMP['about'] = !empty($TEMP['#profile']['about']) ? $TEMP['#profile']['about'] : $TEMP['#word']['description'];

$TEMP['birthday'] = $TEMP['#profile']['birthday'] > 0 ? "{$TEMP['#word']['date_of_birth']} ({$TEMP['#profile']['birthday_format']})" : $TEMP['#word']['date_of_birth'];

$TEMP['gender'] = ucfirst($TEMP['#word']['gender'])." ({$TEMP['#profile']['gender_txt']})";

$newsletter = $dba->query('SELECT slug, status, COUNT(*) as count FROM '.T_NEWSLETTER.' WHERE email = ?', $TEMP['#profile']['email'])->fetchArray();
$TEMP['#newsletter_exists'] = false;
$TEMP['#newsletter_status'] = 'disabled';
if($newsletter['count'] > 0){
    $TEMP['#newsletter_exists'] = $newsletter['status'] == 'enabled';
    $TEMP['#newsletter_status'] = $newsletter['status'];
}
$TEMP['newsletter'] = "{$TEMP['#word']['newsletter_settings']} ({$TEMP['#word'][$TEMP['#newsletter_status']]})";
$TEMP['newsletter_slug'] = $newsletter['slug'];

$TEMP['2check'] = "{$TEMP['#word']['2check']} ({$TEMP['#word'][$TEMP['#profile']['2check']]})";
$TEMP['facebook'] = !empty($TEMP['#profile']['facebook']) ? "{$TEMP['#word']['facebook']} ({$TEMP['#profile']['facebook']})" : $TEMP['#word']['facebook'];
$TEMP['twitter'] = !empty($TEMP['#profile']['twitter']) ? "{$TEMP['#word']['twitter']} ({$TEMP['#profile']['twitter']})" : $TEMP['#word']['twitter'];
$TEMP['instagram'] = !empty($TEMP['#profile']['instagram']) ? "{$TEMP['#word']['instagram']} ({$TEMP['#profile']['instagram']})" : $TEMP['#word']['instagram'];

$main_sonet = ucfirst($TEMP['#profile']['main_sonet']);
$TEMP['main_social_network'] = "{$TEMP['#word']['main_social_network']} ({$main_sonet})";

$TEMP['contact_email'] = !empty($TEMP['#profile']['contact_email']) ? "{$TEMP['#word']['contact_email']} ({$TEMP['#profile']['contact_email']})" : $TEMP['#word']['contact_email'];

$messages_status = $TEMP['#profile']['shows']['followers'] == 'on' ? 'enabled' : 'disabled';

$TEMP['followers'] = "{$TEMP['#word']['followers_settings']} ({$TEMP['#word'][$messages_status]})";

$messages_status = $TEMP['#profile']['shows']['messages'] == 'on' ? 'enabled' : 'disabled';

$TEMP['messages'] = "{$TEMP['#word']['message_settings']} ({$TEMP['#word'][$messages_status]})";

$TEMP['username_alert'] = time() < $TEMP['#profile']['user_changed'] ? "{$TEMP['#word']['have_already_changed_username_change_day']} ".Specific::DateFormat($TEMP['#profile']['user_changed']) : $TEMP['#word']['when_change_username_will_months'];
$TEMP['email_alert'] = empty($TEMP['#profile']['new_email']) ? $TEMP['#word']['use_email_login_where_will_send'] : $TEMP['#word']['requested_change_email_need_verify'];
$TEMP['birthday_alert'] = $TEMP['#profile']['birthday_changed'] == 0 ? $TEMP['#word']['can_only_change_date_birth'] : "{$TEMP['#word']['just_changed_date_birth_day']} ".Specific::DateFormat($TEMP['#profile']['birthday_changed']);
$TEMP['red_social'] = ucfirst($TEMP['#profile']['type']);
$TEMP['#text_email_count'] = strlen(strip_tags($TEMP['#profile']['about']));
$TEMP['about_br2nl'] = Specific::br2nl($TEMP['#profile']['about']);

$TEMP['month'] = ucfirst($TEMP['#word']['month']);
$TEMP['default_holder'] = $TEMP['#profile']['avatar'] != 'default-holder' ? 1 : 0;
$TEMP['#days'] = date('t', strtotime($TEMP['#profile']['birthday']));
$TEMP['#years'] = date('Y');

$TEMP['#page']        = 'account';
$TEMP['#title']       = $TEMP['#word']['settings'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("settings/account/content");
?>