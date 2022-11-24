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

$ROUTE = array();

// You can edit these fields, with this you edit the routes in the urls
// PLEASE DON'T USE SPECIAL CHARS >:C, COULD GENERATE UNNECESSARY CONFLICT
// IMPORTANT: If you edit this file, be sure to edit the routes.js file in the "nodejs" folder and the .htaccess file with the same values.

$ROUTE['#r_home'] = '';
$ROUTE['#r_tag'] = 'tag';
$ROUTE['#r_category'] = 'category';
$ROUTE['#r_search'] = 'search';
$ROUTE['#r_login'] = 'login';
$ROUTE['#r_social_login'] = 'social-login';
$ROUTE['#r_verify_email'] = 'verify-email';
$ROUTE['#r_change_email'] = 'change-email';
$ROUTE['#r_unlink_email'] = 'unlink-email';
$ROUTE['#r_2check'] = '2check';
$ROUTE['#r_register'] = 'register';
$ROUTE['#r_forgot_password'] = 'forgot-password';
$ROUTE['#r_reset_password'] = 'reset-password';
$ROUTE['#r_change_password'] = 'change-password';
$ROUTE['#r_create_post'] = 'create-post';
$ROUTE['#r_edit_post'] = 'edit-post';
$ROUTE['#r_settings'] = 'settings';
$ROUTE['#r_account'] = 'account';
$ROUTE['#r_logins'] = 'logins';
$ROUTE['#r_password'] = 'password';
$ROUTE['#r_newsletter'] = 'newsletter';
$ROUTE['#r_user'] = 'user';
$ROUTE['#r_messages'] = 'messages';
$ROUTE['#r_saved'] = 'saved';
$ROUTE['#r_locked'] = 'locked';
$ROUTE['#r_blocked_users'] = 'blocked-users';
$ROUTE['#r_sitemap'] = 'sitemap';
$ROUTE['#r_rss'] = 'rss';
$ROUTE['#r_page'] = 'page';
$ROUTE['#r_delete_account'] = 'delete-account';
$ROUTE['#r_terms_of_use'] = 'terms-of-use';
$ROUTE['#r_habeas_data'] = 'habeas-data';
$ROUTE['#r_about_us'] = 'about-us';
$ROUTE['#r_contact'] = 'contact';
$ROUTE['#r_show_palette'] = 'show-palette';
$ROUTE['#r_compatibility'] = 'compatibility';
$ROUTE['#r_logout'] = 'logout';

//Params
$ROUTE['#p_return'] = 'return';
$ROUTE['#p_provider'] = 'provider';
$ROUTE['#p_insert'] = 'insert';
$ROUTE['#p_keyword'] = 'keyword';
$ROUTE['#p_date'] = 'date';
$ROUTE['#p_category'] = 'category';
$ROUTE['#p_author'] = 'author';
$ROUTE['#p_sort'] = 'sort';
$ROUTE['#p_show_alert'] = 'show-alert';
$ROUTE['#p_deleted_post'] = 'deleted-post';
$ROUTE['#p_comment_id'] = 'c';
$ROUTE['#p_reply_id'] = 'r';
$ROUTE['#p_all'] = 'all';
$ROUTE['#p_today'] = 'today';
$ROUTE['#p_this_week'] = 'this-week';
$ROUTE['#p_this_month'] = 'this-month';
$ROUTE['#p_this_year'] = 'this-year';
$ROUTE['#p_newest'] = 'newest';
$ROUTE['#p_oldest'] = 'oldest';
$ROUTE['#p_views'] = 'views';

$ROUTE['#p_year'] = 'year';
$ROUTE['#p_month'] = 'month';
$ROUTE['#p_day'] = 'day';
$ROUTE['#p_yesterday'] = 'yesterday';
$ROUTE['#p_last_week'] = 'last-week';

$ROUTE['#p_user_id'] = 'user-id';
$ROUTE['#p_language'] = 'language';

?>