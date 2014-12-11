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
<div style="padding-left:20px;">
	<p>
		<span style="color:#3c452d;font:bold 1em Verdana, Arial, Sans-Serif"><?php echo $par->user;?></span>
		&mdash; 
		<span style="font-size:11px;color:#999"><?php echo $par->date;?></span>
	</p>
	<div style="border:1px solid #ccc;padding:10px 5px;margin:5px 0;font:normal 1em Verdana, Arial, Sans-Serif">
		<?php echo $par->comment;?>
	</div>
</div>