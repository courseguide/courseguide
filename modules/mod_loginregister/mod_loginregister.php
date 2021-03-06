<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_loginregister
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the login functions only once
require_once __DIR__ . '/helper.php';

$params->def('greeting', 1);

$type	= modLoginregisterHelper::getType();
$return	= modLoginregisterHelper::getReturnURL($params, $type);
$user	= JFactory::getUser();
$layout = $params->get('layout', 'default');
$language = JFactory::getLanguage();
$language->load('mod_login');
// Logged users must load the logout sublayout
if (!$user->guest)
{
	$layout .= '_logout';
}

require JModuleHelper::getLayoutPath('mod_loginregister', $layout);
