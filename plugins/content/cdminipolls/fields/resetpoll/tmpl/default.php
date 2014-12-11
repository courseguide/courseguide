<?php
/**
 * Core Design Scriptegrator plugin for Joomla! 1.7
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla 
 * @subpackage	System
 * @category	Plugin
 * @version		2.0.7
 * @copyright	Copyright (C) 2007 - 2011 Great Joomla!, http://www.greatjoomla.com
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL 3
 * 
 * This file is part of Great Joomla! extension.   
 * This extension is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die;
?>
<div id="<?php echo $type; ?>">
	<h4><?php echo JText::_('PLG_CONTENT_CDMINIPOLLS_ADMINISTRATION_RESETPOLL_TITLE'); ?></h4>
	<p><?php echo JText::_('PLG_CONTENT_CDMINIPOLLS_ADMINISTRATION_RESETPOLL_INFOTEXT'); ?></p>
	<?php if ($polls): ?>
		<select name="pollToResetSelect">
			<?php foreach($polls as $poll): ?>
				<option value="<?php echo (int) $poll->id; ?>"><?php echo (string) $poll->identifier; ?></option>
			<?php endforeach; ?>
		</select>
		<button type="button"><?php echo JText::_('PLG_CONTENT_CDMINIPOLLS_ADMINISTRATION_RESETPOLL_BUTTON'); ?></button>
		<script type="text/javascript">
			<!--
			if (typeof(jQuery) === 'function') {
				jQuery(document).ready(function($){
					$('#' + '<?php echo $type; ?> button').click(function(e) {
						e.preventDefault();
						$('body').append(
									$('<form />', {
										name : 'pollToResetForm',
										action : '',
										method : 'post',
										css : { display : 'none' },
										html : $('<input />', {
											type : 'hidden',
											name : 'pollToReset',
											value : $('select[name="pollToResetSelect"]').val()
										})
									})
								);
						$('form[name="pollToResetForm"]').submit();
					});
				});
			}
			// -->
		  </script>
	<?php else: ?>
		<?php echo JText::_('PLG_CONTENT_CDMINIPOLLS_ADMINISTRATION_RESETPOLL_NO_POLLS'); ?>
	<?php endif; ?>
	<div></div>
</div>