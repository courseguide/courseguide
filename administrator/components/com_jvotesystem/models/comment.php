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
class jVoteSystemModelComment extends JModel
{

	var $data, $id, $aid;

    function __construct()
    {
        parent::__construct();

        $app = JFactory::getApplication('administrator');
		
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
		
		$this->_db->updateObject('#__jvotesystem_comments',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function publish() {
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->published = 1;
		$ins->no_spam_admin = 1;
		
		$this->_db->updateObject('#__jvotesystem_comments',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function getData() {
		$sql = 'SELECT * '
        . ' FROM `#__jvotesystem_comments` '
        . ' WHERE `id` = '.$this->id;
		$this->_db->setQuery($sql);
		return $this->_db->loadObject();
	}
	
	function getAnswers() {
		//Antworten laden
		$sql = "SELECT `id`, IF(LENGTH(`answer`) > 80, CONCAT(LEFT(`answer`, 77), '...'), `answer`) AS answer FROM `#__jvotesystem_answers` ";
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
	
	function store() {
		$date = new JDate();
		$this->user->loadUser(true);
	
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->answer_id = JRequest::getInt('answer_id',null);;
		$ins->comment = JRequest::getString('comment');
		$ins->published = JRequest::getInt('published', 1);
		$ins->no_spam_admin = JRequest::getInt('protected', 1);
		
		JFactory::getSession()->set('aid', $ins->answer_id, 'jVS_Admin_Filter');
		
		if($this->id > 0) {
			$ins->modified = $date->toMySQL();
			//Updaten
			$this->_db->updateObject('#__jvotesystem_comments', $ins, 'id');
			$this->id = $ins->id;
		} else {
			$ins->created = $date->toMySQL();
			$ins->autor_id = $this->user->id;
			//Neues Element
			$this->_db->insertObject('#__jvotesystem_comments', $ins);
			$this->id = $this->_db->insertid();
		}
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function delete() {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) return false;
		foreach($cid AS $id) {
			VBComment::getInstance()->removeComment($id);
		}
		return true;
	}
}//class
