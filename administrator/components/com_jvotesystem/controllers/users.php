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
class jVoteSystemControllerUsers extends jVoteSystemController
{
    function __construct()
    {
        parent::__construct();
		
		//-- Register Extra tasks
        $this->registerTask('unblock', 'unblock');
		$this->registerTask('block', 'block');
    }// function

	 function unblock() {
		$model = $this->getModel('users');
		
		if($model->unblock())
        {
            $msg = JText::_('USER_UNBLOCKED');
        }
        else
        {
            $msg = JText::_('ERROR_USER_UNBLOCKING');
        }

        $link = 'index.php?option=com_jvotesystem&view=users&controller=users';
        $this->setRedirect($link, $msg);
	}
	
	function block() {
		$model = $this->getModel('users');
		
		if($model->block())
        {
            $msg = JText::_('USER_BLOCKED');
        }
        else
        {
            $msg = JText::_('ERROR_USER_BLOCKING');
        }

        $link = 'index.php?option=com_jvotesystem&view=users&controller=users';
        $this->setRedirect($link, $msg);
	}
	
	function remove()
    {
        $model = $this->getModel('users');
        if(!$model->delete()){
            $msg = JText::_('ERROR_ONE_OR_MORE_USERS_COULD_NOT_BE_DELETED');
        } else {
            $msg = JText::_('Users_Deleted');
        }

		$link = 'index.php?option=com_jvotesystem&view=users&controller=users';
        $this->setRedirect($link, $msg);
    }// function

}//class
