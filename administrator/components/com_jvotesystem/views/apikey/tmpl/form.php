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
<script type="text/javascript">
<?php if(version_compare( JVERSION, '1.6.0', 'lt' )) { ?>
function submitbutton(task) {
<?php } else { ?>
Joomla.submitbutton = function(task) {
<?php } ?>
	var form = document.adminForm;
	if (task == 'cancel') {
		submitform( task );
	} else {
		submitform( task );
	}
}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col width-60" style="float: left;">
   <fieldset class="adminform">
      <legend><?php echo JText::_('Details'); ?></legend>

      <table class="admintable">
		<tr>
			<td width="100" class="key">
				<label for="title">
					<?php echo JText::_( 'JVS_API_TITLE' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="35" value="<?php echo @$this->item->params->title; ?>" />
			</td>
		</tr>
	  <tr>
         <td width="100" class="key">
            <label>
               <?php echo JText::_('JVS_API_ALLOWED_REQUESTS'); ?>:
            </label>
         </td>
         <td>
         	<ul>
	         <?php foreach($this->lists->tasks AS $task) { ?>
	         	<li style="overflow:hidden;">
		            <input <?php echo (in_array($task, $this->item->params->tasks)) ? 'checked="checked"' : "";?> type="checkbox" value="<?php echo $task;?>" id="tasks<?php echo $task;?>" name="tasks[]">
					<label for="tasks<?php echo $task;?>"><?php echo JText::_('JVS_API_TASK_'.$task); ?></label>
				</li>
			<?php } ?>
			</ul>
         </td>
      </tr>
      <tr>
         <td width="100" class="key">
            <label>
               <?php echo JText::_('JVS_API_REQUEST_LIMIT'); ?>:
            </label>
         </td>
         <td>
         	<input class="text_area" type="text" name="limit" id="limit" style="text-align:right;" size="5" value="<?php echo $this->item->params->limit;?>" />
         	<label><?php echo JText::_('JVS_API_REQUESTS_PER')?></label>
         	<select name="limit_type">
         	<?php foreach($this->lists->limit_types AS $type) { ?>
         		<option <?php if($this->item->params->limit_type == $type) { ?> selected="selected" <?php } ?> value="<?php echo $type;?>">
					<?php echo JText::_('JVS_API_LIMIT_TYPE_'.$type);?>
				</option>
         	<?php }?>	
         	</select>
         </td>
        </tr>
        <?php if(!$this->new) {?>
		<tr>
			<td width="100" class="key">
				<label for="title">
					<?php echo JText::_( 'JVS_API_KEY' ); ?>:
				</label>
			</td>
			<td>
				<i style="color:grey;"><?php echo $this->item->key;?></i>
			</td>
		</tr>
   		<?php }?>
      </table>
   </fieldset>
</div>
   <?php if(!$this->new) {?>
<div class="col width-40" style="float: right;">
   <fieldset class="adminform">
      <legend><?php echo JText::_('Statistik'); ?></legend>
		<?php echo $this->stats;?>
   </fieldset>
</div>
   <?php }?>
<div class="clr"></div>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="id" value="<?php echo $this->item->key; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="apikeys" />
</form>
<?php $this->general->getAdminFooter(); ?>