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

class VBComment
{
	//Variablen
	private $db, $user, $document, $loaded, $commentData, $id, $access;
	
	private function __construct() {
		$this->document = & JFactory::getDocument();
		$this->db =& JFactory::getDBO();
		$this->user =& VBUser::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->general =& VBGeneral::getInstance();
		$this->template =& VBTemplate::getInstance();
		$this->spam =& VBSpam::getInstance();
		$this->vbparams =& VBParams::getInstance();
		$this->log =& VBLog::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBComment();
		}
		return $instance;
	}
	
	function getID() {
		return $this->id;
	}
	
	function getCommentIcon($box, $answer) {
		
		//Parameter festlegen
		$par = new JObject();
			$par->bid = $box->id;
			$par->aid = $answer->id;
			//Link
			$par->icon_link = $this->general->buildLink("poll", $box->id, "", array("aid"=>$answer->id))."#vb".$box->id."answer".$answer->id;
			//Comments
			$par->count = $this->getCommentCount($answer, $box);
		
		//Wenn nicht erlaubt bzw. und keine Kommentare.. nicht anzeigen!
		if($this->access->isUserAllowedToAddNewComment($box, $answer, true) == false and !$par->count) return '';
		
		//Template-Datei laden
		$out = $this->template->loadTemplate("commenticon", $par);
		return $out;
	}
	
	function getCommentBox($comment,$box) {
		$user = $this->user->getUserData($comment->autor_id);
		
		//Parameter festlegen
		$par = new JObject();
			$par->bid = $box->id;
			$par->cid = $comment->id;
			$par->avatar = $this->user->getAvatar($user->id, 40);
			$par->user = $this->general->convertUser($user->id);
			$par->creation_date = $this->general->convertTime($comment->created);
			$par->Qtrash = $this->access->isUserAllowedToMoveCommentToTrash($comment,$box);
			$par->state = $comment->published == 1 ? 'published' : 'unpublished';
			$par->Qpublish = $this->access->isUserAllowedToChangePublishState($box);
			$par->Qreport = $this->access->isUserAllowedToReportComment($box, $comment);
			$par->commenttext = nl2br($this->general->shortText($comment->comment, $this->vbparams->get('shortCountComment')));

		$out = $this->template->loadTemplate("comment", $par);
		return $out;
	}
	
	function changePublishState($commentID, $state) {
	
		$upd->id = $commentID;
		$upd->published = $state;
		$upd->no_spam_admin = 1;
		
		$this->db->updateObject('#__jvotesystem_comments', $upd, 'id');
		
		if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'ChangingPublishStateComment', array("id"=>$commentID, "state"=>$state, "db_error"=>$this->db->getErrorMsg())); return false; }
		$this->log->add("DB", 'ChangedPublishStateComment', array("id"=>$commentID, "state"=>$state));
		
		//Tasks
		$tasks =& VBTasks::getInstance();
		$tasks->removeTask(VBTasks::$_Comment, $commentID);
		$tasks->removeTask(VBTasks::$_Spam_Comment, $commentID);
		
		return true;
	}
	
	function getNewCommentBox($box, $answer) {
	
		$par = new JObject();
			$par->Qaddnew = $this->access->isUserAllowedToAddNewComment($box, $answer, true);
			$par->user = $this->general->convertUser($this->user->id);
			if ($par->Qaddnew == "true") {
				$par->bid = $box->id;
				$par->aid = $answer->id;
				$par->BBToolbar = $this->vbparams->get('activate_bbcode') ? $this->general->getBBCodeToolbar2() : '';
			}
			$par->avatar = $this->user->getAvatar($this->user->id, 40);
		$out = $this->template->loadTemplate("newcomment", $par);
		return $out;
	}
	
	function removeComment($comment) {
		$text = @VBComment::getComment($comment)->comment;
		
		$sql = 'DELETE FROM `#__jvotesystem_comments` '
		. ' WHERE `id` = '.$this->db->quote($comment)
		. ' LIMIT 1';
		$this->db->setQuery($sql);
		$this->db->query();
		if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingComment', array("id"=>$answer, "db_error"=>$this->db->getErrorMsg())); return false; }
		$this->log->add("DB", 'RemovedComment', array("id"=>$comment, "comment" => $text));
		
		//Tasks
		$tasks =& VBTasks::getInstance();
		$tasks->removeTask(VBTasks::$_Comment, $comment);
		$tasks->removeTask(VBTasks::$_Spam_Comment, $comment);
		
		return true;
	}
	
	private $commentPage, $commentPageCount;
	function getComments($box, $answer, $page = 1, $withNewAnswer = 1) {
		//Page in Session speichern bzw. abrufen
		if($page > 0) {
			$session = JSession::getInstance('none',array());
			$pageArray = $session->get('jVoteBoxCommentPageArray', array());
			if(!isset($pageArray[$answer->id])) $pageArray[$answer->id] = array();
			$view = $this->vbparams->getView();
			if($withNewAnswer == 0) { 
				$pageArray[$answer->id][$view] = $page;
			} elseif($withNewAnswer == 1 AND isset($pageArray[$answer->id][$view])) {
				$page = $pageArray[$answer->id][$view];
			}
			$session->set("jVoteBoxCommentPageArray", $pageArray);	
		} else {
			$page = 1;
		}
		$this->commentpage = $page;
		
		//comments per page festlegen
		$commentsperpage = (!isset($box->comments_per_page) || $box->comments_per_page <= 0) ? $this->vbparams->get('commentsPerPage') : $box->comments_per_page;
		
		//Comments holen
		$sql = 'SELECT c. * '
        . ' FROM `#__jvotesystem_comments` AS c '
        . ' LEFT JOIN `#__jvotesystem_answers` AS a ON ( a. `id` = c. `answer_id` ) '
        . ' WHERE a. `id` = '.$answer->id.' AND ('
		. ' (c.`autor_id` = "'.$this->user->id.'" AND c.`published` = 0 AND "'.$this->user->id.'" != 0) ';
		if(!$this->access->isUserAllowedToChangePublishState($box)) $sql .= ' OR c.`published` = 1 ';  else  $sql .= ' OR c.`published` = 1 OR c.`published` = 0';
        $sql .= ' ) GROUP BY c. `id` '
		. ' ORDER BY c.`created` '.$this->vbparams->get('orderComment')
		. ' LIMIT '.($page*$commentsperpage-$commentsperpage).','.$commentsperpage;
		$this->db->setQuery($sql);
		$comments = $this->db->loadObjectList();
		
		$count = $this->getCommentCount($answer,$box);
		
		if($page > 1 AND !$comments) {
			$lastPage = ceil($count/$commentsperpage);
			if($page != $lastPage) {
				$page = $lastPage;
			}
			if($page == 1) $page = 0;
			return $this->getComments($box, $answer, $page, $withNewAnswer);
		}
		$this->commentPage = $page;
		$this->commentPageCount = count($comments); //???? #comments != #pages
		//VBSpam - Box laden
		$this->spam->loadData('comment');
		//Navigator - nötig?
		$vote =& VBVote::getInstance();
		$navi = $vote->buildnavi ($count, true, $page, $commentsperpage, $this->vbparams->get('shortNavi'), $uri = null, "comments");
		
		$out = '';
		
		$position = $withNewAnswer ? '' : ' style="position:absolute;width:100%;left:-100%;"';
		
		if($withNewAnswer) {
			$out .= '<div class="commentbox">';
		}
		
		$out .= '<div class="commentpage" data-cp="'.$page.'"'.$position.'>';
		$out .= $navi;
		if($comments) {
			foreach($comments AS $comment) {
				$out .= $this->getCommentBox($comment,$box);
			}
		}
		$out .= $navi;
		$out .= '</div>';
		
		if($withNewAnswer == 1) {
			$out .= '</div>';
			$out .= $this->getNewCommentBox($box, $answer);
		}
		return $out;
	}
	
	function getCommentPage() { //unused
		return $this->commentPage;
	}
	
	function getCommentPageCount() { //unused
		return $this->commentPageCount;
	}
	
	function getCommentCount($answer, $box) {
		$this->commentData = $this->getData($box);
		
		foreach($this->commentData AS $a) {
			if($a->id == $answer->id) return $a->comments;
		}
		
		return 0;
	}
	
	function getData($box) {
		if(empty($this->commentData)) {
			$sql = 'SELECT a. `id` , COUNT( c. `id` ) AS comments '
			. ' FROM `#__jvotesystem_answers` AS a '
			. ' LEFT JOIN `#__jvotesystem_comments` AS c ON ( a. `id` = c. `answer_id` AND ('
			. ' (c.`autor_id` = "'.$this->user->id.'" AND c.`published` = 0 AND "'.$this->user->id.'" != 0) ';
			if(!$this->access->isUserAllowedToChangePublishState($box)) $sql .= ' OR c.`published` = 1 ';  else  $sql .= ' OR c.`published` = 1 OR c.`published` = 0';
			$sql .= ' ) ) WHERE a.`box_id`='.$box->id
			. ' GROUP BY a. `id` '; 
			$this->db->setQuery($sql);
			$this->commentData = $this->db->loadObjectList();
		}
		return $this->commentData;
	}
	
	function unsetData() {
		unset($this->commentData);
	}
	
	function addComment($answer, $comment, $published=1) {
		$this->user->loadUser(true);
		
		$date = new JDate();
	
		$ins = new JObject();
		$ins->id = null;
		$ins->answer_id = $answer;
		$ins->comment = $comment;
		$ins->published = $published;
		$ins->autor_id = $this->user->id;
		$ins->created = $date->toMySQL();
		
		$this->db->insertObject('#__jvotesystem_comments', $ins);
		
		if(!$this->db->getErrorMsg())  {
			$this->id = $this->db->insertid();
			$this->log->add("DB", 'AddedComment', array("id"=>$this->id));
				
			//other extensions
			JPluginHelper::importPlugin( 'jvotesystem' );
			$dispatcher =& JDispatcher::getInstance();
			$res = $dispatcher->trigger( 'onCommentAdded', array( $this->id ) );
				
			return true;
		} else {
			$this->log->add("ERROR", 'AddingComment', array("db_error"=>$this->db->getErrorMsg()));
			return false;
		}
	}
	
	function getComment($CID) {
		$sql = 'SELECT * '
        . ' FROM `#__jvotesystem_comments` '
        . ' WHERE `id` = '.$CID;
		$this->db->setQuery($sql);
        return $this->db->loadObject();
	}
	
	function getCommentsPageCount($box, $comments) { // im vergleich zu getAnswersPageCount (answer.class) fehlt hier einiges ???
		$commentsperpage = (!isset($box->comments_per_page) || $box->comments_per_page <= 0) ? $this->vbparams->get('commentsPerPage') : $box->comments_per_page;
		$seiten = ($comments / $commentsperpage);
		$pages = ceil($seiten);
		return $pages;
	}
	
	function getPage() {
		return $this->commentpage;
	}
}//class
