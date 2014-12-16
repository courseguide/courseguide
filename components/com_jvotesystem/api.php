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
define ('RELATIVE_PATH', 'components'.DS.'com_jvotesystem' );
define ('JPATH_BASE', str_replace(RELATIVE_PATH, "", ABSOLUTE_PATH));

require_once ( JPATH_BASE . DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE . DS.'includes'.DS.'framework.php' );

$mainframe =& JFactory::getApplication("site");
$lang =& JFactory::getLanguage();
$lang->setLanguage(JRequest::getString("lang", $lang->getDefault()));
$lang->load();

jimport( 'joomla.error.profiler' );
$profiler = new JProfiler();
$buildTime = $profiler->getmicrotime();

define ('JVS_ROOT', str_replace("/components/com_jvotesystem", "", JUri::root(true)));

require_once(JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'connect.php');

ob_start();
header('Content-Type: text/plain; charset=utf-8');

//-- No direct access
defined('_JEXEC') or die('=;)');

class jVoteSystemAPI
{
	private $db;
	
	function __construct() {
		$this->connect =& jVSConnect::getInstance();
		
		if(!$this->connect->active()) {
			echo '{"error": "jVoteSystem not enabled..", "success": false}';
			exit();
		}
		
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		if($config->get("offline", 0) && !VBAccess::getInstance()->isUserAllowedToConfig()) {
			$ar = array();
			$ar["success"] = false;
			$ar["error"] = "Site offline..";
			
			$this->output($ar);
		}
		
		$this->db =& JFactory::getDBO();		
		$this->api =& VBApi::getInstance();
		$this->lib =& joomessLibrary::getInstance();
	}
	
