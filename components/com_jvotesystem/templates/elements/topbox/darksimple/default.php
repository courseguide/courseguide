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
<div class="topbox">
<?php if($par->chart_show) { ?>
	<?php if($par->chart_show_bar) { ?><a class="chart barchart inactive"<?php if(!$par->chart_visible) {?> style="display:none;"<?php }?>></a><?php }?>
	<?php if($par->chart_show_pie) { ?><a class="chart piechart inactive"<?php if(!$par->chart_visible) {?> style="display:none;"<?php }?>></a><?php }?>
<?php } ?>
	<?php if($par->votes_left_show) { ?>
	<div class="votes_left">
		<span class="count"><?php echo $par->votes_left;?></span> <?php echo JText::_('VOTES_LEFT');?>
	</div>
	<?php } ?>
<?php //<!--Menubar-->?>
	<div class="topbar">
<?php //<!--NoViewRights-->?>
<?php if($par->norights) { ?>
		<img style="border: 0 none;" src="<?php echo $this->template->getImageSrc("icons", "icon-48-deny.png");?>" class="icon" title="<?php echo JText::_("NOTALLOWEDTOVIEWPOLL");?>">
<?php } ?>
	</div>
<?php //<!--Like-Button-->?>
<?php /*if($par->like_show) { ?>
	<iframe src="http://www.facebook.com/plugins/like.php?app_id=226274454067878&amp;href=<?php echo urlencode($par->like_url);?>&amp;send=false&amp;layout=button_count&amp;width=80&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=90" scrolling="no" frameborder="0" style="background: none repeat scroll 0 0 transparent; border: medium none; overflow: hidden; height: 25px; float: left; width: 80px; margin: 5px 5px 0pt -5px;" allowTransparency="true"></iframe>
<?php } */?>
<?php //<!--Title & Question-->?>
	<div class="title"><?php echo $par->title;?></div>
	<p class="question"><?php echo $par->question;?></p>
</div>