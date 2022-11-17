<?php
if($one == 'catag'){
	$catag_ids = Functions::Filter($_POST['catag_ids']);
	$catag_ids = html_entity_decode($catag_ids);
	$catag_ids = json_decode($catag_ids);
	$type = Functions::Filter($_POST['typet']);
	$catag_id = Functions::Filter($_POST['catag_id']);

	if(!empty($catag_id) && is_numeric($catag_id) && in_array($type, array('category', 'tag'))){
		$TEMP['#page'] = $type;
		if($type == 'category'){
			$catag_load = Loads::Category($catag_id, $catag_ids);
		} else {
			$catag_load = Loads::Tag($catag_id, $catag_ids);
		}
		if($catag_load['return']){
			$widget = Functions::GetWidget('horizposts');
			if($widget['return']){
				$catag_load['html'] .= $widget['html'];
			}
			$deliver = array(
				'S' => 200,
				'HT' => $catag_load['html'],
				'IDS' => $catag_load['catag_ids']
			);
		}
	}
} else if($one == 'saved'){
	$saved_ids = Functions::Filter($_POST['saved_ids']);
	$saved_ids = html_entity_decode($saved_ids);
	$saved_ids = json_decode($saved_ids);

	if($TEMP['#loggedin'] == true){
		$saved_load = Loads::Saved($saved_ids);
		if($saved_load['return']){
			$deliver = array(
				'S' => 200,
				'HT' => $saved_load['html'],
				'IDS' => $saved_load['saved_ids']
			);
		}
	}
} else if($one == 'sitemap'){
	$date = Functions::Filter($_POST['date']);
	$day = Functions::Filter($_POST['day']);
	$month = Functions::Filter($_POST['month']);
	$year = Functions::Filter($_POST['year']);

	$sitemap_ids = Functions::Filter($_POST['sitemap_ids']);
	$sitemap_ids = html_entity_decode($sitemap_ids);
	$sitemap_ids = json_decode($sitemap_ids);

	if(!empty($day) && !empty($month) && !empty($year) && $date == 'other' || $date != 'other'){
		if($dba->query('SELECT status FROM '.T_PAGE.' WHERE slug = "sitemap"')->fetchArray(true) == 'enabled'){
			$sitemap = Loads::Sitemap(array(
				'date' => $date,
				'day' => $day,
				'month' => $month,
				'year' => $year
			), $sitemap_ids);

			if($sitemap['return']){
				$deliver = array(
					'S' => 200,
					'HT' => $sitemap['html'],
					'IDS' => $sitemap['sitemap_ids']
				);
			}
		}
	}
}
?>