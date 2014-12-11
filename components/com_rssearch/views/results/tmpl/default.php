<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die('Restricted access'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rssearch&view=results&module_id='.(int) $this->module_id.'&search='.$this->escape($this->search)); ?>" method="post" accept-charset="utf-8">
	<h1><?php echo JText::sprintf('COM_RSSEARCH_RESULTS_FOR', $this->search); ?></h1>

	<?php if (count($this->results) != 0) { ?>
	<?php foreach ($this->results as $result) { ?>
		<div class="rssearch_row">
			
			<?php if ($this->type && isset($result->type)) { ?>
			<div class="rssearch_type">
				(<?php echo $result->type; ?>)
			</div>
			<?php } ?>
			
			<h2>
				<a href="<?php echo $result->link; ?>">
					<?php echo $this->escape($result->title);?>
				</a>
			</h2>
			
			<div class="rssearch_content">
				<?php echo $this->HighlightSearch($this->search, $this->cutText($result->text, $this->nr_words)); ?>
			</div>
			
			<div class="clearfix">&nbsp;</div>
			<a class="rssearch_readon" href="<?php echo $result->link; ?>">
				<?php echo JText::_('COM_RSSEARCH_READ_MORE'); ?>
			</a>
		</div>
		<div class="clearfix">&nbsp;</div>
	<?php } ?>

		<div class="pagination">
			<p class="counter pull-right">
				<?php echo $this->pagination->getPagesCounter(); ?>
				<?php echo $this->pagination->getLimitBox(); ?>
			</p>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php } else { ?>
		<p><?php echo JText::_('COM_RSSEARCH_NO_RESULTS');?></p>
	<?php } ?>
</form>