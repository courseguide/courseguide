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
class jVoteSystemModelBoxen extends JModel
{

	var $_data, $_total, $_pagination, $_filter;

    function __construct()
    {
        parent::__construct();

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
		
		$this->_filter->cid = JRequest::getInt('filter_cid', $session->get('cid', '', 'jVS_Admin_Filter'));
		$this->_filter->search = JRequest::getString('filter_search', $session->get('polls_search', '', 'jVS_Admin_Filter'));
		
		$session->set('cid', $this->_filter->cid, 'jVS_Admin_Filter');
		$session->set('polls_search', $this->_filter->search, 'jVS_Admin_Filter');
		
		//Kategorien holen
		$this->_filter->categories = VBCategory::getInstance()->getCategories();
	}
	
	function getFilter() {
		return $this->_filter;
	}

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */
    function _buildQuery()
    {
        $query = 'SELECT b. * , COUNT( a. `id` ) AS answers, Stats.`votes`, CommentCount.`comments`, c.`title` AS cattitle '
        . ' FROM `#__jvotesystem_categories` AS c, `#__jvotesystem_boxes` AS b '
        . ' LEFT JOIN `#__jvotesystem_answers` AS a ON ( b. `id` = a. `box_id`) '
		. ' LEFT JOIN ( '
		. ' SELECT a.`box_id`, IFNULL(SUM(v.`votes`),0) AS votes'
        . ' FROM `#__jvotesystem_answers` AS a'
        . ' LEFT JOIN `#__jvotesystem_votes` AS v ON v.`answer_id`=a.`id`'
        . ' LEFT JOIN `#__jvotesystem_users` AS u ON (u.`id`=v.`user_id` AND u.`blocked`=0)'
		. ' GROUP BY a.`box_id`'
		. ' ) AS Stats ON (Stats.`box_id`=b.`id`)'
		. ' LEFT JOIN( '
			. '	SELECT a. `box_id` , IFNULL(COUNT( c. `id` ),0) AS comments '
			. ' FROM `#__jvotesystem_answers` AS a '
			. ' LEFT JOIN `#__jvotesystem_comments` AS c ON ( a. `id` = c. `answer_id`'
			. ' ) '
			. ' GROUP BY a. `box_id` '
		. ' ) AS CommentCount ON CommentCount.`box_id`=b.`id`'
		. ' WHERE c.`id`=b.`catid` AND b.`published` != -1 AND (b.`title` LIKE "%'.$this->_filter->search.'%" OR b.`question` LIKE "%'.$this->_filter->search.'%")';
		if($this->_filter->cid != '') $query .= ' AND b.`catid`="'.$this->_filter->cid.'" ';
        $query .= ' GROUP BY b. `id` ORDER BY ordering'; 

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
	
	function saveorder($cid = array(), $order)
	{
		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row = new JObject();
			$row->id = $cid[$i];
			$row->ordering = $order[$i];
			
			$this->_db->updateObject('#__jvotesystem_boxes', $row, 'id');
		}

		return true;
	}

}//class
