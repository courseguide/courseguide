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


$lib =& joomessLibrary::getInstance();
$jvs =& jVSConnect::getInstance();

$vbanswer 	=& $jvs->get("Answer");
$vote 		=& $jvs->get("Vote");
$general 	=& $jvs->get("General");

$conf = array();
$conf["id"] = 				$par->aid;
$conf["pid"] = 				$par->pid;
$conf["uservotes"] = 		$par->uservotes;
$conf["votes"] = 			$par->count;
$conf["max_votes"] = 		$par->max_votes;
$conf["allowed_votes"] = 	$par->allowed_votes;
$conf["token"] = 			$par->token;
$conf["root"] = 			$par->root;
$conf["error"] = 			$par->error;
$conf["lang"] =				JFactory::getLanguage()->getTag();

$answer = $vbanswer->getAnswer($par->aid);
$poll = $vote->getBox($par->pid);

$lang = array();
if(!$par->error) {
	$bbcode = $jvs->setBBCode(false);
	$answer->answer = $general->shortText($answer->answer, 50, false);
	$jvs->setBBCode($bbcode);
	
	$poll->link = $jvs->route("poll", $poll->id, "", array(), false);
	
	$lang["thankyou_message"] = JText::_('THANKYOUFORVOTING');
	$lang["tooltip"] = $par->voteAllowed ? sprintf(JText::_('JVS_NOTICE_VOTE_FOR_ANSWER_OF_THE_POLL'), $answer->answer, $poll->link ,$poll->title) : $vote->getAnswerTooltip($poll, $answer);
	$lang["vote"] = JText::_('VOTE');
} else {
	$lang["error"] = $par->error_msg;
}

 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<script src="<?php echo $lib->root();?>/components/com_jvotesystem/templates/elements/votebutton/default/default.js" type="text/javascript"></script>
		<script src="<?php echo $lib->root();?>/components/com_jvotesystem/assets/js/jvs-embed-jquery.js" type="text/javascript"></script>
		<link rel="stylesheet" href="<?php echo $lib->root();?>/components/com_jvotesystem/templates/elements/votebutton/default/default.css" type="text/css" />
		<script type="text/javascript"> jVSEmbed.conf = <?php echo json_encode($conf);?>; jVSEmbed.lang = <?php echo json_encode($lang);?>; </script>
	</head>
	<body>
		<a class="button<?php if($par->uservotes != 0){?> voted<?php } elseif($par->error) {?> error<?php }?>" href="#Vote"><?php echo ($par->uservotes == 0) ? JText::_("Vote") : "+".$par->uservotes;?></a>
		<div class="count"><span class="vote_count"><?php echo $par->count;?></span> <?php echo JText::_("Votes");?></div>
	</body>
</html>