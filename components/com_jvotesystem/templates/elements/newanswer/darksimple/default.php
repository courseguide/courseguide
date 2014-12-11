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
<div class="newanswerbox answer">
	<div class="rank">+</div>
	<div class="answerbox">
		<form action="index.php?option=com_jvotesystem&view=ajax&task=addAnswer&box=<?php echo $par->bid;?>" method="post">
			<?php echo $par->BBToolbar ?>
			<textarea class="answertext text" type="text" rows="1" cols="90" name="answer" scrollbar=no data-start="<?php echo JText::_('ADD_NEW_ANSWER');?>"><?php echo JText::_('ADD_NEW_ANSWER');?></textarea>
			<input type="submit" value="<?php echo JText::_('Vorschlagen');?>" class="submitbutton" name="submit" data-nohide="true">
			<input type="button" value="<?php echo JText::_('RESET');?>" class="button" name="reset" style="visibility:hidden;position:absolute;">
		</form>
	</div>
</div>
<?php } elseif ($par->Qaddnew === "needToLogin") { ?>
<div class="newanswerbox answer">
	<div class="rank">+</div>
	<div class="answerbox"><form>
		<textarea class="needLogin" class="answertext text" type="text" rows="1" cols="90" name="answer" scrollbar=no data-start="<?php echo JText::_('ADD_NEW_ANSWER');?>"><?php echo JText::_('ADD_NEW_ANSWER');?></textarea>
		<input type="submit" value="<?php echo JText::_('Vorschlagen');?>" class="submitbutton" name="submit" data-nohide="true">
	</form></div>
</div>
<?php } ?>