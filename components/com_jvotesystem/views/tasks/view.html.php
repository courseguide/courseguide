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
jimport( 'joomla.application.component.view');

class jVoteSystemViewTasks extends JView
{
	var $user;

    function display($tpl = null)
    {
		$this->general =& VBGeneral::getInstance();
		$this->vbparams =& VBParams::getInstance();
		
		$hash = JRequest::getString("hash", null); 
		if($hash == null) { 
			JController::setRedirect($this->general->buildLink("list"), 'NOHASH', "error");
			JController::redirect();
		}
		$task = $this->general->getTask($hash); 
		if($task == null) {
			JController::setRedirect($this->general->buildLink("list"), 'NOTASK', "error");
			JController::redirect();
		}
		if(!$task->active) {
			JController::setRedirect($this->general->buildLink("list"), 'TASKINACTIVE', "error");
			JController::redirect();
		} 
		$dateToday = JFactory::getDate();
		$dateCreated = JFactory::getDate($task->created);
		if($dateToday->toUnix() > ($dateCreated->toUnix() + $this->vbparams->get('validityPeriod')*24*60*60)) {
			JController::setRedirect($this->general->buildLink("list"), 'TASKVALIDITYPERIOD', "error");
			JController::redirect();
		}
		
		$this->vote =& VBVote::getInstance();
		$this->vbanswer =& VBAnswer::getInstance();
		$this->comment =& VBComment::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->access->setUser($task->uid);
				
		switch($task->task) {
			case VBGeneral::$_changePublishStateAnswer:
				//Daten holen
				$answer = $this->vbanswer->getAnswer($task->id);
				$box = $this->vote->getBox(@$answer->box_id);
				
				//Redirect
				$url = $this->general->buildLink('poll', @$box->id, "", array("aid" => @$answer->id) );
				
				//Rechte
				if(!$answer or !$box) {
					JController::setRedirect($this->general->buildLink("list"), JText::_('ERRORNOBOXORANSWERFOUND'), "error");
				} elseif(!$this->access->isUserAllowedToChangePublishState($box)) {
					JController::setRedirect($url, JText::_('ERRORNOTALLOWEDTOCHANGEPUBLISHSTATE'), "error");
				} elseif($this->vbanswer->changePublishStateAnswer($answer->id, $task->state)) {
					JController::setRedirect($url, JText::_('ANSWERPUBLISHSTATECHANGED'));
					
					$this->general->unactivateTask($task->hash);
				}				
				break;
				
			case VBGeneral::$_changePublishStateComment :
				//Daten holen
				$comment = $this->comment->getComment($task->id);
				$answer = $this->vbanswer->getAnswer(@$comment->answer_id);
				$box = $this->vote->getBox(@$answer->box_id);
				
				$url = $this->general->buildLink("poll", @$box->id, "", array("aid" => @$answer->id, "cid" => @$comment->id));
				//Rechte überprüfen
				if(!$comment or !$box) {
					JController::setRedirect($this->general->buildLink("list"), JText::_('ERRORNOBOXORANSWERFOUND'), "error");
				} elseif(!$this->access->isUserAllowedToChangePublishState($box)) {
					JController::setRedirect($url, JText::_('ERRORNOTALLOWEDTOCHANGEPUBLISHSTATE'), "error");
				} elseif($this->comment->changePublishState($comment->id, $task->state)) {
					JController::setRedirect($url, JText::_('COMMENTPUBLISHSTATECHANGED'));
					
					$this->general->unactivateTask($task->hash);
				}
				break;
				
			case VBGeneral::$_changePublishStatePoll :
				$vbcategory =& VBCategory::getInstance();
				//Daten holen
				$box = $this->vote->getBox($task->id);
				$cat = $vbcategory->getCategory(@$box->catid);
				
				$url = $this->general->buildLink("poll", @$box->id);
				//Rechte überprüfen
				if(!$box || !$cat) {
					JController::setRedirect($this->general->buildLink("list"), JText::_('ERRORNOBOXORANSWERFOUND'), "error");
				} elseif(!$this->access->editState($cat, $box)) {
					JController::setRedirect($url, JText::_('ERRORNOTALLOWEDTOCHANGEPUBLISHSTATE'), "error");
				} elseif($this->vote->editPollState($box->id, $task->state)) {
					JController::setRedirect($url, JText::_('JVS_TASKS_PUBLISHSTATECHANGED'));
						
					$this->general->unactivateTask($task->hash);
				}
				break;
				
			case VBGeneral::$_removeAnswer :
				//Daten holen
				$answer = $this->vbanswer->getAnswer($task->id);
				$box = $this->vote->getBox(@$answer->box_id);
				
				$url = $this->general->buildLink("poll", @$box->id);
				//Rechte überprüfen
				if(!$answer or !$box) {
					JController::setRedirect($this->general->buildLink("list"), JText::_('ERRORNOBOXORANSWERFOUND'), "error");
				} elseif(!$this->access->isUserAllowedToMoveAnswerToTrash($answer, $box)) {
					JController::setRedirect($url, JText::_('ERRORNOTALLOWEDTODELETE'), "error");
				} elseif($this->vbanswer->removeAnswer($answer->id)) {
					JController::setRedirect($url, JText::_('ANSWERREMOVED'));
					
					$this->general->unactivateTask($task->hash);
				}
				break;
				
			case VBGeneral::$_removeComment:
				//Daten holen
				$comment = $this->comment->getComment($task->id);
				$answer = $this->vbanswer->getAnswer(@$comment->answer_id);
				$box = $this->vote->getBox(@$answer->box_id);
				
				$url = $this->general->buildLink("poll", $box->id, "", array("aid" => @$answer->id));
				//Rechte überprüfen
				if(!$comment or !$box) {
					JController::setRedirect($this->general->buildLink("list"), JText::_('ERRORNOBOXORANSWERFOUND'), "error");
				} elseif(!$this->access->isUserAllowedToMoveCommentToTrash($comment, $box)) {
					JController::setRedirect($url, JText::_('ERRORNOTALLOWEDTODELETE'), "error");
				} elseif($this->comment->removeComment($comment->id)) {
					JController::setRedirect($url, JText::_('COMMENDREMOVED'));
					
					$this->general->unactivateTask($task->id);
				}
				break;
				
			case VBGeneral::$_removePoll:
				$vbcategory =& VBCategory::getInstance();
				//Daten holen
				$box = $this->vote->getBox($task->id);
				$cat = $vbcategory->getCategory(@$box->catid);
				
				$url = $this->general->buildLink("list", null, "", array("cid" => @$cat->id));
				//Rechte überprüfen
				if(!$box || !$cat) {
					JController::setRedirect($this->general->buildLink("list"), JText::_('ERRORNOBOXORANSWERFOUND'), "error");
				} elseif(!$this->access->remove($cat, $box)) {
					JController::setRedirect($url, JText::_('ERRORNOTALLOWEDTODELETE'), "error");
				} elseif($this->vote->removePoll($box->id)) {
					JController::setRedirect($url, JText::_('JVS_TASKS_POLL_REMOVED'));
				
					$this->general->unactivateTask($task->hash);
				}
				break;
				
			default:
				JController::setRedirect($this->general->buildLink(), 'NOTASK', "error");
				break;
		}
		JController::redirect();
    }//function
}//class
