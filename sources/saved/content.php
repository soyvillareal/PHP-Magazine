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

$save_load = Loads::Saved();

$TEMP['saved_time'] = Functions::DateFormat(time());
$TEMP['saved_posts'] = $save_load['html'];
$TEMP['saved_ids'] = implode(',', $save_load['saved_ids']);

$TEMP['#page'] 		  = 'saved';
$TEMP['#title']       = $TEMP['#word']['saved_posts'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keywords']     = $TEMP['#settings']['keywords'];

$TEMP['#content']     = Functions::Build("saved/content");
?>