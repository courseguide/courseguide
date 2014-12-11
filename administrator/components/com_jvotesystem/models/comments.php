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

class jVoteSystemModelComments extends JModel
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
		$this->_filter->aid = JRequest::getInt('filter_aid', $session->get('aid', '', 'jVS_Admin_Filter'));
		$this->_filter->cid = JRequest::getInt('filter_cid', $session->get('cid', '', 'jVS_Admin_Filter'));
		$this->_filter->search = JRequest::getString('filter_search', $session->get('comments_search', '', 'jVS_Admin_Filter'));
		
		$session->set('bid', $this->_filter->bid, 'jVS_Admin_Filter'); 
		$session->set('aid', $this->_filter->aid, 'jVS_Admin_Filter');
		$session->set('cid', $this->_filter->cid, 'jVS_Admin_Filter');
		$session->set('comments_search', $this->_filter->search, 'jVS_Admin_Filter');
		
		//Kategorien holen
		$this->_filter->categories = $this->category->getCategories();
		//Umfragen holen
		$sql = "SELECT `id`, `title` FROM `#__jvotesystem_boxes` WHERE `published` >= 0 ";
		if($this->_filter->cid != "") $sql .= 'AND `catid`="'.$this->_filter->cid.'" ';
		$this->_db->setQuery($sql);
		$this->_filter->boxen = $this->_db->loadObjectList();
			//Umfragen-ID überprüfen
			$found = false;
			if(!empty($this->_filter->boxen)) {
				foreach($this->_filter->boxen AS $box) {
					if($box->id == $this->_filter->bid) {
						$found = true;
						break;
					}
				}
			}
			if($found == false) $this->_filter->bid = '';
		//Antworten holen
		$sql = "SELECT a.`id`, IF(LENGTH(`answer`) > 80, CONCAT(LEFT(`answer`, 77), '...'), `answer`) AS answer FROM `#__jvotesystem_answers` AS a, `#__jvotesystem_boxes` AS b WHERE b.`id`=a.`box_id` ";
		if($this->_filter->cid != "") $sql .= 'AND b.`catid`="'.$this->_filter->cid.'" ';
		if($this->_filter->bid != "") $sql .= 'AND `box_id`="'.$this->_filter->bid.'" ';
		$this->_db->setQuery($sql);
		$this->_filter->answers = $this->_db->loadObjectList();
			//Antworten-ID überprüfen
			$found = false;
			if(!empty($this->_filter->answers)) {
				foreach($this->_filter->answers AS $answer) {
					if($answer->id == $this->_filter->aid) {
						$found = true;
						break;
					}
				}
			}
			if($found == false) $this->_filter->aid= '';
	}
	
	function getFilter() {
		return $this->_filter;
	}
	
    function _buildQuery()
    {
       $sql = 'SELECT c. *, b.`catid` AS catid, b.`title` AS poll, b.`id` AS pid, IF(LENGTH(`answer`) > 18, CONCAT(LEFT(`answer`, 15), "..."), `answer`) AS answer, a.`id` AS aid '
		. ' FROM `#__jvotesystem_boxes` AS b , `#__jvotesystem_comments` AS c '
        . ' LEFT JOIN `#__jvotesystem_answers` AS a ON ( a. `id` = c. `answer_id` ) '
        . ' WHERE b.`id`=a.`box_id` AND b.`published` >= 0 AND c.`comment` LIKE "%'.$this->_filter->search.'%" ';
		if($this->_filter->aid != '') $sql .= ' AND c.`answer_id`="'.$this->_filter->aid.'" ';
		if($this->_filter->bid != '') $sql .= ' AND a.`box_id`="'.$this->_filter->bid.'" ';
		if($this->_filter->cid != '') $sql .= ' AND b.`catid`="'.$this->_filter->cid.'" ';
		$sql .= ' GROUP BY c. `id` '
		. ' ORDER BY c.`created` DESC '; //echo str_replace("#__", "jos_", $sql);
        return $sql;
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