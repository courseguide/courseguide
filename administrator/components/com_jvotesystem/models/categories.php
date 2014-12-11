<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @author Johannes Me�mer
 * @copyright (C) 2010- Johannes Me�mer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport('joomla.application.component.model');


class jVoteSystemModelCategories extends JModel
{

    var $_data;

    /**
     * Items total
     * @var integer
     */
    var $_total = null;

    /**
     * Pagination object
     * @var object
     */
    var $_pagination = null;

    function __construct()
    {
        parent::__construct();

        $app = JFactory::getApplication('administrator');
        
        $this->category =& VBCategory::getInstance();
        //$this->filter = self::getFilter();
    }//function
    
    /*function getFilter() {
	    if(empty($this->filter)) {
	    	$session =& JFactory::getSession();
	    
	    	$this->filter = array();
	    	$this->filter["section"] = JRequest::getString("filter_section", $session->get("filter_section", "", "SG"));
	        		
	        //In Session speichern
		    $session->set("filter_section", $this->filter["section"], "SG");
	   	}
	    return $this->filter;
    }*/

    function getData()
    {
        //-- Lets load the data if it doesn't already exist
        if (empty( $this->_data ))
        {
        	$query = $this->category->getCategoriesQuery();
            $data = $this->_getList($query);
            
            $this->_data = array();
            $this->category->_getChilds($this->_data, $data, 0);
        }

        return $this->_data;
    }//function

    function getTotal()
    {
        //-- Load the content if it doesn't already exist
        if(empty($this->_total))
        {
            $query = $this->category->getCategoriesQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }//function

    function getPagination()
    {
        //-- Load the pagination object if it doesn't already exist
        if(empty($this->_pagination))
        {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), 0, 0);
        }

        return $this->_pagination;
    }//function

}// class
