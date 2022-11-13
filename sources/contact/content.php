<?php 

$page = $dba->query('SELECT * FROM '.T_PAGE.' WHERE slug = "contact"')->fetchArray();

$TEMP['#page'] = 'contact';
$TEMP['#title'] = $TEMP['#word']["page_{$page['slug']}"]. ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $page['description'];
$TEMP['#keyword'] = $page['keywords'];

$TEMP['#content'] = Functions::Build('contact/content');
?>