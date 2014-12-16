<?php
/*
# ------------------------------------------------------------------------
# Extensions for Joomla 2.5.x - Joomla 3.x
# ------------------------------------------------------------------------
# Copyright (C) 2011-2013 Ext-Joom.com. All Rights Reserved.
# @license - PHP files are GNU/GPL V2.
# Author: Ext-Joom.com
# Websites:  http://www.ext-joom.com 
# Date modified: 03/09/2013 - 13:00
# ------------------------------------------------------------------------
*/

// No direct access
defined('_JEXEC') or die;

$document 			= JFactory::getDocument(); 
$ext_id				= $params->get('ext_id', 'id');
$api_id 			= (int)$params->get('api_id');
$poll_id 			= $params->get('poll_id');
$width 				= (int)$params->get('width', 300);
$script_vk			= (int)$params->get('script_vk');
$moduleclass_sfx	= $params->get('moduleclass_sfx');

if ( $script_vk == 0 ) {	
	$document->addCustomTag('<script src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript" charset="utf-8"></script>');
}
	
require JModuleHelper::getLayoutPath('mod_ext_vk_poll', $params->get('layout', 'default'));
/*echo JText::_(COP_JOOMLA);*/
?>

