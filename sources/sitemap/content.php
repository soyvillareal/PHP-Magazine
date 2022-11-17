<?php 

$page = $dba->query('SELECT * FROM '.T_PAGE.' WHERE slug = "sitemap"')->fetchArray();

if($page['status'] == 'disabled'){
	header("Location: " . Functions::Url('404'));
	exit();
}

$date = Functions::Filter($_GET[$ROUTE['#p_date']]);

$day = Functions::Filter($_GET[$ROUTE['#p_day']]);
$month = Functions::Filter($_GET[$ROUTE['#p_month']]);
$year = Functions::Filter($_GET[$ROUTE['#p_year']]);

$TEMP['#has_link'] = true;
$TEMP['#has_dates'] = false;
$TEMP['#has_content'] = false;
$TEMP['#has_posts'] = false;

$TEMP['#date'] = 'other';
if(in_array($date, array($ROUTE['#p_today'], $ROUTE['#p_yesterday'], $ROUTE['#p_this_week'], $ROUTE['#p_last_week']))){
	$TEMP['#date'] = $date;
}

$TEMP['#day'] = 0;
$TEMP['#month'] = 0;
$TEMP['#year'] = 0;
if(!empty($day)){
	$TEMP['#day'] = $day;
}
if(!empty($month)){
	$TEMP['#month'] = $month;
}
if(!empty($year)){
	$TEMP['#year'] = $year;
}

if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE status = "approved"')->fetchArray(true) > 0){
	$TEMP['#has_content'] = true;

	$today = strtotime("00:00:00 today");
	$dates = array(
		'today' => $today,
		'yesterday' => array(
			'start' => $today,
			'end' => strtotime("-1 days")
		),
		'this_week' => strtotime("-7 days"),
		'last_week' => array(
			'start' => strtotime("last sunday",strtotime("-1 week")),
			'end' => strtotime("this sunday",strtotime("-1 week"))
		)
	);

	foreach ($dates as $key => $d) {
		$query = ' AND created_at >= '.$d;
		if(is_array($d)){
			$query = " AND created_at >= {$d['start']} AND created_at <= {$d['end']}";
		}
		if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE status = "approved"'.$query)->fetchArray(true) > 0){
			$TEMP['#has_dates'] = true;
			$TEMP['!title'] = $TEMP['#word'][$key];
			$TEMP['!url'] = Functions::Url("{$ROUTE['#r_sitemap']}?{$ROUTE['#p_date']}={$ROUTE["#p_{$key}"]}");

			$TEMP['dates'] .= Functions::Build('sitemap/includes/date');
		}
	}

	if(!empty($date)){
		$sitemap = Loads::Sitemap(array(
			'date' => $date
		));

		if($sitemap['return']){
			$TEMP['#has_posts'] = true;
			$TEMP['content'] = $sitemap['html'];
		} else {
	    	header("Location: " . Functions::Url('404'));
	    	exit();
		}
	} else {
		$first_date = $dba->query('SELECT MIN(created_at) FROM '.T_POST.' WHERE status = "approved"')->fetchArray(true);

		if(!empty($day)){
			$TEMP['title'] = $TEMP['#word']['publications'];
			$TEMP['#year'] = $year;
			$TEMP['#month'] = $month;
			$TEMP['#day'] = $day;

			$sitemap = Loads::Sitemap(array(
				'date' => 'other',
				'day' => $day,
				'month' => $month,
				'year' => $year
			));

			if($sitemap['return']){
				$TEMP['#has_posts'] = true;
				$TEMP['content'] = $sitemap['html'];
			} else {
		    	header("Location: " . Functions::Url('404'));
		    	exit();
			}

		} else if(!empty($month)){
			$TEMP['title'] = $TEMP['#word']['days'];
			$TEMP['#year'] = $year;
			$TEMP['#month'] = $month;

			$month = date('F', mktime(0, 0, 0, $month, 10));

			$first_day = strtotime("1 {$month} {$year}");
			$last_day = strtotime("last day of {$month} {$year}");

			if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE created_at >= ? AND created_at <= ? AND status = "approved"', $first_day, $last_day)->fetchArray(true) > 0){


				for ($i=1; $i <= date('t', $last_day); $i++) {
					$first_hour = strtotime("00:00:00 {$i} {$month} {$year}");
					$last_hour = strtotime("23:59:59 {$i} {$month} {$year}");

					if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE created_at > ? AND created_at < ? AND status = "approved"', $first_hour, $last_hour)->fetchArray(true) > 0){
						$TEMP['!day'] = $i;
						$TEMP['content'] .= Functions::Build('sitemap/includes/day');
					}
				}
			} else {
		    	header("Location: " . Functions::Url('404'));
		    	exit();
			}
		} else if(!empty($year)){
			$TEMP['title'] = $TEMP['#word']['months'];
			$TEMP['#year'] = $year;

			$month_first = strtotime("1 January {$year}");
			$month_last = strtotime("31 December {$year}");

			if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE created_at >= ? AND created_at <= ? AND status = "approved"', $month_first, $month_last)->fetchArray(true) > 0){
				for ($i=0; $i < 12; $i++) {
					$first_day = strtotime("+{$i} month", $month_first);
					$last_day = strtotime("last day of this month", $first_day);

					$test = $dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE created_at > ? AND created_at < ? AND status = "approved"', $first_day, $last_day)->fetchArray(true);


					if($test > 0){
						$TEMP['!month'] = Functions::DateFormat($first_day, 'month');
						$TEMP['!month_url'] = date('m', $first_day);
						$TEMP['content'] .= Functions::Build('sitemap/includes/month');
					}
				}
			} else {
		    	header("Location: " . Functions::Url('404'));
		    	exit();
			}
		} else {
			$TEMP['#has_link'] = false;
			$TEMP['title'] = $TEMP['#word']['years'];
			$diff = abs(time() - $first_date); 
			$years = floor($diff / (365*60*60*24));

			for ($i=0; $i <= $years; $i++){ 
				$year = date('Y', $first_date)+$i;
				$first_month = strtotime("1 January {$year}");
				$last_month = strtotime("31 December {$year}");

				if($dba->query('SELECT COUNT(*) FROM '.T_POST.' WHERE created_at >= ? AND created_at <= ? AND status = "approved"', $first_month, $last_month)->fetchArray(true) > 0){
					$TEMP['!year'] = $year;
					$TEMP['content'] .= Functions::Build('sitemap/includes/year');
				}
			}
		}
	}
}

$TEMP['sitemap_ids'] = implode(',', $sitemap['sitemap_ids']);

$TEMP['#page'] = 'sitemap';
$TEMP['#title'] = $TEMP['#word']["page_{$page['slug']}"] . ' - ' . $TEMP['#settings']['title'];
$TEMP['#description'] = $page['description'];
$TEMP['#keyword'] = $page['keywords'];

$TEMP['#content'] = Functions::Build('sitemap/content');
?>