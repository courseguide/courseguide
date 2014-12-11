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

<div data-c="<?php echo $par->cid;?>">
	<div class="comment">
		<div class="avatar"><div class="point"></div><?php echo $par->avatar;?></div>
		
		<div class="comment-text">
			<div class="icons">
				<span class="creation-date"><?php echo $par->creation_date;?></span>
				<?php if($par->Qtrash) { ?>
							<a title="<?php echo JText::_("Entfernen") ?>" class="trash icon"></a>
				<?php } ?>
				
				<?php if($par->Qpublish) { ?>
							<a title="<?php echo JText::_("CHANGE_STATE") ?>" class="state icon <?php echo $par->state?>"></a>
				<?php } elseif ($par->state == "unpublished") {?>
							<a title="<?php echo JText::_("JVS_COMMENT_UNPUBLISHED") ?>" class="icon <?php echo $par->state?>"></a>
				<?php } ?>
				
				<?php if($par->Qreport) { ?>
							<a title="<?php echo JText::_("REPORT_SPAM") ?>" class="report icon"></a>
				<?php } ?>
			</div>
			<?php echo $par->commenttext;?>
			
		</div>
	</div>
</div>