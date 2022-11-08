<?php
require_once('./includes/autoload.php');

$type = Specific::Filter($_POST['type']);
if(empty($type)){
	$type = Specific::Filter($_GET['type']);
}

if(!empty($type) && in_array($type, array('save', 'darkmode', 'reaction', 'next-page-config'))){
	$code = 400;
	$deliver = array();

    header("Content-type: application/json");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: ". str_replace('.', '-', $TEMP['#site_url']) .".cdn.ampproject.org");
    header("AMP-Access-Control-Allow-Source-Origin: " . $TEMP['#site_url']);
    header("Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin");


	if($type == 'darkmode'){
		$darkmode = Specific::Filter($_POST['darkmode']);
		if(!empty($darkmode) && in_array($darkmode, array('true', 'false'))){
			$darkmodes = array(
				'false' => 1,
				'true' => 0
			);
			if($TEMP['#loggedin'] == true){
				if($dba->query('UPDATE '.T_USER.' SET darkmode = ? WHERE id = ?', $darkmodes[$darkmode], $TEMP['#user']['id'])->returnStatus()){
					$code = 200;
					$typet = true;
					$deliver['text'] = $TEMP['#word']['light_mode'];
					if($darkmode == 'true'){
						$typet = false;
						$deliver['text'] = $TEMP['#word']['dark_mode'];
					}
					$deliver['type'] = json_encode($typet);
				}
			} else {
				$code = 200;
				setcookie("darkmode", $darkmodes[$darkmode], time() + 604800, "/");
				$deliver['type'] = json_encode($darkmode == 'true' ? false : true);
			}
		}
	}

	if($TEMP['#loggedin'] == true){
		if($type == 'save'){
			$save_post = Specific::SavePost($_POST['post_id'], true);

			if($save_post['return']){
				$code = $save_post['data'];
			}
		} else if($TEMP['#loggedin'] == true && $type == 'reaction'){
			/*
			$post_id = $_POST['post_id'];
			$reaction = $_POST['reaction'];
			if(empty($post_id)){
				$post_id = $_GET['post_id'];
			}
			if(empty($reaction)){
				$reaction = $_GET['reaction'];
			}

			$reaction_post = Specific::ReactionPost($post_id, $reaction);

			if($reaction_post['return']){
				$code = $reaction_post['data']['S'];
				$dislikes = 0;
				if(!empty($reaction_post['data']['CO'])){
					$dislikes = $reaction_post['data']['CO'];
				}
				$deliver['likes'] = $reaction_post['data']['CR'];
				$deliver['dislikes'] = $dislikes;
			}
			*/
		} else if($TEMP['#loggedin'] == true && $type == 'next-page-config'){
			// Este codigo esta sin utilizar

			/*
			$post_ids = Specific::Filter($_GET['post_ids']);

			$post_ids = empty($post_ids) ? array() : explode(',', $post_ids);
			
			$query = '';
			if(!empty($post_ids)){
				foreach ($post_ids as $key => $id) {
					if(!ctype_digit($id)){
						unset($post_ids[$key]);
					}
				}
				if(!empty($post_ids)){
					$query = ' AND id NOT IN ('.implode(',', $post_ids).')';
				}
			}

			$posts = $dba->query('SELECT * FROM '.T_POST.' WHERE status = "approved"'.$query.' LIMIT 3')->fetchAll();
			if(!empty($posts)){
				$config = array();
				foreach ($posts as $post) {
					$config['pages'][] = array(
						'image' => Specific::GetFile($post['thumbnail'], 1, 's'),
						'title' => $post['title'],
						'url' => Specific::Url("amp/{$post['slug']}")
					);

					$post_ids[] = $post['id'];
				}

				$config['next'] = Specific::Url("amp.php?type=next-page-config&post_ids=".implode(',', $post_ids));

				$code = 200;
				$deliver = $config;
			}
			*/
			
		}
	}

	if(!empty($code)){
		http_response_code($code);
	}
	echo json_encode($deliver);
    exit;

}
?>