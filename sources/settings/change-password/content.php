<?php
if ($TEMP['#loggedin'] === false) {
    header("Location: ".Specific::ReturnUrl());
    exit();
}

$TEMP['#page']        = 'change-password';
$TEMP['#title']       = $TEMP['#word']['change_password'] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $TEMP['#settings']['description'];
$TEMP['#keyword']     = $TEMP['#settings']['keyword'];

$TEMP['#content']     = Specific::Maket("settings/change-password/content");
?>