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

if($par->type == "answers") {

$access =& VBAccess::getInstance();
$vote =& VBVote::getInstance();

$box =& $vote->getBox($par->id);
$votes = $vote->getVotesByUser($box->id);
?>
<div class="navi <?php echo $par->type ?>navi">
	<div class="naviVor">
	<?php if (isset($par->prev)) echo $par->prev ?>
	</div>
	<div class="naviWeiter">
	<?php if (isset($par->next)) echo $par->next ?>
	</div>
	<?php if ($par->type === 'answers' && $access->isUserAllowedToViewResult($box, $votes) && $this->vbparams->get('diagramm')) { ?>
		<?php if($box->chart_type == "bar" || $box->chart_type == "both") {?>
			<div class="naviPages"><a class="barchart inactive"><?php echo JText::_('CHARTS') ?></a></div>
		<?php } else { ?>	
			<div class="naviPages"><a class="piechart inactive"><?php echo JText::_('CHARTS') ?></a></div>
		<?php } ?>
	<?php } ?>
</div>

<?php } else {?>

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

<?php }?>