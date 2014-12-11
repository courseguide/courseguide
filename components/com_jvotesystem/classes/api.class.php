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
jimport( 'joomla.file.file' );
jimport( 'joomla.application.component.helper' );

class VBApi
{

	private function __construct() { 
		$this->db =& JFactory::getDBO();
		$this->document = & JFactory::getDocument();
		$this->vbparams =& VBParams::getInstance();
		$this->general =& VBGeneral::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBApi();
		}
		return $instance;
	}
	
	function addApiKey($params, $returnKey = false) {
		$ins = new JObject();
		$ins->key 			= $this->general->getNewHash();
		$ins->params 		= json_encode($params);
		$ins->count 		= 0;
		$ins->total_count	= 0;
		$ins->last_access 	= '0000-00-00 00:00:00';
		
		$this->db->insertObject('#__jvotesystem_apikeys', $ins);
		if($this->db->getErrorMsg()) {
			VBLog::getInstance()->add("ERROR", "FailedToGenerateApiKey", array("ins" => $ins, "db_error" => $this->db->getErrorMsg()));
			return false;
		}
		return $returnKey ? $ins->key : true;
	}
	
	function updateKey($params, $key) {
		$ins = new JObject();
		$ins->key 		= $key;
		$ins->params 	= json_encode($params);
		
		$this->db->updateObject('#__jvotesystem_apikeys', $ins, 'key');
		if($this->db->getErrorMsg()) {
			VBLog::getInstance()->add("ERROR", "FailedToUpdateApiKey", array("ins" => $ins, "db_error" => $this->db->getErrorMsg()));
			return false;
		}
		return true;
	}
	
	function getTaskList() {
		return array(
				"global",
				"poll",
				"polls",
				"votebutton"
			   );
	}
	
	function count( $key, $count, $total ) {
		$upd = new JObject();
		$upd->count 		= ($count == 0) ? 1 : $count;
		if($count == 0) $upd->last_start = JFactory::getDate()->toMySQL();
		$upd->last_access 	= JFactory::getDate()->toMySQL();
		$upd->total_count   = $total;
		
		$sql = array();
		$sql[] = "UPDATE #__jvotesystem_apikeys SET";
		foreach($upd AS $k => $value) {
			if($k != '_errors') {
				if(count($sql) > 1) $sql[] = ",";
				$sql[] = "`$k` = ".$this->db->quote($value);
			}
		}
		$sql[] = "WHERE `key` = ".$this->db->quote($key);
		$this->db->setQuery(implode(" ", $sql));
		$this->db->query();
	}
	
	function getList() {
		$this->db->setQuery(' SELECT * FROM `#__jvotesystem_apikeys` ');
		$keys = $this->db->loadObjectList();
		
		foreach($keys AS &$key) {
			$key->params = json_decode($key->params);
			foreach($key->params->tasks AS &$task)
				$task = JText::_('JVS_API_TASK_'.$task);
		}
		
		return $keys;
	}
	
	function loadKey( $key ) {
		$this->db->setQuery(' SELECT * FROM `#__jvotesystem_apikeys` WHERE `key` = '.$this->db->quote($key));
		$row = $this->db->loadObject();
		if(!$row) return null;
		
		$row->params = json_decode($row->params);
		return $row;
	}
	
	function removeKey( $key ) {
		$sql = 'DELETE FROM `#__jvotesystem_apikeys` '
		. ' WHERE `key` = '.$this->db->quote($key)
		. ' LIMIT 1';
		$this->db->setQuery($sql);
		$this->db->query();
		
		if($this->db->getErrorMsg()) {
			VBLog::getInstance()->add("ERROR", "FailedToRemoveApiKey", array("key" => $key, "db_error" => $this->db->getErrorMsg()));
			return false;
		}
		return true;
	}
	
}//class
