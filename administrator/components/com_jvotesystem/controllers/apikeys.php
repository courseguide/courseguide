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

jimport('joomla.application.component.controller');

/**
 * jVoteSystem Controller
 *
 * @package    jVoteSystem
 * @subpackage Controllers
 */
class jVoteSystemControllerApiKeys extends jVoteSystemController
{
	function __construct()
    {
        parent::__construct();
		//-- Register Extra tasks
        $this->registerTask('add', 'edit');
    }
    
    function edit()
    {
        JRequest::setVar('view', 'apikey');
		JRequest::setVar('model', 'apikey');
		JRequest::setVar('controller', 'apikeys');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }// function

    function save()
    {
        $model = $this->getModel('apikey');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }

        $link = 'index.php?option=com_jvotesystem&view=apikeys&controller=apikeys';
        $this->setRedirect($link, $msg);
    }// function
	
	function apply() {
		$model = $this->getModel('apikey');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }
		
		JRequest::setVar('view', 'apikey');
		JRequest::setVar('model', 'apikey');
		JRequest::setVar('controller', 'apikeys');
        JRequest::setVar('layout', 'form');
		JRequest::setVar('id', $model->getId());
        JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('apikey');
        if(!$model->delete()){
            $msg = JText::_('ERROR_ONE_OR_MORE_RECORDS_COULD_NOT_BE_DELETED');
        } else {
            $msg = JText::_('Records_Deleted');
        }

        $this->setRedirect('index.php?option=com_jvotesystem&view=apikeys&controller=apikeys', $msg);
    }// function

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
		JRequest::setVar('view', 'apikeys');
		JRequest::setVar('controller', 'apikeys');
		JRequest::setVar('model', 'apikeys');
		
		parent::display();
    }//function

}//class
