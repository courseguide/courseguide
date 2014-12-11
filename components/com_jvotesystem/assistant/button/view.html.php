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

class AssistantViewButton {
	var $document, $db, $user;

	function __construct($task) {
		$this->document = & JFactory::getDocument();
		$this->db = JFactory::getDBO();
		$this->user =& VBUser::getInstance(true);
		
		$vote =& VBVote::getInstance();
		
		$polls = $vote->getPolls(array(), 0, -1);
		
		//HTML laden
		require_once ( ABSOLUTE_PATH.DS.'button'.DS.'default.php' );
	}
}
?>