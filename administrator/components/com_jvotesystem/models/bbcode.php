<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport( 'joomla.application.component.model' );
jimport( 'joomla.utilities.date' );
/**
 * jVoteSystem Model
 *
 * @package    jVoteSystem
 * @subpackage Models
 */
class jVoteSystemModelBbcode extends JModel
{

	var $data, $id;

    function __construct()
    {
        parent::__construct();
		
		$this->setId();
		$this->user =& VBUser::getInstance();
    }//function
	
	function setId($bid = null) {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) $this->id = JRequest::getInt('id', 0);
        else $this->id = $cid[0];
	}
	
	function getId() {
		return $this->id;
	}
	
	 function unpublish() {
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->published = 0;
		
		$this->_db->updateObject('#__jvotesystem_bbcodes',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function publish() {
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->published = 1;
		
		$this->_db->updateObject('#__jvotesystem_bbcodes',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function getData() {
		$sql = 'SELECT * '
        . ' FROM `#__jvotesystem_bbcodes` '
        . ' WHERE `id` = '.$this->id;
		$this->_db->setQuery($sql);
		return $this->_db->loadObject();
	}
	
	function copy() {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) return false;
		foreach($cid AS $id) {
			$sql = "INSERT into `#__jvotesystem_bbcodes`\n"
				. "SELECT null, `name`, `published`, `regex`, `replace`, `replaceNot`, `withButton`, `buttonInfo`, `editorCode`, `buttonImage`\n"
				. "FROM `#__jvotesystem_bbcodes`\n"
				. "WHERE `id`=".$id;
			$this->_db->setQuery($sql);
			$this->_db->query();
			if($this->_db->getErrorMsg()) return false;
		}
		return true;
	}
	
	function store() {
		$date = new JDate();
	
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->name = JRequest::getString('name');
		$ins->regex = JRequest::getVar('regex', null, 'default', "STRING", JREQUEST_NOTRIM);
		$ins->replace = JRequest::getVar('replace', null, 'default', "STRING", JREQUEST_ALLOWRAW);
		$ins->replaceNot = JRequest::getVar('replaceNot', null, 'default', "STRING", JREQUEST_NOTRIM);
		$ins->withButton = JRequest::getInt('withButton',1);
		$ins->buttonInfo = JRequest::getString('buttonInfo',"");
		$ins->editorCode = JRequest::getVar('editorCode', null, 'default', "STRING", JREQUEST_NOTRIM);
		$ins->buttonImage = JRequest::getString('buttonImage',"");
		
		if($this->id > 0) {
			//Updaten
			$this->_db->updateObject('#__jvotesystem_bbcodes', $ins, 'id');
			$this->id = $ins->id;
		} else {
			$ins->published = 1;
			//Neues Element
			$this->_db->insertObject('#__jvotesystem_bbcodes', $ins);
			$this->id = $this->_db->insertid();
		}
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function delete() {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) return false;
		foreach($cid AS $id) {
			$sql = 'DELETE FROM `#__jvotesystem_bbcodes` '
			. ' WHERE `id` = '.$id
			. ' LIMIT 1'; 
			$this->_db->setQuery($sql);
			$this->_db->query();
			if($this->_db->getErrorMsg()) return false;
		}
		return true;
	}
	
	function setWithButton($state) {
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->withButton = $state;
		
		$this->_db->updateObject('#__jvotesystem_bbcodes',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
}//class
