<?php

$save_load = Loads::Saved();

$TEMP['saved_time'] = Functions::DateFormat(time());
$TEMP['saved_posts'] = $save_load['html'];
$TEMP['saved_ids'] = implode(',', $save_load['saved_ids']);

$TEMP['#page'] 		  = 'saved';
$TEMP['#title']       = $TEMP['#word']['saved_posts'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Functions::Build("saved/content");
?>