<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- Kein Zugang
defined('_JEXEC') or die('=;)');

class jVSConnect
{
	private $loader, $active;
	
	//Call this function to get a jVSConnect object. 
	public static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new jVSConnect();
		}
		return $instance;
	}
	
	//You may not create your own instance of the Connecter, don't use "new jVSConnect()". Use the "getInstance"-Method instead. -> private
	private function __construct() {
		$this->active = null;
		if($this->active()) {
			$this->forceLoad();
		} elseif( JRequest::getString('option', '') == 'com_jvotesystem' ) {
			//Raise Error
			JError::raiseError("jVS [0002]", JText::_('JVS_ERROR_NOT_ENABLED'));
		}
	}
	
	public function forceLoad() { //Forces jVSConnect to load all libraries (Could fail if disabled -> use it only if you know what you are doing)
		require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'classes'.DS.'loader.php';
			
		$this->loader =& VBLoader::getInstance();
		$this->loader->loadLanguageFiles(true);
	}
	
	//Check if jVoteSystem is enabled -> required!
	public function active() {
		if($this->active == null) {
			$db = JFactory::getDBO();
			
			// Is jVoteSystem enabled?
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$db->setQuery('SELECT `enabled` FROM `#__extensions` WHERE `element` = "com_jvotesystem" AND `type` = "component"');
				$enabled = $db->loadResult();
			} else {
				$db->setQuery('SELECT `enabled` FROM `#__components` WHERE `link` = "option=com_jvotesystem"');
				$enabled = $db->loadResult();
			}
			$this->active = (!$enabled) ? false : true;
		}
		return $this->active;
	}
	
	//Returns the poll object of the DB
	public function getPoll($id, $withParams = false) {
		if(!$this->active()) return false;
		
		$vote =& $this->get("Vote");
		
		$box = $vote->getBox($id);
		if($box) {
			if(!$withParams) { 
				foreach(json_decode($box->params) AS $key => $param) unset($box->$key);
				unset($box->params);
				unset($box->access);
			}
			
			//Category Access
			$cat =& $this->get("Category");
			if($cat->getCategory($box->catid)->id == null) return null;
		}
		
		return $box;
	}
	
	//Returns the poll html-code
	public function getPollHTML($id, $tmpl = null, $toolbar = true, $link = false) {
		if(!$this->active()) return "";
		
		return $this->loader->getID($id, "plugin", $tmpl, $toolbar, $link);
	}
	
	//Returns a list of polls
	public function getPolls($filter = array(), $start = 0, $limit = null, $withParams = false) {
		if(!$this->active()) return null;
		
		$vote =& $this->get("Vote");
		
		$polls = $vote->getPolls($filter, $start, $limit);
		foreach($polls AS &$poll) {
			if(!$withParams) {
				foreach(json_decode($poll->params) AS $key => $param) unset($poll->$key);
				unset($poll->params);
				unset($poll->access);
			}
		}
		
		return $polls;
	}
	
	//Returns the category object of the DB
	public function getCategory($id, $withParams = false) {
		if(!$this->active()) return false;
		
		$cat =& $this->get("Category");
		
		$category = $cat->getCategory($id);
		if($category) {
			if(!$withParams) {
				foreach(json_decode($category->params) AS $key => $param) unset($category->$key);
				unset($category->params);
				unset($category->access);
			}
		}
		
		return $category;
	}
	
	//Returns the answer object of the DB
	public function getAnswer($id) {
		if(!$this->active()) return false;
		
		$answer =& $this->get("Answer");
		
		return $answer->getAnswer($id);
	}
	
	private $AVBJSL = false;
	//Returns a votebutton for an answer
	public function getAnswerVoteButton($id, $params = array()) {
		if(!$this->active()) return "";
		//Check if answer exists
		$answer = $this->getAnswer($id);
		if(!$answer) {
			$template =& VBTemplate::getInstance();
			$template->setTemplate("default");
			//Parameter
			$par = new JObject();
			$par->msg = sprintf(JText::_("JVS_ANSWER_NOT_FOUND"), $id);
			$par->type = "error";
			 
			//laden
			return '<div class="jvotesystem jvs-default">'.$template->loadTemplate("notification", $par).'</div>';
		}
		
		$lib =& joomessLibrary::getInstance();
		//Append JS-File
		if(!$this->AVBJSL) {
			if(!JFile::exists(JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'assets'.DS.'js'.DS."jvs-embed.js"))
				VBUpdate::getInstance()->generateUpdateFiles();
			if(@$params["async"] == true) {
				$js = "(function() {
				var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				po.src = '{$lib->root()}/components/com_jvotesystem/assets/js/jvs-embed.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				})();";
				$lib->documentReady($js);
			} else JFactory::getDocument()->addScript($lib->root().'/components/com_jvotesystem/assets/js/jvs-embed.js');
			
			$this->AVBJSL = true;
		}
		
		//Params
		$pars 					= array();
		$pars["class"] 			= "jvs-votebutton";
		$pars["data-lang"] 		= (isset($params["lang"])) ? $params["lang"] : JFactory::getLanguage()->getTag();
		$pars["data-host"]		= (isset($params["host"])) ? $params["host"] : $lib->root();
		$pars["data-id"]		= $answer->id;
		$pars["data-token"]		= VBGeneral::getInstance()->getToken();
		if(isset($params["apikey"])) 
			$pars["data-apikey"] = $params["apikey"];
		
		//Styles
		$styles = array();
		if(isset($params["float"])) $styles["float"] = $params["float"];
		
		if(!empty($styles)) $pars["style"] = "";
		foreach($styles AS $key => $style)
			$pars["style"] .= $key.":".$style.";";
		
		//Build HTML tag
		$html = array();
		$html[] = '<div';
		foreach($pars AS $key => $value)
			$html[] = ' '.$key.'="'.$value.'"';
		$html[] = '> </div>';
		
		return implode("", $html);		
	}
	
	//Returns the answers of an poll
	public function getAnswers($id, $filter = array(), $start = 0, $limit = null) {
		if(!$this->active()) return false;
		
		$vote =& $this->get("Vote");
		
		return $vote->getAnswers($id, $filter, $start, $limit);
	}
	
	//Returns the comment object of the DB
	public function getComment($id) {
		if(!$this->active()) return false;
		
		$comment =& $this->get("Comment");
		
		return $answer->getComment($id);
	}
	
	public function setBBCode($state = true) {
		if(!$this->active()) return null;
		
		$pars =& $this->get("Params");
		
		$curState = $pars->get("activate_bbcode");
		$pars->set("global", "activate_bbcode", $state);
		$pars->set("global", "general_published_bbcode", !$state);
		return $curState;
	}
	
	public function get($class /*, args */) {
		if(!$this->active()) return null;
		
		$c = "VB".ucfirst(strtolower($class));
		$args = func_get_args();
		array_splice($args, 0, 1);
		
		if(class_exists($c)) {
			$ins =& call_user_func_array(array($c, "getInstance"), $args);
			return $ins;
		} else return null;
	}
	
	public function route($view = "", $id = null, $task = "", $pars = array(), $local = true, $route = true) {
		if(!$this->active()) return "#";
		
		$general =& $this->get("General");
		
		return $general->buildLink($view, $id, $task, $pars, $local, $route);
	}
	
	public function checkVersion( $requiredVersion ) {
		$cur_version = floatval(VBUpdate::$_JVS_VERSION);
		if($cur_version < floatval($requiredVersion)) return false;
		else return true;
	}
}//class
