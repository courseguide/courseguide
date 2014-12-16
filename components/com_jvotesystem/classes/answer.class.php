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

class VBAnswer
{
	//Variablen
	var $db, $user, $document, $id;
	private $vote;
	
	private function __construct() {
		$this->document = & JFactory::getDocument();
		$this->db =& JFactory::getDBO();
		$this->user =& VBUser::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->vote =& VBVote::getInstance();
		$this->vbparams =& VBParams::getInstance();
		$this->log =& VBLog::getInstance();
	}
	
	static function &getInstance() {
		static $instance;
		if(empty($instance)) {
			$instance = new VBAnswer();
		}
		return $instance;
	}

    function addAnswer($boxID, $answer, $published, $color) {
    	$this->user->loadUser(true);
    	
		$date = new JDate();
	
		$ins = new JObject();
		$ins->id = null;
		$ins->box_id = $boxID;
		$ins->answer = $answer;
		$ins->published = $published;
		$ins->autor_id = $this->user->id;
		$ins->created = $date->toMySQL();
		$ins->color = substr($color, 1);
		
		$this->db->insertObject('#__jvotesystem_answers', $ins);
		$this->id = $this->db->insertid();
		
		if(!$this->db->getErrorMsg())  {
			$this->log->add("DB", 'AddedAnswer', array("id"=>$this->id));
			
			$this->vote->checkVote($boxID, $this->id,true);
			
			//other extensions
			JPluginHelper::importPlugin( 'jvotesystem' );
			$dispatcher =& JDispatcher::getInstance();
			$res = $dispatcher->trigger( 'onAnswerAdded', array( $this->id ) );
			
			return true;
		} else {
			$this->log->add("ERROR", 'AddingAnswer', array("db_error"=>$this->db->getErrorMsg()));
			return false;
		}
	}
	
	function getID() {
		return $this->id;
	}
	
	function removeAnswer($answer) {
		$text = @VBAnswer::getAnswer($answer, false)->answer;
		
    	$vbtemplate =& VBTemplate::getInstance(); 
    	$path = $vbtemplate->getTemplatePath("main").DS."removingAnswer.php";
    	if(JFile::exists($path)) @require $path;
    	
		$sql = 'DELETE FROM `#__jvotesystem_answers` '
		. ' WHERE `id` = '.$answer
		. ' LIMIT 1';
		$this->db->setQuery($sql);
		$this->db->query();
		if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingAnswer', array("id"=>$answer, "db_error"=>$this->db->getErrorMsg())); return false; }
		
		$sql = 'DELETE FROM `#__jvotesystem_votes` '
		.' WHERE `answer_id` = '.$answer;
		$this->db->setQuery($sql);
		$this->db->query();
		$votes = $this->db->getAffectedRows();
		if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingVotesOfAnswer', array("id"=>$answer, "db_error"=>$this->db->getErrorMsg())); return false; }
		
		$sql = 'DELETE FROM `#__jvotesystem_comments` '
		.' WHERE `answer_id` = '.$answer;
		$this->db->setQuery($sql);
		$this->db->query();
		$comments = $this->db->getAffectedRows();
		if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingCommentsOfAnswer', array("id"=>$answer, "db_error"=>$this->db->getErrorMsg())); return false; }
		
		$this->log->add("DB", 'RemovedAnswer', array("id"=> (int)$answer, "answer" => $text, "votes"=>$votes, "comments"=>$comments));
		
		//Tasks
		$tasks =& VBTasks::getInstance();
		$tasks->removeTask(VBTasks::$_Answer, $answer);
		$tasks->removeTask(VBTasks::$_Spam_Answer, $answer);
		
		return true;
	}
	
	function changePublishStateAnswer($answerID, $state) {
	
		$upd->id = $answerID;
		$upd->published = $state;
		$upd->no_spam_admin = 1;
		
		$this->db->updateObject('#__jvotesystem_answers', $upd, 'id');
		if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'ChangingPublishStateAnswer', array("id"=>$answerID, "state"=>$state, "db_error"=>$this->db->getErrorMsg())); return false; }
		
		//Tasks
		$tasks =& VBTasks::getInstance();
		$tasks->removeTask(VBTasks::$_Answer, $answerID);
		$tasks->removeTask(VBTasks::$_Spam_Answer, $answerID);
		
		$this->log->add("DB", 'ChangedPublishStateAnswer', array("id"=>$answerID, "state"=>$state));
		return true;
	}
	
	function getAnswersPageCount($box, $aid, $search = '') {
		$answer = $this->vote->getAnswers($box->id, array( "answers" => $aid, 'search' => $search ), 0, 1); 
		
		if(empty($answer)) return null;
		
		$answersperpage = (!isset($box->answers_per_page) || $box->answers_per_page <= 0) ? $this->vbparams->get('answersPerPage') : $box->answers_per_page;
		$seiten = ($answer[0]->counter / $answersperpage);
		$pages = ceil($seiten);
		return $pages;
	}
	
	function getRankOfAnswer($bid, $aid) {
		$answers = $this->vote->getAnswers($bid, array( "answers" => $aid ), 0, 1);
		if(empty($answers) || $answers[0]->published == 0) return "#";
		return (int)$answers[0]->rank;
	}
	
	function getAnswer($answerID, $published = true) {
		$sql = 'SELECT a. * , COUNT( c. `id` ) AS comments '
        . ' FROM `#__jvotesystem_answers` AS a '
        . ' LEFT JOIN `#__jvotesystem_comments` AS c ON ( c. `answer_id` = a. `id` ';
        if($published == true) $sql .= ' AND c. `published` = 1 ';
        $sql .= ' ) '
        . ' WHERE a. `id` = '.$this->db->quote($answerID)
        . ' GROUP BY a. `id`'; 
		$this->db->setQuery($sql);
        return $this->db->loadObject();
	}
}//class
