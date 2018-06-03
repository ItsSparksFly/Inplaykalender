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
}

function inplaykalender_deactivate() {
    global $mybb, $db;
}
