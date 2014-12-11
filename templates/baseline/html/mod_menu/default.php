<?php
/*======================================================================*\
|| #################################################################### ||
|| # Package - Joomla Template based on YJSimpleGrid Framework          ||
|| # Copyright (C) 2010  Youjoomla.com. All Rights Reserved.            ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        ||
|| # bound by Proprietary License of Youjoomla.com                      ||
|| # for more information visit http://www.youjoomla.com/license.html   ||
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         ||
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/
// No direct access.
defined('_JEXEC') or die;
$getapps= & JFactory::getApplication();
$template = $getapps->getTemplate();
$menu_path = YJSGPATH."html".DIRECTORY_SEPARATOR."mod_menu".DIRECTORY_SEPARATOR;
require($menu_path."yjsg_menuswitch.php");

/*yjmega*/ 
if ($params->get('class_sfx') =='nav' || $params->get('class_sfx') =='navd' || $params->get('class_sfx') =='split') {
		
		require( $menu_path."yjsgmega".DIRECTORY_SEPARATOR."default.php");
		
/*bootstrap pill*/ 
}elseif($params->get('class_sfx') =='nav nav-pills'){
		
		require( $menu_path."bootstrappill".DIRECTORY_SEPARATOR."default.php");
		
/*bootstrap navbar*/ 		
}elseif($params->get('class_sfx') =='navbar' || $params->get('class_sfx') == 'navbar navbar-inverse'){
		
		require( $menu_path."bootstrapnavbar".DIRECTORY_SEPARATOR."default.php");
		
/*yjsgacc*/ 		
}elseif($params->get('class_sfx') =='yjsgacc'){
		
		require( $menu_path."yjsgacc".DIRECTORY_SEPARATOR."default.php");
		
/*joomla default*/ 		
}else{
		
		require( $menu_path."joomladefault".DIRECTORY_SEPARATOR."default.php");
}
?>