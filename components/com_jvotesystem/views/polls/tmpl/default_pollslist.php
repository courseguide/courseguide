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
<?php foreach($this->polls AS $i => $poll) {?>
	<div class="poll-element">
	<?php echo $this->vote->getVoteBox($poll->id, false, null, true); ?>
	</div>
<?php }?>
