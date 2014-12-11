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

?>
<div class="answer" data-a="<?php echo $par->aid; ?>">
	<?php echo $par->comment_icon;?>
<?php //<!--Ranking-->?>
<?php if($par->activate_ranking) { ?>
	<div class="rank"><?php echo $par->rank;?></div>
<?php } ?>
	<div class="answerbox">
		<?php //Vote ?>
		<div class="votebutton">
			<a href="#" class="votingbutton <?php echo $par->votebutton_class;?>"<?php echo $par->votebutton_disabled ?> title="<?php echo $par->translation_vote;?>" data-jvs_tooltip="<?php echo urlencode($par->votebutton_tooltip);?>"> </a>
			<a class="vote-down<?php if($par->resetAllowed) {?> reset<?php }?>"<?php if($par->uservotes == 0) {?> style="display:none;"<?php }?> data-steps="1"><span class="ownvotes"><?php echo $par->uservotes;?></span></a>
		</div>
		<?php //<!--AnswerField-->?>
		<div class="answertext jvsclearfix">
			<?php echo $par->answer;?>
		</div>
		<div class="answericons"><span>
		<?php if($par->icon_trash_active) { ?>
			<a href="#" class="trash icon" title="<?php echo JText::_("Entfernen");?>"><?php echo JText::_("Entfernen");?></a>
		<?php } ?>
		<?php if($par->icon_state_show) { ?>
			<a title="<?php echo JText::_("CHANGE_STATE");?>" href="#" class="state icon <?php echo $par->icon_state_state ?>"><?php echo JText::_("CHANGE_STATE");?></a>
		<?php } ?>
		<?php if($par->icon_spam_active) { ?>
			<a title="<?php echo JText::_("REPORT_SPAM");?>" href="#" class="report icon"><?php echo JText::_("REPORT_SPAM");?></a>
		<?php } ?>
		</span></div>
	</div>
	<?php if($par->show_result) { ?>
	<?php //<!--VoteCount-->?>
		<div class="count">
			<?php if($par->show_userlist) { ?>
				<a href="#" class="icon userlist" title="<?php echo JText::_("SHOW_USERLIST");?>"></a>
			<?php } ?>
			<span class="votecount"><?php echo $par->votes;?></span>
		</div>
	<?php }?>
	<?php if($par->show_comments) { ?>
		<div class="comments-holder"><div class="comments"><?php echo $par->comments;?></div></div>
	<?php } else { ?>
		<div class="comments-holder"><div class="comments"></div></div>
	<?php } ?>
</div>
