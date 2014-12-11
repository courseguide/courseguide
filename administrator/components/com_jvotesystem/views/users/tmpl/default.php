<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
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
				<?php echo JText::_( 'ID' ); ?>
			</th>
			<th width="5">
				<?php echo JText::_( 'JID' ); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>			
			<th>
				<?php echo JText::_( 'Name' ); ?>
			</th>
			<th width="100">
				<?php echo JText::_( 'IP-Address' ); ?>
			</th>
			<th width="130">
				<?php echo JText::_( 'First_Visit' ); ?>
			</th>
			<th width="130">
				<?php echo JText::_( 'Last_Visit' ); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'Aktiv' ); ?></th>
		</tr>			
	</thead>
	<tfoot>
    <tr>
      <td colspan="9">
      	<?php echo $this->pagination->getListFooter(); ?>
      </td>
    </tr>
  </tfoot>
  <tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		
		$img 	= $row->blocked ? 'publish_x.png' : 'tick.png';
		$task 	= $row->blocked ? 'unblock' : 'block';
		$alt 	= $row->blocked ? JText::_( 'Enabled' ) : JText::_( 'Blocked' );
		
		if($row->jid != 0) {
			$jUser = JFactory::getUser($row->jid);
			$name = $jUser->name;
		} else {
			$name = JText::_('Anonym');
			$row->jid = '-';
		}
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo $row->jid; ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php echo $this->general->convertUser($row->id); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $row->ip; ?>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertTime($row->registered_time); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertTime($row->lastVisitDate); ?>
			</td>
			<td align="center">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')">
						<?php if(!version_compare( JVERSION, '1.6.0', 'lt' )) echo JHtml::_('image','admin/'.$img, '', NULL, true); else {?>
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } ?></a>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="users" />
</form>
<?php $this->general->getAdminFooter(); ?>