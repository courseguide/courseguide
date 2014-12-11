<?php
/**
* @package Component jVoteSystem for Joomla! 1.5-2.5 - 2.5
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
class jVoteSystemModeljVoteSystem extends JModel
{
    function getBoxen()
    {
        $db =& JFactory::getDBO();

       $sql = 'SELECT * '
        . ' FROM `#__jvotesystem_boxes`'
        . ' WHERE `published` = 1'
        . ' AND `start_time`<NOW()'
        . ' AND (`end_time`>NOW() OR `end_time` = "0000-00-00 00:00:00")'
		. ' AND object_group = "com_jvotesystem"'
        . ' ORDER BY `ordering` ASC, `created` DESC'; 
        $db->setQuery($sql);
        $boxen = $db->loadObjectList();

        return $boxen;
    }//function

}//class

