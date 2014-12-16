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

class VBLog
{
	//Construct
	private $db, $doc, $user, $logs, $date, $general;
	public $pages, $total, $log_dir;
	
	private function __construct() {
		$this->doc = & JFactory::getDocument();
		$this->db =& JFactory::getDBO();
		$this->user =& JFactory::getUser();
		$this->date =& JFactory::getDate();
		$this->logs = array();
		$this->log_dir = JPATH_SITE.DS."administrator".DS."components".DS."com_jvotesystem".DS."logs";
		
		$this->vsuser =& VBUser::getInstance();
		$this->vbparams =& VBParams::getInstance();
		$this->general =& VBGeneral::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBLog();
		}
		return $instance;
	}
	//Destruct
	public function save() {
		//Save logs in DB - short time peroid
		foreach($this->logs AS $log) {
			$ins = new JObject();
			$ins->type 		= strtoupper($log["type"]);
			$ins->time 		= $log["time"]["sec"];
			$ins->time_ms 	= $log["time"]["usec"];
			$ins->uid 		= $log["user"];
			$ins->jvsuid 	= $this->vsuser->id;
			$ins->message 	= $log["msg"];
			$ins->pars 		= $log["pars"]; 
			
			$this->db->insertObject('#__jvotesystem_logs', $ins);
			if($this->db->getErrorMsg()) echo $this->db->getErrorMsg();
		}
		$this->logs = array();
		
		//Clean up DB all 10 minutes
		if(rand(1, 3) == 2) $this->performDatabaseCleanUp(); //33% chance of running
	}
	
	function add($type, $message, $pars = array(), $user = null, $force = false) { 
		$this->logs[] = array(	"msg"	=>	$message,
								"type"	=>	$type,
								"pars"	=>	json_encode($pars),
								"user"	=>	($user == null) ? $this->user->id : $user->id,
								"time" 	=> 	gettimeofday() );
		if($force) $this->save();
	}
	
	function performDatabaseCleanUp() {
		
		$lib =& joomessLibrary::getInstance();
		
		// Check if you need to clear Cache - all
		$last = $lib->getParam("jvs_log_last_run", 0);
		$now = time();
		if(abs($now-$last) < 600) return false;
		
		// Update last run status
		$lib->setParam('jvs_log_last_run', $now, true);
		//Read database entries -> max 50 (keep 10)
		$sql = ' SELECT * FROM `#__jvotesystem_logs` ORDER BY `time` DESC, `time_ms` DESC LIMIT 10, 50';
		$this->db->setQuery($sql);
		$logs = $this->db->loadObjectList();
		
		if(empty($logs)) return true;
		
		if($this->vbparams->get("logging") != 0) {
			$logs = array_reverse($logs);
			//Prepare file content
			$content = "";
			foreach($logs AS $log) {
				if($this->vbparams->get("logging") == 1 || $log->type == "ERROR")
					$content .= "\n[".$log->type."]\t".JFactory::getDate($log->time)->toMySQL()."\t".$log->uid."\t".$log->jvsuid."\t".$log->message."\t".$log->pars;
			}
			
			//Write file
			$path = $this->log_dir.DS.$this->date->toFormat("%Y.%m").".php";
				//Check file
				if(JFile::exists($path)) $old = JFile::read($path);
				else $old = '#<?php die("Forbidden."); ?>' . "\n"
							  . '#Date: '.$this->date->toMySQL().' UTC' . "\n"
							  . '#Software: jVoteSystem for Joomla by www.joomess.de' . "\n"
							  . "\n"
							  . '#Fields: type	date-time	uid	jvsuid		message						parameters';
				//Add Logs
				$content = $old.$content;
				//Write File
				if(!JFile::write($path, $content)) return false;
		}
		
		//Delete db entries
		$sql = ' DELETE FROM `#__jvotesystem_logs` WHERE ';
		foreach($logs AS $i => $log) {
			if($i != 0) $sql .= " OR ";
			$sql .= ' (`time` = "'.$log->time.'" && `time_ms` = "'.$log->time_ms.'" )';
		}
		$sql .= ' LIMIT 30 ';
		$this->db->setQuery($sql);
		return $this->db->query();
	}
	
	function getFiles($full = false) {
		$files = JFolder::files($this->log_dir, ".php", false, $full);
		sort($files);
		if(count($files) > 0 && substr($files[count($files) - 1], 0, -4) != $this->date->toFormat("%Y.%m")) $files[] = $this->date->toFormat("%Y.%m").".php";
		return $files;
	}
	
	private $lastID;
	function lastID() {
		return $this->lastID["time"].".".$this->lastID["time_ms"];
	}
	
	function checkForNewEntries($lastIDraw) {
		$lastIDsplit = explode('.', $lastIDraw);
		$lastID = array( "time" => $lastIDsplit[0], "time_ms" => @$lastIDsplit[1] );
		if($lastID["time"] == 0 && !isset($this->lastID)) {
			$this->db->setQuery('SELECT `time`, `time_ms` FROM `#__jvotesystem_logs` ORDER BY `time` DESC, `time_ms` DESC LIMIT 0, 1');
			if($cur = $this->db->loadAssoc()) $this->lastID = $cur;
			else $this->lastID = array( "time" => 0, "time_ms" => 0 );
		} elseif( $lastID["time"] > $this->lastID["time"] || ($lastID["time"] >= $this->lastID["time"] && $lastID["time_ms"] > $this->lastID["time_ms"]) ) $this->lastID = $lastID;
		
		$this->db->setQuery('SELECT `time`, `time_ms` FROM `#__jvotesystem_logs` WHERE `time` > '.$this->db->quote($this->lastID["time"]).' OR (`time` >= '.$this->db->quote($this->lastID["time"]).' && `time_ms` > '.$this->db->quote($this->lastID["time_ms"]).') ORDER BY `time` DESC, `time_ms` DESC LIMIT 0, 1');
		if($cur = $this->db->loadAssoc()) {
			$last = $this->lastID;
			$this->lastID = $cur;
			return $last["time"].".".$last["time_ms"];
		} else return false;
	}
	
	function getDBEntries(&$logs, $id_limit_raw = 0, $count_limit = 0) {
		$limitIDsplit = explode('.', $id_limit_raw);
		$id_limit = array( "time" => $limitIDsplit[0], "time_ms" => @$limitIDsplit[1] );
		$sql = ' SELECT * FROM `#__jvotesystem_logs` WHERE `time` > '.$this->db->quote($id_limit["time"]).' OR (`time` >= '.$this->db->quote($id_limit["time"]).' && `time_ms` > '.$this->db->quote($id_limit["time_ms"]).') ORDER BY `time` DESC, `time_ms` DESC ';
		if($count_limit != 0) $sql .= ' LIMIT 0, '.$count_limit;
		$this->db->setQuery($sql);
		$dblogs = $this->db->loadObjectList();
			
		foreach($dblogs AS $dlog) {
			$log = new JObject();
			$log->id = $dlog->time.".".$dlog->time_ms;
			$log->type = $dlog->type;
			$log->created = $dlog->time;
			$log->jid = $dlog->uid;
			$log->vsid = $dlog->jvsuid;
			$log->msg = $dlog->message;
			$log->pars = json_decode($dlog->pars);
		
			$logs[] = $log;
		}
	}
	
	function getEntries($file, $slimit = 0, $page = 1, $onlyDB = false) {
		$limit = $slimit * $page;
		
		$logs = array();
		
		if($file == "latest") $file = $this->date->toFormat("%Y.%m").".php";
		
		//When file is current => add latest db entries
		if(substr($file, 0, -4) == $this->date->toFormat("%Y.%m")) {
			$this->getDBEntries($logs, 0, $limit);
		}
		
		if(JFile::exists($this->log_dir.DS.$file) && !$onlyDB) {
			$content = JFile::read($this->log_dir.DS.$file);
				
			$lines = explode("\n", $content);
			
			$this->total = count($lines) + count($logs);
			$this->pages = (int) ceil( $this->total / $slimit );
			
			if($limit != 0 && count($logs) >= $limit) {
				
			} else {
				$lines = array_reverse($lines);
				//Read logs of the file
				foreach($lines AS $line) {
					//Skip Comments || Empty Values
					if(substr($line, 0, 1) != "#" && $line != "") {
						$data = explode("\t", $line);
							
						if(count($data) > 5) {
							$log = new JObject();
							$log->type = trim($data[0], "[]");
							$log->created = $data[1];
							$log->jid = (int)$data[2];
							$log->vsid = (int)$data[3];
							$log->msg = $data[4];
							$log->pars = json_decode($data[5]);
								
							$logs[] = $log;
								
							if($limit != 0 && count($logs) >= $limit) break;
						}
					}
				}
			}
		}
		
		$logs = array_splice($logs, $slimit * ($page-1) );
		
		return $logs;
	}

    function convertMsg($line) {
    	$str = JText::_('JVS_LOG_'.$line->msg);
    	$vote =& VBVote::getInstance();
    	$vbanswer =& VBAnswer::getInstance();
    	$vbcomment =& VBComment::getInstance();
    	
    	switch(strtolower($line->msg)) {
    		case "addedvoting":
    			$out = sprintf($str, 
    				$this->general->convertUser(@$line->vsid), 
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->aid),
    					($answer = $vbanswer->getAnswer(@$line->pars->aid)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->aid
    				),  
    				$this->general->buildAdminLink(
    					"poll", 
    					@$line->pars->bid, 
    					($box = $vote->getBox(@$line->pars->bid)) ? $box->title : @$line->pars->bid
    				)
    			);
    			$line->action = "add";
    			break;
    		case "resettedvoting":
    			$out = sprintf($str, 
    				$this->general->convertUser(@$line->vsid),  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->aid),
    					($answer = $vbanswer->getAnswer(@$line->pars->aid)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->aid
    				),  
    				$this->general->buildAdminLink(
    					"poll", 
    					@$line->pars->bid, 
    					($box = $vote->getBox(@$line->pars->bid)) ? $box->title : @$line->pars->bid
    				), 
    				@$line->pars->votes
    			);
    			$line->action = "remove";
    			break;
    		case "addedanswer":
    			$out = sprintf($str, 
    				$this->general->convertUser(@$line->vsid),  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->id),
    					($answer = $vbanswer->getAnswer(@$line->pars->id)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->id
    				),  
    				$this->general->buildAdminLink(
    					"poll", 
    					@$answer->box_id, 
    					($box = $vote->getBox(@$answer->box_id)) ? $box->title : @$answer->box_id
    				)
    			);
    			$line->action = "add";
    			break;
    		case "addedcomment":
    			$out = sprintf($str, 
    				$this->general->convertUser(@$line->vsid),   
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("comment", @$line->pars->id),
    					($comment = $vbcomment->getComment(@$line->pars->id)) ? $this->general->shortText($comment->comment, 60, false, false) : $line->pars->id
    				),  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$comment->answer_id),
    					($answer = $vbanswer->getAnswer(@$comment->answer_id)) ? $this->general->shortText($answer->answer, 60, false, false) : @$comment->answer_id
    				)
    			);
    			$line->action = "add";
    			break;
    		case "updatedvoting":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),
    				@$line->pars->votes,  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->aid),
    					($answer = $vbanswer->getAnswer(@$line->pars->aid)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->aid
    				),  
    				$this->general->buildAdminLink(
    					"poll", 
    					@$line->pars->bid, 
    					($box = $vote->getBox(@$line->pars->bid)) ? $box->title : @$line->pars->bid
    				)
    			);
    			$line->action = "edit";
    			break;
    		case "removedanswer":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),
    				isset($line->pars->answer) ? $this->general->shortText($line->pars->answer, 60, false, false) : @$line->pars->id,
    				@$line->pars->votes,
    				@$line->pars->comments
    			);
    			$line->action = "remove";
    			break;
    		case "removedcomment":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),
    				isset($line->pars->comment) ? $this->general->shortText($line->pars->comment, 60, false, false) : @$line->pars->id
    			);
    			$line->action = "remove";
    			break;
    		case "userreachedvotelimit":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),  
    				$this->general->buildAdminLink(
    					"poll", 
    					@$line->pars->bid, 
    					($box = $vote->getBox(@$line->pars->bid)) ? $box->title : @$line->pars->bid
    				)
    			);
    			break;
    		case "userreachedvotelimitperanswer":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),
    				@$line->pars->limit,  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->aid),
    					($answer = $vbanswer->getAnswer(@$line->pars->aid)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->aid
    				),  
    				$this->general->buildAdminLink(
    					"poll", 
    					@$line->pars->bid, 
    					($box = $vote->getBox(@$line->pars->bid)) ? $box->title : @$line->pars->bid
    				)
    			);
    			break;
    		case "ajaxscripterror":
    			$out = '<div style="max-height: 100px; overflow: auto; max-width: 600px;">';
	    			$out .= sprintf($str,
	    				urldecode(@$line->pars->php_error)
	    			);
	    		$out .= '</div>';
    			break;
    		case "cleanedupusersessiondata":
    			$out = $str."<ul>";
    			foreach($line->pars AS $row) {
    				$out .= "<li>"; 
    					$out .= sprintf(JText::_("JVS_LOG_REMOVEDENTRIESOFUSER"), 
    								@$row->rows,
    								$this->general->convertUser(@$row->uid)
    							);
    				$out .= "</li>";
    			}    			
    			$out .= "</ul>";
    			$line->action = "clean";
    			break;
    		case "cleanedupemailtasks":
    		case "cleanedupsessiondata":
    			$out = sprintf($str,
    				@$line->pars->rows,
    				@$line->pars->period
    			);
    			$line->action = "clean";
    			break;
    		case "votingmissingparameters":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid)
    			);
    			break;
    		case "changedpublishstateanswer":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->id),
    					($answer = $vbanswer->getAnswer(@$line->pars->id)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->id
    				),
    				(@$line->pars->state) ? JText::_("JVS_LOG_Published") : JText::_("JVS_LOG_UnPublished")
    			);
    			$line->action = isset($line->pars->state) ? ( ($line->pars->state) ? "allow" : "deny" ) : "unknown";
    			break;
    		case "changedpublishstatecomment":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),   
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("comment", @$line->pars->id),
    					($comment = $vbcomment->getComment(@$line->pars->id)) ? $this->general->shortText($comment->comment, 60, false, false) : $line->pars->id
    				),  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$comment->answer_id),
    					($answer = $vbanswer->getAnswer(@$comment->answer_id)) ? $this->general->shortText($answer->answer, 60, false, false) : @$comment->answer_id
    				),
    				(@$line->pars->state) ? JText::_("JVS_LOG_Published") : JText::_("JVS_LOG_UnPublished")
    			);
    			$line->action = isset($line->pars->state) ? ( ($line->pars->state) ? "allow" : "deny" ) : "unknown";
    			break;
    		case "bannedanswer":
    			$out = sprintf($str, 
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->id),
    					($answer = $vbanswer->getAnswer(@$line->pars->id)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->id
    				),
    				@$line->pars->reports
    			);
    			
    			$line->action = "deny";
    			break;
    		case "reportedanswer":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("answer", @$line->pars->id),
    					($answer = $vbanswer->getAnswer(@$line->pars->id)) ? $this->general->shortText($answer->answer, 60, false, false) : @$line->pars->id
    				),
    				(@$line->pars->msg != "") ? $line->pars->msg : JText::_("JVS_LOG_NO_MESSAGE")
    			);
    			
    			$line->action = "notice";
    			break;
    		case "bannedcomment":
    			$out = sprintf($str, 
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("comment", @$line->pars->id),
    					($comment = $vbcomment->getComment(@$line->pars->id)) ? $this->general->shortText($comment->comment, 60, false, false) : @$line->pars->id
    				),
    				@$line->pars->reports
    			);
    			
    			$line->action = "deny";
    			break;
    		case "reportedcomment":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),  
    				$this->general->buildHtmlLink(
    					$this->general->buildAdminLink("comment", @$line->pars->id),
    					($comment = $vbcomment->getComment(@$line->pars->id)) ? $this->general->shortText($comment->comment, 60, false, false) : @$line->pars->id
    				),
    				(@$line->pars->msg != "") ? $line->pars->msg : JText::_("JVS_LOG_NO_MESSAGE")
    			);
    			
    			$line->action = "notice";
    			break;
    		case "removedpoll":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),
    				isset($line->pars->title) ? $this->general->shortText($line->pars->title, 60, false, false) : @$line->pars->id,
    				@$line->pars->answers,
    				@$line->pars->votes,
    				@$line->pars->comments,
    				@$line->pars->spam_reports
    			);
    			$line->action = "remove";
    			break;
    		case "updatedpoll":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),
    				$this->general->buildAdminLink(
    					"poll", 
    					@$line->pars->id, 
    					($box = $vote->getBox(@$line->pars->id)) ? $box->title : @$line->pars->id 
    				),
    				(@$line->pars->admin) ? JText::_("JVS_LOG_BACKEND") : JText::_("JVS_LOG_FRONTEND")
    			);
    			$line->action = "edit";
    			break;
    		case "addedpoll":
    			$out = sprintf($str,
    				$this->general->convertUser(@$line->vsid),
    				$this->general->buildAdminLink(
    					"poll", 
    					@$line->pars->id, 
    					($box = $vote->getBox(@$line->pars->id)) ? $box->title : @$line->pars->id 
    				),
    				(@$line->pars->admin) ? JText::_("JVS_LOG_BACKEND") : JText::_("JVS_LOG_FRONTEND")
    			);
    			$line->action = "edit";
    			break;
    		case "changedstatepoll":
    			$out = sprintf($str,
    					$this->general->convertUser(@$line->vsid),
    					$this->general->buildAdminLink(
    							"poll",
    							@$line->pars->id,
    							($box = $vote->getBox(@$line->pars->id)) ? $box->title : @$line->pars->id
    					),
    					(@$line->pars->state) ? JText::_("JVS_LOG_Published") : JText::_("JVS_LOG_UnPublished")
    			);
    			$line->action = isset($line->pars->state) ? ( ($line->pars->state) ? "allow" : "deny" ) : "unknown";
    			break;
    			
    		case "sendedmails":
    			//Vorschau
    			if(isset($line->pars->pars) && isset($line->pars->tmpl)) {
    				$tmpl = VBTemplate::getInstance();
    				
    				$content = $tmpl->loadTemplate('mails/'.$line->pars->tmpl, $line->pars->pars);
    				
    				$par = new JObject();
    				$par->subject = 'Preview';
    				$par->content = $content;
    				
    				$preview = $tmpl->loadTemplate('mails/html', $par);
    			} else $preview = JText::_("JVS_LOG_NO_PREVIEW_AVAILABLE");
    			
    			//Benutzerliste
    			$userList = array();
    			if(isset($line->pars->users)) {
    				$userList[] = '<ul>';
    				foreach($line->pars->users AS $user) {
    					$userList[] = '<li>'.$this->general->convertUser($user->id).': '.$user->mail.' '.(@$user->success ? '✔' : 'X').' </li>';
    				}
    				$userList[] = '</ul>';
    			}
    			
    			//Ausgabe
    			if(@$line->pars->success) {
    				$out = sprintf(JText::_("JVS_LOG_SENDED_MAILS"),
    						JText::_("JVS_LOG_MAILS_".@$line->pars->tmpl.( isset($line->pars->group) ? "_".$line->pars->group : "" )),
    						urlencode($preview),
    						@count($line->pars->users),
    						urlencode(implode('', $userList))
    					);
    			} else {
    				$out = sprintf(JText::_("JVS_LOG_FAILED_TO_SEND_MAILS"),
    						JText::_("JVS_LOG_MAILS_".@$line->pars->tmpl.( isset($line->pars->group) ? "_".$line->pars->group : "" )),
    						urlencode($preview),
    						@count($line->pars->users),
    						urlencode(implode('', $userList))
    					);
    			}
    			
    			$line->action = isset($line->pars->success) ? ($line->pars->success ? "allow" : "error") : "unknown";
    			break;
    			
    		default:
    			$out = $str;
    			
    			$line->action = "unknown";
    			break;
    	}
    	
    	$line->msg = $out;
    	if(!isset($line->action)) $line->action = "none";
    	
    	return $line;
    }
}//class
