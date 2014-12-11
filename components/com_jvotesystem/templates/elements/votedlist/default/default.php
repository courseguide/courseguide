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
<ul class="userlist jvsclearfix jvotesystem">
	<li class="number">
		<span class="votecount"><?php echo $par->count ?></span>
		<span class="votecounttext"><?php echo JText::_('Votes') ?></span>
	</li>
	<li class="profile_img">
		<?php echo $par->avatar;?>
	</li>	
	<li class="detail">
		<span class="user"><?php echo $par->user ?></span>
		<br />
		<span class="time"><?php echo $par->date ?></span>
	</li>
</ul>