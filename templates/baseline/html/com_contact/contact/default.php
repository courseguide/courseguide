<?php
 /**
 * $Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if(intval(JVERSION) >= 3 || JPluginHelper::getPlugin('system', 'JBootstrap')) {
	include 'default30.php';
}else{
	include 'default25.php';
}
?>