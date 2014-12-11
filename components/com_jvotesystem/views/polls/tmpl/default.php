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

$bold = "<strong>%s</strong>";
$category =& VBCategory::getInstance();

VBGeneral::getInstance()->charset('utf-8');
?>
	
<table class="list filters"><tbody>
	<?php if($this->params->get("showOrderBar", 1) || $this->params->get("showTimeBar", 1)) {?>
	<tr>
		<td>
			<?php if($this->params->get("showOrderBar", 1)) {?>
			<div style="float:left;">
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("order"=>"popular")));?>"><?php echo sprintf(($this->filter["order"] == "popular") ? $bold : "%s", JText::_("Popular"));?></a>
				<strong> | </strong>
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("order"=>"recent")));?>"><?php echo sprintf(($this->filter["order"] == "recent") ? $bold : "%s", JText::_("Most_Recent"));?></a>
				<strong> | </strong>
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("order"=>"most-voted")));?>"><?php echo sprintf(($this->filter["order"] == "most-voted") ? $bold : "%s", JText::_("Most_Voted"));?></a>
				<strong> | </strong>
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("order"=>"most-discussed")));?>"><?php echo sprintf(($this->filter["order"] == "most-discussed") ? $bold : "%s", JText::_("Most_Discussed"));?></a>
			</div>
			<?php } if($this->params->get("showTimeBar", 1)) {?>
			<div style="float:right;">
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("time"=>"today")));?>"><?php echo sprintf(($this->filter["time"] == "today") ? $bold : "%s", JText::_("Today"));?></a>
				<strong> | </strong>
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("time"=>"week")));?>"><?php echo sprintf(($this->filter["time"] == "week") ? $bold : "%s", JText::_("This_week"));?></a>
				<strong> | </strong>
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("time"=>"month")));?>"><?php echo sprintf(($this->filter["time"] == "month") ? $bold : "%s", JText::_("This_month"));?></a>
				<strong> | </strong>
				<a href="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("time"=>"all-time")));?>"><?php echo sprintf(($this->filter["time"] == "all-time") ? $bold : "%s", JText::_("All_time"));?></a>
			</div>
			<?php }?>
		</td>
	</tr>
	<?php } if($this->params->get("showSearchField", 1) || $this->params->get("showCategoryDropdown", 1)) {?>
	<tr>
		<td><?php if($this->params->get("showSearchField", 1)) {?>
			<form action="<?php $u =& JURI::getInstance(); echo $u->toString();?>" method="post" name="jvsListFilter">
				<div style="float:left;">
					<input type="text" name="keyword" id="keyword" size="32" maxlength="250" value="<?php echo $this->filter["keyword"];?>" />
					<input type="submit" value="<?php echo JText::_("Find");?>" />
					<input type="button" value="<?php echo JText::_("Reset");?>" onclick="JMQuery(this).parent().find('#keyword').val(''); JMQuery(this).closest('form').submit();" />
				</div>
				<input type="hidden" name="order" value="<?php echo $this->filter["order"];?>" />
				<input type="hidden" name="time" value="<?php echo $this->filter["time"];?>" />
			</form>
			<?php } if($this->params->get("showCategoryDropdown", 1)) {?>
				<div style="float:right;">
					<?php echo JText::_("Category");?>: 
					<select name="cat" onchange="window.location.href = (this.value);">
						<option value="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("cat"=> "all" )));?>"><?php echo JText::_("All");?></option>
					<?php foreach($this->cats AS $cat) {?>
						<option<?php if($cat->id == $this->filter["cid"]) {?> selected="selected"<?php }?> value="<?php echo $this->general->buildLink("list", null, "", array_merge($this->filter, array("cat"=> $cat->alias )));?>"><?php for($i = 0; $i <= $cat->level; $i++) echo " - "; echo JText::_($cat->title);?></option>
					<?php }?>
					</select>
				</div>
			<?php }?>
		</td>
	</tr>
	<?php }?>
</tbody></table>

<?php echo $this->show("navi");?>	

<?php echo $this->show($this->layout);?>

<?php echo $this->show("navi");?>
	
<?php VBGeneral::getInstance()->charset('plain'); ?>