	function get($task)
    {
    	$ar = array();
    	$ar["success"] = false;
    	
    	//Referrer überprüfen => Votebutton auf eigener Seite erlauben
    	$referrer = JRequest::getString('HTTP_REFERER', null, 'SERVER');
    	$uri = JUri::getInstance($referrer);
		$base = $uri->toString(array('scheme', 'host', 'port', 'path'));
		$host = $uri->toString(array('scheme', 'host', 'port'));
    	$is_internal = !(stripos( $base, $this->lib->root()) !== 0 && !empty($host));
    	
    	if(($is_internal || JRequest::checkToken('GET')) && $task == 'votebutton') {
    		
    	} else {
    		$keyStr = JRequest::getString('key', "");
    		if($keyStr == "") {
    			$ar["error"] = JText::_("JVS_API_KEY_NOT_FOUND");
    			$this->output($ar);
    		}
    		 
    		//Key überprüfen
    		$key = $this->api->loadKey( $keyStr );
    		if(!$key) {
    			$ar["error"] = JText::_("JVS_API_INVALID_KEY");
    			$this->output($ar);
    		}
    		 
    		//Tasks
    		if(!in_array($task, $key->params->tasks)) {
    			$ar["error"] = JText::_("JVS_API_TASK_DENIED");
    			$this->output($ar);
    		}
    		 
    		//Limit
    		$new_count = $key->count + 1;
    		$date = JFactory::getDate();
    		$last_start = JFactory::getDate( $key->last_start );
    		
    		switch($key->params->limit_type) {
    			case "month": $diff = 60 * 60 * 24 * 7 * 30; break;
    			case "week": $diff = 60 * 60 * 24 * 7; break;
    			case "day": $diff = 60 * 60 * 24; break;
    			case "hour":
    			default: $diff = 60 * 60;
    		}
    		if(($date->toUnix()-$last_start->toUnix()) > $diff) {
    			$new_count = 0;
    		} else {
    			if($new_count > $key->params->limit) {
    				$ar["error"] = JText::_("JVS_API_REACHED_LIMIT");
    				$this->output($ar);
    			}
    		}
    		 
    		//Count updaten
    		$this->api->count( $key->key, $new_count, $key->total_count + 1 );
    	}
    	
    	switch($task) {
    		case "global": //First API Function.. global stats of the jVoteSystem component
    			$sql = "SELECT * FROM
							(SELECT COUNT(`id`) AS polls FROM `#__jvotesystem_boxes`) b,
							(SELECT COUNT(`id`) AS answers FROM `#__jvotesystem_answers`) a,
							(SELECT COUNT(`id`) AS comments FROM `#__jvotesystem_comments`) c,
							(SELECT COUNT(`id`) AS categories FROM `#__jvotesystem_categories`) ca,
							(SELECT SUM(`votes`) AS votes FROM `#__jvotesystem_votes`) v
						";
    			$this->db->setQuery($sql);
    			$stats = $this->db->loadObject();
    			if(!$error = $this->db->getErrorMsg()) {
    				$ar["success"] = true;
    				$ar["stats"] = array();
    				foreach($stats AS $key => $stat) $ar["stats"][$key] = (int) $stat;
    			}
    			break;
    			
    		case "poll":
    			$id = JRequest::getInt("id", null);
    			
    			$cat =& $this->connect->get("Category");
    			
    			//Poll
    			$poll = $this->connect->getPoll($id, false);
    			$ar["poll"] = $poll;
    			
    			if($poll) {
    				//Category
    				$category = $this->connect->getCategory($poll->catid);
    				$ar["category"] = $category;
    				
    				$ar["success"] = true;
    			} else {
    				$ar["error"] = JText::_("NOBOXFOUNDORPUBLISHED");
    			}
    			
    			break;
    			
    		case "polls":
    			$vote =& VBVote::getInstance();
    			
    			$ar["polls"] = $this->connect->getPolls();
    			
    			break;
    			
    		//VoteButton
    		case "votebutton":
    			$lib =& joomessLibrary::getInstance();
    			$tmpl =& VBTemplate::getInstance();
    			$vbanswer =& VBAnswer::getInstance();
    			$vote =& VBVote::getInstance();
    			$general =& VBGeneral::getInstance();
    			$access =& VBAccess::getInstance();
    			
    			$ref = JRequest::getString("ref", JRequest::getString('HTTP_REFERER', null, 'SERVER'));
    			
    			$aid = JRequest::getInt("id", null);
    			$answer = $vbanswer->getAnswer($aid); 
    			
    			$par = new JObject();
    			
    			if($answer) {
    				$poll = $vote->getBox($answer->box_id);
    				$votes = $vote->getVotesFromAnswer($answer->id);
    				 
    				$uservotes = $vote->getVotesByUser($poll->id, $answer->id);
    				$uservotesAll = $vote->getVotesByUser($poll->id);
    				
    				$par->count = $votes->votes;
    				$par->uservotes = $uservotes->votes;
    				$par->aid = (int)$answer->id;
    				$par->pid = (int)$poll->id;
    				$par->max_votes = (int)$poll->max_votes_on_answer;
    				$par->allowed_votes = $uservotesAll->allowed_votes;
    				$par->error = false;
    			
    				$par->voteAllowed = $access->isUserAllowedToVoteAnswer($poll, $answer);
    			} else {
    				$par->max_votes = 0;
    				$par->allowed_votes = 0;
    				$par->count = 0;
    				$par->uservotes = 0;
    				$par->aid = $aid;
    				$par->pid = 0;
    				$par->error = true;
    				$par->error_msg = sprintf(JText::_("JVS_ERROR_LOADING_VOTE_BUTTON"), $this->connect->route("polls", null, "", array(), false));
    			}
    			
    			
    			$par->token = $general->getToken();
    			$par->root = $lib->root()."/";
    			
    			$out = $tmpl->loadTemplate("votebutton", $par);
    			
    			$error = ob_get_contents();
    			if($error) {
    				$log =& VBLog::getInstance();
    				$log->add("ERROR", 'ApiError', array("php_error"=>$error));
    			}
    			ob_end_clean(); 
    			
    			header('content-type: text/html; charset=utf-8');
    			echo $out.$error;
    			
    			exit();
    			
    			break;
    			
    		default:
    			$ar["error"] = JText::_("NOTASKFOUND");
    			break;
    	}    	
		
		$this->output($ar);
    }//function
	
	function output($ar) { 
		global $profiler, $buildTime;
		$ar["servertime"] = JFactory::getDate()->toUnix();
		$ar["time"] = round(($profiler->getmicrotime() - $buildTime)*1000);
		
		$type = strtolower(JRequest::getString("type", "json"));
		
		//Überprüfen ob Fehler ausgegeben wurden
		$error = ob_get_contents();
		$log =& VBLog::getInstance();
		if($error) {
			$log->add("ERROR", 'ApiError', array_merge($ar, array("php_error"=>$error)));
			
			$ar["success"] = false;
			$ar["error"] = $error;
		}
		$log->save();
		
		//foreach($ar AS &$a) if(is_string($a)) $a = str_replace(JUri::root(true), JVS_ROOT, $a);
		
		ob_end_clean();

		switch($type) {
			case "xml":
				$general = $this->connect->get("General");
				
				header('Content-Type: text/xml');
				echo $general->arrayToXML($ar);
				break;
				
			case "json":
			default:
				header('Content-Type: text/plain; charset=utf-8');
				echo json_encode($ar);
				break;
		}
		
		exit();
	}
}//class

$load = new jVoteSystemAPI();
$task = JRequest::getWord("task", null);
$load->get($task);

echo '{"success": false}';
exit();
