<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
 
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
define ('ABSOLUTE_PATH', dirname(__FILE__) );
define ('RELATIVE_PATH', 'components' . DS . 'com_jvotesystem' . DS . 'assistant');
define ('JPATH_BASE', str_replace(RELATIVE_PATH, "", ABSOLUTE_PATH)); 

require_once ( JPATH_BASE . DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE . DS.'includes'.DS.'framework.php' );

$interface = JRequest::getString('interface', 'site');

$mainframe =& JFactory::getApplication($interface);
$lang =& JFactory::getLanguage();
$lang->setLanguage(JRequest::getString("lang", $lang->getDefault()));
$lang->load();
$lang->load('lib_joomla');
		
//-- No direct access
defined('_JEXEC') or die('=;)');

header('Content-Type: text/html; charset=utf-8');
ob_start();

require_once(JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'connect.php');
$jvs =& jVSConnect::getInstance();

if(!$jvs->active()) {
	echo '{"error": "jVoteSystem not enabled..", "success": false}';
	exit();
}

$config = JFactory::getConfig();
if($config->get("offline", 0) && !$mainframe->isAdmin() && !VBAccess::getInstance()->isUserAllowedToConfig()) {
	echo '{"error": "Site offline..", "success": false}';
	exit();
}

//Load admin language files
VBLoader::getInstance()->loadLanguageFiles();

$document = & JFactory::getDocument();

$view = JRequest::getString('view', null);
$task = JRequest::getString('task', null);
	
//Standard-Files hinzuf�gen
$lib = & joomessLibrary::getInstance();

$lib->css('components/com_jvotesystem/assets/css/general.css');
$lib->css('components/com_jvotesystem/assistant/assets/css/general.css');
$lib->css('administrator/components/com_jvotesystem/assets/css/general.css');
$lib->css('administrator/components/com_jvotesystem/assets/css/icons.css');
$lib->css('administrator/templates/system/css/system.css');
if(version_compare( JVERSION, '1.6.0', 'lt' )) $lib->css('administrator/templates/khepri/css/template.css', null, true);
else $lib->css('administrator/templates/bluestork/css/template.css', null, true);
$lib->css('components/com_jvotesystem/assistant/assets/css/themes/base/jquery.ui.all.css');
//$document->addStyleSheet(JURI::root(true).'/PATH_ASSISTANT/assets/css/themes/base/jquery.ui.all.css');

$lib->jQuery();
$lib->js('components/com_jvotesystem/assistant/assets/js/jquery.ui.custom.min.js', false, true, true);
$lib->js('components/com_jvotesystem/assistant/assets/js/prettyComments.js');
$lib->js('components/com_jvotesystem/assets/js/jvotesystem.js');
$lib->js('administrator/components/com_jvotesystem/assets/js/general.js');
$lib->js('components/com_jvotesystem/assistant/assets/js/general.js');
$document->addScript('https://www.google.com/jsapi');
$document->addScriptDeclaration('google.load("visualization", "1", {packages:["corechart"]});');

//Tooltip
$conf = array();
$conf["alwaysTop"] = true;

$document->addScriptDeclaration("jVS.tip.config = " . json_encode($conf));

//Rechte
$access =& VBAccess::getInstance();
if(!$access->assistant()) {
	if(version_compare( JVERSION, '1.6.0', 'lt' ))
		$mainframe->redirect(editLink(JURI::base(false)).($interface == "administrator" ? "administrator/index.php?option=com_jvotesystem&view=boxen" : "index.php?option=com_user&tmpl=component&view=login&return=".base64_encode(JRequest::getURI())), "NOACCESSRIGHTS", "error");
	else
		$mainframe->redirect(editLink(JURI::base(false)).($interface == "administrator" ? "administrator/index.php?option=com_jvotesystem&view=boxen" : "index.php?option=com_users&tmpl=component&view=login&return=".base64_encode(JRequest::getURI())), "NOACCESSRIGHTS", "error");
	return;
} 

//View �ffnen
class ViewLoader {
	function getView($view, $task, $interface) {
		ob_start();
		switch($view) {
			case "poll":
				require_once ( ABSOLUTE_PATH.DS.'poll'.DS.'view.html.php' );
				
				$view = new AssistantViewPoll($task, $interface);
				break;
			case "ajax":
				require_once ( ABSOLUTE_PATH.DS.'ajax'.DS.'view.html.php' );
				
				$view = new AssistantViewAjax($task, $interface);
				break;
			case "button":
				require_once ( ABSOLUTE_PATH.DS.'button'.DS.'view.html.php' );
				
				$view = new AssistantViewButton($task);
				break;
		}
		$component = ob_get_contents();
		ob_clean();
		return $component;
	}
}

$loader = new ViewLoader();
$component = $loader->getView($view, $task, $interface);

//ausgeben
$lib->render();
$headData = $document->getHeadData();
//HeadData & Text verarbeiten
function editLink($link) {
	$link = str_replace('components/com_jvotesystem/assistant/', "", $link);
	$link = str_replace('PATH_ASSISTANT', "components/com_jvotesystem/assistant/", $link);
	return $link;
}

$newStyleSheets = array();
foreach($headData["styleSheets"] AS $key => $stylesheet) {
	if(!isset($stylesheet["attribs"]["jm_safe"])) $newKey = editLink($key); else $newKey = $key;
	$newStyleSheets[$newKey] = $stylesheet;
}
$headData["styleSheets"] = $newStyleSheets; 

$newScripts = array();
foreach($headData["scripts"] AS $key => $script) {
	$newKey = editLink($key);
	$newScripts[$newKey] = $script;
}
$headData["scripts"] = $newScripts; 

$component = editLink($component);

if($view == "ajax") {
	echo $component;
} else {
	require_once ( ABSOLUTE_PATH .DS.'html.php' );
}

VBLog::getInstance()->save();

?>