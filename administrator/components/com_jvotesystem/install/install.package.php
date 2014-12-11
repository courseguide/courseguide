<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5
 * @projectsite www.joomess.de/projekte/18
 * @author Johannes Meßmer
 * @copyright (C) 2010- Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'classes'.DS.'update.class.php';
$updater 	=& VBUpdate::getInstance();

$status = new JObject();
$status->modules = array();
$status->plugins = array();
$status->updates = array();
$status->errors = array();

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * INSTALL JOOMESS LIBRARY - REQUIRED FOR INSTALLATION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

jimport( 'joomla.installer.installer' );
jimport( 'joomla.filesystem.file' );
$db 	= & JFactory::getDBO();
$src 	= $this->parent->getPath('source');

$result = $updater->installExtension( 'joomessLibrary', $src.'/plugins/joomessLibrary', true, false);
$status->plugins[] = array('name'=>'System - joomessLibrary','group'=>'system', 'success'=> $result);

$result = $updater->installExtension( 'mod_joomessLibrary_status', $src.'/modules/joomessLibrary_status', false, false, 'status');
$status->modules[] = array('name'=>'Module - joomessLibrary - Status', 'client' => 'admin', 'success'=> $result);
 
 /***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * Load jVSConnect
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'connect.php'; 
$jvs 		=& jVSConnect::getInstance();
$jvs->forceLoad();
$lib		=& joomessLibrary::getInstance();

//Clean the cache
$lib->cleanAll();
JFactory::getCache()->clean('jVoteSystem');
JFactory::getCache()->clean('jVoteSystem - Lists');

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * BEGIN INSTALLATION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

$jvs_success = '<div style="float:left;background:#6f6;color:#000;font-size:12px;height:14px;line-height:16px;width:14px;text-align:center;border-radius:8px;border:1px solid #000;font-weight:bold;margin-right:3px">&#10004;</div>';
$jvs_error = '<div style="float:left;background:#f66;color:#000;font-size:12px;height:14px;line-height:16px;width:14px;text-align:center;border-radius:8px;border:1px solid #000;font-weight:bold;margin-right:3px">&#10005;</div>';

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * INSTALLING MISSING DATABASE TABLES
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
 
$sql = "SHOW TABLES LIKE '#__jvotesystem_categories' ";
$db->setQuery($sql);
$tableCats = $db->loadObject();

if(!$tableCats) {
	$sql = "CREATE TABLE IF NOT EXISTS `#__jvotesystem_categories` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `parent_id` int(11) NOT NULL DEFAULT '0',
			  `order` int(11) NOT NULL,
			  `title` text NOT NULL,
			  `alias` varchar(50) NOT NULL,
			  `description` text NOT NULL,
			  `accesslevel` int(11) NOT NULL DEFAULT '1',
			  `published` int(1) NOT NULL,
			  `params` text NOT NULL,
			  `autor_id` int(11) NOT NULL,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARACTER SET utf8;";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
}

$sql = "SHOW TABLES LIKE '#__jvotesystem_sessions' ";
$db->setQuery($sql);
$tableSessions = $db->loadObject();

if(!$tableSessions) {
	$sql = "CREATE TABLE IF NOT EXISTS `#__jvotesystem_sessions` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) NOT NULL,
			  `cookie` varchar(32) NOT NULL,
			  `rights` int(1) NOT NULL DEFAULT '0',
			  `lastVisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `jsession_id` varchar(200) NOT NULL,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARACTER SET utf8;";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
}

$sql = "SHOW TABLES LIKE '#__jvotesystem_apikeys' ";
$db->setQuery($sql);
$tableSessions = $db->loadObject();

if(!$tableSessions) {
	$sql = "CREATE TABLE IF NOT EXISTS `#__jvotesystem_apikeys` (
			  `key` varchar(72) NOT NULL,
			  `params` text NOT NULL,
			  `count` int(11) NOT NULL,
			  `total_count` int(11) NOT NULL,
			  `last_start` datetime NOT NULL,
			  `last_access` datetime NOT NULL,
			  PRIMARY KEY (`key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
}

$sql = "SHOW TABLES LIKE '#__jvotesystem_email_tasks' ";
$db->setQuery($sql);
$tableSessions = $db->loadObject();

if(!$tableSessions) {
	$sql = "CREATE TABLE IF NOT EXISTS `#__jvotesystem_email_tasks` (
			  `hash` varchar(72) NOT NULL,
			  `params` text NOT NULL,
			  `uid` int(11) NOT NULL,
			  `created` datetime NOT NULL,
			  `active` int(1) NOT NULL DEFAULT '1',
			  PRIMARY KEY (`hash`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
}

$sql = "SHOW TABLES LIKE '#__jvotesystem_logs' ";
$db->setQuery($sql);
$tableSessions = $db->loadObject();

if(!$tableSessions) {
	$sql = "CREATE TABLE IF NOT EXISTS `#__jvotesystem_logs` (
			  `type` varchar(10) NOT NULL,
			  `time` int(11) NOT NULL,
			  `time_ms` int(11) NOT NULL,
			  `uid` int(11) NOT NULL,
			  `jvsuid` int(11) NOT NULL,
			  `message` text NOT NULL,
			  `pars` text NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * UPDATE INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
 
 //Updates von 1.00 nicht überprüfen, wenn Datenbankstruktur schon verändern(1.12)
$sql = 'DESCRIBE `#__jvotesystem_boxes` `params` '; 
$db->setQuery($sql);
$version112 = $db->loadResult();

if(!$version112) {
	//Wenn Version 1.00, dann Update auf 1.01
	$query = 'DESCRIBE `#__jvotesystem_boxes` `add_comment` ';
	$db->setQuery($query);
	$version101 = $db->loadResult();
	if(!$version101) {
	   #############################################################################
		#                                                       #
		# Database Update Comments `#__jvotesystem_boxes` From 1.00 to 1.01    #
		#                                                       #
		#############################################################################
	   $sql = 'ALTER TABLE `#__jvotesystem_boxes` '
			. ' ADD `add_comment` INT(1) NOT NULL DEFAULT "0" AFTER `add_answer_access`, '
			. ' ADD `add_comment_access` INT NOT NULL DEFAULT "18" AFTER `add_comment`';
	   $db->setQuery($sql);
	   if(!$db->query()) $success = false;
	   else $success = true;
	   
	   $status->updates[] = array ('name'=>'Comments', 'version'=>'1.01', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
	}
	//Wenn Version 1.08, dann Update auf 1.09
	$sql = 'DESCRIBE `#__jvotesystem_boxes` `send_mail_admin_answer` '; 
	$db->setQuery($sql);
	$version109 = $db->loadResult();
	if(!$version109) {
		#########################################################
		#                                                       #
		# Database Update Settings  `#__jvotesystem_boxes`      #
		#                                                       #
		#########################################################
	   $sql = 'ALTER TABLE `#__jvotesystem_boxes`
				ADD `object_group` varchar(100) NOT NULL DEFAULT "com_jvotesystem" AFTER `id`,
				ADD `object_id` int(11) DEFAULT NULL AFTER `object_group`,
				ADD `send_mail_admin_answer` INT(1) NOT NULL AFTER `add_comment_access`,
				ADD `send_mail_user_answer_comments` INT(1) NOT NULL AFTER `send_mail_admin_answer`,
				ADD `send_mail_admin_comment` INT(1) NOT NULL AFTER `send_mail_user_answer_comments`,
				ADD `send_mail_user_comment_comments` INT(1) NOT NULL AFTER `send_mail_admin_comment`,
				ADD `activate_spam` INT(1) NOT NULL AFTER `send_mail_user_comment_comments`,
				ADD `spam_count` INT NOT NULL AFTER `activate_spam`,
				ADD `spam_mail_admin_report` INT(1) NOT NULL AFTER `spam_count`,
				ADD `spam_mail_admin_ban` INT(1) NOT NULL AFTER `spam_mail_admin_report`;'; 
	   $db->setQuery($sql);
	   if(!$db->query()) $success = false;
	   else $success = true;
	   
	   $status->updates[] = array ('name'=>'Settings', 'version'=>'1.09', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
	   
		#########################################################
		#                                                       #
		# Database Update Spam      `#__jvotesystem_answers`    #
		#                                                       #
		#########################################################
	   $sql = 'ALTER TABLE `#__jvotesystem_answers`
				ADD `no_spam_admin` int(1) NOT NULL DEFAULT "0" AFTER `created`;'; 
	   $db->setQuery($sql);
	   if(!$db->query()) $success = false;
	   else $success = true;
	   
	   $status->updates[] = array ('name'=>'Spam', 'version'=>'1.09', 'success'=>$success, 'table'=>'#__jvotesystem_answers');
	   
	   #########################################################
		#                                                       #
		# Database Update Spam      `#__jvotesystem_comments`    #
		#                                                       #
		#########################################################
	   $sql = 'ALTER TABLE `#__jvotesystem_comments`
				ADD `no_spam_admin` int(1) NOT NULL DEFAULT "0" AFTER `modified`;'; 
	   $db->setQuery($sql);
	   if(!$db->query()) $success = false;
	   else $success = true;
	   
	   $status->updates[] = array ('name'=>'Spam', 'version'=>'1.09', 'success'=>$success, 'table'=>'#__jvotesystem_comments');
	   
	   #########################################################
		#                                                       #
		# Database Update Email      `#__jvotesystem_users`    #
		#                                                       #
		#########################################################
	   $sql = 'ALTER TABLE `#__jvotesystem_users`
				ADD `email` varchar(255) NOT NULL AFTER `lastvisitDate`;'; 
	   $db->setQuery($sql);
	   if(!$db->query()) $success = false;
	   else $success = true;
	   
	   $status->updates[] = array ('name'=>'Mail', 'version'=>'1.09', 'success'=>$success, 'table'=>'#__jvotesystem_users');
	}

	//Wenn Version 1.09, dann Update auf 1.10
	$sql = 'DESCRIBE `#__jvotesystem_boxes` `activate_ranking` '; 
	$db->setQuery($sql);
	$version110 = $db->loadResult();
	if(!$version110) {
		#########################################################
		#                                                       #
		# Database Update Settings  `#__jvotesystem_boxes`      #
		#                                                       #
		#########################################################
	   $sql = "ALTER TABLE `#__jvotesystem_boxes` ADD `activate_ranking` INT( 1 ) NOT NULL DEFAULT '0';"; 
	   $db->setQuery($sql);
	   if(!$db->query()) $success = false;
	   else $success = true;
	   
	   $status->updates[] = array ('name'=>'Ranking', 'version'=>'1.10', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
	} 
}

//Wenn Version 1.11 dann Update auf 1.12
if(!$version112) {
    #########################################################
    #                                                       #
    # Database Update Settings  `#__jvotesystem_boxes`      #
    #                                                       #
    #########################################################
   $sql = "ALTER TABLE `#__jvotesystem_boxes` ADD `params` TEXT NOT NULL;"; 
   $db->setQuery($sql);
   if(!$db->query()) $success = false;
   else $success = true;
   
   $status->updates[] = array ('name'=>'Params', 'version'=>'1.12', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
   
    #########################################################
    #                                                       #
    # Database Update Settings  `#__jvotesystem_boxes`      #
    #                                                       #
    #########################################################
   $sql = 'UPDATE `#__jvotesystem_boxes`
SET `params` = CONCAT("send_mail_admin_answer=", `send_mail_admin_answer`, "\nsend_mail_user_answer_comments=", `send_mail_user_answer_comments`, "\nsend_mail_admin_comment=", `send_mail_admin_comment`, "\nsend_mail_user_comment_comments=", `send_mail_user_comment_comments`, "\nactivate_spam=", `activate_spam`, "\nspam_count=", `spam_count`, "\nspam_mail_admin_report=", `spam_mail_admin_report`, "\nspam_mail_admin_ban=", `spam_mail_admin_ban`, "\nactivate_ranking=", `activate_ranking`)'; 
   $db->setQuery($sql);
   if(!$db->query()) $success = false;
   else $success = true;
   
   $status->updates[] = array ('name'=>'Insert old columns into params', 'version'=>'1.12', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
   
    #########################################################
    #                                                       #
    # Database Update Settings  `#__jvotesystem_boxes`      #
    #                                                       #
    #########################################################
   $sql = "ALTER TABLE `#__jvotesystem_boxes` DROP `send_mail_admin_answer`, DROP `send_mail_user_answer_comments`, DROP `send_mail_admin_comment`, DROP `send_mail_user_comment_comments`, DROP `activate_spam`, DROP `spam_count`, DROP `spam_mail_admin_report`, DROP `spam_mail_admin_ban`, DROP `activate_ranking`;";
   $db->setQuery($sql);
   if(!$db->query()) $success = false;
   else $success = true;
   
   $status->updates[] = array ('name'=>'Remove old columns', 'version'=>'1.12', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
} 

//Wenn Version 1.12.. dann updaten
$sql = 'DESCRIBE `#__jvotesystem_boxes` `ordering` '; 
$db->setQuery($sql);
$version113 = $db->loadResult();

if(!$version113) {
	#########################################################
    #                                                       #
    # Database Update Settings  `#__jvotesystem_boxes`      #
    #                                                       #
    #########################################################
   $sql = "ALTER TABLE `#__jvotesystem_boxes` ADD `ordering` INT NOT NULL AFTER `published`;";
   $db->setQuery($sql);
   if(!$db->query()) $success = false;
   else $success = true;
   
   $status->updates[] = array ('name'=>'Add Polls-Ordering', 'version'=>'1.13', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
}

//MAJOR-UPDATE Version 2.00
$sql = 'DESCRIBE `#__jvotesystem_boxes` `catid` '; 
$db->setQuery($sql);
$version200 = $db->loadResult();

if(!$version200) {
	//In allen Artikeln die alten Plugineinträge überschrieben
	$sql = 'SELECT `id`, `introtext`, `fulltext` FROM `#__content`';
	$db->setQuery($sql);
	$contents = $db->loadObjectList();
	
	$replacedCount = 0;
	
	function replaceOldPars($text) {
		$reg = "#{jvotesystem poll==(.*?)}#s";
		preg_match_all($reg, $text, $matches);
		if(count($matches) > 0) {
			foreach($matches[0] AS $i => $match) {
				$output = "{jvotesystem poll=|".$matches[1][$i]."|}";
				$text = preg_replace ('{'.$matches[0][$i].'}', $output, $text);
				$replacedCount++;
			}
		}
		return $text;
	};
	
	foreach($contents AS $content) {
		$needUpdate = false;
	
		$intro = replaceOldPars($content->introtext);
		if($intro != $content->introtext) { $needUpdate = true; $content->introtext = $intro; }
		$full = replaceOldPars($content->fulltext);
		if($full != $content->fulltext) { $needUpdate = true; $content->fulltext = $full; }
		
		if($needUpdate) $db->updateObject('#__content', $content, 'id');
	}
	
	$status->updates[] = array ('name'=>'Updated '.$replacedCount.' content articles: replaced {jvotesystem poll==ID} with {jvotesystem poll=|ID|}', 'version'=>'2.00', 'success'=>true, 'table'=>'#__content');
	
	//Änderungen an den Umfragentabellen -> Kategorien einführen & neue Rechte
	$sql = "ALTER TABLE `#__jvotesystem_boxes` 
				DROP `object_group`,
				DROP `object_id`,
				ADD `alias` varchar(25) NOT NULL AFTER `question`,
				ADD `catid` INT NOT NULL DEFAULT '1' AFTER `id` ,
				ADD `result_access` INT(11) NOT NULL DEFAULT '0' AFTER `access`;";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
   
	$status->updates[] = array ('name'=>'Updated polls', 'version'=>'2.00', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
	
	//Joomla 1.6+: Für ResultAccess Werte eintragen
	if(!version_compare( JVERSION, '1.6.0', 'lt' )) {
		$sql = " SELECT `id` FROM `#__jvotesystem_boxes` ";
		$db->setQuery($sql);
		$polls = $db->loadObjectList();
		$success = true;
		foreach($polls AS $poll) { $id = $poll->id;
			$sql = "INSERT INTO `#__jvotesystem_access` (`box_id`, `group_id`, `access`) VALUES
					($id, 1, 'result_access'),
					($id, 2, 'result_access'),
					($id, 3, 'result_access'),
					($id, 4, 'result_access'),
					($id, 5, 'result_access'),
					($id, 6, 'result_access'),
					($id, 7, 'result_access'),
					($id, 8, 'result_access');";
			$db->setQuery($sql);
			if(!$db->query()) $success = false;
		}
		
		$status->updates[] = array ('name'=>'Added Entries for Result', 'version'=>'2.00', 'success'=>$success, 'table'=>'#__jvotesystem_access');
	}
   
	//Alias generieren
	$sql = 'SELECT `id`, `title` FROM `#__jvotesystem_boxes`';
	$db->setQuery($sql);
	$polls = $db->loadObjectList();
	
	$general =& VBGeneral::getInstance(false);
	$success = true;
	foreach($polls AS $poll) {
		$poll->alias = $general->cleanStr($poll->title);
		
		$db->updateObject('#__jvotesystem_boxes', $poll, 'id');
		if($db->getErrorMsg()) $success = false;
	}
	
	$status->updates[] = array ('name'=>'Generated aliases for polls', 'version'=>'2.00', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
	
	//Access Tabelle überabeiten .. ID entfernen
	if(!version_compare( JVERSION, '1.6.0', 'lt' )) {
		$sql = "ALTER TABLE `#__jvotesystem_access` DROP `id`;";
		$db->setQuery($sql);
		if(!$db->query()) $success = false;
		else $success = true;
		
		$sql = "ALTER TABLE `#__jvotesystem_access` ADD PRIMARY KEY ( `box_id` , `group_id` , `access` ) ;";
		$db->setQuery($sql);
		if(!$db->query()) $success = false;
		
		$status->updates[] = array ('name'=>'Dropped primary key id', 'version'=>'2.00', 'success'=>$success, 'table'=>'#__jvotesystem_access');
	}
}

//Zweites Update auf Version 2.00 - um Timeouts zu verhindern
$sql = 'DESCRIBE `#__jvotesystem_users` `cookie` '; 
$db->setQuery($sql);
$version200 = $db->loadResult();

if($version200) {
	//Usertabelle in neue Session-Tabelle übertragen, anschließend Benutzertabelle reinigen
	$sql = "INSERT INTO `#__jvotesystem_sessions` (
				SELECT NULL, `id`, `cookie`, 1, `lastVisitDate`, ''
				FROM `#__jvotesystem_users`
			)";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
	
	$status->updates[] = array ('name'=>'Copied userdata in new sessiontable', 'version'=>'2.00', 'success'=>$success, 'table'=>'#__jvotesystem_users -> #__jvotesystem_sessions');
	
	//Alte Elemente entfernen
	$sql = "ALTER TABLE `#__jvotesystem_users` 
				DROP `cookie`,
				DROP `lastVisitDate` ";
	$db->setQuery($sql);
	if(!$db->query()) $success = false;
	else $success = true;
	
	$status->updates[] = array ('name'=>'Removed old userdata', 'version'=>'2.00', 'success'=>$success, 'table'=>'#__jvotesystem_users');
}

//New Updater
require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'classes'.DS.'update.class.php';
$updater =& VBUpdate::getInstance();
if($updater->needVersionUpdate_2_50()) {
	$success = $updater->doVersionUpdate_2_50();
	$status->updates[] = array ('name'=>'Updated access; added colors & logs', 'version'=>'2.50', 'success'=>$success, 'table'=>'#__jvotesystem_access, #__jvotesystem_answers, #__jvotesystem_logs');
}
if($updater->needVersionUpdate_2_56()) {
	$success = $updater->doVersionUpdate_2_56();
	$status->updates[] = array ('name'=>'Updated to version 2.56', 'version'=>'2.56', 'success'=>$success, 'table'=>'...');
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * DATABASE TABLES INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

//Standard-Kategorie einfügen
$sql = ' SELECT IFNULL(COUNT(`id`),0) FROM `#__jvotesystem_categories` '; 
$db->setQuery($sql);
$categoriesCount = $db->loadResult();

if($categoriesCount == 0) {
	
	if($lib->getJoomlaVersion() == joomessLibrary::jVersion15) {
		$access = '{"add_poll":"19","edit_poll":"20","remove_poll":"21"}';
	} else {
		$access = "{\"add_poll\":[\"3\",\"4\",\"5\",\"6\",\"7\",\"8\"],\"edit_poll\":[\"4\",\"5\",\"6\",\"7\",\"8\"],\"remove_poll\":[\"6\",\"7\",\"8\"]}";
	}
	//Kategorie
	$date = JFactory::getDate();
	$sql = "INSERT INTO `#__jvotesystem_categories` (`id`, `parent_id`, `order`, `title`, `alias`, `description`, `accesslevel`, `published`, `params`, `access`, `autor_id`, `created`) VALUES\n"
    . "(1, 0, 0, 'Uncategorized', 'uncategorized', '', ".((version_compare( JVERSION, '1.6.0', 'lt' )) ? 0 : 1).", 1, '{\"autopublish_polls\":\"1\",\"mail_admin_new_poll\":\"1\",\"edit_own_poll\":\"1\",\"remove_own_poll\":\"1\",\"allowed_tabs\":[\"settings\",\"display\",\"result\",\"votes\"]}', '".$access."', ".JFactory::getUser()->id.", '".$date->toMySql()."');";
	$db->setQuery($sql);
	if(!$db->query()) {
		$success = false;
		$status->errors[] = array( "error" => "Failed to add category", "sql" => $db->getErrorMsg() );
	} else $success = true;
	
	//Standardsettings
	$vote =& VBVote::getInstance(false);
	if(!$vote->addDefaultSettingsBox(1)) $success = false;
	
	$status->updates[] = array ('name'=>'Added uncategorized category', 'version'=>'2.00', 'success'=>$success, 'table'=>'#__jvotesystem_categories');
}

// UPDATE - Version 2.02
//Nicht verknüpfte Datenbankeinträge mit Kategorie-ID versehen
$sql = "SELECT b.`id` 
		FROM `#__jvotesystem_boxes` AS b
		WHERE NOT EXISTS (SELECT `id` FROM `#__jvotesystem_categories` AS c WHERE c.`id`=b.`catid`) 
		AND b.`published` > -1 ";
$db->setQuery($sql);
$boxesAlone = $db->loadObjectList();
if($boxesAlone) {
	$c = 0; $success = true;
	//cat-id holen
	$sql = "SELECT `id` 
			FROM `#__jvotesystem_categories` 
			ORDER BY `id` ASC 
			LIMIT 0,1";
	$db->setQuery($sql);
	$cat = $db->loadResult();
	if(!$cat) {
		$success = false;
	} else {
		foreach($boxesAlone AS $box) {
			$box->catid = $cat;
			$db->updateObject("#__jvotesystem_boxes", $box, "id");
			if($this->db->getErrorMsg()) $success = false;
			$c++;
		}
	}
	
	$status->updates[] = array ('name'=>'Reconnected '.$c.' Polls with Category-ID '.$cat, 'version'=>'2.02', 'success'=>$success, 'table'=>'#__jvotesystem_boxes');
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * Install modules and plugins -- BEGIN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

// -- ContentPlugin
$result = $updater->installExtension( 'jvotesystemcontent', $src.'/plugins/content');
$status->plugins[] = array('name'=>'Content - jVoteSystemContent','group'=>'content', 'success'=> $result);

// -- ContentButtonPlugin
$result = $updater->installExtension( 'jvotesystembutton', $src.'/plugins/button');
$status->plugins[] = array('name'=>'Button - jVoteSystemButton','group'=>'editors-xtd', 'success'=> $result);

// -- DatabasePlugin
$result = $updater->installExtension( 'jvotesystemdatabase', $src.'/plugins/database');
$status->plugins[] = array('name'=>'System - jVoteSystemDatabase','group'=>'system', 'success'=> $result);

// -- Module
$result = $updater->installExtension( 'jvotesystemmodule', $src.'/modules/module', false, false);
$status->modules[] = array('name'=>'Module - Poll', 'client' => 'site', 'success'=> $result);

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * SPECIAL-EXTENSIONs INSTALLATION SECTION
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
 
	#########################################################
    #                                                       #
    # JoomFish										        #
    #                                                       #
    #########################################################
	if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_joomfish')) {
		//Dateien kopieren..
		if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_jvotesystem/plugins/joomfish/jvotesystem_answers.xml')) {
			if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/jvotesystem_answers.xml')) {
				//Datei schon vorhanden.. entfernen.
				JFile::delete(JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/jvotesystem_answers.xml');
			}
			//Datei kopieren
			JFile::copy(JPATH_ADMINISTRATOR.'/components/com_jvotesystem/plugins/joomfish/jvotesystem_answers.xml', JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/jvotesystem_answers.xml');
		}
		if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_jvotesystem/plugins/joomfish/jvotesystem_boxes.xml')) {
			if(JFile::exists(JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/jvotesystem_boxes.xml')) {
				//Datei schon vorhanden.. entfernen.
				JFile::delete(JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/jvotesystem_boxes.xml');
			}
			//Datei kopieren
			JFile::copy(JPATH_ADMINISTRATOR.'/components/com_jvotesystem/plugins/joomfish/jvotesystem_boxes.xml', JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/jvotesystem_boxes.xml');
		}
		
		$status->plugins[] = array('name'=>'JoomFish Plugin','group'=>'Translation');
	}
 
/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * CONTACT JOOMESS REGISTRATION SYSTEM
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

//joomessLibrary
$lib->connectToServer( joomessLibrary::$_JLIB_EXTENSION_ID, 'installedExtension', array( "version" => joomessLibrary::$_JLIB_VERSION ) );
//jVoteSystem
$sql = ' SELECT IFNULL(COUNT(`key`),0) FROM `#__jvotesystem_apikeys` '; 
$db->setQuery($sql);
$apiCount = $db->loadResult();

$params = array();
if($apiCount == 0) {
	//Generate API-Key for joomess Server - only global statistics
	$options 				= new JObject();
	$options->tasks 		= array( "global" );
	$options->limit 		= 10;
	$options->limit_type 	= "week";
	$options->title 		= "Global statistics for jVoteSystem";
	
	$params["apikey"] = VBApi::getInstance()->addApiKey($options, true);
} else {
	$db->setQuery(' SELECT `key` FROM `#__jvotesystem_apikeys` WHERE `params` = \'{"tasks":["global"],"limit":10,"limit_type":"week","title":"Global statistics for jVoteSystem"}\' ');
	if($key = $db->loadResult())
		$params["apikey"] = $key;
}
$updater->connectToServer( 'installedExtension', $params ); 
	
/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$rows = 0;
?>

<h2>jVoteSystem Installation</h2>
<table class="adminlist">
    <thead>
        <tr>
            <th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
            <th width="30%"><?php echo JText::_('Status'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    <tbody>
        <tr class="row0">
            <td class="key" colspan="2"><?php echo 'jVoteSystem '.JText::_('Component'); ?></td>
            <td><?php echo $jvs_success; ?><strong><?php echo JText::_('Installed'); ?></strong></td>
        </tr>
        <?php if (count($status->modules)) : ?>
        <tr>
            <th><?php echo JText::_('Module'); ?></th>
            <th><?php echo JText::_('Client'); ?></th>
            <th></th>
        </tr>
        <?php foreach ($status->modules as $module) : ?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo $module['client']; ?></td>
            <td><?php echo $module['success'] ? $jvs_success : $jvs_error; ?><strong><?php echo $module['success'] ? JText::_('Installed') : JText::_('Failed'); ?></strong></td>
        </tr>
        <?php endforeach;
    endif;
    if (count($status->plugins)) : ?>
        <tr>
            <th><?php echo JText::_('Plugin'); ?></th>
            <th><?php echo JText::_('Group'); ?></th>
            <th></th>
        </tr>
        <?php foreach ($status->plugins as $plugin) : ?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
            <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
            <td><?php echo $plugin['success'] ? $jvs_success : $jvs_error; ?><strong><?php echo $plugin['success'] ? JText::_('Installed') : JText::_('Failed'); ?></strong></td>
        </tr>
        <?php endforeach;
    endif;
    if (count($status->updates)) : ?>
        <tr>
            <th><?php echo JText::_('Update'); ?></th>
            <th><?php echo JText::_('Version'); ?></th>
            <th></th>
        </tr>
        <?php foreach ($status->updates as $update) : ?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo ucfirst($update['name']); ?></td>
            <td class="key"><?php echo ucfirst($update['version']); ?></td>
            <td><?php if($update['success']) echo $jvs_success; else echo $jvs_error;?><strong><?php if($update['success']) echo JText::_('Updated'); else echo JText::_('Failed');?></strong> (<?php echo $update['table']; ?>)</td>
        </tr>
        <?php endforeach;
    endif; 
	if (count($status->errors)) : ?>
        <tr>
            <th><?php echo JText::_('Error'); ?></th>
            <th><?php echo JText::_('SQL'); ?></th>
            <th></th>
        </tr>
        <?php foreach ($status->errors as $error) : ?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo $error["error"]; ?></td>
            <td class="key"><?php echo $error["sql"]; ?></td>
            <td><?php echo $jvs_error; ?><strong><?php echo JText::_('Failed');?></strong></td>
        </tr>
        <?php endforeach;
    endif; ?>
    </tbody>
</table>
<?php
##ECR_MD5CHECK##

##ECR_MD5CHECK_FNC##