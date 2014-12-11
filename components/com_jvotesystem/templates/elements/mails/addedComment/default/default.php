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

<p> <?php echo JText::_('Poll');?>: <a href="<?php echo $par->title_link;?>" target="_blank"><?php echo $par->title; ?></a> &mdash; <?php echo JText::_('Answer');?>: </p>
<div style="border:1px solid #ccc;padding:10px 5px;margin:5px 0;font:normal 1em Verdana, Arial, Sans-Serif">
	<?php echo $par->answer;?>
</div>