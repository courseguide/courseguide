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
	} else if (form.name.value == ""){
		form.name.style.border = "2px solid red";
		form.name.focus();
	} else if (form.regex.value == ""){
		form.regex.style.border = "2px solid red";
		form.regex.focus();
	} else {
		submitform( task );
	}
}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
   <fieldset class="adminform">
      <legend><?php echo JText::_('Details'); ?></legend>

      <table class="admintable">
	  <tr>
         <td width="100" align="right" class="key">
            <label for="name">
               <?php echo JText::_('Name'); ?>:
            </label>
         </td>
         <td>
            <input class="text_area" type="text" name="name" id="name" size="50" value="<?php echo $this->item->name;?>" />
         </td>
      </tr>
      <tr>
         <td width="100" align="right" class="key">
            <label for="regex">
               <?php echo JText::_('Regex'); ?>:
            </label>
         </td>
         <td>
            <textarea class="text_area" name="regex" id="regex" rows="3" cols="50"><?php echo $this->item->regex;?></textarea>
         </td>
      </tr>
	  <tr>
         <td width="100" align="right" class="key">
            <label for="replace">
               <?php echo JText::_('Replace'); ?>:
            </label>
         </td>
         <td>
            <textarea class="text_area" name="replace" id="replace" rows="3" cols="50"><?php echo $this->item->replace;?></textarea>
			<br />
			<b>{bbCodeImagePath}</b> = "/components/com_jvotesystem/assets/images/bbcode/"
         </td>
      </tr>
	  <tr>
         <td width="100" align="right" class="key">
            <label for="replaceNot">
               <?php echo JText::_('ReplaceNot'); ?>:
            </label>
         </td>
         <td>
            <input class="text_area" type="text" name="replaceNot" id="replaceNot" size="50" value="<?php echo $this->item->replaceNot;?>" />
         </td>
      </tr>
   </table>
   </fieldset>
   <fieldset class="adminform">
      <legend><?php echo JText::_('Frontend'); ?></legend>

      <table class="admintable">
	  <tr>
         <td width="100" align="right" class="key">
            <label for="withButton">
               <?php echo JText::_('withButton'); ?>:
            </label>
         </td>
         <td>
            <input <?php if($this->item->withButton == 1) echo 'checked="checked"';?> type="radio" value="1" id="withButton1" name="withButton">
			<label for="withButton1"><?php echo JText::_('YES'); ?></label>
			<input <?php if($this->item->withButton == 0) echo 'checked="checked"';?> type="radio" value="0" id="withButton0" name="withButton">
			<label for="withButton0"><?php echo JText::_('NO'); ?></label>
         </td>
      </tr>
	  <tr>
         <td width="100" align="right" class="key">
            <label for="buttonInfo">
               <?php echo JText::_('buttonInfo'); ?>:
            </label>
         </td>
         <td>
            <input class="text_area" type="text" name="buttonInfo" id="buttonInfo" size="40" value="<?php echo $this->item->buttonInfo;?>" />
         </td>
      </tr>
	  <tr>
         <td width="100" align="right" class="key">
            <label for="editorCode">
               <?php echo JText::_('editorCode'); ?>:
            </label>
         </td>
         <td>
            <input class="text_area" type="text" name="editorCode" id="editorCode" size="40" value="<?php echo $this->item->editorCode;?>" />
         </td>
      </tr>
      <tr>
         <td width="100" align="right" class="key">
            <label for="buttonImage">
               <?php echo JText::_('buttonImage'); ?>:
            </label>
         </td>
         <td>
			/components/com_jvotesystem/assets/images/bbcode/
            <input class="text_area" type="text" name="buttonImage" id="buttonImage" size="20" value="<?php echo $this->item->buttonImage;?>" />
         </td>
      </tr>
   </table>
   </fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="bbcodes" />
</form>
<?php $this->general->getAdminFooter(); ?>