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
<?php if (!$par->radiobutton) { ?> 
<?php //<!--Ranking-->?>
<?php if($par->activate_ranking) { ?>
	<div class="rank"><?php echo $par->rank;?></div>
<?php } ?>
<?php //<!--VoteBox-->?>
<?php if(($par->vote_state == null AND $par->votes_left > 0) OR $par->show_result) { ?>
	<div class="answervote">
		<a href="#" class="votingbutton <?php echo $par->votebutton_class;?>"<?php echo $par->votebutton_disabled ?> data-jvs_tooltip="<?php echo urlencode($par->votebutton_tooltip);?>"><?php echo $par->translation_vote;?></a>
	</div>
	<?php if($par->show_result) { ?>
	<?php //<!--VoteCount-->?>
			<div class="votecount">
				<?php echo $par->votes;?>
			</div>
	<?php } ?>
<?php } ?>
<?php } ?> 
<?php //<!--AnswerBox-->?>
	<div class="answerbox">
<?php //<!--AnswerField-->?>
		<div class="text">
<?php //<!--Answer-->?>
<?php if ($par->radiobutton) { ?>
	<input class="radiobutton" type="radio" name="jvsradios<?php echo $par->bid;?>" id="jvsradio<?php echo $par->aid; ?>" data-a="<?php echo $par->aid; ?>" <?php echo $par->votebuttonradio_disabled?>/>
	<label for="jvsradio<?php echo $par->aid; ?>" class="answertext"><?php echo $par->answer;?></label>
<?php } else {?>
	<span class="answertext"><?php echo $par->answer;?></span>
<?php }?>
<?php //<!--Author-->?>
<?php if($par->author_show == 1 && 0) { ?>
			<p class="autor"><?php echo $par->author_text;?></p>
<?php } ?>
		</div>
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
