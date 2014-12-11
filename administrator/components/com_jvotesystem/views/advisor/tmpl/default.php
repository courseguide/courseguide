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
// var_dump($this->advise_php);
// var_dump($this->advise_cookie);
// var_dump($this->advise_livesite);
// var_dump($this->advise_joomla);
// var_dump($this->advise_sh404);

?>
<div class="advisor">
	<h2><?php echo JText::_('JVS_ADV_HEAD');?></h2>

	<div <?php if (!$this->advise_php->check) { echo 'class="fatal"';}?>>
		<h2><?php echo JText::_('JVS_ADV_PHP_TTL');?></h2>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_PHP_DESC');?></div>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_PHP_A');?> <?php echo $this->advise_php->version; ?></div>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_PHP_B');?> <?php echo $this->advise_php->required; ?></div>
	</div>

	<div <?php if (!$this->advise_joomla->check) { echo 'class="fatal"';} if ($this->advise_joomla->check === 1) { echo 'class="critic"';}?>>
		<h2><?php echo JText::_('JVS_ADV_JOOMLA_TTL');?></h2>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_JOOMLA_DESC');?></div>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_JOOMLA_A');?> <?php echo $this->advise_joomla->version; ?></div>
		<?php if ($this->advise_joomla->check === 1) { ?>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_JOOMLA_B');?></div>
		<?php } else if ($this->advise_joomla->check === 1) { ?>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_JOOMLA_C');?></div>
		<?php } ?>
	</div>

	<div <?php if (!$this->advise_cookie->check) { echo 'class="fatal"';}?>>
		<h2><?php echo JText::_('JVS_ADV_COOKIE_TTL');?></h2>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_COOKIE_DESC');?></div>
		<?php if (!$this->advise_cookie->check || $this->advise_cookie->juri_root !== '') { ?>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_COOKIE_A');?> <?php echo $this->advise_cookie->cookie_path; ?></div>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_COOKIE_B');?> <?php echo $this->advise_cookie->juri_root; ?></div>
		<?php } ?>
		<?php if (!$this->advise_cookie->check) { ?>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_COOKIE_C');?></div>
		<?php } ?>
	</div>
	
	<div <?php if (!$this->advise_livesite->check) { echo 'class="critic"';}?>>
		<h2><?php echo JText::_('JVS_ADV_LIVESITE_TTL');?></h2>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_LIVESITE_DESC');?></div>
		<?php if (!$this->advise_livesite->check) { ?>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_LIVESITE_A');?> <?php echo $this->advise_livesite->livesite; ?></div>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_LIVESITE_B');?></div>
		<?php } ?>
	</div>
	
	<div <?php if (!$this->advise_sh404->check) { echo 'class="critic"';}?>>
		<h2><?php echo JText::_('JVS_ADV_SHAOA_TTL');?></h2>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_SHAOA_DESC');?></div>
		<?php if (!$this->advise_sh404->check) { ?>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_SHAOA_A');?></div>
		<?php } else {?>
		<div class="advisortext"><?php echo JText::_('JVS_ADV_SHAOA_B');?></div>
		<?php } ?>
	</div>
</div>
<?php $this->general->getAdminFooter(); ?>