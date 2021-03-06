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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jVoteSystem Component
 *
 * @package    jVoteSystem
 * @subpackage Views
 */

class jVoteSystemViewComments extends JView
{
    /**
     * jVoteSystemList view display method
     * @return void
     **/
    function display($tpl = null)
    {
    	$this->general =& VBGeneral::getInstance();
    	
        //Daten laden
		$filter = $this->get('Filter');
		$items = $this->get('Data');
		$pagination = $this->get('Pagination');
		
		//Toolbar
        JToolBarHelper::addNewX();
        JToolBarHelper::editListX();
        JToolBarHelper::deleteList();

        //Daten übergeben
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('filter', $filter);

        parent::display($tpl);
    }//function

}//class
