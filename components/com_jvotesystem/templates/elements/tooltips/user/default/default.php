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
		<td>
			<?php echo $par->avatar;?>
		</td>
		<td class="tool-content">
			<div class="tool-title">
			<?php if($par->do_link) {?> <a href="<?php echo $par->link;?>"> <?php }?>
				<?php echo $par->name;?>
			<?php if($par->do_link) {?> </a> <?php }?>
			</div>
			
			<table class="tool-infotable">
				<tr class="tool-infotable-values">
					<td> <?php echo $par->stats->votes;?> </td>
					<td> <?php echo $par->stats->answers;?> </td>
					<td> <?php echo $par->stats->comments;?> </td>
				</tr>
				<tr class="tool-infotable-legends">
					<td> <?php echo JText::_("Vote".($par->stats->votes != 1 ? "s" : ""))?> </td>
					<td> <?php echo JText::_("Answer".($par->stats->answers != 1 ? "s" : ""))?> </td>
					<td> <?php echo JText::_("Comment".($par->stats->comments != 1 ? "s" : ""))?> </td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php if($par->show_info) {?>
<table class="admininfo_list">
	<thead>
		<tr>
			<th colspan="2"> <?php echo JText::_("Admin_Info");?> </th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th> <?php echo JText::_("ID");?>: </th>
			<td> <?php echo $par->id;?> </td>
		</tr>
		<tr>
			<th> <?php echo JText::_("JID");?>: </th>
			<td> <?php echo $par->jid;?> </td>
		</tr>
		<tr>
			<th> <?php echo JText::_("IP-Address");?>: </th>
			<td> <?php echo $par->ip;?> </td>
		</tr>
		<tr>
			<th> <?php echo JText::_("First_Visit");?>: </th>
			<td> <?php echo $par->first_visit;?> </td>
		</tr>
		<tr>
			<th> <?php echo JText::_("Last_Visit");?>: </th>
			<td> <?php echo $par->last_visit;?> </td>
		</tr>
		<tr>
			<th> <?php echo JText::_("Sessions");?>: </th>
			<td> <?php echo $par->stats->sessions;?> </td>
		</tr>
	</tbody>
</table>
<?php }?>