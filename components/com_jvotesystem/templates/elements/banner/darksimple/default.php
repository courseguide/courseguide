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
<div class="answer jvsbanner">
<?php if($par->activate_ranking) { ?>
	<div class="rank" style="visibility: hidden;">?</div>
<?php } ?>
	<div class="answerbox">
		<div class="bannercode"><?php echo $par->script;?></div>
	</div>
	<div class="count">
		<span class="votecount"><?php echo JText::_("Werbung");?></span>
	</div>
</div>
