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

$provider = Functions::Filter($_GET[$ROUTE['#p_provider']]);
if ($TEMP['#loggedin'] == false && !empty($provider) && in_array($provider, array('facebook', 'twitter', 'google')) && !isset($_GET['denied'])) {
    try {
        $hybridauth = new Hybridauth\Hybridauth(array(
            "callback" => Functions::Url("{$ROUTE['#r_social_login']}?{$ROUTE['#p_provider']}={$provider}"),
            "providers" => array(
                // openid providers
                "Facebook" => array(
                    "enabled" => true,
                    "keys" => array(
                        "id" => $TEMP['#settings']['fb_app_id'],
                        "secret" => $TEMP['#settings']['fb_secret_id']
                    ),
                    "scope" => "email",
                    "trustForwarded" => false
                ), "Twitter" => array(
                    "enabled" => true,
                    "keys" => array(
                        "key" => $TEMP['#settings']['tw_api_key'],
                        "secret" => $TEMP['#settings']['tw_api_key_secret']
                    ),
                    "includeEmail" => true
                ), "Google" => array(
                    "enabled" => true,
                    "keys" => array(
                        "id" => $TEMP['#settings']['go_app_id'],
                        "secret" => $TEMP['#settings']['go_secret_id']
                    ),
                ),
            ),
            'debug_mode' => false,
            'debug_file' => __FILE__ . '.log'
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
            $user = $dba->query('SELECT id FROM '.T_USER.' WHERE email = ?', $user_email)->fetchArray();
            if (!empty($user)) {
                $login_token = sha1(Functions::RandomKey().md5(time()));
                if($dba->query('INSERT INTO '.T_SESSION.' (user_id, token, details, created_at) VALUES (?, ?, ?, ?)', $user['id'], $login_token, json_encode(Functions::BrowserDetails()['details']), time())->returnStatus()){
                    if($save_session == 'on'){
                        setcookie("_SAVE_SESSION", $login_token, time() + 315360000, "/");
                    }
                    $_SESSION['_LOGIN_TOKEN'] = $login_token;
                    setcookie("_LOGIN_TOKEN", $login_token, time() + 315360000, "/");
                    $dba->query('UPDATE '.T_USER.' SET ip = ? WHERE id = ?', Functions::GetClientIp(), $user['id']); 
                    header("Location: " . Functions::Url());
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
                    $facebook_url = Functions::Filter($fa_social_url[4]);
                    if (!empty($profile->gender)) {
                        if ($profile->gender == 'male') {
                            $gender = 'male';
                        } else if ($profile->gender == 'female') {
                            $gender = 'female';
                        }
                    }
                }
                if ($provider == 'twitter') {
                    $twitter_url = Functions::Filter($social_url);
                }
                if (!empty($profile->description)) {
                    $about = Functions::Filter($profile->description);
                }
                $username = Functions::Filter($username);
                $profile_image = Functions::OAuthImage($profile->photoURL, $username);
                $email = Functions::Filter($user_email);
                $password = Functions::Filter($password);
                $verify_email = Functions::UserToken('verify_email')['token'];
                $change_email = Functions::UserToken('change_email')['token'];
                $reset_password = Functions::UserToken('reset_password')['token'];
                $unlink_email = Functions::UserToken('unlink_email')['token'];
                $_2check = Functions::UserToken('2check')['token'];
                $name = Functions::Filter($name);
                if(!empty($profile->lastName)){
                    $surname = Functions::Filter($profile->lastName);
                }
                $avatar = Functions::Filter($profile_image);
                $type = Functions::Filter($provider);

                if(!empty($profile->email)){
                    $to_name = $name;
                    if(!empty($profile->displayName)){
                        $to_name = $profile->displayName;
                    }

                    $TEMP['username'] = $to_name;
                    $TEMP['provider'] = ucfirst($provider);
                    $TEMP['user'] = $username;
                    $TEMP['code'] = $code;
                    $send = Functions::SendEmail(array(
                        'from_email' => $TEMP['#settings']['from_email'],
                        'from_name' => $TEMP['#settings']['title'],
                        'to_email' => $profile->email,
                        'to_name' => $to_name,
                        'subject' => $TEMP['#word']['access_credentials'],
                        'charSet' => 'UTF-8',
                        'text_body' => Functions::Build('emails/includes/send-credentials'),
                        'is_html' => true
                    ));
                }
                $ip = Functions::GetClientIp();
                $darkmode = 0;
                if($TEMP['#settings']['switch_mode'] == 'on' && $TEMP['#settings']['theme_mode'] == 'night'){
                    $darkmode = 1;
                }
                $user_id = $dba->query('INSERT INTO '.T_USER.' (username, email, password, name, surname, gender, about, facebook, twitter, avatar, ip, darkmode, status, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "active", ?, ?)', $username, $email, $password, $name, $surname, $gender, $about, $facebook_url, $twitter_url, $avatar, $ip, $darkmode, $type, time())->insertId();

                if ($user_id) {
                    if($dba->query('INSERT INTO '.T_TOKEN.' (user_id, verify_email, change_email, reset_password, unlink_email, 2check, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', $user_id, $verify_email, $change_email, $reset_password, $unlink_email, $_2check, time())->returnStatus()){
                        $login_token = sha1(Functions::RandomKey().md5(time()));
                        if($dba->query('INSERT INTO '.T_SESSION.' (user_id, token, details, created_at) VALUES (?, ?, ?, ?)', $user_id, $login_token, json_encode(Functions::BrowserDetails()['details']), time())->returnStatus()){
                            if($save_session == 'on'){
                                setcookie("_SAVE_SESSION", $login_token, time() + 315360000, "/");
                            }
                            $_SESSION['_LOGIN_TOKEN'] = $login_token;
                            setcookie("_LOGIN_TOKEN", $login_token, time() + 315360000, "/");
                            $dba->query('UPDATE '.T_USER.' SET ip = ? WHERE id = ?', Functions::GetClientIp(), $user_id); 
                            header("Location: " . Functions::Url());
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
            header("Location: " . Functions::Url("{$ROUTE['#r_social_login']}?{$ROUTE['#p_provider']}=$provider"));
            exit();
        }
    }*/
    catch (Exception $e) {
        if(isset($_SESSION['HYBRIDAUTH::STORAGE'])){
            unset($_SESSION['HYBRIDAUTH::STORAGE']);
            header("Location: " . Functions::Url("{$ROUTE['#r_social_login']}?{$ROUTE['#p_provider']}={$provider}"));
            exit();
        }
        exit($e->getMessage());
        switch ($e->getCode()) {
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
        print("an error found while processing your request! <b><a href='" . Functions::Url() . "'>Try again<a></b>");
        
    }
} else {
    header("Location: " . Functions::Url());
    exit();
}
?>