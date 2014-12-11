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
<div class="header icon-48-boxen">
	<?php echo JText::_('Polls');?>: 
	<span class="title">jVoteSystem</span>
	
	<div id="toolbar" class="toolbar-list">
		<ul>
			<li id="toolbar-cancel" class="button">
				<a class="toolbar" onclick="parent.SqueezeBox.close();" href="#">
					<span class="icon-32-cancel"> </span> <?php echo JText::_("Close");?>
				</a>
			</li>
		</ul>
	</div>
</div>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'ID' ); ?>
			</th>	
			<th width="5">
				<?php echo JText::_( 'Category' ); ?>
			</th>			
			<th>
				<?php echo JText::_( 'Title' ); ?>
			</th>
			<th>
				<?php echo JText::_( 'Question' ); ?>
			</th>
		</tr>			
	</thead>
	<tfoot>
    <tr>
      <td colspan="6">
      	<?php echo $vote->getFooter();?>
      </td>
    </tr>
  </tfoot>
  <tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $polls ); $i < $n; $i++)
	{
		$row = &$polls[$i];
		$java = 'parent.insertjVoteSystemPoll('.$row->id.', \''.str_replace('"', "",str_replace("'", "",$row->title)).'\'); return false;';
		
		$img 	= $row->published ? 'tick.png' : 'publish_x.png';
		$alt 	= $row->published ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
		$published = '<img src="images/'. $img .'" border="0" alt="'. $alt .'" />';
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $row->id; ?>
			</td>
			<td>
				<?php echo VBCategory::getInstance()->getCategory($row->catid)->title; ?>
			</td>
			<td>
				<a href="#" onclick="<?php echo $java; ?>"><?php echo $row->title; ?></a>
			</td>
			<td>
				<a href="#" onclick="<?php echo $java; ?>"><?php echo $row->question; ?></a>
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
<input type="hidden" name="view" value="boxenlist" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="boxen" />
</form>