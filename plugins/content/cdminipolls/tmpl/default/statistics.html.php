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
<div class="<?php echo $this->_name; ?>_statistics">
	<?php if ( (int) self::$settings->get( 'show_statistics_total', 1 ) ): ?>
		<?php if ($result->get('total', 0)): ?>
			<div class="<?php echo $this->_name; ?>_statistics_total_votes"><?php echo JText::sprintf('PLG_CONTENT_CDMINIPOLLS_STATISTICS_VOTES', (int) $result->get('total', 0)); ?></div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( (int) self::$settings->get( 'show_statistics_firstvote', 1 ) ): ?>
		<?php if ($firstvote = $result->get('firstvote', '')): ?>
			<div class="<?php echo $this->_name; ?>_statistics_firstvote"><?php echo JText::sprintf('PLG_CONTENT_CDMINIPOLLS_STATISTICS_FIRSTVOTE', JHtml::_('date', (string) $firstvote)); ?></div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( (int) self::$settings->get( 'show_statistics_lastvote', 1 ) ): ?>
		<?php if ($lastvote = $result->get('lastvote', '')): ?>
			<div class="<?php echo $this->_name; ?>_statistics_lastvote"><?php echo JText::sprintf('PLG_CONTENT_CDMINIPOLLS_STATISTICS_LASTVOTE', JHtml::_('date',  (string) $lastvote)); ?></div>
		<?php endif; ?>
	<?php endif; ?>
</div>