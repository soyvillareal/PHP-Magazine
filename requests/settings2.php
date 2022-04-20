<?php
if ($TEMP['#loggedin'] === true) {
    if ($one == 'general') {
        $dates  = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
        $emptys = array();
        $gen = 'male';

        $age_changed = $TEMP['#user']['age_changed'];
        $d = $TEMP['#user']['birthday'];
        $m = $TEMP['#user']['birthday_month'];
        $y = $TEMP['#user']['birthday_year'];

        $day = Specific::Filter($_POST['day']);
        $month = Specific::Filter($_POST['month']);
        $year = Specific::Filter($_POST['year']);

        $email = Specific::Filter($_POST['settings-email']);
        $cellphone = Specific::Filter($_POST['cellphone']);
        $phone = Specific::Filter($_POST['phone']);
        $gender = Specific::Filter($_POST['gender']);

        if (empty($email)) {
            $emptys[] = 'settings-email';
        }
        if (empty($cellphone)) {
            $emptys[] = 'cellphone';
        }
        if($day != $d || $month != $m || $year != $y && $age_changed < 1){
            if(empty($day)){
                $emptys[] = 'day';
            }
            if(empty($month)){
                $emptys[] = 'month';
            }
            if(empty($year)){
                $emptys[] = 'year';
            }
        }
        if(empty($emptys)){
            if ($day != $d || $month != $m || $year != $y && $age_changed < 1) {
                $d = $day;
                $m = $month;
                $y = $year;
                if(strlen($day) > 2){
                    $errors[] = array('error' => $TEMP['#word']['please_enter_valid_date'], 'el' => 'day');
                }
                if(!in_array($month, $dates)){
                    $errors[] = array('error' => $TEMP['#word']['please_enter_valid_date'], 'el' => 'month');
                } 
                if(strlen($year) > 4 || strlen($year) < 4){
                    $errors[] = array('error' => $TEMP['#word']['please_enter_valid_date'], 'el' => 'year');
                }
                if(!checkdate($month, $day, $year)){
                    $errors[] = array('error' => $TEMP['#word']['please_enter_valid_date'], 'el' => 'day-month-year');
                }
            }
            if (!empty($gender)) {
                if (in_array($gender, array(1, 2))) {
                    $gen = $gender;
                }
            }
            if ($email != $TEMP['#user']['email']) {
                if ($dba->query('SELECT COUNT(*) FROM user WHERE email = "'.$email.'"')->fetchArray() > 0) {
                    $errors[] = array('error' => $TEMP['#word']['email_exists'], 'el' => 'settings-email');
                }
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = array('error' => $TEMP['#word']['email_invalid_characters'], 'el' => 'settings-email');
            }
            if (!is_numeric($cellphone) || strlen($cellphone) > 12) {
                $errors[] = array('error' => $TEMP['#word']['invalid_cell_phone'], 'el' => 'cellphone');
            }
            if (!empty($phone)){
                if(!is_numeric($phone) || strlen($phone) > 12) {
                    $errors[] = array('error' => $TEMP['#word']['invalid_phone'], 'el' => 'phone');
                }
            } else {
                $phone = 'NULL';
            }
                
            if (!isset($errors)) {
                $update_data = '';
                $redirect_verify = false;
                $birthday = DateTime::createFromFormat('d-m-Y H:i:s', "$d-$m-$y 00:00:00");

                if(empty($TEMP['#user']['change_email'])){
                    if ($TEMP['#settings']['validate_email'] == 'on' && !empty($email) && $TEMP['#user']['email'] != $email) {
                        $code = rand(111111, 999999);
                        $token = md5($code);
                        $dba->query('UPDATE user SET token = "'.$token.'" WHERE id = '.$TEMP['#user']['id']);

                        $TEMP['token'] = $token;
                        $TEMP['code'] = $code;
                        $TEMP['ukey'] = $TEMP['#user']['ukey'];
                        $TEMP['name'] = $TEMP['#user']['names'];
                        $TEMP['text'] = $TEMP['#word']['check_your_new_email'];
                        $TEMP['footer'] = $TEMP['#word']['just_ignore_this_message'];
                        $TEMP['type'] = 'change';

                        $send = Specific::SendEmail(array(
                            'from_email' => $TEMP['#settings']['smtp_username'],
                            'from_name' => $TEMP['#settings']['title'],
                            'to_email' => $email,
                            'to_name' => $TEMP['#user']['name'],
                            'subject' => $TEMP['#word']['verify_your_account'],
                            'charSet' => 'UTF-8',
                            'text_body' => Specific::Maket('emails/includes/verify-email'),
                            'is_html' => true
                        ));
                        if ($send == true) {
                            $redirect_verify = true;
                            $deliver['token'] = $token;
                            $update_data = ', change_email = "'.$email.'"';
                        }
                    }else{
                        $update_data = ', email = "'.$email.'"';
                    }
                }
                    
                $update = $dba->query('UPDATE user SET cellphone = '.$cellphone.', phone = '.$phone.', gender = '.$gen.', age_changed = '.$age_changed.', birthday = '.$birthday->getTimestamp().', province = "'.Specific::Filter($_POST['province']).'", municipality = '.Specific::Filter($_POST['municipality']).', about = "'.Specific::Filter($_POST['about']).'"'.$update_data.' WHERE id = '.$TEMP['#user']['id'])->returnStatus();
                if ($update == true){
                    $deliver['status'] = 200;
                    $deliver['redirect_verify'] = $redirect_verify;
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'errors' => $errors
                );
            }
        } else {
            $deliver = array(
                'status' => 400,
                'emptys' => $emptys
            );
        }
    } else if ($one == 'change-password') {
        $emptys = array();
        if (empty($_POST['current-password'])) {
            $emptys[] = 'current-password';
        }
        if (empty($_POST['password'])) {
            $emptys[] = 'password';
        }
        if (empty($_POST['re-password'])) {
            $emptys[] = 're-password';
        }

        if (empty($emptys)) {
            if ($TEMP['#user']['password'] != sha1($_POST['current-password'])) {
                $errors[] = array('error' => $TEMP['#word']['current_password_dont_match'], 'el' => 'current-password');
            }
            if (strlen($_POST['password']) < 4) {
                $errors[] = array('error' => $TEMP['#word']['password_is_short'], 'el' => 'password');
            }
            if ($_POST['password'] != $_POST['re-password']) {
                $errors[] = array('error' => $TEMP['#word']['new_password_dont_match'], 'el' => 're-password');
            }
            if (!isset($errors)) {
                if ($dba->query('UPDATE user SET password = "'.sha1($_POST['password']).'" WHERE id = '.$TEMP['#user']['id'])->returnStatus()) {
                    $deliver['status'] = 200;
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'errors' => $errors 
                );
            }
        } else {
            $deliver = array(
                'status' => 400,
                'emptys' => $emptys
            );
        }
    } else if ($one == 'authentication') {
        if(in_array($_POST['authentication'], array(0, 1))){
            if($dba->query('UPDATE user SET authentication = '.Specific::Filter($_POST['authentication']).' WHERE id = '.$TEMP['#user']['id'])->returnStatus()){
                $deliver['status'] = 200;
            }
        }
    } else if($one == 'change-avatar'){
        if(!empty($_FILES['avatar'])){
            if(!empty($_FILES['avatar']['tmp_name'])){
                $file_info = array(
                    'file' => $_FILES['avatar']['tmp_name'],
                    'size' => $_FILES['avatar']['size'],
                    'name' => $_FILES['avatar']['name'],
                    'type' => $_FILES['avatar']['type'],
                    'from' => 'avatar',
                    'crop' => array('width' => 400, 'height' => 400)
                );
                $file_data = Specific::UploadImage($file_info);
                if (!empty($file_data)) {
                    if(!empty($TEMP['#user']['ex_avatar']) && $TEMP['#user']['avatar'] != 'images/default-avatar.jpg' && $TEMP['#user']['avatar'] != 'images/default-favatar.jpg'){
                        unlink($TEMP['#user']['ex_avatar']);
                    }
                    if ($dba->query('UPDATE user SET avatar = ? WHERE id = '.$TEMP['#user']['id'], $file_data)->returnStatus()) {
                        $deliver['status'] = 200;
                    }
                }
            }
        }
    } else if ($one == 'delete-session'){
        $id = Specific::Filter($_POST['id']);
        if (!empty($id)) {
            $sessions = $dba->query('SELECT * FROM session WHERE id = '.$id)->fetchArray();
            if (!empty($sessions)) {
                $deliver['reload'] = false;
                if (($sessions['user_id'] == $TEMP['#user']['id']) || Specific::Admin()) {
                    if ($dba->query('DELETE FROM session WHERE id = '.$id)->returnStatus()) {
                        $deliver['status'] = 200;
                        if ((!empty($_SESSION['_LOGIN_TOKEN']) && $_SESSION['_LOGIN_TOKEN'] == $sessions['session_id']) || (!empty($_COOKIE['_LOGIN_TOKEN']) && $_COOKIE['_LOGIN_TOKEN'] == $sessions['session_id'])) {
                            setcookie('_LOGIN_TOKEN', null, -1, '/');
                            session_destroy();
                            $deliver['reload'] = true;
                        }
                    }
                }
            }
        }
    } else if($one == 'table-sessions'){
        $page = Specific::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $user_sessions = $dba->query('SELECT * FROM session WHERE user_id = '.$TEMP['#user']['id'].' ORDER BY id DESC LIMIT ? OFFSET ?', 10, $page)->fetchAll();
            if (!empty($user_sessions)) {
                foreach ($user_sessions as $value) {
                    $session = Specific::GetSessions($value);
                    $TEMP['!id'] = $value['id'];
                    $TEMP['!ip'] = $session['ip'];
                    $TEMP['!browser'] = $session['browser'];
                    $TEMP['!platform'] = $session['platform'];
                    $TEMP['!time'] = Specific::DateFormat($value['time']);
                    $html .= Specific::Maket("settings/security/includes/sessions");
                }
                Specific::DestroyMaket();
            }
            $deliver['status'] = 200;
            $deliver['html'] = $html;
        }
    } else if($one == 'delete-email'){
        $user_id = Specific::Filter($_POST['user_id']);
        if(!empty($user_id) && Specific::IsOwner($user_id)){
            $code = rand(111111, 999999);
            $token = md5($code);
            if($dba->query('UPDATE user SET change_email = ?, token = ? WHERE id = '.$user_id, NULL, $token)->returnStatus()){
                $deliver['status'] = 200;
            }
        }
    }
}
?>