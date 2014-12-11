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

jimport( 'joomla.application.component.helper' );
jimport( 'joomla.mail.mail' );
jimport( 'joomla.utilities.date' );

class VBMailHashTasks {
	const changePublishStateAnswer 	= 0;
	const changePublishStateComment = 1;
	const removeAnswer 				= 2;
	const removeComment 			= 3;
}

class VBMail
{
	//Variablen
	var $db, $user, $document, $jobs;
	
	private function __construct() {
		$this->comment =& VBComment::getInstance();
		$this->answer =& VBAnswer::getInstance();
		$this->document = & JFactory::getDocument();
		$this->db =& JFactory::getDBO();
		$this->user =& VBUser::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->vbparams =& VBParams::getInstance();
		$this->general =& VBGeneral::getInstance();
		$this->tmpl =& VBTemplate::getInstance();
		$this->log =& VBLog::getInstance();
		
		$this->jobs = array();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBMail();
		}
		return $instance;
	}
	
	
	function getJobs() {
		return $this->jobs;
	}
	
	function runJobs($jobs = null) {
		if($jobs == null) $jobs = $this->jobs;
		if(!empty($jobs)) {
			foreach($jobs AS $job) {
				switch($job[0]) {
					case 'addedAnswer':
						$this->addedAnswer($job[1][0], $job[1][1], $job[1][2], $job[1][3]);
						break;
					case 'addedComment':
						$this->addedComment($job[1][0], $job[1][1], $job[1][2], $job[1][3], $job[1][4]);
						break;
					case 'reportedObject':
						$this->reportedObject($job[1][0], $job[1][1], $job[1][2]);
						break;
					case 'bannedObject':
						$this->bannedObject($job[1][0], $job[1][1], $job[1][2]);
						break;
					case 'addedPoll':
						$this->addedPoll($job[1]["cat"], $job[1]["poll"]);
						break;
				}
			}
		}
	}
	
	function addJob($function, $arguments) {
		$this->jobs[] = array($function, $arguments);
	}
	
	private $admins;
	function loadAdmins($poll = null) {
		$key = ($poll == null) ? '-1' : $poll->id;
		if(!isset($this->admins)) $this->admins = array();
		if(!isset($this->admins[$key])) {
			if(version_compare( JVERSION, '1.6.0', 'lt' )) {
				$sql = "SELECT ju.`id` AS jid, ju.`email` AS email, u.`id`"
				. " FROM `#__users` AS ju, `#__jvotesystem_users` AS u"
				. " WHERE u.`jid`=ju.`id` AND ju.`block`=0"
				. " AND ju.`gid`>='".(($poll != null) ? $poll->access->admin_access : 25)."'";
			} else {
				if($poll != null) {
					$sql = 'SELECT ju.`id` AS jid, ju.`email` AS email, u.`id`
						FROM `#__users` AS ju, `#__jvotesystem_users` AS u, `#__user_usergroup_map` AS ugm
						WHERE ju.`id`=ugm.`user_id`
						AND u.`jid`=ju.`id`
						AND (ugm.`group_id` = "'. implode('" OR ugm.`group_id` = "', $poll->access->admin_access). '")';
				} else {
					$sql = 'SELECT `email`
							FROM `#__users`
							WHERE `sendEmail` = 1';
				}
			}
			$this->db->setQuery($sql); 
			$this->admins[$key] = $this->db->loadObjectList();
		}
		return $this->admins[$key];
	}
	
	function addedPoll($cat, $poll) {
		if($cat->mail_admin_new_poll) {
			if($this->access->isUserAllowedToConfig()) return true;
			
			$userData = $this->user->getUserData($this->user->id);
			
			$admins = $this->loadAdmins($poll);
			if(empty($admins)) return;
			
			$app =& JFactory::getApplication();
			$date = JFactory::getDate(null, $app->getCfg('offset'));
				
			$subject = JText::_('JVS_MAIL_POLL_ADDED').': '.$poll->title;
				
			$par = new JObject();
			$par->title 			= $poll->title;
			$par->title_link 		= $this->general->buildLink("poll", $poll->id, "", array(), false);
			$par->user 				= $userData->name;
			$par->user_mail 		= ($this->user->email) ? $this->user->email : $userData->email;
			$par->user_mail_show 	= ($par->user_mail != '');
			$par->user_ip			= $this->user->ip;
			$par->date 				= $date->toFormat('%d.%B %Y - %H:%M');
			$par->question 			= $this->general->BBCode(nl2br($poll->question), ' ');
			$par->answers 			= VBVote::getInstance()->getAnswers($poll->id);
			foreach($par->answers AS &$answer)
				$answer->answer = $this->general->BBCode(nl2br($answer->answer), ' ');
			$par->published			= $poll->published;
			
			//Email erstellen
			$html = $this->tmpl->loadTemplate('mails/addedPoll', $par);
			
			$log_data = array();
			$log_data["subject"] 	= $subject;
			$log_data["tmpl"] 		= 'addedPoll';
			$log_data["users"] 		= array();
			$log_data["hashs"]		= 0;
			$log_data["success"]	= true;
				
			foreach($admins AS $admin) {
				if($admin->email) {
					$content = $html;
					//Moderation erstellen
					if($this->vbparams->get('quickModeration')) {
						$quick_mods = array();
			
						if(!$par->published) {
							$hash = $this->general->generateHash(VBGeneral::$_changePublishStatePoll, $poll->id, $admin->id, array( "state" => 1 ));
							$quick_mods[] = array( "title" => JText::_("Publish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
						} else {
							$hash = $this->general->generateHash(VBGeneral::$_changePublishStatePoll, $poll->id, $admin->id, array( "state" => 0 ));
							$quick_mods[] = array( "title" => JText::_("UNPublish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
						}
						$hash = $this->general->generateHash(VBGeneral::$_removePoll, $poll->id, $admin->id);
						$quick_mods[] = array( "title" => JText::_("Delete"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
			
						$content .= $this->tmpl->loadTemplate('mails/quickModeration', $quick_mods);
						$log_data["hashs"] += 2;
					}
					$success = $this->sendMail($subject, $admin->email, $content, true);
					if(!$success) $log_data["success"] = false;
					$log_data["users"][] = array( "id" => $admin->id, "mail" => $admin->email, "success" => $success, "error" => (!$success) ? $this->error : "" );
				}
			}
				
			$this->log->add( 'MAIL', 'sendedMails', $log_data );
		}
	}

	function addedAnswer($id, $answer, $poll, $user) {	
		if($poll->send_mail_admin_answer) {
			$userData = $this->user->getUserData($user->id);
			$jUser = JFactory::getUser($userData->jid);
			if($this->access->checkAccessGroup('admin_access', $poll, false, $jUser)) return true;
			
			$admins = $this->loadAdmins($poll);
			if(empty($admins)) return;
			
			$app =& JFactory::getApplication();
			$date = JFactory::getDate(null, $app->getCfg('offset'));
			
			$subject = JText::_('ANSWER_ADDED').': '.$poll->title;
			
			$par = new JObject();
			$par->title 			= $poll->title;
			$par->title_link 		= $this->general->buildLink("poll", $poll->id, "", array( "aid" => $id ), false);
			$par->user 				= $userData->name;
			$par->user_mail 		= ($user->email) ? $user->email : $userData->email;
			$par->user_mail_show 	= ($par->user_mail != '');
			$par->user_ip			= $user->ip;
			$par->date 				= $date->toFormat('%d.%B %Y - %H:%M');
			$par->answer 			= $this->general->BBCode(nl2br($answer), ' ');
			$par->published 		= ($this->vbparams->get('autoPublish') == 1);
			
			//Email erstellen
			$html = $this->tmpl->loadTemplate('mails/addedAnswer', $par);
			
			$log_data = array();
			$log_data["subject"] 	= $subject;
			$log_data["tmpl"] 		= 'addedAnswer';
			$log_data["pars"] 		= $par;
			$log_data["users"] 		= array();
			$log_data["hashs"]		= 0;
			$log_data["success"]	= true;
			
			foreach($admins AS $admin) {
				if($admin->email) {
					$content = $html;
					//Moderation erstellen
					if($this->vbparams->get('quickModeration')) {
						$quick_mods = array();
						
						if(!$par->published) {
							$hash = $this->general->generateHash(VBGeneral::$_changePublishStateAnswer, $id, $admin->id, array( "state" => 1 ));
							$quick_mods[] = array( "title" => JText::_("Publish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
						} else {
							$hash = $this->general->generateHash(VBGeneral::$_changePublishStateAnswer, $id, $admin->id, array( "state" => 0 ));
							$quick_mods[] = array( "title" => JText::_("UNPublish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
						}
						$hash = $this->general->generateHash(VBGeneral::$_removeAnswer, $id, $admin->id);
						$quick_mods[] = array( "title" => JText::_("Delete"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
						
						$content .= $this->tmpl->loadTemplate('mails/quickModeration', $quick_mods);
						$log_data["hashs"] += 2;
					}
					$success = $this->sendMail($subject, $admin->email, $content, true);
					if(!$success) $log_data["success"] = false;
					$log_data["users"][] = array( "id" => $admin->id, "mail" => $admin->email, "success" => $success, "error" => (!$success) ? $this->error : "" );
				}
			}
			
			$this->log->add( 'MAIL', 'sendedMails', $log_data );
		}
	}
	
	function addedComment($id, $comment, $answer, $poll, $user) {
		$userData = $this->user->getUserData($user->id);
		$jUser = JFactory::getUser($userData->jid);
		$app =& JFactory::getApplication();
		$date = new JDate();
		$date->setOffset($app->getCfg('offset'));
		
		$duplicateEmail = array();
		if(($poll->send_mail_admin_comment AND !$this->access->checkAccessGroup('admin_access', $poll, false, $jUser) && !empty($admins)) OR ($poll->send_mail_user_answer_comments AND $user->id != $answer->autor_id)) {
			$admins = $this->loadAdmins($poll);
			
			$subject = JText::_('COMMENT_ADDED').': '.$poll->title;
			
			$par					= new JObject();
			$par->title 			= $poll->title;
			$par->title_link 		= $this->general->buildLink("poll", $poll->id, "", array( "aid" => $answer->id, "cid" => $id ), false);
			$par->answer 			= $this->general->BBCode(nl2br($answer->answer), ' ');
			
			//Email erstellen
			$html = $this->tmpl->loadTemplate('mails/addedComment', $par);
			
			if($poll->send_mail_admin_comment AND !$this->access->checkAccessGroup('admin_access', $poll, false, $jUser) && !empty($admins)) {
				$content = $html;
				
				$par 					= new JObject();
				$par->user 				= $userData->name;
				$par->user_mail 		= ($user->email) ? $user->email : $userData->email;
				$par->user_mail_show 	= ($par->user_mail != '');
				$par->user_ip			= $user->ip;
				$par->date 				= $date->toFormat('%d.%B %Y - %H:%M');
				$par->comment			= $this->general->BBCode(nl2br($comment), ' ');
				$par->published 		= ($this->vbparams->get('autoPublishComment') == 1);
				
				$content .= $this->tmpl->loadTemplate('mails/addedCommentAdmin', $par);
				
				$log_data = array();
				$log_data["subject"] 	= $subject;
				$log_data["tmpl"] 		= 'addedCommentAdmin';
				$log_data["users"] 		= array();
				$log_data["hashs"]		= 0;
				$log_data["success"]	= true;
					
				foreach($admins AS $admin) {
					if($admin->email) {
						$text = $content;
						//Moderation erstellen
						if($this->vbparams->get('quickModeration')) {
							$quick_mods = array();
				
							if(!$par->published) {
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateComment, $id, $admin->id, array( "state" => 1 ));
								$quick_mods[] = array( "title" => JText::_("Publish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
							} else {
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateComment, $id, $admin->id, array( "state" => 0 ));
								$quick_mods[] = array( "title" => JText::_("UNPublish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
							}
							$hash = $this->general->generateHash(VBGeneral::$_removeComment, $id, $admin->id);
							$quick_mods[] = array( "title" => JText::_("Delete"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
				
							$text .= $this->tmpl->loadTemplate('mails/quickModeration', $quick_mods);
							$log_data["hashs"] += 2;
						}
						$duplicateEmail[$admin->email] = true;
						$success = $this->sendMail($subject, $admin->email, $content, true);
						if(!$success) $log_data["success"] = false;
						$log_data["users"][] = array( "id" => $admin->id, "mail" => $admin->email, "success" => $success, "error" => (!$success) ? $this->error : "" );
					}
				}
					
				$this->log->add( 'MAIL', 'sendedMails', $log_data );
			}
			
			if($poll->send_mail_user_answer_comments AND $user->id != $answer->autor_id) {
				$content = $html;
				
				$par 					= new JObject();
				$par->user 				= $userData->name;
				$par->date 				= $date->toFormat('%d.%B %Y - %H:%M');
				$par->comment			= $this->general->BBCode(nl2br($comment), ' ');
				
				$content .= $this->tmpl->loadTemplate('mails/addedCommentAutor', $par);
				
				$log_data = array();
				$log_data["subject"] 	= $subject;
				$log_data["tmpl"] 		= 'addedCommentAutor';
				$log_data["users"] 		= array();
				$log_data["hashs"]		= 0;
				$log_data["success"]	= true;
				
				$autorData = VBUser::getUserData($answer->autor_id);
				$autor_mail = ($autorData->email != '') ? $autorData->email : $autorData->jemail;
				
				if($autor_mail != '' && !isset($duplicateEmail[$autor_mail])) {
					$success = $this->sendMail($subject, $autor_mail, $content, true);
					if(!$success) $log_data["success"] = false;
					$log_data["users"][] = array( "id" => $autorData->id, "mail" => $autor_mail, "success" => $success, "error" => (!$success) ? $this->error : "" );
					
					$this->log->add( 'MAIL', 'sendedMails', $log_data );
				}
			}
		}
	}
	
	function reportedObject($what, $row, $poll) {
		if($poll->spam_mail_admin_report) {
			
			$admins = $this->loadAdmins($poll);
			if(empty($admins)) return;
			
			$par = new JObject();
			$par->title 			= $poll->title;
			$par->title_link 		= $this->general->buildLink("poll", $poll->id, "", array(), false);
			$par->group 			= $what;
			
			switch($what) {
				case 'answer':
					$subject = JText::_('ANSWER_REPORTED').': '.$poll->title;
					$par->content = $this->general->BBCode(nl2br($row->answer));
					
					$par->info = JText::_('ANSWER_REPORTED_TIMES');
					break;
				case 'comment':
					$subject = JText::_('COMMENT_REPORTED').': '.$poll->title;
					$par->content = $this->general->BBCode(nl2br($row->comment));
					
					$par->info = JText::_('COMMENT_REPORTED_TIMES');
					break;
			}
			
			$par->info = str_replace('%REPORTS%', $row->reports, $par->info);
			$par->info = str_replace('%SPAMCOUNT%', $poll->spam_count, $par->info);
			
			//Email erstellen
			$html = $this->tmpl->loadTemplate('mails/reportedObject', $par);
				
			$log_data = array();
			$log_data["subject"] 	= $subject;
			$log_data["tmpl"] 		= 'reportedObject';
			$log_data["pars"] 		= $par;
			$log_data["users"] 		= array();
			$log_data["hashs"]		= 0;
			$log_data["success"]	= true;
			$log_data["group"]		= $what;
			
			foreach($admins AS $admin) {
				if($admin->email) {
					$content = $html;
					//Moderation erstellen
					if($this->vbparams->get('quickModeration')) {
						$quick_mods = array();
						
						switch($what) {
							case 'answer':
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateAnswer, $row->id, $admin->id, array( "state" => 1 ));
								$quick_mods[] = array( "title" => JText::_("Protect"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateAnswer, $row->id, $admin->id, array( "state" => 0 ));
								$quick_mods[] = array( "title" => JText::_("UNPublish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								$hash = $this->general->generateHash(VBGeneral::$_removeAnswer, $row->id, $admin->id);
								$quick_mods[] = array( "title" => JText::_("Delete"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								break;
							case 'comment':
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateComment, $row->id, $admin->id, array( "state" => 1 ));
								$quick_mods[] = array( "title" => JText::_("Protect"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateComment, $row->id, $admin->id, array( "state" => 0 ));
								$quick_mods[] = array( "title" => JText::_("UNPublish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								$hash = $this->general->generateHash(VBGeneral::$_removeComment, $row->id, $admin->id);
								$quick_mods[] = array( "title" => JText::_("Delete"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								break;
						}
						
						$content .= $this->tmpl->loadTemplate('mails/quickModeration', $quick_mods);
						$log_data["hashs"] += 3;
					}
					$success = $this->sendMail($subject, $admin->email, $content, true);
					if(!$success) $log_data["success"] = false;
					$log_data["users"][] = array( "id" => $admin->id, "mail" => $admin->email, "success" => $success, "error" => (!$success) ? $this->error : "" );
				}
			}
							
			$this->log->add( 'MAIL', 'sendedMails', $log_data );
		}
	}
	
	function bannedObject($what, $row, $poll) {
		if($poll->spam_mail_admin_ban) {
			
			$admins = $this->loadAdmins($poll);
			if(empty($admins)) return;
				
			$par = new JObject();
			$par->title 			= $poll->title;
			$par->title_link 		= $this->general->buildLink("poll", $poll->id, "", array(), false);
			$par->group 			= $what;
				
			switch($what) {
				case 'answer':
					$subject = JText::_('ANSWER_BLOCKED').': '.$poll->title;
					$par->content = $this->general->BBCode(nl2br($row->answer));
						
					$par->info = JText::_('ANSWER_BLOCKED_TIMES');
					break;
				case 'comment':
					$subject = JText::_('COMMENT_BLOCKED').': '.$poll->title;
					$par->content = $this->general->BBCode(nl2br($row->comment));
						
					$par->info = JText::_('COMMENT_BLOCKED_TIMES');
					break;
			}
				
			$par->info = str_replace('%REPORTS%', $row->reports, $par->info);
				
			//Email erstellen
			$html = $this->tmpl->loadTemplate('mails/bannedObject', $par);
			
			$log_data = array();
			$log_data["subject"] 	= $subject;
			$log_data["tmpl"] 		= 'bannedObject';
			$log_data["pars"] 		= $par;
			$log_data["users"] 		= array();
			$log_data["hashs"]		= 0;
			$log_data["success"]	= true;
			$log_data["group"]		= $what;
				
			foreach($admins AS $admin) {
				if($admin->email) {
					$content = $html;
					//Moderation erstellen
					if($this->vbparams->get('quickModeration')) {
						$quick_mods = array();
			
						switch($what) {
							case 'answer':
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateAnswer, $row->id, $admin->id, array( "state" => 1 ));
								$quick_mods[] = array( "title" => JText::_("Publish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								$hash = $this->general->generateHash(VBGeneral::$_removeAnswer, $row->id, $admin->id);
								$quick_mods[] = array( "title" => JText::_("Delete"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								break;
							case 'comment':
								$hash = $this->general->generateHash(VBGeneral::$_changePublishStateComment, $row->id, $admin->id, array( "state" => 1 ));
								$quick_mods[] = array( "title" => JText::_("Publish"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								$hash = $this->general->generateHash(VBGeneral::$_removeComment, $row->id, $admin->id);
								$quick_mods[] = array( "title" => JText::_("Delete"), "link" => $this->general->buildLink("task", $hash, "", array(), false) );
								break;
						}
			
						$content .= $this->tmpl->loadTemplate('mails/quickModeration', $quick_mods);
						$log_data["hashs"] += 3;
					}
					$success = $this->sendMail($subject, $admin->email, $content, true);
					if(!$success) $log_data["success"] = false;
					$log_data["users"][] = array( "id" => $admin->id, "mail" => $admin->email, "success" => $success, "error" => (!$success) ? $this->error : "" );
				}
			}
				
			$this->log->add( 'MAIL', 'sendedMails', $log_data );
		}
	}
	
	function updateNotification($version, $download, $changelog) {
		$admins = $this->loadAdmins(); 
		if(empty($admins)) return;
		
		$subject = JText::_('JVS_MAIL_UPDATE_NOTIFICATION').': '.$version;
		
		$par = new JObject();
		$par->version 			= $version;
		$par->download 			= $download;
		$par->changelog 		= $changelog;
		$par->website			= str_replace('https://', '', str_replace('http://', '', joomessLibrary::getInstance()->root())) ;
		
		//Email erstellen
		$html = $this->tmpl->loadTemplate('mails/updateNotification', $par);
			
		$log_data = array();
		$log_data["subject"] 	= $subject;
		$log_data["tmpl"] 		= 'updateNotification';
		$log_data["pars"] 		= $par;
		$log_data["users"] 		= array();
		$log_data["success"]	= true;
		
		foreach($admins AS $admin) {
			if($admin->email) {
				$success = $this->sendMail($subject, $admin->email, $html, true); 
				if(!$success) $log_data["success"] = false;
				$log_data["users"][] = array( "id" => 0, "mail" => $admin->email, "success" => $success, "error" => (!$success) ? @$this->error : "" );
			}
		} 
		
		$this->log->add( 'MAIL', 'sendedMails', $log_data, null, true );
	}
	
	function sendMail($betreff, $email, $inhalt, $isHTML) {
		 $mainframe = JFactory::getApplication();
		
		if (!$inhalt || !$betreff || !$email) return false;
		
		if($isHTML) {
			$par = new JObject();
			$par->subject = $betreff;
			$par->content = $inhalt;
			
			$inhalt = $this->tmpl->loadTemplate('mails/html', $par);
		}
		
		$mailer =& JFactory::getMailer();
		$mailer->setSender(array($mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname')));
		$mailer->setSubject($betreff);
		$mailer->setBody($inhalt);
		$mailer->IsHTML($isHTML);
		
		// Add recipients
		$mailer->addRecipient($email);
		
		// Send the Mail
		$rs	= @$mailer->Send();
		
		// Check for an error
		if ( JError::isError($rs) ) {
			$this->error = $rs;
			return false;
		} else {
			return true;
		}
	}
}//class
