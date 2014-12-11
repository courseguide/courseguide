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
<?php //<!--Ranking-->?>
<?php if($par->activate_ranking) { ?>
	<div class="rank"><?php echo $par->rank;?></div>
<?php } ?>
<?php //<!--VoteBox-->?>
<?php if(($par->vote_state == null AND $par->votes_left > 0) OR $par->show_result) { ?>
	<div class="answervote">
<?php if($par->show_result) { ?>
<?php //<!--VoteCount-->?>
		<div class="count">
<?php //<!--UserList-->?>
<?php if($par->show_userlist) { ?>
			<a href="#" class="icon userlist" title="<?php echo JText::_("SHOW_USERLIST");?>"></a>
<?php } ?>
<?php //<!--Count-->?>
		<div class="vote-down<?php if($par->resetAllowed) {?> reset<?php }?>"<?php if($par->uservotes == 0) {?> style="display:none;"<?php }?>><span class="point"></span><div class="reset-bub"><div><span><?php echo JText::_("RESET_VOTES");?></span></div></div><em><span class="operator">+</span><span class="ownvotes"><?php echo $par->uservotes;?></span></em></div>
			<span class="votecount"><?php echo $par->votes;?></span>
			<p class="votecounttext"><?php echo $par->translation_votes;?></p>
		</div>
<?php } ?>
<?php //<!--VoteButton-->?>
		<a href="#" class="votingbutton <?php echo $par->votebutton_class;?>"<?php echo $par->votebutton_disabled ?> data-jvs_tooltip="<?php echo urlencode($par->votebutton_tooltip);?>"><?php echo $par->translation_vote;?></a>
	</div>
<?php } ?>
<?php //<!--AnswerBox-->?>
	<div class="answerbox">
<?php //<!--AnswerField-->?>
		<div class="text jvsclearfix">
<?php //<!--Answer-->?>
			<span class="answertext jvsclearfix"><?php echo $par->answer;?></span>
		</div>
		<div class="answeroptions">
		<?php //<!--Author-->?>
			<div class="answericons">
			<?php //<!--Icons-->?>
			<?php if($par->icon_trash_active) { ?>
						<a title="<?php echo JText::_("Entfernen");?>" href="#" class="trash icon"></a>
			<?php } ?>
			<?php if($par->icon_state_show) { ?>
						<a title="<?php echo JText::_("CHANGE_STATE");?>" href="#" class="state icon <?php echo $par->icon_state_state ?>"></a>
			<?php } ?>
			<?php if($par->icon_spam_active) { ?>
						<a title="<?php echo JText::_("REPORT_SPAM");?>" href="#" class="report icon"></a>
			<?php } ?>
			</div>
		<p class="autor jvsclearfix">
		<?php if($par->showShare == 1) { ?><span class="sharebutton" data-jvs_tooltip="<?php echo urlencode($par->social);?>"><?php echo JText::_("JVS_SHARE_ANSWER");?></span> - <?php }?>
		<?php if($par->author_show == 1) { ?>
			<span class="author" data-u="<?php echo $par->author_id;?>"><a href="<?php echo $par->author_link;?>"><?php echo $par->author_name;?></a></span> - 
		<?php } ?>
			<span class="creation_time"><?php echo $par->creation_time;?></span>

		</p>
<?php //<!--CommentIcon-->?>
			<?php echo $par->comment_icon;?>
		</div>
<?php //<!--Comments-->?>
<?php if($par->show_comments) { ?>
		<div class="comments"><?php echo $par->comments;?></div>
<?php } else { ?>
		<div class="comments"></div>
<?php } ?>
	</div>
</div>
