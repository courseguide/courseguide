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
class jVoteSystemControllerBbcodes extends jVoteSystemController
{
	function __construct()
    {
        parent::__construct();
		//-- Register Extra tasks
        $this->registerTask('add', 'edit');
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('publish', 'publish');
    }// function

	 function unpublish() {
		$model = $this->getModel('bbcode');
		
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
		$model = $this->getModel('bbcode');
		
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
	
	function unWithButton() {
		$model = $this->getModel('bbcode');
		
		$model->setWithButton(0);
		
		parent::display();
	}
	
	function withButton() {
		$model = $this->getModel('bbcode');
		
		$model->setWithButton(1);
		
		 parent::display();
	}
	
	function copy() {
		$model = $this->getModel('bbcode');
		
		if($model->copy())
        {
            $msg = JText::_('ITEM COPIED');
        }
        else
        {
            $msg = JText::_('ERROR ITEM COPYING');
        }
		
		 parent::display();
	}
   
	/**
     * display the edit form
     * @return void
     */
    function edit()
    {
        JRequest::setVar('view', 'bbcode');
		JRequest::setVar('model', 'bbcode');
		JRequest::setVar('controller', 'bbcodes');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }// function
	
    function save()
    {
        $model = $this->getModel('bbcode');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }

        $link = 'index.php?option=com_jvotesystem&view=bbcodes&controller=bbcodes';
        $this->setRedirect($link, $msg);
    }// function
	
	function apply() {
		$model = $this->getModel('bbcode');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }
		
		JRequest::setVar('view', 'bbcode');
		JRequest::setVar('model', 'bbcode');
		JRequest::setVar('controller', 'bbcodes');
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
        $model = $this->getModel('bbcode');
        if(!$model->delete()){
            $msg = JText::_('ERROR_ONE_OR_MORE_RECORDS_COULD_NOT_BE_DELETED');
        } else {
            $msg = JText::_('Records_Deleted');
        }

        $this->setRedirect('index.php?option=com_jvotesystem&view=bbcodes&controller=bbcodes', $msg);
    }// function

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
		JRequest::setVar('view', 'bbcodes');
		JRequest::setVar('model', 'bbcodes');
		JRequest::setVar('controller', 'bbcodes');
		parent::display();
    }//function

}//class
