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

class AssistantViewPoll {
	var $document, $db, $user;

	function __construct($task, $interface) {
		$this->document = & JFactory::getDocument();
		$this->db = JFactory::getDBO();
		$this->user =& VBUser::getInstance(true);
		$this->charts =& VBCharts::getInstance();
		$this->general =& VBGeneral::getInstance();
		$access =& VBAccess::getInstance();
		$this->lib =& joomessLibrary::getInstance();
		
		//ID laden
		$id = JRequest::getInt('id', null);
		$type = JRequest::getString("type", "standard");
		
		//Switch für Editorbutton
		$origin = JRequest::getString("origin", "standard");
		
		//laden
		$this->template =& VBTemplate::getInstance();
		$this->vbparams =& VBParams::getInstance();
		$this->category =& VBCategory::getInstance();
		
		//Schon vorhanden?
		$new = ($id == null) ? true : false;
		
		if($type == "defaultSettings") {
			$this->document->setTitle(JText::_("DefaultSettings").' - jVoteSystem');
			$category = $this->category->getCategory($id); 
			if($category->id == 0) return;
		} else {
			$this->document->setTitle(JText::_("Poll").' - jVoteSystem');
			
			$catid = JRequest::getInt('catid', 1);
			$category = $this->category->getCategory($catid);
		}
		//Lists
		$lists = array();
		//Parent-Category
		$lists["categories"] = $this->category->getCategories();
		
		//Access
		$actions = array();
			//Vote
			$action = array();
			$action["title"] = JText::_('Vote');
			$action["name"] = "access";
		$actions[] = $action;
		//Show Result
			$action = array();
			$action["title"] = JText::_('show_result');
			$action["name"] = "result_access";
		$actions[] = $action;
		//add_answer
			$action = array();
			$action["title"] = JText::_('add_answer');
			$action["name"] = "add_answer_access";
		$actions[] = $action;
		//add_comment
			$action = array();
			$action["title"] = JText::_('add_comment');
			$action["name"] = "add_comment_access";
		$actions[] = $action;
		//Vote
			$action = array();
			$action["title"] = JText::_('admin');
			$action["name"] = "admin_access";
		$actions[] = $action;
		
		$accessCode = "";
		
		if($new == false AND $type != "defaultSettings") {
			//Box-Row holen
			$sql = 'SELECT b. * , COUNT( a. `id` ) AS answers '
			. ' FROM `#__jvotesystem_boxes` AS b '
			. ' LEFT JOIN `#__jvotesystem_answers` AS a ON ( b. `id` = a. `box_id`) '
			. ' WHERE b. `id` = '.$id
			. ' GROUP BY b. `id` '; 
			$this->db->setQuery($sql);
				
			$box = $this->db->loadObject();
			//Params verarbeiten
			$item = $this->vbparams->convertBoxParams($box);
			$this->charts->getFrontendChart2($id);
		} else {
			$sql = "SELECT * 
					FROM `#__jvotesystem_boxes`
					WHERE `catid`='".(($type == "defaultSettings") ? $id : $catid)."' AND `published` = -1 ";
			$this->db->setQuery($sql);
			
			$box = $this->db->loadObject();
			if($box) {
				//Params verarbeiten
				$item = $this->vbparams->convertBoxParams($box);
				if($type != "defaultSettings") {
					$new = true;
					$accessCode = $access->getHtmlAccessLists($actions, $item);
					$item->id = null;
					
					$date=JFactory::getDate();
					if($box->start_time == "0000-00-00 00:00:00") $box->start_time = $date->toMySQL();
				}
			} else {
				if(((!$category || $category->id == 0) && $interface != "administrator") || count($lists["categories"]) == 0 ) exit(JText::_("NOCATEGORYFOUND"));
				
					
				$iN = new JObject();
				$iN->id = null;
				$iN->title = '';
				$iN->question = '';
				$iN->alias = '';
				$iN->catid = ($type != "defaultSettings") ? ((!$category || $category->id == 0) ? $lists["categories"][0]->id : $category->id) : $id;
				$iN->allowed_votes = 5;
				$iN->published = 1;
				$iN->add_answer = 1;
				$iN->add_comment = 1;
				
				$iN->access = new JObject();
				$iN->access->access = 0;
				$iN->access->admin_access = 22;
				$iN->access->add_answer_access = 18;
				$iN->access->add_comment_access = 18;
				$iN->access->result_access = 0;
				
				$date=JFactory::getDate();
				$iN->start_time = $date->toMySQL();
				$iN->end_time = '0000-00-00 00:00:00';
				$iN->send_mail_admin_answer = 1;
				$iN->send_mail_user_answer_comments = 1;
				$iN->send_mail_admin_comment = 1;
				$iN->send_mail_user_comment_comments = 1;
				$iN->activate_spam = 1;
				$iN->spam_count = 5;
				$iN->spam_mail_admin_report = 1;
				$iN->spam_mail_admin_ban = 1;
				$iN->activate_ranking = 0;
				$iN->answers_orderby = "votes";
				$iN->answers_orderby_direction = "DESC";
				$iN->max_votes_on_answer = 3;
				$iN->ranking_orderby = "votes";
				$iN->ranking_orderby_direction = "DESC";
				$iN->show_author = 1;
				$iN->show_searchfield = 1;
				$iN->template = "default";
				$iN->show_result = "always";
				$iN->show_result_after_date = "0000-00-00 00:00:00";
				$iN->show_thankyou_message = 0;
				$iN->redirect = "chart";
				$iN->chart_type = "both";
				$iN->answers_per_page = -1;
				$iN->comments_per_page = -1;
				$iN->barcount = -1;
				$iN->cur_color_index = 0;
					
				$item = $iN;
			}
		}
		
		$category = $this->category->getCategory($item->catid);
		
		if($accessCode == "") $accessCode = $access->getHtmlAccessLists($actions, $item);
		
		
		//Antworten
		$sql = 	'SELECT * '
		. ' FROM `#__jvotesystem_answers`'
		. ' WHERE `box_id`='.$this->db->quote($item->id).' ORDER BY `id` ASC';
		$this->db->setQuery($sql);
		$answers = $this->db->loadObjectList();	
		
		//Data f�r Votes laden
		if(!$new) {
			//Answers
			$sql = 	'SELECT a.*, IFNULL(SUM(`votes`),0) AS votes, MAX(v.`voted_time`) AS lastvote, MAX(v.`voted_time`) AS firstvote '
					. ' FROM `#__jvotesystem_answers` AS a'
					. ' LEFT JOIN `#__jvotesystem_votes` AS v ON v.`answer_id`=a.`id`'
					. ' LEFT JOIN `#__jvotesystem_users` AS u ON (u.`id`=v.`user_id` AND u.`blocked`=0)'
					. ' WHERE a.`box_id`='.$item->id.' GROUP BY a.`id` ORDER BY votes DESC';
			$this->db->setQuery($sql);
			$answersVotes = $this->db->loadObjectList();		
			
			//Uservotes
			$sql = "SELECT `answer_id` AS aid, SUM(`votes`) AS voted, ju.id, ju.name, ju.username, IF(ju.`id` IS NULL, 0, 1) AS userFirst, `voted_time`, COUNT(v.`user_id`) AS users\n"
			. "FROM `#__jvotesystem_votes` AS v\n"
			. "LEFT JOIN `#__jvotesystem_users` AS u ON (v.`user_id`=u.`id` AND u.`blocked`=0)\n"
			. "LEFT JOIN `#__users` AS ju ON (u.`jid`=ju.`id`)\n"
			. "AND v.`votes`>0\n"
			. "GROUP BY `answer_id`, ju.`id`\n"
			. "ORDER BY userFirst DESC, voted DESC, voted_time ASC";
			$this->db->setQuery($sql);
			$uVotesRows = $this->db->loadObjectList();
			
			$uVotes = array();
			foreach($uVotesRows AS $uVote) {
				if(!isset($uVotes[$uVote->aid])) $uVotes[$uVote->aid] = array();
				$uVotes[$uVote->aid][] = $uVote;
			}
		}
		
		//Colors
		$this->document->addScriptDeclaration("jVS.colors = " . json_encode($this->general->getColors()));
		$this->document->addScriptDeclaration("jVS.cur_color = " . $item->cur_color_index);
		
		//HTML laden
		require_once ( ABSOLUTE_PATH.DS.'poll'.DS.'default.php' );
	}
}
?>