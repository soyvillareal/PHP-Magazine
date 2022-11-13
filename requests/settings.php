<?php
if($TEMP['#loggedin'] == true){
	$TEMP['#data'] = $TEMP['#user'];
	if($TEMP['#moderator'] == true){
		$user_id = Functions::Filter($_POST['user_id']);
		if(!empty($user_id) && $TEMP['#user']['id'] != $user_id){
			$TEMP['#data'] = Functions::Data($user_id);
		}
	}
	if($one == 'account'){
		$input = Functions::Filter($_POST['input']);
		$value = Functions::Filter($_POST['value']);

		if(in_array($input, array('username', 'new_email', 'name', 'surname', 'about', 'birthday', 'gender', 'newsletter', '2check', 'facebook', 'twitter', 'instagram', 'main_sonet', 'contact_email', 'followers', 'messages'))){
			if(in_array($input, array('followers', 'messages'))){
				$shows = Functions::Shows($input, $value);
				if($shows['return']){
					$deliver = $shows['data'];
				}
			} else if($input != 'newsletter'){
				$no_emptys = array('username', 'new_email', 'gender', 'birthday');
				$red_social = array('facebook', 'twitter', 'instagram');
				$trim_value = trim($value);
				if(!empty($trim_value) || !in_array($input, $no_emptys)){
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
			            if($TEMP['#data']['birthday_changed'] != 0){
			                $error = true;
			            }
					}
					if($input == 'username' && (time() < $TEMP['#data']['user_changed'])){
						$error = true;
					}
					if($input == 'about'){
						if(mb_strlen(strip_tags($value), "UTF8") > $TEMP['#settings']['max_words_about']){
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
							if(($input == 'new_email' && $dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE email = ?', $value)->fetchArray(true) == 0) || $input != 'new_email'){
								if(($input == 'surname' && strlen($value) <= 55) || $input != 'surname'){
									if(($input == 'username' && preg_match('/^[a-zA-Z0-9]+$/', $value)) || $input != 'username'){
										$emails = in_array($input, array('new_email', 'contact_email'));
										if(($emails && (filter_var($value, FILTER_VALIDATE_EMAIL) || ($input == 'contact_email' && empty($trim_value)))) || !$emails){
											if((in_array($input, $red_social) && filter_var($value, FILTER_VALIDATE_URL) === false || empty($trim_value)) || !in_array($input, $red_social)){
												$pass = true;
												$send_email = false;
												if($input == 'birthday'){
												    if($value[0] == $TEMP['#data']['birth_day'] && $value[1] == $TEMP['#data']['birthday_month'] && $value[2] == $TEMP['#data']['birthday_year']){
												    	$pass = false;
												    }
										            $value = DateTime::createFromFormat('d-n-Y H:i:s', "{$value[0]}-{$value[1]}-{$value[2]} 00:00:00")->getTimestamp();
												}
												if($input != 'new_email' && $TEMP['#data'][$input] == $value){
													$pass = false;
												} else {
													if($input == 'username'){
														$time = strtotime("+3 month, 12:00am", time());
														$update = ", user_changed = $time";
														$deliver['EM'] = $TEMP['#word']['have_already_changed_username_change_day'].Functions::DateFormat($time);
													} else if($input == 'about'){
														if(empty($trim_value)){
															$update = ", about = NULL";
														}
													} else if($input == 'birthday'){
														$time = time();
														$update = ', birthday_changed = '.$time;
														$deliver['EM'] = $TEMP['#word']['just_changed_date_birth_day'].Functions::DateFormat($time);
													} else if($input == 'new_email'){
														if($TEMP['#settings']['verify_email'] == 'on'){
															if($TEMP['#data']['email'] == $value){
																$pass = false;
																$deliver['EQ'] = 0;
																$deliver['EV'] = 0;
																if(!empty($TEMP['#data']['new_email'])){
																	$deliver['EV'] = 1;
																	$pass = true;
																}
																$deliver['EM'] = $TEMP['#word']['use_email_login_where_will_send'];
																$update = ", email = '$value', new_email = NULL";
															} else {
																if(empty($TEMP['#data']['new_email'])){
																	$send_email = true;
																	$deliver['EQ'] = 1;
																	$deliver['EV'] = 1;
																	$deliver['EM'] = $TEMP['#word']['requested_change_email_need_verify'];
																	$update = ", new_email = '$value'";
																} else {
																	$deliver['EQ'] = 0;
																	if($TEMP['#data']['new_email'] == $value){
																		$deliver['EQ'] = 1;
																		$pass = false;
																	}
																}
															}
														} else {
															$pass = true;
															$update = ", email = '$value', new_email = NULL";
														}

													}
												}

												if($pass == true){
													if($dba->query("UPDATE ".T_USER." SET $input = ? {$update} WHERE id = ?", $value, $TEMP['#data']['id'])->returnStatus()){
														$deliver['M'] = $value;
														
														if(empty(trim($value)) && !in_array($input, $no_emptys)){
															$deliver['M'] = $TEMP['#word'][$input];
														} else {
															if($input == 'birthday'){
																$date_of_birth = Functions::DateFormat($value);
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
															} else if($input == 'new_email'){
																if($send_email){
																	Functions::SendChangeEmailToken($TEMP['#data'], $value);
																}
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
									'E' => "*{$TEMP['#word']['email_already_registered']}"
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
					if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE email = ?', $TEMP['#data']['email'])->fetchArray(true) == 0){
						$slug = Functions::RandomKey(12, 16);
						if($dba->query('SELECT COUNT(*) FROM '.T_NEWSLETTER.' WHERE slug = ?', $slug)->fetchArray(true) > 0){
							$slug = Functions::RandomKey(12, 16);
						}
						if($dba->query('INSERT INTO '.T_NEWSLETTER.' (slug, email, created_at) VALUES (?, ?, ?)', $slug, $TEMP['#data']['email'], time())->returnStatus()){
							$deliver = array(
								'S' => 200,
								'M' => "{$TEMP['#word']['newsletter_settings']} ({$TEMP['#word'][$value]})",
								'HT' => "{$TEMP['#word']['configuration_tells_send_news']} <a class='color-blue hover-button animation-ease3s' href='".Functions::Url("{$RUTE['#r_newsletter']}/{$slug}")."' target='_self'>{$TEMP['#word']['see_detailed_settings']}</a>"
							);
						}
					} else {
						if($dba->query('UPDATE '.T_NEWSLETTER.' SET reason = NULL, status = ? WHERE email = ?', $value, $TEMP['#data']['email'])->returnStatus()){
							$slug = $dba->query('SELECT slug FROM '.T_NEWSLETTER.' WHERE email = ?', $TEMP['#data']['email'])->fetchArray(true);
							$deliver['HT'] = $value == 'enabled' ? "{$TEMP['#word']['configuration_tells_send_news']} <a class='color-blue hover-button animation-ease3s' href='".Functions::Url("{$RUTE['#r_newsletter']}/{$slug}")."' target='_self'>{$TEMP['#word']['see_detailed_settings']}</a>" : $TEMP['#word']['configuration_tells_send_news'];
							$deliver['S'] = 200;
							$deliver['M'] = "{$TEMP['#word']['newsletter_settings']} ({$TEMP['#word'][$value]})";
						}
					}
				}
			}
		}
	} else if($one == 'shows'){
		$shows = Functions::Shows($_POST['input'], $_POST['show']);
		if($shows['return']){
			$deliver = $shows['data'];
		}
	} else if($one == 'send-code'){
		$send_change_email_token = Functions::SendChangeEmailToken($TEMP['#data']);

		if($send_change_email_token['return']){
			$deliver = array(
				'S' => $send_change_email_token['status'],
				'M' => $send_change_email_token['message'],
				'TK' => $send_change_email_token['token']
			);
		} else {
			$deliver = array(
				'S' => $send_change_email_token['status'],
				'E' => $send_change_email_token['error']
			);
		}
	} else if($one == 'verify-code'){
		$code = Functions::Filter($_POST['code']);
		if(!empty($code)){
			$user_id = $dba->query("SELECT user_id FROM ".T_TOKEN." WHERE change_email = ?", md5($code))->fetchArray(true);
			if (!empty($user_id)) {
				$change_email = Functions::UserToken('change_email', $user_id);
				if($change_email['return']){
					if($dba->query('UPDATE '.T_USER.' SET email = ?, new_email = NULL, type = "normal" WHERE id = ?', $TEMP['#data']['new_email'], $TEMP['#data']['id'])->returnStatus()){
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
                $upload_avatar = Functions::UploadAvatar($_FILES);
                if ($upload_avatar['return']) {
                    if($TEMP['#data']['avatar'] != 'default-holder'){
                        unlink($TEMP['#data']['ex_avatar_b']);
                        unlink($TEMP['#data']['ex_avatar_s']);
                    }
                    if ($dba->query('UPDATE '.T_USER.' SET avatar = ? WHERE id = ?', $upload_avatar['image'], $TEMP['#data']['id'])->returnStatus()) {
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
                		'DH' => $TEMP['#data']['avatar'] != 'default-holder' ? 1 : 0,
		        		'EM' => $TEMP['#word']['try_again'],
		        		'ED' => $TEMP['#word']['delete']
                	);
                }
            }
        }
    } else if($one == 'reset-avatar'){
    	if($TEMP['#data']['avatar'] != 'default-holder'){
	        if($dba->query('UPDATE '.T_USER.' SET avatar = "default-holder" WHERE id = ?', $TEMP['#data']['id'])->returnStatus()){
	        	unlink($TEMP['#data']['ex_avatar_b']);
                unlink($TEMP['#data']['ex_avatar_s']);
	        	$deliver = array(
	        		'S' => 200,
	        		'AV' => Functions::GetFile('default-holder', 5, 's'),
	        		'EM' => $TEMP['#word']['upload_a_picture'],
	        		'ED' => $TEMP['#word']['delete']
	        	);
	        }
	    }
    } else if($one == 'change-password'){
		$current_password = Functions::Filter($_POST['current_password']);
		$password = Functions::Filter($_POST['password']);
		$re_password = Functions::Filter($_POST['re_password']);

		if(!empty($current_password) && !empty($password) && !empty($re_password) && $password == $re_password){
			if(password_verify($current_password, $TEMP['#data']['password'])){
				$password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
				if ($dba->query('UPDATE '.T_USER.' SET password = ? WHERE id = ?', $password, $TEMP['#data']['id'])->returnStatus()) {
					$deliver['S'] = 200;

					$return = Functions::Url("{$RUTE['#r_settings']}/{$RUTE['#r_reset_password']}");
					$return = urlencode($return);

					$deliver['UR'] = Functions::Url("{$RUTE['#r_login']}?{$RUTE['#p_return']}={$return}");
					if($TEMP['#user']['id'] == $TEMP['#data']['id']){
						setcookie('_LOGIN_TOKEN', null, -1, '/');
						if (isset($_COOKIE['_SAVE_SESSION'])){
						    setcookie('_SAVE_SESSION', null, -1, '/');
						}
		                session_destroy();
					} else {
						if($TEMP['#moderator'] == true){
							$deliver['UR'] = Functions::Url("{$RUTE['#r_settings']}/{$RUTE['#r_reset_password']}?{$RUTE['#p_user_id']}={$TEMP['#data']['id']}");
						}
					}
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
    	$session_id = Functions::Filter($_POST['session_id']);
        if (!empty($session_id)) {
	        $session = $dba->query('SELECT user_id, token FROM '.T_SESSION.' WHERE id = ?', $session_id)->fetchArray();
	        if (!empty($session)) {
	            if (($session['user_id'] == $TEMP['#data']['id'])) {
		            if ($dba->query('DELETE FROM '.T_SESSION.' WHERE id = ?', $session_id)->returnStatus()) {
		                $deliver['S'] = 200;
		                if ((!empty($_SESSION['_LOGIN_TOKEN']) && $_SESSION['_LOGIN_TOKEN'] == $session['token']) || (!empty($_COOKIE['_LOGIN_TOKEN']) && $_COOKIE['_LOGIN_TOKEN'] == $session['token'])) {
		                    setcookie('_LOGIN_TOKEN', null, -1, '/');
							if (isset($_COOKIE['_SAVE_SESSION'])) {
							    setcookie('_SAVE_SESSION', null, -1, '/');
							}
		                    session_destroy();
		                    $deliver['UR'] = Functions::Url($RUTE['#r_login']);
		                }
		            }
		        }
	        }
	    }
    } else if($one == 'table-sessions'){
        $page = Functions::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $user_sessions = $dba->query("SELECT * FROM ".T_SESSION." WHERE user_id = {$TEMP['#data']['id']} ORDER BY id DESC LIMIT ? OFFSET ?", 10, $page)->fetchAll();
            if (!empty($user_sessions)) {
                foreach ($user_sessions as $value) {
                    $TEMP['!id'] = $value['id'];
                    $session = Functions::GetSessions($value);
                    $TEMP['!ip'] = $session['ip'];
                    $TEMP['!browser'] = $session['browser'];
                    $TEMP['!platform'] = $session['platform'];
                    $TEMP['!created_at'] = Functions::DateFormat($value['created_at']);
                    $html .= Functions::Build("settings/logins/includes/sessions");
                }
                Functions::DestroyBuild();
            }
            $deliver = array(
            	'S' => 200,
            	'HT' => $html
            );
        }
    } else if ($one == 'unlock-user'){
    	$profile_id = Functions::Filter($_POST['profile_id']);
        if (!empty($profile_id)) {
	        if ($dba->query('SELECT COUNT(*) FROM '.T_USER.' WHERE id = ? AND status = "active"', $profile_id)->fetchArray(true) > 0) {
		        if ($dba->query('DELETE FROM '.T_BLOCK.' WHERE user_id = ? AND profile_id = ?', $TEMP['#data']['id'], $profile_id)->returnStatus()) {
		            $deliver['S'] = 200;
		        }
	        }
	    }
    } else if($one == 'table-blocked-users'){
        $page = Functions::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $blocked_users = $dba->query("SELECT * FROM ".T_BLOCK." b WHERE user_id = {$TEMP['#data']['id']} AND (SELECT status FROM ".T_USER." WHERE id = b.profile_id) = 'active' ORDER BY id DESC LIMIT ? OFFSET ?", 10, $page)->fetchAll();
            if (!empty($blocked_users)) {
                foreach ($blocked_users as $blocked) {
                	$user = Functions::Data($blocked['profile_id'], array(
			            'username',
			            'name',
			            'surname'
			        ));

        			$TEMP['!id'] = $blocked['profile_id'];
			        $TEMP['!name'] = $user['username'];
			        $TEMP['!created_at'] = Functions::DateFormat($blocked['created_at']);

			        $html .= Functions::Build("settings/blocked-users/includes/users");
                }
                Functions::DestroyBuild();
            }
            $deliver = array(
            	'S' => 200,
            	'HT' => $html
            );
        }
    } else if($one == 'delete-account'){
    	$delete_command = Functions::Filter($_POST['delete_command']);

    	if(Functions::IsOwner($TEMP['#data']['id']) || $TEMP['#moderator'] == true){
	    	if($delete_command === $TEMP['#word']['DELETE_COMMAND']){
	    		if(Functions::DeleteUser($TEMP['#data']['id'])){
	    			$deliver['S'] = 200;
	    			if($TEMP['#user']['id'] == $TEMP['#data']['id']){
			            setcookie('_LOGIN_TOKEN', null, -1, '/');
						if (isset($_COOKIE['_SAVE_SESSION'])) {
							setcookie('_SAVE_SESSION', null, -1, '/');
						}
			            session_destroy();
			            $deliver['UR'] = Functions::Url($RUTE['#r_login']);
			        } else {
			        	if($TEMP['#moderator'] == true){
							$deliver['UR'] = Functions::Url($RUTE['#r_settings']);
						}
			        }
	    		}
	    	} else {
	    		$deliver = array(
	    			'S' => 400,
	    			'E' => $TEMP['#word']['seems_that_typed_word_not_correct']
	    		);
	    	}
	    }

    }
}