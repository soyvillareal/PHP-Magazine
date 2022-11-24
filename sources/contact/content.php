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

$page = $dba->query('SELECT * FROM '.T_PAGE.' WHERE slug = "contact"')->fetchArray();

$TEMP['#page'] = 'contact';
$TEMP['#title'] = $TEMP['#word']["page_{$page['slug']}"]. ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $page['description'];
$TEMP['#keywords'] = $page['keywords'];

$TEMP['#content'] = Functions::Build('contact/content');
?>