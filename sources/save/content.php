<?php

$save_load = Load::Save();

$TEMP['save_time'] = Specific::DateFormat(time());
$TEMP['save_posts'] = $save_load['html'];
$TEMP['save_ids'] = implode(',', $save_load['save_ids']);

$TEMP['#page'] 		  = 'save';
$TEMP['#title']       = $TEMP['#word']['saved_posts'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("save/content");
?>