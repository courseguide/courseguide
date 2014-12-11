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

/**
 * jVoteSystem Model
 *
 * @package    jVoteSystem
 * @subpackage Models
 */
class jVoteSystemModelBox extends JModel
{

	var $data, $id;

    function __construct()
    {
		parent::__construct();

        $app = JFactory::getApplication('administrator');
		
		$this->setId();
		$this->user =& VBUser::getInstance();
		$this->vbparams =& VBParams::getInstance();
    }//function
	
	function setId() {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) {
			$this->id = JRequest::getInt('id', 0);
			$this->cid = array ( $this->id );
		} else {
        	$this->id = $cid[0];
        	$this->cid = $cid;
        }
	}
	
	function getId() {
		return $this->id;
	}
	
	 function unpublish() {
		foreach($this->cid AS $id) {
			$ins = new JObject();
			$ins->id = $id;
			$ins->published = 0;
			
			$this->_db->updateObject('#__jvotesystem_boxes',$ins,'id');
			if($this->_db->getErrorMsg()) return false;
		}
		return true;
	}
	
	function publish() {
		foreach($this->cid AS $id) {
			$ins = new JObject();
			$ins->id = $id;
			$ins->published = 1;
			
			$this->_db->updateObject('#__jvotesystem_boxes',$ins,'id');
			if($this->_db->getErrorMsg()) return false;
		}
		return true;
	}
	
	function getData($id = null) {
		if($id != null) $this->id = $id;
		//Box-Row holen
		$sql = 'SELECT b. * , COUNT( a. `id` ) AS answers '
        . ' FROM `#__jvotesystem_boxes` AS b '
        . ' LEFT JOIN `#__jvotesystem_answers` AS a ON ( b. `id` = a. `box_id`) '
        . ' WHERE b. `id` = '.$this->id
        . ' GROUP BY b. `id` '; 
		$this->_db->setQuery($sql);
		
		$box = $this->_db->loadObject();
		//Params verarbeiten
		$box = $this->vbparams->convertBoxParams($box);
		return $box;
	}
	
	function delete() {
		$vote =& VBVote::getInstance();
		foreach($this->cid AS $id) {
			if(!$vote->removePoll($id)) return false;
		}
		return true;
	}
	
	function resetVotes(){
		//Alle Vote-Einträge der Umfrage enfernen
		$sql = 'DELETE FROM `#__jvotesystem_votes` WHERE `answer_id` IN (
		SELECT `id`
		FROM `#__jvotesystem_answers`
		WHERE `box_id`="'.$this->id.'"
		)';
			
		$this->_db->setQuery($sql);
		$this->_db->query();
	}
}//class
