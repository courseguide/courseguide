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
 
class AssistantViewAjax {
	var $document, $db, $user, $id;

	function __construct($task, $interface) {
		$this->document = & JFactory::getDocument();
		$this->db = JFactory::getDBO();
		$this->user =& VBUser::getInstance(true);
		$this->general =& VBGeneral::getInstance();
		$this->vote =& VBVote::getInstance();
		
		//laden
		$this->template =& VBTemplate::getInstance();
		$this->vbparams =& VBParams::getInstance();
		
		$html = array();
		$html["msg"] = "NOTASK";
		switch($task) {
			case "savepoll":
				$this->id = JRequest::getInt('id', null);
				$date = JFactory::getDate();
				$access =& VBAccess::getInstance();
				$vbcategory =& VBCategory::getInstance();
				$type = JRequest::getString("type", "standard");
				
				$admin = ($interface == "administrator");
	
				$ins = new JObject();
				//Kategorie
				$ins->catid = JRequest::getInt("catid", 1);
				$cat = $vbcategory->getCategory($ins->catid);
				
				$new = ($this->id < 1);
				
				if($type != "defaultSettings") {
					//Standarddaten laden
					if($new) { 
						$sql = "SELECT *
								FROM `#__jvotesystem_boxes`
								WHERE `catid`='$ins->catid' AND `published` = -1 ";
						$this->db->setQuery($sql);
						$ins = $this->db->loadObject();
						if(!$ins) {
							$ins = new JObject();
							$ins->id = 0;
							$ins->alias = "";
							$ins->catid = $cat->id;
						}
						
						$defaultId = $ins->id;
						$this->id = 0;
						$ins->published = $cat->autopublish_polls;
					} else {
						$sql = "SELECT *
								FROM `#__jvotesystem_boxes`
								WHERE `id`='$this->id' AND `published` > -1 ";
						$this->db->setQuery($sql);
						$ins = $this->db->loadObject();
						$ins->catid = $cat->id;
					}
					
					$ins->title = JRequest::getString('title');
					$ins->question = JRequest::getVar('question');
				}
				
				$ins->id = $this->id;

				$params = array();
				if($admin || in_array("settings", $cat->allowed_tabs))  {
					$ins->start_time = JRequest::getString("start_time");
					$ins->end_time = JRequest::getString("end_time");
					$ins->add_answer = JRequest::getInt("add_answer");
					$ins->add_comment = JRequest::getInt("add_comment");
					$ins->allowed_votes = JRequest::getInt("allowed_votes");
					$params = array_merge($params, array('max_votes_on_answer', 'show_thankyou_message', 'redirect', 'redirectPage', 'show_result', 'show_result_after_date'));
				}
				if($admin || in_array("display", $cat->allowed_tabs)) $params = array_merge($params, array('activate_ranking', 'ranking_orderby', 'ranking_orderby_direction', 'answers_orderby', 'answers_orderby_direction', 'show_author', 'template', 'chart_type', 'answers_per_page', 'comments_per_page', 'barcount', 'show_searchfield'));
				if($admin || in_array("email_spam", $cat->allowed_tabs)) $params = array_merge($params, array('send_mail_admin_answer', 'send_mail_user_answer_comments', 'send_mail_admin_comment', 'activate_spam', 'spam_count', 'spam_mail_admin_report', 'spam_mail_admin_ban'));
				
				// --> in Params zusammenfassen
				if(isset($ins->params)) $values = json_decode($ins->params);
				else $values = new JObject();
				//Template - Parameter
				if($admin) {
					$data = JRequest::get();
					foreach($data AS $key => $value) {
						if(substr($key, 0, 5) == "tmpl_") {
							$values->$key = $value;
						}
					}
				}
				//Haupteinstellungen
				foreach($params AS $param) {
					$values->$param = JRequest::getString($param, null);
					switch($param) {
						case 'template': //Check if template really exists
							$templates = VBTemplate::getInstance()->getTemplates(true);	
							if(!in_array($values->$param, $templates)) $values->$param = 'default';						
							break;
					}
				}
				$ins->params = json_encode($values);
				
				if(in_array("access", $cat->allowed_tabs) || $admin) 
					$ins->access = $access->storeAccessData(array( 'access', 'result_access', 'admin_access', 'add_answer_access', 'add_comment_access' ));
				
				if($type != "defaultSettings") {
					$curAlias = $ins->alias;
					
					if($admin) $ins->alias = JRequest::getString("alias", "");
					if($ins->alias == "") $ins->alias = $ins->title; 
					$ins->alias = $this->general->cleanStr($ins->alias); 
					if($ins->alias != $curAlias) $ins->alias = $this->general->checkAlias($ins->alias);
					$html["alias"] = $ins->alias;
				} else {
					$ins->published = -1;
				}
				
				$log = VBLog::getInstance();
				if($new) {
					//Neues Element
					$ins->autor_id = $this->user->id;
					$ins->created = $date->toMySQL();
					$ins->ordering = 0;
					
					$this->db->insertObject('#__jvotesystem_boxes', $ins);
					
					$this->id = $this->db->insertid();
					
					if(!$admin)
						$html["redirect"] = $this->general->buildLink("poll", $this->id, "", array( "notifi" => "add_success" ));
					
					//Tasks
					if($ins->published == 0)
						VBTasks::getInstance()->addTask(VBTasks::$_Poll, $this->id);
					
					$log->add("DB", 'AddedPoll', array( "id"=> $this->id, "admin" => $admin ));
					
					VBMail::getInstance()->addJob("addedPoll", array("poll" => VBVote::getInstance()->getBox($this->id), "cat" => $cat));
				} else {
					//Updaten
					$this->db->updateObject('#__jvotesystem_boxes', $ins, 'id');
					$this->id = $ins->id;
					
					$log->add("DB", 'UpdatedPoll', array( "id"=> $this->id, "admin" => $admin ));
				}
				
				$html["id"] = $this->id;
				
				if($this->db->getErrorMsg())
					$msg = JText::_('Error_Saving_Record');
				else
					$msg = JText::_('Record_Saved');
				
				if($type != "defaultSettings") {
					//Antworten speichern
					$answers = JRequest::getVar("answers", null, "default", "ARRAY");
					$a_ids = JRequest::getVar("a_id", null, "default", "ARRAY");
					$a_states = JRequest::getVar("a_state", null, "default", "ARRAY");
					$a_colors = JRequest::getVar("a_color", null, "default", "ARRAY");
					
					$newIDs = "";
					$removedIDs = "";
					if($answers) {
						foreach($answers AS $i => $answer) {
							$ins = new JObject();
							$ins->id = $a_ids[$i];
							$ins->box_id = $this->id;
							$ins->answer = $answer;
							$ins->published = $a_states[$i];
							$ins->color = substr($a_colors[$i], 1);
							
							if($ins->id == 0 AND $ins->published >= 0) {
								//Neue Antwort erstelln
								$ins->autor_id = $this->user->id;
								$ins->created = $date->toMySQL();
								$this->db->insertObject('#__jvotesystem_answers', $ins);
								$newIDs .= $this->db->insertid().",";
								
								//Color-index hochzählen
								$values->cur_color_index++;
							} elseif($ins->id > 0) {
								//Alte Antwort updaten
								if($ins->published == -1) {
									//Antwort entfernen
									$sql = 'DELETE FROM `#__jvotesystem_answers` '
									. ' WHERE `id` = '.$ins->id
									. ' LIMIT 1'; 
									$this->db->setQuery($sql);
									$this->db->query();
									//Votes l�schen
									$sql = 'DELETE FROM `#__jvotesystem_votes` '
									. ' WHERE `answer_id` = '.$ins->id; 
									$this->db->setQuery($sql);
									$this->db->query();
									//Kommentare l�schen
									$sql = 'DELETE FROM `#__jvotesystem_comments` '
									. ' WHERE `answer_id` = '.$ins->id; 
									$this->db->setQuery($sql);
									$this->db->query();
									
									$removedIDs .= $ins->id.",";
								} else {
									//Updaten
									$this->db->updateObject('#__jvotesystem_answers', $ins, 'id');
								}
							}
						}
					}
					
					//Neuen Farb-index speichern, wenn nötig
					if($newIDs != "") {
						$upd = new JObject();
						$upd->id = $this->id;
						$upd->params = json_encode($values);
						
						$this->db->updateObject('#__jvotesystem_boxes', $upd, 'id');
					}
					
					$html["newids"] = $newIDs;
					$html["removedids"] = $removedIDs;
					
					//Cache clearen
					jimport("joomla.cache.cache");
					JFactory::getCache()->clean('jVoteSystem - Lists');
				}
				
				$html["msg"] = $msg;
				
				break;
		}
		
		VBMail::getInstance()->runJobs();
		
		//ausgeben
		foreach($html as $key => $code) {
			echo $key.'='.urlencode(editLink($code)).'&';
		}
	}
}
?>