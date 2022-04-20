<?php
if($TEMP['#loggedin'] == true){
	if($one == 'account'){
		$input = Specific::Filter($_POST['input']);
		$value = Specific::Filter($_POST['value']);

		if(in_array($input, array('username', 'new_email', 'name', 'surname', 'about', 'birthday', 'gender', 'facebook', 'twitter', 'instagram'))){
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
				if(($input == 'name' && strlen($value) <= 55) || $input != 'name'){
					if(($input == 'surname' && strlen($value) <= 55) || $input != 'surname'){
						if(($input == 'username' && preg_match('/^[a-zA-Z0-9]+$/', $value)) || $input != 'username'){
							if(($input == 'new_email' && filter_var($value, FILTER_VALIDATE_EMAIL)) || $input != 'new_email'){
								if((in_array($input, $red_social) && preg_match("#^(https?://www\.{$input}.com/(.+?)|https?://{$input}.com/(.+?)|www\.{$input}.com/(.+?))#i", $value) == true || empty($value)) || !in_array($input, $red_social)){
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
									if($error == false){
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
														$deliver['M'] = Specific::DateFormat($value);
													} else if($input == 'gender'){
														$deliver['M'] = $TEMP['#word'][$value];
													}
												}
												$deliver['S'] = 200;
											}
										} else {
											$deliver['S'] = 200;
										}
									}
								} else {
									$deliver = array(
										'S' => 400,
										'E' => "*{$TEMP['#word']['enter_a_valid_url']}"
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
			} else {
				$deliver = array(
					'S' => 400,
					'E' => "*{$TEMP['#word']['this_field_is_empty']}"
				);
			}
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
    }
}