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

<div class="files">
<?php foreach($this->files AS $file) {?>
	<div class="file"<?php if($file == $this->file) {?> style="font-weight: bold;"<?php }?>>
		<a href="index.php?option=com_jvotesystem&view=logs&file=<?php echo $file;?>"><?php $dates = explode(".", rtrim($file, ".php")); echo JFactory::getDate($dates[0]."-".$dates[1]."-01")->toFormat("%B %Y");?></a>
	</div>
<?php }?>
</div>
<form action="index.php" method="post" name="adminForm">
<table class="logs adminlist" data-type="default">
	<thead>
		<tr>
			<th width="16">#</th>
			<th width="16">*</th>
			<th width="100"><?php echo JText::_("User");?></th>
			<th><?php echo JText::_("Message");?></th>
			<th width="100"><?php echo JText::_("Params");?></th>
			<th width="100"><?php echo JText::_("Created");?></th>
		</tr>
	</thead>
	<tfoot>
    <tr>
      <td colspan="6">
      	<?php echo $this->pagination->getListFooter(); ?>
      </td>
    </tr>
  </tfoot>
	<tbody>
	<?php foreach($this->data AS $row) { $row = $this->log->convertMsg($row);?>
		<tr>
			<td class="icon-16 icon-<?php echo strtolower($row->type);?>"></td>
			<td class="icon-16 icon-<?php echo strtolower($row->action);?>"></td>
			<td style="text-align:center;"><?php echo $this->general->convertUser($row->vsid);?></td>
			<td><?php echo $row->msg;?></td>
			<td style="text-align:center;"><span data-jvs_tooltip="<?php echo urlencode($this->general->dumpTree($row->pars));?>"><?php echo sprintf(JText::_("JVS_LOG_COUNTPARAMS"), sizeof(JArrayHelper::fromObject($row->pars)));?></span></td>
			<td style="text-align:center;"><?php echo $this->general->convertTime($row->created);?></td>
		</tr>
	<?php }?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="view" value="logs" />
<input type="hidden" name="controller" value="logs" />
</form>

<?php $this->general->getAdminFooter(); ?>