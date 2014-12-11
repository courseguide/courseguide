<?php
/**
 * Core Design Mini Polls plugin for Joomla! 1.7
 * @author		Daniel Rataj, <info@greatjoomla.com>
 * @package		Joomla
 * @subpackage	Content
 * @category   	Plugin
 * @version		1.0.0
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

// no direct access
defined('_JEXEC') or die;
?>
<div
class="<?php echo $this->_name; ?> <?php echo self::$uitheme; ?>"
id="<?php echo self::$identifier; ?>"
data-total="<?php echo (int) $result->get('total', 0); ?>"
data-identifier="<?php echo self::$identifier; ?>"
data-multiple_voting="<?php echo (int) self::$settings->get('multiple_voting', 0); ?>"
>
	<div class="ui-widget">
	  <?php if (is_array($options) and $options): ?>
	  	<?php if (self::$settings->exists('title')): ?>
	  		<h4 class="<?php echo $this->_name; ?>_title"><?php echo self::$settings->get('title', ''); ?></h4>
	  	<?php endif; ?>
	  	<?php foreach($options as $poll_option): ?>
		  	<div class="<?php echo $this->_name; ?>_option">
		  		<div class="<?php echo $this->_name; ?>_option_text"><?php echo $poll_option; ?></div>
		  		<div class="<?php echo $this->_name; ?>_votecontainer">
		  			<div
		  			class="<?php echo $this->_name; ?>_progressbar"
	  				data-votes="<?php echo (int) $result->get(md5($poll_option) . '.votes', ''); ?>"
	  				style="width: <?php echo (string) self::$settings->get('progressbar_width', '200px'); ?>;"
	  				></div>
			  		<div class="<?php echo $this->_name; ?>_votebutton">
				  		<form action="" method="post">
				  			<button type="submit"><?php echo JText::_('PLG_CONTENT_CDMINIPOLLS_VOTE_FOR_OPTION', true); ?></button>
				  			<input type="hidden" name="<?php echo $this->_name; ?>_poll_option" value="<?php echo md5($poll_option); ?>" />
				  			<input type="hidden" name="<?php echo $this->_name; ?>_identifier" value="<?php echo $row->get('identifier', self::$identifier); ?>" />
				  			<input type="hidden" name="<?php echo $this->_name; ?>_task" value="ajax_vote" />
				  			<?php echo JHtml::_('form.token'); ?>
				  		</form>
			  		</div>
			  		<div style="clear: both;"></div>
		  		</div>
		  	</div>
	  	<?php endforeach; ?>
	  	<?php if ( (int) self::$settings->get( 'show_statistics', 1 ) ): ?>
	  		<?php require dirname(__FILE__) . DS . 'statistics.html.php'; ?>
	  	<?php endif; ?>
	  <?php endif; ?>
	</div>
</div>