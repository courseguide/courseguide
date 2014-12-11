<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @author Johannes Me�mer
 * @copyright (C) 2010- Johannes Me�mer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

?>
<form action="index.php" method="post" name="adminForm">
	
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_('ID'); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th width="30%">
				<?php echo JText::_('Title'); ?>
			</th>
			<th width="50%">
				<?php echo JText::_('Description'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
			</th>
			<th width="6%">
				<?php echo JText::_('Access_Level'); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
			<th width="3%"><?php echo JText::_( 'POLLS' ); ?></th>
		</tr>
	</thead>
	<?php
	$k = 0;
	foreach($this->items AS $i => $row) {
		$checked = JHTML::_('grid.id', $i, $row->id);
		$link 		= 'index.php?option=com_jvotesystem&view=category&id='. $row->id ;
		$published 	= JHTML::_('grid.published', $row, $i);
		
		$childs = $this->category->getCategoryChilds($row->parent_id);
		$pos = array_search($row->id, $childs);
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>">
					<?php for($u = 0; $u < $row->level; $u++) echo "|&mdash; ";
					echo $row->title; ?>
				</a>
			</td>
			<td>
				<?php echo strip_tags($row->description);?>
			</td>
			<td class="order">
				<span><?php echo $this->pagination->orderUpIcon($i, ($pos > 0), 'orderup', 'JLIB_HTML_MOVE_UP'); ?></span>
				<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($pos < (count($childs) - 1)), 'orderdown', 'JLIB_HTML_MOVE_DOWN'); ?></span>
				<input type="text" name="order[]" disabled="disabled" size="5" value="<?php echo $row->order;?>" class="text-area-order" />
			</td>
			<td style="text-align:center;">
				<?php echo @$this->accesslevels[$row->accesslevel]["name"]; ?>
			</td>
			<td style="text-align:center;">
				<?php echo $published; ?>
			</td>
			<td style="text-align:center;">
				<a href="index.php?option=com_jvotesystem&view=boxen&filter_cid=<?php echo $row->id;?>">
					<b><?php echo $row->polls;?></b>
					<img src="components/com_jvotesystem/assets/images/icon-16-forward.png" />
				</a>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</table>
</div>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="view" value="categories" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="categories" />
</form>
<?php $this->general->getAdminFooter(); ?>