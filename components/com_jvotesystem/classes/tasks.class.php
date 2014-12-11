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

class VBTasks
{

	private function __construct() { 
		$this->db =& JFactory::getDBO();
		$this->document = & JFactory::getDocument();
		$this->general =& VBGeneral::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBTasks();
		}
		return $instance;
	}
	
	//Tasks
	public static $_Answer 			= "Answer";
	public static $_Poll 			= "Poll";
	public static $_Comment			= "Comment";
	public static $_Spam_Answer		= "Spam:Answer";
	public static $_Spam_Comment	= "Spam:Comment";
	
	public function addTask( $group, $id ) {
		//Wenn vorhanden, abbrechen
		$this->db->setQuery( ' SELECT * FROM `#__jvotesystem_tasks` WHERE `group` = '.$this->db->quote($group).' AND `id` = '.$this->db->quote($id) );
		if($this->db->loadResult()) return false;
		
		//Zur Datenbank hinzufügen
		$ins = new JObject();
		$ins->group = $group;
		$ins->id	= $id;
		
		$this->db->insertObject('#__jvotesystem_tasks', $ins);
		if($this->db->getErrorMsg()) VBLog::getInstance()->add('ERROR', 'FailedToStoreTask', array( "group" => $group, "id" => $id, "db_error" => $this->db->getErrorMsg() ));
	}
	
	public function removeTask( $group, $id ) {
		$this->db->setQuery( ' DELETE FROM `#__jvotesystem_tasks` WHERE `group` = '.$this->db->quote($group).' AND `id` = '.$this->db->quote($id).' LIMIT 1' );
		$this->db->query();
		if($this->db->getErrorMsg()) VBLog::getInstance()->add('ERROR', 'FailedToRemoveTask', array( "group" => $group, "id" => $id, "db_error" => $this->db->getErrorMsg() ));
	}
	
	public function getTaskList() {
		//JS & CSS laden
		$lib =& joomessLibrary::getInstance();
		$lib->js('components/com_jvotesystem/assets/js/jvotesystem.js');
		$lib->css('administrator/components/com_jvotesystem/assets/css/tasks.css');
		
		$html = array();
		$html[] = '<div class="tasks jvotesystem">';
		
		//Update nötig?
		$upd = VBUpdate::getInstance()->needUpdate();
		if($upd || $upd == -1) {
			$data = VBUpdate::getInstance()->getServerData();
			//Nachricht ausgeben
			$html[] = '<a href="'.VBUpdate::getInstance()->getDownloadLink().'" target="_blank">';
				$html[] = '<p class="task_msg t_'.((!$data) ? 'unknownUpdate' : 'needUpdate').'">';
					$html[] = '<span class="count">'.((!$data) ? '?' : $data->version).'</span>';
					$html[] = JText::_('JVS_TASKS_'.((!$data) ? 'unknownUpdate' : 'needUpdate'));
				$html[] = '</p>';		
			$html[] = '</a>';		
		}
		
		//Aus Datenbank abrufen
		$this->db->setQuery(' SELECT * FROM `#__jvotesystem_tasks` ORDER BY `group` ');
		$tasks = $this->db->loadObjectList();
		
		if(empty($tasks) && count($html) == 1) return "";
		
		//Gruppieren & Vorbereiten
		$data = array();
		foreach($tasks AS $task) {
			if(!isset($data[$task->group])) $data[$task->group] = array( "data" => array(), "count" => 0 );
			
			switch($task->group) {
				case self::$_Answer:
					$answer = VBAnswer::getInstance()->getAnswer($task->id, false); if(!$answer) break;
					
					if(!isset($data[$task->group]["data"][$answer->box_id])) $data[$task->group]["data"][$answer->box_id] = array();
					$data[$task->group]["data"][$answer->box_id][] = $answer;
					$data[$task->group]["count"]++;
					break;
				case self::$_Comment:
					$comment = VBComment::getInstance()->getComment($task->id); if(!$comment) break;
					
					if(!isset($data[$task->group]["data"][$comment->answer_id])) $data[$task->group]["data"][$comment->answer_id] = array();
					$data[$task->group]["data"][$comment->answer_id][] = $comment;
					$data[$task->group]["count"]++;
					break;
				case self::$_Poll:
					$poll = VBVote::getInstance()->getBox($task->id); if(!$poll) break;
					
					$data[$task->group]["data"][$poll->id] = $poll;
					$data[$task->group]["count"]++;
					break;
				case self::$_Spam_Answer:
					$reports = VBSpam::getInstance()->getReports('answer', $task->id);
					
					$data[$task->group]["data"][$task->id] = $reports;
					$data[$task->group]["count"] += count($reports);
					break;
				case self::$_Spam_Comment:
					$reports = VBSpam::getInstance()->getReports('comment', $task->id);
					
					$data[$task->group]["data"][$task->id] = $reports;
					$data[$task->group]["count"] += count($reports);
					break;
				default:
					$data[$task->group]["data"][] = $task->id;
					$data[$task->group]["count"]++;
					break;
			}
		}
		
		//Html erzeugen
		foreach($data AS $group => $pars) {
			//Nachricht ausgeben
			$html[] = '<p class="task_msg t_'.str_replace(":", "_", $group).'">';
				$html[] = '<span class="count">'.$pars["count"].'</span>';
				$html[] = JText::_('JVS_TASKS_'.str_replace(":", "_", $group));
			$html[] = '</p>';
			//Ausgeblendete Daten ausgeben
			$html[] = '<div style="display:none;">';
			switch($group) {
				case self::$_Answer:
					foreach($pars["data"] AS $pid => $answers) { 
						$poll = VBVote::getInstance()->getBox($pid); if(!$poll) break;
						$html[] = '<div class="task_element">';
							//Titel & Frage ausgeben
							$html[] = '<div class="task_head">';
								$html[] = '<b>'.$poll->title.'</b> - ';
								$html[] = $this->general->shortText($poll->question, 100, false, false);
							$html[] = '</div>';
							foreach($answers AS $answer) {
								$html[] = '<div class="task_data">';
									$html[] = $this->general->buildHtmlLink($this->general->buildAdminLink("user", $answer->autor_id), VBUser::getInstance()->getAvatar($answer->autor_id, 16, true, false) );
									
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_REMOVE").'" onclick="jVS.submitTask(this, '." '".$group."', 'answer', 'remove', ".$answer->id.'); return false;" class="icon-16 icon-remove"> </a>';
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_PUBLISH").'" onclick="jVS.submitTask(this, '." '".$group."', 'answer', 'publish', ".$answer->id.'); return false;" class="icon-16 icon-allow"> </a>';
									
									$html[] = $this->general->buildHtmlLink($this->general->buildAdminLink("answer", $answer->id), $this->general->shortText($answer->answer, 100, false, false));
								$html[] = '</div>';
							}
						$html[] = '</div>';
					}
					
					break;
				case self::$_Comment:
					foreach($pars["data"] AS $aid => $comments) {
						$answer = VBAnswer::getInstance()->getAnswer($aid, false); if(!$answer) break;
						$poll = VBVote::getInstance()->getBox($answer->box_id); if(!$poll) break;
						$html[] = '<div class="task_element">';
							//Antwort ausgeben
							$html[] = '<div class="task_head">';
								$html[] = '<b>'.$this->general->buildHtmlLink($this->general->buildAdminLink("answer", $answer->id), $this->general->shortText($answer->answer, 120, false, false) ).'</b>';
								$html[] = ' ('.$poll->title.')';
							$html[] = '</div>';
							foreach($comments AS $comment) {
								$html[] = '<div class="task_data">';
									$html[] = $this->general->buildHtmlLink($this->general->buildAdminLink("user", $comment->autor_id), VBUser::getInstance()->getAvatar($comment->autor_id, 16, true, false) );
								
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_REMOVE").'" onclick="jVS.submitTask(this, '." '".$group."', 'comment', 'remove', ".$comment->id.'); return false;" class="icon-16 icon-remove"> </a>';
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_PUBLISH").'" onclick="jVS.submitTask(this, '." '".$group."', 'comment', 'publish', ".$comment->id.'); return false;" class="icon-16 icon-allow"> </a>';
										
									$html[] = $this->general->buildHtmlLink($this->general->buildAdminLink("comment", $comment->id), $this->general->shortText($comment->comment, 100, false, false));
								$html[] = '</div>';
							}
						$html[] = '</div>';
					}
					break;
				case self::$_Poll:
					foreach($pars["data"] AS $pid => $poll) {
						$html[] = '<div class="task_element">';
							$html[] = '<div class="task_data">';
								$html[] = $this->general->buildHtmlLink($this->general->buildAdminLink("user", $poll->autor_id), VBUser::getInstance()->getAvatar($poll->autor_id, 16, true, false) );
								
								$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_REMOVE").'" onclick="jVS.submitTask(this, '." '".$group."', 'poll', 'remove', ".$poll->id.'); return false;" class="icon-16 icon-remove"> </a>';
								$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_PUBLISH").'" onclick="jVS.submitTask(this, '." '".$group."', 'poll', 'publish', ".$poll->id.'); return false;" class="icon-16 icon-allow"> </a>';
								
								$html[] = '<b>'.$this->general->buildAdminLink("poll", $poll->id, $poll->title)."</b> - ";
								$html[] = $poll->question;
							$html[] = '</div>';
						$html[] = '</div>';
					}
					break;
				case self::$_Spam_Answer:
					foreach($pars["data"] AS $aid => $reports) {
						$answer = VBAnswer::getInstance()->getAnswer($aid, false); if(!$answer) break;
						$poll = VBVote::getInstance()->getBox($answer->box_id); if(!$poll) break;
						$html[] = '<div class="task_element">';
							//Antwort ausgeben
							$html[] = '<div class="task_head">';
								$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_REMOVE").'" onclick="jVS.submitTask(this, '." '".$group."', 'answer', 'remove', ".$answer->id.'); return false;" class="icon-16 icon-remove"> </a>';
								if($answer->published)
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_PROTECT").'" onclick="jVS.submitTask(this, '." '".$group."', 'answer', 'protect', ".$answer->id.'); return false;" class="icon-16 icon-protect"> </a>';
								else
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_PUBLISH").'" onclick="jVS.submitTask(this, '." '".$group."', 'answer', 'publish', ".$answer->id.'); return false;" class="icon-16 icon-allow"> </a>';
								
								$html[] = '<b>'.$this->general->buildHtmlLink($this->general->buildAdminLink("answer", $answer->id), $this->general->shortText($answer->answer, 120, false, false) ).'</b>';
								$html[] = ' ('.$poll->title.')';
							$html[] = '</div>';
							foreach($reports AS $report) {
								$html[] = '<div class="task_data">';
									$html[] = $this->general->buildHtmlLink($this->general->buildAdminLink("user", $report->user_id), VBUser::getInstance()->getAvatar($report->user_id, 16, true, false) );
									
									$html[] = ($report->msg) ? $report->msg : JText::_("JVS_LOG_NO_MESSAGE");
								$html[] = '</div>';
							}
						$html[] = '</div>';
					}
					break;
				case self::$_Spam_Comment:
					foreach($pars["data"] AS $cid => $reports) {
						$comment = VBComment::getInstance()->getComment($cid); if(!$comment) break;
						$answer = VBAnswer::getInstance()->getAnswer($comment->answer_id, false); if(!$answer) break;
						$poll = VBVote::getInstance()->getBox($answer->box_id); if(!$poll) break;
						$html[] = '<div class="task_element">';
							//Antwort ausgeben
							$html[] = '<div class="task_head">';
								$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_REMOVE").'" onclick="jVS.submitTask(this, '." '".$group."', 'comment', 'remove', ".$comment->id.'); return false;" class="icon-16 icon-remove"> </a>';
								if($comment->published)
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_PROTECT").'" onclick="jVS.submitTask(this, '." '".$group."', 'comment', 'protect', ".$comment->id.'); return false;" class="icon-16 icon-protect"> </a>';
								else
									$html[] = '<a href="#" title="'.JText::_("JVS_TASKS_PUBLISH").'" onclick="jVS.submitTask(this, '." '".$group."', 'comment', 'publish', ".$comment->id.'); return false;" class="icon-16 icon-allow"> </a>';
								
								$html[] = '<b>'.$this->general->buildHtmlLink($this->general->buildAdminLink("comment", $comment->id), $this->general->shortText($comment->comment, 120, false, false) ).'</b>';
								$html[] = ' ('.$this->general->buildHtmlLink($this->general->buildAdminLink("answer", $answer->id), $this->general->shortText($answer->answer, 50, false, false) ).' - '.$poll->title.')';
							$html[] = '</div>';
							foreach($reports AS $report) {
								$html[] = '<div class="task_data">';
									$html[] = $this->general->buildHtmlLink($this->general->buildAdminLink("user", $report->user_id), VBUser::getInstance()->getAvatar($report->user_id, 16, true, false) );
									
									$html[] = ($report->msg) ? $report->msg : JText::_("JVS_LOG_NO_MESSAGE");
								$html[] = '</div>';
							}
						$html[] = '</div>';
					}
					break;
			}
			$html[] = '</div>';
		}
		
		$html[] = '</div>';
		
		return implode("", $html);
	}
	
}//class
