<?php
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