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

$general = VBGeneral::getInstance();
?>
<p>
	<b><?php echo JText::_("JVS_MAIL_QUICK_MODERATION");?>:</b> 
	<?php foreach($par AS $i => $quick) {
		if($i != 0) echo " | ";
		echo $general->buildHtmlLink($quick["link"], $quick["title"]);
	}?>
</p>