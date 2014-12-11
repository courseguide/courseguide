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
class jVoteSystemModelBoxenList extends JModel
{

	var $data;

    function __construct()
    {
        parent::__construct();
    }//function

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */
    function _buildQuery()
    {
        $query = 'SELECT b. * , COUNT( a. `id` ) AS answers, c.`title` AS cattitle '
        . ' FROM `#__jvotesystem_boxes` AS b '
        . ' LEFT JOIN `#__jvotesystem_answers` AS a ON ( b. `id` = a. `box_id`) '
		. ' ,`#__jvotesystem_categories` AS c'
		. ' WHERE c.`id`=b.`catid`'
        . ' GROUP BY b. `id` '; 

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
			$this->_db->setQuery($query);
            $this->_data = $this->_db->loadObjectList();
        }

        return $this->_data;
    }//function
}//class
