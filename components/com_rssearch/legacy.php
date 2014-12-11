<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

if (version_compare(JVERSION, '2.5.0', '>=')) {
	
	jimport('joomla.application.component.model');
	jimport('joomla.application.component.view');
	jimport('joomla.application.component.controller');
	
	// Joomla! 2.5
	if (!class_exists('JModelLegacy')) {
		class JModelLegacy extends JModel { 
			public static function addIncludePath($path = '', $prefix = '') {
				return parent::addIncludePath($path, $prefix);
			}
		}
	}
	
	if (!class_exists('JViewLegacy')) {
		class JViewLegacy extends JView { }
	}
	
	if (!class_exists('JControllerLegacy')) {
		class JControllerLegacy extends JController { }
	}
}