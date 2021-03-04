<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook("global_intermediate", "inplaykalender_global");
$plugins->add_hook("newthread_start", "inplaykalender_newthread");
$plugins->add_hook("newthread_do_newthread_end", "inplaykalender_do_newthread");
$plugins->add_hook("newthread_do_newthread_end", "inplaykalender_do_newthread");
$plugins->add_hook("editpost_start", "inplaykalender_editpost_start");
$plugins->add_hook("editpost_do_editpost_end", "inplaykalender_do_editpost");

function inplaykalender_info(){
    return array(
        "name"			=> "Inplaykalender",
        "description"	=> "Fügt dem Forum einen Kalender hinzu, der extra auf das Ingame eines RPGs abgestimmt ist.",
        "website"		=> "http://github.com/user/its-sparks-fly",
        "author"		=> "sparks fly",
        "authorsite"	=> "http://github.com/user/its-sparks-fly",
        "version"		=> "1.0",
        "compatibility" => "*"
    );
}

function inplaykalender_install() {
    global $mybb, $db;

    $setting_group = array(
        'name' => 'inplaykalender',
        'title' => 'Inplaykalender Einstellungen',
        'description' => 'Fügt dem Forum einen Kalender hinzu, der extra auf das Ingame eines RPGs abgestimmt ist.',
        'disporder' => 5, // The order your setting group will display
        'isdefault' => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
    // A text setting
    'inplaykalender_year' => array(
        'title' => 'Spieljahr',
        'description' => 'In welchem Jahr spielt dein RPG?',
        'optionscode' => 'text',
        'value' => '2017', // Default
        'disporder' => 1
    ),
    'inplaykalender_months' => array(
        'title' => 'Spieljahr',
        'description' => 'In welchen Monaten spielt dein RPG? Monate mit "," trennen.',
        'optionscode' => 'text',
        'value' => 'April,Mai,Juni', // Default
        'disporder' => 1
    ),
    'inplaykalender_text' => array(
        'title' => 'Spieljahr',
        'description' => 'Text für den aktuellen Inplayzeitraum',
        'optionscode' => 'textarea',
        'value' => '', // Default
        'disporder' => 1
    ),
    );

    foreach($setting_array as $name => $setting) {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

    rebuild_settings();
    
    $header_inplaykalender = [
        'title'        => 'header_inplaykalender',
        'template'    => $db->escape_string('<div id="container">
        <table cellspacing="3" cellpadding="3" class="tborder trow2">
            <tr>
                <td colspan="2">
                    <div class="header-top"><i class="fa fa-calendar" aria-hidden="true"></i> Inplay-Info &raquo; Spieljahr: <strong>{$mybb->settings[\'inplaykalender_year\']}</strong> &raquo; <a href="inplaykalender.php" target="blank">[ <i class="fa fa-arrow-right" aria-hidden="true"></i> Zum <em>Kalender</em> ]</a></div>
                </td>
            </tr>
            <tr>
                <td width="23%" valign="top">
                    <div class="header-box" style="height: 175px;">
    <div class="header-text" style="height: 175px;">{$mybb->settings[\'inplaykalender_text\']}</div></div></td>
                <td>{$header_inplaykalender_bit}</td>
            </tr>
        </table>
 </div>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $header_inplaykalender);

    $header_inplaykalender_bit = [
        'title'        => 'header_inplaykalender_bit',
        'template'    => $db->escape_string('<div style="float: left; margin: 3px; margin-top: 0px; width: {$width}%; height: 175px;">
        <table cellspacing="1" cellpadding="1" class="tborder" id="mini-kalender">
            <tr>
                <td colspan="7" class="tcat" align="center">{$month} {$year}</td>
            </tr>
            <tr align="center" style="font-weight: bold;">
                <td class="thead">Sun</td>
                <td class="thead">Mon</td>
                <td class="thead">Tue</td>
                <td class="thead">Wed</td>
                <td class="thead">Thu</td>
                <td class="thead">Fri</td>
                <td class="thead">Sat</td>
            </tr>
            <tr>
            </tr>
                <tr>
                    {$day_calendar_bit}
                </tr>
        </table>
    </div>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $header_inplaykalender_bit);

    $inplaykalender = [
        'title'        => 'inplaykalender',
        'template'    => $db->escape_string('<html>
        <head>
        <title>{$mybb->settings[\'bbname\']} - {$lang->inplaykalender}</title>
        {$headerinclude}
        </head>
        <body>
        {$header}
        <table width="100%" border="0" align="center">
        <tr>
        <td width="20%" valign="top">
        {$menu}
        </td>
        <td valign="top">
        <table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr>
        <td class="thead"><strong>{$lang->inplaykalender}</strong></td>
        </tr>
        <tr>
        <td class="trow2" style="padding: 10px; text-align: justify;">
        <div style="width: 95%; margin: auto; padding: 8px;  font-size: 12px; line-height: 1.5em;">
            <table cellspacing="5" cellpadding="5" style="margin: 10px auto; font-size: 8px; text-align: center; text-transform: uppercase;">
                <tr>
                    <td class="szenen"><strong>Inplay-Szenen</strong></td>
                    <td class="event"><strong>Events</strong></td>
					<td class="timeline"><strong>Plots</strong></td>
					<td class="geburtstag"><strong>Eigene Termine</strong></td>
                </tr>
            </table>
            {$month_bit}
        </div>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        {$footer}
        </body>
        </html>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $inplaykalender);

    $inplaykalender_nav = [
        'title'        => 'inplaykalender_nav',
        'template'    => $db->escape_string('<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder con-nav">
        <tbody>
            <tr>
                <td class="thead"><strong>Navigation</strong></td>
            </tr>
            <tr>
                <td class="trow2 smalltext"><i class="fa fa-calendar" aria-hidden="true"></i> <a href="inplaykalender.php">Kalender</a></td>
            </tr>
        </tbody>
</table>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $inplaykalender_nav);

    $inplaykalender_no_day_bit = [
        'title'        => 'inplaykalender_no_day_bit',
        'template'    => $db->escape_string('<td class="inplaykalender_tag">
	
        </td>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $inplaykalender_no_day_bit);

    $inplaykalender_day_bit = [
        'title'        => 'inplaykalender_day_bit',
        'template'    => $db->escape_string('<td class="inplaykalender_tag trow2 {$event}">
        {$title}
	    {$day_popup}
  </td>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $inplaykalender_day_bit);

    #TODO: Template korrigieren
    $inplaykalender_day_bit_popup = [
        'title'        => 'inplaykalender_day_bit_popup',
        'template'    => $db->escape_string('<div id="{$date}" class="calpop">
        <div class="pop">
		<div class="category">{$week_day} - {$fulldate}</div>
			<div class="thead">Szenen</div>
			<div style="margin: 5px 40px;">
			{$threadlist}
			</div>
			<br /><br /><div class="thead">Plots</div>
			<div style="margin: 5px 40px;">
				{$plotlist}</div>
			<br /><br /><div class="thead">Events</div>
			<div style="margin: 5px 40px;">
				{$eventlist}</div>
        </div>
        <a href="#closepop" class="closepop"></a>
</div>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $inplaykalender_day_bit_popup);

    $inplaykalender_month_bit = [
        'title'        => 'inplaykalender_month_bit',
        'template'    => $db->escape_string('<div style="float: left; margin: 3px; width: 49%; height: 230px;">
        <table cellspacing="1" cellpadding="3" class="tborder">
            <tr>
                <td colspan="7" class="thead" align="center">{$month} {$year}</td>
            </tr>
            <tr align="center" style="font-weight: bold;">
                <td class="tcat">Sun</td>
                <td class="tcat">Mon</td>
                <td class="tcat">Tue</td>
                <td class="tcat">Wed</td>
                <td class="tcat">Thu</td>
                <td class="tcat">Fri</td>
                <td class="tcat">Sat</td>
            </tr>
            <tr>
            </tr>
                <tr>
                    {$day_bit}
                </tr>
        </table>
    </div>'),
            'sid'        => '-1',
            'version'    => '',
            'dateline'    => TIME_NOW
    ];

    $db->insert_query("templates", $inplaykalender_month_bit);

}

function inplaykalender_is_installed() {
    global $db;

    #TODO: find method
    if($db->table_exists("events")) {
        return true;
    }
    
    return false;
}

function inplaykalender_uninstall() {
    global $db;  

    $db->delete_query('settings', "name LIKE 'inplaykalender%'");
    $db->delete_query('settinggroups', "name = 'inplaykalender'");
    rebuild_settings();

    #TODO: delete templates
}

function inplaykalender_activate() {
    global $mybb, $db;

        // CSS  
    $css = array(
        'name' => 'inplaykalender.css',
        'tid' => 1,
        "stylesheet" => '       .inplaykalender_tag { text-align: center; font-size: 8px; letter-spacing: 1px; } 
        .szenen { background-color: #C8B6CC; } 
        .szenen strong { color: #543D59 !important; } 
        .geburtstag { background-color: #EBD39D; } 
        .geburtstag strong { color: #6E644E !important; } 
        .timeline { background-color: #BADBAF;}
        .timeline strong { color: #3D4F37 !important; }
        .event { background-color: #ABD9D8; }
        .event strong { color: #4D6E6D !important; }
        
        .szenentimeline { background: linear-gradient(to left top, #EBD39D 50%, #BADBAF 50%); }
        .szenentimeline strong { color: #3D4F37 !important; }
        .szenengeburtstag { background: linear-gradient(to left top, #EBD39D 50%, #C8B6CC 50%); }
        .szenenevent { background: linear-gradient(to left top, #EBD39D 50%, #ABD9D8 50%); }
        .szenenevent strong { }
        .szenengeburtstag strong { color: #543D59 !important; }
        .geburtstagtimeline { background: linear-gradient(to left top, #C8B6CC 50%, #BADBAF 50%); }
        .geburtstagtimeline strong { color: #6E644E !important;  }
        .geburtstagevent { background: linear-gradient(to left top, #C8B6CC 50%, #ABD9D8 50%); }
        .geburtstagevent strong { }
        .timelineevent { background: linear-gradient(to left top, #BADBAF 50%, #ABD9D8 50%);  }
        .timelineevent strong { }
        
        .szenengeburtstagtimeline { background: linear-gradient(to left top, #EBD39D 33%, #C8B6CC 33%, #C8B6CC 66%, #BADBAF 66%); }
        .szenengeburtstagtimeline strong { color: #543D59 !important; }
        .szenengeburtstagevent { background: linear-gradient(to left top, #EBD39D 33%, #C8B6CC 33%, #C8B6CC 66%, #ABD9D8 66%); }
        .szenengeburtstagevent strong { }
        .szenentimelineevent {  background: linear-gradient(to left top, #EBD39D 33%, #BADBAF 33%, #BADBAF 66%, #ABD9D8 66%); }
        .szenentimelineevent strong { }
        .geburtstagtimelineevent { background: linear-gradient(to left top, #C8B6CC 33%, #BADBAF 33%, #BADBAF 66%, #ABD9D8 66%); }
        .geburtstagtimelineevent strong { }
        
        .szenengeburtstagtimelineevent { background: linear-gradient(to left top, #EBD39D 25%, #C8B6CC 25%, #C8B6CC 50%, #BADBAF 50%, #BADBAF 75%, #ABD9D8 75%); }

        #mini-kalender { font-size: 7px; }
        #mini-kalender td { padding: 5px; }
        .calpop { position: fixed; top: 0; right: 0; bottom: 0; left: 0; background: rgba(0,0,0,.5); z-index: 1000; opacity:0; -webkit-transition: .5s ease-in-out; -moz-transition: .5s ease-in-out; transition: .5s ease-in-out; pointer-events: none; } 


        .calpop:target { opacity:1; pointer-events: auto; }

        .calpop > .pop { text-align: justify; background: rgba(255,255,255,8); width: 800px; position: relative; margin: 5% auto; padding: 10px; z-index: 1002; font-size: 11px; }

        .closepop { position: absolute; right: -5px; top:-5px; width: 100%; height: 100%; z-index: 999; }

        .inplaykalender-eventlist { max-height: 50px; overflow: auto; padding-right: 5px;}',
        'cachefile' => $db->escape_string(str_replace('/', '', inplaykalender.css)),
        'lastmodified' => time()
    );

    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";

    $sid = $db->insert_query("themestylesheets", $css);
    $db->update_query("themestylesheets", array("cachefile" => "css.php?stylesheet=".$sid), "sid = '".$sid."'", 1);

    $tids = $db->simple_select("themes", "tid");
    while($theme = $db->fetch_array($tids)) {
        update_theme_stylesheet_list($theme['tid']);
    }

    include MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('<navigation>')."#i", '{$header_inplaykalender}<br /><navigation>');
}

function inplaykalender_deactivate() {
    global $mybb, $db;

    // drop css
    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    $db->delete_query("themestylesheets", "name = 'inplaykalender.css'");
    $query = $db->simple_select("themes", "tid");
    while($theme = $db->fetch_array($query)) {
        update_theme_stylesheet_list($theme['tid']);
    }

    include MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('{$header_inplaykalender}<br />')."#i", '', 0);
}

function inplaykalender_global() {
    global $lang, $mybb, $db, $templates, $theme, $day_calendar_bit, $header_inplaykalender_bit, $header_inplaykalender;

    $lang->load('inplaykalender');

    // get inplay months
    $inplay_months = explode(",", $mybb->settings['inplaykalender_months']);
    $months_count = count($inplay_months);

    // set up months array
    $months = array(1 => $lang->inplaykalender_januar, $lang->inplaykalender_februar, $lang->inplaykalender_maerz, $lang->inplaykalender_april, $lang->inplaykalender_mai, $lang->inplaykalender_juni, $lang->inplaykalender_juli, $lang->inplaykalender_august, $lang->inplaykalender_september, $lang->inplaykalender_oktober, $lang->inplaykalender_november, $lang->inplaykalender_dezember);
    $months_en = array(1 => $lang->inplaykalender_januar_en, $lang->inplaykalender_februar_en, $lang->inplaykalender_maerz_en, $lang->inplaykalender_april_en, $lang->inplaykalender_mai_en, $lang->inplaykalender_juni_en, $lang->inplaykalender_juli_en, $lang->inplaykalender_august_en, $lang->inplaykalender_september_en, $lang->inplaykalender_oktober_en, $lang->inplaykalender_november_en, $lang->inplaykalender_dezember_en);

    foreach($inplay_months as $month)
    {
        $key = array_search($month, $months);
        $month_en = $months_en[$key];
        $day_calendar_bit = "";
        $event = "";

        // get days in month
        $number_days = cal_days_in_month(CAL_GREGORIAN, $key, $mybb->settings['inplaykalender_year']);

        // get first day of month
        $time_str = "01-{$months_en[$key]}-{$mybb->settings['inplaykalender_year']}"; // pattern: d-F-Y
        $first_day = date('w', strtotime($time_str));
        
        //get last day of month
        $time_str = "{$number_days}-{$months_en[$key]}-{$mybb->settings['inplaykalender_year']}"; // pattern: d-F-Y
        $last_day = date('w', strtotime($time_str));
        
        // get empty table datas (e.g. month starts on thursday)
        for($j = 0; $j < $first_day; $j++) {
            eval("\$day_calendar_bit .= \"".$templates->get("inplaykalender_no_day_bit")."\";");
            $days++;
            if($days == 7) {
                $day_calendar_bit .= "</tr><tr>";
                $days = 0;
            }
        }

        // get month's days table datas            
        for($i = 1; $i <= $number_days; $i++) {
            $date = strtotime("{$i}-{$months_en[$key]}-{$mybb->settings['inplaykalender_year']}");
            $title = $i;
            $event = "";
            
            // get inplay scenes
            $szenen = false;
            $query = $db->query("SELECT * FROM ".TABLE_PREFIX."threads WHERE ipdate = '$date'");
            if(mysqli_num_rows($query) > 0) {
                $szenen = true;
            }
            
            // get birthdays
            $birthday = false;
            $fulldate = date("j-n", $date);                
            $query = $db->query("SELECT * FROM ".TABLE_PREFIX."users WHERE birthday LIKE '$fulldate-%'");
            if(mysqli_num_rows($query) > 0) {
                $birthday = true;
            }

			$birthdayusers = "";
			while($user = $db->fetch_array($query)) {
				$profilelink = build_profile_link($user['username'], $user['uid']);
				$birthdayusers .= "{$profilelink} <br />";
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
            
            // set css class and format day link 
            $list_of_events = array("$lang->inplaykalender_class_scenes" => $szenen, "$lang->inplaykalender_class_birthday" => $birthday, "$lang->inplaykalender_class_timeline" => $timeline, "$lang->inplaykalender_class_event" => $plots);
            foreach($list_of_events as $class => $single_event) {
                if($single_event) {
                    $event .= $class;
                    $title = "<a href=\"inplaykalender.php#{$date}\" target=\"blank\"><strong>{$i}</strong></a>";
                }
            }

            // get day template
            eval("\$day_calendar_bit .= \"".$templates->get("inplaykalender_day_bit")."\";");
            $days++;
            // check for full week
            if($days == 7) {
                $day_calendar_bit .= "</tr><tr>";
                $days = 0;
            }
        }
        
        // get empty table datas (e.g. month ends on saturday)
        for($k = $last_day + 1; $k <= 6; $k++) {
            eval("\$day_calendar_bit .= \"".$templates->get("inplaykalender_no_day_bit")."\";");
            $days++;
            if($days == 7) {
                $day_calendar_bit .= "</tr><tr>";
                $days = 0;
            }
        }
        // get table width & max 3 months per row
        $width = 100 / $months_count;
        $width = $width - 1;
        if($width > 32) {
            $width = 32;
        }
        eval("\$header_inplaykalender_bit .= \"".$templates->get("header_inplaykalender_bit")."\";");
    }
    if($mybb->usergroup['cancp'] == "1") {
        eval("\$header_inplaykalender = \"".$templates->get("header_inplaykalender")."\";");
    }
}