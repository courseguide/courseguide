<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip'); ?>

<div class="row-fluid">
	<div class="span8 rsleft rsspan8">
		<div>
			<div class="dashboard-container">
				<div class="span4 rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
						<a href="index.php?option=com_modules&amp;task=module.edit&amp;id=<?php echo $this->moduleid; ?>">
							<?php echo JHTML::_('image', 'administrator/components/com_rssearch/assets/images/icons/modules.png', JText::_('COM_RSSEARCH_MODULE')); ?>
							<span class="dashboard-title"><?php echo JText::_('COM_RSSEARCH_MODULE'); ?></span>
						</a>
						</div>
					</div>
				</div>
				<div class="span4 rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
								<a href="index.php?option=com_plugins&amp;filter_folder=rssearch">
									<?php echo JHTML::_('image', 'administrator/components/com_rssearch/assets/images/icons/plugins.png', JText::_('COM_RSSEARCH_PLUGINS')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSSEARCH_PLUGINS'); ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="span4 rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
								<a href="http://www.rsjoomla.com/support.html" target="_blank">
									<?php echo JHTML::_('image', 'administrator/components/com_rssearch/assets/images/icons/support.png', JText::_('COM_RSSEARCH_SUPPORT')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSSEARCH_SUPPORT'); ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div>
			<div class="dashboard-container">
				<div class="dashboard-info">
					<span style="text-align: left;">
						<h3><?php echo JText::_('COM_RSSEARCH_PLUGINS'); ?></h3>
						<?php echo JText::_('COM_RSSEARCH_PLUGINS_DESC'); ?>
					</span>
					<table class="dashboard-table">
						<?php foreach ($this->plugins as $plugin => $state) { ?>
						<?php $badge = $state ? 'success' : 'important'; ?>
						<tr>
							<td nowrap="nowrap" align="right" width="35%"><?php echo JText::_('COM_RSSEARCH_PLUGIN_'.strtoupper($plugin)); ?></td>
							<td><strong class="badge badge-<?php echo $badge; ?>"><?php echo JText::_('COM_RSSEARCH_STATE_'.$state); ?></strong></td>
						</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<br />
	
	<div class="span4 rsleft rsspan4 pull-left rsj_margin">
		<div class="dashboard-container">
			<div class="dashboard-info">
				<span>
					<img src="components/com_rssearch/assets/images/rssearch.png" align="middle" alt="RSSearch! logo"/>
				</span>
				<table class="dashboard-table">
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSSEARCH_INSTALLED_VERSION') ?> </strong></td>
						<td colspan="2"><?php echo RS_PRODUCT . ' ' . RS_VERSION. ' rev ' . RS_REVISION; ?></td>
					</tr>
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSSEARCH_COPYRIGHT') ?> </strong></td>
						<td nowrap="nowrap">&copy; 2007 - <?php echo date('Y'); ?> <a href="http://www.rsjoomla.com" target="_blank">RSJoomla.com</a></td>
					</tr>
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSSEARCH_LICENSE') ?> </strong></td>
						<td nowrap="nowrap">GPL Commercial License</td>
					</tr>
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSSEARCH_AUTHOR') ?> </strong></td>
						<td nowrap="nowrap"><a href="http://www.rsjoomla.com" target="_blank">www.rsjoomla.com</a></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>