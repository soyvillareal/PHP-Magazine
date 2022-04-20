<?php 
if ($TEMP['#loggedin'] === true && Specific::Admin() === true) {
    if($one == 'search-course') {
        $nextrues = array(true);
        $keyword = Specific::Filter($_POST['keyword']);
        $type = Specific::Filter($_POST['typet']);
        $course_id = Specific::Filter($_POST['course_id']);
        $plan_id = Specific::Filter($_POST['plan_id']);
        $program_id = Specific::Filter($_POST['program_id']);
        $courses = Specific::Filter($_POST['courses']);
        $html = '';
        $query = '';
        if(!empty($keyword)){
            $courses = explode(',', $courses);
            $query .= " WHERE (id LIKE '%$keyword%' OR name LIKE '%$keyword%' OR code LIKE '%$keyword%')";
            if($type == 'edit'){
                if(!empty($course_id)){
                    $query .= " AND id <> $course_id";
                }
            }
            if(!empty($plan_id)){
                $plcourses = $dba->query('SELECT course_id FROM curriculum WHERE plan_id = '.$plan_id)->fetchAll(false);
                $deleted = array_diff($plcourses, $courses);
                if(!empty($deleted)){
                    $query .= ' AND (id NOT IN ((SELECT course_id FROM curriculum)) OR id IN ('.implode(',', $deleted).'))';
                } else {
                    $query .= ' AND id NOT IN ((SELECT course_id FROM curriculum))';
                }
            } else {
                if(!empty($course_id) && $course_id != 'null'){
                    $plan_id = $dba->query('SELECT plan_id FROM curriculum WHERE course_id = '.$course_id)->fetchArray();
                    if($plan_id){
                        $plcourses = $dba->query('SELECT course_id FROM curriculum c WHERE plan_id = '.$plan_id)->fetchAll(false);
                        $pkcourses = $dba->query('SELECT preknowledge FROM course WHERE id = '.$course_id)->fetchArray();
                        if(!empty($pkcourses)){
                            $deleted = array_diff(explode(',', $pkcourses), $courses);
                            if(!empty($deleted)){
                                $query .= " AND (id NOT IN (".$pkcourses.") OR id IN (".implode(',', $deleted)."))";
                            } else {
                                $query .= " AND id NOT IN (".$pkcourses.")";
                            }
                        }
                        if(!empty($plcourses)){
                            $query .= " AND id IN (".implode(',', $plcourses).")";
                        }
                    } else {
                        $query .= " AND id = 0";
                    }
                } else {
                    $TEMP['#programs'] = $dba->query('SELECT id FROM program')->fetchAll(false);
                    if(in_array($program_id, $TEMP['#programs'])){
                        $plan_id = $dba->query('SELECT id FROM plan WHERE program_id = '.$program_id)->fetchArray();
                        if(!empty($plan_id)){
                            $plcourses = $dba->query('SELECT course_id FROM curriculum c WHERE plan_id = '.$plan_id)->fetchAll(false);
                            if(!empty($plcourses)){
                                $query .= " AND id IN (".implode(',', $plcourses).")";
                            }
                        } else {
                            $query .= " AND id = 0";
                        }
                    } else {
                        $query .= " AND id = 0";
                    }
                }
            }
            $courses = $dba->query('SELECT * FROM course'.$query.' LIMIT 5')->fetchAll();
        }

        if (!empty($courses)) {
            foreach ($courses as $course) {
                $html .= "<button class='tipsit-search display-flex btn-noway border-bottom border-grey padding-10 background-hover' data-id='".$course['id']."' data-name='".$course['name']."'>".$course['name']."</button>";
            }
            $deliver['status'] = 200;
        } else {
            $TEMP['keyword'] = $keyword;
            $html .= Specific::Maket('not-found/aj-result-for');
        }
        $deliver['html'] = $html;
    } else if($one == 'this-courses'){
        $qualifications  = array('activated', 'deactivated');
        $credits  = array(1, 2, 3, 4);
        $types  = array('practice', 'theoretical');
        $schedules  = array('daytime', 'nightly');
        $emptys = array();
        $errors = array();


        $type = Specific::Filter($_POST['type']);
        $id = Specific::Filter($_POST['id']);
        $name = Specific::Filter($_POST['name']);
        $preknowledge = Specific::Filter($_POST['preknowledge']);
        $qualification = Specific::Filter($_POST['qualification']);
        $credit = Specific::Filter($_POST['credits']);
        $quota = Specific::Filter($_POST['quota']);
        $typec = Specific::Filter($_POST['typec']);
        $schedule = Specific::Filter($_POST['schedule']);

        if(empty($name)){
            $emptys[] = 'name';
        }
        if(empty($qualification)){
            $emptys[] = 'qualification';
        }
        if(empty($credit)){
            $emptys[] = 'credits';
        }
        if(empty($quota)){
            $emptys[] = 'quota';
        }
        if(empty($typec)){
            $emptys[] = 'type';
        }
        if(empty($schedule)){
            $emptys[] = 'schedule';
        }
        if(empty($emptys)){
            if(!empty($preknowledge)){
                $preknowledges = explode(',', $preknowledge);
                $prektrues = array();
                foreach ($preknowledges as $prek) {
                    if($dba->query('SELECT COUNT(*) FROM course WHERE id = '.$prek)->fetchArray() > 0){
                        $prektrues[] = true;
                    } else {
                        $prektrues[] = false;
                    }
                }
                if(in_array(false, $prektrues)){
                    $errors[] = 'preknowledge';
                }
            }
            if(!in_array($qualification, $qualifications)){
                $errors[] = 'qualification';
            }
            if(!in_array($credit, $credits)){
                $errors[] = 'credits';
            }
            if(!in_array($typec, $types)){
                $errors[] = 'type';
            }
            if(!in_array($schedule, $schedules)){
                $errors[] = 'schedule';
            }
            if (empty($errors)) {
                if(!empty($type)){
                    if($type == 'add'){
                        $code = Specific::RandomKey(5, 7);
                        if($dba->query('SELECT COUNT(*) FROM course WHERE code = "'.$code.'"')->fetchArray() > 0){
                            $code = Specific::RandomKey(5, 7);
                        }
                        if($dba->query('INSERT INTO course (code, name, preknowledge, qualification, credits, quota, type, schedule, `time`) VALUES (?, ?, ?, ?, ?, ?, ?, ?,'.time().')', $code, $name, $preknowledge, $qualification, $credit, $quota, $typec, $schedule)->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    } else if(isset($id) && is_numeric($id)){
                        if($dba->query('UPDATE course SET name = ?, preknowledge = ?, qualification = ?, credits = ?, quota = ?, type = ?, schedule = ? WHERE id = '.$id, $name, $preknowledge, $qualification, $credit, $quota, $typec, $schedule)->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    }
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'errors' => $errors
                );
            }
        } else {
            $deliver = array(
                'status' => 400,
                'emptys' => $emptys
            );
        }
    } else if($one == 'delete-course'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            $enrolled_exists = $dba->query('SELECT COUNT(*) FROM enrolled WHERE course_id = '.$id)->fetchArray();
            if($enrolled_exists == 0){
                if($dba->query('DELETE FROM course WHERE id = '.$id)->returnStatus()){
                    $deliver['status'] = 200;
                };
            } else {
                $deliver = array(
                    'status' => 400,
                    'error' => $TEMP['#word']['you_cannot_delete']
                );
            }
        }
    } else if($one == 'get-pitems'){
        $id = Specific::Filter($_POST['id']);
        if(isset($id) && is_numeric($id)){
            $items = $dba->query('SELECT name, faculty_id, title, snies, level, semesters, mode FROM program WHERE id = '.$id)->fetchArray();
            if (!empty($items)) {
                $deliver = array(
                    'status' => 200,
                    'items' => $items
                );
            }
        }
    } else if($one == 'this-programs'){
        $semesters  = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
        $modalities  = array('presential', 'distance', 'virtual');
        $faculties = $dba->query('SELECT id FROM faculty')->fetchAll(false);
        $emptys = array();
        $errors = array();

        $id = Specific::Filter($_POST['id']);
        $name = Specific::Filter($_POST['name']);
        $faculty_id = Specific::Filter($_POST['faculty_id']);
        $title = Specific::Filter($_POST['title']);
        $snies = Specific::Filter($_POST['snies']);
        $level = Specific::Filter($_POST['level']);
        $semester = Specific::Filter($_POST['semesters']);
        $mode = Specific::Filter($_POST['mode']);
        $type = Specific::Filter($_POST['type']);

        if(empty($name)){
            $emptys[] = 'name';
        }
        if(empty($faculty_id)){
            $emptys[] = 'faculty_id';
        }
        if(empty($title)){
            $emptys[] = 'title';
        }
        if(empty($snies)){
            $emptys[] = 'snies';
        }
        if(empty($level)){
            $emptys[] = 'level';
        }
        if(empty($semester)){
            $emptys[] = 'semesters';
        }
        if(empty($mode)){
            $emptys[] = 'mode';
        }

        if(empty($emptys)){
            if(!in_array($semester, $semesters)){
                $errors[] = 'semesters';
            }
            if(!in_array($mode, $modalities)){
                $errors[] = 'mode';
            }
            if(!in_array($faculty_id, array_values($faculties))){
                $errors[] = 'faculty_id';
            }
            if (empty($errors)) {
                if(!empty($type)){
                    if($type == 'add'){
                        if($dba->query('INSERT INTO program (name, faculty_id, title, snies, level, semesters, mode, `time`) VALUES ("'.$name.'", '.$faculty_id.',"'.$title.'",'.$snies.',"'.$level.'",'.$semester.',"'.$mode.'",'.time().')')->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    } else if(isset($id) && is_numeric($id)){
                        if($dba->query('UPDATE program SET name = ?, faculty_id = ?, title= ?, snies = ?, level = ?, semesters = ?, mode = ? WHERE id = '.$id, $name, $faculty_id, $title, $snies, $level, $semester, $mode)->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    }
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'errors' => $errors
                );
            }
        } else {
            $deliver = array(
                'status' => 400,
                'emptys' => $emptys
            );
        }
    } else if($one == 'delete-program'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            if($dba->query('DELETE FROM program WHERE id = '.$id)->returnStatus()){
                $deliver['status'] = 200;
            };
        }
    } else if($one == 'search-programs') {
        $keyword = Specific::Filter($_POST['keyword']);
            $html = '';
            $query = '';
            if(!empty($keyword)){
                $query .= " WHERE name LIKE '%$keyword%' OR title LIKE '%$keyword%' OR snies LIKE '%$keyword%'";
            }
            $programs = $dba->query('SELECT * FROM program'.$query.' LIMIT ? OFFSET ?', 10, 1)->fetchAll();
            $deliver['total_pages'] = $dba->totalPages;
            if (!empty($programs)) {
                foreach ($programs as $program) {
                    $TEMP['!id'] = $program['id'];
                    $TEMP['!name'] = $program['name'];
                    $TEMP['!title'] = $program['title'];
                    $TEMP['!snies'] = $program['snies'];
                    $TEMP['!level'] = $program['level'] == 'pre' ? $TEMP['#word']['undergraduate'] : ($program['level'] == 'tec' ? $TEMP['#word']['technique'] : $TEMP['#word']['technologist']);
                    $TEMP['!semesters'] = $program['semesters'];
                    $TEMP['!mode'] = $TEMP['#word'][$program['mode']];
                    $TEMP['!time'] = Specific::DateFormat($program['time']);
                    $html .= Specific::Maket('programs/includes/programs-list');
                }
                Specific::DestroyMaket();
                $deliver['status'] = 200;
            } else {
                if(!empty($keyword)){
                    $TEMP['keyword'] = $keyword;
                    $html .= Specific::Maket('not-found/result-for');
                } else {
                    $html .= Specific::Maket('not-found/programs');
                }
            }
            $deliver['html'] = $html;
    } else if($one == 'table-programs'){
        $page = Specific::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $query = '';
            $keyword = Specific::Filter($_POST['keyword']);
            if(!empty($keyword)){
                $query .= " WHERE name LIKE '%$keyword%' OR title LIKE '%$keyword%' OR snies LIKE '%$keyword%'";
            }
            $programs = $dba->query('SELECT * FROM program'.$query.' LIMIT ? OFFSET ?', 10, $page)->fetchAll();
            if (!empty($programs)) {
                foreach ($programs as $program) {
                    $TEMP['!id'] = $program['id'];
                    $TEMP['!name'] = $program['name'];
                    $TEMP['!title'] = $program['title'];
                    $TEMP['!snies'] = $program['snies'];
                    $TEMP['!level'] = $program['level'] == 'pre' ? $TEMP['#word']['undergraduate'] : ($program['level'] == 'tec' ? $TEMP['#word']['technique'] : $TEMP['#word']['technologist']);
                    $TEMP['!semesters'] = $program['semesters'];
                    $TEMP['!mode'] = $TEMP['#word'][$program['mode']];
                    $TEMP['!time'] = Specific::DateFormat($program['time']);
                    $html .= Specific::Maket('programs/includes/programs-list');
                }
                Specific::DestroyMaket();
                $deliver['status'] = 200;
            }
            $deliver['status'] = 200;
            $deliver['html'] = $html;
        }
    } else if($one == 'this-faculty'){
        $statusa = array('activated', 'deactivated');
        $id = Specific::Filter($_POST['id']);
        $type = Specific::Filter($_POST['type']);
        $name = Specific::Filter($_POST['name']);
        $status = Specific::Filter($_POST['status']);
        $emptys = array();
        $errors = array();

        if(empty($name)){
            $emptys[] = 'name';
        }
        if(empty($status)){
            $emptys[] = 'status';
        }
        if(empty($emptys)){
            if(!in_array($status, array_values($statusa))){
                $errors[] = 'status';
            }
            if (empty($errors)) {
                if(!empty($type)){
                    if($type == 'add'){
                        if($dba->query('INSERT INTO faculty (name, status, `time`) VALUES ("'.$name.'","'.$status.'",'.time().')')->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    } else if(isset($id) && is_numeric($id)){
                        if($dba->query('UPDATE faculty SET name = "'.$name.'", status = "'.$status.'" WHERE id = '.$id)->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    }
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'errors' => $errors
                );
            }
        } else {
            $deliver = array(
                'status' => 400,
                'emptys' => $emptys
            );
        }
    } else if($one == 'get-fitems'){
        $id = Specific::Filter($_POST['id']);
        if(isset($id) && is_numeric($id)){
            $items = $dba->query('SELECT name, status FROM faculty WHERE id = '.$id)->fetchArray();
            if (!empty($items)) {
                $deliver = array(
                    'status' => 200,
                    'items' => $items
                );
            }
        }
    } else if($one == 'search-faculties') {
        $keyword = Specific::Filter($_POST['keyword']);
            $html = '';
            $query = '';
            if(!empty($keyword)){
                $query .= " WHERE name LIKE '%$keyword%'";
            }
            $faculties = $dba->query('SELECT * FROM faculty'.$query.' LIMIT ? OFFSET ?', 10, 1)->fetchAll();
            $deliver['total_pages'] = $dba->totalPages;
            if (!empty($faculties)) {
                foreach ($faculties as $faculty) {
                    $TEMP['!id'] = $faculty['id'];
                    $TEMP['!name'] = $faculty['name'];
                    $TEMP['!status'] = $TEMP['#word'][$faculty['status']];
                    $TEMP['!time'] = Specific::DateFormat($faculty['time']);
                    $html .= Specific::Maket('faculties/includes/faculties-list');
                }
                Specific::DestroyMaket();
                $deliver['status'] = 200;
            } else {
                if(!empty($keyword)){
                    $TEMP['keyword'] = $keyword;
                    $html .= Specific::Maket('not-found/result-for');
                } else {
                    $html .= Specific::Maket('not-found/faculties');
                }
            }
            $deliver['html'] = $html;
    } else if($one == 'delete-faculty'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            $courses_exists = $dba->query('SELECT COUNT(*) FROM program WHERE faculty_id = '.$id)->fetchArray();
            if($courses_exists == 0){
                if($dba->query('DELETE FROM faculty WHERE id = '.$id)->returnStatus()){
                    $deliver['status'] = 200;
                };
            } else {
                $deliver = array(
                    'status' => 400,
                    'error' => $TEMP['#word']['you_cannot_delete']
                );
            }
        }
    } else if($one == 'table-faculties'){
        $page = Specific::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $query = '';
            $keyword = Specific::Filter($_POST['keyword']);
            if(!empty($keyword)){
                $query = " WHERE name LIKE '%$keyword%'";
            }
            $faculties = $dba->query('SELECT * FROM faculty'.$query.' LIMIT ? OFFSET ?', 10, $page)->fetchAll();
            if (!empty($faculties)) {
                foreach ($faculties as $faculty) {
                    $TEMP['!id'] = $faculty['id'];
                    $TEMP['!name'] = $faculty['name'];
                    $TEMP['!status'] = $TEMP['#word'][$faculty['status']];
                    $TEMP['!time'] = Specific::DateFormat($faculty['time']);
                    $html .= Specific::Maket('faculties/includes/faculties-list');
                }
                Specific::DestroyMaket();
                $deliver['status'] = 200;
            }
            $deliver['status'] = 200;
            $deliver['html'] = $html;
        }
    } else if($one == 'get-peitems'){
        $id = Specific::Filter($_POST['id']);
        if(isset($id) && is_numeric($id)){
            $items = $dba->query('SELECT name, start, final, status FROM period WHERE id = '.$id)->fetchArray();
            $name = explode('-', $items['name']);
            unset($items['name']);
            $items['year'] = $name[0];
            $items['period'] = $name[1];
            $items['start'] = date('Y-m-d', $items['start']);
            $items['final'] = date('Y-m-d', $items['final']);
            if (!empty($items)) {
                $deliver = array(
                    'status' => 200,
                    'items' => $items
                );
            }
        }
    } else if($one == 'get-ditems'){
        $id = Specific::Filter($_POST['id']);
        if(!empty($id) && is_numeric($id)){
            $period = $dba->query('SELECT name, final, dates FROM period WHERE id = '.$id)->fetchArray();
            $dates = Specific::ComposeDates($period['dates']);
            if (!empty($dates)) {
                $deliver = array(
                    'status' => 200,
                    'items' => json_decode($period['dates'], true),
                    'dates' => $dates,
                    'name' => $period['name'],
                    'final' => date('Y-m-d', $period['final'])
                );
            }
        }
    } else if($one == 'table-periods'){
        $page = Specific::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $query = '';
            $keyword = Specific::Filter($_POST['keyword']);
            if(!empty($keyword)){
                $query .= " WHERE id LIKE '%$keyword%' OR name LIKE '%$keyword%'";
            }
            $periods = $dba->query('SELECT * FROM period'.$query.' ORDER BY start ASC LIMIT ? OFFSET ?', 10, $page)->fetchAll();
            if (!empty($periods)) {
                foreach ($periods as $period) {
                    $TEMP['!id'] = $period['id'];
                    $TEMP['!name'] = $period['name'];
                    $TEMP['!start'] = Specific::DateFormat($period['start']);
                    $TEMP['!final'] = Specific::DateFormat($period['final']);
                    $TEMP['!status'] = $TEMP['#word'][$period['status']];
                    $TEMP['!time'] = Specific::DateFormat($period['time']);
                    $html .= Specific::Maket('periods/includes/periods-list');
                }
                Specific::DestroyMaket();
                $deliver['status'] = 200;
            }
            $deliver['status'] = 200;
            $deliver['html'] = $html;
        }
    } else if($one == 'search-periods') {
        $keyword = Specific::Filter($_POST['keyword']);
        $html = '';
        $query = '';
        if(!empty($keyword)){
            $query .= " WHERE id LIKE '%$keyword%' OR name LIKE '%$keyword%'";
        }
        $periods = $dba->query('SELECT * FROM period'.$query.' ORDER BY start ASC LIMIT ? OFFSET ?', 10, 1)->fetchAll();
        $deliver['total_pages'] = $dba->totalPages;
        if (!empty($periods)) {
            foreach ($periods as $period) {
                $TEMP['!id'] = $period['id'];
                $TEMP['!name'] = $period['name'];
                $TEMP['!start'] = Specific::DateFormat($period['start']);
                $TEMP['!final'] = Specific::DateFormat($period['final']);
                $TEMP['!status'] = $TEMP['#word'][$period['status']];
                $TEMP['!time'] = Specific::DateFormat($period['time']);
                $html .= Specific::Maket('periods/includes/periods-list');
            }
            Specific::DestroyMaket();
            $deliver['status'] = 200;
        } else {
            if(!empty($keyword)){
                $TEMP['keyword'] = $keyword;
                $html .= Specific::Maket('not-found/result-for');
            } else {
                $html .= Specific::Maket('not-found/periods');
            }
        }
        $deliver['html'] = $html;
    } else if($one == 'delete-period'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            $courses_exists = $dba->query('SELECT COUNT(*) FROM enrolled WHERE period_id = '.$id)->fetchArray();
            if($courses_exists == 0){
                if($dba->query('DELETE FROM period WHERE id = '.$id)->returnStatus()){
                    $deliver['status'] = 200;
                };
            } else {
                $deliver = array(
                    'status' => 400,
                    'error' => $TEMP['#word']['you_cannot_delete']
                );
            }
        }
    } else if($one == 'this-periods'){
        $statusa  = array('enabled', 'disabled');
        $emptys = array();
        $errors = array();

        $id = Specific::Filter($_POST['id']);
        $period = Specific::Filter($_POST['period']);
        $start = Specific::Filter($_POST['start']);
        $final = Specific::Filter($_POST['final']);
        $status = Specific::Filter($_POST['status']);
        $type = Specific::Filter($_POST['type']);

        if(empty($period)){
            $emptys[] = 'period';
        }
        if(empty($start)){
            $emptys[] = 'start';
        }
        if(empty($final)){
            $emptys[] = 'final';
        }
        if(empty($status)){
            $emptys[] = 'status';
        }
        if(empty($emptys)){
                $year = explode('-', $start)[0];
                $name = "$year-$period";
                $start = strtotime($start);
                $final = strtotime($final);
                if(!in_array($status, $statusa)){
                    $errors[] = 'status';
                }
                if($start > $final){
                    $errors[] = 'start';
                }
                $query = $type == 'edit' ? ' AND id <> '.$id : '';
                if($dba->query('SELECT COUNT(*) FROM period WHERE name = "'.$name.'"'.$query)->fetchArray() == 0){
                    if($dba->query('SELECT COUNT(*) FROM period WHERE start < '.$start.' AND final > '.$final)->fetchArray() == 0){
                        if (empty($errors)) {
                            if(!empty($type)){
                                if($type == 'add'){
                                    if($status == 'enabled'){
                                        $dba->query('UPDATE period SET status = "disabled" WHERE status = "enabled"');
                                    }
                                    $courses = $dba->query('SELECT id FROM course')->fetchAll(false);
                                    foreach ($courses as $course_id) {
                                        $code = Specific::RandomKey(5, 7);
                                        if($dba->query('SELECT COUNT(*) FROM course WHERE code = "'.$code.'"')->fetchArray() > 0){
                                            $code = Specific::RandomKey(5, 7);
                                        }
                                        $dba->query('UPDATE course SET code = "'.$code.'" WHERE id = '.$course_id);
                                    }
                                    if($dba->query('INSERT INTO period (name, start, final, status, `time`) VALUES ("'.$name.'", '.$start.', '.$final.', "'.$status.'",'.time().')')->returnStatus()){
                                        $deliver['status'] = 200;
                                    }
                                } else {
                                    if(isset($id) && is_numeric($id)){
                                        if($status == 'disabled' || $dba->query('SELECT id FROM period WHERE status = "enabled"')->fetchArray() == $id || ($status == 'enabled' && $dba->query('SELECT COUNT(*) FROM period WHERE status = "enabled"')->fetchArray() == 0)){
                                            if($dba->query('UPDATE period SET name = ?, start = ?, final = ?, status = ? WHERE id = '.$id, $name, $start, $final, $status)->returnStatus()){
                                                $deliver['status'] = 200;
                                            }
                                        } else {
                                            $deliver = array(
                                                'status' => 400,
                                                'error' => $TEMP['#word']['there_already_active_period']
                                            );
                                        }
                                    }
                                }
                            }
                        } else {
                            $deliver = array(
                                'status' => 400,
                                'errors' => $errors
                            );
                        }
                    } else {
                        $deliver = array(
                            'status' => 400,
                            'error' => $TEMP['#word']['there_already_period_these_dates']
                        );
                    }
                } else {
                    $deliver = array(
                        'status' => 400,
                        'error' => $TEMP['#word']['this_period_already_exists']
                    );
                }
            
        } else {
            $deliver = array(
                'status' => 400,
                'emptys' => $emptys
            );
        }
    } else if($one == 'this-dates'){
        $emptys = array();
        $errors = array();

        $id = Specific::Filter($_POST['id']);
        $dates = Specific::Filter($_POST['dates']);

        if(!empty($id) && is_numeric($id) && !empty($dates)){
            $dates = html_entity_decode($dates);
            $dates = json_decode($dates);
            
            if($dba->query('SELECT COUNT(*) FROM period WHERE id = '.$id)->fetchArray() > 0){
                if(!empty($dates[1]) && empty($dates[2]) || empty($dates[1]) && !empty($dates[2])){
                    $emptys[] = 0;
                }
                if(!empty($dates[3]) && empty($dates[4]) || empty($dates[3]) && !empty($dates[4])){
                    $emptys[] = 1;
                }
                if(!empty($dates[8]) && empty($dates[9]) || empty($dates[8]) && !empty($dates[9])){
                    $emptys[] = 2;
                }
                if(!empty($dates[11]) && empty($dates[12]) || empty($dates[11]) && !empty($dates[12])){
                    $emptys[] = 3;
                }
                $aerror = array();
                foreach ($dates as $key => $value) {
                    $aerror[$key] = true;
                    if(!empty($value)){
                        $value = strtotime($value);
                        if($value > $dba->query('SELECT final FROM period WHERE id = '.$id)->fetchArray()){
                            $aerror[$key] = false;
                        }
                    }
                }

                if(!in_array(false, $aerror)){
                    if(empty($emptys)){
                        if(strtotime($dates[1]) > strtotime($dates[2])){
                            $errors[] = 0;
                        }
                        if(strtotime($dates[3]) > strtotime($dates[4])){
                            $errors[] = 1;
                        }
                        if(strtotime($dates[8]) > strtotime($dates[9])){
                            $errors[] = 2;
                        }
                        if(strtotime($dates[11]) > strtotime($dates[12])){
                            $errors[] = 3;
                        }
                        if (empty($errors)) {
                            if($dba->query('UPDATE period SET dates = ? WHERE id = '.$id, json_encode($dates))->returnStatus()){
                                $deliver['status'] = 200;
                            }
                        } else {
                            $deliver = array(
                                'status' => 400,
                                'errors' => $errors
                            );
                        }
                    } else {
                        $deliver = array(
                            'status' => 400,
                            'emptys' => $emptys
                        );
                    }
                }
            }
        }
    } else if($one == 'get-plitems'){
        $id = Specific::Filter($_POST['id']);
        if(isset($id) && is_numeric($id)){
            $items = $dba->query('SELECT program_id, name, resolution, date_approved, duration, credits, note_mode, status FROM plan WHERE id = '.$id)->fetchArray();
            $items['date_approved'] = date('Y-m-d', $items['date_approved']);
            if (!empty($items)) {
                $deliver = array(
                    'status' => 200,
                    'items' => $items
                );
            }
        }
    } else if($one == 'this-plans'){
        $programs = $dba->query('SELECT id FROM program')->fetchAll(false);
        $modalities  = array('100', '30-30-40');
        $statusa  = array('enabled', 'disabled');
        $emptys = array();
        $errors = array();

        $id = Specific::Filter($_POST['id']);
        $name = Specific::Filter($_POST['name']);
        $resolution = Specific::Filter($_POST['resolution']);
        $date_approved = Specific::Filter($_POST['date_approved']);
        $duration = Specific::Filter($_POST['duration']);
        $credits = Specific::Filter($_POST['credits']);
        $note_mode = Specific::Filter($_POST['note_mode']);
        $program_id = Specific::Filter($_POST['program_id']);
        $status = Specific::Filter($_POST['status']);
        $type = Specific::Filter($_POST['type']);

        if(empty($name)){
            $emptys[] = 'name';
        }
        if(empty($resolution)){
            $emptys[] = 'resolution';
        }
        if(empty($date_approved)){
            $emptys[] = 'date_approved';
        }
        if(empty($duration)){
            $emptys[] = 'duration';
        }
        if(empty($credits)){
            $emptys[] = 'credits';
        }
        if(empty($note_mode)){
            $emptys[] = 'note_mode';
        }
        if(empty($program_id)){
            $emptys[] = 'program_id';
        }
        if(empty($status)){
            $emptys[] = 'status';
        }

        if(empty($emptys)){
            $approved = explode('-', $date_approved);
            $date_approved = strtotime($date_approved);
            if(!checkdate($approved[1], $approved[2], $approved[0])){
                $errors[] = 'date_approved';
            }
            if(!is_numeric($resolution)){
                $errors[] = 'resolution';
            }
            if(!is_numeric($duration)){
                $errors[] = 'duration';
            }
            if(!is_numeric($credits)){
                $errors[] = 'credits';
            }
            if(!in_array($note_mode, array_values($modalities))){
                $errors[] = 'note_mode';
            }
            if(!in_array($program_id, array_values($programs))){
                $errors[] = 'program_id';
            }
            if(!in_array($status, array_values($statusa))){
                $errors[] = 'status';
            }
            if (empty($errors)) {
                if(!empty($type)){
                    if($type == 'add'){
                        if($dba->query('INSERT INTO plan (program_id, name, resolution, date_approved, duration, credits, note_mode, status, `time`) VALUES ('.$program_id.', "'.$name.'", '.$resolution.', '.$date_approved.', '.$duration.', '.$credits.', "'.$note_mode.'", "'.$status.'", '.time().')')->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    } else if(isset($id) && is_numeric($id)){
                        if($dba->query('UPDATE plan SET program_id = ?, name = ?, resolution = ?, date_approved = ?, duration = ?, credits = ?, note_mode = ?, status = ? WHERE id = '.$id, $program_id, $name, $resolution, $date_approved, $duration, $credits, $note_mode, $status)->returnStatus()){
                            $deliver['status'] = 200;
                        }
                    }
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'errors' => $errors
                );
            }
        } else {
            $deliver = array(
                'status' => 400,
                'emptys' => $emptys
            );
        }
    } else if($one == 'delete-plan'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            if($dba->query('SELECT COUNT(*) FROM curriculum WHERE plan_id = '.$id)->fetchArray() == 0){
                if($dba->query('DELETE FROM plan WHERE id = '.$id)->returnStatus()){
                    $deliver['status'] = 200;
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'error' => $TEMP['#word']['you_cannot_delete']
                );
            }
        }
    } else if($one == 'search-plans') {
        $keyword = Specific::Filter($_POST['keyword']);
            $html = '';
            $query = '';
            if(!empty($keyword)){
                $query .= " WHERE name LIKE '%$keyword%' OR resolution LIKE '%$keyword%'";
            }
            $plans = $dba->query('SELECT * FROM plan'.$query.' LIMIT ? OFFSET ?', 10, 1)->fetchAll();
            $deliver['total_pages'] = $dba->totalPages;
            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    $TEMP['!id'] = $plan['id'];
                    $TEMP['!name'] = $plan['name'];
                    $TEMP['!program'] = $dba->query('SELECT name FROM program WHERE id = '.$plan['program_id'])->fetchArray();
                    $TEMP['!resolution'] = $plan['resolution'];
                    $TEMP['!date_approved'] = Specific::DateFormat($plan['date_approved']);
                    $TEMP['!duration'] = $plan['duration'];
                    $TEMP['!credits'] = $plan['credits'];
                    $TEMP['!courses'] = $dba->query('SELECT COUNT(*) FROM curriculum WHERE plan_id = '.$plan['id'])->fetchArray();
                    $TEMP['!note_mode'] = $plan['note_mode'];
                    $TEMP['!status'] = $TEMP['#word'][$plan['status']];
                    $TEMP['!time'] = Specific::DateFormat($plan['time']);
                    $html .= Specific::Maket('more/plans/includes/plans-list');
                }
                Specific::DestroyMaket();
                $deliver['status'] = 200;
            } else {
                if(!empty($keyword)){
                    $TEMP['keyword'] = $keyword;
                    $html .= Specific::Maket('not-found/result-for');
                } else {
                    $html .= Specific::Maket('not-found/plans');
                }
            }
            $deliver['html'] = $html;
    } else if($one == 'table-plans'){
        $page = Specific::Filter($_POST['page_id']);
        if(!empty($page) && is_numeric($page) && isset($page) && $page > 0){
            $html = "";
            $query = '';
            $keyword = Specific::Filter($_POST['keyword']);
            if(!empty($keyword)){
                $query .= " WHERE name LIKE '%$keyword%' OR resolution LIKE '%$keyword%'";
            }
            $plans = $dba->query('SELECT * FROM plan'.$query.' LIMIT ? OFFSET ?', 10, $page)->fetchAll();
            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    $TEMP['!id'] = $plan['id'];
                    $TEMP['!name'] = $plan['name'];
                    $TEMP['!program'] = $dba->query('SELECT name FROM program WHERE id = '.$plan['program_id'])->fetchArray();
                    $TEMP['!resolution'] = $plan['resolution'];
                    $TEMP['!date_approved'] = Specific::DateFormat($plan['date_approved']);
                    $TEMP['!duration'] = $plan['duration'];
                    $TEMP['!credits'] = $plan['credits'];
                    $TEMP['!courses'] = $dba->query('SELECT COUNT(*) FROM curriculum WHERE plan_id = '.$plan['id'])->fetchArray();
                    $TEMP['!note_mode'] = $plan['note_mode'];
                    $TEMP['!status'] = $TEMP['#word'][$plan['status']];
                    $TEMP['!time'] = Specific::DateFormat($plan['time']);
                    $html .= Specific::Maket('more/plans/includes/plans-list');
                }
                Specific::DestroyMaket();
                $deliver['status'] = 200;
            }
            $deliver['status'] = 200;
            $deliver['html'] = $html;
        }
    } else if($one == 'this-rules'){
    	$id = Specific::Filter($_POST['id']);
    	$rules = Specific::Filter($_POST['rules']);
    	$status = Specific::Filter($_POST['status']);
    	$type = Specific::Filter($_POST['type']);
    	if(!empty($rules) && !empty($status)){
            if (preg_match_all('/{\#(.+?)->(.+?)}/i', htmlspecialchars_decode($rules), $rls)) {
                $count = 0;
                $params_uniq = array_count_values($rls[1]);
                foreach ($params_uniq as $key => $uniq) {
                    if(in_array($rls[1][$count], $TEMP['#rules'])){
                        if($uniq > 1){
                            $uniq_error = $TEMP['#word']['the_parameter'].' "'.$key.'" '.$TEMP['#word']['repeated_parameters_unique'];
                            break;
                        }
                    }
                    $count++;
                }
                for ($i=0; $i < count($TEMP['#rulen']); $i++) { 
                    $pnot = array_search($TEMP['#rulen'][$i], $rls[1]);
                    $pmax = array_search('NM', $rls[1]);
                    if($rls[2][$pnot] > $rls[2][$pmax]){
                        $max_error = $TEMP['#word']['the_maximum_grade_is'].' '.$TEMP['#word']['and_was_set_to'].' "'.$rls[1][$pmax].'", '.$TEMP['#word']['change_the_parameter'].': '.$rls[1][$pnot];
                    }
                }    
            }
            if(!isset($max_error)){
                if(!isset($uniq_error)){
                    if($type == 'add'){
                        $deliver['change'] = 'false';
                        $rule_id = $dba->query('INSERT INTO rule (user_id, rules, status, modified, `time`) VALUES ('.$TEMP['#user']['id'].', "'.$rules.'", "'.$status.'" , 0, '.time().')')->insertId();
                        if($rule_id){
                            $rule = $dba->query('SELECT id, COUNT(*) AS count FROM rule WHERE status = "enabled" AND id <> '.$rule_id)->fetchArray();
                            if($rule['count'] > 0 && $status == 'enabled'){
                                if($dba->query('UPDATE rule SET status = "disabled" WHERE id = '.$rule['id'])->returnStatus()){
                                    $deliver['change'] = 'true';
                                    $deliver['rule_id'] = $rule['id'];
                                }
                            }
                            $deliver['status'] = 200;
                        }
                    } else {
                        if(isset($id) && is_numeric($id)){
                            $deliver['change'] = 'false';
                            if($dba->query('UPDATE rule SET rules = ?, status = ? WHERE id = '.$id, $rules, $status)->returnStatus()){
                                $rule = $dba->query('SELECT id, COUNT(*) AS count FROM rule WHERE status = "enabled" AND id <> '.$id)->fetchArray();
                                if($rule['count'] > 0 && $status == 'enabled'){
                                    if($dba->query('UPDATE rule SET status = "disabled" WHERE id = '.$rule['id'])->returnStatus()){
                                        $deliver['change'] = 'true';
                                        $deliver['rule_id'] = $rule['id'];
                                    }
                                }
                                $deliver['status'] = 200;
                                $deliver['html'] = Specific::GetComposeRule($rules, true);
                            }
                        }
                    }
                } else {
                    $deliver = array(
                        'status' => 400,
                        'error' => $uniq_error
                    );
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'error' => $max_error
                );
            }
    	} else {
    		$deliver = array(
    			'status' => 400,
    			'error' => $TEMP['#word']['please_complete_information_before_sending']
    		);
    	}
    } else if($one == 'upload-rule'){
        if(!empty($_FILES['file-rule'])){
            if(!empty($_FILES['file-rule']['tmp_name'])){
                $rule_id = Specific::Filter($_GET['id']);
                if(!empty($rule_id) && is_numeric($rule_id)){
                    $file_info = array(
                        'id' => $rule_id,
                        'file' => $_FILES['file-rule']['tmp_name'],
                        'size' => $_FILES['file-rule']['size'],
                        'name' => $_FILES['file-rule']['name'],
                        'type' => $_FILES['file-rule']['type']
                    );
                    $file_data = Specific::UploadPDF($file_info);
                    if (!empty($file_data)) {
                        $file = $dba->query('SELECT file FROM rule WHERE id = '.$rule_id)->fetchArray();
                        if(!empty($file)){
                            unlink($file);
                        }
                        if ($dba->query('UPDATE rule SET file = ? WHERE id = '.$rule_id, $file_data)->returnStatus()) {
                            $deliver = array(
                                'status' => 200,
                                'file' => $file_data,
                                'url' => Specific::Url($file_data)
                            );
                        }
                    }
                }
            }
        }
    } else if($one == 'delete-frule'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            $file = $dba->query('SELECT file FROM rule WHERE id = '.$id)->fetchArray();
            if($dba->query('UPDATE rule SET file = ? WHERE id = '.$id, NULL)->returnStatus()){
                if(!empty($file)){
                    unlink($file);
                }
                $deliver['status'] = 200;
            };
        }
    } else if($one == 'delete-rule'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            if($dba->query('DELETE FROM rule WHERE id = '.$id)->returnStatus()){
            	$rule = $dba->query('SELECT id, COUNT(*) AS count FROM rule ORDER BY id DESC LIMIT 1')->fetchArray();
    			if($rule['count'] > 0){
    				$dba->query('UPDATE rule SET status = "enabled" WHERE id = '.$rule['id']);
    			}
                $deliver['status'] = 200;
            };
        }
    } else if($one == 'this-curriculum'){
        $teatrues = array(true);
        $types = array('add', 'edit');

        $type = Specific::Filter($_POST['type']);
        $id = Specific::Filter($_POST['id']);
        $courses = Specific::Filter($_POST['courses']);

        if(!empty($type) && in_array($type, $types)){
            if(!empty($courses)){
                $courses = explode(',', $courses);
                if(!empty($courses)){
                    foreach ($courses as $course) {
                        if($dba->query('SELECT COUNT(*) FROM course WHERE id = '.$course)->fetchArray() > 0){
                            $teatrues[] = true;
                        } else {
                            $teatrues[] = false;
                        }
                    }
                }
                if (!in_array(false, $teatrues)) {
                    $instrues = array();
                    if($type == 'add'){
                        for ($i=0; $i < count($courses); $i++) {
                            if($dba->query('SELECT COUNT(*) FROM curriculum WHERE course_id = '.$courses[$i])->fetchArray() == 0){
                                if($dba->query('INSERT INTO curriculum (course_id, plan_id, `time`) VALUES ('.$courses[$i].', '.$id.', '.time().')')->returnStatus()){
                                    $instrues[] = true; 
                                }
                            } else {  
                                $instrues[] = false;
                            }
                        }
                        if(!in_array(false, $instrues)){
                            $deliver['status'] = 200;
                        } else {
                            $deliver = array(
                                'status' => 400,
                                'error' => $TEMP['#word']['one_courses_already_assigned_curriculum']
                            );
                        }
                    } else {
                        $courses_all = $dba->query('SELECT course_id FROM curriculum WHERE plan_id = '.$id)->fetchAll(false);
                        $deleted = array_diff($courses_all, $courses);
                        $addf = array_diff($courses, $courses_all);
                        $adds = explode(',', implode(',', $addf));
                        if(count($addf) > 0 || count($deleted) > 0){
                            if(count($addf) > 0){
                                for ($i=0; $i < count($adds); $i++) {
                                    if($dba->query('SELECT COUNT(*) FROM curriculum WHERE course_id = '.$adds[$i])->fetchArray() == 0){
                                        if($dba->query('INSERT INTO curriculum (course_id, plan_id, `time`) VALUES ('.$adds[$i].', '.$id.', '.time().')')->returnStatus()){
                                            $instrues[] = true;
                                        }
                                    } else {  
                                        $instrues[] = false;
                                    }
                                }
                                if(!in_array(false, $instrues)){
                                    $deliver['status'] = 200;
                                } else {
                                    $deliver = array(
                                        'status' => 302,
                                        'error' => $TEMP['#word']['teacher_already_assigned']
                                    );
                                }
                            }
                            if(count($deleted) > 0){
                                if($dba->query('DELETE FROM curriculum WHERE course_id IN ('.implode(',', $deleted).')')->returnStatus()){
                                    $deliver['status'] = 200;
                                }
                            }
                        } else {
                            $deliver['status'] = 200;
                        }
                    }
                } else {
                    $deliver = array(
                        'status' => 400,
                        'errors' => $TEMP['#word']['please_enter_valid_value']
                    );
                }
            } else {
                $deliver = array(
                    'status' => 400,
                    'errors' => $TEMP['#word']['this_field_is_empty']
                );
            }
        }
    } else if($one == 'get-citems'){
        $id = Specific::Filter($_POST['id']);
        if(isset($id) && is_numeric($id)){
            $items = array();
            $courses = $dba->query('SELECT * FROM curriculum WHERE plan_id = '.$id)->fetchAll();
            foreach ($courses as $course) {
                $name = $dba->query('SELECT name FROM course WHERE id = '.$course['course_id'])->fetchArray();
                $items['courses'][] = array('id' => $course['course_id'], 'name' => $name);   
            }
            if (!empty($items)) {
                $deliver['status'] = 200;
                $deliver['items'] = $items;
            }
        }
    } else if($one == 'delete-curriculum'){
        $id = Specific::Filter($_POST['id']);
        if (isset($id) && is_numeric($id)) {
            if($dba->query('DELETE FROM curriculum WHERE plan_id = '.$id)->returnStatus()){
                $deliver['status'] = 200;
            };
        }
    }
}
?>