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
<?php if ($par->Qaddnew === "true") { ?>
<div class="newcommentbox comment">
	<div class="avatar"><div class="point"></div><?php echo $par->avatar;?></div>

	<form action="index.php?option=com_jvotesystem&view=ajax&task=addComment&box=<?php echo $par->bid ?>&answer=<?php echo $par->aid ?>" method="post">
		<div class="comment-text">
			<textarea type="text" rows="1" cols="80" name="comment" scrollbar="no" data-start="<?php echo JText::_('WRITE_NEW_COMMENT')?>"><?php echo JText::_('WRITE_NEW_COMMENT')?></textarea>
		</div>
		<?php echo $par->BBToolbar ?>
		<div>
			<input type="submit" value="<?php echo JText::_('Kommentieren')?>" name="submit">
			<input type="button" value="<?php echo JText::_('RESET')?>" name="reset">
		</div>
	</form>
</div>
<?php } elseif ($par->Qaddnew === "needToLogin") { ?>
<div class="newcommentbox comment">
	<div class="avatar"><div class="point"></div><?php echo $par->avatar;?></div>

	<form>
		<div class="comment-text">
			<textarea class="needLogin" type="text" rows="1" cols="80" name="comment" scrollbar="no" data-start="<?php echo JText::_('WRITE_NEW_COMMENT')?>"><?php echo JText::_('WRITE_NEW_COMMENT')?></textarea>
		</div>
	</form>
</div>
<?php } ?>