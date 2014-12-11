<?php
/**
* @version 1.2.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class RssearchModelResults extends JModelLegacy {
	
	protected $_total=null;
	
	public function __construct() {
		parent::__construct();
		
		$app		= JFactory::getApplication();
		$limit		= $app->getUserStateFromRequest('com_rssearch.results.limit', 'limit', $app->getCfg('list_limit'));
		$limitstart = $app->input->getInt('limitstart', 0);

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getResults() {
		jimport('joomla.plugin.helper');
		
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		$mid		= JFactory::getApplication()->input->getInt('module_id',0);
		$search		= JFactory::getApplication()->input->getString('search','');
		$results	= array();
		
		if (!$mid)
			exit();
		
		$query->select($db->qn('params'))->from($db->qn('#__modules'))->where($db->qn('id').' = '.$mid);
		$db->setQuery($query);
		$params = $db->loadResult();
		
		$registry = new JRegistry;
		$registry->loadString($params);
		
		$components = $registry->get('comps');
		
		// Get plugins
		JPluginHelper::importPlugin('rssearch');
		$dispatcher = JDispatcher::getInstance();

		if (!empty($components)) {
			
			if (!is_array($components)) {
				$components = (array) $components;
			}
			
			foreach ($components as $component) {
				if ($plugin = JPluginHelper::getPlugin('rssearch',$component)){
					$className = 'plgRSSearch'.$plugin->name;
					if(class_exists($className)) {
						$instance = new $className($dispatcher, (array)$plugin);
						$instance->getResults($results, $search, $registry);
					}
				}
			}
		}

		$this->_total = count($results);

		if($this->getState('limit') > 0){
			$results = array_slice($results, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $results;
	}
	
	public function getTotal() {
		return $this->_total;
	}
	
	public function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_pagination;
	}
	
	public function getWordsLimit() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$mid	= JFactory::getApplication()->input->getInt('module_id',0);
		
		if (!$mid) 
			exit();
		
		$query->select($db->qn('params'))->from($db->qn('#__modules'))->where($db->qn('id').' = '.$mid);
		$db->setQuery($query);
		$params = $db->loadResult();
		
		$registry = new JRegistry;
		$registry->loadString($params);
		
		return $registry->get('nr_words',80);
	}
	
	public function getType() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$mid	= JFactory::getApplication()->input->getInt('module_id',0);
		
		if (!$mid) 
			exit();
		
		$query->select($db->qn('params'))->from($db->qn('#__modules'))->where($db->qn('id').' = '.$mid);
		$db->setQuery($query);
		$params = $db->loadResult();
		
		$registry = new JRegistry;
		$registry->loadString($params);
		
		return $registry->get('show_type',1);
	}
}