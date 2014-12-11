<?php
/**
 * ------------------------------------------------------------------------
 * JA Google Chart Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
defined('_JEXEC') or die('Restricted access');
 
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$basepath = JURI::root(true).'/modules/' . $module->module . '/asset/';

$doc->addStyleSheet($basepath.'style.css');
//load override css
$templatepath = 'templates/'.$app->getTemplate().'/css/'.$module->module.'.css';
if(file_exists(JPATH_SITE . '/' . $templatepath)) {
	$doc->addStyleSheet(JURI::root(true).'/'.$templatepath);
}

//Load the AJAX API
$doc->addScript('https://www.google.com/jsapi');
// Load the Visualization API and the corechart package.
$doc->addScriptDeclaration('google.load("visualization", "1", {packages: ["corechart"]});');
$doc->addScriptDeclaration('google.load("visualization", "1", {packages: ["geochart"]});');
//script
//$doc->addScript($basepath.'script.js');