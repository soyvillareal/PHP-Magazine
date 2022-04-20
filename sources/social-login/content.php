<?php
require_once('./includes/libraries/hybridauth/autoload.php');
$provider = Specific::Filter($_GET[$TEMP['#p_provider']]);
if (!empty($provider) && in_array($provider, array('facebook', 'twitter', 'google')) && !isset($_GET['denied'])) {
    try {
        $hybridauth = new Hybridauth\Hybridauth(array(
            "callback" => Specific::Url("{$TEMP['#r_social_login']}?{$TEMP['#p_provider']}=$provider"),
            "providers" => array(
                // openid providers
                "Facebook" => array(
                    "enabled" => true,
                    "keys" => array("id" => $TEMP['#settings']['fb_app_id'], "secret" => $TEMP['#settings']['fb_secret_id']),
                    "scope" => "email",
                    "trustForwarded" => false
                ), "Twitter" => array(
                    "enabled" => true,
                    "keys" => array("key" => $TEMP['#settings']['tw_app_id'], "secret" => $TEMP['#settings']['tw_secret_id']),
                    "includeEmail" => true
                ), "Google" => array(
                    "enabled" => true,
                    "keys" => array("id" => $TEMP['#settings']['go_app_id'], "secret" => $TEMP['#settings']['go_secret_id']),
                ),
            )
        ));
        $authProvider = $hybridauth->authenticate($provider);
        $profile = $authProvider->getUserProfile();
        if ($profile && isset($profile->identifier)) {
            $name = $profile->firstName;
            if ($provider == 'facebook') {
                $notfound_email     = 'fa_';
                $notfound_email_com = '@facebook.com';
            } else if ($provider == 'twitter') {
                $notfound_email     = 'tw_';
                $notfound_email_com = '@twitter.com';
            } else if ($provider == 'Google') {
                $notfound_email     = 'go_';
                $notfound_email_com = '@google.com';
            }
            $user_name  = $notfound_email . $profile->identifier;
            $user_email = $user_name . $notfound_email_com;
            if (!empty($profile->email)) {
                $user_email = $profile->email;
            }
            $user = $dba->query('SELECT id, COUNT(*) as count FROM '.T_USER.' WHERE email = ?', $user_email)->fetchArray();
            if ($user['count'] > 0) {
                $login_token = sha1(Specific::RandomKey().md5(time()));
                if($dba->query('INSERT INTO '.T_SESSION.' (user_id, token, details, created_at) VALUES (?, ?, ?, ?)', $user['id'], $login_token, json_encode(Specific::BrowserDetails()['details']), time())->returnStatus()){
                    if($save_session == 'on'){
                        setcookie("_SAVE_SESSION", $login_token, time() + 315360000, "/");
                    }
                    $_SESSION['_LOGIN_TOKEN'] = $login_token;
                    setcookie("_LOGIN_TOKEN", $login_token, time() + 315360000, "/");
                    $dba->query('UPDATE '.T_USER.' SET ip = ? WHERE id = ?', Specific::GetClientIp(), $user['id']); 
                    header("Location: " . Specific::Url());
                    exit();
                }
            } else {
                $str = md5(microtime());
                $username = substr($str, 0, 9);
                $code = substr(md5(time()), 0, 9);
                $password = password_hash($code, PASSWORD_BCRYPT, ['cost' => 12]);
                if($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE username = ?', $id)->fetchArray(true) > 0){
                    $str = md5(microtime());
                    $username = substr($str, 0, 9);
                }
                $social_url   = substr($profile->profileURL, strrpos($profile->profileURL, '/') + 1);
                $surname = '';
                $about = '';
                $gender = 'male';
                $facebook_url = '';
                $twitter_url = '';
                if ($provider == 'facebook') {
                    $fa_social_url = @explode('/', $profile->profileURL);
                    $facebook_url = Specific::Filter($fa_social_url[4]);
                    if (!empty($profile->gender)) {
                        if ($profile->gender == 'male') {
                            $gender = 'male';
                        } else if ($profile->gender == 'female') {
                            $gender = 'female';
                        }
                    }
                }
                if ($provider == 'twitter') {
                    $twitter_url = Specific::Filter($social_url);
                }
                if (!empty($profile->description)) {
                    $about = Specific::Filter($profile->description);
                }
                $username = Specific::Filter($username);
                $profile_image = Specific::OAuthImage($profile->photoURL, $username);
                $email = Specific::Filter($user_email);
                $password = Specific::Filter($password);
                $verify_email = Specific::UserToken('verify_email')['token'];
                $change_email = Specific::UserToken('change_email')['token'];
                $reset_password = Specific::UserToken('reset_password')['token'];
                $unlink_email = Specific::UserToken('unlink_email')['token'];
                $_2check = Specific::UserToken('2check')['token'];
                $name = Specific::Filter($name);
                if(!empty($profile->lastName)){
                    $surname = Specific::Filter($profile->lastName);
                }
                $avatar = Specific::Filter($profile_image);
                $type = Specific::Filter($provider);

                if(!empty($profile->email)){
                    $to_name = $name;
                    if(!empty($profile->displayName)){
                        $to_name = $profile->displayName;
                    }

                    $TEMP['username'] = $to_name;
                    $TEMP['provider'] = ucfirst($provider);
                    $TEMP['user'] = $username;
                    $TEMP['code'] = $code;
                    $send = Specific::SendEmail(array(
                        'from_email' => $TEMP['#settings']['smtp_username'],
                        'from_name' => $TEMP['#settings']['title'],
                        'to_email' => $profile->email,
                        'to_name' => $to_name,
                        'subject' => $TEMP['#word']['access_credentials'],
                        'charSet' => 'UTF-8',
                        'text_body' => Specific::Maket('emails/includes/send-credentials'),
                        'is_html' => true
                    ));
                }

                $user_id = $dba->query('INSERT INTO '.T_USER.' (username, email, password, name, surname, gender, about, facebook, twitter, avatar, status, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "active", ?, ?)', $username, $email, $password, $name, $surname, $gender, $about, $facebook_url, $twitter_url, $avatar, $type, time())->insertId();
                if ($user_id) {
                    if($dba->query('INSERT INTO '.T_TOKEN.' (user_id, verify_email, change_email, reset_password, unlink_email, 2check, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', $user_id, $verify_email, $change_email, $reset_password, $unlink_email, $_2check, time())->returnStatus()){
                        $login_token = sha1(Specific::RandomKey().md5(time()));
                        if($dba->query('INSERT INTO '.T_SESSION.' (user_id, token, details, created_at) VALUES (?, ?, ?, ?)', $user_id, $login_token, json_encode(Specific::BrowserDetails()['details']), time())->returnStatus()){
                            if($save_session == 'on'){
                                setcookie("_SAVE_SESSION", $login_token, time() + 315360000, "/");
                            }
                            $_SESSION['_LOGIN_TOKEN'] = $login_token;
                            setcookie("_LOGIN_TOKEN", $login_token, time() + 315360000, "/");
                            $dba->query('UPDATE '.T_USER.' SET ip = ? WHERE id = ?', Specific::GetClientIp(), $user_id); 
                            header("Location: " . Specific::Url());
                            exit();
                        }
                    }
                }
            }
        }
    }
    /*catch (Hybridauth\Exception\HttpRequestFailedException $e) {
        $json = $authProvider->getHttpClient()->getResponseBody();
        if(json_decode($json, true)['errors'][0]['code'] == 89){
            $authProvider->disconnect();
            header("Location: " . Specific::Url("{$TEMP['#r_social_login']}?{$TEMP['#p_provider']}=$provider"));
            exit();
        }
    }*/
    catch (Exception $e) {
        if(isset($_SESSION['HYBRIDAUTH::STORAGE'])){
            unset($_SESSION['HYBRIDAUTH::STORAGE']);
            header("Location: " . Specific::Url("{$TEMP['#r_social_login']}?{$TEMP['#p_provider']}=$provider"));
            exit();
        }
        exit($e->getMessage());
        /*switch ($e->getCode()) {
            case 0:
                print("Unspecified error.");
                break;
            case 1:
                print("Hybridauth configuration error.");
                break;
            case 2:
                print("Provider not properly configured.");
                break;
            case 3:
                print("Unknown or disabled provider.");
                break;
            case 4:
                print("Missing provider application credentials.");
                break;
            case 5:
                print("Authentication failed The user has canceled the authentication or the provider refused the connection.");
                break;
            case 6:
                print("User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.");
                break;
            case 7:
                print("User not connected to the provider.");
                break;
            case 8:
                print("Provider does not support this feature.");
                break;
        }
        print("an error found while processing your request! <b><a href='" . Specific::Url() . "'>Try again<a></b>");
        */
    }
} else {
    header("Location: " . Specific::Url());
    exit();
}
?>