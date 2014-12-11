<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');

class plgRSSearchContent extends JPlugin
{
	public function __construct( &$subject, $config ) {
		parent::__construct( $subject, $config );
	}
	
	public function getResults(&$results, $search, $params) {
		$db				= JFactory::getDbo();
		$app			= JFactory::getApplication();
	    $fields			= (array) $this->params->get('searchfields','');
	    $categories		= (array) $this->params->get('categories','');
	    $orderby		= $this->params->get('orderby','title');
	    $asc_desc		= $this->params->get('asc_desc','ASC');
		$tag			= JFactory::getLanguage()->getTag();
		$languageFilter	= '';

		JFactory::getLanguage()->load('plg_rssearch_content',JPATH_ADMINISTRATOR);
		
		require_once JPATH_SITE.'/components/com_content/helpers/route.php';
		
		require_once JPATH_ADMINISTRATOR . '/components/com_search/helpers/search.php';

		foreach($fields as $field) {
			if ($field == 'content') {
				$tablecolumns	= array('title','hits','ordering','created');
				$where_catid	= '';

				if (!in_array('', $categories)) {
					JArrayHelper::toInteger($categories);
					$where_catid = ' AND '.$db->qn('a.catid').' IN ('.implode(',', $categories).')';
				}
				
				if ($app->isSite() && $app->getLanguageFilter()) {
					$languageFilter = ' AND '.$db->qn('a.language').' IN ('.$db->q($tag).','.$db->q('*').') AND '.$db->qn('c.language').' IN ('.$db->q($tag).','.$db->q('*').') ';
				}
				
				$query = 'SELECT '.$db->qn('a.id').', '.$db->qn('a.title').', '.$db->qn('a.alias').', '.$db->qn('a.catid').', '.$db->qn('a.introtext').', '.$db->qn('a.fulltext').', '.$db->qn('c.alias','categoryname').' FROM '.$db->qn('#__content','a').' LEFT JOIN '.$db->qn('#__categories','c').' ON '.$db->qn('a.catid').' = '.$db->qn('c.id').' WHERE ('.$db->qn('a.title').' LIKE '.$db->q('%'.$search.'%').' OR '.$db->qn('a.introtext').' LIKE '.$db->q('%'.$search.'%').' OR '.$db->qn('a.fulltext').' LIKE '.$db->q('%'.$search.'%').') '.$languageFilter.' AND '.$db->qn('a.state').' = 1 '.$where_catid.' GROUP BY '.$db->qn('a.id').'';
				
				if (in_array($orderby,$tablecolumns))
					$query .= ' ORDER BY '.$db->qn('a.'.$orderby).' '.$db->escape($asc_desc);

				$db->setQuery($query);
				if ($list = $db->loadObjectList()) {
					foreach ($list as $item) {
						if ( !searchHelper::checkNoHTML($item, $search, array('title', 'introtext', 'fulltext')) ) continue;

						$tmp		= new stdClass();
						$tmp->title	= $item->title;
						$tmp->link	= JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid));
						$tmp->text	= $item ->introtext;
						$tmp->type	= JText::_('RSF_CONT_ARTICLE');
						$results[]	= $tmp;
					}
				}
				
			} else if ($field == 'categories') {
				$tablecolumns	= array('title','ordering');
				$where_id		= '';
				
				if (!in_array('', $categories)) {
					JArrayHelper::toInteger($categories);
					$where_id = ' AND '.$db->qn('id').' IN ('.implode(',', $categories).') AND '.$db->qn('extension').' = '.$db->q('com_content').' ';
				}
				
				if ($app->isSite() && $app->getLanguageFilter()) {
					$languageFilter = ' AND '.$db->qn('language').' IN ('.$db->q($tag).','.$db->q('*').') ';
				}
				
				$query = 'SELECT '.$db->qn('id').', '.$db->qn('title').', '.$db->qn('alias').', '.$db->qn('description').' FROM '.$db->qn('#__categories').' WHERE ('.$db->qn('title').' LIKE '.$db->q('%'.$search.'%').' OR '.$db->qn('description').' LIKE '.$db->q('%'.$search.'%').') '.$languageFilter.' AND '.$db->qn('published').' = 1 '.$where_id.' GROUP BY '.$db->qn('id').', '.$db->qn('title').', '.$db->qn('description');
				
				if (in_array($orderby,$tablecolumns))
					$query .= ' ORDER BY '.$db->qn($orderby).' '.$db->escape($asc_desc);
				
				
				$db->setQuery($query);
				if ($list = $db->loadObjectList()) {
					foreach ($list as $item) {
						if ( !searchHelper::checkNoHTML($item, $search, array('title', 'description')) ) continue;

						$tmp		= new stdClass();
						$tmp->title = $item->title;
						$tmp->link 	= JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));
						$tmp->text 	= $item->description;
						$tmp->type	= JText::_('RSF_CONT_CATEGORY');
						$results[] 	= $tmp;
					}
				}
			}
		}
		
	    return true;
	}
}