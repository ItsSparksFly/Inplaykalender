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
// get days as array
$all_days = array($lang->inplaykalender_sonntag, $lang->inplaykalender_montag, $lang->inplaykalender_dienstag, $lang->inplaykalender_mittwoch, $lang->inplaykalender_donnerstag, $lang->inplaykalender_freitag, $lang->inplaykalender_samstag);

// landing page
if(empty($action)) {
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
            $day_popup = "";
            $date = strtotime("{$i}-{$months_en[$id]}-{$year}");
            $title = $i;
            $event = "";
            
            // get inplay scenes
            $szenen = false;
            $query = $db->query("SELECT * FROM ".TABLE_PREFIX."ipt_scenes WHERE date = '$date'");
            if(mysqli_num_rows($query) > 0) {
                    $threadlist = "";
                    while($szenenliste = $db->fetch_array($query)) {
                        $thread = get_thread($szenenliste['tid']);
                        if($thread) {
                            $szenen = true;
                            $threadlist .= "&bull; <a href=\"showthread.php?tid={$thread['tid']}\" target=\"_blank\">{$thread['subject']}</a><br />{$szenenliste['shortdesc']}<br />";
                        } else {  }
                } 
            } else { $threadlist = ""; }
            
            // get birthdays
            $birthday = false;
            $fulldate = date("j-n", $date);                
            $query = $db->query("SELECT * FROM ".TABLE_PREFIX."users WHERE birthday LIKE '$fulldate-%'");
            if(mysqli_num_rows($query) > 0) {
                $birthday = true;
            }
            
            // get timeline events
            if($db->table_exists("timeline")) {
                $timeline = false;
                $query = $db->query("SELECT * FROM ".TABLE_PREFIX."timeline WHERE date = '$date'");
                if(mysqli_num_rows($query) > 0) {
                    $timeline = true;
                }
            }
            
            // get plots
            if($db->table_exists("plots")) {
                $plots = false;
                $query = $db->query("SELECT * FROM ".TABLE_PREFIX."plots");
                $plotlist = "";
                while($plot_list = $db->fetch_array($query)) {
                    if($plot_list['startdate'] <= $date && $plot_list['enddate'] >= $date) {
                        $plots = true;
                        $plotlist .= "&bull; <a href=\"plottracker.php?action=view&plid={$plot_list['plid']}\" target=\"_blank\">{$plot_list['name']}</a>";
                    } else { $plotlist = ""; }
                }
            }
            
            $list_of_events = array("$lang->inplaykalender_class_scenes" => $szenen, "$lang->inplaykalender_class_birthday" => $birthday, "$lang->inplaykalender_class_timeline" => $timeline, "$lang->inplaykalender_class_event" => $plots);

            // if there's an event on this day, create popup
            if(in_array(true, $list_of_events)) {
                foreach($list_of_events as $class => $single_event) {
                    if($single_event) {
                        $event .= $class;
                    }
                }
                $week_day_num = date("w", $date);
                $week_day = $all_days[$week_day_num];
                $fulldate = date("d.m.Y", $date);
                $title = "<a href=\"#{$date}\"><strong>{$i}</strong></a>";
                eval("\$day_popup = \"".$templates->get("inplaykalender_day_bit_popup")."\";");
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

    // set template
    eval("\$page = \"".$templates->get("inplaykalender")."\";");
    output_page($page);
}

if($action == "add") {
    
    // format date dropdowns
    for($i=1 ; $i <=31; $i++) {
        $day_bit .= "<option value=\"{$i}\">{$i}</option>";
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

    // format unix timestamp
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