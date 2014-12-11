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
<div class="answer" data-a="<?php echo $par->aid; ?>">
	<?php if($par->vote_state == null AND $par->votes_left > 0) {?>
		<a href="#" class="votingbutton <?php echo $par->votebutton_class;?>"<?php echo $par->votebutton_disabled ?>>
	<?php }?>
			<span class="answertext" data-jvs_tooltip="<?php echo urlencode($par->votebutton_tooltip);?>"><?php echo $par->answer;?></span>
	<?php if($par->vote_state == null AND $par->votes_left > 0) {?>
		</a>
	<?php }?>	
</div>
