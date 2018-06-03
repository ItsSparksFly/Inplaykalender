<?php
// set some useful constants that the core may require or use
define("IN_MYBB", 1);
define('THIS_SCRIPT', 'inplaykalender.php');

// including global.php gives us access to a bunch of MyBB functions and variables
require_once "./global.php";

// load language-settings
$lang->load('inplaykalender');

// add a breadcrumb
add_breadcrumb($lang->inplaykalender, "inplaykalender.php");

$action = $mybb->input['action'];

// get navigation
eval("\$menu = \"".$templates->get("inplaykalender_nav")."\";");

// get year
$year = $mybb->input['y'];
// no year given? chose year set by admin! 
if(empty($year)) {
    $year = $mybb->settings['inplaykalender_year'];
}
// get month, otherwise the whole year will be displayed
$month = $mybb->input['m'];
// get months as array
$months = array(1 => $lang->inplaykalender_januar, $lang->inplaykalender_februar, $lang->inplaykalender_maerz, $lang->inplaykalender_april, $lang->inplaykalender_mai, $lang->inplaykalender_juni, $lang->inplaykalender_juli, $lang->inplaykalender_august, $lang->inplaykalender_september, $lang->inplaykalender_oktober, $lang->inplaykalender_november, $lang->inplaykalender_dezember);
$months_en = array(1 => $lang->inplaykalender_januar_en, $lang->inplaykalender_februar_en, $lang->inplaykalender_maerz_en, $lang->inplaykalender_april_en, $lang->inplaykalender_mai_en, $lang->inplaykalender_juni_en, $lang->inplaykalender_juli_en, $lang->inplaykalender_august_en, $lang->inplaykalender_september_en, $lang->inplaykalender_oktober_en, $lang->inplaykalender_november_en, $lang->inplaykalender_dezember_en);
// landing page
if(empty($action)) {
    if(empty($month)) {
        foreach($months as $id => $month)  {
            $days = 0;
            $day_bit = "";
            // get days in month of the selected year
            $number_days = cal_days_in_month(CAL_GREGORIAN, $id, $year);
            
            // get first day of month
            $time_str = "01-{$months_en[$id]}-{$year}"; // pattern: d-F-Y
            $first_day = date('w', strtotime($time_str));
            
            //get last day of month
            $time_str = "{$number_days}-{$months_en[$id]}-{$year}"; // pattern: d-F-Y
            $last_day = date('w', strtotime($time_str));
            
            // get empty table datas (e.g. month starts on thursday)
            for($j = 0; $j < $first_day; $j++) {
                eval("\$day_bit .= \"".$templates->get("inplaykalender_no_day_bit")."\";");
                $days++;
                if($days == 7) {
                    $day_bit .= "</tr><tr>";
                    $days = 0;
                }
            }
            // get month's days table datas            
            for($i = 1; $i <= $number_days; $i++) {
                $date = strtotime("{$i}-{$months_en[$id]}-{$year}");
                $title = $i;
                $event = "";
                
                // get inplay scenes
                $szenen = false;
                $query = $db->query("SELECT * FROM ".TABLE_PREFIX."threads WHERE ipdate = '$date'");
                if(mysqli_num_rows($query) > 0) {
                    $title = "<a href=\"#{$date}\"><strong>{$i}</strong></a>";
                    $szenen = true;
                }
                
                // get birthdays
                $birthday = false;
                $fulldate = date("j.m.", $date);                
                $query = $db->query("SELECT * FROM ".TABLE_PREFIX."characters WHERE birthday LIKE '$fulldate%'");
                if(mysqli_num_rows($query) > 0) {
                    $title = "<a href=\"#{$date}\"><strong>{$i}</strong></a>";
                    $birthday = true;
                }
                
                // get timeline events
                $timeline = false;
                $query = $db->query("SELECT * FROM ".TABLE_PREFIX."timeline WHERE date = '$date'");
                if(mysqli_num_rows($query) > 0) {
                    $title = "<a href=\"#{$date}\"><strong>{$i}</strong></a>";
                    $timeline = true;
                }
                
                // get calendar events
                $events = false;
                $query = $db->query("SELECT * FROM ".TABLE_PREFIX."events");
                while($event_list = $db->fetch_array($query)) {
                    if($event_list['starttime'] <= $date && $event_list['endtime'] >= $date) {
                        $title = "<a href=\"#{$date}\"><strong>{$i}</strong></a>";
                        $events = true;
                    }
                }
                
                if($szenen) {
                    $event = "szenen";
                }
                if($birthday) {
                    $event = "geburtstag";
                }
                if($timeline) {
                    $event = "timeline";
                }
                if($events) {
                    $event = "event";
                }
                if($szenen && $birthday) {
                    $event = "szenengeburtstag";
                }
                if($szenen && $timeline) {
                    $event = "szenentimeline";
                }
                if($szenen && $events) {
                    $event = "szenenevent";
                }
                if($birthday && $timeline) {
                    $event = "geburtstagtimeline";
                }
                if($birthday && $events) {
                    $event = "geburtstagevent";
                }
                if($timeline && $events) {
                    $event = "timelineevent";
                }
                if($szenen && $birthday && $timeline) {
                    $event = "szenengeburtstagtimeline";
                }
                if($szenen && $birthday && $events) {
                    $event = "szenengeburtstagevent";
                }
                if($szenen && $timeline && $events) {
                    $event = "szenentimelineevent";
                }
                if($birthday && $timeline && $events) {
                    $event = "geburtstagtimelineevent";
                }
                if($szenen && $birthday && $timeline && $events) {
                    $event = "szenengeburtstagtimelineevent";
                }
                eval("\$day_bit .= \"".$templates->get("inplaykalender_day_bit")."\";");
                $days++;
                if($days == 7) {
                    $day_bit .= "</tr><tr>";
                    $days = 0;
                }
            }
            
            // get empty table datas (e.g. month ends on saturday)
            for($k = $last_day + 1; $k <= 6; $k++) {
                eval("\$day_bit .= \"".$templates->get("inplaykalender_no_day_bit")."\";");
                $days++;
                if($days == 7) {
                    $day_bit .= "</tr><tr>";
                    $days = 0;
                }
            }
            eval("\$month_bit .= \"".$templates->get("inplaykalender_month_bit")."\";");
        }
    }
    // set template
    eval("\$page = \"".$templates->get("inplaykalender")."\";");
    output_page($page);
}
if($action == "add") {
    
    // format date dropdowns
    for($i=1 ; $i <=31; $i++) {
        $day_bit .= "<option value=\"{$i}\">{$i}</option>";
    }
    
    $query = $db->query("SELECT uid, username FROM ".TABLE_PREFIX."users
  		ORDER BY username ASC");
    while($user = $db->fetch_array($query)) {
        $user_bit .= "<option value=\"{$user['uid']}\">{$user['username']}</option>";
    }
    
    foreach($months as $id => $month) {
        $month_bit .= "<option value=\"{$id}\">{$month}</option>";
    }    
    
    for($i=2016; $i <=2017; $i++) {
        $year_bit .= "<option value=\"{$i}\">{$i}</option>";
    }
    
    // set template
    eval("\$page = \"".$templates->get("inplaykalender_add")."\";");
    output_page($page);
}

if($action == "do_add") {
    $starttime = strtotime($mybb->get_input('year_start')."-".$mybb->get_input('month_start')."-".$mybb->get_input('day_start'));
    $endtime = strtotime($mybb->get_input('year_end')."-".$mybb->get_input('month_end')."-".$mybb->get_input('day_end'));
    
    // data to insert into database
    $new_record = array(
        "name" => $db->escape_string($mybb->get_input('name')),
        "starttime" => $starttime,
        "endtime" => $endtime,
        "description" => $db->escape_string($mybb->get_input('desc')),
        "uid" => (int)$mybb->user['uid'],
        "accepted" => (int)"1"
    );
    
    // insert entry
    $db->insert_query("events", $new_record);
    
    // stuff is done, redirect to landing page
    redirect("inplaykalender.php", "{$lang->inplaykalender_added}");
}
?>