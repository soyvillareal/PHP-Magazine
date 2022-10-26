<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Specific {

	public static function GetFile($file, $type = 1, $size = 's'){
	    global $TEMP;
	    if (empty($file)) {
	        return '';
	    }
	    $prefix = '';
	    $suffix = '';
	    if($type == 2){
	        $prefix = "themes/{$TEMP['#settings']['theme']}/";
	    } else {
	    	if($type == 3) {
	    		$prefix = 'uploads/entries/';
	    	} else if($type == 5){
	    		$prefix = 'themes/'.$TEMP['#settings']['theme'].'/images/users/';
		    	if(!empty($size)){
		    		$suffix = "-$size.jpeg";
		    	}
	    	} else {
	    		$folder = $type == 4 ? 'users' : 'posts';
	    		$prefix = "uploads/$folder/";
		    	if(!empty($size)){
		    		$suffix = "-$size.jpeg";
		    	}
	    	}
	    }
	    return self::Url($prefix.$file.$suffix);
	}

	public static function Admin() {
	    global $TEMP;
	    return $TEMP['#loggedin'] === false ? false : $TEMP['#user']['role'] == 'admin' ? true : false;
	}

	public static function Moderator() {
	    global $TEMP;
	    return $TEMP['#loggedin'] === false ? false : $TEMP['#user']['role'] == 'moderator' || $TEMP['#user']['role'] == 'admin' ? true : false;
	}

	public static function Publisher() {
	    global $TEMP;
	    return $TEMP['#loggedin'] === false ? false : $TEMP['#user']['role'] == 'publisher' || $TEMP['#user']['role'] == 'moderator' || $TEMP['#user']['role'] == 'admin' ? true : false;
	}

	public static function Viewer() {
	    global $TEMP;
	    return $TEMP['#loggedin'] === false ? false : $TEMP['#user']['role'] == 'viewer' ? true : false;
	}

	function ResizeImage($max_width, $max_height, $source_file, $dst_dir, $quality = 80) {
	    $imgsize = @getimagesize($source_file);
	    $width   = $imgsize[0];
	    $height  = $imgsize[1];
	    $mime    = $imgsize['mime'];
	    switch ($mime) {
	        case 'image/gif':
	            $image_create = "imagecreatefromgif";
	            $image        = "imagegif";
	            break;
	        case 'image/png':
	            $image_create = "imagecreatefrompng";
	            $image        = "imagepng";
	            break;
	        case 'image/jpeg':
	            $image_create = "imagecreatefromjpeg";
	            $image        = "imagejpeg";
	            break;
	        default:
	            return false;
	            break;
	    }
	    $dst_img    = @imagecreatetruecolor($max_width, $max_height);
	    $src_img    = $image_create($source_file);
	    $width_new  = $height * $max_width / $max_height;
	    $height_new = $width * $max_height / $max_width;
	    if ($width_new > $width) {
	        $h_point = (($height - $height_new) / 2);
	        @imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
	    } else {
	        $w_point = (($width - $width_new) / 2);
	        @imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
	    }
	    @imagejpeg($dst_img, $dst_dir, $quality);
	    if ($dst_img)
	        @imagedestroy($dst_img);
	    if ($src_img)
	        @imagedestroy($src_img);
	}

	public static function CreateDirImage($folder){
		$folder_first = "uploads/$folder/" . date('Y') . '-' . date('m');
	    if (!file_exists($folder_first)) {
		    mkdir($folder_first, 0777, true);
		}
		$dates = date('Y') . '-' . date('m') . '/' . date('m');
		$folder_last = "uploads/$folder/$dates";
	   	if (!file_exists($folder_last)) {
		    mkdir($folder_last, 0777, true);
		}
	    return array(
	    	'full' => $folder_last,
	    	'dates' => $dates
	    );
	}


	public static function UploadThumbnail($data) {
	    if(strpos(strtolower($data['media']), '.gif')){
	    	return array(
		    	'return' => false
		    );
	    }
	    $dir_image = self::CreateDirImage($data['folder']);
	    $getImage = self::getContentUrl($data['media']);
	    if($data['folder'] == 'posts'){
	    	$image = sha1(rand(111,666).self::RandomKey()).'_'.time();
	    	$file = "{$dir_image['full']}/{$image}";
		    $filename_b = "{$file}-b.jpeg";
		    $filename_s = "{$file}-s.jpeg";
			
	    	if (!empty($getImage)){
		        $importImage_b = file_put_contents($filename_b, $getImage);
		        $importImage_s = file_put_contents($filename_s, $getImage);
		        if ($importImage_b) {
		            self::ResizeImage(780, 440, $filename_b, $filename_b, 100);
		        }
		        if ($importImage_s) {
		            self::ResizeImage(400, 266, $filename_s, $filename_s, 100);
		        }
		        if (file_exists($filename_b) && file_exists($filename_s)){
	    			$url_dates = "{$dir_image['dates']}/{$image}";
			        return array(
				    	'return' => true,
			    		'image' => $url_dates,
			    		'image_ext' => "{$url_dates}.jpeg"
				    );
		    	}
		    }
	    } else {
	    	$image = "{$data['post_id']}-{$data['eorder']}-".md5(time().self::RandomKey());
	    	$filename = "{$dir_image['full']}/{$image}.jpeg";
		    if(file_put_contents($filename, $getImage)) {
	    		$url_dates = "{$dir_image['dates']}/{$image}";
			    return array(
			    	'return' => true,
			    	'image' => $url_dates,
			    	'image_ext' => "{$url_dates}.jpeg"
			    );
		    }
	    }
	    return array(
	    	'return' => false
	    );
	}

	public static function UploadImage($data = array()){
		global $TEMP;
		
	    $dir_image = self::CreateDirImage($data['folder']);
	    if (empty($data)) {
	        return false;
	    }
	    if (!in_array(pathinfo($data['name'], PATHINFO_EXTENSION), array('jpeg','jpg','png')) || !in_array($data['type'], array('image/jpeg', 'image/png')) || $data['size'] > $TEMP['#settings']['file_size_limit']) {
	        return array(
	        	'return' => false
	        );
	    }
	    if($data['folder'] == 'posts'){
	    	$image = sha1(rand(111,666).self::RandomKey()).'_'.time();
	    	$file = "{$dir_image['full']}/{$image}";
		    $filename_b = "{$file}-b.jpeg";
		    $filename_s = "{$file}-s.jpeg";
		    if (move_uploaded_file($data['tmp_name'], $filename_b)) {
	    		$url_dates = "{$dir_image['dates']}/{$image}";
	            @self::ResizeImage(780, 440, $filename_b, $filename_b, 70);
			    if (copy($filename_b, $filename_s)) {
		            @self::ResizeImage(400, 266, $filename_s, $filename_s, 70);
			    }
			    return array(
			    	'return' => true,
			    	'image' => $url_dates,
			    	'image_ext' => "{$url_dates}.jpeg"
			    );
		    }

	    } else {
	    	$image = "{$data['post_id']}-{$data['eorder']}-".md5(time().self::RandomKey());
	    	$filename = "{$dir_image['full']}/{$image}.jpeg";
		    if (move_uploaded_file($data['tmp_name'], $filename)) {
	    		$url_dates = "{$dir_image['dates']}/{$image}";
			    return array(
			    	'return' => true,
			    	'image' => $url_dates,
			    	'image_ext' => "{$url_dates}.jpeg"
			    );
		    }
	    }
	    return array('return' => false);
	}

	public static function SizeFormat($bytes, $precision = 2) {
	    $unit = ["B", "KB", "MB", "GB"];
	    $exp = floor(log($bytes, 1024)) | 0;
	    return round($bytes / (pow(1024, $exp)), $precision)." {$unit[$exp]}";
	}

	public static function Notifies(){
		global $dba, $TEMP;

		$data = array(
			'return' => false
		);

		$notifications = $dba->query('SELECT COUNT(*) FROM '.T_NOTIFICATION.' WHERE user_id = ? AND seen = 0', $TEMP['#user']['id'])->fetchArray(true);

		$messages = $dba->query('SELECT COUNT(*) FROM '.T_MESSAGE.' WHERE profile_id = ? AND seen = 0', $TEMP['#user']['id'])->fetchArray(true);

		$notifies = $notifications+$messages;

		if($notifies > 0){
			$data = array(
				'return' => true,
				'count_text' => $notifies <= 9 ? $notifies : "9+",
				'count_notifications' => $notifications,
				'count_messages' => $messages
			);
		}

		return $data;
	}

	public static function BlockedUsers($implode = true) {
	    global $dba, $TEMP;

	    $data = array(0);
	    if($implode){
	    	$data = 0;
	    }
	    if ($TEMP['#loggedin'] === true && $TEMP['#settings']['blocked_users'] == 'on') {
		    $blocked_me = $dba->query('SELECT user_id FROM '.T_BLOCK.' WHERE user_id <> ? AND profile_id = ?', $TEMP['#user']['id'], $TEMP['#user']['id'])->fetchAll(FALSE);

		    $blocked_to = $dba->query('SELECT profile_id FROM '.T_BLOCK.' WHERE user_id = ? AND profile_id <> ?', $TEMP['#user']['id'], $TEMP['#user']['id'])->fetchAll(FALSE);

		    if (!empty($blocked_to) || !empty($blocked_me)) {
		        $data = array_merge($blocked_to, $blocked_me);
		        if($implode) {
		        	$data = implode(',', $data);
		        }
		    }
		}
	    return $data;
	}

	public static function UploadMessagefi($data = array()){
		global $TEMP;

		$dir_file = self::CreateDirImage('messages');
	    if (empty($data)) {
	        return false;
	    }
	    
	    // !in_array($ext, array('jpeg','jpg','png', 'csv', 'xls', 'xlsx', 'pdf', 'doc', 'docx', 'ppt', 'pttx', 'txt', 'xls', 'xlsx')) || !in_array($data['type'], array('image/jpeg', 'image/png', 'application/pdf', 'application/csv', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/msword', 'application/vnd.ms-powerpoint', 'application/vnd.ms-exce')) || 

	    if ($data['size'] > $TEMP['#settings']['file_size_limit']) {
	        return array(
	        	'return' => false
	        );
	    }

	    $ext = pathinfo($data['name'], PATHINFO_EXTENSION);
	    if(!empty($ext)){
	    	$ext = '.'.$ext;
	    }

	   	$filename = "{$data['message_id']}-".md5(time().self::RandomKey()).$ext;
		if (move_uploaded_file($data['tmp_name'], "{$dir_file['full']}/{$filename}")) {
			return array(
			    'return' => true,
			    'file' => "{$dir_file['dates']}/{$filename}"
			);
		}

	}

	public static function UploadAvatar($data = array()){
	    global $TEMP;

	   	if (!file_exists('uploads/users')) {
		    mkdir('uploads/users/', 0777, true);
		}
	    if (empty($data)) {
	        return false;
	    }
	    if (!in_array(pathinfo($data['avatar']['name'], PATHINFO_EXTENSION), array('jpeg','jpg','png')) || !in_array($data['avatar']['type'], array('image/jpeg', 'image/png'))) {
	        return array('return' => false);
	    }
	    $image = "{$TEMP['#user']['username']}-".sha1(time().self::RandomKey());
	    $file = 'uploads/users/' . $image;
	    $filename_b = "{$file}-b.jpeg";
	    $filename_s = "{$file}-s.jpeg";
	    if (move_uploaded_file($data['avatar']['tmp_name'], $filename_b)) {
            @self::ResizeImage(200, 200, $filename_b, $filename_b, 70);
		    if (copy($filename_b, $filename_s)) {
	            @self::ResizeImage(90, 90, $filename_s, $filename_s, 70);
		    }
		    return array(
		    	'return' => true,
		    	'image' => $image,
		    	'avatar_s' => self::Url($filename_s)
		    );
	    }
	    return array('return' => false);
	}

	public static function OAuthImage($media, $username) {
	    $image = "$username-" . sha1(time().self::RandomKey());
	    $file = 'uploads/users/';
	    $file_b = "$file$image-b.jpeg";
	    $file_s = "$file$image-s.jpeg";
	    $getImage = self::getContentUrl($media);
	    if (!empty($getImage)) {
	        $importImage_b = file_put_contents($file_b, $getImage);
	        $importImage_s = file_put_contents($file_s, $getImage);
	        if ($importImage_b) {
	            self::ResizeImage(200, 200, $file_b, $file_b, 100);
	        }
	        if ($importImage_s) {
	            self::ResizeImage(90, 90, $file_s, $file_s, 100);
	        }
	    }
	    if (file_exists($file_b) && file_exists($file_s)){
	        return $image;
    	} else {
	    	return 'default-holder';
	    }
	}

	public static function getContentUrl($url = '') {
	    if (empty($url)) {
	        return false;
	    }
	    $curl = curl_init($url);

	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	    // Start getImage
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	    	'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:45.0) Gecko/20100101 Firefox/45.0',
	        'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
	        'Accept-Encoding: gzip, deflate'
	    ));
	    // End getImage

	    //execute the session
	    $curl_response = curl_exec($curl);

	    // Start getImage
	    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    // End getImage

	    //finish off the session
	    curl_close($curl);

	    // Start getImage
	    if ($code == 200) {
	   		return $curl_response;
	   	} else {
	    	return false;
	    }
	    // End getImage

	}

	//function sanitize_title_with_dashes taken from wordpress
	public static function CreateSlug($str, $char = "-", $tf = "lowercase", $max = 120){
	    $str = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $str); // transliterate
	    $str = str_replace("'", "", $str); // remove “'” generated by iconv
	    $str = substr($str, 0, $max);
	    $str = preg_replace("~[^a-z0-9]+~ui", $char, $str); // replace unwanted by single “-”
	    $str = trim($str, $char); // trim “-”

	    if($tf == "lowercase"){
	    	$str = mb_strtolower($str, "UTF-8"); // lowercase
	    } else if($tf == "uppercase"){
	    	$str = mb_strtoupper($str, "UTF-8");
	    }
	    return $str;
	}

	public static function Settings() {
	    global $dba;
	    $data = array();
	    $settings = $dba->query('SELECT * FROM setting')->fetchAll();
	    foreach ($settings as $value) {
	        $data[$value['name']] = $value['value'];
	    }
	    return $data;
	}

	public static function Data($data, $type = 1) {
	    global $dba, $TEMP;

	    if(is_numeric($type)){
		    if($type == 1){
		        $user = $dba->query('SELECT * FROM '.T_USER.' WHERE id = ?', $data)->fetchArray();
		    } else if($type == 3){
		    	$user = $data;
		    } else {
		        $token = !empty($_SESSION['_LOGIN_TOKEN']) ? $_SESSION['_LOGIN_TOKEN'] : $_COOKIE['_LOGIN_TOKEN'];
				
		        $user = $dba->query('SELECT * FROM '.T_USER.' WHERE (SELECT user_id FROM '.T_SESSION.' WHERE token = ?) = id', $token)->fetchArray();
		    }

		    $user['user'] = $user['username'];
		   	$user['slug'] = self::ProfileUrl($user['username']);
			if(!empty($user['notifications'])){
				$user['notifications'] = json_decode($user['notifications'], true);
			} else {
				$user['notifications'] = array(
					'followers',
					'post',
					'followed',
					'collab',
					'react',
					'pcomment',
					'preply',
					'ucomment',
					'ureply'
				);
			}
			if(!empty($user['shows'])){
				$user['shows'] = json_decode($user['shows'], true);
			} else {
				$user['shows'] = array(
					'birthday' => 'on',
					'gender' => 'on',
					'contact_email' => 'on',
					'followers' => 'on',
					'messages' => 'on'
				);
			}
	    } else {
	    	$user = $dba->query('SELECT '.implode(',', $type).' FROM '.T_USER.' WHERE id = ?', $data)->fetchArray();
	    }

	    if (empty($user)) {
	        return false;
	    }
	    if(!empty($user['name']) && !empty($user['surname'])){
		    $user['username'] = "{$user['name']} {$user['surname']}";
	   	} else if(!empty($user['username'])){
	    	$user['username'] = $user['username'];
	   	}
	    if(!empty($user['birthday'])){
		    $birthday = explode("-", date('d-n-Y', $user['birthday']));

		    $user['birth_day'] = $birthday[0];
		    $user['birthday_month'] = $birthday[1];
		    $user['birthday_year'] = $birthday[2];

		    $user['birthday_format'] = self::DateFormat($user['birthday']);
		}
		if(!empty($user['avatar'])){
		    $rute = 4;
		    if($user['avatar'] == 'default-holder'){
		    	$rute = 5;
		    }
		    $user['ex_avatar_b'] = "uploads/users/{$user['avatar']}-b.jpeg";
		    $user['ex_avatar_s'] = "uploads/users/{$user['avatar']}-s.jpeg";
		    $user['avatar_b'] = self::GetFile($user['avatar'], $rute, 'b');
		   	$user['avatar_s'] = self::GetFile($user['avatar'], $rute, 's');
		}
		if(!empty($user['gender'])){
			$user['gender_txt'] = $TEMP['#word'][$user['gender']];
		}
	    if(!empty($user['created_at'])){
		    $user['created_fat'] = self::DateFormat($user['created_at']);
		    $user['created_sat'] = self::DateString($user['created_at']);
		}
	    return count($user) > 1 ? $user : array_values($user)[0];
	}

	public static function SetNotify($data = array()){
		global $dba, $TEMP;

		$typet = $data['type'];
		if(in_array($data['type'], array('preact', 'creact', 'rreact'))){
			$typet = "react";
		}
		/*
		$time = $dba->query('SELECT MIN(created_at) FROM '.T_NOTIFICATION.' WHERE user_id = ? AND type = ?', $data['user_id'], $type)->fetchArray(true);

		if(in_array($data['type'], array('preact', 'creact', 'rreact')) && strtotime('+3 hour', time()) > $time){
			return false;
		}
		*/


		if(!self::IsOwner($data['user_id'])){
			$user = self::Data($data['user_id']);
			if(in_array($typet, $user['notifications'])){
				$type = "n_{$data['type']}";
				if($dba->query('SELECT COUNT(*) FROM '.T_NOTIFICATION.' WHERE user_id = ? AND notified_id = ? AND type = ?', $data['user_id'], $data['notified_id'], $type)->fetchArray(true) == 0){
					if($dba->query('INSERT INTO '.T_NOTIFICATION.' (user_id, notified_id, type, created_at) VALUES (?, ?, ?, ?)', $data['user_id'], $data['notified_id'], $type, time())->returnStatus()){
						return true;
					}
				}
			}
		}
		return false;
	}

	public static function TextFilter($text, $links = true){
		global $TEMP;

		if(!empty($text)){
			if(!empty($TEMP['#settings']['censored_words'])){
		        $censored_words = explode(',', $TEMP['#settings']['censored_words']);
		        foreach ($censored_words as $word) {
		            $word = trim($word);
		            $search[] = "/{$word}/";
		            $replace[] = preg_replace('/(.+?)/i', '*', $word);
		        }
		        $text = preg_replace($search, $replace, $text);
		    }
		    
		    if(!empty($TEMP['#settings']['hidden_domains'])){
		        $hidden_domains = explode(',', $TEMP['#settings']['hidden_domains']);
		        foreach ($hidden_domains as $domain) {
		            $domain = trim($domain);
		            $search[] = "/(?:(?:[\S]*)({$domain})(?:[\S])*)/i";
		        }
		        $text = preg_replace($search, "[{$TEMP['#word']['hidden_link']}]", $text);
		    }

		    if($links){
			    $text = preg_replace($TEMP['#url_regex'], '<a class="color-blue hover-button animation-ease3s" href="//$3$4" target="_blank">$3$4</a>', $text);
		    }
		}

	    return $text;
	}

	public static function CommentFilter($text, $mention_uid){
		global $dba, $TEMP;

		$text = self::TextFilter($text);

		$username_exists = preg_match_all('/@([a-zA-Z0-9]+)/i', $text, $username);
		if($username_exists > 0){
			for ($i=0; $i < $username_exists; $i++) {
				$user = $dba->query('SELECT id, username, COUNT(*) as count FROM '.T_USER.' WHERE username = ? AND status = "active"', $username[1][$i])->fetchArray();
				if($user['count'] > 0){
					if($user['id'] != $mention_uid){
						return preg_replace("/@({$username[1][$i]}+)/i", '<a class="color-blue hover-button" href="'.self::ProfileUrl($user['username']).'" target="_blank">@'.$user['username'].'</a>', $text);
					}
				}
			}
		}
		return $text;
	}

	public static function FeaturedComment($data_id, $type = 'comment'){
		global $dba;

		if($type == 'comment'){
			$comment = $dba->query('SELECT * FROM '.T_COMMENTS.' WHERE id = ?', $data_id)->fetchArray();
			$comment_id = $comment['id'];
			
			if(!empty($comment)){
				$comment = self::CommentMaket($comment, $order, 'featured-comment');	
			}
		} else {
			$comment = $dba->query('SELECT * FROM '.T_COMMENTS.' WHERE (SELECT comment_id FROM '.T_REPLY.' WHERE id = ?) = id', $data_id)->fetchArray();
			$comment_id = $comment['id'];

			if(!empty($comment)){
				$comment = self::CommentMaket($comment, $order, 'featured-reply');	
			}
		}

		if($comment['return']){
			return array(
				'return' => true,
				'id' => $comment_id,
				'html' => $comment['html']
			);
		}

		return array(
			'return' => false
		);
	}

	public static function Comments($post_id, $order = 'recent', $comment_ids = array()){
		global $dba, $TEMP;

		$query = '';
		if(!empty($comment_ids)){
			$query = ' AND id NOT IN ('.implode(',', $comment_ids).')';
		}
		$order_cby = 'DESC';
		if($order == 'oldest'){
			$order_cby = 'ASC';
		}
		$sql = 'SELECT * FROM '.T_COMMENTS.' WHERE post_id = ?'.$query.' ORDER BY pinned DESC, created_at '.$order_cby.' LIMIT 5';
		if($order == 'featured'){
			if(!empty($comment_ids)){
				$query = ' AND a.id NOT IN ('.implode(',', $comment_ids).')';
			}
			$sql = 'SELECT a.*, COUNT(c.id) as count FROM '.T_COMMENTS.' a LEFT JOIN '.T_REACTION.' c ON a.id = c.reacted_id AND c.type = "like" AND c.place = "comment" WHERE a.post_id = ?'.$query.' GROUP BY a.id ORDER BY a.pinned DESC, count DESC, a.created_at DESC LIMIT 5';
		} else if($order == 'answered'){
			if(!empty($comment_ids)){
				$query = ' AND a.id NOT IN ('.implode(',', $comment_ids).')';
			}
			$sql = 'SELECT a.*, COUNT(c.id) as count FROM '.T_COMMENTS.' a LEFT JOIN '.T_REPLY.' c ON a.id = c.comment_id WHERE a.post_id = ?'.$query.' GROUP BY a.id ORDER BY a.pinned DESC, count DESC, a.created_at DESC LIMIT 5';
		}
		$comments = $dba->query($sql, $post_id)->fetchAll();
		if(!empty($comments)){
			$html = '';
			foreach ($comments as $comment) {
				$comment = self::CommentMaket($comment, $order);
				$html .= $comment['html'];
			}
			self::DestroyMaket();

			return array(
				'return' => true,
				'html' => $html
			);
		}
		return array(
			'return' => false
		);
	}

	public static function CommentMaket($comment = array(), $order = 'recent', $type = 'normal'){
		global $dba, $TEMP, $RUTE;

		if(!empty($comment)){

			$TEMP['cusername'] = $TEMP['#word']['user_without_login'];
			$TEMP['avatar_cs'] = self::Url('/themes/default/images/users/default-holder-s.jpeg');
			if($TEMP['#loggedin'] == true){
				$TEMP['cusername'] = $TEMP['#user']['username'];
				$TEMP['avatar_cs'] = $TEMP['#user']['avatar_s'];
			}
			
			$post = $dba->query('SELECT user_id, slug FROM '.T_POST.' WHERE id = ?', $comment['post_id'])->fetchArray();
			$TEMP['!url_comment'] = self::Url("{$post['slug']}?{$RUTE['#p_comment_id']}={$comment['id']}");
			$TEMP['!comment_id'] = $comment['id'];
			$TEMP['!comment_owner'] = self::IsOwner($comment['user_id']);
			$TEMP['!comment_powner'] = self::IsOwner($post['user_id']);

			$reply_ids = array();
			$TEMP['!comment_type'] = $type;
			if($type == 'featured-reply'){
				$reply = $dba->query('SELECT * FROM '.T_REPLY.' WHERE id = ?', $TEMP['#featured_rid'])->fetchArray();
				$featured_reply = self::ReplyMaket($reply, $comment['id'], 'featured-reply');

				if($featured_reply['return']){
					$TEMP['featured_reply'] = $featured_reply['html'];
					$reply_ids[] = $TEMP['#featured_rid'];
				}
			}

			$TEMP['replies'] = '';
			$TEMP['!reply_owner'] = false;
			$TEMP['!count_replies'] = $dba->query('SELECT COUNT(*) FROM '.T_REPLY.' WHERE comment_id = ?', $comment['id'])->fetchArray(true);
			$replies = self::Replies($comment['id'], $order, $reply_ids);
			if($replies['return']){
				$TEMP['replies'] = $replies['html'];
			}

			$user = self::Data($comment['user_id'], array('username', 'avatar'));

			$TEMP['!post_id'] = $comment['post_id'];
			$TEMP['!text'] = self::CommentFilter($comment['text'], $comment['user_id']);

			$TEMP['!author_name'] = $user['username'];
			$TEMP['!author_url'] = self::ProfileUrl($TEMP['!author_name']);
			$TEMP['!author_avatar'] = $user['avatar_s'];

			$TEMP['!likes_active'] = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "comment"', $TEMP['#user']['id'], $comment['id'])->fetchArray(true);
			$TEMP['!dislikes_active'] = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "comment"', $TEMP['#user']['id'], $comment['id'])->fetchArray(true);


			$likes = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE reacted_id = ? AND type = "like" AND place = "comment"', $comment['id'])->fetchArray(true);
			$dislikes = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE reacted_id = ? AND type = "dislike" AND place = "comment"', $comment['id'])->fetchArray(true);
			$TEMP['!likes'] = self::NumberShorten($likes);
			$TEMP['!dislikes'] = self::NumberShorten($dislikes);

			$TEMP['!created_date'] = date('c', $comment['created_at']);
			$TEMP['!created_at'] = self::DateString($comment['created_at']);

			$maket = 'comment';
			if($comment['pinned'] == 1){
				$maket = 'pinned-comment';
			}
			return array(
				'return' => true,
				'html' => self::Maket("post/includes/{$maket}")
			);
		}

		return array(
			'return' => false
		);
	}

	public static function ReplyMaket($reply = array(), $comment_id, $type = 'normal'){
		global $dba, $TEMP, $RUTE;

		if(!empty($reply)){
			$TEMP['!reply_type'] = $type;
			$user = self::Data($reply['user_id'], array('username', 'avatar'));

			$post = $dba->query('SELECT user_id, slug FROM '.T_POST.' WHERE (SELECT post_id FROM '.T_COMMENTS.' WHERE id = ?) = id', $reply['comment_id'])->fetchArray();
			$TEMP['!url_reply'] = self::Url("{$post['slug']}?{$RUTE['#p_reply_id']}={$reply['id']}");
			$TEMP['!reply_id'] = $reply['id'];
			$TEMP['!reply_owner'] = self::IsOwner($reply['user_id']);

			$TEMP['cusername'] = $TEMP['#word']['user_without_login'];
			$TEMP['avatar_cs'] = self::Url('/themes/default/images/users/default-holder-s.jpeg');
			if($TEMP['#loggedin'] == true){
				$TEMP['cusername'] = $TEMP['#user']['username'];
				$TEMP['avatar_cs'] = $TEMP['#user']['avatar_s'];
			}

			$TEMP['!reply_powner'] = self::IsOwner($post['user_id']);
							
			$TEMP['!text'] = self::CommentFilter($reply['text'], $reply['user_id']);
			$TEMP['!author_name'] = $user['username'];
			$TEMP['!author_url'] = self::ProfileUrl($TEMP['!author_name']);
			$TEMP['!author_avatar'] = $user['avatar_s'];

			$TEMP['!likes_active'] = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "reply"', $TEMP['#user']['id'], $reply['id'])->fetchArray(true);
			$TEMP['!dislikes_active'] = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "reply"', $TEMP['#user']['id'], $reply['id'])->fetchArray(true);

			$likes = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE reacted_id = ? AND type = "like" AND place = "reply"', $reply['id'])->fetchArray(true);
			$dislikes = $dba->query('SELECT COUNT(*) FROM '.T_REACTION.' WHERE reacted_id = ? AND type = "dislike" AND place = "reply"', $reply['id'])->fetchArray(true);
			$TEMP['!likes'] = self::NumberShorten($likes);
			$TEMP['!dislikes'] = self::NumberShorten($dislikes);

			$TEMP['!created_date'] = date('c', $reply['created_at']);
			$TEMP['!created_at'] = self::DateString($reply['created_at']);

			return array(
				'return' => true,
				'html' => self::Maket('post/includes/reply')
			);
		}

		return array(
			'return' => false
		);

	}

	public static function Replies($comment_id, $order = 'recent', $reply_ids = array()){
		global $dba, $TEMP;

		$query = '';
		if(!empty($reply_ids)){
			$query = ' AND id NOT IN ('.implode(',', $reply_ids).')';
		}
		$sql = 'SELECT * FROM '.T_REPLY.' WHERE comment_id = ?'.$query.' LIMIT 5';
		if($order == 'featured'){
			if(!empty($reply_ids)){
				$query = ' AND a.id NOT IN ('.implode(',', $reply_ids).')';
			}
			$sql = 'SELECT a.*, COUNT(c.id) as count FROM '.T_REPLY.' a LEFT JOIN '.T_REACTION.' c ON a.id = c.reacted_id AND c.type = "like" AND c.place = "reply" WHERE a.comment_id = ?'.$query.' GROUP BY a.id ORDER BY count DESC, a.id ASC LIMIT 5';
		}

		$replies = $dba->query($sql, $comment_id)->fetchAll();
		if(!empty($replies)){
			$html = '';
			foreach ($replies as $reply) {
				$reply = self::ReplyMaket($reply, $comment_id);
				$html .= $reply['html'];
			}

			return array(
				'return' => true,
				'html' => $html
			);
		}

		return array(
			'return' => false
		);
	}

	public static function ToMention($data = array(), $type = 'comment'){
		global $dba;

		$username_exists = preg_match_all('/@([a-zA-Z0-9]+)/i', $data['text'], $username);
		if($username_exists > 0){
			for ($i=0; $i < $username_exists; $i++) {
				$user = $dba->query('SELECT id, COUNT(*) as count FROM '.T_USER.' WHERE username = ? AND status = "active"', $username[1][$i])->fetchArray();
				if($user['count'] > 0){
					if($user['id'] != $data['user_id']){
						return self::SetNotify(array(
							'user_id' => $user['id'],
							'notified_id' => $data['insert_id'],
							'type' => "u{$type}",
						));
					}
				}
			}
		}

		return false;
	}

	public static function DeleteUser($user_id, $permanent = false){
		global $dba;


		$deleted_at = time();

		$post_ids = $dba->query('SELECT id FROM '.T_POST.' WHERE user_id = ?', $user_id)->fetchAll(false);

		if(!empty($post_ids)){
			$post_ids = implode(',', $post_ids);
			$dba->query('DELETE FROM '.T_REACTION.' WHERE reacted_id IN ('.$post_ids.') AND place = "post"');
			$dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN ('.$post_ids.') AND type = "n_preact"');
		    $dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN ('.$post_ids.') AND type = "n_post"');
		}

		$comment_ids = $dba->query('SELECT id FROM '.T_COMMENTS.' WHERE user_id = ?', $user_id)->fetchAll(false);
		if(!empty($comment_ids)){
		    $comment_ids = implode(',', $comment_ids);

		    $dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN ('.$comment_ids.') AND (type = "n_pcomment" OR type = "n_ucomment")');
			$dba->query('DELETE FROM '.T_REACTION.' WHERE reacted_id IN ('.$comment_ids.') AND place = "comment"');
			$dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN ('.$comment_ids.') AND type = "n_creact"');
		    $dba->query('DELETE FROM '.T_REPORT.' WHERE reported_id IN ('.$comment_ids.') AND place = "comment"');
		}

		$reply_ids = $dba->query('SELECT id FROM '.T_REPLY.' WHERE user_id = ?', $user_id)->fetchAll(false);
		if(!empty($reply_ids)){
		    $reply_ids = implode(',', $reply_ids);

		    $dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN ('.$reply_ids.') AND (type = "n_preply" OR type = "n_ureply")');
			$dba->query('DELETE FROM '.T_REACTION.' WHERE reacted_id IN ('.$reply_ids.') AND place = "reply"');
			$dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN ('.$reply_ids.') AND type = "n_rreact"');
		    $dba->query('DELETE FROM '.T_REPORT.' WHERE reported_id IN ('.$reply_ids.') AND place = "reply"');
		}

		$dba->query('DELETE FROM '.T_VIEW.' WHERE user_id = ?', $user_id);
		$dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN (SELECT id FROM '.T_COLLABORATOR.' WHERE user_id = ?) AND type = "n_collab"', $user_id);
		$dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE notified_id IN (SELECT id FROM '.T_FOLLOWER.' WHERE user_id = ? OR profile_id = ?) AND type = "n_followers"', $user_id, $user_id);

		$message_ids = $dba->query('SELECT id FROM '.T_MESSAGE.' WHERE user_id = ?', $user_id)->fetchAll(false);

		if($permanent){
			if($dba->query('DELETE FROM '.T_USER.' WHERE id = ?', $user_id)->returnStatus()){
				$dba->query('DELETE FROM '.T_REPORT.' WHERE user_id = ?', $user_id);
		    	$dba->query('DELETE FROM '.T_REPORT.' WHERE reported_id = ? AND place = "user"', $user_id);
		    	if(!empty($post_ids)){
		    		$post_ids = implode(',', $post_ids);
		    		$dba->query('DELETE FROM '.T_REPORT.' WHERE reported_id IN ('.$post_ids.') AND place = "post"');
		    	}

		    	if(!empty($message_ids)){
		    		$message_ids = implode(',', $message_ids);
		    		$dba->query('DELETE FROM '.T_MESSAAN.' WHERE answered_id IN ('.$message_ids.') AND type = "text"');

			    	$messafi_ids = $dba->query('SELECT id FROM '.T_MESSAFI.' WHERE message_id IN ('.$message_ids.')')->fetchAll(false);

			    	if(!empty($messafi_ids)){
			    		$dba->query('DELETE FROM '.T_MESSAAN.' WHERE answered_id IN ('.implode(',', $messafi_ids).') AND (type = "file" OR type = "image")');
			    	}
			    }
				return true;
			}
		} else {
			if($dba->query('UPDATE '.T_USER.' SET status = "deleted" WHERE id = ?', $user_id)->returnStatus()){
				$dba->query('DELETE FROM '.T_NOTIFICATION.' WHERE user_id = ?', $user_id);
				$dba->query('DELETE FROM '.T_SAVED.' WHERE user_id = ?', $user_id);
				$dba->query('DELETE FROM '.T_SESSION.' WHERE user_id = ?', $user_id);
				$dba->query('DELETE FROM '.T_COLLABORATOR.' WHERE user_id = ?', $user_id);
				$dba->query('DELETE FROM '.T_TYPING.' WHERE user_id = ? OR profile_id = ?', $user_id, $user_id);

				if(!empty($post_ids)){
					$dba->query('UPDATE '.T_POST.' SET status = "deleted", deleted_at = ? WHERE id IN ('.implode(',', $post_ids).')', $deleted_at);
				}

				$dba->query('DELETE FROM '.T_FOLLOWER.' WHERE user_id = ? OR profile_id = ?', $user_id, $user_id);
				$dba->query('DELETE FROM '.T_COLLABORATOR.' WHERE user_id = ?', $user_id);
				$dba->query('DELETE FROM '.T_COMMENTS.' WHERE user_id = ?', $user_id);
				$dba->query('DELETE FROM '.T_REPLY.' WHERE user_id = ?', $user_id);
				$dba->query('DELETE FROM '.T_REACTION.' WHERE user_id = ?', $user_id);

				if(!empty($message_ids)){
		    		$message_ids = implode(',', $message_ids);
					if($dba->query('UPDATE '.T_MESSAGE.' SET deleted_at = ? WHERE id IN ('.$message_ids.')', $deleted_at)->returnStatus()){
						$dba->query('UPDATE '.T_MESSAFI.' SET deleted_at = ? WHERE message_id IN ('.$message_ids.')', $deleted_at);
					}
				}
				return true;
			}
		}


		return false;
	}

	public static function SavePost($post_id, $is_amp = false){
		global $dba, $TEMP;

		$post_id = self::Filter($post_id);
	
		if(!empty($post_id) && is_numeric($post_id)){
			if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE id = ? AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"', $post_id)->fetchArray(true) > 0){
				if($dba->query('SELECT COUNT(*) FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post_id)->fetchArray(true) > 0){
					if($dba->query('DELETE FROM '.T_SAVED.' WHERE user_id = ? AND post_id = ?', $TEMP['#user']['id'], $post_id)->returnStatus()){
						return array(
							'return' => true,
							'data' => $is_amp ? 400 : array(
								'S' => 200,
								'AC' => 'delete',
								'BT' => '.btn_save[data-id='.$post_id.']'
							)
						);
					}
				} else {
					if($dba->query('INSERT INTO '.T_SAVED.' (user_id, post_id, created_at) VALUES (?, ?, ?)', $TEMP['#user']['id'], $post_id, time())->returnStatus()){
						return array(
							'return' => true,
							'data' => $is_amp ? 200 : array(
								'S' => 200,
								'AC' => 'save',
								'BT' => '.btn_save[data-id='.$post_id.']'
							)
						);
					}
				}
			}
		}

		return array(
			'return' => false
		);
	}

	public static function Shows($input, $show){
		global $dba, $TEMP;

		$input = self::Filter($input);
		$show = self::Filter($show);

		if(!empty($show) && !empty($input) && in_array($input, array('birthday', 'gender', 'contact_email', 'followers', 'messages'))){
			$show = json_decode($show);
			$shows = array(
				false => 'off',
				true => 'on'
			);
			if(in_array($show, array_keys($shows))){
				$TEMP['#user']['shows'][$input] = $status = $shows[$show];
				if($dba->query("UPDATE ".T_USER." SET shows = ? WHERE id = ?", json_encode($TEMP['#user']['shows']), $TEMP['#user']['id'])->returnStatus()){
					$data = array(
						'S' => 200,
						'M' => $TEMP['#word']['configuration_updated']
					);
					if(in_array($input, array('followers', 'messages'))){
						$status = $status == 'on' ? 'enabled' : 'disabled';
						if($input == 'followers'){
							$word = 'followers_settings';
						} else if($input == 'messages'){
							$word = 'message_settings';
						}
						$data['M'] = "{$TEMP['#word'][$word]} ({$TEMP['#word'][$status]})";
					}
					return array(
						'return' => true,
						'data' => $data
					);
				}
			}
		}
		return array(
			'return' => false
		);
	}

	public static function LastMessage($profile_id, $is_new = false){
		global $dba, $TEMP;

		$data = array(
			'return' => false
		);

		$last_message = $dba->query('SELECT * FROM '.T_MESSAGE.' m WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND profile_id NOT IN ('.$TEMP['#blocked_users'].') AND ((user_id = ? AND deleted_fuser = 0) OR (profile_id = ? AND deleted_fprofile = 0)) AND (SELECT id FROM '.T_CHAT.' WHERE ((user_id = ? AND profile_id = ?) OR (user_id = ? AND profile_id = ?) AND id = m.chat_id)) = chat_id ORDER BY id DESC LIMIT 1', $TEMP['#user']['id'], $TEMP['#user']['id'], $TEMP['#user']['id'], $profile_id, $profile_id, $TEMP['#user']['id'])->fetchArray();

		if(!empty($last_message)){
			$TEMP['#last_unseen'] = $unseen = false;
			if($last_message['seen'] == 0 && self::IsOwner($last_message['profile_id'])){
				$TEMP['#last_unseen'] = $unseen = true;
			}
			if($last_message['text'] == NULL){
				$file = $dba->query('SELECT * FROM '.T_MESSAFI.' WHERE message_id = ? ORDER BY id DESC LIMIT 1', $last_message['id'])->fetchArray();
				if($file['deleted_at'] == 0){
					$last_text = $TEMP['#word']['attached_file'];
					if(self::IsOwner($last_message['user_id'])){
						$unseen = false;
						$last_text = "{$TEMP['#word']['you']}: {$TEMP['#word']['attached_file']}";
					}
				} else {
					$last_text = $TEMP['#word']['deputy_file_deleted'];
					if(self::IsOwner($last_message['user_id'])){
						$unseen = false;
						$last_text = "{$TEMP['#word']['you']}: {$last_text}";
					}
				}
			} else {
				if($last_message['deleted_at'] == 0){
					$last_text = self::TextFilter($last_message['text'], false);
					if(self::IsOwner($last_message['user_id'])){
						$unseen = false;
						$last_text = "{$TEMP['#word']['you']}: {$last_text}";
					}
				} else {
					$last_text = $TEMP['#word']['message_was_deleted'];
					if(self::IsOwner($last_message['user_id'])){
						$unseen = false;
						$last_text = "{$TEMP['#word']['you']}: {$last_text}";
					}
				}
			}

			$data = array(
				'return' => true,
				'unseen' => $unseen,
				'text' => $last_text,
				'chat_id' => $last_message['chat_id'],
				'created_at' => self::DateString($last_message['created_at'])
			);
		}

		return $data;
	}

	public static function Chat($chat, $is_new = false){
		global $dba, $TEMP;

		$data = array();
		
		$user_id = $chat['user_id'];
		if(self::IsOwner($chat['user_id'])){
			$user_id = $chat['profile_id'];
		}
		
		$last_message = self::LastMessage($user_id, $is_new);

		if($last_message['return']){
			$chat_id = $last_message['chat_id'];

			$TEMP['!id'] = $chat_id;
			$TEMP['!last_text'] = $last_message['text'];
			$TEMP['!last_created_at'] = $last_message['created_at'];

			$data['chat_id'] = $chat_id;
		}

		$user = self::Data($user_id);

		$TEMP['!user'] = $user['user'];
		$TEMP['!role'] = $user['role'];
		$TEMP['!user_id'] = $user['id'];

		$TEMP['!user_deleted'] = true;
		if($user['status'] == 'deleted'){
			$TEMP['!user_deleted'] = false;
			$TEMP['!username'] = $TEMP['#word']['user'];
			$TEMP['!avatar_s'] = self::Url('/themes/default/images/users/default-holder-s.jpeg');
		} else {
			$TEMP['!username'] = $user['username'];
			$TEMP['!avatar_s'] = $user['avatar_s'];
		}

		

		$data['html'] = self::Maket("messages/includes/user");

		return $data;
	}

	public static function Followers($user_id){
		global $dba, $TEMP;

		$number = $dba->query('SELECT COUNT(*) FROM '.T_FOLLOWER.' WHERE profile_id = ?', $user_id)->fetchArray(true);

		$followers = self::NumberShorten($number);
		$text = "{$followers} {$TEMP['#word']['follower']}";
		if($number > 1){
			$text = "{$followers} {$TEMP['#word']['followers']}";
		}

		return array(
			'number' => $number,
			'text' => $text
		);
	}

	public static function ValidateUrl($url, $protocol = false){
		global $TEMP;
		$return = preg_match($TEMP['#url_regex'], $url, $match);

		if($protocol){
			if(empty($match[2])){
				$url = "http://{$url}";
			}
			return array(
				'return' => $return,
				'url' => $url
			);
		}
		return $return;
	}

	public static function NumberShorten($n, $precision = 1) {
		if($n < 999) {
			// 0 - 900
			$n_format = number_format($n, $precision);
			$suffix = '';
		} else if($n < 999999) {
			// 0.9k-850k
			$n_format = number_format($n / 1000, $precision);
			$suffix = 'K';
		} else if($n < 999999999) {
			// 0.9m-850m
			$n_format = number_format($n / 1000000, $precision);
			$suffix = 'M';
		} else if($n < 999999999999) {
			// 0.9b-850b
			$n_format = number_format($n / 1000000000, $precision);
			$suffix = 'B';
		} else if($n < 999999999999999) {
			// 0.9t+
			$n_format = number_format($n / 1000000000000, $precision);
			$suffix = 'T';
		} else if($n < 999999999999999999) {
			// 0.9qa+
			$n_format = number_format($n / 1000000000000000, $precision);
			$suffix = 'Qa';
		} else {
			// 0.9qi+
			$n_format = number_format($n / 1000000000000000000, $precision);
			$suffix = 'Qi';
		}

	  	// Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
	  	// Intentionally does not affect partials, eg "1.50" -> "1.50"
		if($precision > 0) {
			$dotzero = '.'.str_repeat('0', $precision);
			$n_format = str_replace($dotzero, '', $n_format);
		}

		return "{$n_format} {$suffix}";
	}

	public static function SendEmail($data = array()) {
	    global $TEMP;

	    $mail = new PHPMailer();
	    $subject = self::Filter($data['subject']);
	    if(empty($data['is_html']) || !isset($data['is_html'])){
	    	$data['is_html'] = false;
	    }
	    if ($TEMP['#settings']['server_type'] == 'smtp') {
	        $mail->isSMTP();
	        $mail->Host        = $TEMP['#settings']['smtp_host'];
	        $mail->SMTPAuth    = true;
	        $mail->Username    = $TEMP['#settings']['smtp_username'];
	        $mail->Password    = $TEMP['#settings']['smtp_password'];
	        $mail->SMTPSecure  = $TEMP['#settings']['smtp_encryption'];
	        $mail->Port        = $TEMP['#settings']['smtp_port'];
	        $mail->SMTPOptions = array(
	            'ssl' => array(
	                'verify_peer' => false,
	                'verify_peer_name' => false,
	                'allow_self_signed' => true
	            )
	        );
	    } else {
	        $mail->IsMail();
	    }

	    $content = $data['text_body'];
	    if($data['is_html'] == true){
	    	$TEMP['title'] = $subject;
		    $TEMP['body'] = $content;
		    $content = self::Maket('emails/content');
	    }
	    $mail->IsHTML($data['is_html']);
	    if(!empty($data['reply_to'])){
	    	$mail->addReplyTo($data['reply_to'], $data['from_name']);
	    }
	    $mail->setFrom(self::Filter($data['from_email']), $data['from_name']);
	    $mail->addAddress(self::Filter($data['to_email']), $data['to_name']);
	    $mail->Subject = $subject;
	    $mail->CharSet = $data['charSet'];
	    $mail->MsgHTML($content);
	    if ($mail->send()) {
	        return true;
	    }
	    return false;
	}

	public static function Url($params = '') {
	    global $site_url;
	    return "{$site_url}/{$params}";
	}

	public static function ReturnUrl() {
		global $TEMP, $RUTE, $site_url;
		$params = "";
		if(!empty($_SERVER["REQUEST_URI"])){
			$url = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
			if(self::Url() != $url){
				$params = "home?{$RUTE['#p_return']}=".urlencode($url);
			}
		}
		return self::Url($params);
	}

	public static function SendChangeEmailToken($user = array(), $new_email = ''){
		global $TEMP, $RUTE;

		$data = array(
			'return' => false,
			'status' => 400,
			'error' => "*{$TEMP['#word']['oops_error_has_occurred']}"
		);

		if(empty($user['new_email'])){
			$user['new_email'] = $new_email;
		}

		if(!empty($user['new_email'])){
			$change_email = Specific::UserToken('change_email', $user['id'], true);
			$code = $change_email['code'];

			$TEMP['code'] = $code;
			$TEMP['username'] = $user['username'];
			$TEMP['url'] = Specific::Url("{$RUTE['#r_change_email']}/{$change_email['token']}?{$RUTE['#p_insert']}=$code");
			$TEMP['text'] = "{$TEMP['#word']['check_your_email']} {$user['new_email']}";
			$TEMP['footer'] = $TEMP['#word']['have_been_one_who_has_carried_out'];
			$TEMP['button'] = $TEMP['#word']['check_your_email'];

			if($change_email['return']){
				$send = Specific::SendEmail(array(
					'from_email' => $TEMP['#settings']['smtp_username'],
				    'from_name' => $TEMP['#settings']['title'],
					'to_email' => $user['new_email'],
					'to_name' => $user['username'],
					'subject' => $TEMP['#word']['check_your_email'],
					'charSet' => 'UTF-8',
			        'text_body' => Specific::Maket('emails/includes/send-code'),
					'is_html' => true
				));
				if($send){
					$data = array(
						'return' => true,
					    'status' => 200,
						'message' => $TEMP['#word']['mail_sent_successfully'],
					    'token' => $change_email['token']
					);
				} else {
					$data = array(
						'return' => false,
						'status' => 400,
				   		'error' => "*{$TEMP['#word']['error_sending_email_again_later']}"
					);
				}
			} else {
				$data = array(
					'return' => false,
					'status' => 400,
					'error' => "*{$TEMP['#word']['made_too_many_attempts_try']}"
				);
			}
		}

		return $data;
	}

	public static function ValidateToken($expires, $type){
		$pass = false;
		if(!empty($expires)){
			$expires = json_decode($expires, true);
			$expires = $expires[$type];
			if(time() > strtotime("+1 hour", $expires['updated_at'])){
				$pass = true;
			}
		}

		return $pass;
	}

	public static function UserToken($token, $user_id = 0, $affected = false){
		global $dba, $TEMP;
		for ($i=0; $i < 1; $i++) {
			$code = rand(111111, 999999);
	    	$tokenu = md5($code);
			if($dba->query("SELECT COUNT(*) FROM ".T_TOKEN." WHERE {$token} = ?", $tokenu)->fetchArray(true) > 0){
				$i--;
			}
		}
		$data = array(
			'return' => false,
			'code' => $code,
			'token' => $tokenu
		);
		if(!empty($user_id)){
			$query = '';
			$pass = true;
			if($affected){
				$pass = false;
				$time = time();
				$expires = $dba->query('SELECT expires FROM '.T_TOKEN.' WHERE user_id = ?', $user_id)->fetchArray(true);
				$ex_empty = array(
					'repeat' => 1,
					'updated_at' => $time
				);
				if(empty($expires)){
					$pass = true;
					$expires = array(
						$token => $ex_empty
					);
				} else {
					$expires = json_decode($expires, true);
					$expire = $expires[$token];

					if(in_array($token, array_keys($expires))){
						if($time < strtotime("+{$TEMP['#settings']['token_expiration_hours']} hour", $expire['updated_at'])){
							if($expire['repeat'] <= $TEMP['#settings']['token_expiration_attempts']){
								$pass = true;
								$expire['repeat'] = $expire['repeat']+1;
							}
						} else {
							$pass = true;
							$expire = $ex_empty;
						}
						$expires[$token] = $expire;
					} else {
						$pass = true;
						$expires[$token] = $ex_empty;
					}
				}
				$expires = json_encode($expires);
				$query = ", expires = '{$expires}'";
			}
			
			if($pass){
				if($dba->query("UPDATE ".T_TOKEN." SET {$token} = ?{$query} WHERE user_id = ?", $tokenu, $user_id)->returnStatus()){
					$data['return'] = true;
				}
			}
		}
		return $data;
	}

	public static function GetWidget($type, $root = 'post', $html = true){
		global $dba, $TEMP;


		$widget = $dba->query('SELECT * FROM '.T_WIDGET.' WHERE type = ?', $type)->fetchArray();

		$data = array(
			'return' => false
		);

		if(!empty($widget)){
			if($widget['status'] == 'enabled'){
				$data['return'] = true;
				
				if($html){
					if(!empty($widget['content'])){
						$TEMP['content_widget'] = $widget['content'];
						
						if($widget['type'] == 'htop'){
							$maket = 'home/includes/advertisement-home-top';
						} else if($widget['type'] == 'hload'){
							$maket = 'home/includes/advertisement-home-load';
						} else if($widget['type'] == 'horizposts'){
							$maket = 'includes/search-profile-category-tag/advertisement-horizontal-posts';
						} else if($widget['type'] == 'aside'){
							$maket = 'includes/search-post-profile-category-tag/advertisement-aside';
						} else if(in_array($widget['type'], array('pbody', 'ptop'))){
							$client_exists = preg_match('/data-ad-client=[\'|"]([^\"]+)[\'|"]/is', $widget['content'], $ad_client);
							$advertisement = 'advertisement-post-top';
							if($widget['type'] == 'pbody'){
								$advertisement = 'advertisement-body';
							}
							$maket = "post/includes/{$advertisement}";
							if($client_exists > 0){
								if($root == 'amp'){
									preg_match('/data-ad-slot=[\'|"]([^\"]+)[\'|"]/is', $widget['content'], $ad_slot);
									$TEMP['ad_client'] = $ad_client[1];
									$TEMP['ad_slot'] = $ad_slot[1];
								}
								$maket = "{$root}/includes/{$advertisement}";
							}
						}
						$data['html'] = self::Maket($maket);
					} else {
						$data['return'] = false;
					}
				}

				$data['name'] = $TEMP['#word']["widget_{$widget['name']}"];
				$data['content'] = $widget['content'];
				$data['updated_at'] = $widget['updated_at'];
				$data['created_at'] = $widget['created_at'];
			}
		}

		return $data;
	}

	public static function ProfileUrl($username){
		global $TEMP, $RUTE;
		return self::Url("{$RUTE['#r_user']}/{$username}");
	}

	public static function IdentifyFrame($frame, $autoplay = false, $is_amp = false){
		global $domain;

		$youtube = preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/", $frame, $yt_video);
		$vimeo = preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/", $frame, $vm_video);
		$dailymotion = preg_match("/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/", $frame, $dm_video);

		$twitch = preg_match("/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?twitch\.tv\/videos\/([0-9]+)$/", $frame, $tw_video);

		$tiktok = preg_match("/^(@[a-zA-z0-9]*|.*)(\/.*\/|trending.?shareId=)([\d]*)/", $frame, $tk_video);

		/*

		"/(?x)https?://(?:(?:www|m)\.(?:tiktok.com)(?:\/)?(@[a-zA-z0-9]*|.*)?(?:v|video|embed|trending)(?:\/)?(?:(\?shareId=|\&item_id=)(\#)$)?)(?P<id>[\da-z]+)/"

		"/^(?:http(?:s)?:\/\/)?(?:(?:www|m)\.(?:tiktok\.com)(?:\/)?(@[a-zA-z0-9]*|.*)?(?:v|video|embed|trending)(?:\/)?(?:\?shareId=)?)(?P<id>[\d]+)/"

		(.*)\/video\/(\d+)

		// /(^http(s)?://)?((www|en-es|en-gb|secure|beta|ro|www-origin|en-ca|fr-ca|lt|zh-tw|he|id|ca|mk|lv|ma|tl|hi|ar|bg|vi|th)\.)?twitch.tv/(?!directory|p|user/legal|admin|login|signup|jobs)(?P<channel>\w+)


		 else if($tiktok == true){
				$type = 'tiktok';
				$html = '<iframe src="//www.tiktok.com/embed/v2/'.$tk_video[3].'" width="100%" height="100%" frameborder="0"></iframe>';

			}

			*/

		
		$auparam = '';
		$autag = '';
		if($autoplay){
			if($is_amp){
				$autag = ' autoplay';
			} else {
				$auparam = 'autoplay=1';
				$autag = ' allow="autoplay"';
			}
		}

		if($youtube == true || $vimeo == true || $dailymotion == true || $twitch == true){
			if($youtube == true && strlen($yt_video[1]) == 11){
				$type = 'youtube';
				if($is_amp){
					$html = '<amp-youtube data-videoid="'.$yt_video[1].'" layout="responsive" width="480" height="270"'.$autag.'></amp-youtube>';
				} else {
					$html = '<iframe src="https://www.youtube.com/embed/'.$yt_video[1]."?{$auparam}".'" width="100%" height="450" frameborder="0" allowfullscreen'.$autag.'></iframe>';
				}
			} else if($vimeo == true){
				$type = 'vimeo';
				if($is_amp){
					$html = '<amp-vimeo data-videoid="'.$vm_video[1].'" layout="responsive" width="480" height="270"'.$autag.'></amp-vimeo>';
				} else {
					$html = '<iframe src="//player.vimeo.com/video/'.$vm_video[1]."?{$auparam}".'" width="100%" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen'.$autag.'></iframe>';
				}
			} else if($dailymotion == true){
				$type = 'dailymotion';
				if($is_amp){
					$html = '<amp-dailymotion data-videoid="'.$dm_video[2].'" layout="responsive" width="480" height="270"'.$autag.'></amp-dailymotion>';
				} else {
					$html = '<iframe src="//www.dailymotion.com/embed/video/'.$dm_video[2]."?{$auparam}".'" width="100%" height="450" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen'.$autag.'></iframe>';
				}
			} else if($twitch == true){
				if($is_amp){
					$html = '<amp-twitch-player data-video="'.$tw_video[1].'" layout="responsive" width="480" height="270"'.$autag.'></amp-twitch-player>';
				} else {
					$html = '<iframe src="//player.twitch.tv/?video='.$tw_video[1]."&{$auparam}&parent=".$domain.'&autoplay=true" frameborder="0" allowfullscreen="true" scrolling="no" width="100%" height="450" allowfullscreen'.$autag.'></iframe>';
				}
				//?channel=blastpremier
			}
		} else {
			return array(
				'return' => false
			);
		}

		return array(
			'return' => true,
			'type' => $type,
			'html' => $html
		);
	}

	public static function MaketFrame($url, $attrs = array(), $default = true, $is_amp = false){
		if(!is_array($attrs)){
			$attrs = html_entity_decode($attrs);
			$attrs = json_decode($attrs, true);
		}

		$defaults = array(
			'width' => '100%',
			'height' => '450',
			'frameborder' => 0
		);

		if($is_amp){
			$defaults = array(
				'width' => '200',
				'height' => '100',
				'layout' => 'responsive',
				'sandbox' => 'allow-scripts allow-same-origin',
				'frameborder' => 0
			);
		}

		$attributes = '';
		foreach ($attrs as $key => $attr) {
			if($attr['name'] == 'attribute'){
				if(!preg_match("/[^A-Za-z0-9\-\_]+/", $attr['value'])){
					$attributes .= " {$attr['value']}";
					if($attrs[$key+1]['name'] == 'value' && preg_match("/[^\"]+/", $attrs[$key+1]['value'])){
						$attributes .= '="'.$attrs[$key+1]['value'].'"';
					}
					if(in_array($attr['value'], $defaults)){
						unset($defaults[$attr['value']]);
					}
				} else {
					if($default){
						unset($attrs[$key]);
						unset($attrs[$key+1]);
					}
				}
			}
		}
		if(!empty($defaults) && $default){
			foreach ($defaults as $key => $value) {
				$attributes .= " {$key}".'="'.$value.'"';
			}
		}
		
		if(self::ValidateUrl($url)){
			$url = preg_replace('/([h|H][t|T]{2}[p|P][s|S]?|[r|R][t|T][s|S][p|P]):\/\//', '//', $url);
		}
		
		$html = '<iframe src="'.$url.'"'.$attributes.'></iframe>';
		if($is_amp){
			$html = '<amp-iframe src="'.$url.'"'.$attributes.'></amp-iframe>';
		}

		return array(
			'attrs' => $attrs,
			'html' => $html
		);
	}

	public static function GetSessions($value = array()){
	    $data = array();
	    $data['ip'] = 'Unknown';
	    $data['browser'] = 'Unknown';
	    $data['platform'] = 'Unknown';
	    if (!empty($value['details'])) {
	        $session = json_decode($value['details'], true);
	        $data['ip'] = $session['ip'];
	        $data['browser'] = $session['name'];
	        $data['platform'] = ucfirst($session['platform']);
	    }
	    return $data;
	}

	public static function RandomKey($minlength = 12, $maxlength = 20, $number = true) {
		$length = mt_rand($minlength, $maxlength);
		$number = $number == true ? "1234567890" : "";
	    return substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$number"), 0, $length);
	}

	public static function TokenSession() {
	    $token = md5(self::RandomKey(60, 70));
	    if (!empty($_SESSION['_LOGIN_TOKEN'])) {
	        return $_SESSION['_LOGIN_TOKEN'];
	    }
	    $_SESSION['_LOGIN_TOKEN'] = $token;
	    return $token;
	}

	public static function DateString($time) {
	    global $TEMP;
	    $diff = time() - $time;
	    if ($diff < 1) {
	        return $TEMP['#word']['now'];
	    }
	    $dates = array(
	        31536000 => array($TEMP['#word']['year'], $TEMP['#word']['years']),
	        2592000 => array($TEMP['#word']['month'], $TEMP['#word']['months']),
	        86400 => array($TEMP['#word']['day'], $TEMP['#word']['days']),
	        3600 => array($TEMP['#word']['hour'], $TEMP['#word']['hours']),
	        60 => array($TEMP['#word']['minute'], $TEMP['#word']['minutes']),
	        1 => array($TEMP['#word']['second'], $TEMP['#word']['seconds'])
	    );
	    foreach ($dates as $key => $value) {
	        $was = $diff/$key;
	        if ($was >= 1) {
	            $was_int = intval($was);
	            $string = $was_int > 1 ? $value[1] : $value[0];
	            return "{$TEMP['#word']['does']} $was_int $string";
	        }
	    }
	}

	public static function DateFormat($ptime, $type = 'normal') {
	    global $TEMP; 

	    $day = strtolower(strftime("%A", $ptime));
	    $month = strtolower(strftime("%B", $ptime)); 
	    $day = $TEMP['#word'][$day];
	    $month = $TEMP['#word'][$month];
	    $B = mb_substr($month, 0, 3, 'UTF-8');

	    $dateFinaly = strftime("%e " . $B . ". %Y", $ptime);
	    if($type == 'day'){
	    	$dateFinaly = strftime("%e", $ptime);
	    }
	    if($type == 'month'){
	    	$dateFinaly = $month;
	    }
	    if($type == 'complete'){
	    	$dateFinaly = strftime("$day, %e {$TEMP['#word']['of']} $month, %Y", $ptime);
	    }

	    return $dateFinaly;
	}

	public static function Words($language = 'en', $paginate = false, $page = 1, $keyword = ''){
	    global $dba, $TEMP;
	    $data = array();
	    if($paginate == true){
	    	$query = '';
		    if(!empty($keyword)){
		        $query = " WHERE word LIKE '%{$keyword}%' OR `{$language}` LIKE '%{$keyword}%'";
		    }
	        $data['sql'] = $dba->query("SELECT * FROM word{$query} LIMIT ? OFFSET ?", 10, $page)->fetchAll();
	        $data['total_pages'] = $dba->totalPages;
	    } else {
	        $sql = $dba->query("SELECT word, {$language} FROM ".T_WORD)->fetchAll();
	        foreach ($sql as $value) {
	            $data[$value['word']] = $value[$language];
	        }
	    }
	    return $data;
	}

	public static function Languages($query = 'type') {
	    global $dba;
	    $data = array();
	    $langs = $dba->query("DESCRIBE word")->fetchAll();
	    foreach ($langs as $key => $lang) {
			if($key != 0){
				if($dba->query('SELECT COUNT(*) FROM '.T_LANGUAGE.' WHERE lang = ? AND status = "enabled"', $lang['Field'])->fetchArray(true) > 0){
					$data[] = $lang['Field'];
				}
			}
	    }
	    return $data;
	}

	public static function Language(){
		global $dba, $TEMP, $RUTE;

		$lang = Specific::Filter($_GET[$RUTE['#p_language']]);

		$language = $TEMP['#settings']['language'];

		if ($TEMP['#loggedin'] == true) {
			$user = Specific::Data(null, 4);
			if(empty($lang)){
			    if (in_array($user['language'], $TEMP['#languages'])) {
			        $language = $user['language'];
				}
			} else {
			    if(in_array($lang, $TEMP['#languages'])){
			        if($dba->query('UPDATE '.T_USER.' SET language = ? WHERE id = ?', $lang, $user['id'])->returnStatus()){
			        	$language = $lang;
			        }
			    }
			}
		} else {
			if(!empty($lang)){
				$language = $lang;
			} else if(!empty($_COOKIE['language'])){
				$language = Specific::Filter($_COOKIE['language']);	
			}
		}
		setcookie("language", $language, time() + 315360000, "/");
		return $language;
	}

	public static function GetClientIp() {
	    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $value) {
	        if (array_key_exists($value, $_SERVER) ) {
	            foreach (array_map('trim', explode(',', $_SERVER[$value])) as $ip) {
	                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE || FILTER_FLAG_NO_RES_RANGE) !== false) {
	                    return $ip;
	                }
	            }
	        }
	    }
	    return '?';
	}

	public static function IsOwner($user_id) {
	    global $TEMP;
	    if ($TEMP['#loggedin'] === true) {
	        if ($TEMP['#user']['id'] == $user_id) {
	            return true;
	        }
	    }
	    return false;
	}

	public static function BrowserDetails() {
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $is_mobile = false;
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version = "";

	    // Is mobile platform?
	    if (preg_match("/(android|Android|ipad|iphone|IPhone|ipod)/i", $u_agent)) {
	        $is_mobile = true;
	    }

	    // First get the platform?
	    // First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
		    $platform = 'Linux';
		} elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
		    $platform = 'Mac';
		} elseif (preg_match('/windows|win32/i', $u_agent)) {
		    $platform = 'Windows';
		} else if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $u_agent)){
			$platform = 'Mobile';
		} else if(preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $u_agent)){
			$platform = 'Tablet';
		}


	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    } elseif(preg_match('/Firefox/i',$u_agent)) {
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    } elseif(preg_match('/Chrome/i',$u_agent)) {
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    } elseif(preg_match('/Safari/i',$u_agent)) {
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    } elseif(preg_match('/Opera/i',$u_agent)) {
	        $bname = 'Opera';
	        $ub = "Opera";
	    } elseif(preg_match('/Netscape/i',$u_agent)) {
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }

	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        } else {
	            $version= $matches['version'][1];
	        }
	    } else {
	        $version= $matches['version'][0];
	    }

	    // check if we have a number
	    if ($version == null || $version == "") {
	        $version="?";
	    }
	    return array(
	        'validate' => array(
	            'is_mobile' => $is_mobile
	        ),
	        'details' => array(
	            'ip' => self::GetClientIp(),
	            'userAgent' => $u_agent,
	            'name' => $bname,
	            'version' => $version,
	            'platform'  => $platform,
	            'pattern' => $pattern
	        )
	    );
	}

	public static function Fingerprint($user_id = 0){
		$client_details = self::BrowserDetails();
		$fingerprint = sha1(md5("{$client_details['validate']['is_mobile']}{$client_details['details']['ip']}{$client_details['details']['userAgent']}{$client_details['details']['name']}{$client_details['details']['version']}{$client_details['details']['platform']}{$client_details['details']['pattern']}{getallheaders()['Accept']}"));

		return $fingerprint;
	}

	public static function MainPosts($post_ids = array()){
		global $dba, $TEMP;

		$query = '';
		if(!empty($post_ids)){
			$query = ' AND id NOT IN ('.implode(',', $post_ids).')';
		}

		$main = $dba->query('SELECT * FROM '.T_POST.' WHERE published_at >= ?'.$query.' AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY published_at ASC LIMIT 15', (time()-(60*60*24*7)))->fetchAll();

		if(count($main) < 15){
			if(!empty($main)){
				$main_ids = array();
				$count = 15-count($main);
				foreach ($main as $post) {
					$main_ids[] = $post['id'];
				}
				$new_main = $dba->query('SELECT * FROM '.T_POST.' WHERE id NOT IN ('.implode(',', $main_ids).') AND user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved" ORDER BY published_at ASC LIMIT '.$count)->fetchAll();
				foreach ($new_main as $key => $post) {
					$main[] = $post;
				}
			} else {
				if(!empty($post_ids)){
					$query = ' AND id NOT IN ('.implode(',', $post_ids).')';
				}
				$main = $dba->query('SELECT * FROM '.T_POST.' WHERE user_id NOT IN ('.$TEMP['#blocked_users'].') AND status = "approved"'.$query.' ORDER BY published_at ASC LIMIT 15')->fetchAll();
			}
		}

		return $main;
	}

	public static function CheckRecaptcha($token){
		global $TEMP;
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
			'secret' => $TEMP['#settings']['recaptcha_private_key'],
			'response' => self::Filter($token)
		)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);

		return json_decode($response, true);
	}

	public static function br2nl($text){
		return str_ireplace(array("<br />", "<br>", "<br/>"), "\r\n", $text);
	}

	public static function DestroyMaket(){
	    global $TEMP;
	    unset($TEMP['!data']);
	    foreach ($TEMP as $key => $value) {
	        if(substr($key, 0, 1) === '!'){
	            unset($TEMP[$key]);
	        }
	    }
	    return $TEMP;
	}

	public static function Maket($page){
	    global $TEMP, $RUTE, $site_url;

		if(empty(pathinfo($page, PATHINFO_EXTENSION))){
			$page = "{$page}.html";
		}

	    $file = "./themes/{$TEMP['#settings']['theme']}/html/{$page}";
	    if(!file_exists($file)){
	    	exit("No found: $file");
	    }
	    ob_start();
	    require($file);
	    $html = ob_get_contents();
	    ob_end_clean();

	    $page = preg_replace_callback('/{\$word->(.+?)}/i', function($matches) use ($TEMP) {
	        return (isset($TEMP['#word'][$matches[1]])?$TEMP['#word'][$matches[1]]:"");
	    }, $html);
	    $page = preg_replace_callback('/{\$settings->(.+?)}/i', function($matches) use ($TEMP) {
	        return (isset($TEMP['#settings'][$matches[1]])?$TEMP['#settings'][$matches[1]]:"");
	    }, $page);
	    $page = preg_replace_callback('/{\$theme->\{(.+?)\}}/i', function($matches) use ($TEMP) {
	        return self::Url("themes/".$TEMP['#settings']['theme']."/".$matches[1]);
	    }, $page);
	    $page = preg_replace_callback('/{\$url->\{(.+?)\}}/i', function($matches) use ($TEMP) {
	        return self::Url($matches[1]!="home"?$matches[1]:"");
	    }, $page);
	    $page = preg_replace_callback('/{\$data->(.+?)}/i', function($matches) use ($TEMP) {
	        return (isset($TEMP['data'][$matches[1]])?$TEMP['data'][$matches[1]]:"");
	    }, $page);
	    $page = preg_replace_callback('/{(\#[a-zA-Z0-9_]+)}/i', function($matches) use ($TEMP, $RUTE) {
	        $tmp_match = $TEMP[$matches[1]];
	        $rte_match = $RUTE[$matches[1]];
	        $return = self::Filter($_GET[$RUTE['#p_return']]);
	    	if(in_array($matches[1], array('#r_login', '#r_register', '#r_logout', '#r_2check'))){
		    	preg_match("/(?:[\w]+)\/([\w\-]+)(?:\/([\w\-]+)|)/", $_SERVER['REQUEST_URI'], $current_url);
		    	if(isset($TEMP['#current_url'])){
		    		$current_url[1] = $TEMP['#current_url'];
		    	}
				$no_returns = array($RUTE['#r_home'], $RUTE['#r_login'], $RUTE['#r_register'], $RUTE['#r_forgot_password'], $RUTE['#r_reset_password'], $RUTE['#r_2check'], $RUTE['#r_verify_email']);
				if(!in_array($current_url[1], $no_returns) || (!empty($return) && !in_array($return, $no_returns))){
					if($current_url[1] == $current_url[2] || !isset($current_url[2])){
						$current_url = urlencode($current_url[1]);
					} else {
				    	$current_url = urlencode("{$current_url[1]}/{$current_url[2]}");
					}
				    if(!empty($return)){
				        $current_url = urlencode($return);
				    }
				    return (!empty($current_url)?"{$rte_match}?{$RUTE['#p_return']}={$current_url}":$rte_match);
				}
			}
	        if(is_bool($tmp_match)){
	        	$tmp_match = json_encode($tmp_match);
	        }
	        return (isset($tmp_match)?$tmp_match:(isset($rte_match)?$rte_match:""));
	    }, $page);
	    $page = preg_replace_callback('/{\$([a-zA-Z0-9_]+)}/i', function($matches) use ($TEMP) {
	    	$match = $TEMP[$matches[1]];
	    	if(is_bool($match)){
	        	$match = json_encode($match);
	        }
	        return (isset($TEMP[$matches[1]])?$match:"");
	    }, $page);

	    if ($TEMP['#loggedin'] === true) {
	        $page = preg_replace_callback('/{\$me->(.+?)}/i', function($matches) use ($TEMP) {
	            return (isset($TEMP['#user'][$matches[1]])) ? $TEMP['#user'][$matches[1]] : '';
	        }, $page);
	    }
	    $page = preg_replace_callback('/{\!data->(.+?)}/i', function($matches) use ($TEMP) {
	        $match = $TEMP['!data'][$matches[1]];
	        return (isset($match)?$match:"");
	    }, $page);
	    $page = preg_replace_callback('/{\!([a-zA-Z0-9_]+)}/i', function($matches) use ($TEMP) {
	        $match = $TEMP["!".$matches[1]];
	    	if(is_bool($match)){
	        	$match = json_encode($match);
	        }
	        return (isset($match)?$match:"");
	    }, $page);

	    return $page;
	}

	public static function HTMLFormatter($page){
		global $TEMP;
		$page = preg_replace('/<!--[^\[](.*)[^\]]-->/Uuis', '', $page);

		preg_match_all('/{%%(.+?)%%}/is', $page, $scripts);
		$page = preg_replace(array(
			'/{\*HERE\*}/',
			'/{%%(.+?)%%}/is',
			'/class=("|\')@(.+?)/',
			'/{#(.*?)#}/'
		), array(
			implode('', $scripts[1]),
			'',
			'class=$1$2',
			'$1'
		), $page);

		return $page;
	}

	public static function Logged() {
		global $dba;
	    if (isset($_SESSION['_LOGIN_TOKEN']) && !empty($_SESSION['_LOGIN_TOKEN'])) {
	        if ($dba->query('SELECT COUNT(*) FROM session WHERE token = "'.self::Filter($_SESSION['_LOGIN_TOKEN']).'"')->fetchArray(true) > 0) {
	            return true;
	        }
	    } else if (isset($_COOKIE['_LOGIN_TOKEN']) && !empty($_COOKIE['_LOGIN_TOKEN'])) {
	        if ($dba->query('SELECT COUNT(*) FROM session WHERE token = "'.self::Filter($_COOKIE['_LOGIN_TOKEN']).'"')->fetchArray(true) > 0) {
	            return true;
	        }
	    }
	    return false;
	}

	public static function Filter($input){
	    global $dba;
	    if(!empty($input)){
	    	$input = mysqli_real_escape_string($dba->returnConnection(), $input);
		    $input = htmlspecialchars($input, ENT_QUOTES);
		    $input = str_replace(array('\r\n', '\n\r', '\r', '\n'), " <br>", $input);
		    $input = stripslashes($input);
	    }
	    return $input;
	}
	
	public static function PostCreate($deliver = array()) {
	    ob_end_clean();
	    header("Content-Encoding: none");
	    header("Connection: close");
	    ignore_user_abort(); // optional
	    ob_start();
	    if (!empty($deliver)) {
	        header('Content-Type: application/json');
	        echo json_encode($deliver);
	    }
	    $size = ob_get_length();
	    header("Content-Length: $size");
	    ob_end_flush(); // Strange behaviour, will not work
	    flush(); // Unless both are called!
	   	session_write_close();
	    if (is_callable('fastcgi_finish_request')) {
	        fastcgi_finish_request();
	    }
	    // Do processing here
	}

	public static function Sitemap($background = false){
		global $dba, $TEMP;
		$dbaLimit = 45000;
		$posts = $dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE status = "approved"')->fetchArray(true);
		if(empty($posts)){
			return false;
		}
		$time = time();
		if($background == true){
			self::PostCreate(array(
				'status' => 200,
                'message' => $TEMP['#word']['sitemap_being_generated_may_take_few_minutes'],
                'time' => self::DateFormat($time)
			));
		}
		$folder_sitemap = 'sitemaps';
		if (!file_exists($folder_sitemap)) {
			mkdir($folder_sitemap, 0777, true);
		}
		$limit = ceil($posts / $dbaLimit);
		$sitemap_x = '<?xml version="1.0" encoding="UTF-8"?>
		                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$sitemap_index = '<?xml version="1.0" encoding="UTF-8"?>
		                    <sitemapindex  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" >';
		for ($i=1; $i <= $limit; $i++) {            
		  	$sitemap_index .= "\n<sitemap>
		                          <loc>" . self::Url("sitemaps/sitemap-$i.xml") . "</loc>
		                          <lastmod>" . date('c') . "</lastmod>
		                        </sitemap>";
		  	$paginate = $dba->query('SELECT * FROM '.T_POST.' WHERE status = "approved" ORDER BY id ASC LIMIT ? OFFSET ?', $dbaLimit, $i)->fetchAll();
		  	foreach ($paginate as $post) {
		    $sitemap_x .= '<url>
		                    <loc>' . self::Url($post['slug']) . '</loc>
		                    <lastmod>' . date('c', $post['created_at']). '</lastmod>
		                    <changefreq>monthly</changefreq>
		                    <priority>0.8</priority>
		                  </url>' . "\n";
		  	}
		  	$sitemap_x .= "\n</urlset>";

		  	file_put_contents("sitemaps/sitemap-$i.xml", $sitemap_x);
		  	$sitemap_x = '<?xml version="1.0" encoding="UTF-8"?>
		                  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'; 
		}
		$sitemap_index .= '</sitemapindex>';
		$file_final = file_put_contents('sitemap-index.xml', $sitemap_index);
		$dba->query('UPDATE '.T_SETTING.' SET value = ? WHERE name = "last_sitemap"', $time);
		return true;
	}
}
?>