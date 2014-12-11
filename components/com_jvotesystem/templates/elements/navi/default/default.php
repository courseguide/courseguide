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
<div class="navi <?php echo $par->type ?>navi">
	<div class="naviVor">
	<?php if (isset($par->prev)) echo $par->prev ?>
	</div>
	<div class="naviWeiter">
	<?php if (isset($par->next)) echo $par->next ?>
	</div>
	<div class="naviPages">
	<?php echo $par->main ?>
	</div>
</div>