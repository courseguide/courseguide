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
			<th width="%1">
				#
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th width="5">
				<?php echo JText::_( 'ID' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Name' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Regex' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Replace' ); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'Button' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
		</tr>			
	</thead>
  <tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= JRoute::_( 'index.php?option=com_jvotesystem&controller=bbcodes&view=bbcode&model=bbcode&task=edit&cid[]='. $row->id );
		
		$published 	= JHTML::_('grid.published', $row, $i );
		
		//Button 
		$img     = $row->withButton ? 'tick.png' : 'publish_x.png';
        $task    = $row->withButton ? 'unWithButton' : 'withButton';
		if(version_compare( JVERSION, '1.6.0', 'lt' ))
			$withButton = '
			<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''.$task .'\')">
			<img src="images/'. $img .'" border="0" /></a>';
		else
			$withButton = '
			<a href="#" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" >'.
			JHtml::_('image','admin/'.$img, '', NULL, true).'</a>'
			;
		
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<img src="<?php echo JURI::root(true);?>/components/com_jvotesystem/assets/images/bbcode/<?php echo $row->buttonImage;?>" />
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
			</td>
			<td>
				<?php echo htmlentities ($row->regex); ?>
			</td>
			<td>
				<?php echo htmlentities ($row->replace); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $withButton; ?>
			</td>
			<td style="text-align:center;">
				<?php echo $published; ?>
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
<input type="hidden" name="view" value="bbcodes" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="bbcodes" />
</form>
<?php $this->general->getAdminFooter(); ?>