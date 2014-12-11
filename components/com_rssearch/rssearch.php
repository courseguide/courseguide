<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once JPATH_COMPONENT.'/legacy.php';
require_once JPATH_COMPONENT.'/controller.php';

JFactory::getDocument()->addStyleSheet(JURI::root(true).'/components/com_rssearch/assets/style.css');

$controller	= JControllerLegacy::getInstance('RSSearch');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();