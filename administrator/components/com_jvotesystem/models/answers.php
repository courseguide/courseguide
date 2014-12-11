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
jimport( 'joomla.database.database' );

class jVoteSystemModelAnswers extends JModel
{

	var $_data, $_total, $_pagination, $_filter;

    function __construct()
    {
        parent::__construct();
        $this->category =& VBCategory::getInstance();
        $mainframe = JFactory::getApplication();
	   
		$this->loadFilter();

    	$limit      = $mainframe->getUserStateFromRequest( 'com_jvotesystem'.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
    	$limitstart = $mainframe->getUserStateFromRequest( 'com_jvotesystem'.JRequest::getCmd( 'view').'.limitstart', 'limitstart', 0, 'int' );
		if($limitstart > $this->getTotal()) $limitstart = 0;
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
    }//function
	
	function loadFilter() {
		$this->_filter = new JObject();
		
		$session =& JFactory::getSession();
		
		$this->_filter->bid = JRequest::getInt('filter_bid', $session->get('bid', '', 'jVS_Admin_Filter'));
		$this->_filter->cid = JRequest::getInt('filter_cid', $session->get('cid', '', 'jVS_Admin_Filter'));
		$this->_filter->search = JRequest::getString('filter_search', $session->get('answers_search', '', 'jVS_Admin_Filter'));
		
		$session->set('bid', $this->_filter->bid, 'jVS_Admin_Filter');
		$session->set('cid', $this->_filter->cid, 'jVS_Admin_Filter');
		$session->set('answers_search', $this->_filter->search, 'jVS_Admin_Filter');
		
		//Kategorien holen
		$this->_filter->categories = $this->category->getCategories();
		//Umfragen holen
		$sql = "SELECT `id`, `title` FROM `#__jvotesystem_boxes` WHERE `published` >= 0 ";
		if($this->_filter->cid != "") $sql .= 'AND `catid`="'.$this->_filter->cid.'" ';
		$this->_db->setQuery($sql);
		$this->_filter->boxen = $this->_db->loadObjectList();
			//Umfragen-ID überprüfen
			$found = false;
			foreach($this->_filter->boxen AS $box) {
				if($box->id == $this->_filter->bid) {
					$found = true;
					break;
				}
			}
			if($found == false) $this->_filter->bid = '';
	}
	
	function getFilter() {
		return $this->_filter;
	}
	
    function _buildQuery()
    {
        $query = 'SELECT a.*, IFNULL(SUM(v.`votes`),0) AS votes, MAX(v.`voted_time`) AS lastvote, MIN(v.`voted_time`) AS firstvote, CommentCount.comments AS comments, b.`catid`, b.`title` AS poll, b.`id` AS pid'
        . ' FROM `#__jvotesystem_boxes` AS b, `#__jvotesystem_answers` AS a'
        . ' LEFT JOIN `#__jvotesystem_votes` AS v ON v.`answer_id`=a.`id`'
        . ' LEFT JOIN `#__jvotesystem_users` AS u ON (u.`id`=v.`user_id` AND u.`blocked`=0)'
		. ' LEFT JOIN( '
		. 'SELECT a. `id` , IFNULL(COUNT( c. `id` ),0) AS comments '
			. ' FROM `#__jvotesystem_answers` AS a '
			. ' LEFT JOIN `#__jvotesystem_comments` AS c ON ( a. `id` = c. `answer_id`'
			. ' ) ';
			if($this->_filter->bid != '') $query .= 'WHERE a.`box_id`="'.$this->_filter->bid.'"';
			$query .= ' GROUP BY a. `id` '
		. ' ) AS CommentCount ON CommentCount.`id`=a.`id`'
		. ' WHERE b.`id`=a.`box_id` AND b.`published` >= 0 AND a.`answer` LIKE "%'.$this->_filter->search.'%" ';
        if($this->_filter->bid != '') $query .= ' AND a.`box_id`="'.$this->_filter->bid.'" ';
		if($this->_filter->cid != '') $query .= ' AND b.`catid`="'.$this->_filter->cid.'" ';
        $query .= ' GROUP BY a.`id`'
        . ' ORDER BY a.`box_id`, votes DESC, lastvote ASC';
		
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
            $query = $this->_buildQuery();
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

}//class