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
<div class="navi answersnavi">
	<div class="naviVor">
		<a class="scaling"><?php echo $par->translation_scaling;?></a>
	</div>
	<div class="naviWeiter">
		<a class="answerlist"><?php echo JText::_("LISTS");?></a>
	</div>
	<?php if($par->show_next) {?>
	<div class="naviPages">
		<a class="showall" data-short="true"><?php echo JText::_("LOAD_NEXT_SHORT");?></a>
	</div>
	<?php }?>
</div>