<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

?>
<form action="index.php" method="post" name="adminForm">
<fieldset id="filter-bar">
	<div class="filter-search fltlft">
		<label for="filter_search" class="filter-search-lbl"><?php echo JText::_("JVS_NAVI_Filter")?>: </label>
		<input type="text" value="<?php echo $this->filter->search;?>" id="filter_search" name="filter_search">
		<button type="submit"><?php echo JText::_("JVS_NAVI_Search")?></button>
		<button onclick="document.id('filter_search').value='';this.form.submit();" type="button"><?php echo JText::_("JVS_NAVI_Reset")?></button>
	</div>	
	<div class="filter-select fltrt">
		<select onchange="submitform();" name="filter_cid">
			<option <?php if($this->filter->cid == '') { ?> selected="selected" <?php } ?> value="">
				- <?php echo JText::_('JVS_NAVI_SELECT_CATEGORY');?> - 
			</option>
			<?php foreach($this->filter->categories AS $section) { ?>
			<option <?php if($section->id == $this->filter->cid) { ?> selected="selected" <?php } ?> value="<?php echo $section->id;?>">
				<?php 
				for($i = 1; $i <= $section->level; $i++) echo " - ";
				echo $section->title;
				?>
			</option>
			<?php } ?>
		</select> 	
		<select onchange="submitform();" name="filter_bid">
			<option <?php if($this->filter->bid == '') { ?> selected="selected" <?php } ?> value="">
				- <?php echo JText::_('JVS_NAVI_SELECT_POLL');?> -
			</option>
			<?php foreach($this->filter->boxen AS $box) { ?>
			<option <?php if($box->id == $this->filter->bid) { ?> selected="selected" <?php } ?> value="<?php echo $box->id;?>">
				<?php echo $box->title;?>				
			</option>
			<?php } ?>
		</select>			
	</div>	
</fieldset>

<div id="editcell">
	<table class="adminlist" id="jvotesystem">
	<thead>
		<tr>		
			<th width="5">
				<?php echo JText::_( 'ID' ); ?>
			</th>
			<?php if($this->filter->cid == '') {?>			
			<th width="5">
				<?php echo JText::_( 'Category' ); ?>
			</th>
			<?php } if($this->filter->bid == '') {?>	
			<th width="5">
				<?php echo JText::_( 'Poll' ); ?>
			</th>
			<?php }?>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th>
				<?php echo JText::_( 'Answer' ); ?>
			</th>
			<th width="100">
				<?php echo JText::_( 'Autor' ); ?>
			</th>
			<th width="100">
				<?php echo JText::_( 'Created' ); ?>
			</th>
			<th width="100">
				<?php echo JText::_( 'First_Vote' ); ?>
			</th>
			<th width="100">
				<?php echo JText::_( 'Last_Vote' ); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
			<th width="1%">
				<?php echo JText::_( 'Votes' ); ?>
			</th>
			<th width="1%">
				<?php echo JText::_( 'Comments' ); ?>
			</th>
		</tr>			
	</thead>
	<tfoot>
    <tr>
      <td colspan="12">
      	<?php echo $this->pagination->getListFooter(); ?>
      </td>
    </tr>
  </tfoot>
  <tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];
		$checked 	= JHTML::_('grid.id',   $i, $row->id );
		$link 		= $this->general->buildAdminLink("answer", $row->id);
		
		$published 	= JHTML::_('grid.published', $row, $i );
		
		$catids = JArrayHelper::getColumn($this->filter->categories, "id");
		$pos = array_search($row->catid, $catids);
		
		$cat = $this->filter->categories[$pos];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<?php if($this->filter->cid == '') {?>
			<td>
				<a href="index.php?option=com_jvotesystem&view=category&id=<?php echo $cat->id;?>" data-cid="<?php echo $cat->id;?>">
					<?php echo $cat->title; ?>
				</a>
			</td>
			<?php } if($this->filter->bid == '') {?>
			<td>
				<?php echo $this->general->buildAdminLink("poll", $row->pid, $row->poll); ?>
			</td>
			<?php }?>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a href="<?php echo $link; ?>"><?php echo $this->general->shortText($row->answer, 100, false); ?></a>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertUser($row->autor_id); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertTime($row->created); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertTime($row->firstvote); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertTime($row->lastvote); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $published; ?>
			</td>
			<td style="text-align:right;">
				<b><?php echo $row->votes; ?></b>
			</td>
			<td style="text-align:center;">
				<a href="index.php?option=com_jvotesystem&view=comments&filter_aid=<?php echo $row->id;?>">
				<b><?php echo $row->comments; ?></b>
				<img src="components/com_jvotesystem/assets/images/icon-16-forward.png" />
				</a>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_jvotesystem" />
<input type="hidden" name="view" value="answers" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="answers" />
</form>
<?php $this->general->getAdminFooter(); ?>