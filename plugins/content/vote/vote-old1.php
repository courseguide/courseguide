<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport( 'joomla.plugin.plugin' );
@require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'connect.php';

class plgContentjVoteSystemContent extends JPlugin
{
	//Variablen
	var $option, $db, $user, $document;

    function plgContentjVoteSystemContent( &$subject, $params )
    {
        parent::__construct( $subject, $params );
		
    }//function
    
    function replace($text) {
    	// Make sure jVoteSystem is installed
		if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_jvotesystem')) {
			return $text;
		}
		
		$db = JFactory::getDBO();
		
		// Is jVoteSystem enabled?
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$db->setQuery('SELECT `enabled` FROM `#__extensions` WHERE `element` = "com_jvotesystem" AND `type` = "component"');
			$enabled = $db->loadResult();
		} else {
			$db->setQuery('SELECT `enabled` FROM `#__components` WHERE `link` = "option=com_jvotesystem"');
			$enabled = $db->loadResult();
		}
		if(!$enabled) return $text;
    	
    	$db =& JFactory::getDBO();
    	//Suchmuster suchen und wenn nicht vorhanden, Script verlassen
    	$reg = "#{jvotesystem (.*?)}#s";
    	preg_match_all($reg, $text, $matches);
    	
    	if(count($matches) == 0) return $text;
    	else $loader = jVSConnect::getInstance()->get('Loader');
    
    	//Jeden gefundenen Treffer auswerten und gegebenenfalls ersetzen
    	foreach($matches[0] AS $i => $match) {
    		//Params auswerten
    		$paramText = $matches[1][$i]." ";
    		preg_match_all("#(.*?)=\\|(.*?)\\|#is", $paramText, $paramSplit, PREG_SET_ORDER);
    		$params = array();
    		foreach($paramSplit AS $param) {
    			$params[trim($param[1])] = trim($param[2]);
    		}
    		
    		$output = "";
    		
    		if(isset($params["poll"])) {
    			if(isset($params["template"])) $tmpl = $params["template"]; else $tmpl = null;
    			if(isset($params["toolbar"])) $tool = $params["toolbar"]; else $tool = true;
    			if(isset($params["link"])) $link = $params["link"]; else $link = false;
    			$output = $loader->getID($params["poll"], 'plugin', $tmpl, $tool, $link);
    		} elseif(isset($params["answer"])) {
    			$output = jVSConnect::getInstance()->getAnswerVoteButton($params["answer"], $params);
    		} else {
    			$template =& VBTemplate::getInstance();
    			$template->setTemplate("default");
    			//Parameter
    			$par = new JObject();
    			$par->msg = sprintf(JText::_("WRONGPARS"), $matches[0][$i]);
    			$par->type = "error";
    			
    			//laden
    			$output = '<div class="jvotesystem jvs-default">'.$template->loadTemplate("notification", $par).'</div>';
    			//$output = "Fehlerhafte Parameter! Umfrage kann nicht geladen werden.";
    		}
    		
    		//Gefundes Suchmuster durch Daten ersetzen
    		$text = preg_replace ('{'.str_replace("|", '\|', $matches[0][$i]).'}', $output, $text);
    	}
    		
    	return $text;
    }
    		
    public function onPrepareContent( &$article, &$params, $limitstart ) {
    	$article->text = self::replace($article->text);
    	return $article->text;
    }
    		
    public function onContentPrepare($context, &$row, &$params, $page = 0) {
    	$row->text = self::replace($row->text);
    	return $row->text;
    }
}//class
