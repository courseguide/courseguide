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
defined('_JEXEC') or die;

// Import library dependencies
jimport('joomla.plugin.plugin');

class plgContentCdMiniPolls extends JPlugin
{
	private				$livepath			=	'';
	private 			$JScriptegrator		=	null;
	private static		$identifier			=	'';
	private static		$uitheme			=	'ui-lightness';
	private static		$settings			=	null;
	
	/* Ajaxed output */
	protected static	$output				=	'';
	
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
		$this->livepath = JURI::root(true);
	}
	
	/**
	 * Joomla! onAfterRender() function
	 * @return void
	 */
	public function onAfterRender()
	{
		if ($task = JRequest::getCmd($this->_name . '_task', '')) {
			$method = 'get';
			if (strpos($task, 'ajax_') !== false) {
				$method = 'ajax';
			}
				
			if ($method === 'ajax') {
				JFactory::getApplication()->close(self::$output);
			}
		}
	}

	/**
	 * Call onContentPrepare function
	 * Method is called by the view.
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The content object.  Note $article->text is also available
	 * @param	object	The content params
	 * @param	int		The 'page' number
	 * @since	1.6
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{		
		// mini poll
		if (strpos($article->text, '{minipolls') === false) return false;
		
		// Scriptegrator check
		if (!class_exists('JScriptegrator')) {
			JError::raiseNotice('', JText::_('PLG_CONTENT_CDMINIPOLLS_ERROR_ENABLE_SCRIPTEGRATOR'));
			return false;
		} else {
			$this->JScriptegrator = JScriptegrator::getInstance('2.1.2');
			if ($error = $this->JScriptegrator->getError()) {
				JError::raiseNotice('', $error);
				return false;
			}
		}
		
		// include tables
		JTable::addIncludePath(array(
			dirname(__FILE__) . DS  . 'table')
		);

		// check table
		$row = @self::dbInstance();
		if (!$row->createDB()) {
			JError::raiseNotice('', $row->getError());
			return false;
		}
		
		$document = JFactory::getDocument(); // set document

		// plugin JS and CSS
		$document->addScript($this->livepath . '/plugins/content/' . $this->_name . '/js/' . $this->_name . '.js');
		$document->addStyleSheet($this->livepath . '/plugins/content/' . $this->_name . '/css/' . $this->_name . '.css');

		// add CSS stylesheet for RTL languages
		if ($document->direction === 'rtl') {
			$document->addStyleSheet($this->livepath . '/plugins/content/' . $this->_name . '/css/' . $this->_name . '_rtl.css');
		}

		// define the regular expression
		$regex = '#{minipolls(.*?)?}(.*?)?{/minipolls}#is';
		
		$article->text = preg_replace_callback($regex, array($this, 'replacer'), $article->text);
	}
	
	/**
	 * Replacer
	 *
	 * @param $match
	 *
	 * @return string
	 */
	private function replacer(&$match)
	{
		$settings = (isset($match[1]) ? trim($match[1]) : '');
		$options = (isset($match[2]) ? trim($match[2]) : '');

		// no options, no poll
		if (!$options) {
			return false;
		}

		// make array from string with polls options
		$options = explode('||', $options);

		// petition id
		preg_match('#id="(.*?)"#', $settings, $identifier);
		if (isset($identifier[1])) {
			self::$identifier = $identifier[1];
		} else {
			JError::raiseNotice('', JText::_('PLG_CONTENT_CDMINIPOLLS_ERROR_NO_IDENTIFIER'));
			return false;
		}

		$row = self::dbInstance();
		$data = array();
		$data['identifier'] = self::$identifier;

		if (!$row->bind($data)) {
			JError::raiseNotice('', $row->getError());
			return false;
		}

		// load row
		if (!$row->loadRow()) {
			JError::raiseNotice('', $row->getError());
			return false;
		}
		
		$result = new JRegistry();
		if ($row->get('data', '')) {
			$result = new JRegistry($row->get('data', ''));
		}

		// inline overrides
		// make INI format
		$overrides = new JRegistry(preg_replace('#(.*?)="(.*?)"#is', "$1=\"$2\"\n", $settings));

		$settings = new JRegistry($this->params->toArray());
		
		// merge global params
		$settings->merge($overrides);
		
		self::$settings = $settings;
		unset($overrides);
		
		// ajax request
		if ($task = JRequest::getCmd($this->_name . '_task', '')) {
			$method = 'get';
			if (strpos($task, 'ajax_') !== false) {
				$method = 'ajax';
			}
				
			if ($method === 'ajax') {
				$application = JFactory::getApplication();
			}
				
			$output 	= array();
			$response 	= array();
			$status 	= 'error';
			$content 	= '';
			$format		= 'xml';
				
			if (is_callable(array($this, $task))) {
				
				// include tables
				JTable::addIncludePath(array(
					dirname(__FILE__) . DS  . 'table')
				);
				
				$response = call_user_func(array($this, $task));

				if (!is_array($response)) {
					// only string, create a "success" array
					$response = array('success', $response);
				}

				if (isset($response[0])) {
					$status = trim($response[0]);
				}
				if (isset($response[1])) {
					$content = trim($response[1]);
				}
				if (isset($response[2])) {
					$format = trim($response[2]);
				}

				unset($response);
					
				switch($format) {
					case 'xml':
						// XML response
						header("Content-Type: text/xml");
						$output[] = '<?xml version="1.0" encoding="utf-8" ?>';
						$output[] = '<response>';
						$output[] = '<status>' . $status . '</status>';
						$output[] = '<content><![CDATA[' . $content . ']]></content>';
						$output[] = '</response>';
					default:
						break;

					case 'text':
						$output[] = $content;
						break;
				}

			}
			if ($method === 'ajax') {
				// assign to static value, then process in "onAfterRender" function
				self::$output = implode('', $output);
				return true;
			}
				
		}

		self::$uitheme = self::$settings->get('uitheme', 'ui-lightness');
		
		$this->JScriptegrator->importLibrary(array(
			'jquery',
			'jqueryui' => array(
				'uitheme' => self::$uitheme
		)
		));
		
		if ($error = $this->JScriptegrator->getError()) {
			JError::raiseNotice('', $error);
			return false;
		}

		$document = JFactory::getDocument(); // set document

		$script_options = array();

		static $once;
		if (!$once) {
			// load language just once
			$lang_array = array();
			//$lang_array[] = 'PLG_CONTENT_CDPERFECTFORMS_STATUS_LOADING : "' . JText::_('PLG_CONTENT_CDPERFECTFORMS_STATUS_LOADING', true) . '"';
				
			// append custom script to header (just once)
			if($lang_array) {
				$language_script = "
				<!--
				if (typeof(jQuery) === 'function') {
					jQuery(document).ready(function($){
						if ($.fn." . $this->_name . ") {
							$.fn.$this->_name.language = {
								" . implode(", ", $lang_array) . "
							}
						}
					});
				}
				// -->";

				$document->addScriptDeclaration($language_script);
			}
			$once = 1;
		}

		// append custom script to header
		
		static $identifier;
		
		if ($identifier !== self::$identifier) {
			$document->addScriptDeclaration("
			<!--
			if (typeof(jQuery) === 'function') {
				jQuery(document).ready(function($){
					if ($.fn." . $this->_name . ") {
						$('#" . self::$identifier . "')." . $this->_name . "({
							" . implode(", ", $script_options) . "
						});
					}
				});
			}
			// -->
			");
			
			$identifier = self::$identifier;
		}
		

		// view
		$tmpl = '';
		if ($layoutpath = $this->getLayoutPath('view')) {
			ob_start();
			require $layoutpath;
			$tmpl .= ob_get_contents();
			ob_end_clean();
		}

		return $tmpl;
	}

	/**
	 * Vote for poll
	 *
	 * @return array
	 */
	private function ajax_vote()
	{
		// security check
		if (!JRequest::checkToken()) {
			return array('error', JText::_('JINVALID_TOKEN'), );
		}

		$row = self::dbInstance();
		
		$data = JRequest::get('post');
		
		foreach ($data as $key=>$value) {
			if (strpos($key, $this->_name . '_') === 0) {
				$data[str_replace($this->_name . '_', '', $key)] = $value;
			}
			unset($data[$key]);
		}
		
		if (!$row->bind($data)) {
			return array('error', $row->getError());
		}
		
		// load row
		if (!$row->loadRow()) {
			return array('error', $row->getError());
		}
		
		$result = JRegistry::getInstance($this->_name);
		
		// poll already exists
		if ( (int) $row->get('id') ) {
			$result->loadString($row->get('data', ''));
		}
		
		// increase vote count for poll option
		$result->set($data['poll_option'] . '.votes', ((int) $result->get($data['poll_option'] . '.votes', 0) + 1));
		
		// increase total count of votes per poll
		$result->set('total', ( (int) $result->get( 'total', 0 ) + 1 ) );
		
		// set first and last vote
		if (!$result->exists('firstvote')) {
			// not voted yet, set only first vote date
			$result->set('firstvote', JFactory::getDate()->toMySQL());
		} else {
			// already voted, change the lastvote value
			$result->set('lastvote', JFactory::getDate()->toMySQL());
		}
		
		$row->set('data', $result->toString());
		
		if (!$row->store()) {
			return array('error', $row->getError());
		}
		
		// re-append statistics
		$tmpl = '';
		if ($layoutpath = $this->getLayoutPath('statistics')) {
			ob_start();
			require $layoutpath;
			$tmpl .= ob_get_contents();
			ob_end_clean();
		}
		
		return array('success', $tmpl);
	}

	/**
	 * Load DB instance
	 *
	 * @return 		A database object
	 */
	private static function dbInstance($instance = '')
	{
		$row = JTable::getInstance('CdMiniPolls' . $instance);
		return $row;
	}

	/**
	 * Get Layout
	 *
	 * @param 		$file
	 * @return 		string
	 */
	private function getLayoutPath($file = '')
	{
		if (!$file) return false;
		 
		$layout = $this->params->get('layout', 'default');
		$type = 'html';
		 
		$tmpldir = dirname(__FILE__) . DS  . 'tmpl' . DS . $layout;
		 
		$filepath = $tmpldir . DS . $file . '.' . $type .  '.php';
		if (!JFile::exists($filepath)) return false;

		return $filepath;
		 
	}

	/**
	 * Check if user has admin privileges
	 *
	 * @return	boolean
	 */
	private static function hasAdminPrivileges()
	{
		return (boolean) JFactory::getUser()->authorise('core.admin');
	}
}
?>