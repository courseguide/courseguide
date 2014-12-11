<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- Kein Zugang
defined('_JEXEC') or die('=;)');

jimport('joomla.application.component.controller');
require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'classes'.DS.'loader.php';

class jVoteSystemController extends JController
{
    function display()
    {
        parent::display();
		
		$this->vbparams =& VBParams::getInstance();
		$this->general =& VBGeneral::getInstance();
		$this->template =& VBTemplate::getInstance();
		
		$loader = VBLoader::getInstance();
		$loader->loadLanguageFiles();
    }//function

}//class
