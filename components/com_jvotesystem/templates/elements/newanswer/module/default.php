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
<div class="makenew"<?php echo $par->hidden?>><span><?php echo JText::_('ADD_NEW_ANSWER');?></span></div>
<div class="newanswerbox answerbox">
<div><span><?php echo JText::_("New");?></span></div>
	<form action="index.php?option=com_jvotesystem&view=ajax&task=addAnswer&box=<?php echo $par->bid;?>" method="post">
		<?php echo $par->BBToolbar ?>
		<div class="text jvsclearfix">
			<textarea type="text" rows="1" cols="90" name="answer" scrollbar=no data-start="<?php echo JText::_('ADD_NEW_ANSWER');?>"><?php echo JText::_('ADD_NEW_ANSWER');?></textarea>
		</div>
		<div>
			<input type="submit" value="<?php echo JText::_('Vorschlagen');?>" class="button" name="submit">
			<input type="button" value="<?php echo JText::_('CANCEL');?>" class="button" name="cancel">
		</div>
	</form>
</div>
<?php } elseif ($par->Qaddnew === "needToLogin") { ?>
<div class="makenew needLogin"<?php echo $par->hidden?>><span><?php echo JText::_('ADD_NEW_ANSWER');?></span></div>
<?php } ?>