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
class jVoteSystemControllerCategories extends jVoteSystemController
{
    function __construct()
    {
        parent::__construct();
        
        $this->registerTask('add', 'edit');
    }// function
    
    function edit()
    {
    	JRequest::setVar('view', 'category');
    	JRequest::setVar('model', 'category');
    	JRequest::setVar('controller', 'categories');
    	JRequest::setVar('hidemainmenu', 1);
    
    	parent::display();
    }// function

	function save() {
		$model = $this->getModel('category');
		
		if($model->store())
        {
            $msg = JText::_('category_SAVED');
        }
        else
        {
            $msg = JText::_('ERROR_category_SAVING');
        }

        $link = 'index.php?option=com_jvotesystem&view=categories';
        $this->setRedirect($link, $msg);
	}	
	
	function apply() {
		$model = $this->getModel('category');
		
		if($model->store())
        {
            $msg = JText::_('CATEGORY_SAVED');
        }
        else
        {
            $msg = JText::_('ERROR_CATEGORY_SAVING');
        }
        
        $this->setRedirect('index.php?option=com_jvotesystem&view=category&id='.$model->getID(), $msg);
	}	
	
	function publish() {
		$model = $this->getModel('category');
		
		if($model->publish())
        {
            $msg = JText::_('PUBLISHSTATE_CHANGED');
        }
        else
        {
            $msg = JText::_('ERROR_PUBLISHSTATE_CHANGING');
        }

        $link = 'index.php?option=com_jvotesystem&view=categories';
        $this->setRedirect($link, $msg);
	}	
	
	function unpublish() {
		$model = $this->getModel('category');
		
		if($model->unpublish())
        {
            $msg = JText::_('PUBLISHSTATE_CHANGED');
        }
        else
        {
            $msg = JText::_('ERROR_PUBLISHSTATE_CHANGING');
        }

        $link = 'index.php?option=com_jvotesystem&view=categories';
        $this->setRedirect($link, $msg);
	}

    function remove()
    {
        $model = $this->getModel('category');
        if(!$model->delete()){
            $msg = JText::_('ERROR_ONE_OR_MORE_RECORDS_COULD_NOT_BE_DELETED');
        } else {
            $msg = JText::_('Records_Deleted');
        }
		
        $this->setRedirect('index.php?option=com_jvotesystem&view=categories', $msg);
    }// function

    function cancel()
    {
        $msg = JText::_('Operation_Cancelled');
        $this->setRedirect('index.php?option=com_jvotesystem&view=categories', $msg);
    }//function
    
    function orderup() {
    	$model = $this->getModel('category');
    	if(!$model->move(-1)){
    		$msg = JText::_('ERROR_ONE_OR_MORE_RECORDS_COULD_NOT_BE_MOVED');
    	} else {
    		$msg = JText::_('Records_Moved');
    	}
    	
    	$this->setRedirect('index.php?option=com_jvotesystem&view=categories', $msg);
    }
    
    function orderdown() {
    	$model = $this->getModel('category');
    	if(!$model->move(1)){
    		$msg = JText::_('ERROR_ONE_OR_MORE_RECORDS_COULD_NOT_BE_MOVED');
    	} else {
    		$msg = JText::_('Records_Moved');
    	}
    	 
    	$this->setRedirect('index.php?option=com_jvotesystem&view=categories', $msg);
    }
}//class
