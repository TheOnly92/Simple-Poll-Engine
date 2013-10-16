<?php

define('POLL_SCRIPT_ROOT', dirname(__FILE__));

require_once(POLL_SCRIPT_ROOT.'/config.php');
require_once(POLL_SCRIPT_ROOT.'/interfaces/database.php');
require_once(POLL_SCRIPT_ROOT.'/domain.php');
require_once(POLL_SCRIPT_ROOT.'/usecases.php');
require_once(POLL_SCRIPT_ROOT.'/interfaces/repositories.php');
require_once(POLL_SCRIPT_ROOT.'/controllers.php');

switch ($config['database']['type']) {
    case 'mysql':
        require_once(POLL_SCRIPT_ROOT.'/infrastructure/mysqlihandler.php');
        $pollDb = new MySQLiHandler($config['database']['server'], $config['database']['username'], $config['database']['password'], $config['database']['dbname']);
        break;
}

// This function checks if tables are created, and creates them if not created
function checkInstalled(DB $db, $config) {
    $db->Exec("CREATE TABLE IF NOT EXISTS `{$config['database']['prefix']}polls` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`question` text NOT NULL,`close_date` int(11) unsigned NOT NULL,`vote_repeating` smallint(5) unsigned NOT NULL,`cookie_expire` int(10) unsigned NOT NULL,`show_hide_results` smallint(5) unsigned NOT NULL,`order_results` tinyint(3) unsigned NOT NULL,`created` int(11) unsigned NOT NULL,`randomize_order` tinyint(1) unsigned NOT NULL,`closed` tinyint(1) unsigned NOT NULL,PRIMARY KEY (`id`))");
    $db->Exec("CREATE TABLE IF NOT EXISTS `{$config['database']['prefix']}poll_answers` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`poll_id` int(10) unsigned NOT NULL,`answer` text NOT NULL,`sort_order` int(10) unsigned NOT NULL,`image` int(11) unsigned NOT NULL,PRIMARY KEY (`id`))");
    $db->Exec("CREATE TABLE IF NOT EXISTS `{$config['database']['prefix']}poll_votes` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`poll_id` int(10) unsigned NOT NULL,`identifier` varchar(255) NOT NULL DEFAULT '',`voted_date` int(11) unsigned NOT NULL,`voted_ip` int(11) unsigned NOT NULL,`answer_id` int(11) unsigned NOT NULL,PRIMARY KEY (`id`))");
    $db->Exec("CREATE TABLE IF NOT EXISTS `{$config['database']['prefix']}poll_images` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT,`upload_path` varchar(100) NOT NULL DEFAULT '',PRIMARY KEY (`id`))");
}
