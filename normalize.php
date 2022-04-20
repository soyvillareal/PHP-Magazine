<?php
require_once('./includes/info.php');
require_once('./includes/class.db.php');
require_once('./includes/specs.php');


$widgets = $dba->query("SELECT * FROM widget")->fetchAll();

$trues = array();

foreach ($widgets as $widget) {
	$updated_at = strtotime($widget['updated_at']);
	$created_at = strtotime($widget['created_at']);

	if($dba->query("UPDATE widget SET updated_att = ?, created_att = ? WHERE id = ".$widget['id'], $updated_at, $created_at)->returnStatus()){
		$trues[] = 'true';
	} else {
		$trues[] = 'false';
	}
}

print_r($trues);

?>