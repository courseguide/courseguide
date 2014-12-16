<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class RssearchModelRssearch extends JModelLegacy
{
	public function __construct() {
		parent::__construct();
	}
	
	public function getModuleId() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select($db->qn('id'))
			->from($db->qn('#__modules'))
			->where($db->qn('module').' = '.$db->q('mod_rssearch'));
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	public function getPlugins() {
		$plugins = array('content' => 0, 'jevents' => 0, 'k2' => 0, 'kunena' => 0, 'rsblog' => 0, 'rseventspro' => 0, 'rsfiles' => 0, 'sobipro' => 0);
		
		if (!$this->is30()) {
			$plugins['eventlist'] = 0;
			$plugins['rsevents'] = 0;
			$plugins['rsmembership'] = 0;
			$plugins['mosets'] = 0;
			$plugins['virtuemart'] = 0;
		}
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/content/content.php'))
			$plugins['content'] = 1;
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/jevents/jevents.php'))
			$plugins['jevents'] = 1;
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/k2/k2.php'))
			$plugins['k2'] = 1;
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/kunena/kunena.php'))
			$plugins['kunena'] = 1;
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/rsblog/rsblog.php'))
			$plugins['rsblog'] = 1;
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/rseventspro/rseventspro.php'))
			$plugins['rseventspro'] = 1;
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/rsfiles/rsfiles.php'))
			$plugins['rsfiles'] = 1;
		
		if (file_exists(JPATH_SITE.'/plugins/rssearch/sobipro/sobipro.php'))
			$plugins['sobipro'] = 1;
		
		if (!$this->is30()) {
			if (file_exists(JPATH_SITE.'/plugins/rssearch/eventlist/eventlist.php'))
				$plugins['eventlist'] = 1;
			
			if (file_exists(JPATH_SITE.'/plugins/rssearch/rsevents/rsevents.php'))
				$plugins['rsevents'] = 1;
			
			if (file_exists(JPATH_SITE.'/plugins/rssearch/rsmembership/rsmembership.php'))
				$plugins['rsmembership'] = 1;
			
			if (file_exists(JPATH_SITE.'/plugins/rssearch/mosets/mosets.php'))
				$plugins['mosets'] = 1;
			
			if (file_exists(JPATH_SITE.'/plugins/rssearch/virtuemart/virtuemart.php'))
				$plugins['virtuemart'] = 1;
		}
		
		return $plugins;
	}
	
	protected function is30() {
		return version_compare(JVERSION, '3.0', '>=');
	}
}