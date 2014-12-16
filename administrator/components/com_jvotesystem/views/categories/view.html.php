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

jimport('joomla.application.component.view');

class jVoteSystemViewCategories extends JView
{
    function display($tpl = null)
    {
    	$this->category =& VBCategory::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->general =& VBGeneral::getInstance();
		
		JToolBarHelper::addNewX();
        JToolBarHelper::editListX();
        JToolBarHelper::deleteList();

        //-- Get data from the model
        $items = $this->category->getCategories();
        $pagination = $this->get("Pagination");
        
        $accesslevels = $this->access->getViewLevels();

        //-- Push data into the template
        $this->assignRef('items', $items);
        $this->assignRef('accesslevels', $accesslevels);
        $this->assignRef('pagination', $pagination);

        parent::display($tpl);
    }//function

}//class
