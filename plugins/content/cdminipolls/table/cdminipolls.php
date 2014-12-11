<?php
/**
 * Core Design Mini Polls plugin for Joomla! 1.7
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla
 * @subpackage	Content
 * @category   	Plugin
 * @version		1.0.0
 * @copyright	Copyright (C) 2007 - 2011 Great Joomla!, http://www.greatjoomla.com
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL 3
 * 
 * This file is part of Great Joomla! extension.   
 * This extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined( '_JEXEC' ) or die;

/**
 * Table
 *
 * @package 		Joomla.Framework
 * @subpackage		Table
 * @since			1.0
 */
class JTableCdMiniPolls extends JTable
{
	/** @var int Primary key */
	public $id = null;
	
	
	
	/**
	 * A database connector object
	 * 
	 * @param database A database connector object
	 * @return void
	 */
	public function __construct( &$db ) {
		parent::__construct( '#__cdminipolls', 'id', $db );
	}
	
	/**
	 * Create a database
	 * 
	 * @return boolean
	 */
	public function createDB() {
		$query = '
	  	CREATE TABLE IF NOT EXISTS ' . $this->_tbl . ' (' .
            '`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,' .
			'`identifier` varchar(255) NOT NULL DEFAULT \'\',' .
			'`data` text NOT NULL DEFAULT \'\',' .
			' PRIMARY KEY (`id`)' .
            ') ENGINE=MyISAM;';

		$this->_db->setQuery($query);
		
		if (!$this->_db->query()) {
			$this->setError($this->_db->stderr());
			return false;
		}
		return true;
	}
	
	/**
	 * Load row based on required identifier or ID
	 * 
	 * @return	boolean
	 */
	public function loadRow() {
		$query = $this->_db->getQuery(true);
		
		// find existing row if is
		$query->select('*');
		$query->from($this->_db->nameQuote($this->_tbl));
		if ((int) $this->get('id', 0) !== 0) {
			$query->where($this->_db->nameQuote('id') . ' = ' . (int) $this->get('id', 0));
		} else {
			$query->where($this->_db->nameQuote('identifier') . ' LIKE ' . $this->_db->quote($this->get('identifier', '')));
		}
		
		$this->_db->setQuery($query);
		
		if (!$this->_db->query()) {
			$this->setError($this->_db->stderr());
			return false;
		}
		$row = (object) $this->_db->loadObject();
		
		return $this->bind($row);
	}
	
	/**
	 * Load all available polls
	 * 
	 * @return array
	 */
	public function getPollList() {
		$query = $this->_db->getQuery(true);
		
		// find existing row if is
		$query->select($this->_db->nameQuote('id') . ',' . $this->_db->nameQuote('identifier'));
		$query->from($this->_db->nameQuote($this->_tbl));
		$this->_db->setQuery($query);
		
		if (!$this->_db->query()) {
			$this->setError($this->_db->stderr());
			return false;
		}
		$row = (object) $this->_db->loadObjectList();
		return $row;
	}
}
?>
