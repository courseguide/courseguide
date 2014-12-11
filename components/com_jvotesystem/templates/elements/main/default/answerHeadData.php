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

$general->setHeadData(
	$answer->answer,
	$general->buildLink("poll", $box->id, "", array( "aid" => $aid, "ref" => "social" ), false, false),
	$lib->root().'/components/com_jvotesystem/assets/images/icon-100-jvotesystem.png',
	JText::_('Poll').': '.$box->title." - ".$box->question
);
?>