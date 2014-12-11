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

$curPage = $this->filter["page"];
$pollsPerPage = $this->vbparams->get("pollsPerPage");

$pages = ceil($this->count/$pollsPerPage);
if($pages <= 1) return;
?>
<div class="list_navi">
	<?php if($curPage > 1) {?>
	<a class="goLeft" href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("page"=>$curPage-1)));?>">
		<?php echo JText::_("Vor");?>
	</a>
	<?php }?>
	<?php for($i = 1; $i <= $pages; $i ++) {?>
	<a<?php if($curPage == $i) {?> class="curPage"<?php }?> href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("page"=>$i)));?>">&nbsp;<?php echo $i;?>&nbsp;</a>
	<?php }?>
	<?php if(($this->count - $curPage*$pollsPerPage) > 0) {?>
	<a class="goRight" href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("page"=>$curPage+1)));?>">
		<?php echo JText::_("Weiter");?>
	</a>
	<?php }?>
</div>