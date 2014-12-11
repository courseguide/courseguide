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

/**
 *  Require the base controller.
 */
require_once JPATH_COMPONENT.DS.'controller.php';

//-- Require specific controller if requested
if($controller = JRequest::getCmd('controller'))
{
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';

    if(file_exists($path))
    {
        require_once $path;
    }
    else
    {
        $controller = '';
    }
}

//-- Create the controller
$classname = 'jVoteSystemController'.$controller;
$controller = new $classname();

$document =& JFactory::getDocument();

require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'connect.php';
$jvs = jVSConnect::getInstance();
VBLoader::getInstance()->loadLanguageFiles(false);
$jvs->get("Loader")->loadJSConfig();

$lib =& joomessLibrary::getInstance();
$lib->jQuery();
$lib->js('components/com_jvotesystem/assets/js/jvotesystem.js');
$lib->js('administrator/components/com_jvotesystem/assets/js/general.js');

$lib->css('administrator/components/com_jvotesystem/assets/css/icons.css');
$lib->css('administrator/components/com_jvotesystem/assets/css/general.css', null, true);
$lib->css('components/com_jvotesystem/assets/css/general.css');

JHTML::_( 'behavior.modal' );
    	
//-- Perform the Request task
$controller->execute(JRequest::getCmd('task'));

//-- Redirect if set by the controller
$controller->redirect();

function addSub($title, $v, $controller = null, $image = null) {
	
	$enabled = false;
	$view = JRequest::getWord("view", 'jvotesystem');
	if($view == $v) {
		$img = $v;
		if($image != null) $img = $image;
		JToolBarHelper::title(   JText::_( $title).' - '.( 'jVoteSystem' ), $img.'.png' );
		$enabled = true;
	}
	$link = 'index.php?option=com_jvotesystem&view='.$v;
	if($controller != null) $link .= '&controller='.$controller;
	JSubMenuHelper::addEntry( JText::_($title), $link, $enabled);
}
