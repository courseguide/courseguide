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
<table class="adminlist">
	<thead>
		<tr>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th>
				<?php echo JText::_("JVS_API_TITLE")?>
			</th>
			<th width="1%">
				<?php echo JText::_("JVS_API_KEY")?>
			</th>
			<th>
				<?php echo JText::_("JVS_API_ALLOWED_REQUESTS")?>
			</th>
			<th width="100">
				<?php echo JText::_("JVS_API_CURRENT_REQUESTS")?>
			</th>
			<th width="100">
				<?php echo JText::_("JVS_API_TOTAL_REQUESTS")?>
			</th>
			<th width="100">
				<?php echo JText::_("JVS_API_LAST_ACCESS")?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php 
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->key );
		$link 		= $this->general->buildAdminLink("apikey", $row->key);
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $checked; ?>
			</td>	
			<td>
				<a href="<?php echo $link;?>"><?php echo @$row->params->title;?></a>
			</td>
			<td style="font-style:italic;">
				<?php echo $row->key;?>
			</td>	
			<td>
				<?php echo implode(", ", $row->params->tasks);?>
			</td>	
			<td style="text-align:center;">
				<?php echo $row->count;?> / <?php echo $row->params->limit;?>
			</td>	
			<td style="text-align:center;">
				<?php echo $row->total_count;?>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertTime($row->last_access);?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	} ?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="view" value="apikeys" />
<input type="hidden" name="controller" value="apikeys" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
</form>

<?php $this->general->getAdminFooter(); ?>