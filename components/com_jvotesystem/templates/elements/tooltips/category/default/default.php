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
<table>
	<tr>
		<td class="icon-48-category">
			
		</td>
		<td class="tool-content">
			<div class="tool-title">
				<a href="<?php echo $par->link;?>">
					<?php echo $par->name;?>
				</a> 
			</div>
			
			<table class="tool-infotable">
				<tr class="tool-infotable-values">
					<td> <?php echo $par->stats->polls;?> </td>
					<td> <?php echo $par->stats->votes;?> </td>
					<td> <?php echo $par->stats->comments;?> </td>
				</tr>
				<tr class="tool-infotable-legends">
					<td> <?php echo JText::_("Poll".($par->stats->polls != 1 ? "s" : ""))?> </td>
					<td> <?php echo JText::_("Vote".($par->stats->votes != 1 ? "s" : ""))?> </td>
					<td> <?php echo JText::_("Comment".($par->stats->comments != 1 ? "s" : ""))?> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>