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

$this->vbparams->set("global", "activate_bbcode", 0);
$this->vbparams->set("global", "general_published_bbcode", 0);

$category =& VBCategory::getInstance();
?>
	<table class="list polls">
		<thead>
			<tr>
				<th style="text-align:left;"><?php echo JText::_("Title")?></th>
				<th style="text-align:left;"><?php echo JText::_("Question")?></th>
				<th><?php echo JText::_("Category")?></th>
				<th><?php echo JText::_("Author")?></th>
				<th><?php echo JText::_("Votes")?></th>
				<th><?php echo JText::_("Comments")?></th>
				<th><?php echo JText::_("Created")?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($this->polls AS $i => $poll) {
			$cat = $category->getCategory($poll->catid); 
			$author = $this->user->getUserData($poll->autor_id);
			?>
			<tr class="<?php if($poll->published == 0) {?>disabled<?php }?>">
				<td style="text-align:left;"><a href="<?php echo $this->general->buildLink("poll", $poll->id);?>"><?php echo $poll->title;?></a></td>
				<td style="text-align:left;font-weight: bold;"><a href="<?php echo $this->general->buildLink("poll", $poll->id);?>"><?php echo $this->general->shortText($poll->question, 60, false);?></a></td>
				<td><a class="category" href="<?php echo $this->general->buildLink("category", $cat->id);?>" data-cid="<?php echo $cat->id;?>"><?php echo $cat->title;?></a></td>
				<td nowrap="nowrap"><a class="author" href="<?php echo $this->general->buildLink("user", $poll->autor_id);?>" data-u="<?php echo $poll->autor_id;?>"><?php echo $author->name;?></a></td>
				<td style="font-weight: bold;"><?php echo $poll->votes;?></td>
				<td><?php echo $poll->comments;?></td>
				<td nowrap="nowrap"><?php echo $this->general->convertTime($poll->created);?>
			</tr>
		<?php } if(empty($this->polls)) { ?>
			<tr>
				<td colspan="7"><i><?php echo JText::_("NO_POLLS_FOUND");?></i></td>
			</tr>
		<?php }?>
		</tbody>
	</table>
