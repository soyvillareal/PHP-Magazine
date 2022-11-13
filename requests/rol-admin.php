<?php 
if ($TEMP['#loggedin'] === true && $TEMP['#admin'] === true) {

    if($one == 'change-palette'){
        $type = Specific::Filter($_POST['type']);
        $light_palette = Specific::Filter($_POST['light_palette']);
        $dark_palette = Specific::Filter($_POST['dark_palette']);
        if(!empty($light_palette) && !empty($dark_palette)){
            $light_palette = Specific::BuildPalette($light_palette);
            $dark_palette = Specific::BuildPalette($dark_palette, 'dark');

            if($dba->query("UPDATE ".T_SETTING." SET value = ? WHERE name = 'light_palette'", $light_palette)->returnStatus()){
                if($dba->query("UPDATE ".T_SETTING." SET value = ? WHERE name = 'dark_palette'", $dark_palette)->returnStatus()){
                    $deliver['S'] = 200;
                }
            }
        }
    } else if($one == 'reset-palette'){
        $type = Specific::Filter($_POST['type']);
        if(in_array($type, array('light', 'dark'))){
            $light_palette = array(
                'background' => array(
                    'white' => '#fff',
                    'blue' => '#326891',
                    'grely' => '#e9e9e9',
                    'redly' => '#dd6e68',
                    'red' => '#cb423b',
                    'black' => '#000',
                    'blackly' => 'rgba(0,0,0,.5)',
                    'green' => '#61a125'
                ),
                'color' => array(
                    'blackly' => '#333',
                    'black' => '#000',
                    'white' => '#fff',
                    'grey' => '#909090',
                    'blue' => '#326891',
                    'red' => '#cb0e0b',
                    'green' => '#61a125',
                    'orange' => '#f29f18',
                ),
                'border' => array(
                    'blue' => '#326891',
                    'focus-blue' => '#326891',
                    'grely' => '#cdcdcd',
                    'grey' => '#909090',
                    'black' => '#000',
                    'red' => '#cb0e0b',
                ),
                'hover' => array(
                    'blue' => array(
                        'type' => 'color',
                        'value' => '#326891'
                    ),
                    'background' => array(
                        'type' => 'background-color',
                        'value' => '#ebebeb'
                    )
                )
            );
            $dark_palette = array(
                'background' => array(
                    'white' => '#181818',
                    'blue' => '#265070',
                    'grely' => '#303030',
                    'black' => '#e4e6eb'
                ),
                'color' => array(
                    'blackly' => '#b0b3b8',
                    'black' => '#e4e6eb',
                    'white' => '#181818',
                    'grey' => '#aaa',
                    'blue' => '#265070'
                ),
                'border' => array(
                    'blue' => '#265070',
                    'focus-blue' => '#265070',
                    'grely' => '#606060',
                    'grey' => '#aaa',
                    'black' => '#a5a6ab'
                ),
                'hover' => array(
                    'blue' => array(
                        'type' => 'color',
                        'value' => '#265070'
                    ),
                    'background' => array(
                        'type' => 'background-color',
                        'value' => '#222222'
                    )
                )
            );

            $palette = $light_palette;
            if($type == 'dark'){
                $palette = $dark_palette;
            }

            $deliver['XD'] = $palette;
            if($dba->query("UPDATE ".T_SETTING." SET value = ? WHERE name = '{$type}_palette'", json_encode($palette))->returnStatus()){
                $deliver['S'] = 200;
            }
        }
    }
}
?>