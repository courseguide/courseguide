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

if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filesystem.file' );

$path_jlib = JPATH_SITE.DS.'plugins'.DS.'system'.( (version_compare( JVERSION, '1.6.0', 'lt' )) ? "" : DS.'joomessLibrary' ).DS.'joomessLibrary.php';
if(JFile::exists($path_jlib))
	require_once $path_jlib;

if(!class_exists('joomessLibrary')) { 
	//Required! => Disable jVoteSystem
	$jlib_db = JFactory::getDBO();
		
	// Disable jVoteSystem
	if(version_compare(JVERSION, '1.6.0', 'ge'))
		$jlib_db->setQuery('UPDATE `#__extensions` SET `enabled`="0" WHERE `element` = "com_jvotesystem" AND `type` = "component"');
	else 
		$jlib_db->setQuery('UPDATE `#__components` SET `enabled`="0" WHERE `link` = "option=com_jvotesystem"');
	$jlib_db->query();
	
	//Raise Error
	JError::raiseError("jVS [0001]", JText::_('JVS_ERROR_FAILED_TO_LOAD_JOOMESSLIBRARY'));
}

$classes = array( "vote", "user", "answer", "access", "params", "comment", "general", "mail", "spam", "charts", "template", "category", "toolbar", "log", "export", "tasks", "update", "api" );
foreach($classes AS $cload)
	require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'classes'.DS.$cload.'.class.php';

//require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'joomessLibrary_tutorials.php';

class VBLoader
{
	//Variablen
	private $db, $user, $document;
	
	private function __construct($load) { if(!$load) return;
		$this->user =& VBUser::getInstance();
		$this->template =& VBTemplate::getInstance();
		$this->general =& VBGeneral::getInstance();
		
		$this->document = & JFactory::getDocument();
		$this->db = JFactory::getDBO();		
		//Sprache
		$this->loadLanguageFiles(true);
		
		if(JRequest::getBool("run_update", false) && VBAccess::getInstance()->isUserAllowedToConfig())
			VBUpdate::getInstance()->doVersionUpdate_2_56();
	}
	
	static function &getInstance($load = true) {
		static $instance;
		if(empty($instance)) {
			$instance = new VBLoader($load);
		}
		return $instance;
	}
	
	function loadLanguageFiles($onlySite = false) {
		$jlang =& JFactory::getLanguage();
		//-- Load language files
		$jlang->load('com_jvotesystem', JPATH_SITE, 'en-GB', true);
		$jlang->load('com_jvotesystem', JPATH_SITE, null, true);
		$jlang->load('com_jvotesystem', JPATH_SITE.DS.'components'.DS.'com_jvotesystem', null, true);
		if($onlySite) return;
		//-- Load language files
		$jlang->load('com_jvotesystem', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_jvotesystem', JPATH_ADMINISTRATOR, null, true);
		$jlang->load('com_jvotesystem', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jvotesystem', null, true);
	}
	
	private $jsConfigLoaded;
	function loadJSConfig() {
		if(empty($this->jsConfigLoaded)) {
			$app =& JFactory::getApplication();
				
			$conf = array();
			$conf["site"] = joomessLibrary::getInstance()->root()."/";
			$conf["time"] = JFactory::getDate(null, $app->getCfg('offset'))->toUnix();
			$conf["admin"] = $app->isAdmin();
			$conf["lang"] = JFactory::getLanguage()->getTag();
				
			$conf["token"] = $this->general->getToken();
			$conf["legacy"] = version_compare( JVERSION, '1.6.0', 'lt' );
				
			$params = VBParams::getInstance();
			$conf["executionTime"] = $params->get("maxExecutionTime");
				
			$conf["polls"] = array();
				
			joomessLibrary::getInstance()->jsCode("jVS.conf = " . json_encode($conf));
			
			$this->jsConfigLoaded = true;
		}
	}
	
	function getID($id, $view, $tmpl = null, $toolbar = true, $link = false) {
		//Laden..
		VBParams::getInstance($view, true);
		$this->vote =& VBVote::getInstance();
		
		VBGeneral::getInstance()->charset('utf-8'); //Load charset		
		
		$output = $this->vote->getVotebox($id, false, null, $link, $tmpl, $toolbar);
		
		/* The copyright information may not be removed or made invisible! To remove the code, please purchase a version on www.joomess.de. Thanks!*/
		joomessLibrary::getInstance()->copyright('jVoteSystem', $output);
		
		if($tmpl != 'module' && $tmpl != 'easyquestion') {
			$old = VBTemplate::getInstance()->prepare();
			VBGeneral::getInstance()->charset('plain'); //Load charset header
			$output .= VBTemplate::getInstance()->getHtml($old);
		}
		
		return $output;
	}
	
}//class
