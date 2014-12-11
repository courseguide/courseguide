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

?>
<div class="topbox">
<?php //<!--Menubar-->?>
	<div class="topbar">
<?php //<!--NoViewRights-->?>
<?php if($par->norights) { ?>
		<img style="border: 0 none;" src="<?php echo $this->template->getImageSrc("icons", "icon-48-deny.png");?>" class="icon" title="<?php echo JText::_("NOTALLOWEDTOVIEWPOLL");?>">
<?php } ?>
	</div>
<?php //<!--Title & Question-->?>
	<div class="title"><?php echo $par->title;?></div>
	<p class="question"><?php echo $par->question;?></p>
</div>