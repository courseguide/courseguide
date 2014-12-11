<?php 
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @author Johannes MeÃŸmer
 * @copyright (C) 2010- Johannes MeÃŸmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
define ('ABSOLUTE_PATH', dirname(__FILE__) );
define ('RELATIVE_PATH', 'components'.DS.'com_jvotesystem' );
define ('JPATH_BASE', str_replace(RELATIVE_PATH, "", ABSOLUTE_PATH));

require_once ( JPATH_BASE . DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE . DS.'includes'.DS.'framework.php' );

$admin = JRequest::getBool('admin', false);
$interface = $admin ? "administrator" : "site";

$mainframe =& JFactory::getApplication($interface);
$lang =& JFactory::getLanguage();
$lang->setLanguage(JRequest::getString("lang", $lang->getDefault()));
$lang->load();

jimport( 'joomla.error.profiler' );
$profiler = new JProfiler();
$buildTime = $profiler->getmicrotime();

define ('JVS_ROOT', str_replace("/components/com_jvotesystem", "", JUri::root(true)));
require_once(JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'connect.php');

//-- No direct access
defined('_JEXEC') or die('=;)');

class jVoteSystemViewAjax
{
	private $cs;
	
	function __construct() {
		header('Content-Type: text/plain; charset=utf-8');
		ob_start();
		
		$this->jvs =& jVSConnect::getInstance();
		
		if(!$this->jvs->active()) {
			echo '{"error": "jVoteSystem not enabled..", "success": false}';
			exit();
		}
		
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		if($config->get("offline", 0) && !$app->isAdmin() && !VBAccess::getInstance()->isUserAllowedToConfig()) {
			echo '{"error": "Site offline..", "success": false}';
			exit();
		}
	}
	
