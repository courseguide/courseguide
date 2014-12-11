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

class jVoteSystemModelBbcodes extends JModel
{

	var $_data;

    function __construct()
    {
        parent::__construct();
    }//function
	
    function _buildQuery()
    {
        $sql = "SELECT * FROM `#__jvotesystem_bbcodes` ";
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
			$this->_db->setQuery($query);
            $this->_data = $this->_db->loadObjectList();
        }

        return $this->_data;
    }//function

}//class