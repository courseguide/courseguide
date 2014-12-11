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
	} else if (form.box_id.value == ""){
		form.box_id.style.border = "2px solid red";
		form.box_id.focus();
	} else if (form.answer.value == ""){
		form.answer.style.border = "2px solid red";
		form.answer.focus();
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
         <td width="100" align="right" class="key">
            <label for="box_id">
               <?php echo JText::_('Poll'); ?>:
            </label>
         </td>
         <td>
            <select name="box_id" id="box_id">
				<option <?php if($this->lists->id == '') { ?> selected="selected" <?php } ?> value="">
					<?php echo JText::_('Select');?>
				</option>
				<?php foreach($this->lists->polls AS $box) { ?>
				<option <?php if($box->id == $this->lists->id) { ?> selected="selected" <?php } ?> value="<?php echo $box->id;?>">
					<?php echo $box->title;?>
				</option>
				<?php } ?>
			</select>
         </td>
      </tr>
      <tr>
         <td width="100" align="right" class="key">
            <label for="answer">
               <?php echo JText::_('Answer'); ?>:
            </label>
         </td>
         <td>
         	<?php //echo VBGeneral::getInstance()->getBBCodeToolbar2();?>
            <textarea class="text_area" name="answer" id="answer" rows="3" cols="50"><?php echo $this->item->answer;?></textarea>
         </td>
      </tr>
      <tr>
         <td width="100" align="right" class="key">
            <label for="color">
               <?php echo JText::_('JVS_Color'); ?>:
            </label>
         </td>
         <td>
            <?php echo joomessLibrary::getInstance()->special('colorInput', array( "name" => "color", "value" => "#".$this->item->color ))?>
         </td>
      </tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="published">
					<?php echo JText::_( 'Publish_State' ); ?>:
				</label>
			</td>
			<td>
				<input <?php echo ($this->item->published == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="published1" name="published">
				<label for="published1"><?php echo JText::_('JYES'); ?></label>
				<input <?php echo ($this->item->published == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="published0" name="published">
				<label for="published0"><?php echo JText::_('JNO'); ?></label>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="protected">
					<?php echo JText::_( 'Protected' ); ?>:
				</label>
			</td>
			<td>
				<input <?php echo ($this->item->no_spam_admin == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="protected1" name="protected">
				<label for="protected1"><?php echo JText::_('JYES'); ?></label>
				<input <?php echo ($this->item->no_spam_admin == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="protected0" name="protected">
				<label for="protected0"><?php echo JText::_('JNO'); ?></label>
			</td>
		</tr>
	  </table>
   </fieldset>
   <?php if(!$this->new) {?>
   <fieldset class="adminform">
      <legend><?php echo JText::_('Statistik'); ?></legend>
		<?php echo $this->stats;?>
   </fieldset>
   <?php }?>
</div>
   <?php if(!$this->new) {?>
<div class="col width-40" style="float: right;">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Votes' ); ?></legend>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="30"> <?php echo JText::_("Autor");?> </th>
					<th> <?php echo JText::_("Votes");?> </th>
					<th> <?php echo JText::_("Last_vote");?> </th>
				</tr>
			</thead>
			<tbody>
			<?php $totalvotes = 0; foreach($this->votes AS $vote) {?>
				<tr>
					<td style="padding: 0px;"> <?php echo VBUser::getInstance()->getAvatar($vote->user_id, 40);?> </td>
					<td style="text-align:center;"> <?php $totalvotes += $vote->votes; echo $vote->votes;?> </td>
					<td style="text-align:center;"> <?php echo JFactory::getDate($vote->voted_time)->toFormat();?> </td>
				</tr>
			<?php }?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo JText::_("JVS_STATS_TOTAL_VOTES");?>: <b><?php echo $totalvotes;?></b>
					</td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Comments' ); ?></legend>
		<table class="adminlist">
			<thead>
				<tr>
					<th width="30"> <?php echo JText::_("Autor");?> </th>
					<th> <?php echo JText::_("Comment");?> </th>
					<th> <?php echo JText::_("Created");?> </th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($this->comments AS $comment) {?>
				<tr>
					<td style="padding: 0px;"> <?php echo VBUser::getInstance()->getAvatar($comment->autor_id, 40);?> </td>
					<td> <?php echo $this->general->buildHtmlLink($this->general->buildAdminLink("comment", $comment->id), $this->general->shortText($comment->comment, 150, false, false) );?> </td>
					<td style="text-align:center;"> <?php echo JFactory::getDate($comment->created)->toFormat();?> </td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php echo JText::_("JVS_STATS_TOTAL_COMMENTS");?>: <b><?php echo count($this->comments);?></b>
					</td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
</div>
   <?php }?>
<div class="clr"></div>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="answers" />
</form>
<?php $this->general->getAdminFooter(); ?>