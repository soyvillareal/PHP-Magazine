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

if($one == 'validate'){
	$username = Functions::Filter($_POST['username']);
	$email = Functions::Filter($_POST['email']);
	if(!empty($username)){
		$query = '';
		if($TEMP['#loggedin'] == true){
			$query = " AND username <> '{$TEMP['#user']['user']}'";
		}
		if($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE username = ?'.$query, $username)->fetchArray(true) > 0){
			$deliver = array(
				'S' => 200,
				'E' => "*{$TEMP['#word']['username_already_exists']}"
			);
		}
	} else if(!empty($email)){
		$query = '';
		if($TEMP['#loggedin'] == true){
			$query = " AND email <> '{$TEMP['#user']['email']}'";
		}
		if($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE email = ?'.$query, $email)->fetchArray(true) > 0){
			$deliver = array(
				'S' => 200,
				'E' => "*{$TEMP['#word']['email_already_registered']}"
			);
		}
	}
} else {
	if ($TEMP['#loggedin'] == false) {
		if($one == 'login'){
			$username = Functions::Filter($_POST['username']);
			$password = Functions::Filter($_POST['password']);
			$save_session = Functions::Filter($_POST['save_session']);
			$return_url = Functions::Filter($_POST['return_url']);
			$return_param = Functions::Filter($_POST['return_param']);

			if(!empty($username) && !empty($password)){
			    $user = $dba->query('SELECT * FROM '.T_USER.' WHERE username = ? AND status <> "deleted"', $username)->fetchArray();
			    $password_verify = password_verify($password, $user['password']);

			    $recaptcha_success = true;
			    if ($TEMP['#settings']['recaptcha'] == 'on') {
					$recaptcha = Functions::CheckRecaptcha($_POST['recaptcha']);
			        if (!isset($_POST['recaptcha']) || empty($_POST['recaptcha']) || $recaptcha["action"] != 'login' || $recaptcha["success"] == false || $recaptcha["score"] < 0.5) {
			            $recaptcha_success = false;
			        }
			    } 

			    if($recaptcha_success){
				    if(!empty($user) && $password_verify == true) {
				        if($user['status'] == 'pending') {
				           	$deliver = array(
				           		'S' => 401,
				           		'E' => "*{$TEMP['#word']['account_pending_verification']} <button id='btn-rcode' class='btn-noway color-blue' type='button'>{$TEMP['#word']['resend_email']}</button>",
				           		'EE' => 'pending',
				           		'TK' => $dba->query('SELECT verify_email FROM '.T_TOKEN.' WHERE user_id = ?', $user['id'])->fetchArray(true)
				            );
				        } else if ($user['status'] == 'deactivated') {
				           	$deliver = array(
				           		'S' => 401,
				           		'E' => "*{$TEMP['#word']['account_was_deactivated_if_need_help']} <a class='color-blue' href='".Functions::Url($ROUTE['#r_contact'])."' target='_blank'>{$TEMP['#word']['contact_us']}</a>",
				           		'EE' => 'deactivated'
				            );
				        } else {
				        	if ($user['2check'] == 'activated' && $user['ip'] != Functions::GetClientIp()) {
								$user = Functions::Data($user, 3);
								$_2check = Functions::UserToken('2check', $user['id'], true);
				            	$code = $_2check['code'];
				           		$token = $_2check['token'];


								if($_2check['return']){
				           			$reset_password = $dba->query('SELECT reset_password FROM '.T_TOKEN.' WHERE user_id = ?', $user['id'])->fetchArray(true);

									$TEMP['code'] = $code;
									$TEMP['username'] = $user['username'];
									$TEMP['url'] = Functions::Url("{$ROUTE['#r_2check']}/$token?{$ROUTE['#p_insert']}=$code");
									$TEMP['text'] = $TEMP['#word']['confirm_are_who_trying_enter'];
									$TEMP['footer'] = "{$TEMP['#word']['arent_trying_access']}, {$TEMP['#word']['we_recommend_you']} <a target='_blank' href='".Functions::Url("{$ROUTE['#r_reset_password']}/{$reset_password}")."' style='display:inline-block;'>{$TEMP['#word']['change_your_password']}</a>.";
									$TEMP['button'] = $TEMP['#word']['enter_code'];

									$send = Functions::SendEmail(array(
										'from_email' => $TEMP['#settings']['from_email'],
							            'from_name' => $TEMP['#settings']['title'],
										'to_email' => $user['email'],
										'to_name' => $user['username'],
										'subject' => $TEMP['#word']['2check'],
										'charSet' => 'UTF-8',
								        'text_body' => Functions::Build('emails/includes/send-code'),
										'is_html' => true
									));
									if($send){
								        if($save_session == 'on'){
								            setcookie("_SAVE_SESSION", $TEMP['#token_session'], time() + 315360000, "/");
								        }
										$deliver = array(
										    'S' => 200,
										    'UR' => Functions::Url("{$ROUTE['#r_2check']}/{$token}{$return_param}")
										);
									} else {
										$deliver = array(
										    'S' => 400,
									   		'E' => "*{$TEMP['#word']['error_sending_email_again_later']}"
										);
									}
								} else {
									$deliver = array(
										'S' => 400,
										'E' => "*{$TEMP['#word']['made_too_many_attempts_try']}"
									);
								}
				            } else {
					            $login_token = sha1(Functions::RandomKey().md5(time()));
						        if($dba->query('INSERT INTO '.T_SESSION.' (user_id, token, details, created_at) VALUES (?, ?, ?, ?)', $user['id'], $login_token, json_encode(Functions::BrowserDetails()['details']), time())->returnStatus()){
							        if($save_session == 'on'){
							            setcookie("_SAVE_SESSION", $login_token, time() + 315360000, "/");
							        }
							        $_SESSION['_LOGIN_TOKEN'] = $login_token;
							        setcookie("_LOGIN_TOKEN", $login_token, time() + 315360000, "/");
							        $dba->query('UPDATE '.T_USER.' SET ip = ? WHERE id = ?', Functions::GetClientIp(), $user['id']);
							        $deliver = array(
							            'S' => 200,
							            'UR' => $return_url
							        );
							    }
							}
				        }
			      	} else {
				        $deliver = array(
					        'S' => 401,
				       		'E' => "*{$TEMP['#word']['incorrect_user_password']}"
					    );
				    }
				} else {
			        $deliver = array(
			        	'S' => 400,
			        	'E' => "*{$TEMP['#word']['recaptcha_error']}"
			        );
				}
			}
		} else if($one == 'register'){
			$username = Functions::Filter($_POST['username']);
		    $email = Functions::Filter($_POST['email']);
		    $password = Functions::Filter($_POST['password']);
		    $re_password = Functions::Filter($_POST['re_password']);
		    $accept_checkbox = Functions::Filter($_POST['accept_checkbox']);
		    $return_url = Functions::Filter($_POST['return_url']);
		    $return_param = Functions::Filter($_POST['return_param']);

		    if(!empty($username) && !empty($email) && !empty($password) && $accept_checkbox == 'on' && !empty($re_password)){
		    	if(filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^[a-zA-Z0-9]+$/', $username) && $password == $re_password){
			    	if ($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE username = ? OR email = ?', $username, $email)->fetchArray(true) == 0) {
			    		$recaptcha_success = true;
					    if ($TEMP['#settings']['recaptcha'] == 'on') {
							$recaptcha = Functions::CheckRecaptcha($_POST['recaptcha']);
					        if (!isset($_POST['recaptcha']) || empty($_POST['recaptcha']) || $recaptcha["action"] != 'register' || $recaptcha["success"] == false || $recaptcha["score"] < 0.5) {
					            $recaptcha_success = false;
					        }
					    } 

				    	if($recaptcha_success){
							$slug = Functions::RandomKey(12, 16);
							$ip = Functions::GetClientIp();
							$password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
				            $status = 'active';
				            if($TEMP['#settings']['verify_email'] == 'on'){
				            	$status = 'pending';
				            }
							$darkmode = 0;
							if($TEMP['#settings']['switch_mode'] == 'on' && $TEMP['#settings']['theme_mode'] == 'night'){
								$darkmode = 1;
							}

							$role = 'viewer';
							if($dba->query('SELECT COUNT(*) FROM '.T_USER)->fetchArray(true) == 0){
								$role = 'admin';
							}

				            $user_id = $dba->query('INSERT INTO '.T_USER.' (username, email, password, ip, darkmode, status, role, type, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, "normal", ?)', $username, $email, $password, $ip, $darkmode, $status, $role, time())->insertId();

				            if($user_id) {
				            	$verify_email = Functions::UserToken('verify_email');
				            	$code = $verify_email['code'];
				            	$token = $verify_email['token'];

							    $change_email = Functions::UserToken('change_email')['token'];
							    $reset_password = Functions::UserToken('reset_password')['token'];
							    $unlink_email = Functions::UserToken('unlink_email')['token'];
							    $_2check = Functions::UserToken('2check')['token'];

							    if($dba->query('INSERT INTO '.T_TOKEN.' (user_id, verify_email, change_email, reset_password, unlink_email, 2check, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', $user_id, $token, $change_email, $reset_password, $unlink_email, $_2check, time())->returnStatus()){
						            if ($TEMP['#settings']['verify_email'] == 'on') {
						            	$user = Functions::Data($user_id);
										$TEMP['code'] = $code;
										$TEMP['username'] = $user['username'];

										$TEMP['url'] = Functions::Url("{$ROUTE['#r_verify_email']}/$token?{$ROUTE['#p_insert']}=$code");
										$TEMP['text'] = "{$TEMP['#word']['verify_your_account']} {$TEMP['#word']['of']} {$TEMP['#settings']['title']}";
										$TEMP['footer'] = "{$TEMP['#word']['one_who_has_registered_this_account']}, <a target='_blank' href='".Functions::Url("{$ROUTE['#r_unlink_email']}/$unlink_email")."' style='display:inline-block;'>{$TEMP['#word']['let_us_know']}</a>.";
										$TEMP['button'] = $TEMP['#word']['verify_your_account'];

						                $send = Functions::SendEmail(array(
						                    'from_email' => $TEMP['#settings']['from_email'],
							                'from_name' => $TEMP['#settings']['title'],
						                    'to_email' => $user['email'],
						                    'to_name' => $user['username'],
						                    'subject' => $TEMP['#word']['verify_your_account'],
						                    'charSet' => 'UTF-8',
									        'text_body' => Functions::Build('emails/includes/send-code'),
						                    'is_html' => true
						                ));
						                if($send){
							                $deliver = array(
							                	'S' => 200,
							                	'UR' => Functions::Url("{$ROUTE['#r_verify_email']}/{$token}{$return_param}")
							                );
						               	} else {
											$deliver = array(
											    'S' => 400,
											    'E' => "*{$TEMP['#word']['error_sending_email_again_later']}"
											);
										}
						            } else {
						                $login_token = sha1(Functions::RandomKey().md5(time()));
								        if($dba->query('INSERT INTO '.T_SESSION.' (user_id, token, details, created_at) VALUES (?, ?, ?, ?)', $user_id, $login_token, json_encode(Functions::BrowserDetails()['details']), time())->returnStatus()){
								        	$_SESSION['_LOGIN_TOKEN'] = $login_token;
							                setcookie("_LOGIN_TOKEN", $login_token, time() + 315360000, "/");
							    			$dba->query('UPDATE '.T_USER.' SET ip = ? WHERE id = ?', Functions::GetClientIp(), $user['id']);
							                $deliver = array(
							                	'S' => 200,
							                	'UR' => $return_url
							                );
								        }
						            }
						        }
							}
						} else {
					        $deliver = array(
					        	'S' => 400,
					        	'E' => "*{$TEMP['#word']['recaptcha_error']}"
					        );
						}
					}
		    	}
		    }
		} else if($one == 'forgot-password'){
			$email = Functions::Filter($_POST['email']);

			if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
			    $user = $dba->query('SELECT * FROM '.T_USER.' WHERE email = ? AND status <> "deleted"', $email)->fetchArray();
				if(!empty($user)){
					$recaptcha_success = true;
					if ($TEMP['#settings']['recaptcha'] == 'on'){
						$recaptcha = Functions::CheckRecaptcha($_POST['recaptcha']);
					    if (!isset($_POST['recaptcha']) || empty($_POST['recaptcha']) || $recaptcha["action"] != 'forgot_password' || $recaptcha["success"] == false || $recaptcha["score"] < 0.5){
					        $recaptcha_success = false;
					    }
					} 

				    if($recaptcha_success){
				    	if ($user['status'] == 'pending'){
				           	$deliver = array(
				           		'S' => 401,
				           		'E' => "*{$TEMP['#word']['account_pending_verification']} <button id='btn-rcode' class='btn-noway color-blue' type='button'>{$TEMP['#word']['resend_email']}</button>",
				           		'EE' => 'pending',
				           		'TK' => $dba->query('SELECT verify_email FROM '.T_TOKEN.' WHERE user_id = ?', $user['id'])->fetchArray(true)
				            );
				        } else if ($user['status'] == 'deactivated'){
					        $deliver = array(
					         	'S' => 401,
				           		'E' => "*{$TEMP['#word']['account_was_deactivated_if_need_help']} <a class='color-blue' href='".Functions::Url($ROUTE['#r_contact'])."' target='_blank'>{$TEMP['#word']['contact_us']}</a>",
				           		'EE' => 'deactivated'
					        );
					    } else { 	
							$user = Functions::Data($user, 3);
						    $reset_password = Functions::UserToken('reset_password', $user['id'], true);
						    $token = $reset_password['token'];

				           	if($reset_password['return']){
					           	$TEMP['token'] = $token;
								$TEMP['username'] = $user['username'];
					            $send = Functions::SendEmail(array(
					           		'from_email' => $TEMP['#settings']['from_email'],
						            'from_name' => $TEMP['#settings']['title'],
					           		'to_email' => $email,
					           		'to_name' => $user['username'],
					           		'subject' => $TEMP['#word']['reset_password'],
					           		'charSet' => 'UTF-8',
					           		'text_body' => Functions::Build('emails/includes/reset-password'),
					           		'is_html' => true
					           	));
					            if($send){
					            	$deliver = array(
					            		'S' => 200,
								    	'M' => $TEMP['#word']['mail_sent_successfully']
					            	);
					            } else {
					            	$deliver = array(
									    'S' => 400,
									    'E' => "*{$TEMP['#word']['error_sending_email_again_later']}"
									);
					            }
					        } else {
					        	$deliver = array(
									'S' => 400,
									'E' => "*{$TEMP['#word']['made_too_many_attempts_try']}"
								);
					        }
				        }
					} else {
					    $deliver = array(
					       	'S' => 400,
					       	'E' => "*{$TEMP['#word']['recaptcha_error']}"
					    );
					}
			    } else {
			    	$deliver = array(
			    		'S' => 400,
			    		'E' => "*{$TEMP['#word']['email_not_exist']}"
			    	);
			    }
		    }
		} else if($one == 'reset-password'){
			$tokenu = Functions::Filter($_POST['tokenu']);
			$password = Functions::Filter($_POST['password']);
			$re_password = Functions::Filter($_POST['re_password']);
			if(!empty($tokenu)){
				$user_id = $dba->query('SELECT user_id FROM '.T_TOKEN.' WHERE reset_password = ?', $tokenu)->fetchArray();
				if(!empty($user_id)){
					if(!empty($password) && !empty($re_password) && $password == $re_password){
						$reset_password = Functions::UserToken('reset_password', $user_id);
						$token = $reset_password['token'];
					    if($reset_password['return']){
					       	$password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
							if ($dba->query('UPDATE '.T_USER.' SET password = ? WHERE id = ? AND status = "active"', $password, $user_id)->returnStatus()) {
								$deliver['S'] = 200;
							}
						}
					}
				}
			}
		} else if($one == 'resend-code'){
			$tokenu = Functions::Filter($_POST['tokenu']);
			$type = Functions::Filter($_POST['type']);
			if(!empty($tokenu) && !empty($type) && in_array($type, array('verify_email', '2check'))){
				$user_id = $dba->query("SELECT user_id FROM ".T_TOKEN." WHERE $type = ?", $tokenu)->fetchArray(true);
				if(!empty($user_id)) {
					$user = Functions::Data($user_id, array('id', 'username', 'email', 'status'));
					if(in_array($user['status'], array('active', 'pending'))){
						$pass = false;
						$TEMP['username'] = $user['username'];
						if($type == 'verify_email'){
						    $verify_email = Functions::UserToken('verify_email', $user['id'], true);
						    $pass = $verify_email['return'];
				            $code = $verify_email['code'];
				            $token = $verify_email['token'];

				            $unlink_email = $dba->query('SELECT unlink_email FROM '.T_TOKEN.' WHERE user_id = ?', $user['id'])->fetchArray(true);

							$TEMP['code'] = $code;
							$TEMP['url'] = Functions::Url("{$ROUTE['#r_verify_email']}/$token?{$ROUTE['#p_insert']}=$code");
							$TEMP['text'] = "{$TEMP['#word']['verify_your_account']} {$TEMP['#word']['of']} {$TEMP['#settings']['title']}";
							$TEMP['footer'] = "{$TEMP['#word']['one_who_has_registered_this_account']}, <a target='_blank' href='".Functions::Url("{$ROUTE['#r_unlink_email']}/$unlink_email")."'>{$TEMP['#word']['let_us_know']}</a>.";
							$TEMP['button'] = $TEMP['#word']['verify_your_account'];
							$subject = $TEMP['#word']['verify_your_account'];
						} else {
						    $_2check = Functions::UserToken('2check', $user['id'], true);
						    $pass = $_2check['return'];
				            $code = $_2check['code'];
				           	$token = $_2check['token'];

				           	$reset_password = $dba->query('SELECT reset_password FROM '.T_TOKEN.' WHERE user_id = ?', $user['id'])->fetchArray(true);

							$TEMP['code'] = $code;
							$TEMP['url'] = Functions::Url("{$ROUTE['#r_2check']}/$token?{$ROUTE['#p_insert']}=$code");
							$TEMP['text'] = $TEMP['#word']['confirm_are_who_trying_enter'];
							$TEMP['footer'] = "{$TEMP['#word']['arent_trying_access']}, {$TEMP['#word']['we_recommend_you']} <a target='_blank' href='".Functions::Url("{$ROUTE['#r_reset_password']}/{$reset_password}")."' style='display:inline-block;'>{$TEMP['#word']['change_your_password']}</a>.";
							$TEMP['button'] = $TEMP['#word']['enter_code'];
							$subject = $TEMP['#word']['2check'];
						}

						if($pass){
							$send = Functions::SendEmail(array(
								'from_email' => $TEMP['#settings']['from_email'],
					            'from_name' => $TEMP['#settings']['title'],
								'to_email' => $user['email'],
								'to_name' => $user['username'],
								'subject' => $subject,
								'charSet' => 'UTF-8',
						        'text_body' => Functions::Build('emails/includes/send-code'),
								'is_html' => true
							));
							if($send){
								$deliver = array(
								    'S' => 200,
								    'M' => $TEMP['#word']['mail_sent_successfully'],
								    'TK' => $token
								);
							} else {
								$deliver = array(
								    'S' => 400,
							   		'E' => "*{$TEMP['#word']['error_sending_email_again_later']}"
								);
							}
						} else {
							$deliver = array(
								'S' => 400,
								'E' => "*{$TEMP['#word']['made_too_many_attempts_try']}"
							);
						}
					}
				}
			}
		} else if($one == 'verify-code'){
			$code = Functions::Filter($_POST['code']);
			$type = Functions::Filter($_POST['type']);
			$return_url = Functions::Filter($_POST['return_url']);
			if(!empty($type) && in_array($type, array('verify_email', '2check'))){
				if(!empty($code)){
					$user_id = $dba->query("SELECT user_id FROM ".T_TOKEN." WHERE {$type} = ?", md5($code))->fetchArray(true);
					if (!empty($user_id)){
						if($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND (status = "active" OR status = "pending")', $user_id)->fetchArray(true) > 0){
							$user_token = Functions::UserToken($type, $user_id);
							if($user_token['return']){
								$login_token = sha1(Functions::RandomKey().md5(time()));
							    if($dba->query('INSERT INTO '.T_SESSION.' (user_id, token, details, created_at) VALUES (?, ?, ?, ?)', $user_id, $login_token, json_encode(Functions::BrowserDetails()['details']), time())->returnStatus()){
							    	if(!empty($_COOKIE['_SAVE_SESSION']) && $_COOKIE['_SAVE_SESSION'] == $_SESSION['_LOGIN_TOKEN']){
									    setcookie("_SAVE_SESSION", $login_token, time() + 315360000, "/");
									}
								    $_SESSION['_LOGIN_TOKEN'] = $login_token;
								    setcookie("_LOGIN_TOKEN", $login_token, time() + 315360000, "/");
								    $dba->query('UPDATE '.T_USER.' SET ip = ?, status = "active" WHERE id = ?', Functions::GetClientIp(), $user_id);
								    $deliver = array(
								        'S' => 200,
								        'UR' => $return_url
								    );
								}
							}
						}
					} else {
						$deliver = array(
							'S' => 400,
							'E' => "*{$TEMP['#word']['wrong_confirm_code']}"
						);
					}
				} else {
					$deliver = array(
						'S' => 400,
						'E' => "*{$TEMP['#word']['confirm_code']}"
					);
				}
			}
		} else if($one == 'deactivate-account'){
			$tokenu = Functions::Filter($_POST['tokenu']);
			if(!empty($token)){
				$user_id = $dba->query('SELECT user_id FROM '.T_TOKEN.' t WHERE unlink_email = ? AND (SELECT id FROM '.T_USER.' WHERE status = "pending" AND id = t.user_id) = user_id', $tokenu)->fetchArray(true);
				if(!empty($user_id)){
					if($dba->query('UPDATE '.T_USER.' SET status = "deactivated" WHERE id = ?', $user_id)->returnStatus()){
						$deliver['S'] = 200;
					}
				}
			}
		}
	} else {
		$deliver = array(
			'S' => 400,
			'E' => "*{$TEMP['#word']['already_logged_in']}"
		);
	}
}
?>