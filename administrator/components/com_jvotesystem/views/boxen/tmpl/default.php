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
<script type="text/javascript">
<?php if(version_compare( JVERSION, '1.6.0', 'lt' )) { ?>
function submitbutton(task) {
<?php } else { ?>
Joomla.submitbutton = function(task) {
<?php } ?>
	var form = document.adminForm;
	if (task == "add") { 
		loadAssistant(this, "<?php echo JUri::root(true);?>", "poll", "&catid=" + JMQuery("#filter_cid").val());
	} else if(task == "edit") {
		var count = 0;
		JMQuery("input[type=checkbox][name^='cid']").each(
			function() {
				if( JMQuery(this).attr('checked')){
					if(count != 0) return ;
					count++;
					loadAssistant(this, "<?php echo JUri::root(true);?>", "poll", "&id=" + JMQuery(this).val());
				}
			}
		);
	} else if(task == "remove") {
		JMQuery.Zebra_Dialog('<?php echo JText::_("QUESTIONREMOVEPOLL");?>', {
			'type':     'question',
			'title':    '<?php echo JText::_("ARE_YOU_SURE");?>',
			'buttons':	[ '<?php echo JText::_("JYes");?>', '<?php echo JText::_("JNo");?>' ],
			'onClose': function(button) {
				if(button == '<?php echo JText::_("JYes");?>') {
					submitform( task );
				}
			}
		});
	} else if(task == "resetVotes") {
		JMQuery.Zebra_Dialog('<?php echo JText::_("JVS_QUESTION_RESET_VOTES");?>', {
			'type':     'question',
			'title':    '<?php echo JText::_("ARE_YOU_SURE");?>',
			'buttons':	[ '<?php echo JText::_("JYes");?>', '<?php echo JText::_("JNo");?>' ],
			'onClose': function(button) {
				if(button == '<?php echo JText::_("JYes");?>') {
					submitform( task );
				}
			}
		});
	} else if(task == "showPreview") {
		var count = 0;
		JMQuery("input[type=checkbox][name^='cid']").each(function() {
			if( JMQuery(this).attr('checked')){ if(count != 0) return ; count++;
				jVS.loadSqueezebox(false, '<?php echo $this->lib->root();?>/index.php?option=com_jvotesystem&tmpl=component&view=poll&id=' + JMQuery(this).val(), 800, 600, true);
			}
		});
	} else if(task == "exportData") {
		JMQuery.Zebra_Dialog('<?php echo JText::_("JVS_QUESTION_EXPORT_FORMAT");?>', {
			'type':     'question',
			'buttons':	[ '.xls', '.csv' ],
			'onClose': function(format) { 
				if(format.type != undefined) return;
				document.adminForm.exportFormat.value = format;
				submitform( task );
				document.adminForm.task.value = "";
			}
		});
	} else {
		submitform( task );
	}
} 
</script>
							
<form action="index.php" method="post" name="adminForm">
<fieldset id="filter-bar">
	<div class="filter-search fltlft">
		<label for="filter_search" class="filter-search-lbl"><?php echo JText::_("JVS_NAVI_Filter")?>: </label>
		<input type="text" value="<?php echo $this->filter->search;?>" id="filter_search" name="filter_search">
		<button type="submit"><?php echo JText::_("JVS_NAVI_Search")?></button>
		<button onclick="document.id('filter_search').value='';this.form.submit();" type="button"><?php echo JText::_("JVS_NAVI_Reset")?></button>
	</div>	
	<div class="filter-select fltrt">
		<select onchange="submitform();" name="filter_cid" id="filter_cid">
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
			<?php }?>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>			
			<th>
				<?php echo JText::_( 'Title' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Question' ); ?>
			</th>
			<th width="100">
				<?php echo JText::_( 'Autor' ); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
			<th width="1%"><?php echo JHTML::_('grid.order', $this->items, 'filesave.png', 'saveorderboxen' ); ?></th>
			<th width="1%">
				<?php echo JText::_( 'Answers' ); ?>
			</th>
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
      <td colspan="<?php echo 11 - (($this->filter->cid == '') ? 0 : 1);?>">
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
		$link 		= JUri::root(true) . '/components/com_jvotesystem/assistant/index.php?interface=administrator&view=poll&id='. $row->id ;
		
		$params = array();
		$params["id"] = $row->id;
		$onclick 	= "loadAssistant(this, '".JUri::root(true)."', 'poll', '&id=".$row->id."'); return false;";
		
		$published 	= JHTML::_('grid.published', $row, $i );
		
		$catlink 	= "index.php?option=com_jvotesystem&view=category&id=".$row->catid;
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<?php if($this->filter->cid == '') {?>
			<td>
				<a href="<?php echo $catlink; ?>" data-cid="<?php echo $row->catid;?>">
					<?php echo $row->cattitle; ?>
				</a>
			</td>
			<?php }?>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<a onclick="<?php echo $onclick; ?>" href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
			</td>
			<td>
				<a onclick="<?php echo $onclick; ?>" href="<?php echo $link; ?>"><?php echo $row->question; ?></a>
			</td>
			<td style="text-align:center;">
				<?php echo $this->general->convertUser($row->autor_id); ?>
			</td>
			<td style="text-align:center;">
				<?php echo $published; ?>
			</td>
			<td class="order">
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td style="text-align:center;">
				<a href="index.php?option=com_jvotesystem&view=answers&filter_bid=<?php echo $row->id;?>" style="text-decoration: none ! important;">
					<b><?php echo $row->answers; ?></b>
					<img src="components/com_jvotesystem/assets/images/icon-16-forward.png" />
				</a>
			</td>
			<td style="text-align:right;">
				<b><?php echo $row->votes; ?></b>
			</td>
			<td style="text-align:right;">
				<b><?php echo $row->comments; ?></b>
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
<input type="hidden" name="view" value="boxen" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="boxen" />
<input type="hidden" name="exportFormat" value=".xls" />
</form>
<?php $this->general->getAdminFooter(); ?>