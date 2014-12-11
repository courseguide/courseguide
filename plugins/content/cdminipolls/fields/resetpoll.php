<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.7
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla 
 * @subpackage	System
 * @category	Plugin
 * @version		2.0.7
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldResetPoll extends JFormField
{
	private static $row		=	null;
	
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public	$type 		= 'resetPoll';
	
	public function __construct($form = null) {
		parent::__construct($form);
		
		// include tables
		JTable::addIncludePath(array(
			dirname(dirname(__FILE__)) . DS  . 'table')
		);

		// check table
		self::$row = @JTable::getInstance('CdMiniPolls');
		
		// reset poll
		if ( $pollid = (int) JRequest::getInt('pollToReset')) {
			
			$data = array();
			$data['id'] = (int) $pollid;
			
			if (!self::$row->bind($data)) {
				JError::raiseNotice('', self::$row->getError());
				return false;
			}
			
			if (!self::$row->load()) {
				JError::raiseNotice('', self::$row->getError());
				return false;
			}
			
			$poll_identifier = self::$row->get('identifier', '');
			
			// reset poll - delete actually
			if (!self::$row->delete()) {
				JError::raiseNotice('', self::$row->getError());
				return false;
			}
			
			JError::raiseNotice('', JText::sprintf('PLG_CONTENT_CDMINIPOLLS_ADMINISTRATION_RESETPOLL_SUCCESS_MSG', $poll_identifier));
			return true;
		}
	}
	
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		return '';
	}
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getLabel()
	{
		jimport('joomla.plugin.helper');
		
		$error_msg_tmpl = '<div style="font-weight: bold; border: 3px solid red; background-color: #FFCCCC; font-size: 120%; margin: 10px 0 0 0; padding: 3px; text-align: center">%error%</div>';
		
		if (!JPluginHelper::isEnabled('system', 'cdscriptegrator')) {
			echo str_replace('%error%', 'Please enable Core Design Scriptegrator plugin.', $error_msg_tmpl);
			return false;
		}
		
		$type = strtolower($this->type);
		
		// Build the script.
		$script = array();
		
		$JScriptegrator = JScriptegrator::getInstance('2.0.4');
		$JScriptegrator->importLibrary('jquery');
		
		if ($error = $JScriptegrator->getError()) {
			echo str_replace('%error%', $error, $error_msg_tmpl);
			return false;
		}
		
		$polls = array();
		
		$polls = self::$row->getPollList();
		
		$html = '';
		ob_start();
			require_once dirname(__FILE__) . DS . $type . DS . 'tmpl' . DS . 'default.php';
			$html .= ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
}
?>