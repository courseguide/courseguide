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

class jVoteSystemViewLogs extends JView
{
    /**
     * jVoteSystem view display method
     *
     * @return void
     **/
    function display($tpl = null)
    {
		$this->general =& VBGeneral::getInstance();
		$this->log =& VBLog::getInstance();
		$user 		= & JFactory::getUser();
		
		$limit = JRequest::getInt('limit', 50);
		$limit_start = JRequest::getInt('limitstart', 0);
		$page = JRequest::getInt('page', ($limit_start+$limit)/$limit);
		
		//AJAX-Javascript file
		if($page == 1) {
			$lib =& JoomessLibrary::getInstance();
			$lib->js('components/com_jvotesystem/assets/js/jvotesystem.js');
			$lib->js("administrator/components/com_jvotesystem/assets/js/log.js");
		}
				
		//build Toolbar
		if(VBAccess::getInstance()->isUserAllowedToConfig()) 
			JToolBarHelper::preferences('com_jvotesystem', 500);
		
		$this->files = $this->log->getFiles();
		
		$curFile = JRequest::getString("file", null);
		if($curFile == null) $curFile = JFactory::getDate()->toFormat("%Y.%m").".php";
		
		$data = $this->log->getEntries($curFile, $limit, $page);
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination($this->log->total, ($page-1)*$limit, $limit);
		
		$this->assignRef("file", $curFile);
		$this->assignRef("data", $data);
		$this->assignRef('pagination', $pagination);
		
        parent::display($tpl);
    }//function
}//class
