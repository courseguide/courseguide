<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rssearch/controller.php';
require_once JPATH_SITE.'/components/com_rssearch/legacy.php';

$controller	= JControllerLegacy::getInstance('RSSearch');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();