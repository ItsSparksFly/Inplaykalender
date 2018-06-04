<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}

// link function to file hook // only uncomment if there is a function to link to
// $plugins->add_hook("hook", "function");

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

    if(!$db->table_exists("events")) {
        $db->query("CREATE TABLE `mybb_events` (
            `eid` int(11) NOT NULL AUTO_INCREMENT,
            `uid` int(11) NOT NULL,
            `name` text NOT NULL,
            `description` text NOT NULL,
            `starttime` varchar(20) NOT NULL,
            `endtime` varchar(20) NOT NULL,
            `accepted` int(1) NOT NULL,
            PRIMARY KEY (`eid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
    }

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
    );

    foreach($setting_array as $name => $setting) {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

    rebuild_settings();
}

function inplaykalender_is_installed() {
    global $db;

    if($db->table_exists("events")) {
        return true;
    }
    return false;
}

function inplaykalender_uninstall() {
    global $db;  

    if($db->table_exists("events")) {
        $db->query("DROP TABLE `mybb_events`");
    }

    $db->delete_query('settings', "name LIKE 'inplaykalender%'");
    $db->delete_query('settinggroups', "name = 'inplaykalender'");
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
        
        .szenengeburtstagtimelineevent { background: linear-gradient(to left top, #EBD39D 25%, #C8B6CC 25%, #C8B6CC 50%, #BADBAF 50%, #BADBAF 75%, #ABD9D8 75%); }',
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
}
