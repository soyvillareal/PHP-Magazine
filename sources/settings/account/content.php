<?php
if ($TEMP['#loggedin'] === false) {
    header("Location: ".Specific::ReturnUrl());
    exit();
}

$TEMP['email'] = !empty($TEMP['#user']['new_email']) ? $TEMP['#user']['new_email'] : $TEMP['#user']['email'];
$TEMP['name'] = !empty($TEMP['#user']['name']) ? $TEMP['#user']['name'] : $TEMP['#word']['name'];
$TEMP['surname'] = !empty($TEMP['#user']['surname']) ? $TEMP['#user']['surname'] : $TEMP['#word']['surname'];
$TEMP['about'] = !empty($TEMP['#user']['about']) ? $TEMP['#user']['about'] : $TEMP['#word']['description'];

$TEMP['birthday'] = $TEMP['#user']['birthday'] > 0 ? "{$TEMP['#word']['date_of_birth']} ({$TEMP['#user']['birthday_format']})" : $TEMP['#word']['date_of_birth'];

$TEMP['gender'] = ucfirst($TEMP['#word']['gender'])." ({$TEMP['#user']['gender_txt']})";

$newsletter = $dba->query('SELECT slug, status, COUNT(*) as count FROM '.T_NEWSLETTER.' WHERE email = ?', $TEMP['#user']['email'])->fetchArray();
$TEMP['#newsletter_exists'] = false;
$TEMP['#newsletter_status'] = 'disabled';
if($newsletter['count'] > 0){
    $TEMP['#newsletter_exists'] = $newsletter['status'] == 'enabled';
    $TEMP['#newsletter_status'] = $newsletter['status'];
}
$TEMP['newsletter'] = "{$TEMP['#word']['newsletter_settings']} ({$TEMP['#word'][$TEMP['#newsletter_status']]})";
$TEMP['newsletter_slug'] = $newsletter['slug'];

$TEMP['2check'] = "{$TEMP['#word']['2check']} ({$TEMP['#word'][$TEMP['#user']['2check']]})";
$TEMP['facebook'] = !empty($TEMP['#user']['facebook']) ? "{$TEMP['#word']['facebook']} ({$TEMP['#user']['facebook']})" : $TEMP['#word']['facebook'];
$TEMP['twitter'] = !empty($TEMP['#user']['twitter']) ? "{$TEMP['#word']['twitter']} ({$TEMP['#user']['twitter']})" : $TEMP['#word']['twitter'];
$TEMP['instagram'] = !empty($TEMP['#user']['instagram']) ? "{$TEMP['#word']['instagram']} ({$TEMP['#user']['instagram']})" : $TEMP['#word']['instagram'];

$main_sonet = ucfirst($TEMP['#user']['main_sonet']);
$TEMP['main_social_network'] = "{$TEMP['#word']['main_social_network']} ({$main_sonet})";

$TEMP['contact_email'] = !empty($TEMP['#user']['contact_email']) ? "{$TEMP['#word']['contact_email']} ({$TEMP['#user']['contact_email']})" : $TEMP['#word']['contact_email'];

$messages_status = $TEMP['#user']['shows']['followers'] == 'on' ? 'enabled' : 'disabled';

$TEMP['followers'] = "{$TEMP['#word']['followers_settings']} ({$TEMP['#word'][$messages_status]})";

$messages_status = $TEMP['#user']['shows']['messages'] == 'on' ? 'enabled' : 'disabled';

$TEMP['messages'] = "{$TEMP['#word']['message_settings']} ({$TEMP['#word'][$messages_status]})";

$TEMP['username_alert'] = time() < $TEMP['#user']['user_changed'] ? "{$TEMP['#word']['have_already_changed_username_change_day']} ".Specific::DateFormat($TEMP['#user']['user_changed']) : $TEMP['#word']['when_change_username_will_months'];
$TEMP['email_alert'] = empty($TEMP['#user']['new_email']) ? $TEMP['#word']['use_email_login_where_will_send'] : $TEMP['#word']['requested_change_email_need_verify'];
$TEMP['birthday_alert'] = $TEMP['#user']['birthday_changed'] == 0 ? $TEMP['#word']['can_only_change_date_birth'] : "{$TEMP['#word']['just_changed_date_birth_day']} ".Specific::DateFormat($TEMP['#user']['birthday_changed']);
$TEMP['red_social'] = ucfirst($TEMP['#user']['type']);
$TEMP['#text_email_count'] = strlen(strip_tags($TEMP['#user']['about']));
$TEMP['about_br2nl'] = Specific::br2nl($TEMP['#user']['about']);

$TEMP['month'] = ucfirst($TEMP['#word']['month']);
$TEMP['default_holder'] = $TEMP['#user']['avatar'] != 'default-holder' ? 1 : 0;
$TEMP['#days'] = date('t', strtotime($TEMP['#user']['birthday']));
$TEMP['#years'] = date('Y');

$TEMP['#page']        = 'account';
$TEMP['#title']       = $TEMP['#word']['settings'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("settings/account/content");
?>