<?php
if($one == 'catag'){
	$catag_ids = Specific::Filter($_POST['catag_ids']);
	$catag_ids = html_entity_decode($catag_ids);
	$catag_ids = json_decode($catag_ids);
	$type = Specific::Filter($_POST['typet']);
	$catag_id = Specific::Filter($_POST['catag_id']);

	if(!empty($catag_id) && is_numeric($catag_id) && in_array($type, array('category', 'tag'))){
		$TEMP['#page'] = $type;
		if($type == 'category'){
			$catag_load = Load::Category($catag_id, $catag_ids);
		} else {
			$catag_load = Load::Tag($catag_id, $catag_ids);
		}
		if($catag_load['return']){
			$widget = Specific::GetWidget('horizposts');
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
} else if($one == 'save'){
	$save_ids = Specific::Filter($_POST['save_ids']);
	$save_ids = html_entity_decode($save_ids);
	$save_ids = json_decode($save_ids);

	if($TEMP['#loggedin'] == true){
		$save_load = Load::Save($save_ids);
		if($save_load['return']){
			$deliver = array(
				'S' => 200,
				'HT' => $save_load['html'],
				'IDS' => $save_load['save_ids']
			);
		}
	}
} else if($one == 'sitemap'){
	$date = Specific::Filter($_POST['date']);
	$day = Specific::Filter($_POST['day']);
	$month = Specific::Filter($_POST['month']);
	$year = Specific::Filter($_POST['year']);

	$sitemap_ids = Specific::Filter($_POST['sitemap_ids']);
	$sitemap_ids = html_entity_decode($sitemap_ids);
	$sitemap_ids = json_decode($sitemap_ids);

	if(!empty($day) && !empty($month) && !empty($year) && $date == 'other' || $date != 'other'){
		if($dba->query('SELECT status FROM '.T_PAGE.' WHERE slug = "sitemap"')->fetchArray(true) == 'enabled'){
			$sitemap = Load::Sitemap(array(
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