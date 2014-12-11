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

jimport( 'joomla.utilities.date' );

class VBAccess
{
	//Variablen
	var $db, $user, $jUser, $document, $jUserSystem;
	private $vote, $params;
	
	private function __construct() {	
		$this->vbparams =& VBParams::getInstance();
		$this->category =& VBCategory::getInstance();
		
		$this->document = & JFactory::getDocument();
		$this->db =& JFactory::getDBO();
		$this->user = VBUser::getInstance();
		$this->jUser = JFactory::getUser();
		if($this->user->id == 0) {
			$this->jUser = JFactory::getUser();
		} else {
			if($this->user->jid != null && $this->user->jid != 0)
				$this->jUser = JFactory::getUser($this->user->jid);
			else
				$this->jUser = JFactory::getUser();
		}
		$this->jUserSystem = JFactory::getUser();
		$this->log =& VBLog::getInstance();
		$this->spam =& VBSpam::getInstance();
		$this->lib =& joomessLibrary::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBAccess();
		}
		return $instance;
	}
	
	function isAdmin($box, $loggedIn = false) {
		if($this->user->id == 0) return false;
		//User ist Admin
		if($this->checkAccessGroup('admin_access', $box, $loggedIn)) return true;
		//User ist JoomlaAdmin
		if($this->isUserAllowedToConfig()) return true;
		
		return false;
	}
	
	function isRunning($box, $started = true, $bool = false) {
		$app =& JFactory::getApplication();
		$date = JFactory::getDate();
		$date->setOffset($app->getCfg('offset'));
		$start = JFactory::getDate($box->start_time);
		$start->setOffset($app->getCfg('offset'));
		$end = JFactory::getDate($box->end_time);
		$end->setOffset($app->getCfg('offset'));
		////Noch nicht angefangen?
		if(($date->toUnix() - $start->toUnix()) < 0 AND $started == true) return $bool ? "pollNotStarted" : false;
		//Schon vorbei
		if(($date->toUnix() - $end->toUnix()) > 0 AND $box->end_time != '0000-00-00 00:00:00') return $bool ? "pollAlreadyOver" : false;
		return true;
	}
	
	function setUser($id) {
		$this->user = $this->user->getUserData($id);
		$this->jUser = JFactory::getUser($this->user->jid);
		$this->jUserSystem = $this->jUser;
	}

    function isUserAllowedToMoveAnswerToTrash($answer,$box) {
		if($this->user->id == 0) return false;
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//User ist Admin
		if($this->isAdmin($box, true)) return true;
		//Kommentare dürfen nicht gelöscht werden
		if($this->vbparams->get('deleteOwnAnswer') == 0) return false;
		//Umfrage schon beendet...
		if(!$this->isRunning($box, false)) return false;
		//User ist Eigentümer
		if($answer->autor_id == $this->user->id AND $this->user->rights == 1) return true;
				
		return false;
	}
	
	function isUserAllowedToMoveCommentToTrash($comment,$box) {
		if($this->user->id == 0) return false;
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//User ist Admin
		if($this->isAdmin($box, true)) return true;
		//Kommentare dürfen nicht gelöscht werden
		if($this->vbparams->get('deleteOwnComment') == 0) return false;
		//User ist Eigentümer
		if($comment->autor_id == $this->user->id AND $this->user->rights == 1) return true;
				
		return false;
	}
	
	function isUserAllowedToChangePublishState($box) {
		if($this->user->id == 0) return false;
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//User ist Admin
		if($this->isAdmin($box, true)) return true;
		
		return false;
	}
	
	function isUserAllowedToAddNewAnswer($box, $login = false) {
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//Im Zeitrahmen
		if(!$this->isRunning($box, false)) return false;
		//User ist Admin
		if($this->isAdmin($box, true)) if($login == true) return 'true'; else return true;
		//Hinzufügen ist von Box verboten
		if($box->add_answer == 0) return false;
		//Umfrage nicht veröffentlicht
		if($box->published == 0 && !($this->user->id == $box->autor_id && $this->user->rights)) return false;
		//User muss bestimmtes Level sein
		if($this->checkAccessGroup('add_answer_access', $box)) {
			if($login == true) return 'true'; else return true;
		} else { if($this->checkAccessRegistered('add_answer_access', $box) AND $login == true) return 'needToLogin'; }
		return false;
	}
	
	function isUserAllowedToAddNewComment($box, $answer, $login = false) {
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//User ist Admin
		if($this->isAdmin($box, true)) if($login == true) return 'true'; else return true;
		//Hinzufügen ist von Box verboten
		if($box->add_comment == 0) return false;
		//Antwort veröffentlicht
		if($answer->published != 1) return false;
		//Umfrage nicht veröffentlicht
		if($box->published == 0 && !($this->user->id == $box->autor_id && $this->user->rights)) return false;
		//User muss bestimmtes Level sein
		if($this->checkAccessGroup('add_comment_access', $box)) {
			if($login == true) return 'true'; else return true;
		} else { if($this->checkAccessRegistered('add_comment_access', $box) AND $login == true) return 'needToLogin'; }
		return false;
	}

	function isUserAllowedToVoteAnswer($box, $answer, $voteown=false, $bool = true) {
		$vote =& VBVote::getInstance();
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return $bool ? false : "noViewRight";
		//Im Zeitrahmen
		if(!$this->isRunning($box)) return $bool ? false : $this->isRunning($box, true, true);
		//Vote-rechte
		if(!$this->checkAccessGroup('access', $box)) return $bool ? false : "noVoteRight";
		//Umfrage veröffentlicht
		if($box->published != 1) return $bool ? false : "pollNotPublished";
		//Antwort veröffentlicht
		if($answer->published != 1 AND $voteown == false) return $bool ? false : "notPublished";
		//Von User erstellt
		if($answer->autor_id == $this->user->id AND $voteown == false AND $this->vbparams->get('voteOnOwn') == 0) return $bool ? false : "noVoteOnOwn";
		//Stimmen verfügbar
		$votes = $vote->getVotesByUser($box->id);
		if($votes->allowed_votes <= 0) return $bool ? false : "noVotesLeft";
		//Max-Votes pro Antwort
		if($box->allowed_votes > $box->max_votes_on_answer) {
			$votesA = $vote->getVotesByUser($box->id, $answer->id);
			if($votesA->votes >= $box->max_votes_on_answer) return $bool ? false : "noVotesAnswerLeft";
		}
		
		return true;
	}
	
	function isUserAllowedToResetVotes($box, $answer) {
		if(!$this->vbparams->get("resetOwnVotes")) return false;
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//Vote-rechte
		if(!$this->checkAccessGroup('access', $box)) return false;
		//Keine Rechte in dieser Sitzung
		if($this->user->rights == 0) return false;
		//Antwort veröffentlicht
		if($answer->published != 1 AND $this->vbparams->get('voteOnOwn') == 0) return false;
		//Im Zeitrahmen
		if(!$this->isRunning($box)) return false;
		
		return true;
	}
	
	function isUserAllowedToReportAnswer($box, $answer) {
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//User ist Admin
		if($this->isAdmin($box)) return false;
		//Spam-Melden verboten
		if($box->activate_spam == 0) return false;
		//Antwort veröffentlicht
		if($answer->published != 1) return false;
		//User hat die Antwort bereits reported
		if($this->spam->checkRow('answer', $answer->id)) return false;
		//User ist Autor der Antwort
		if($answer->autor_id == $this->user->id) return false;
		//Wenn der Kommentar nicht mehr geblockt werden kann..
		if($answer->no_spam_admin == 1) return false;
		//Autor ist Admin
		$autor = $this->user->getUserData($answer->autor_id);
		if($autor->jid != 0) {
			$jAutor = JFactory::getUser($autor->jid);
			if($this->checkAccessGroup('admin_access', $box, false, $jAutor)) return false;
		}
		//Im Zeitrahmen
		if(!$this->isRunning($box, false)) return false;
		
		return true;
	}
	
	function isUserAllowedToReportComment($box, $comment) {
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//User ist Admin
		if($this->isAdmin($box)) return false;
		//Spam-Melden verboten
		if($box->activate_spam == 0) return false;
		//Antwort veröffentlicht
		if($comment->published != 1) return false;
		//User hat die Antwort bereits reported
		if($this->spam->checkRow('comment', $comment->id)) return false;
		//User ist Autor der Antwort
		if($comment->autor_id == $this->user->id) return false;
		//Wenn der Kommentar nicht mehr geblockt werden kann..
		if($comment->no_spam_admin == 1) return false;
		//Autor ist Admin
		$autor = $this->user->getUserData($comment->autor_id);
		$jAutor = JFactory::getUser($autor->jid);
		if($this->checkAccessGroup('admin_access', $box, false, $jAutor)) return false;
		
		return true;
	}
	
	function isUserAllowedToViewUserList($box) {
		//Admin
		if($this->isAdmin($box, true)) return true;
		
		if($this->user->id == 0) return false;
		if(!$this->isUserAllowedToViewPoll($box)) return false;		
		return false;
	}
	
	function isUserAllowedToViewPoll($box) {
		//User is blockiert
		if($this->user->blocked == 1) return false;
		//Admin
		if($this->isAdmin($box, true)) return true;
		
		$cat = $this->category->getCategory($box->catid);
		//Kategorie veröffentlicht
		if($cat->published == 0) return false;
		//Kategorie-Level überprüfen
		if(!$this->checkAccessLevel($cat->accesslevel)) return false;
		
		//Autor
		if($box->autor_id == $this->user->id) return true;
		//Umfrage veröffentlicht
		if($box->published <= 0) return false;
		
		return true;
	}
	
	function isUserAllowedToViewResult($box, $votes, $allowAfterVote = false) {
		//Grundeinstellung erlaubt es
		if(!$this->isUserAllowedToViewPoll($box)) return false;
		//Admin
		if($this->isAdmin($box, true)) return true; 
		//Show Result - Access
		if(!$this->checkAccessGroup('result_access', $box)) return false;
		//Optionen beachten
		switch($box->show_result) {
			default:
			case "always":
				return true;
				break;
			case "afterVote":
				if($allowAfterVote == true) return true;
				//Wenn Umfrage noch läuft..
				if($this->isRunning($box)) {
					//Wenn keine Stimmen mehr übrig..
					if($votes->allowed_votes <= 0) return true;
				} else return true;
				break;
			case "afterDate":
				//Wenn Datum vorbei ist.. dann Ergebnis anzeigen
				$app =& JFactory::getApplication();
				$date = JFactory::getDate();
				$date->setOffset($app->getCfg('offset'));
				$minDate = JFactory::getDate($box->show_result_after_date);
				$minDate->setOffset($app->getCfg('offset'));
				if(($date->toUnix() - $minDate->toUnix()) > 0 OR $box->show_result_after_date == '0000-00-00 00:00:00') return true;
				break;
			case "never":
				return false;
				break;
		}
		return false;
	}
	
	var $guest_usergroup;
	function checkAccessGroup($access, $item, $login = true, $user = null) {
		if($user == null) $user = $this->jUser;
		
		switch($this->lib->getJoomlaVersion()) {
			case joomessLibrary::jVersion15:
				$accessGID = 25;
				if(isset($item->access->$access)) $accessGID = $item->access->$access;
				
				if($login == true) {
					if($user->gid >= $accessGID AND ($this->jUserSystem->id != 0 OR $accessGID == 0)) return true;
				} else {
					if($user->gid >= $accessGID) return true;
				}				
				break;
			default:
				if(!isset($this->guest_usergroup)) {
					jimport('joomla.html.parameter');
					jimport('joomla.application.component.helper');
					$component =& JComponentHelper::getComponent('com_users');
					$params = new JParameter($component->params);
					$this->guest_usergroup = (int)$params->getValue('guest_usergroup', 1);
				}
				
				//Gruppen vom Nutzer
				$uGroups = $user->getAuthorisedGroups(); 
				if($this->jUserSystem->id == 0)
					$uGroups[] = $this->guest_usergroup;
				
				foreach($uGroups as $group) {
					if(isset($item->access->$access))
						if(in_array($group, $item->access->$access))
							if(($login == true AND ($this->jUserSystem->id != 0 OR $group == $this->guest_usergroup)) OR ($login == false))
								return true;
				}
				break;
		}
		return false;
	}
	
	function checkAccessRegistered($access, $item) {
		switch($this->lib->getJoomlaVersion()) {
			case joomessLibrary::jVersion15:
				$accessGID = 25;
				if(isset($item->access->$access)) $accessGID = $item->access->$access;
				
				if($accessGID == 18) return true;
				break;
			default:
				if(isset($item->access->$access))
					if(in_array(2, $item->access->$access))
						return true;
				break;
		}
		return false;
	}
	
	function checkAccessLevel($level) {
		switch($this->lib->getJoomlaVersion()) {
			case joomessLibrary::jVersion15:
				if($level <= $this->jUserSystem->gid) return true;
				break;
			default:
				$levels = $this->jUserSystem->getAuthorisedViewLevels();
					
				foreach($levels AS $l) {
					if($level == $l) return true;
				}
				break;
		}
		return false;
	}
	
	function getHtmlAccessLists($actions, $box) {
	
		//Benutzergruppen laden
		$usergroups = $this->getUserGroups();
		
		//Bilder-Array
		$base = JURI::root(true);
		$images = array();
		$images['allow'] = '<a class="icon-16-allow"> </a>';
		$images['deny'] = '<a class="icon-16-deny"> </a>';
		
		//Werte laden
		$oldActions = $actions;
		$actions = array();
		foreach($oldActions AS $action) {
			$action["values"] = $this->getActionValues($action, $box, $usergroups);
			$actions[] = $action;
		}
		
		//HTML ausgeben
		$html = array();
		
		$html[] = '	<table class="accesstable">';
		
		//Titel ausgeben
		$html[] = '		<tr>';
		$html[] = '			<th class="col1 hidelabeltxt">'.JText::_('JLIB_RULES_GROUPS').'</th>';
		foreach ($actions as $i => $action)
		{
			$html[] = '			<th class="col2" style="width:'.round(75/count($actions)).'%">'.$action["title"].'</th>';
		}
		$html[] = '		</tr>';
		
		//Usergroups & Werte ausgeben
		foreach ($usergroups as $i => $group)
		{
			$html[] = '		<tr class="row'.($i%2).'">';
			$html[] = '			<td class="col1">'.$group["name"].'</td>';
			foreach ($actions as $i => $action)
			{
				$html[] = '			<td class="col2" action="'.$action["name"].'" group="'.$group["value"].'">'.
					(
						$action["values"][$group["value"]] ? $images['allow'] : $images['deny']
					).
					'</td>';
			}
			$html[] = '		</tr>';
		}
		
		$html[] = '	</table>';
		
		//Unsichtbare Selects ausgeben
		foreach ($actions as $i => $action) {
		$html[] = '	<select id="'.$action["name"].'" name="'.$action["name"].'[]" class="accesstable" size="'.count($usergroups).'" multiple="multiple">';
			foreach($usergroups as $group) {
				$html[] = '		<option action="'.$action["name"].'" group="'.$group["value"].'"'.
				(
					$action["values"][$group["value"]] ? ' selected="selected"' : ''
				).
				' value="'.$group["value"].'">'.$group["name"].'</option>';
			}
		$html[] = '	</select>';
		}
		
		//Legende
		$html[] = '	<ul class="accesstable-legend">';
		$html[] = '		<li class="acl-allowed">'.JText::_('JLIB_RULES_ALLOWED').'</li>';
		$html[] = '		<li class="acl-denied">'.JText::_('JLIB_RULES_DENIED').'</li>';
		$html[] = '	</ul>';
		
		return implode("\n", $html);
	}
	
	function storeAccessData($accessList) {
		//Collect Data
		switch($this->lib->getJoomlaVersion()) {
			case joomessLibrary::jVersion15:
				$data = array();
				foreach($accessList AS $access) {
					$var = JRequest::getVar($access, null, "default", "ARRAY");
					$data[$access] = (is_array($var)) ? min($var) : $var;
				}
				break;
			default:
				$data = array();
				foreach($accessList AS $access) {
					$data[$access] = JRequest::getVar($access, null, "default", "ARRAY");
				}
				break;
		}
				
		//Return json-string
		return json_encode($data);
	}
	
	function getActionValues($action, $item, $usergroups) {
		switch($this->lib->getJoomlaVersion()) {
			case joomessLibrary::jVersion15:
				$values = array();
				$aname = $action["name"];
				foreach($usergroups AS $usergroup) {
					if($item->access->$aname <= $usergroup["value"])
						$value = true;
					else
						$value = false;
						
					$values[$usergroup["value"]] = $value;
				}
				break;
			default:
				if($item->id == null) {
					$entrys = array();
					switch($action["name"]) {
						case "poll_access":
						case "access":
						case "result_access":
							$entrys[1] = true;
						case "add_answer_access":
						case "add_comment_access":
							$entrys[2] = true;
						case "add_poll":
							$entrys[3] = true;
						case "edit_poll":
							$entrys[4] = true;
						case "remove_poll":
							$entrys[5] = true;
						case "admin_access":
							$entrys[6] = true;
							$entrys[7] = true;
							$entrys[8] = true;
							break;
					}
				} else {
					$entrys = array();
					$aname = $action["name"]; 
					if(isset($item->access->$aname)) 
						foreach($item->access->$aname AS $gid) 
							$entrys[$gid] = true;
				}
				//Array erstellen
				$values = array();
				foreach($usergroups AS $usergroup) {
					if(isset($entrys[$usergroup["value"]])) {
						$value = true;
					} else {
						$value = false;
					}
				
					$values[$usergroup["value"]] = $value;
				}
				break;
		}
		
		return $values;
	}
	
	function getUserGroups() {
		if(version_compare( JVERSION, '1.6.0', 'lt' )) {
			$groups = array(	array(	"name"		=>	JText::_("PUBLIC_FRONTEND"),
										"value"		=>	0),
								array(	"name"		=>	JText::_("REGISTERED"),
										"value"		=>	18),
								array(	"name"		=>	JText::_("AUTHOR"),
										"value"		=>	19),
								array(	"name"		=>	JText::_("EDITOR"),
										"value"		=>	20),
								array(	"name"		=>	JText::_("PUBLISHER"),
										"value"		=>	21),
								array(	"name"		=>	JText::_("PUBLIC_BACKEND"),
										"value"		=>	22),
								array(	"name"		=>	JText::_("MANAGER"),
										"value"		=>	23),
								array(	"name"		=>	JText::_("ADMINISTRATOR"),
										"value"		=>	24),
								array(	"name"		=>	JText::_("SUPER_ADMINISTRATOR"),
										"value"		=>	25));
		} else {
			$sql = "SELECT `title` AS name, `id` AS value\n"
				. "FROM `#__usergroups`"
				. "ORDER BY `id` ASC";
			$this->db->setQuery($sql);
			$groups = $this->db->loadAssocList();
		}
		
		return $groups;
	}
	
	function getViewLevels() {
		if(version_compare( JVERSION, '1.6.0', 'lt' )) {
			$data = array(		array(	"name"		=>	JText::_("PUBLIC"),
										"value"		=>	0),
								array(	"name"		=>	JText::_("REGISTERED"),
										"value"		=>	18),
								array(	"name"		=>	JText::_("SPECIAL"),
										"value"		=>	19));
		} else {
			$sql = "SELECT `title` AS name, `id` AS value "
			. "FROM `#__viewlevels` "
			. "ORDER BY `ordering` ASC";
			$this->db->setQuery($sql);
			$data = $this->db->loadAssocList();
			
			
		}
		
		$groups = array();
		foreach($data AS $group) {
			$groups[$group["value"]] = $group;
		}
		
		return $groups;
	}
	
	/*************************************************/
	/****** SECTION FOR ADMIN-RIGHTS *****************/
	/*************************************************/
	
	function isUserAllowedToConfig() {
		if(version_compare( JVERSION, '1.6.0', 'lt' )) {
			if($this->jUserSystem->gid >= 24 AND $this->jUserSystem->id != 0) return true;
		} else {
			//Only Joomla 1.6+
			if($this->jUser->authorize( 'core.admin' , 'com_jvotesystem' ) AND $this->jUserSystem->id != 0) {
				return true;
			}
		}
		return false;
	}
	
	/*************************************************/
	/****** SECTION FOR CATEGORY-RIGHTS **************/
	/*************************************************/
	function add($cat) {	
		//Kategorie
		if($cat->id == 0) return false;
		//Admin
		if($this->isUserAllowedToConfig()) return true;
		//Rechte
		if($this->checkAccessGroup('add_poll', $cat)) return true;
			
		return false;
	}
	
	function edit($cat, $box) {	
		if($this->user->id == 0) return false;
		//Admin
		if($this->isUserAllowedToConfig()) return true;
		//Rechte
		if($this->checkAccessGroup('edit_poll', $cat)) return true;
		//Wenn vom gleichen Benutzer erstellt & seine Session aktiv ist
		if($box->autor_id == $this->user->id AND $this->user->rights == 1 AND $cat->edit_own_poll) return true;
		
		return false;
	}
	
	function remove($cat, $box) {	
		if($this->user->id == 0) return false;
		//Admin
		if($this->isUserAllowedToConfig()) return true;
		//Rechte
		if($this->checkAccessGroup('remove_poll', $cat)) return true;
		//Wenn vom gleichen Benutzer erstellt & seine Session aktiv ist
		if($box->autor_id == $this->user->id AND $this->user->rights == 1 AND $cat->remove_own_poll) return true;
		
		return false;
	}
	
	function editState($cat, $box) {	
		if($this->user->id == 0) return false;
		//Admin
		if($this->isUserAllowedToConfig()) return true;
		//Rechte
		if($this->checkAccessGroup('edit_poll', $cat)) return true;		
		
		return false;
	}
	
	/* Access function for the whole modal assistant: IMPORTANT */
	function assistant() {
		$vscat =& VBCategory::getInstance();
		$vote = VBVote::getInstance();
		
		$interface = JRequest::getString("interface", 'site');
		$view = JRequest::getString('view', null);
		$task = JRequest::getString('task', null);
		$id = JRequest::getInt("id", null);
		$catid = JRequest::getInt("catid", null);
		
		$box = $vote->getBox($id);
		
		if($interface == "site") {
			switch($view) {
				case "poll": case "ajax":
					$cat = $vscat->getCategory($catid);
					if($cat->id == 0) $cat = $vscat->getCategory(@$box->catid);
					if($cat->id == 0) return false;
					if($id == null) {
						//Neue Umfrage
						if(!$this->add($cat)) return false;
					} else {
						//Alte Umfrage
						if(!$this->edit($cat, $box)) return false;
					}
					break;
				case "button":
					if($this->jUserSystem->id == 0) return false;
					break;
				default:
					return false;
					break;
			}
		} else {
			switch($view) {
				case "poll": case "ajax":
					if(!$this->isUserAllowedToConfig()) return false;
					break;
				case "button":
					return true;
					break;
				default:
					return false;
			}
		}
		
		return true;
	}
	
	function needCaptcha($box, $task) {
		if($this->isAdmin($box, true)) return false;
		if(!JFactory::getUser()->guest) return false;
		if(!$this->vbparams->get('captcha_'.strtolower($task)))  return false;
		
		switch($task) {
			case "vote":
				//Captcha nur bei erster Abstimmung anzeigen
				$vote =& VBVote::getInstance();
				$votes = $vote->getVotesByUser($box->id);
				
				if($votes->votes > 0) return false;
				break;
			case "addAnswer": case "addComment":
				
				break;
		}
		
		return true;
	}
}//class
