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

if(in_array($_POST['subject'], array('content', 'technical', 'pattern', 'ask', 'suggestions', 'other'))){
	$emptys = array();
	$errors = array();
	$name = Functions::Filter($_POST['name']);
	$subject = Functions::Filter($_POST['subject']);
	$text = Functions::Filter($_POST['text']);
	$email = Functions::Filter($_POST['email']);
	$query = Functions::Filter($_POST['query']);
	if (empty($name)){
        $emptys[] = 'name';
    }
    if(empty($email)){
        $emptys[] = 'email';
    }
    if (empty($query)) {
        $emptys[] = 'query';
    }
    
    if($subject == 'content'){
    	$subject_ = $TEMP['#word']['contents'];
    } else if($subject == 'technical'){
    	$subject_ = $TEMP['#word']['technical_problems'];
    } else if($subject == 'pattern'){
    	$subject_ = $TEMP['#word']['pattern_with_us'];
    } else if($subject == 'ask'){
    	$subject_ = $TEMP['#word']['ask_for_information'];
    } else if($subject == 'suggestions'){
    	$subject_ = $TEMP['#word']['suggestions_requests'];
    } else if($subject == 'other'){
    	if(!empty($text)){
    		$subject_ = $text;
    	} else {
    		$subject_ = $TEMP['#word']['other'];
    	}
    }

    if(empty($emptys)){
    	if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    		$errors[] = 'email';
    	}
    	if(empty($errors)){
			$recaptcha_success = true;
			if ($TEMP['#settings']['recaptcha'] == 'on'){
				$recaptcha = Functions::CheckRecaptcha($_POST['recaptcha']);
				if (!isset($_POST['recaptcha']) || empty($_POST['recaptcha']) || $recaptcha["action"] != 'contact' || $recaptcha["success"] == false || $recaptcha["score"] < 0.5){
					$recaptcha_success = false;
				}
			}

			if($recaptcha_success){
		    	$send_email_data = array(
				    'from_email' => $TEMP['#settings']['smtp_username'],
				    'from_name' => $name,
		        	'reply_to' => $email,
				    'to_email' => $TEMP['#settings']['contact_email'],
				    'to_name' => $TEMP['#settings']['title'],
				    'subject' => $subject_,
				    'charSet' => 'UTF-8',
				    'text_body' => $query
				);
				if(Functions::SendEmail($send_email_data)){
					$deliver = array(
						'S' => 200,
						'M' => $TEMP['#word']['mail_sent_successfully']
					);
				} else {
					$deliver = array(
						'S' => 400,
						'E' => $TEMP['#word']['could_not_send_message_error']
					);
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
	    		'EL' => $errors,
	    		'TX' => $TEMP['#word']['enter_a_valid_email']
	    	);
		}
    } else {
    	$deliver = array(
    		'S' => 400,
    		'EL' => $emptys,
    		'TX' => $TEMP['#word']['this_field_is_empty']
    	);
    }
}
?>