<?php
/**
 * @version		$Id: edit.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die; 

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>

<script type="text/javascript">
<?php if(version_compare( JVERSION, '1.6.0', 'lt' )) { ?>
function submitbutton(task) {
<?php } else { ?>
Joomla.submitbutton = function(task) {
<?php } ?>
	var form = document.adminForm;
	if (task == "defaultSettings") { 
		<?php if($this->item->id != null) {?>
		loadAssistant(this, "<?php echo JUri::root(true);?>", "poll", "&type=defaultSettings&id=<?php echo $this->item->id;?>");
		<?php } else { ?>
		alert("<?php echo JText::_("SAVEPOLLBEFOREYOUCANEDITDEFAULTSETTINGS");?>");
		<?php }?>
	} else {
		submitform( task );
	}
} 
</script>

<form action="index.php" method="post" name="adminForm">
<div class="col width-60" style="float: left;">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Details' ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" class="key">
				<label for="title">
					<?php echo JText::_( 'Name' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="title" id="title" size="35" value="<?php echo $this->item->title; ?>" />
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="alias">
					<?php echo JText::_( 'Alias' ); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="alias" id="alias" size="25" value="<?php echo $this->item->alias; ?>" />
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="name">
					<?php echo JText::_( 'Access_level' ); ?>:
				</label>
			</td>
			<td>
				<select name="accesslevel" id="accesslevel">
				<?php foreach($this->lists["accesslevel"] AS $list) { ?>
					<option value="<?php echo $list["value"];?>"<?php if($this->item->accesslevel == $list["value"]) {?> selected="selected"<?php }?>><?php echo $list["name"];?></option>
				<?php }?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="name">
					<?php echo JText::_( 'Parent_Category' ); ?>:
				</label>
			</td>
			<td>
				<select name="parent_id" id="parent_id">
					<option value="0"><?php echo JText::_("NO_PARENT_CATEGORY");?></option>
				<?php foreach($this->lists["parent"] AS $list) {
					if($list->id != $this->item->id) {?>
					<option value="<?php echo $list->id;?>"<?php if($this->item->parent_id == $list->id) {?> selected="selected"<?php }?>><?php for($u = 0; $u <= $list->level; $u++) echo "- "; echo $list->title;?></option>
				<?php } }?>
				</select>
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
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
	</table>
	</fieldset>
</div>
<div class="col width-40" style="float: right;">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Parameters' ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" class="key">
				<label for="auto_publish">
					<?php echo JText::_( 'AutoPublish_New_Polls' ); ?>:
				</label>
			</td>
			<td>
				<input <?php echo (@$this->item->autopublish_polls == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="autopublish_polls1" name="params[autopublish_polls]">
				<label for="autopublish_polls1"><?php echo JText::_('JYES'); ?></label>
				<input <?php echo (@$this->item->autopublish_polls == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="autopublish_polls0" name="params[autopublish_polls]">
				<label for="autopublish_polls0"><?php echo JText::_('JNO'); ?></label>
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="mail_admin_new_poll">
					<?php echo JText::_( 'SEND_MAIL_ADMIN_NEW_POLL' ); ?>:
				</label>
			</td>
			<td>
				<input <?php echo (@$this->item->mail_admin_new_poll == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="mail_admin_new_poll1" name="params[mail_admin_new_poll]">
				<label for="mail_admin_new_poll1"><?php echo JText::_('JYES'); ?></label>
				<input <?php echo (@$this->item->mail_admin_new_poll == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="mail_admin_new_poll0" name="params[mail_admin_new_poll]">
				<label for="mail_admin_new_poll0"><?php echo JText::_('JNO'); ?></label>
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="allowed_tabs">
					<?php echo JText::_( 'ALLOWED_TABS' ); ?>:
				</label>
			</td>
			<td>
				<div style="float:left;"><input type="checkbox" id="allowed_tabs_settings" name="allowed_tabs[settings]"<?php if(in_array("settings", @$this->item->allowed_tabs)) {?> checked="checked"<?php }?> value="settings"><label for="allowed_tabs_settings"><?php echo JText::_("Einstellungen");?> | </label></div>
				<div style="float:left;"><input type="checkbox" id="allowed_tabs_display" name="allowed_tabs[display]"<?php if(in_array("display", @$this->item->allowed_tabs)) {?> checked="checked"<?php }?>  value="display"><label for="allowed_tabs_display"><?php echo JText::_("Display");?> | </label></div>
				<div style="float:left;"><input type="checkbox" id="allowed_tabs_email_spam" name="allowed_tabs[email_spam]"<?php if(in_array("email_spam", @$this->item->allowed_tabs)) {?> checked="checked"<?php }?>  value="email_spam"><label for="allowed_tabs_email_spam">eMail & Spam | </label></div>
				<div style="float:left;"><input type="checkbox" id="allowed_tabs_access" name="allowed_tabs[access]"<?php if(in_array("access", @$this->item->allowed_tabs)) {?> checked="checked"<?php }?>  value="access"><label for="allowed_tabs_access"><?php echo JText::_('Access'); ?> | </label></div>
				<div style="float:left;"><input type="checkbox" id="allowed_tabs_result" name="allowed_tabs[result]"<?php if(in_array("result", @$this->item->allowed_tabs)) {?> checked="checked"<?php }?>  value="result"><label for="allowed_tabs_result"><?php echo JText::_('Result'); ?> | </label></div>
				<div style="float:left;"><input type="checkbox" id="allowed_tabs_votes" name="allowed_tabs[votes]"<?php if(in_array("votes", @$this->item->allowed_tabs)) {?> checked="checked"<?php }?>  value="votes"><label for="allowed_tabs_votes"><?php echo JText::_('Votes'); ?></label></div>
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="edit_own_poll">
					<?php echo JText::_( 'edit_own_poll' ); ?>:
				</label>
			</td>
			<td>
				<input <?php echo (@$this->item->edit_own_poll == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="edit_own_poll1" name="params[edit_own_poll]">
				<label for="edit_own_poll1"><?php echo JText::_('JYES'); ?></label>
				<input <?php echo (@$this->item->edit_own_poll == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="edit_own_poll0" name="params[edit_own_poll]">
				<label for="edit_own_poll0"><?php echo JText::_('JNO'); ?></label>
			</td>
		</tr>
		<tr>
			<td width="100" class="key">
				<label for="remove_own_poll">
					<?php echo JText::_( 'remove_own_poll' ); ?>:
				</label>
			</td>
			<td>
				<input <?php echo (@$this->item->remove_own_poll == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="remove_own_poll1" name="params[remove_own_poll]">
				<label for="remove_own_poll1"><?php echo JText::_('JYES'); ?></label>
				<input <?php echo (@$this->item->remove_own_poll == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="remove_own_poll0" name="params[remove_own_poll]">
				<label for="remove_own_poll0"><?php echo JText::_('JNO'); ?></label>
			</td>
		</tr>
	</table>
	</fieldset>
</div>
<div class="col width-60" style="float: left;">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Description' ); ?></legend>
	<?php
		echo $this->editor->display( 'description',  $this->item->description, '100%;', '350', '75', '20', array('pagebreak', 'readmore', 'jvotesystembutton') ) ;
	?>
	</fieldset>
</div>
<div class="col width-40" style="float: right;">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Access' ); ?></legend>
	<?php echo $this->accessHtml;?>
	</fieldset>
</div>

	<input type="hidden" name="option" value="com_jvotesystem" />
	<input type="hidden" name="view" value="category" />
	<input type="hidden" name="controller" value="categories" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>