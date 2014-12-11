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
<div class="newanswerbox">
	<div class="label_for_newanswer mainRGB"><?php echo JText::_('NEW');?></div>
	<form action="#" method="post">
		<table>
			<tr>
				<td><?php echo JText::_("Title");?>:</td>
				<td><input type="text" id="answertext" name="answer" value="" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("Picture");?>:</td>
				<td><input type="file" name="answerimg" id="answerimg" disabled="disabled" /></td>
			</tr>
		</table>
		<div class="controls">
			<input type="submit" value="<?php echo JText::_('Vorschlagen');?>" class="button mainRGB" name="submitButton">
			<input type="button" value="<?php echo JText::_('RESET');?>" class="button mainRGB" name="resetButton" style="visibility:hidden;position:absolute;">
		</div>
	</form>
</div>
<?php } elseif ($par->Qaddnew === "needToLogin") {?>
<div class="newanswerbox" style="min-height: auto; text-align: center; padding: 5px;">
	<?php echo JText::_('ERRORNEEDTOLOGIN');?>
</div>
<?php }?>