<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

if (file_exists(JPATH_SITE.'/components/com_rssearch/rssearch.php')) {
	$document		= JFactory::getDocument();
	$class_suffix	= $params->get('moduleclass_sfx');
	$itemid			= $params->get('itemid', '');
	$show_btn		= $params->get('show_submit','yes');
	$limit			= $params->get('search_limit','5');
	$class_suffix 	= (empty($class_suffix)) ? '' : '_'.$class_suffix;
	$field_width 	= $params->get('field_width','210');
	$box_width		= (int) $params->get('results_box_width','210');
	$nr_words		= $params->get('nr_words','80');
	$show_loop		= $params->get('show_loop','no');
	
	JHtml::_('behavior.framework',true);
	$document->addStyleSheet(JURI::root(true).'/modules/mod_rssearch/assets/css/rssearch.css');
	
	require(JModuleHelper::getLayoutPath('mod_rssearch','default'));
} else{
	echo JText::_('RSF_MODULE_LIST_CHECK');
}