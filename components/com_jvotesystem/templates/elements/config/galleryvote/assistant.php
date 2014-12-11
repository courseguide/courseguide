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

$lib =& joomessLibrary::getInstance();
?>
<table class="category"><tr>
	<td class="name">
		<?php echo chunk_split(JText::_('JVS_TMPL_GV_LAYOUT'), 1, "<br />"); ?>
	</td>
	<td>
		<table class="params">
			<tr class="number medium">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_TEMPLATE_COLOR'); ?>:
				</td>
				<td class="field">
					<?php echo $lib->special("colorInput", array("width" => "85px", "name" => "tmpl_galleryvote_main_color", "value" => isset($item->tmpl_galleryvote_main_color) ? $item->tmpl_galleryvote_main_color : "#07B7E3"));?>
				</td>
			</tr>
			<tr class="number medium">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_FONT_COLOR'); ?>:
				</td>
				<td class="field">
					<?php echo $lib->special("colorInput", array("width" => "85px", "name" => "tmpl_galleryvote_font_color", "value" => isset($item->tmpl_galleryvote_font_color) ? $item->tmpl_galleryvote_font_color : "#FFFFFF"));?>
				</td>
			</tr>
		</table>
	</td>
</tr></table>
<table class="category"><tr>
	<td class="name">
		<?php echo chunk_split(JText::_('JVS_TMPL_GV_UPLOAD'), 1, "<br />"); ?>
	</td>
	<td>
		<table class="params">
			<tr class="number large">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_MAX_UPLOAD_SIZE'); ?>:
				</td>
				<td class="field">
					<input type="text" name="tmpl_galleryvote_max_upload_size" id="tmpl_galleryvote_max_upload_size" size="10" maxlength="5" value="<?php echo isset($item->tmpl_galleryvote_max_upload_size) ? $item->tmpl_galleryvote_max_upload_size : "2048" ;?>" /> kB
				</td>
			</tr>
			<tr class="number large">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_MAX_UPLOADS_PER_USER'); ?>:
				</td>
				<td class="field">
					<input type="text" name="tmpl_galleryvote_max_uploads_per_user" id="tmpl_galleryvote_max_uploads_per_user" size="10" maxlength="5" value="<?php echo isset($item->tmpl_galleryvote_max_uploads_per_user) ? $item->tmpl_galleryvote_max_uploads_per_user : "5" ;?>" />
				</td>
			</tr>
		</table>
	</td>
</tr></table>
<table class="category"><tr>
	<td class="name">
		<?php echo chunk_split(JText::_('JVS_TMPL_GV_PREVIEW'), 1, "<br />"); ?>
	</td>
	<td>
		<table class="params">
			<tr class="number medium">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_MAX_HEIGHT'); ?>:
				</td>
				<td class="field">
					<input type="text" name="tmpl_galleryvote_preview_max_height" id="tmpl_galleryvote_preview_max_height" size="10" maxlength="5" value="<?php echo isset($item->tmpl_galleryvote_preview_max_height) ? $item->tmpl_galleryvote_preview_max_height : "220" ;?>" /> px
				</td>
			</tr>
			<tr class="number medium">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_MAX_WIDTH'); ?>:
				</td>
				<td class="field">
					<input type="text" name="tmpl_galleryvote_preview_max_width" id="tmpl_galleryvote_preview_max_width" size="10" maxlength="5" value="<?php echo isset($item->tmpl_galleryvote_preview_max_width) ? $item->tmpl_galleryvote_preview_max_width : "220" ;?>" /> px
				</td>
			</tr>
			<tr class="select medium">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_RESIZE_TYPE'); ?>:
				</td>
				<td class="field">
					<select name="tmpl_galleryvote_preview_resize_type">
						<option value="cube"><?php echo JText::_("JVS_TMPL_GV_CUBE");?></option>
						<option value="resize"><?php echo JText::_("JVS_TMPL_GV_RESIZE");?></option>
					</select>
				</td>
			</tr>
		</table>
	</td>
</tr></table>
<table class="category"><tr>
	<td class="name">
		<?php echo chunk_split(JText::_('JVS_TMPL_GV_POPUP'), 1, "<br />"); ?>
	</td>
	<td>
		<table class="params">
			<tr class="number medium">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_MAX_HEIGHT'); ?>:
				</td>
				<td class="field">
					<input type="text" name="tmpl_galleryvote_popup_max_height" id="tmpl_galleryvote_popup_max_height" size="10" maxlength="5" value="<?php echo isset($item->tmpl_galleryvote_popup_max_height) ? $item->tmpl_galleryvote_popup_max_height : "800" ;?>" /> px
				</td>
			</tr>
			<tr class="number medium">
				<td class="param">
					<?php echo JText::_('JVS_TMPL_GV_MAX_WIDTH'); ?>:
				</td>
				<td class="field">
					<input type="text" name="tmpl_galleryvote_popup_max_width" id="tmpl_galleryvote_popup_max_width" size="10" maxlength="5" value="<?php echo isset($item->tmpl_galleryvote_popup_max_width) ? $item->tmpl_galleryvote_popup_max_width : "800" ;?>" /> px
				</td>
			</tr>
		</table>
	</td>
</tr></table>