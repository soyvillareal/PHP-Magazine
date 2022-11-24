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

if($TEMP['#loggedin'] == true && $TEMP['#settings']['switch_mode'] == 'on'){
    $darkmode = Functions::Filter($_POST['darkmode']);
    if(isset($darkmode) && in_array($darkmode, array('1', '0'))){
        if($dba->query('UPDATE '.T_USER.' SET darkmode = ? WHERE id = ?', $darkmode, $TEMP['#user']['id'])->returnStatus()){
            $deliver = array(
                'S' => 200,
                'TX' => (bool)$darkmode ? $TEMP['#word']['light_mode'] : $TEMP['#word']['dark_mode']
            );
        }
    }
}
?>