<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport( 'joomla.methods' );
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.application.component.helper' );
jimport( 'joomla.cache.cache' );

class VBUpdate
{
	
	public static $_JVS_VERSION 		= "2.56";
	public static $_JVS_EXTENSION_ID 	= 1;

	private function __construct() { 
		$this->db =& JFactory::getDBO();
		$this->document = & JFactory::getDocument();
		if(class_exists('joomessLibrary'))
			$this->lib =& joomessLibrary::getInstance();
		$this->cache = & JCache::getInstance();
		$this->cache->setCaching( 1 );
		$this->cache->setLifeTime( 30 );
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBUpdate();
		}
		return $instance;
	}
	
	function needUpdate() {
		$out = $this->getServerData();
		return (!$out) ?  -1 : !$out->upToDate;
	}
	
	function getServerData() {
		if(!$out = json_decode($this->cache->get('updateCheck', 'jVoteSystem'))) {

			$out = $this->connectToServer('updateCheck');
			
			$this->cache->store(json_encode($out), 'updateCheck', 'jVoteSystem');
		}
		return $out;
	}
	
	function connectToServer($task, $params = array()) {
		$params["version"] = self::$_JVS_VERSION;
		return $this->lib->connectToServer(self::$_JVS_EXTENSION_ID, $task, $params);
	}
	
	function getDownloadLink() {
		$out = $this->getServerData();
		if($out) return $out->download;
		else return 'http://joomess.de/projects/jvotesystem/download';
	}
	
	function generateUpdateFiles() {		
		//Embed source file
		$path = JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'assets'.DS.'js';
		if(JFile::exists($path.DS."jvs-embed.js"))
			JFile::delete($path.DS."jvs-embed.js");
		if(JFile::exists($path.DS."jvs-embed.src.js")) {
			$content = JFile::read($path.DS."jvs-embed.src.js");
			$min = $this->lib->minifyJS($content);
			
			//Root
			$min = str_replace( 'ROOT_PATH_PLACEHOLDER', $this->lib->root()."/", $min);
			
			$min = '/**!
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/'.$min;			
			
			JFile::write($path.DS."jvs-embed.js", $min);
		}
	}
	
	function installExtension( $name, $path, $publish = true, $checkPublished = true, $modulePosition = '' ) {
		if($checkPublished) {
			if(version_compare( JVERSION, '1.6.0', 'lt' ))
				$this->db->setQuery( "SELECT `published` FROM `#__plugins` WHERE `element` = ".$this->db->quote($name) );					
			else
				$this->db->setQuery( "SELECT `enabled` FROM `#__extensions` WHERE `element` = ".$this->db->quote($name) );
			$result = $this->db->loadResult();
			if($result != null)
				$publish = (bool)$result;
		}
		
		//XML-Files überprüfen
		if(!JFile::exists($path.DS.$name.'.xml')) {
			if(version_compare( JVERSION, '1.6.0', 'lt' )) 		$joomla_version = 1.5;
			elseif(version_compare( JVERSION, '3.0.0', 'lt' )) 	$joomla_version = 2.5;
			else 												$joomla_version = 3.0;
			
			switch($joomla_version) {
				case 3.0:
					$xml_path = $path.DS.$name.'.xml.j30';
					if(JFile::exists($xml_path)) break;
				case 2.5:
					$xml_path = $path.DS.$name.'.xml.j25';
					if(JFile::exists($xml_path)) break;
				case 1.5:
					$xml_path = $path.DS.$name.'.xml.j15';
					if(JFile::exists($xml_path)) break;
				default:
					$xml_path = "";
					break;
			}
			if($xml_path != "")
				JFile::move($xml_path, $path.DS.$name.'.xml');
		}
		
		$installer = new JInstaller;
		$result = $installer->install($path);
		
		if(!$result) return false;
		
		if($publish) {
			if(version_compare( JVERSION, '1.6.0', 'lt' ))
				$this->db->setQuery( "UPDATE #__plugins SET published=1 WHERE `element`= ".$this->db->quote($name) );
			else
				$this->db->setQuery( "UPDATE #__extensions SET enabled=1 WHERE `element`= ".$this->db->quote($name) );
			$this->db->query();
		}
		
		if($modulePosition != "") {
			$this->db->setQuery( "UPDATE #__modules SET `published`=1, `access`=1, `position`=".$this->db->quote($modulePosition)." WHERE `module`= ".$this->db->quote($name) );
			$this->db->query();
			
			$this->db->setQuery( "SELECT `id` FROM #__modules WHERE `module`= ".$this->db->quote($name) );
			if($result = $this->db->loadResult()) {
				$sql = "INSERT IGNORE INTO `#__modules_menu` (
					`moduleid` ,
					`menuid`
				)
				VALUES (
					'$result', '0'
				)";
				$this->db->setQuery($sql);
				$this->db->query();
			}
				
				
			
		}
		
		if($name == 'joomessLibrary') {
			require_once JPATH_SITE.DS.'plugins'.DS.'system'.( (version_compare( JVERSION, '1.6.0', 'lt' )) ? "" : DS.'joomessLibrary' ).DS.'joomessLibrary.php';
			$this->lib =& joomessLibrary::getInstance();
		}
			
		
		return true;
	}
	
	function needVersionUpdate_2_50() {
		/*
		 * Update-Check
		 */
		$this->db->setQuery( ' DESCRIBE `#__jvotesystem_answers` `color` ' );
		$result = $this->db->loadResult();
		
		if(!$result) return true;
		else return false;
	}
	
	function doVersionUpdate_2_50() {
		/** DB-Changelog:
		 * 
		 * -- Access --
		 * (+) `access` column for JSON-Arrays to `categories` & `boxes` tables
		 * (*) transfer access settings from `access` table to new `access` columns
		 * (-) `access` table
		 * (-) `access` columns in `boxes` table
		 * 
		 * -- Colors --
		 * (+) `color` column for Hex-Code to `answers` table
		 * (*) set default colors for polls
		 * 
		 * -- Logs --
		 * (+) `logs` table for temporary logging in db
		 * 
		 * -- Tasks --
		 * (-) Old `tasks` table for email (replaced through `email_tasks`)
		 * (+) New table `tasks` for quick moderation
		 * 
		 * -- Spam --
		 * (+) Add column `msg`
		 * 
		 **/
		
		$errors = array();
		
		/*
		 * Access
		 */
		
		//New Columns
		$this->db->setQuery(' ALTER TABLE `#__jvotesystem_categories` ADD `access` TEXT NOT NULL AFTER `params` ');
		$this->db->query();
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		$this->db->setQuery(' ALTER TABLE `#__jvotesystem_boxes` CHANGE `access` `access` TEXT NOT NULL ');
		$this->db->query();
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		//Transfer data
		$poll_columns = array ( 'access', 'result_access', 'admin_access', 'add_answer_access', 'add_comment_access' );
		$cat_columns = array ( 'add_poll', 'edit_poll', 'remove_poll' );
		switch($this->lib->getJoomlaVersion()) {
			case joomessLibrary::jVersion15:
				//Polls
				$this->db->setQuery(' SELECT `id`, `'.implode("`,`", $poll_columns).'` FROM `#__jvotesystem_boxes` ');
				$polls = $this->db->loadObjectList();
				if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
				
				foreach($polls AS $poll) {
					$data = array();
					foreach($poll_columns AS $col) 
						$data[$col] = (int)(isset($poll->$col) ? $poll->$col : 25);
					
					$upd = new JObject();
					$upd->id = $poll->id;
					$upd->access = json_encode($data);
					
					$this->db->updateObject("#__jvotesystem_boxes", $upd, 'id');
					if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
				}
				//Categories
				$this->db->setQuery(' SELECT `id`, `params` FROM `#__jvotesystem_categories` ');
				$cats = $this->db->loadObjectList();
				if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
				
				foreach($cats AS $cat) {
					$params = json_decode($cat->params);
					$data = array();
					foreach($cat_columns AS $col) {
						$data[$col] = (int) (isset($params->$col) ? $params->$col : 25);
						unset($params->$col);	
					}
					
					$upd = new JObject();
					$upd->id = $cat->id;
					$upd->access = json_encode($data);
					$upd->params = json_encode($params);
					
					$this->db->updateObject("#__jvotesystem_categories", $upd, 'id');	
					if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();				
				}
				
				break;
			default:
				//Polls
				$this->db->setQuery(' SELECT `id` FROM `#__jvotesystem_boxes` ');
				$polls = $this->db->loadResultArray();
				if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
				
				$this->db->setQuery(' SELECT * FROM `#__jvotesystem_access` '.
									' WHERE (`box_id` = "'.implode('" OR `box_id` = "', $polls).'") '.
									' AND (`access` = "'.implode('" OR `access` = "', $poll_columns).'") ');
				$raw_data = $this->db->loadObjectList();
				if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
				
				$data = array();
				foreach($raw_data AS $row) {
					if(!isset($data[$row->box_id])) $data[$row->box_id] = array();
					if(!isset($data[$row->box_id][$row->access])) $data[$row->box_id][$row->access] = array();
					$data[$row->box_id][$row->access][] = (int)$row->group_id;
				}
				
				foreach($polls AS $pid) {
					if(isset($data[$pid])) {
						$upd = new JObject();
						$upd->id = $pid;
						$upd->access = json_encode($data[$pid]);
							
						$this->db->updateObject("#__jvotesystem_boxes", $upd, 'id');
						if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
					}
				}
				
				//Categories
				$this->db->setQuery(' SELECT `id` FROM `#__jvotesystem_categories` ');
				$cats = $this->db->loadResultArray();
				if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
				
				$this->db->setQuery(' SELECT * FROM `#__jvotesystem_access` '.
						' WHERE (`box_id` = "'.implode('" OR `box_id` = "', $cats).'") '.
						' AND (`access` = "'.implode('" OR `access` = "', $cat_columns).'") ');
				$raw_data = $this->db->loadObjectList();
				if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
				
				$data = array();
				foreach($raw_data AS $row) {
					if(!isset($data[$row->box_id])) $data[$row->box_id] = array();
					if(!isset($data[$row->box_id][$row->access])) $data[$row->box_id][$row->access] = array();
					$data[$row->box_id][$row->access][] = (int)$row->group_id;
				}
				
				foreach($cats AS $cid) {
					if(isset($data[$cid])) {
						$upd = new JObject();
						$upd->id = $cid;
						$upd->access = json_encode($data[$cid]);
						
						$this->db->updateObject("#__jvotesystem_categories", $upd, 'id');
						if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
					}
				}
				
				break;
		}
		
		//Remove `access` table
		$this->db->setQuery(' DROP TABLE `#__jvotesystem_access` ');
		$this->db->query();
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		//Remove `access` columns in `boxes` table
		$this->db->setQuery(' ALTER TABLE `#__jvotesystem_boxes`
							  DROP `result_access`,
							  DROP `admin_access`,
							  DROP `add_answer_access`,
							  DROP `add_comment_access` ');
		$this->db->query();
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		/*
		 * Logs
		*/
		
		//Clean up the existing logs folder
		$files = JFolder::files(VBLog::getInstance()->log_dir, '.php', false, true);
		foreach($files AS $file)
			JFile::delete($file);
		
		/*
		 * Tasks
		*/
		
		//Drop old `tasks` table
		$this->db->setQuery(' DROP TABLE `#__jvotesystem_tasks` ');
		$this->db->query();
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		
		$this->db->setQuery(' 	CREATE TABLE IF NOT EXISTS `#__jvotesystem_tasks` (
									`group` varchar(20) NOT NULL,
									`id` int(11) NOT NULL,
									PRIMARY KEY (`group`,`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8; ');
		$this->db->query();
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		/*
		 * Spam
		 */
		
		//Add column msg
		$this->db->setQuery(' ALTER TABLE `#__jvotesystem_spam_reports` ADD `msg` TEXT NOT NULL ');
		$this->db->query();
		//if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		/*
		 * Colors
		 */
		
		//Add `color` column
		$this->db->setQuery(' ALTER TABLE `#__jvotesystem_answers` ADD `color` VARCHAR( 6 ) NOT NULL AFTER `answer` ');
		$this->db->query();
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		$general =& VBGeneral::getInstance(false);
		
		//Color codes
		$sql = 'SELECT `id` FROM `#__jvotesystem_boxes`';
		$this->db->setQuery($sql);
		foreach($this->db->loadResultArray() AS $id) {
			$general->resetColorsPoll($id);
		}
		if($this->db->getErrorMsg()) $errors[] = $this->db->getErrorMsg();
		
		if(empty($errors)) return true;
		else {
			$this->errors = $errors;
			return false;
		}
	}
	
	function needVersionUpdate_2_56() {
		return (self::$_JVS_VERSION == "2.56" && class_exists("VBLog"));
	}
	
	function doVersionUpdate_2_56() {
		/** Changelog:
		 *
		 * -- Logs --
		 * (*) Fixed ordering of log files
		 *
		 **/
	
		$errors = array();
		
		/**
		 * Logs
		 */
		$files = VBLog::getInstance()->getFiles();
		foreach($files AS $file) {
			$parts = explode(".", $file);
			if(strlen($parts[0]) == 2) {
				$newName = $parts[1].".".$parts[0].".".$parts[2];
				$dir = VBLog::getInstance()->log_dir.DS;
				JFile::move($dir.$file, $dir.$newName);
			}
		}
	
		if(empty($errors)) return true;
		else {
			$this->errors = $errors;
			return false;
		}
	}
		
	function getErrors() {
		return $this->errors;
	}
	
}//class
