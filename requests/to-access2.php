<?php
if ($TEMP['#loggedin'] === true && !in_array($one, array('verify-change-email', 'resend-change-email', 'register'))) {
	$deliver = array(
		'status' => 400,
		'error' => $TEMP['#word']['already_logged_in']
	);
    echo json_encode($deliver);
    exit();
}

if($one == 'login'){
	$error = '';
	$emptys = array();
	$dni = Specific::Filter($_POST['dni']);
	$password = Specific::Filter($_POST['password']);
	$recaptcha = Specific::CheckRecaptcha($_POST['recaptcha']);
	if(empty($dni)){
		$emptys[] = 'dni';
	}
	if(empty($password)){
		$emptys[] = 'password';
	}
	if(empty($emptys)){
		if($dba->query('SELECT COUNT(*) FROM user WHERE dni = "'.$dni.'"')->fetchArray() == 0){
	        $error = array('error' => $TEMP['#word']['invalid_dni'], 'el' => 'dni');
	    } else if ($dba->query('SELECT COUNT(*) FROM user WHERE dni = "'.$dni.'" AND password = "'.sha1($password).'"')->fetchArray() == 0){
	       	$error = array('error' => $TEMP['#word']['invalid_password'], 'el' => 'password');
	    } else if ($TEMP['#settings']['recaptcha'] == 'on') {
            if (!isset($_POST['recaptcha']) || empty($_POST['recaptcha']) || ($recaptcha["success"] == false && $recaptcha["score"] < 0.5)) {
                $error = array('error' => $TEMP['#word']['recaptcha_error'], 'el' => 'g-recaptcha');
            }
        } 

	    $to_access = $dba->query('SELECT * FROM user WHERE dni = "'.$dni.'" AND password = "'.sha1($password).'"')->fetchArray();
	    if (empty($error)) {
	        if ($to_access['status'] == 'pending') {
	           	$deliver = array(
	           		'status' => 401,
	           		'ukey' => $to_access['ukey'],
	           		'token' => $to_access['token'],
	           		'html' => $TEMP['#word']['account_is_not_active'] . ' <button class="btn-noway color-blue" id="resend-email">' . $TEMP['#word']['resend_email'] . '</button>'
	            );
	        } else if ($to_access['status'] == 'deactivated') {
	           	$deliver = array(
	           		'status' => 401,
	           		'html' => $TEMP['#word']['account_was_deactivated_owner_email_related'] . ' ' . $TEMP['#word']['if_you_need_more_help'] . ' <a class="color-blue" href="'.Specific::Url('contact').'" target="_self">' . $TEMP['#word']['contact_our_helpdesk'] . '</a>'
	            );
	        } else {
	            if ($to_access['authentication'] == 1 && $to_access['ip'] != Specific::GetClientIp()) {
	                $code = rand(111111, 999999);
	                $token = md5($code);
	                $dba->query('UPDATE user SET token = "'.$token.'" WHERE dni = "'.$dni.'"');

	                $ukey = $to_access['ukey'];
	                $name = $to_access['names'];
	                $TEMP['ukey'] = $ukey;
	                $TEMP['token'] = $token;
					$TEMP['code'] = $code;
					$TEMP['name'] = $name;
	                $send_email_data = array(
	                   	'from_email' => $TEMP['#settings']['smtp_username'],
	                    'from_name' => $TEMP['#settings']['title'],
	                    'to_email' => $to_access['email'],
	                    'to_name' => $name,
	                    'subject' => $TEMP['#word']['authentication'],
	                    'charSet' => 'UTF-8',
	                    'text_body' => Specific::Maket('emails/includes/authentication'),
	                    'is_html' => true
	                );
	                $send = Specific::SendEmail($send_email_data);
			       	if($send == true){
		                $deliver = array(
						    'status' => 401,
		            		'url' => "&one=authentication&tokenu=$token&ukey=".$ukey
						);
	                } else {
						$deliver = array(
							'status' => 400,
							'error' => $TEMP['#word']['error_occurred_to_send_mail']
						);
					}
	            } else {
	                $session_id = sha1(Specific::RandomKey()) . md5(time());
		            $session_details = json_encode(Specific::BrowserDetails()['details']);
		            $insert = $dba->query("INSERT INTO session (user_id, session_id, details, time) VALUES ({$to_access['id']},'$session_id','$session_details',".time().')')->insertId();

		            $_SESSION['_LOGIN_TOKEN'] = $session_id;
		            setcookie("_LOGIN_TOKEN", $session_id, time() + 315360000, "/");
		            $dba->query('UPDATE user SET ip = "'.Specific::GetClientIp().'" WHERE id = '.$to_access['id']);
		            $deliver = array(
		            	'status' => 200,
		            	'return' => $_POST['return']
		            );
	            }
	        }
      	} else {
	        $deliver = array(
		        'status' => 401,
	       		'error' => $error
		    );
	    }
	} else {
		$deliver = array(
			'status' => 204,
			'emptys' => $emptys
		);
	}
} else if($one == 'resend-token'){
	$ukey = Specific::Filter($_POST['ukey']);
	$token = Specific::Filter($_POST['tokenu']);
	$user = $dba->query('SELECT * FROM user WHERE ukey = "'.$ukey.'" AND token = "'.$token.'"')->fetchArray();
	if (!empty($user)) {
	    $code = rand(111111, 999999);
	    $token = md5($code);
	    $dba->query('UPDATE user SET token = "'.$token.'" WHERE ukey = "'.$ukey.'"');
		
		$TEMP['ukey'] = $ukey;
		$TEMP['token'] =  $token;
		$TEMP['code'] = $code;
		$TEMP['name'] = $user['names'];
	    $send_email_data = array(
	        'from_email' => $TEMP['#settings']['smtp_username'],
	        'from_name' => $TEMP['#settings']['title'],
	        'to_email' => $user['email'],
	        'to_name' => $user['names'],
	        'subject' => $TEMP['#word']['authentication'],
	        'charSet' => 'UTF-8',
	        'text_body' => Specific::Maket('emails/includes/authentication'),
	        'is_html' => true
	    );
	    $send = Specific::SendEmail($send_email_data);
		if($send == true){
			$deliver = array(
				'status' => 200,
				'token' => $token
			);
		} else {
			$deliver = array(
				'status' => 400,
		       	'error' => $TEMP['#word']['error_occurred_to_send_mail']
			);
		}
	} else {
		$deliver = array(
		    'status' => 204,
	   		'error' => $TEMP['#word']['error']
		);
	}
} else if($one == 'resend-email'){
	$ukey = Specific::Filter($_POST['ukey']);
	$token = Specific::Filter($_POST['tokenu']);
	if(!empty($ukey) && !empty($token)){
		$user = $dba->query('SELECT * FROM user WHERE ukey = "'.$ukey.'" AND token = "'.$token.'"')->fetchArray();
		if(!empty($user)) {
			$code = rand(111111,999999);
			$token = sha1($code);
			$dba->query('UPDATE user SET token = "'.$token.'" WHERE ukey = "'.$ukey.'"');

			$TEMP['ukey'] = $ukey;
			$TEMP['token'] = $token;
			$TEMP['code'] = $code;
			$TEMP['name'] = $user['names'];
			$TEMP['text'] = $TEMP['#word']['verify_your_account'].' '.$TEMP['#word']['of'].' '.$TEMP['#settings']['title'];
			$TEMP['footer'] = '<a target="_blank" href="'.Specific::Url("not-me/$token/$ukey").'" style="color: #999; text-decoration: underline;">'.$TEMP['#word']['let_us_know'].'</a>.';
			$TEMP['type'] = 'verify';

			$send = Specific::SendEmail(array(
				'from_email' => $TEMP['#settings']['smtp_username'],
	            'from_name' => $TEMP['#settings']['title'],
				'to_email' => $user['email'],
				'to_name' => $user['names'],
				'subject' => $TEMP['#word']['verify_your_account'],
				'charSet' => 'UTF-8',
		        'text_body' => Specific::Maket('emails/includes/verify-email'),
				'is_html' => true
			));
			if($send == true){
				$deliver = array(
				    'status' => 200,
				    'token' => $token
				);
			} else {
				$deliver = array(
				    'status' => 400,
			   		'error' => $TEMP['#word']['error_occurred_to_send_mail']
				);
			}
		} else {
			$deliver = array(
			    'status' => 204,
	   			'error' => $TEMP['#word']['error']
			);
		}
	} else {
		$deliver = array(
		    'status' => 204,
	   		'error' => $TEMP['#word']['error']
		);
	}
} else if($one == 'verify-code'){
	$ukey = Specific::Filter($_POST['ukey']);
	$token = Specific::Filter($_POST['tokenu']);
	if($TEMP['#settings']['authentication'] == 'on' && !empty($ukey)){
		if(!empty($token)){
			$user = $dba->query('SELECT * FROM user WHERE ukey = "'.$ukey.'" AND token = "'.md5($token).'"')->fetchArray();
			if (!empty($user)) {
			    $token = md5(rand(111111, 999999));
				$session_id = sha1(Specific::RandomKey()).md5(time());
			    if($dba->query('INSERT INTO session (user_id, session_id, time) VALUES ('.$user['id'].',"'.$session_id.'",'.time().')')->returnStatus()){
			    	$_SESSION['_LOGIN_TOKEN'] = $session_id;
				    setcookie("_LOGIN_TOKEN", $session_id, time() + 315360000, "/");
				    $dba->query('UPDATE user SET ip = "'.Specific::GetClientIp().'", token = "'.$token.'" WHERE id = '.$user['id']);
				    $deliver = array(
					    'status' => 200,
					    'url' => !empty($_POST['to']) ? $_POST['to'] : Specific::Url()
					);
			    }
			} else {
				$deliver = array(
				    'status' => 400,
				    'error' => $TEMP['#word']['wrong_confirm_code']
				);
			}
		} else {
			$deliver = array(
				'status' => 400,
				'error' => $TEMP['#word']['confirm_code']
			);
		}
	}
} else if($one == 'forgot-password'){
	$error = '';
	$email = Specific::Filter($_POST['email']);
   
    if (empty($email)) {
        $error = $TEMP['#word']['this_field_is_empty'];
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $TEMP['#word']['email_invalid_characters'];
    }

	if (empty($error)) {
	    $user = $dba->query('SELECT * FROM user WHERE email = "'.$email.'"')->fetchArray();
		if(!empty($user)){
	    	if ($user['status'] == 'deactivated') {
		        $deliver = array(
		         	'status' => 401,
		           	'html' => $TEMP['#word']['account_was_deactivated_owner_email_related'] . ' ' . $TEMP['#word']['if_you_need_more_help'] . ' <a class="color-blue" href="'.Specific::Url('contact').'" target="_self">' . $TEMP['#word']['contact_our_helpdesk'] . '</a>'
		        );
		    } else {
	           	$user = Specific::Data($user['id']);
	           	$code = time() + rand(111111,999999);
	           	$token = sha1($code);
	           	$dba->query('UPDATE user SET token = "'.$token.'" WHERE id = '.$user['id']);

	           	$TEMP['token'] = $token;
				$TEMP['name'] = $user['names'];
	           	$send_email_data = array(
	           		'from_email' => $TEMP['#settings']['smtp_username'],
		            'from_name' => $TEMP['#settings']['title'],
	           		'to_email' => $email,
	           		'to_name' => $user['names'],
	           		'subject' => $TEMP['#word']['reset_your_password'],
	           		'charSet' => 'UTF-8',
	           		'text_body' => Specific::Maket('emails/includes/reset-password'),
	           		'is_html' => true
	           	);
	            $send = Specific::SendEmail($send_email_data);
	            if($send == true){
	            	$deliver['status'] = 200;
	            } else {
	            	$deliver = array(
					    'status' => 400,
					    'error' => $TEMP['#word']['error_occurred_to_send_mail']
					);
	            }
	        }
	    } else {
	    	$deliver = array(
	    		'status' => 400,
	    		'error' => $TEMP['#word']['email_not_exist']
	    	);
	    }
    } else {
        $deliver = array(
			'status' => 400,
			'error' => $error
		);
    }
} else if($one == 'reset-password'){
	$errors = array();
	$emptys = array();
	$token = Specific::Filter($_POST['tokenu']);
	$password = Specific::Filter($_POST['password']);
	$re_password = Specific::Filter($_POST['re-password']);
	if(!empty($token)){
		$user_id = $dba->query('SELECT id FROM user WHERE token = "'.$token.'"')->fetchArray();
		if(empty($password)){
			$emptys[] = 'password';
		}
		if(empty($password)){
			$emptys[] = 're-password';
		}
		if(!empty($user_id)){
			if (empty($emptys)) {
			    if ($password != $re_password) {
			        $errors = array('error' => $TEMP['#word']['password_not_match'], 'el' => 're-password');
			    }
			    if (strlen($password) < 4 || strlen($password) > 25) {
			        $errors = array('error' => $TEMP['#word']['password_is_short'], 'el' => 'error');
			    }

			    if (empty($errors)) {
			       	$token = sha1(time() + rand(111111,999999));
			       	if ($dba->query('UPDATE user SET password = "'.sha1($password).'", token = "'.$token.'" WHERE id = '.$user_id)->returnStatus()) {
				        $deliver = array(
						    'status' => 200,
							'url' => '&one=login'
						);
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
		}
	}
} else if($one == 'register'){
	$dates 			= array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
	$errors      	= array();
	$emptys     	= array();
	$form_key = Specific::Filter($_POST['form_key']);
	$dni        = Specific::Filter($_POST['dni']);
	$names        = Specific::Filter($_POST['names']);
	$surnames        = Specific::Filter($_POST['surnames']);
    $password        = Specific::Filter($_POST['password']);
    $re_password      = Specific::Filter($_POST['re-password']);
    $email           = Specific::Filter($_POST['email']);
    $gender          = Specific::Filter($_POST['gender']);
    $day          = Specific::Filter($_POST['day']);
    $month          = Specific::Filter($_POST['month']);
    $year          = Specific::Filter($_POST['year']);
	$recaptcha = Specific::CheckRecaptcha($_POST['recaptcha']);
	if (empty($dni)){
		$emptys[] = 'dni';
	}
	if (empty($email)){
		$emptys[] = 'email';
	}
	if (empty($names)){
		$emptys[] = 'names';
	}
	if (empty($surnames)){
		$emptys[] = 'surnames';
	}
	if (empty($password)){
		$emptys[] = 'password';
	}
	if (empty($re_password)){
		$emptys[] = 're-password';
	}
	if (empty($gender)){
		$emptys[] = 'gender';
	}
	if (empty($day)){
		$emptys[] = 'day';
	}
	if (empty($month)){
		$emptys[] = 'month';
	}
	if (empty($year)){
		$emptys[] = 'year';
	}

	$access = $dba->query('SELECT access FROM form WHERE form_key = "'.$form_key.'"')->fetchArray();
	$access = explode(',', $access);
	if(empty($emptys)){
        if ($dba->query('SELECT COUNT(*) FROM user WHERE dni = "'.$dni.'"')->fetchArray() > 0) {
            $errors[] = array('error' => $TEMP['#word']['document_already_exists'], 'el' => 'dni');
        }
        if (!preg_match('/^[0-9]/', $dni)) {
            $errors[] = array('error' => $TEMP['#word']['invalid_document_characters'], 'el' => 'dni');
        }
        if ($dba->query('SELECT COUNT(*) FROM user WHERE email = "'.$email.'"')->fetchArray() > 0) {
            $errors[] = array('error' => $TEMP['#word']['email_exists'], 'el' => 'email');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = array('error' => $TEMP['#word']['email_invalid_characters'], 'el' => 'email');
        }
        if ($password != $re_password) {
            $errors[] = array('error' => $TEMP['#word']['password_not_match'], 'el' => 're-password');
        }
        if (strlen($password) < 4) {
            $errors[] = array('error' => $TEMP['#word']['password_is_short'], 'el' => 'password');
        }
        if (!in_array($gender, array(1, 2))) {
            $errors[] = array('error' => $TEMP['#word']['gender_is_invalid'], 'el' => 'gender');
        }
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
        if ($TEMP['#settings']['recaptcha'] == 'on') {
            if (!isset($_POST['recaptcha']) || empty($_POST['recaptcha']) || ($recaptcha["success"] == false && $recaptcha["score"] < 0.5)) {
                $errors[] = array('error' => $TEMP['#word']['recaptcha_error'], 'el' => 'g-recaptcha');
            }
        } 

        if (empty($errors)) {
        	if(in_array($dni, $access) || Specific::Academic() == true){
	        	$birthday = DateTime::createFromFormat('d-m-Y H:i:s', "$day-$month-$year 00:00:00");
	            $code = rand(111111,999999);
				$token = sha1($code);
				$ukey = Specific::RandomKey(12, 16);
				$ip = Specific::GetClientIp();
				$password = sha1($password);
	            $insert_array = array(
	            	'ukey' => "'$ukey'",
	                'dni' => "$dni",
	                'names' => "'$names'",
	                'surnames' => "'$surnames'",
	                'password' => "'$password'",
	                'email' => "'$email'",
	                'ip' => "'$ip'",
	                'gender' => "'$gender'",
	                'status' => $TEMP['#settings']['validate_email'] == 'on' ? "'pending'" : "'active'",
	                'token' => "'$token'",
	                'birthday' => $birthday->getTimestamp(),
	                'time' => time()
	            );
	            if ($gender == 1) {
	                $insert_array['avatar'] = "'images/default-avatar.jpg'";
	            }else{
	                $insert_array['avatar'] = "'images/default-favatar.jpg'";
	            }
	            $insert_array['language'] = "'{$TEMP['#settings']['language']}'";
	            if (!empty($_SESSION['language'])) {
	                if (in_array($_SESSION['language'], $TEMP['#languages'])) {
	                    $insert_array['language'] = "'{$_SESSION['language']}'";
	                }
	            }
	            $user_id = $dba->query('INSERT INTO user ('.implode(',', array_keys($insert_array)).') VALUES ('.implode(',', array_values($insert_array)).')')->insertId();

	            if($user_id) {
		            if ($TEMP['#settings']['validate_email'] == 'on') {
						$TEMP['ukey'] = $ukey;
		            	$TEMP['token'] = $token;
						$TEMP['code'] = $code;
						$TEMP['name'] = $names;
						$TEMP['text'] = $TEMP['#word']['verify_your_account'].' '.$TEMP['#word']['of'].' '.$TEMP['#settings']['title'];
						$TEMP['footer'] = '<a target="_blank" href="'.Specific::Url("not-me/$token/$ukey").'" style="color: #999; text-decoration: underline;">'.$TEMP['#word']['let_us_know'].'</a>.';
						$TEMP['type'] = 'verify';

		                $send = Specific::SendEmail(array(
		                    'from_email' => $TEMP['#settings']['smtp_username'],
			                'from_name' => $TEMP['#settings']['title'],
		                    'to_email' => $email,
		                    'to_name' => $names,
		                    'subject' => $TEMP['#word']['verify_your_account'],
		                    'charSet' => 'UTF-8',
					        'text_body' => Specific::Maket('emails/includes/verify-email'),
		                    'is_html' => true
		                ));
		                if($send == true){
			                $deliver['status'] = 200;
		               	} else {
							$deliver = array(
							    'status' => 400,
							    'error' => $TEMP['#word']['error_occurred_to_send_mail']
							);
						}
		            } else {
		                $session_id = sha1(Specific::RandomKey()) . md5(time());
				        $session_details = json_encode(Specific::BrowserDetails()['details']);
				        if($dba->query('INSERT INTO session (user_id, session_id, details, time) VALUES (?, ?, ?, ?)', $user_id, $session_id, $session_details, time())->insertId()){
				        	$_SESSION['_LOGIN_TOKEN'] = $session_id;
			                setcookie("_LOGIN_TOKEN", $session_id, time() + 315360000, "/");
			                $deliver = array(
								'status' => 200,
								'url' => Specific::Url()
							);
				        }
		            }
		        }
		    } else {
			    $deliver = array(
					'status' => 400,
				    'error' => $TEMP['#word']['you_do_not_have_access_this_form']
				);
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
} else if ($one == 'verify-email'){
	$ukey = Specific::Filter($_POST['ukey']);
	$token = Specific::Filter($_POST['tokenu']);
	if(!empty($token)){
		if(!empty($ukey)){
			$user = $dba->query('SELECT * FROM user WHERE ukey = "'.$ukey.'" AND token = "'.sha1($token).'"')->fetchArray();
			if(!empty($user)){
				if ($dba->query('UPDATE user SET status = "active", token = "'.sha1(rand(111111,999999)).'" WHERE id = '.$user['id'])->returnStatus()) {
					$session_id = sha1(Specific::RandomKey()).md5(time());
				    if($dba->query('INSERT INTO session (user_id, session_id, time) VALUES ('.$user['id'].',"'.$session_id.'",'.time().')')->returnStatus()){
				    	$_SESSION['_LOGIN_TOKEN'] = $session_id;
					    setcookie("_LOGIN_TOKEN", $session_id, time() + 315360000, "/");
					    $deliver = array(
							'status' => 200,
							'url' => Specific::Url()
						);
				    }
				}
			} else {
				$deliver = array(
					'status' => 400,
					'error' => $TEMP['#word']['wrong_confirm_code']
				);
			}
		}
	} else {
		$deliver = array(
			'status' => 400,
			'error' => $TEMP['#word']['confirm_code']
		);
	}
} else if($one == 'verify-change-email'){
	$ukey = Specific::Filter($_POST['ukey']);
	$token = Specific::Filter($_POST['tokenu']);
	if (!empty($ukey)) {
		if(!empty($token)){
			$user = $dba->query('SELECT * FROM user WHERE ukey = "'.$ukey.'" AND token = "'.md5($token).'"')->fetchArray();
			if(!empty($user)){
				if(Specific::IsOwner($user['id'])){
					$code = rand(111111, 999999);
				    $token = md5($code);
				    if($dba->query('UPDATE user SET token = ?, email = ?, change_email = ? WHERE id = '.$user['id'], $token, $user['change_email'], NULL)->returnStatus()){
				    	$deliver = array(
						    'status' => 200,
						    'url' => 'settings'
						);
				    }
				}
			} else {
				$deliver = array(
					'status' => 400,
					'error' => $TEMP['#word']['wrong_confirm_code']
				);
			}
		} else {
			$deliver = array(
				'status' => 400,
				'error' => $TEMP['#word']['confirm_code']
			);
		}
	}
} else if($one == 'resend-change-email'){
	$ukey = Specific::Filter($_POST['ukey']);
	$token = Specific::Filter($_POST['tokenu']);
	if(!empty($ukey) && !empty($token)){
		$user = $dba->query('SELECT * FROM user WHERE ukey = "'.$ukey.'" AND token = "'.$token.'"')->fetchArray();
		if (!empty($user)) {
		    $code = rand(111111, 999999);
		    $token = md5($code);
		    $dba->query('UPDATE user SET token = "'.$token.'" WHERE ukey = "'.$ukey.'"');

		    $TEMP['ukey'] = $ukey;
			$TEMP['token'] = $token;
			$TEMP['code'] = $code;
			$TEMP['name'] = $user['names'];
			$TEMP['text'] = $TEMP['#word']['check_your_new_email'];
	        $TEMP['footer'] = $TEMP['#word']['just_ignore_this_message'];
	        $TEMP['type'] = 'change';

		    $send_email_data = array(
		        'from_email' => $TEMP['#settings']['smtp_username'],
		        'from_name' => $TEMP['#settings']['title'],
		        'to_email' => $user['change_email'],
		        'to_name' => $user['names'],
		        'subject' => $TEMP['#word']['verify_your_account'],
		        'charSet' => 'UTF-8',
		        'text_body' => Specific::Maket('emails/includes/verify-email'),
		        'is_html' => true
		    );
		    $send = Specific::SendEmail($send_email_data);
		    if($send == true){
		    	$deliver = array(
					'status' => 200,
					'token' => $token
				);
		    } else {
		    	$deliver = array(
					'status' => 400,
			       	'error' => $TEMP['#word']['error_occurred_to_send_mail']
				);
		    }
			
		} else {
			$deliver = array(
			    'status' => 204,
		   		'error' => $TEMP['#word']['error']
			);
		}
	} else {
		$deliver = array(
			'status' => 204,
		   	'error' => $TEMP['#word']['error']
		);
	}
} else if($one == 'bubbles'){
	$bubbles = Specific::Filter($_POST['bubbles']);
	if(!empty($bubbles)){
		$bubbles = Specific::Bubbles(array('rands' => explode(',', $bubbles)));
		$deliver = array(
			'status' => 200,
			'bubble' => $bubbles['avatar'],
			'bubbles' => $bubbles['rands']
		);
	}
} else if($one == 'deactivate-account'){
	$user_id = Specific::Filter($_POST['user_id']);
	$token = Specific::Filter($_POST['tokenu']);
	$ukey = Specific::Filter($_POST['ukey']);
	if(!empty($user_id) && !empty($token) && !empty($ukey)){
		if($dba->query('UPDATE user SET status = "deactivated" WHERE id = '.$user_id.' AND token = "'.$token.'" AND ukey = "'.$ukey.'"')->returnStatus()){
			$deliver['status'] = 200;
		}
	}
}
?>