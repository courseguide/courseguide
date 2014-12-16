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
class jVoteSystemControllerBoxen extends jVoteSystemController
{
    function __construct()
    {
        parent::__construct();
		
		//-- Register Extra tasks
        $this->registerTask('add', 'edit');
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask('publish', 'publish');
		$this->registerTask('loadAnswers', 'loadAnswers');
    }// function

	 function unpublish() {
		$model = $this->getModel('box');
		
		if($model->unpublish())
        {
            $msg = JText::_('PUBLISHSTATE_CHANGED');
        }
        else
        {
            $msg = JText::_('ERROR_PUBLISHSTATE_CHANGING');
        }

        $link = 'index.php?option=com_jvotesystem&view=boxen';
        $this->setRedirect($link, $msg);
	}
	
	function publish() {
		$model = $this->getModel('box');
		
		if($model->publish())
        {
            $msg = JText::_('PUBLISHSTATE_CHANGED');
        }
        else
        {
            $msg = JText::_('ERROR_PUBLISHSTATE_CHANGING');
        }

        $link = 'index.php?option=com_jvotesystem&view=boxen';
        $this->setRedirect($link, $msg);
	}
   
	/**
     * display the edit form
     * @return void
     */
    function edit()
    {
        JRequest::setVar('view', 'box');
		JRequest::setVar('model', 'box');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }// function

    function loadAnswers() {
		$model = $this->getModel('box');
		
		JRequest::setVar('view', 'answers');
		JRequest::setVar('controller', 'answers');
		JRequest::setVar('model', 'answers');
		JRequest::setVar('bid', $model->getId());
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
	}
	
	function goToAnswers()
	{
		$model = $this->getModel('box');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }
		
		$link = 'index.php?option=com_jvotesystem&view=answers&bid='.$model->getID();
		$this->setRedirect($link, $msg);
	}
	
	function apply()
    {
        $model = $this->getModel('box');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }

		JRequest::setVar('id', $model->getId());
        JRequest::setVar('view', 'box');
		JRequest::setVar('model', 'box');
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }// function
	
    function save()
    {
        $model = $this->getModel('box');
		
		if($model->store())
        {
            $msg = JText::_('Record_Saved');
        }
        else
        {
            $msg = JText::_('Error_Saving_Record');
        }
		
		/*Assistant*/
		$withAssistant = JRequest::getBool('assistant', false);
		if($withAssistant) {
			echo $msg;
			exit();
		} else {
			$link = 'index.php?option=com_jvotesystem&view=boxen';
			$this->setRedirect($link, $msg);
		}
    }// function

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel('box');
        if(!$model->delete()){
            $msg = JText::_('ERROR_ONE_OR_MORE_RECORDS_COULD_NOT_BE_DELETED');
        } else {
            $msg = JText::_('Records_Deleted');
        }

        $this->setRedirect('index.php?option=com_jvotesystem&view=boxen', $msg);
    }// function

    /**
     * cancel editing a record
     * @return void
     */
    function cancel()
    {
        $msg = JText::_('Operation_Cancelled');
        $this->setRedirect('index.php?option=com_jvotesystem&view=boxen', $msg);
    }//function
	
	function saveorderboxen()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(0), 'post', 'array' );
		JArrayHelper::toInteger($order, array(0));

		$model = $this->getModel('boxen');
		$model->saveorder($cid, $order);

		$msg = 'New ordering saved';
		$this->setRedirect('index.php?option=com_jvotesystem&view=boxen', $msg);
	}
	
	function resetVotes() {
		$model = $this->getModel('box');
		
		$model->resetVotes();
		
		$msg = JText::_('JVS_Votes_resetted');
		$this->setRedirect('index.php?option=com_jvotesystem&view=boxen', $msg);
	}
	
	function resetColors() {
		$model = $this->getModel('box');
		
		VBGeneral::getInstance()->resetColorsPoll($model->getId());		
		
		$msg = JText::_('JVS_Colors_resetted');
		$this->setRedirect('index.php?option=com_jvotesystem&view=boxen', $msg);
	}
	
	function exportData() {
		$model = $this->getModel('box');
		
		if(!VBExport::getInstance()->exportPollData( $model->getId(), substr(JRequest::getString("exportFormat", "xls"), 1) )) {
			$msg = JText::_('JVS_Failed_to_export_data');
			$this->setRedirect('index.php?option=com_jvotesystem&view=boxen', $msg);
		}
	}

}//class
