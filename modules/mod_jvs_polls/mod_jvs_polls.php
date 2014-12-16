<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)'); 

//Import jVSConnect Libary
$connect_file = JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'connect.php';
if(JFile::exists($connect_file)) include_once($connect_file);
else {
	echo '<p style="color:red;">Failed to load module - jVoteSystem is not installed!</span>';
	return;
}

//load Instance of jVSConnect - check active
$jvs =& jVSConnect::getInstance();
if(!$jvs->active()) {
	echo '<p style="color:red;">Failed to load module - jVoteSystem is not enabled!</span>';
	return;
}

//Check version
if(!method_exists($jvs, "checkVersion") || !$jvs->checkVersion("2.56")) {
	echo '<p style="color:red;">Failed to load module - jVoteSystem Version 2.56 is required!</span>';
	return;
}

//Load Joomess Libary Plugin -> load CSS, JS-Files or jQuery plugins
$lib =& joomessLibrary::getInstance();

//CSS
$lib->css("modules/mod_jvs_polls/general.css"); //That's it! :)

$general 	=& VBGeneral::getInstance();
$user 		=& VBUser::getInstance();
		
$general->charset('utf-8'); //Load charset	

//Params
$par_short 			= $params->get("short", 50);
$par_bbcode 		= $params->get("bbcode", false);
$par_limit			= $params->get("limit", 10);
$par_avatar_size	= 30;//$params->get("avatar_size", 30);
$par_all_cats		= $params->get('cat_all', true);

$par_show_avatar 	= $params->get("show_avatar", true);
$par_show_title 	= $params->get("show_title", true);
$par_show_question 	= $params->get("show_question", true);
$par_show_votes		= $params->get("show_votes", true);
$par_show_date 		= $params->get("show_date", true);

$filter = array();
if(!$par_all_cats)
	$filter["cid"] 	= $params->get('cat_id', 0);
$filter["order"] 	= $params->get("order", "popular");
$filter["subcats"] 	= $params->get('sub_cats', true);
$filter["time"] 	= $params->get('time', 'all-time');
$filter["stats"] 	= $par_show_votes;

$polls = $jvs->getPolls($filter, 0, $par_limit); 

if(!$par_bbcode)
	$bbcode = $jvs->setBBCode(false); //Disable BBCode
?>
<table class="jvs-module-polls"><tbody>
<?php foreach($polls AS $poll) {?>
	<tr onclick="location.href='<?php echo $general->buildLink("poll", $poll->id);?>';">
		<?php if($par_show_avatar) {?><td class="avatar"> <?php echo $user->getAvatar($poll->autor_id, $par_avatar_size);?> </td><?php }?>
		<td class="poll"> 
			<?php if($par_show_title) {?><span class="poll-title"><?php echo $general->shortText($poll->title, $par_short, false);?></span> <br><?php }?>
			<?php if($par_show_question) {?><span class="poll-question"><?php echo $general->shortText($poll->question, $par_short, false);?></span><?php }?>
		</td>
		<td class="stats">
			<?php if($par_show_votes) {?><span class="votes"><?php echo $poll->votes;?></span> <br><?php }?>
			<?php if($par_show_date) {?><span class="created"><?php echo $general->convertTimeTight($poll->created);?></span><?php }?>
		</td>
	</tr>
<?php }?>
</tbody></table>

<?
	
/* The copyright information may not be removed or made invisible! To remove the code, please purchase a version on www.joomess.de. Thanks!*/
joomessLibrary::getInstance()->copyright('jVoteSystem');

if(!$par_bbcode)
	$jvs->setBBCode($bbcode); //Reset BBCode

$general->charset('module'); //Load charset header
