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
class jVoteSystemModelUsers extends JModel
{

	var $_data, $_total, $_pagination, $id;

    function __construct()
    {
        parent::__construct();

        $mainframe = JFactory::getApplication();

		$this->setId();
		
		$limit      = $mainframe->getUserStateFromRequest( 'com_jvotesystem'.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
    	$limitstart = $mainframe->getUserStateFromRequest( 'com_jvotesystem'.JRequest::getCmd( 'view').'.limitstart', 'limitstart', 0, 'int' );
		if($limitstart > $this->getTotal()) $limitstart = 0;
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
    }//function

    function setId($bid = null) {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) $this->id = JRequest::getInt('id', 0);
        else $this->id = $cid[0];
	}
	
    function _buildQuery()
    {
        $query = 'SELECT u.`id`, u.`jid`, u.`ip`, u.`registered_time`, MAX(s.`lastVisitDate`) AS lastVisitDate, u.`blocked` '
        . ' FROM `#__jvotesystem_users` AS u '
        . ' LEFT JOIN `#__jvotesystem_sessions` AS s ON (s.`user_id`= u.`id`) '
        . ' GROUP BY u. `id` '; 

        return $query;
    }

    /**
     * Retrieves the hello data
     * @return array Array of objects containing the data from the database
     */
    function getData()
    {
        //-- Load the data if it doesn't already exist
        if(empty($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }//function

    function getTotal()
    {
        //-- Load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = 'SELECT u.`id` '
		          . ' FROM `#__jvotesystem_users` AS u '; 
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }//function

    function getPagination()
    {
        //-- Load the content if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_pagination;
    }//function
	
	function block() {
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->blocked = 1;
		
		$this->_db->updateObject('#__jvotesystem_users',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function unblock() {
		$ins = new JObject();
		$ins->id = $this->id;
		$ins->blocked = 0;
		
		$this->_db->updateObject('#__jvotesystem_users',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function delete() {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) return false;
		foreach($cid AS $id) {
			$sql = 'DELETE FROM `#__jvotesystem_users` '
			. ' WHERE `id` = '.$id
			. ' LIMIT 1'; 
			$this->_db->setQuery($sql);
			$this->_db->query();
			if($this->_db->getErrorMsg()) return false;
			//Votes löschen
			$sql = 'DELETE FROM `#__jvotesystem_votes` '
			. ' WHERE `user_id` = '.$id; 
			$this->_db->setQuery($sql);
			$this->_db->query();
			if($this->_db->getErrorMsg()) return false;
		}
		return true;
	}

}//class
