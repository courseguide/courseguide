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

class jVoteSystemController extends JController
{
    /**
     * Method to display the view
     *
     * @access	public
     */
    function display()
    {
		
		//Create Submenu
		addSub( 'Overview', 'jvotesystem');
		addSub( 'Categories', 'categories');
		addSub( 'Boxen', 'boxen');
		addSub( 'Answers', 'answers', 'answers');
		addSub( 'Comments', 'comments', 'comments');
		addSub( 'Users', 'users', 'users');
		addSub( 'BBCodes', 'bbcodes', 'bbcodes', "generic");
		addSub( 'Logs', 'logs');
		addSub( 'JVS_ADV_ADVISOR', 'advisor', 'advisor');
		addSub( 'JVS_API_KEYS', 'apikeys', 'apikeys');
		//Dateien laden
		VBUser::getInstance();
		VBParams::getInstance();
		
		parent::display();
    }// function

}// class
