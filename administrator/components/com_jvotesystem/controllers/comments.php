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
class jVoteSystemControllerComments extends jVoteSystemController
{
	var $aid;
	
    function __construct()
    {
        parent::__construct();
		//-- Register Extra tasks
        $this->registerTask('add', 'edit');
    }// function

	 function unpublish() {
		$model = $this->getModel('comment');
		
		if($model->unpublish())
        {
            $msg = JText::_('PUBLISHSTATE_CHANGED');
        }
        else
        {
            $msg = JText::_('ERROR_PUBLISHSTATE_CHANGING');
        }
		
		parent::display();
	}
	
	function publish() {
		$model = $this->getModel('comment');
		
		if($model->publish())
        {
            $msg = JText::_('PUBLISHSTATE_CHANGED');
        }
        else
        {
            $msg = JText::_('ERROR_PUBLISHSTATE_CHANGING');
        }
		
		 parent::display();
	}
   
	/**
     * display the edit form
     * @return void
     */
    function edit()
    {
        JRequest::setVar('view', 'comment');
		JRequest::setVar('model', 'comment');
		JRequest::setVar('controller', 'comments');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }// function
	
    function save()
    {
        $model = $this->getModel('comment');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }

        $link = 'index.php?option=com_jvotesystem&view=comments&controller=comments';
        $this->setRedirect($link, $msg);
    }// function
	
	function apply() {
		$model = $this->getModel('comment');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }
		
		JRequest::setVar('view', 'comment');
		JRequest::setVar('model', 'comment');
		JRequest::setVar('controller', 'comments');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('id', $model->getId());
		
		parent::display();
	}

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('comment');
        if(!$model->delete()){
            $msg = JText::_('ERROR_ONE_OR_MORE_RECORDS_COULD_NOT_BE_DELETED');
        } else {
            $msg = JText::_('Records_Deleted');
        }

        $this->setRedirect('index.php?option=com_jvotesystem&view=comments&controller=comments', $msg);
    }// function

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
		JRequest::setVar('view', 'comments');
		JRequest::setVar('controller', 'comments');
		JRequest::setVar('model', 'comments');
		
		parent::display();
    }//function

}//class
