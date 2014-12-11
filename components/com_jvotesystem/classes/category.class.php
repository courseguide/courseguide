<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

class VBCategory
{
	static $db, $document;

	private function __construct() {
		$this->db =& JFactory::getDBO();
		$this->document = & JFactory::getDocument();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBCategory();
		}
		return $instance;
	}
	
	var $category;
	function getCategory($id, $stats = false) { 
		$empty = array("alias" => "", "id" => 0, "title" => "", "published" => 0);
		if($id == 0) return JArrayHelper::toObject($empty);
		
		$kid = $id.'.'.$stats;
		
		if(empty($this->category)) $this->category = array();
		if(empty($this->category[$kid])) {
			$sql = 'SELECT c.*, IFNULL(COUNT(b.`id`), 0) AS `polls`';
			if($stats) $sql .= ', stats.*';
			$sql .= 'FROM `#__jvotesystem_categories` AS c LEFT JOIN `#__jvotesystem_boxes` AS b ON(b.`catid`=c.`id` AND b.`published` > -1)';
			if($stats) {
				$sql .= 'LEFT JOIN (
						SELECT b.catid, IFNULL(SUM(aStats.votes), 0) AS votes, IFNULL(SUM(cStats.comments), 0) AS comments
						FROM `#__jvotesystem_boxes` AS b
						LEFT JOIN (
							SELECT a.`box_id`, SUM(v.`votes`) AS votes
							FROM `#__jvotesystem_answers` AS a
							LEFT JOIN `#__jvotesystem_votes` AS v ON (v.`answer_id`=a.`id`)
							GROUP BY a.`box_id`
						) AS aStats ON(aStats.box_id=b.`id`)
						LEFT JOIN (
							SELECT a.`box_id`, COUNT(c.`id`) AS comments
							FROM `#__jvotesystem_answers` AS a
							LEFT JOIN `#__jvotesystem_comments` AS c ON (c.`answer_id`=a.`id`)
							GROUP BY a.`box_id`
						) AS cStats ON(cStats.box_id=b.`id`)
						GROUP BY b.catid
					) AS stats ON(stats.catid=c.`id`)';
			}	
			$sql .= ' WHERE ('.$this->getWhereSql().') AND c.`id`= '.$this->db->quote($id);
			$sql .= " GROUP BY c.`id` ";
			$this->db->setQuery($sql);
			$this->category[$kid] = $this->db->loadObject();
			if(!isset($this->category[$kid]->id)) return JArrayHelper::toObject($empty);
			
			$this->category[$kid]->title = JText::_($this->category[$kid]->title);
			foreach(json_decode($this->category[$kid]->params) AS $key => $param) {
				$this->category[$kid]->$key = $param;
			}
			$this->category[$kid]->access = json_decode($this->category[$kid]->access); 
		}
		return $this->category[$kid];
	}
	
	var $categoryChilds;
	function getCategoryChilds($id, $level = 1) {
		if(empty($this->categoryChilds)) $this->categoryChilds = array();
		if(empty($this->categoryChilds[$id])) {
			$sql = 'SELECT c.`id`'
			. ' FROM `#__jvotesystem_categories` AS c '
			. ' WHERE ('.$this->getWhereSql().') AND `parent_id`= '.$this->db->quote($id);
			$sql .= ' ORDER BY c.`order` ASC';
			$this->db->setQuery($sql); 
			$data = $this->db->loadObjectList();
			$this->categoryChilds[$id] = JArrayHelper::getColumn($data, "id");			
		}
		if($level > 1 || $level <= 0) {
			$cats = $this->categoryChilds[$id];
			foreach($this->categoryChilds[$id] AS $child) {
				$cats = array_merge($cats, $this->getCategoryChilds($child, $level--));
			}
			return $cats;
		}
		
		return $this->categoryChilds[$id];
	}
	
	function getCategoriesQuery() {
		$sql = 'SELECT c.*, IFNULL(COUNT(b.`id`), 0) AS `polls`
				FROM `#__jvotesystem_categories` AS c LEFT JOIN `#__jvotesystem_boxes` AS b ON(b.`catid`=c.`id` AND b.`published` > -1) 
				WHERE 1=1 AND ('.$this->getWhereSql().') 
				GROUP BY c.`id`';
		$sql .='ORDER BY `parent_id` ASC, `order` ASC';
		
		return $sql;
	}
	
	function getWhereSql() {
		$access = VBAccess::getInstance();
		if(!$access->isUserAllowedToConfig()) $sql = " c.`published`=1 AND ";
		else $sql = "";
		
		//Access
		$user =& JFactory::getUser();
		if(version_compare( JVERSION, '1.6.0', 'lt' )) {
			$sql .= " c.`accesslevel` <= '".$user->gid."' ";
		} else {
			$sql .= "(";
			$levels = $user->getAuthorisedViewLevels();
			
			foreach($levels AS $i => $level) {
				if($i > 0) $sql .= ' OR ';
				$sql .= 'c.`accesslevel`='.$level;
			}
			$sql .= ")";
		}
		
		return $sql;
	}
	
	var $categories;
	function getCategories($exclude = null) {
		$db =& JFactory::getDBO();
		
		if(empty($this->categories)) {
			$sql = $this->getCategoriesQuery();
			
			$db->setQuery($sql);
			$data = $db->loadObjectList();
			//Daten umwandeln
			$this->categories = array();
			$this->_getChilds($this->categories, $data, 0, 0, $exclude);
			
			foreach($this->categories AS &$category) 
				$category->access = json_decode($category->access);
			
		}
		return $this->categories;
	}
	
	function _getChilds(&$ar, $data, $id, $level = 0, $exclude = null) {
		foreach($data AS $row) {
			if($row->parent_id == $id) {
				if($exclude == null || $exclude != $id) {
					$row->level = $level;
					$ar[] = $row;
					$this->_getChilds($ar, $data, $row->id, ($level + 1), $exclude);
				}
			}
		}
	}
	
	function getCatIdByAlias($alias) {
		if($alias == "all") return 0;
		$sql = 'SELECT `id` FROM  `#__jvotesystem_categories` WHERE `alias` = "'.$alias.'"';
		$this->db->setQuery($sql);
		return $this->db->loadResult();
	}
}//class
