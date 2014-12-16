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
class jVoteSystemModelApiKey extends JModel
{

	var $data, $id, $bid;

    function __construct()
    {
        parent::__construct();

        $app = JFactory::getApplication('administrator');
		
		$this->setId();
    }//function
	
	function setId($bid = null) {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) $this->id = JRequest::getString('id', 0);
        else $this->id = $cid[0];
	}
	
	function getId() {
		return $this->id;
	}
	
	 function unpublish() { 
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->published = 0;
		
		$this->_db->updateObject('#__jvotesystem_answers',$ins,'id'); 
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function publish() {
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->published = 1;
		$ins->no_spam_admin = 1;
		
		$this->_db->updateObject('#__jvotesystem_answers',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function getData() {
		return VBApi::getInstance()->loadKey($this->getId());
	}
	
	function store() {
		$params = new JObject();
		$params->tasks = JRequest::getVar("tasks", array(), "post", "ARRAY");
		$params->limit = JRequest::getInt("limit", 1000);
		$params->limit_type = JRequest::getString("limit_type", "week");
		$params->title = JRequest::getString("title", "");
		
		return ($this->getId()) ? VBApi::getInstance()->updateKey($params, $this->getId()) : VBApi::getInstance()->addApiKey($params);
	}
	
	function delete() {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) return false;
		foreach($cid AS $id) {
			VBApi::getInstance()->removeKey($id);
		}
		return true;
	}
}//class
