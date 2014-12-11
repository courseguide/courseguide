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

?>
<div class="answer" data-a="<?php echo $par->aid; ?>">
	<div class="answerContainer" style="height:<?php echo $par->poll->tmpl_galleryvote_preview_max_height;?>px">
		<a href="<?php echo $lib->root();?>/images/jvotesystem/<?php echo $par->aid;?>/large.jpg" class="vote_thumb_link">
			<img class="vote_thumb" style="max-height:<?php echo $par->poll->tmpl_galleryvote_preview_max_height;?>px;max-width:<?php echo $par->poll->tmpl_galleryvote_preview_max_width;?>px;" src="<?php echo $lib->root();?>/images/jvotesystem/<?php echo $par->aid;?>/medium.jpg" />
		</a>
		<div class="toolbar">
			<div class="vote-down<?php if($par->resetAllowed) {?> reset<?php }?>"<?php if($par->uservotes == 0) {?> style="display:none;"<?php }?>><span class="point"></span><div class="reset-bub"><div><span><?php echo JText::_("RESET_VOTES");?></span></div></div><em><span class="operator">+</span><span class="ownvotes"><?php echo $par->uservotes;?></span></em></div>
			<a href="#" class="votingbutton mainRGB <?php echo $par->votebutton_class;?>"<?php echo $par->votebutton_disabled ?> data-jvs_tooltip="<?php echo urlencode($par->votebutton_tooltip);?>">+</a>
			<?php if($par->showShare == 1) { ?>
				<a href="#" class="sharebutton mainRGB" onclick="return false;" title="<?php echo JText::_("JVS_Share_Answer");?>" data-jvs_tooltip="<?php echo urlencode($par->social);?>"> </a>
			<?php }?>
			<?php if($par->author_show == 1 && $par->author_id != 0) { ?> 
				<a href="<?php echo $par->author_link;?>" class="autorbutton mainRGB" data-u="<?php echo $par->author_id;?>"><?php echo VBUser::getInstance()->getAvatar($par->author_id, 32, false, false);?></a>
			<?php }?>
			<div class="text"><?php echo $par->answer;?></div>
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
		</div>
		<?php if($par->activate_ranking) { ?>
			<div class="rank mainRGB"><?php echo $par->rank;?></div>
		<?php } ?>
		<?php if($par->show_result) { ?>
			<div class="count_hover mainRGB">
			<?php if($par->show_userlist && false) { ?>
				<a href="#" class="icon userlist" title="<?php echo JText::_("SHOW_USERLIST");?>"></a>
			<?php } ?>
				
				<span class="votecount"><?php echo $par->votes;?></span> 
				<span class="votecount_label"><?php echo $par->translation_votes;?></span>
			</div>
		<?php } ?>
	</div>
</div>
