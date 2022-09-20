<?php
if($TEMP['#loggedin'] == true){
	if($one == 'account'){
		$input = Specific::Filter($_POST['input']);
		$value = Specific::Filter($_POST['value']);

		if(in_array($input, array('username', 'new_email', 'name', 'surname', 'about', 'birthday', 'gender', 'newsletter', '2check', 'facebook', 'twitter', 'instagram', 'main_sonet', 'contact_email', 'followers', 'messages'))){
			if(in_array($input, array('followers', 'messages'))){
				$shows = Specific::Shows($input, $value);
				if($shows['return']){
					$deliver = $shows['data'];
				}
			} else if($input != 'newsletter'){
				$no_emptys = array('username', 'new_email', 'gender', 'birthday');
				$red_social = array('facebook', 'twitter', 'instagram');
				if(!empty($value) || !in_array($input, $no_emptys)){
					$error = false;
					if($input == 'birthday'){
						$value = json_decode($value, true);
			            if(!in_array($value[1], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12))){
			                $error = true;
			            }
			            if(strlen($value[2]) != 4){
			                $error = true;
			            }
			            if(!checkdate($value[1], $value[0], $value[2])){
			                $error = true;
			            }
			            if($TEMP['#user']['birthday_changed'] != 0){
			                $error = true;
			            }
					}
					if($input == 'username' && (time() < $TEMP['#user']['user_changed'])){
						$error = true;
					}
					if($input == 'about'){
						if(strlen(strip_tags($value)) > 500){
							$error = true;
						}
					}
					if($input == 'gender' && !in_array($value, array('male', 'female'))){
						$error = true;
					}
					if($input == '2check' && !in_array($value, array('deactivated', 'activated'))){
						$error = true;
					}
					if($input == 'main_sonet' && !in_array($value, $red_social)){
						$error = true;
					}
					if($error == false){
						if(($input == 'name' && strlen($value) <= 55) || $input != 'name'){
							if(($input == 'surname' && strlen($value) <= 55) || $input != 'surname'){
								if(($input == 'username' && preg_match('/^[a-zA-Z0-9]+$/', $value)) || $input != 'username'){
									$emails = in_array($input, array('new_email', 'contact_email'));
									if(($emails && (filter_var($value, FILTER_VALIDATE_EMAIL) || ($input == 'contact_email' && empty($value)))) || !$emails){
										if((in_array($input, $red_social) && filter_var($value, FILTER_VALIDATE_URL) === false || empty($value)) || !in_array($input, $red_social)){
											$pass = true;
											if($input == 'birthday'){
											    if($value[0] == $TEMP['#user']['birth_day'] && $value[1] == $TEMP['#user']['birthday_month'] && $value[2] == $TEMP['#user']['birthday_year']){
											    	$pass = false;
											    }
									            $value = DateTime::createFromFormat('d-n-Y H:i:s', "{$value[0]}-{$value[1]}-{$value[2]} 00:00:00")->getTimestamp();
											}
											if($input != 'new_email' && $TEMP['#user'][$input] == $value){
												$pass = false;
											} else {
												if($input == 'username'){
													$time = strtotime("+3 month, 12:00am", time());
													$update = ", user_changed = $time";
													$deliver['EM'] = $TEMP['#word']['have_already_changed_username_change_day'].Specific::DateFormat($time);
												} else if($input == 'birthday'){
													$time = time();
													$update = ', birthday_changed = '.$time;
													$deliver['EM'] = $TEMP['#word']['just_changed_date_birth_day'].Specific::DateFormat($time);
												} else if($input == 'new_email' && $TEMP['#settings']['verify_email'] == 'on'){
													if($TEMP['#user']['email'] == $value){
														$pass = false;
														$deliver['EQ'] = 0;
														$deliver['EV'] = 0;
														if(!empty($TEMP['#user']['new_email'])){
															$deliver['EV'] = 1;
															$pass = true;
														}
														$deliver['EM'] = $TEMP['#word']['use_email_login_where_will_send'];
														$update = ", email = '$value', new_email = NULL";
													} else {
														if(empty($TEMP['#user']['new_email'])){
															$deliver['EQ'] = 1;
															$deliver['EV'] = 1;
															$deliver['EM'] = $TEMP['#word']['requested_change_email_need_verify'];
															$update = ", new_email = '$value'";
														} else {
															$deliver['EQ'] = 0;
															if($TEMP['#user']['new_email'] == $value){
																$deliver['EQ'] = 1;
																$pass = false;
															}
														}
													}
												}
											}

											if($pass == true){
												if($dba->query("UPDATE ".T_USER." SET $input = ? $update WHERE id = ?", $value, $TEMP['#user']['id'])->returnStatus()){
													$deliver['M'] = $value;
													if(empty($value) && !in_array($input, $no_emptys)){
														$deliver['M'] = $TEMP['#word'][$input];
													} else {	
														if($input == 'birthday'){
															$date_of_birth = Specific::DateFormat($value);
															$deliver['M'] = "{$TEMP['#word']['date_of_birth']} ({$date_of_birth})";
														} else if($input == 'gender'){
															$deliver['M'] = ucfirst($TEMP['#word']['gender'])." ({$TEMP['#word'][$value]})";
														} else if($input == '2check'){
															$deliver['M'] = "{$TEMP['#word']['2check']} ({$TEMP['#word'][$value]})";
														} else if($input == 'facebook'){
															$deliver['M'] = "{$TEMP['#word']['facebook']} ({$value})";
														} else if($input == 'twitter'){
															$deliver['M'] = "{$TEMP['#word']['twitter']} ({$value})";
														} else if($input == 'instagram'){
															$deliver['M'] = "{$TEMP['#word']['instagram']} ({$value})";
														} else if($input == 'main_sonet'){
															$deliver['M'] = "{$TEMP['#word']['main_social_network']} (".$TEMP['#word']["{$value}_"].")";
														} else if($input == 'contact_email'){
															$deliver['M'] = "{$TEMP['#word']['contact_email']} ({$value})";
														}


													}
													$deliver['S'] = 200;
												}
											} else {
												$deliver['S'] = 200;
											}
										} else {
											$deliver = array(
												'S' => 400,
												'E' => "*{$TEMP['#word']['please_enter_valid_username']}"
											);
										}
									} else {
										$deliver = array(
											'S' => 400,
											'E' => "*{$TEMP['#word']['enter_a_valid_email']}"
										);
									}
								} else {
									$deliver = array(
										'S' => 400,
										'E' => "*{$TEMP['#word']['write_only_numbers_letters']}"
									);
								}
							} else {
								$deliver = array(
									'S' => 400,
									'E' => "*{$TEMP['#word']['name_large_maximum_characters']}"
								);
							}
						} else {
							$deliver = array(
								'S' => 400,
								'E' => "*{$TEMP['#word']['surname_large_maximum_characters']}"
							);
						}
					}
				} else {
					$deliver = array(
						'S' => 400,
						'E' => "*{$TEMP['#word']['this_field_is_empty']}"
					);
				}
			} else {
				if(in_array($value, array('disabled', 'enabled'))){
					if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE email = ?', $TEMP['#user']['email'])->fetchArray(true) == 0){
						$slug = Specific::RandomKey(12, 16);
						if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE slug = ?', $slug)->fetchArray(true) > 0){
							$slug = Specific::RandomKey(12, 16);
						}
						if($dba->query('INSERT INTO '.T_NEWSLETTER.' (slug, email, created_at) VALUES (?, ?, ?)', $slug, $TEMP['#user']['email'], time())->returnStatus()){
							$deliver = array(
								'S' => 200,
								'M' => "{$TEMP['#word']['newsletter_settings']} ({$TEMP['#word'][$value]})",
								'HT' => "{$TEMP['#word']['configuration_tells_send_news']} <a class='color-blue hover-button animation-ease3s' href='".Specific::Url("{$TEMP['#r_newsletter']}/{$slug}")."' target='_self'>{$TEMP['#word']['see_detailed_settings']}</a>"
							);
						}
					} else {
						if($dba->query('UPDATE '.T_NEWSLETTER.' SET reason = NULL, status = ? WHERE email = ?', $value, $TEMP['#user']['email'])->returnStatus()){
							$slug = $dba->query('SELECT slug FROM '.T_NEWSLETTER.' WHERE email = ?', $TEMP['#user']['email'])->fetchArray(true);
							$deliver['HT'] = $value == 'enabled' ? "{$TEMP['#word']['configuration_tells_send_news']} <a class='color-blue hover-button animation-ease3s' href='".Specific::Url("{$TEMP['#r_newsletter']}/{$slug}")."' target='_self'>{$TEMP['#word']['see_detailed_settings']}</a>" : $TEMP['#word']['configuration_tells_send_news'];
							$deliver['S'] = 200;
							$deliver['M'] = "{$TEMP['#word']['newsletter_settings']} ({$TEMP['#word'][$value]})";
						}
					}
				}
			}
		}
	} else if($one == 'shows'){
		$shows = Specific::Shows($_POST['input'], $_POST['show']);
		if($shows['return']){
			$deliver = $shows['data'];
		}
	} else if($one == 'send-code'){
		if(!empty($TEMP['#user']['new_email'])){
			$change_email = Specific::UserToken('change_email', $TEMP['#user']['id']);
			$code = $change_email['code'];

			$TEMP['code'] = $code;
			$TEMP['username'] = $TEMP['#user']['username'];
			$TEMP['url'] = Specific::Url("{$TEMP['#r_change_email']}?{$TEMP['#p_insert']}=$code");
			$TEMP['text'] = "{$TEMP['#word']['check_your_email']} {$TEMP['#user']['new_email']}";
			$TEMP['footer'] = $TEMP['#word']['have_been_one_who_has_carried_out'];
			$TEMP['button'] = $TEMP['#word']['check_your_email'];

			if($change_email['return'] == true){
				$send = Specific::SendEmail(array(
					'from_email' => $TEMP['#settings']['smtp_username'],
				    'from_name' => $TEMP['#settings']['title'],
					'to_email' => $TEMP['#user']['new_email'],
					'to_name' => $TEMP['#user']['username'],
					'subject' => $TEMP['#word']['check_your_email'],
					'charSet' => 'UTF-8',
			        'text_body' => Specific::Maket('emails/includes/send-code'),
					'is_html' => true
				));
				if($send){
					$deliver = array(
					    'S' => 200,
						'M' => $TEMP['#word']['mail_sent_successfully'],
					    'TK' => $change_email['token']
					);
				} else {
					$deliver = array(
						'S' => 400,
				   		'E' => "*{$TEMP['#word']['error_sending_email_again_later']}"
					);
				}
			}
		}
	} else if($one == 'verify-code'){
		$code = Specific::Filter($_POST['code']);
		if(!empty($code)){
			$user_id = $dba->query("SELECT user_id FROM ".T_TOKEN." WHERE change_email = ?", md5($code))->fetchArray(true);
			if (!empty($user_id)) {
				$change_email = Specific::UserToken('change_email', $user_id);
				if($change_email['return'] == true){
					if($dba->query('UPDATE '.T_USER.' SET email = ?, new_email = NULL, type = "normal" WHERE id = ?', $TEMP['#user']['new_email'], $TEMP['#user']['id'])->returnStatus()){
						$deliver['S'] = 200;
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
	} else if($one == 'upload-avatar'){
        if(!empty($_FILES['avatar'])){
            if(!empty($_FILES['avatar']['tmp_name'])){
                $upload_avatar = Specific::UploadAvatar($_FILES);
                if ($upload_avatar['return'] == true) {
                    if($TEMP['#user']['avatar'] != 'default-holder'){
                        unlink($TEMP['#user']['ex_avatar_b']);
                        unlink($TEMP['#user']['ex_avatar_s']);
                    }
                    if ($dba->query('UPDATE '.T_USER.' SET avatar = ? WHERE id = ?', $upload_avatar['image'], $TEMP['#user']['id'])->returnStatus()) {
                        $deliver = array(
                        	'S' => 200,
                        	'AV' => $upload_avatar['avatar_s'],
			        		'EM' => $TEMP['#word']['upload_a_picture'],
			        		'ED' => $TEMP['#word']['delete']
                        );
                    }
                } else {
                	$deliver = array(
                		'S' => 400,
                		'E' => "*{$TEMP['#word']['file_not_supported']}",
                		'DH' => $TEMP['#user']['avatar'] != 'default-holder' ? 1 : 0,
		        		'EM' => $TEMP['#word']['try_again'],
		        		'ED' => $TEMP['#word']['delete']
                	);
                }
            }
        }
    } else if($one == 'reset-avatar'){
    	if($TEMP['#user']['avatar'] != 'default-holder'){
	        if($dba->query('UPDATE '.T_USER.' SET avatar = "default-holder" WHERE id = ?', $TEMP['#user']['id'])->returnStatus()){
	        	unlink($TEMP['#user']['ex_avatar_b']);
                unlink($TEMP['#user']['ex_avatar_s']);
	        	$deliver = array(
	        		'S' => 200,
	        		'AV' => Specific::GetFile('default-holder', 5, 's'),
	        		'EM' => $TEMP['#word']['upload_a_picture'],
	        		'ED' => $TEMP['#word']['delete']
	        	);
	        }
	    }
    } else if($one == 'change-password'){
		$current_password = Specific::Filter($_POST['current-password']);
		$password = Specific::Filter($_POST['password']);
		$re_password = Specific::Filter($_POST['re-password']);
		if(!empty($current_password) && !empty($password) && !empty($re_password) && $password == $re_password){
			if(password_verify($current_password, $TEMP['#user']['password'])){
				$password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
				if ($dba->query('UPDATE '.T_USER.' SET password = ? WHERE id = ?', $password, $TEMP['#user']['id'])->returnStatus()) {
					$deliver['S'] = 200;
				}
			} else {
				$deliver = array(
					'S' => 400,
					'E' => $TEMP['#word']['current_password_not_match'],
					'EX' => 1
				);
			}
		}
	} else if ($one == 'delete-session'){
    	$session_id = Specific::Filter($_POST['session_id']);
        if (!empty($session_id)) {
	        $session = $dba->query('SELECT user_id, token FROM '.T_SESSION.' WHERE id = ?', $session_id)->fetchArray();
	        if (!empty($session)) {
	            $deliver['RL'] = 0;
	            if (($session['user_id'] == $TEMP['#user']['id'])) {
		            if ($dba->query('DELETE FROM '.T_SESSION.' WHERE id = ?', $session_id)->returnStatus()) {
		                $deliver['S'] = 200;
		                if ((!empty($_SESSION['_LOGIN_TOKEN']) && $_SESSION['_LOGIN_TOKEN'] == $session['token']) || (!empty($_COOKIE['_LOGIN_TOKEN']) && $_COOKIE['_LOGIN_TOKEN'] == $session['token'])) {
		                    setcookie('_LOGIN_TOKEN', null, -1, '/');
							if (isset($_COOKIE['_SAVE_SESSION'])) {
							    setcookie('_SAVE_SESSION', null, -1, '/');
							}
		                    session_destroy();
		                    $deliver['RL'] = 1;
		                }
		            }
		        }
	        }
	    }
    } else if($one == 'table-sessions'){
        $page = Specific::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $user_sessions = $dba->query("SELECT * FROM ".T_SESSION." WHERE user_id = {$TEMP['#user']['id']} ORDER BY id DESC LIMIT ? OFFSET ?", 10, $page)->fetchAll();
            if (!empty($user_sessions)) {
                foreach ($user_sessions as $value) {
                    $TEMP['!id'] = $value['id'];
                    $session = Specific::GetSessions($value);
                    $TEMP['!ip'] = $session['ip'];
                    $TEMP['!browser'] = $session['browser'];
                    $TEMP['!platform'] = $session['platform'];
                    $TEMP['!created_at'] = Specific::DateFormat($value['created_at']);
                    $html .= Specific::Maket("settings/logins/includes/sessions");
                }
                Specific::DestroyMaket();
            }
            $deliver = array(
            	'S' => 200,
            	'HT' => $html
            );
        }
    }
}