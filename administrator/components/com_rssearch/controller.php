<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class rssearchController extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
		
		$lang = JFactory::getLanguage();
		
		$lang->load('com_rssearch', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('com_rssearch', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
		$lang->load('com_rssearch', JPATH_ADMINISTRATOR, null, true);
	}
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false) {
		JHTML::_('behavior.framework');
		JFactory::getDocument()->addStyleSheet(JURI::root(true).'/administrator/components/com_rssearch/assets/css/style.css?v=3');	
		
		if (!version_compare(JVERSION, '3.0', '>='))
			JFactory::getDocument()->addStyleSheet(JURI::root(true).'/administrator/components/com_rssearch/assets/css/j2.css?v=3');	
		
		parent::display();
		return $this;
	}
}