	function display()
    {
		$this->vbparams =& VBParams::getInstance(JRequest::getWord("paramView", 'pollslist'), true);
		$task = JRequest::getWord("task", null);
		
		$json = false;
		if ($task === "getlang") { 
			ob_end_clean();
		
			$lang = Array();
			$lang['qremoveanswer'] = JText::_('QUESTIONREMOVEANSWER');
			$lang['qreportanswer'] = JText::_('QUESTIONREPORTANSWER');
			$lang['qremovecomment'] = JText::_('QUESTIONREMOVECOMMENT');
			$lang['qreportcomment'] = JText::_('QUESTIONREPORTCOMMENT');
			$lang['newcomment'] = JText::_('WRITE_NEW_COMMENT');
			$lang['newanswertovotebox'] = JText::_('WRITE_NEW_ANSWER_TO_POLL');
			$lang['noemptyordefault'] = JText::_('NO_EMPTY_OR_DEFAULT_VALUE');
			$lang['votessingular'] = JText::_('VOTES_SINGULAR');
			$lang['votesplural'] = JText::_('VOTES');
			$lang['vote'] = JText::_('Vote');
			$lang['load_next'] = sprintf(JText::_("LOAD_NEXT"), "%STEPS%");
			$lang['load_next_short'] = JText::_("LOAD_NEXT_SHORT");
			$lang['hideall'] = JText::_("HIDEALL");
			//JText::_('ERRORCOMMENTNOTEXT');
			$lang['second'] = JText::_('second'); $lang['seconds'] = JText::_('seconds');
			$lang['minute'] = JText::_('minute'); $lang['minutes'] = JText::_('minutes');
			$lang['hour'] = JText::_('hour'); $lang['hours'] = JText::_('hours');
			$lang['day'] = JText::_('day'); $lang['days'] = JText::_('days');
			$lang['week'] = JText::_('week'); $lang['weeks'] = JText::_('weeks');
			$lang['month'] = JText::_('month'); $lang['months'] = JText::_('months');
			$lang['year'] = JText::_('year'); $lang['years'] = JText::_('years');
			$lang['captchaLoading'] = JText::_("CAPTCHA_LOADING");
			$lang['captchaEnterCode'] = JText::_("CAPTCHA_ENTER_CODE");
			$lang['search'] = JText::_("JVS_SEARCH");
			
			//Buttons
			$lang['yes'] = JText::_("JYES");
			$lang['no'] = JText::_("JNO");
			$lang['cancel'] = JText::_("Cancel");
			$lang['send'] = JText::_("Send");
			$lang['ok'] = JText::_("ok");
			
			$lang['reportAddMessage'] = JText::_("reportAddMessage");
			$lang['needToLogin'] = JText::_("ERRORNEEDTOLOGIN");
			$lang['error'] = JText::_('SOMETHING_WENT_WRONG');
			
			$lang['qremovepoll'] = JText::_("QUESTIONREMOVEPOLL");
			$lang['titleQuestion'] = JText::_("ARE_YOU_SURE");
			$lang['commentsofanswer'] = JText::_("COMMENTS_ANSWER");
			
			$lang['relative'] = JText::_("JVS_NAVI_Relative");
			$lang['absolute'] = JText::_("JVS_NAVI_Absolute");
			
			//EasyQuestion
			$lang['result'] = JText::_("JVS_RESULT");

			
			//header('Content-Type: application/json; charset=utf-8');
			header('Content-Type: text/plain; charset=utf-8');
			header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 3600)); //Cache fÃ¼r 1 Stunde
			echo json_encode($lang);
			exit();
		}
		
		//Token überprüfen
		$admin = JRequest::getBool("admin", false);
		$type = JRequest::getString("type", "POST");
		if(!$admin && !JRequest::checkToken($type)) {
			$task = "redirect";
		} elseif ($admin) { 
			$this->access =& VBAccess::getInstance();
			if(!$this->access->isUserAllowedToConfig()) $task = "redirect";
		} else {
			$this->access =& VBAccess::getInstance();
		}
		
		$this->mail =& VBMail::getInstance();
		
		$oAr = array();
		
		//Links von fremden Seiten bzw. durch Google Bots abblocken
		/*$referer = JRequest::getString('HTTP_REFERER', null, 'SERVER');
		
		if(isset($referer) AND $partsRef = parse_url ($referer)) {
			//Hosts mit Server vergleichen
			$current = JUri::current();
			$partsCur = parse_url ($current);
			
			if($partsRef["host"] != $partsCur["host"]) {
				$task = "redirect";
			}
		} TODO*/
		
		//Template setzen
		$tmpl = JRequest::getString("template", 'default');
		
		switch($task) {
			case "redirect":
				$json = true;
				
				$oAr["error"] = JText::_("JLIB_ENVIRONMENT_SESSION_EXPIRED");
				break;
			case 'vote':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$vote =& VBVote::getInstance();
				$vbanswer =& VBAnswer::getInstance();
				
				$answerID = JRequest::getInt('answer', null);
				$boxID = JRequest::getInt('box', null);
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID);
				
				//Captcha
				$oAr = $this->checkCaptcha($box, "vote");		
				
				if($oAr['captcha'] == 0 OR $oAr['erfolg'] == 0) {
					$oAr['poll_link'] = VBGeneral::getInstance()->buildLink('poll', $box->id, "", array( "aid" => $answer->id, "notifi" => "needCaptcha" ), false);
				} else {
					VBGeneral::getInstance()->disableCacheAnswers($boxID);
					
					$oAr = $this->flat($vote->checkVote($boxID, $answerID)); 
					$votes = $vote->getVotesByUser($box->id);
					
					if ($this->access->isUserAllowedToViewUserList($box)) {
						$oAr["make_userlist"] = true;
					}
					
					$oAr["answer"] = $answerID;
					$oAr["box"] = $boxID;
					
					if (@$oAr["leftVotes"] === 0) {
						if ($box->show_thankyou_message) {
							$oAr["thankyou_title"] = JText::_('SUCCESS_THANKS');
							$oAr["thankyou_message"] = JText::_('THANKYOUFORVOTING');
						}
						if ($box->redirect == 'chart' AND $this->vbparams->get('diagramm') AND $this->access->isUserAllowedToViewResult($box, $votes)) {
							$oAr["goto_chart"] = true;
						} elseif($box->redirect == 'page') {
							$oAr["goto_page"] = true;
							$oAr["redirect_page"] = $box->redirectPage;
						} else {
							$oAr["goto_box"] = true;
							$oAr["page"] = 1;
						}
					} else {
						$votes = $vote->getVotesByUser($box->id);
						$oAr["voted_message"] = sprintf(JText::_("JVS_NOTICE_VOTED_MESSAGE"), $votes->allowed_votes." ".(($votes->allowed_votes == 1) ? JText::_("VOTES_SINGULAR") : JText::_("VOTES")));
					}
					
					$oAr["tooltip"] = $vote->getAnswerTooltip($box, $answer);
				}
				
				$oAr["voteLimitMax"] = sprintf(JText::_('VOTELIMITMAX'), $box->max_votes_on_answer);

				break;
			case 'resetvotes':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$vote =& VBVote::getInstance();
				$vbanswer =& VBAnswer::getInstance();
				
				$answerID = JRequest::getInt('answer', null);
				$boxID = JRequest::getInt('box', null);
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID);
				$reset = JRequest::getInt('reset', 1);
				
				if($this->access->isUserAllowedToResetVotes($box, $answer)) {
					if($vote->resetVotes($box, $answer, $reset)) {
						VBGeneral::getInstance()->disableCacheAnswers($boxID);
						$oAr["tooltip"] = $vote->getAnswerTooltip($box, $answer);
						$oAr["erfolg"] = 1;
					}
				} else {
					$oAr["erfolg"] = 0;
				}
				
				$oAr["answer"] = $answerID;
				$oAr["box"] = $boxID;
				
				break;
			case 'answers': 
				$json = true;
				$vote =& VBVote::getInstance();
				$page = JRequest::getInt("page",1);
				$boxID = JRequest::getInt('box', null);
				$template = JRequest::getString('template','default'); 

				$box = $vote->getBox($boxID);
				$search = JRequest::getString('q', '');
				$aid = JRequest::getInt('aid', null);
				
				$oAr['erfolg'] = 1;
				$oAr['currentPage'] = JRequest::getInt("currentPage",1);
				$oAr['answers'] = (int) $box->answers;
				$oAr['anchor'] = JRequest::getString("anchor", 'vb'.$box->id);
				$oAr['code'] = $vote->getVoteBox($box->id, true, $page, false, $template, false, $search);
				$oAr['page'] = $vote->getPage();
				$oAr['count'] = $vote->getCountAnswers();
				$oAr['box'] = (int) $box->id;
				if($aid != null) $oAr["scrollToAnswer"] = $aid;
				if($this->vbparams->get('adsense') && $this->vbparams->get('adsense_key') != "") {
					$vbtemplate =& VBTemplate::getInstance();
					$pars = array("adsense_key" => $this->vbparams->get('adsense_key'), "load" => false);
					$oAr['banner_code'] = $vbtemplate->loadTemplate("banner_code", JArrayHelper::toObject($pars));
				}
				break;
			case 'addAnswer':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$vote =& VBVote::getInstance();
				$vbanswer =& VBAnswer::getInstance();
				$general =& VBGeneral::getInstance(false);
				
				$answer = trim(JRequest::getVar('answer', null));
				$boxID = JRequest::getInt('box', null);
				$box = $vote->getBox($boxID);
				
				if($box) {
					//Doppelte Antworten verhindern
					$check_answers = $vote->getAnswers($box->id, array( "search" => $answer, "search_mode" => 'sensitive' ), 0, 1);
					if(count($check_answers) > 0) {
						$oAr["erfolg"] = 0;
						$oAr["error"] = JText::_('JVS_ERROR_ANSWER_ALREADY_EXISTS');
						
						$oAr["newSearchKey"] = $answer;
					} else {
						//Captcha
						$oAr = $this->checkCaptcha($box, "addAnswer");
							
						$oAr['text'] = $answer;
							
						//Automatisch veröffentlichen
						if($this->access->isUserAllowedToChangePublishState($box) OR $this->access->isAdmin($box)) $autoPublish = 1;
						else $autoPublish = $this->vbparams->get('autoPublish');
							
						if($oAr['captcha'] == 0 OR $oAr['erfolg'] == 0) {
							//captcha notwendig oder Fehler bei Captcha
						} elseif($answer == "") {
							$oAr["erfolg"] = 0;
							$oAr["error"] = JText::_('ERRORANSWERNOTEXT');
						} elseif(!$this->access->isUserAllowedToAddNewAnswer($box)) {
							$oAr["erfolg"] = 0;
							$oAr["error"] = JText::_('ERRORNOTALLOWEDTOADDNEWANSWER');
						} elseif($vbanswer->addAnswer($boxID, $answer, $autoPublish, $general->getColorCode($box->cur_color_index))) {
							$oAr["failed"] = false;
							$oAr["answer"] = $vbanswer->getID();
							//Falls Template-Main AddingAnswer: hier laden
							$vbtemplate =& VBTemplate::getInstance();
							$vbtemplate->setTemplate($tmpl);
							$path = $vbtemplate->getTemplatePath("main").DS."addingAnswer.php";
							if(JFile::exists($path)) @require $path;
						
							if(!$oAr["failed"]) {
								VBGeneral::getInstance()->clearCacheAnswers($boxID);
								//Mail als Job eintragen in VBMail
								$this->mail->addJob('addedAnswer', array($vbanswer->getID(), $answer, $box, $this->user));
								
								//Tasks
								if(!$autoPublish) 
									VBTasks::getInstance()->addTask(VBTasks::$_Answer, $vbanswer->getID());
						
								$votes = $vote->getVotesByUser($boxID);
									
								$oAr["erfolg"] = 1;
								//$oAr["page"] = $vbanswer->getAnswersPageCount($box, $vbanswer->getID());
								if($autoPublish == 1) {
									$oAr["success"] = JText::_('ANSWERADDED');
								} else $oAr["success"] = JText::_('ANSWERADDEDNOTPUBLISHED');
								$oAr["leftVotes"] = $votes->allowed_votes;
								$oAr["newPage"] = $vbanswer->getAnswersPageCount($box, $vbanswer->getID(), '');
									
								//Colorcode hochzählen
								$general->updateColorIndex($box->id, $box->cur_color_index + 1);
							} else {
								$oAr['erfolg'] = 0;
								$vbanswer->removeAnswer($oAr["answer"]);
							}
						} else {
							$oAr["erfolg"] = 0;
							$oAr["error"] = JText::_('ERRORADDANSWER');
						}
					}
				}
				$oAr["box"] = $boxID;
				
				break;
			case 'addComment':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$vbanswer =& VBAnswer::getInstance();
				$vbcomment =& VBComment::getInstance();
				$vote =& VBVote::getInstance();
				
				$answerID = JRequest::getInt('answer', null);
				$boxID = JRequest::getInt('box', null);
				$comment = JRequest::getVar('comment', '');
				
				$answerID = 	JRequest::getInt('answer',0);
				$boxID = 		JRequest::getInt('box',0);
				$comment = 		JRequest::getVar('comment');
				$template = 	JRequest::getString('template','default');
				
				if (!$answerID || !$boxID) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('JVS_DEBUG_ADDCOMMENT_MISSING_ID');
					break;
				}
				
				//Daten holen
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID, (!$this->access->isUserAllowedToChangePublishState($box)));
				if($this->vbparams->get('orderComment') == 'ASC') $page = $vbcomment->getCommentsPageCount($box, $answer->comments+1);
				else $page = 1;
				
				//Captcha
				$oAr = $this->checkCaptcha($box, "addComment");
				$oAr["comment"] = $comment;
				
				
				//Automatisch veröffentlichen
				if($this->access->isUserAllowedToChangePublishState($box) OR $this->access->isAdmin($box)) $autoPublish = 1;
				else $autoPublish = $this->vbparams->get('autoPublishComment');
				
				if($oAr['captcha'] == 0 OR $oAr['erfolg'] == 0) {
					//captcha notwendig oder Fehler bei Captcha
				} elseif(!$this->access->isUserAllowedToAddNewComment($box, $answer)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOADDNEWCOMMENT');
				} elseif($vbcomment->addComment($answer->id, $comment, $autoPublish)) {
					//Mail als Job eintragen in VBMail
					$this->mail->addJob('addedComment', array($vbcomment->getID(), $comment, $answer, $box, $this->user));
					
					//Tasks
					if(!$autoPublish)
						VBTasks::getInstance()->addTask(VBTasks::$_Comment, $vbcomment->getID());
					
					$oAr["erfolg"] = 1;

					$oAr["page"] = $page;
					if($autoPublish == 1) $oAr["success"] = JText::_('COMMENTADDED');
					else $oAr["success"] = JText::_('COMMENTADDEDNOTPUBLISHED');
				} else {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORADDCOMMENT');
				}
				
				$oAr["box"] =$box->id;
				$oAr["answer"] = $answer->id;
				break;
			case 'removeAnswer':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$vbanswer =& VBAnswer::getInstance();
				$vote =& VBVote::getInstance();
				
				$answerID = JRequest::getInt('answer', null);
				$boxID = JRequest::getInt('box', null);
				//Daten holen
				$vbtemplate =& VBTemplate::getInstance();
				$vbtemplate->setTemplate($tmpl);
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID);
				//Rechte Ã¼berprÃ¼fen
				if(!$answer or !$box) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORANSWERFOUND');
				} elseif(!$this->access->isUserAllowedToMoveAnswerToTrash($answer, $box)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTODELETE');
				} elseif($vbanswer->removeAnswer($answerID)) {
					VBGeneral::getInstance()->clearCacheAnswers($boxID);
					
					unset($vote->votesByUser);
					$votes = $vote->getVotesByUser($boxID);
					
					$oAr["erfolg"] = 1;
					$oAr["success"] = JText::_('ANSWERREMOVED');
					$oAr["leftVotes"] = $votes->allowed_votes;
				} else {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORREMOVEANSWER');
				}
				$oAr["box"] = $boxID;
				$oAr["answer"] = $answerID;
				break;
			case 'reportAnswer':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$vbanswer =& VBAnswer::getInstance();
				$spam =& VBSpam::getInstance();
				$spam->loadData('answer');
				$vote =& VBVote::getInstance();
				
				$answerID = JRequest::getInt('answer', null);
				$boxID = JRequest::getInt('box', null);
				//Daten holen
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID);
				$msg = JRequest::getString("reportMessage", null);
				//Rechte Ã¼berprÃ¼fen
				if(!$answer or !$box) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORANSWERFOUND');
				} elseif(!$this->access->isUserAllowedToReportAnswer($box, $answer)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOREPORT');
				} elseif($spam->report('answer', $answerID, $msg)) {
					$oAr["erfolg"] = 1;
					$oAr["success"] = JText::_('ANSWERREPORTED');
					//Kommentar Ã¼berprÃ¼fen, wenn Limit Ã¼berschritten.. speeren
					$spam->checkReports('answer', $answerID, $box);
				} else {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORREPORTANSWER');		
				}
				$oAr["box"] = $boxID;
				$oAr["answer"] = $answerID;
				break;
			case 'reportComment':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$spam =& VBSpam::getInstance();
				$spam->loadData('comment');
				$vote =& VBVote::getInstance();
				$vbcomment =& VBComment::getInstance();
				
				$commentID = JRequest::getInt('comment', null);
				$boxID = JRequest::getInt('box', null);
				$msg = JRequest::getString("reportMessage", null);
				//Daten holen
				$box = $vote->getBox($boxID);
				$comment = $vbcomment->getComment($commentID); 
				//Rechte Ã¼berprÃ¼fen
				if(!$comment or !$box) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORCOMMENTFOUND');
				} elseif(!$this->access->isUserAllowedToReportComment($box, $comment)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOREPORT');
				} elseif($spam->report('comment', $commentID, $msg)) {
					$oAr["erfolg"] = 1;
					$oAr["success"] = JText::_('COMMENTREPORTED');
					//Kommentar Ã¼berprÃ¼fen, wenn Limit Ã¼berschritten.. speeren
					$spam->checkReports('comment', $commentID, $box);
				} else {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORREPORTCOMMENT');
				}
				$oAr["box"] = $boxID;
				$oAr["comment"] = $commentID;
				break;
			case 'removeComment':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$this->comment =& VBComment::getInstance();
				$vote =& VBVote::getInstance();
				
				$commentID = JRequest::getInt('comment', null);
				$boxID = JRequest::getInt('box', null);
				//Daten holen
				$box = $vote->getBox($boxID);
				$comment = $this->comment->getComment($commentID);
				//Rechte Ã¼berprÃ¼fen
				if(!$comment or !$box) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORANSWERFOUND');
				} elseif(!$this->access->isUserAllowedToMoveCommentToTrash($comment, $box)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTODELETE');
				} elseif($this->comment->removeComment($commentID)) {
					$oAr["erfolg"] = 1;
					$oAr["success"] = JText::_('COMMENDREMOVED');
				} else {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORREMOVECOMMEND');
				}
				$oAr["box"] =$boxID;
				$oAr["answer"] = $comment->answer_id;
				$oAr["comment"] = $comment->id;
				break;
			case 'changePublishStateAnswer':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$vbanswer =& VBAnswer::getInstance();
				$vote =& VBVote::getInstance();
				
				$answerID = JRequest::getInt('answer', null);
				$boxID = JRequest::getInt('box', null);
				//Daten holen
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID);
				//Status
				if($answer->published == 1) { 
					$state = 'unpublished';
					$answer->published = 0;
				} else {
					$state = 'published';
					$answer->published = 1;
				}
				//Rechte Ã¼berprÃ¼fen
				if(!$answer or !$box) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORANSWERFOUND');
				} elseif(!$this->access->isUserAllowedToChangePublishState($box)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOCHANGEPUBLISHSTATE');
				} elseif($vbanswer->changePublishStateAnswer($answerID, $answer->published)) {
					VBGeneral::getInstance()->clearCacheAnswers($boxID);
					
					$oAr["erfolg"] = 1;
					$oAr["success"] = JText::_('ANSWERPUBLISHSTATECHANGED');
					$oAr["state"] = $state;
					$oAr["rank"] = $vbanswer->getRankOfAnswer($box->id, $answerID);
					$oAr["votingAllowed"] = $this->access->isUserAllowedToVoteAnswer($box, $answer);
					$oAr["tooltip"] = $vote->getAnswerTooltip($box, $answer);
				}
				$oAr["box"] = $boxID;
				$oAr["answer"] = $answerID;
				break;
			case 'changePublishStateComment':
				$json = true;
				$this->user =& VBUser::getInstance(true);
				$this->comment =& VBComment::getInstance();
				$vote =& VBVote::getInstance();
				
				$commentID = JRequest::getInt('comment', null);
				$boxID = JRequest::getInt('box', null);
				//Daten holen
				$box = $vote->getBox($boxID);
				$comment = $this->comment->getComment($commentID);
				//Status
				if($comment->published == 1) { 
					$state = 'unpublished';
					$comment->published = 0;
				} else {
					$state = 'published';
					$comment->published = 1;
				}
				//Rechte Ã¼berprÃ¼fen
				if(!$comment or !$box) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORANSWERFOUND');
				} elseif(!$this->access->isUserAllowedToChangePublishState($box)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOCHANGEPUBLISHSTATE');
				} elseif($this->comment->changePublishState($comment->id, $comment->published)) {
					$oAr["erfolg"] = 1;
					$oAr["success"] = JText::_('COMMENTPUBLISHSTATECHANGED');
				}
				$oAr["box"] = $boxID;
				$oAr["comment"] = $commentID;
				break;
			case 'loadComments':
				$json = true;
				$user =& VBUser::getInstance();
				$this->comment =& VBComment::getInstance();
				$this->template =& VBTemplate::getInstance();
				$this->general =& VBGeneral::getInstance();
				$vote =& VBVote::getInstance();
				$vbanswer =& VBAnswer::getInstance();
				
				$answerID = 	JRequest::getInt('answer',0);
				$boxID = 		JRequest::getInt('box',0);
				$page = 		JRequest::getInt('page',1);
				$currentpage = 	JRequest::getInt('currentpage',0);
				$template = 	JRequest::getString('template','default');
				
				if (!$answerID || !$boxID) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('JVS_DEBUG_LOADCOMMENTS_MISSING_ID');
					break;
				}
				
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID);
				
				$this->template->setTemplate($template);

				$oAr["erfolg"] = 1;
				$oAr["code"] = $this->comment->getComments($box, $answer, $page, $currentpage === 0);
				$oAr["firstLoad"] = ($currentpage === 0);
				break;
			case 'loadCharts':
				$json = true;
				$this->charts =& VBCharts::getInstance();
				$vote =& VBVote::getInstance();
				$this->template =& VBTemplate::getInstance();
				
				$boxID = JRequest::getInt('box', null);
				$mode = JRequest::getVar('mode', null);
				
				//Daten holen
				$box = $vote->getBox($boxID);
				
				//Template setzen
				$template = JRequest::getString('template', null);
				if($template == null) $template = $box->template;
				$this->template->setTemplate($template);
				
				if($box) {
					$votes = $vote->getVotesByUser($boxID);
					if($this->access->isUserAllowedToViewResult($box, $votes)) {
						$oAr += $this->charts->getFrontendChartJSON($boxID);
						$oAr["allowed"] = 1;
					} else {
						$oAr["allowed"] = 0;
					}
				} else {
					$oAr["allowed"] = 0;
				}
				if ($mode) $oAr["mode"] = $mode;
				$oAr["erfolg"] = 1;
				
				//Navi
				$par = new JObject();
				$par->translation_scaling = JText::_("Scaling");
				$par->translation_next = sprintf(JText::_("LOAD_NEXT"), $this->vbparams->get("chart_barscount"));
				$par->show_next = ($box->answers > $this->vbparams->get("chart_barscount"));
				
				$oAr["code"] = $this->template->loadTemplate("chartnavi", $par);
				$oAr["count"] = $this->vbparams->get("chart_barscount");
				
				$oAr["onload"] = JRequest::getInt('onload', 0);
				$oAr["base"] = JURI::base( true );
				$oAr["box"] = $boxID;
				break;
			case 'loadUserList':
				$json = true;
				$user =& VBUser::getInstance(true);
				$vbanswer =& VBAnswer::getInstance();
				$vote =& VBVote::getInstance();
				
				$answerID = JRequest::getInt('answer', null);
				$boxID = JRequest::getInt('box', null);
				//Daten holen
				$box = $vote->getBox($boxID);
				$answer = $vbanswer->getAnswer($answerID);
				
				//Rechte Ã¼berprÃ¼fen
				if(!$answer or !$box) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORANSWERFOUND');
				} elseif(!$this->access->isUserAllowedToViewUserList($box)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOVIEWUSERLIST');
				} else {
					$oAr["erfolg"] = 1;
					$oAr["code"] = $user->loadUserListVotedOnAnswer($answer);
				}
				$oAr["title"] = JText::_('USERS_VOTED_FOR_ANSWER');
				$oAr["box"] = $boxID;
				$oAr["answer"] = $answerID;
				break;
			case 'loadUserTooltip':
				$json = true;
				
				$oAr["erfolg"] = 1;
				$uid = JRequest::getInt("uid", null);
				
				$cache_id = md5(serialize(array('user', $uid, $this->access->isUserAllowedToConfig())));
				$cache = & JCache::getInstance();
				$cache->setLifeTime(30);
				$cache->setCaching(true);
				
				if(!($oAr["html"] = $cache->get($cache_id, 'jVoteSystem - Tooltips'))) {
					$user =& VBUser::getInstance();
					$general =& VBGeneral::getInstance();
					$curuser = $user->getUserData($uid);
					//Stats
					$stats = $user->getUserStats($curuser->id);
					
					//Html-Code erzeugen
					$vbtmpl =& VBTemplate::getInstance();
					$vbtmpl->setTemplate('default');
					
					$par = new JObject();
					$par->avatar = $user->getAvatar($curuser->id, 50, false);
					$par->link = $general->buildLink("user", $curuser->id);
					$par->do_link = ($par->link != '#');
					$par->name = $curuser->name;
					$par->stats = $stats;
					
					$par->show_info = ($this->access->isUserAllowedToConfig() && $curuser->id > 0);
					if($par->show_info) {
						VBLoader::getInstance()->loadLanguageFiles();
							
						$par->ip = @$curuser->ip;
						$par->id = $curuser->id;
						$par->jid = $curuser->jid;
						$par->first_visit = $general->convertTime($curuser->registered_time);
						$par->last_visit = $general->convertTime($curuser->lastVisitDate);
					}
					
					$oAr["html"] = $vbtmpl->loadTemplate('tooltips/user', $par);
					
					$cache->store($oAr["html"], $cache_id, 'jVoteSystem - Tooltips');
				}				
				break;
			case 'loadCategoryTooltip':
				$json = true;
				
				$category =& VBCategory::getInstance();
				$general =& VBGeneral::getInstance();
					
				$cid = JRequest::getInt("cid", null);
				
				$cache_id = md5(serialize(array('category', $cid)));
				$cache = & JCache::getInstance();
				$cache->setLifeTime(30);
				$cache->setCaching(true);
				
				if(!($oAr["html"] = $cache->get($cache_id, 'jVoteSystem - Tooltips'))) {
					$cat = $category->getCategory($cid, true);
					
					//Html-Code erzeugen
					$vbtmpl =& VBTemplate::getInstance();
					$vbtmpl->setTemplate('default');
					
					$par = new JObject();
					$par->link = $general->buildLink("category", $cat->id);
					$par->name = $cat->title;
					$par->stats = new JObject();
					$par->stats->polls = $cat->polls;
					$par->stats->votes = $cat->votes;
					$par->stats->comments = $cat->comments;
					
					$oAr["html"] = $vbtmpl->loadTemplate('tooltips/category', $par);
					
					$cache->store($oAr["html"], $cache_id, 'jVoteSystem - Tooltips');
				}
			
				$oAr["erfolg"] = 1;
			
				break;
			case "removePoll":
				$json = true;
				$vote =& VBVote::getInstance();
				$general =& VBGeneral::getInstance();
				$category =& VBCategory::getInstance();
				$template =& VBTemplate::getInstance();
				
				$boxID = JRequest::getInt("box");
				$tmpl = JRequest::getString('template','default');
				
				$box = $vote->getBox($boxID);
				$cat = $category->getCategory(@$box->catid);
				$template->setTemplate($tmpl);
				
				if(!$box || $cat->id == 0) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORCATEGORYFOUND');
				} elseif(!$this->access->remove($cat, $box)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOREMOVEPOLL');
				} else {
					if($vote->removePoll($box->id)) {
						$pars = array("type" => "success", "msg" => sprintf(JText::_("POLL_SUCCESSFULLY_REMOVED"), $general->buildLink("category", $box->catid)));
						$oAr["code"] = $template->loadTemplate("notification", JArrayHelper::toObject($pars));
						$oAr["erfolg"] = 1;
					} else {
						$oAr["erfolg"] = 0;
						$oAr["error"] = JText::_('ERRORREMOVINGPOLL');
					}
				}
				
				break;
			case "editPollState":
				$json = true;
				$vote =& VBVote::getInstance();
				$category =& VBCategory::getInstance();
				
				$pollID = JRequest::getInt("poll");
				$state = JRequest::getBool("state", null);
				
				$poll = $vote->getBox($pollID);
				$cat = $category->getCategory(@$poll->catid);
				
				if(!$poll || $cat->id == 0) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOBOXORCATEGORYFOUND');
				} elseif(!$this->access->editState($cat, $poll)) {
					$oAr["erfolg"] = 0;
					$oAr["error"] = JText::_('ERRORNOTALLOWEDTOEDITPOLLSTATE');
				} else {
					if($vote->editPollState($poll->id, $state)) {
						$oAr["erfolg"] = 1;
					} else {
						$oAr["erfolg"] = 0;
						$oAr["error"] = JText::_('ERRORWHILEEDITSTATEPOLL');
					}
				}
				break;
			/*case "loginFBUser":
				$this->user =& VBUser::getInstance(true);
				
				$mode =& VBMode::getInstance("FacebookVoting");
				$mode->login();
				
				if($url = $mode->check()) { 
					$app =& JFactory::getApplication();
					$app->redirect($url);
				} else {
					ob_end_clean();
					?>
					<html>
						<head>
						   <title>Close</title>
							<script type="text/javascript">opener.jVS.validateFB(); self.close();</script>
						</head>
						<body> </body>
					</html>
					<?php 
					exit();
				}
				break;*/
			case "testMaxExecutionTime":
				//Max execution time
				if( !@ini_get('safe_mode') ) @set_time_limit(60);
				
				$json = true;
				if($this->access->isUserAllowedToConfig()) {
					$exec_time = JRequest::getInt("stime", 5);
					sleep($exec_time);
					$oAr["success"] = true;
					
					//Success => save execution time in the config
					if(!$this->vbparams->setConfig("maxExecutionTime", $exec_time - 4)) $oAr["success"] = false;
					
				} else {
					$oAr["error"] = "noRights";
					$oAr["success"] = false;						
				}
				break;
				
			case "checkForNewLogs":
				//Max execution time
				if( !@ini_get('safe_mode') ) @set_time_limit(60);
				
				$json = true;
				if($this->access->isUserAllowedToConfig()) {
					$log =& VBLog::getInstance();
					$general =& VBGeneral::getInstance();
					$lastid = JRequest::getString("lastID", 0);
					$type = JRequest::getString("type", "default");
					for($i = 0; $i < $this->vbparams->get("maxExecutionTime"); $i++) {
						sleep(1);
						if($cur = $log->checkForNewEntries($lastid)) {
							//Language files
							$this->jvs->get("Loader")->loadLanguageFiles();
							
							$oAr["newEntries"] = true;
							$oAr["logs"] = array();
							$logs = array();
							$log->getDBEntries($logs, $cur);
							
							foreach($logs AS $row) {
								$row = $log->convertMsg($row);
								
								$row->parsTree = $general->dumpTree($row->pars);
								
								//Puffer leeren und vorbereiten..
								$old = ob_get_contents();
								ob_clean();
								
								switch($type) {
									case "small":
									?>
									
									<tr>
										<td class="icon-16 icon-<?php echo strtolower($row->type);?>"></td>
										<td class="icon-16 icon-<?php echo strtolower($row->action);?>"></td>
										<td><?php echo $row->msg;?></td>
										<td style="text-align:center;"><?php echo $general->convertTime($row->created);?></td>
									</tr>
									
									<?php 
									break;
									default:
									?>
									
									<tr>
										<td class="icon-16 icon-<?php echo strtolower($row->type);?>"></td>
										<td class="icon-16 icon-<?php echo strtolower($row->action);?>"></td>
										<td style="text-align:center;"><?php echo $general->convertUser($row->vsid);?></td>
										<td><?php echo $row->msg;?></td>
										<td style="text-align:center;"><span data-jvs_tooltip="<?php echo urlencode($row->parsTree);?>"><?php echo sprintf(JText::_("JVS_LOG_COUNTPARAMS"), sizeof(JArrayHelper::fromObject($row->pars)));?></span></td>
										<td style="text-align:center;"><?php echo $general->convertTime($row->created);?></td>
									</tr>
									
									<?php 
									break;
								}
		
								//Puffer zurückgeben
								$out = ob_get_contents();
								ob_clean();
								echo $old;
								
								$oAr["logs"][] = str_replace("\n", "", str_replace( "\t", "", $out) );
							} 
							$oAr["logs"] = array_reverse($oAr["logs"]);
							break;
						} else $oAr["newEntries"] = false;
						$lastid = $log->lastID();
					}
					$oAr["last"] = $log->lastID();
					$oAr["success"] = true;
					
				} else {
					$oAr["error"] = "noRights";
					$oAr["success"] = false;
				}	
				break;
				
			case "submitTask":
				$json = true;
				
				$this->user =& VBUser::getInstance(true);
				$vbanswer =& VBAnswer::getInstance();
				$vbcomment =& VBComment::getInstance();
				$vote =& VBVote::getInstance();
				
				$group 		= JRequest::getString('group', null);
				$action 	= JRequest::getString('action', null);
				$task_group = JRequest::getString('task_group', null);
				$id 		= JRequest::getInt('id', null);
				
				if(!$group || !$id || !$action || !$task_group) {
					$oAr["error"] = "MissingParameters";
				} else {
					switch($group) {
						case 'answer':
							$answer = $vbanswer->getAnswer($id);
							$poll = $vote->getBox(@$answer->box_id);
					
							if(!$answer || !$poll) {
								$oAr["error"] = "ObjectNotFound";
							} else {
								switch($action) {
									case 'publish':
									case 'protect':
										if($this->access->isUserAllowedToChangePublishState($poll)) {
											if(!$vbanswer->changePublishStateAnswer($answer->id, 1))
												$oAr["error"] = "FailedToChangePublishState";
										} else $oAr["error"] = "NoRights";
										break;
									case 'remove':
										if($this->access->isUserAllowedToMoveAnswerToTrash($answer, $poll)) {
											if(!$vbanswer->removeAnswer($answer->id))
												$oAr["error"] = "FailedToRemoveAnswer";
										} else $oAr["error"] = "NoRights";
										break;
									default: $oAr["error"] = "WrongAction"; break;
								}
							}
							break;
						case 'comment':
							$comment = $vbcomment->getComment($id);
							$answer = $vbanswer->getAnswer(@$comment->answer_id);
							$poll = $vote->getBox(@$answer->box_id);
					
							if(!$answer || !$comment || !$poll) {
								$oAr["error"] = "ObjectNotFound";
							} else {
								switch($action) {
									case 'publish':
									case 'protect':
										if($this->access->isUserAllowedToChangePublishState($poll)) {
											if(!$vbcomment->changePublishState($comment->id, 1))
												$oAr["error"] = "FailedToChangePublishState";
										} else $oAr["error"] = "NoRights";
										break;
									case 'remove':
										if($this->access->isUserAllowedToMoveCommentToTrash($comment, $poll)) {
											if(!$vbcomment->removeComment($comment->id))
												$oAr["error"] = "FailedToRemoveComment";
										} else $oAr["error"] = "NoRights";
										break;
									default: $oAr["error"] = "WrongAction"; break;
								}
							}
							break;
							
						case 'poll':
							$poll = $vote->getBox($id);
							$cat = VBCategory::getInstance()->getCategory(@$poll->catid);
							
							if(!$poll || $cat->id == 0) {
								$oAr["error"] = "ObjectNotFound";
							} else {
								switch($action) {
									case 'publish':
									//case 'protect':
										if($this->access->editState($cat, $poll)) {
											if(!$vote->editPollState($poll->id, 1))
												$oAr["error"] = "FailedToChangePublishState";
										} else $oAr["error"] = "NoRights";
										break;
									case 'remove':
										if($this->access->remove($cat, $poll)) {
											if(!$vote->removePoll($poll->id))
												$oAr["error"] = "FailedToRemovePoll";
										} else $oAr["error"] = "NoRights";
										break;
									default: $oAr["error"] = "WrongAction"; break;
								}
							}
							
							break;
							
						default: $oAr["error"] = "WrongGroup"; break;
					}
				}
				
				$oAr["success"] = !isset($oAr["error"]);
				
				break;
				
			default:
				$json = true;
				
				$oAr["success"] = 0;
				$oAr["error"] = JText::_("WRONG_TASK");				
				break;
		}
		
		$this->output($oAr,$json);
    }//function
	
	function output($ar,$json) {
		//EMails nach Beenden des Skripts senden.. *nÃ¤chste Version
		$this->mail->runJobs($this->mail->getJobs());
 
		global $profiler, $buildTime;
		$ar["time"] = round(($profiler->getmicrotime() - $buildTime)*1000);
		$app =& JFactory::getApplication();
		$ar["cur_time"] = JFactory::getDate(null, $app->getCfg('offset'))->toUnix() + (int)($ar["time"]/1000);
		
		//foreach($ar AS &$a) if(is_string($a)) $a = str_replace(JUri::root(true), JVS_ROOT, $a);
		
		//ÃœberprÃ¼fen ob Fehler ausgegeben wurden
		$error = ob_get_contents();
		$log =& VBLog::getInstance();
		if($error) {
			$log->add("ERROR", 'AjaxScriptError', array_merge($ar, array("php_error"=>$error)));
			
			if($this->vbparams->get("showErrors")) {
				$json ? $ar["erfolg"] = 0 : $ar[] = array("key"=>"erfolg","value"=>0);
				$json ? $ar["error"] = $error : $ar[] = array("key"=>"error","value"=>"Error: ".$error);
			}
		}
		ob_end_clean();
		
		$log->save();
		
		global $mainframe;

		if (!$json) {
			$out = '';
			foreach($ar AS $a) {
				if($out != '') $out .= '&';
				if($a['key'] == "error" or $a['key'] == "success") $a['value'] = urlencode($a['value']);
				$out .= $a['key'].'='.$a['value'];
			}
			echo $out;
		} else {
			//header('Content-Type: application/json; charset=utf-8');
			echo json_encode($ar);
		}

		exit();
	} 
	
	function checkCaptcha($box, $task) {
		if(!$this->access->needCaptcha($box, $task)) {
			$oAr['erfolg'] = 1;
			$oAr['captcha'] = 1;
			return $oAr;
		}
		$oAr = array();
		if($this->vbparams->get('recaptcha') and $this->vbparams->get('recaptcha_publickey') and $this->vbparams->get('recaptcha_privatekey')) {
			$captcha = JRequest::getVar('captcha', null);
			require_once(JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'classes'.DS.'recaptchalib.php');
			if($captcha == null) {
				//Captcha notwendig
				$oAr['captcha'] = 0;
				$u =& JURI::getInstance();
			} else {
				//Captcha Ã¼berprÃ¼fen
				$privatekey = $this->vbparams->get('recaptcha_privatekey');
				$resp = recaptcha_check_answer ($privatekey,
												$_SERVER["REMOTE_ADDR"],
												JRequest::getVar("recaptcha_challenge_field"),
												$captcha);
				if (!$resp->is_valid) {
					$oAr['erfolg'] = 0;
					$oAr['captcha'] = 0;//sonst error...
					$oAr["error"] = JText::_('WRONGCAPTCHA');
				} else {
					$oAr['erfolg'] = 1;
					$oAr['captcha'] = 1;
				}
			}
		} else {
			$oAr['erfolg'] = 1;
			$oAr['captcha'] = 1;
		}
		return $oAr;
	}
	
	//converter function
	function flat($ar) {
		$oo = array();
		foreach($ar AS $a) {
			$oo[$a['key']]=$a['value'];
		}
		return $oo;
	}
}//class

$load = new jVoteSystemViewAjax();
$load->display();

echo '{"success": false}';
exit();
