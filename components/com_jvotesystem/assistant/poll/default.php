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
<div class="header icon-48-<?php echo ($type != "defaultSettings") ? (($new) ? "box-add" : "boxen") : "options" ;?>">
	<div id="toolbar" class="toolbar-list">
		<ul>
			<li id="toolbar-save" class="button">
				<a class="toolbar" onclick="actionAssistant('save', '<?php echo $this->lib->root() . '/components/com_jvotesystem/assistant';?>');JMQuery('#toolbar-back').fadeIn();" href="#">
					<span class="icon-32-save"> </span> <?php echo JText::_("Save");?>
				</a>
			</li>
			<?php if($origin === "button") { ?>
			<li id="toolbar-back" class="button" style="display:none;">
				<a class="toolbar" href="components/com_jvotesystem/assistant/index.php?view=button&interface=<?php echo $interface; ?>">
					<span class="icon-32-back"> </span> <?php echo JText::_("BACK");?>
				</a>
			</li>
			<?php } else { ?>
			<li id="toolbar-cancel" class="button">
				<a class="toolbar" onclick="<?php if($type != "defaultSettings") {?>if(needReload) parent.window.location.reload(); else <?php }?>parent.SqueezeBox.close();" href="#">
					<span class="icon-32-cancel"> </span> <?php echo JText::_("Close");?>
				</a>
			</li>
			<?php }?>
		</ul>
	</div>
	<?php echo ($type != "defaultSettings") ? JText::_('Poll') : JText::_("defaultSettings");?>: 
	<span class="title"><?php echo ($type != "defaultSettings") ? $item->title : $category->title;?></span>
	<?php if($type != "defaultSettings") {?><span class="task">[<?php echo ($new) ? JText::_("New") : JText::_("Edit") ;?>]</span><?php }?>
</div>
<div class="assistant-message" id="message">
	
</div>
<div class="assistant-loading" id="loading">

</div>
<script>
	JMQuery(function() {
		JMQuery( "#tabs" ).tabs();
		<?php $pages = ($interface == "administrator") ? 6 : count($category->allowed_tabs) - 1 + ((!in_array("result", $category->allowed_tabs)) ? 1 : 0) + ((!in_array("votes", $category->allowed_tabs)) ? 1 : 0);
		if(($new or empty($answers)) AND $type != "defaultSettings" && (in_array("result", $category->allowed_tabs) || in_array("votes", $category->allowed_tabs))) { ?>JMQuery('#tabs').tabs("option","disabled", [<?php echo $pages;?>, <?php echo $pages + 1;?>]); <?php }?>
	});
</script>
<div id="tabs">
	<ul>
		<?php if($type != "defaultSettings") {?><li><a href="#tab-general"><?php echo JText::_('General'); ?></a></li><?php }?>
		<?php if(in_array("settings", $category->allowed_tabs) || $interface == "administrator") {?><li><a href="#tab-settings"><?php echo JText::_('Einstellungen'); ?></a></li><?php }?>
		<?php if(in_array("display", $category->allowed_tabs) || $interface == "administrator") {?><li><a href="#tab-display"><?php echo JText::_('Display'); ?></a></li><?php }?>
		<?php if(in_array("email_spam", $category->allowed_tabs) || $interface == "administrator") {?><li><a href="#tab-email-spam">eMail & Spam</a></li><?php }?>
		<?php if(in_array("access", $category->allowed_tabs) || $interface == "administrator") {?><li><a href="#tab-access"><?php echo JText::_('Access'); ?></a></li><?php }?>
		<?php if($interface == "administrator") {?><li><a href="#tab-template"><?php echo JText::_('Template'); ?></a></li><?php }?>
		<?php if($type != "defaultSettings") {?>
			<?php if(in_array("result", $category->allowed_tabs) || $interface == "administrator") {?><li><a href="#tab-result"><?php echo JText::_('Result'); ?></a></li><?php }?>
			<?php if(in_array("votes", $category->allowed_tabs) || $interface == "administrator") {?><li><a href="#tab-votes"><?php echo JText::_('Votes'); ?></a></li><?php }?>
		<?php }?>
	</ul>
<form action="#" method="post" name="adminForm" id="adminForm">
<?php if($type != "defaultSettings") {?>
	<div id="tab-general">
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Details'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="text small required">
						<td class="param">
							<?php echo JText::_('Title'); ?>:
						</td>
						<td class="field">
							<input type="text" name="title" id="title" size="32" maxlength="250" value="<?php echo $item->title;?>" />
						</td>
					</tr>
					<?php if($interface == "administrator") {?>
					<tr class="text small">
						<td class="param">
							<?php echo JText::_('Alias'); ?>:
						</td>
						<td class="field">
							<input type="text" name="alias" id="alias" size="32" maxlength="250" value="<?php echo $item->alias;?>" />
						</td>
					</tr>
					<?php }?>
					<tr class="area small required">
						<td class="param">
							<?php echo JText::_('Question'); ?>:
						</td>
						<td class="field">
							<?php echo $this->general->getBBCodeToolbar2(true); ?>
							<textarea type="text" name="question" id="question" rows="4" class="large" cols="50"><?php echo $item->question;?></textarea>
						</td>
					</tr>
					<tr class="select small required">
						<td class="param">
							<?php echo JText::_('Category'); ?>:
						</td>
						<td class="field">
							<select name="catid" id="catid">
							<?php foreach($lists["categories"] AS $list) {?>
								<option value="<?php echo $list->id;?>"<?php if($item->catid == $list->id) {?> selected="selected"<?php }?><?php if(!$access->add($list)) {?> disabled="disabled"<?php }?>><?php for($u = 0; $u < $list->level; $u++) echo "|&mdash; "; echo $list->title;?></option>
							<?php }?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Answers'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params answers">
					<tr>
						<th><?php echo JText::_('JVS_Color');?></th>
						<th><?php echo JText::_('Answer');?></th>
						<th><?php echo JText::_('State');?></th>
					</tr>
					<?php if($answers) foreach($answers AS $answer) { ?>
					<tr class="area answer">
						<td class="field color" width="1%">
							<?php echo $this->lib->special("colorInput", array( "hidden" => true, "value" => $answer->color, "name" => "a_color[]" ));?>
						</td>
						<td class="field answer">
							<?php echo $this->general->getBBCodeToolbar2(true); ?>
							<textarea type="text" name="answers[]" id="answer<?php echo $answer->id;?>" rows="1" cols="50"><?php echo $answer->answer;?></textarea>
							<input type="hidden" name="a_id[]" value="<?php echo $answer->id;?>" />
						</td>
						<td class="field state" width="1%">
							<input type="hidden" name="a_state[]" id="a<?php echo $answer->id;?>_state" value="<?php echo $answer->published;?>" />
							<?php $class = $answer->published ? 'published' : 'unpublished';?>
							<a href="#" onclick="return false;" class="state-<?php echo $class;?>"> 
							</a>
						</td>
					</tr>
					<?php } ?>
					<tr class="area answer new template">
						<td class="field color" width="1%">
							<?php echo $this->lib->special("colorInput", array( "hidden" => true, "name" => "a_color[]", "active" => false ));?>
						</td>
						<td class="field answer">
							<?php echo $this->general->getBBCodeToolbar2(true); ?>
							<textarea type="text" name="answers[]" id="newanswer" rows="1" cols="50"><?php echo JText::_('ADD_NEW_ANSWER');?></textarea>
							<input type="hidden" name="a_id[]" value="-1" />
						</td>
						<td class="field state" width="1%">
							<input type="hidden" name="a_state[]" id="anew_state" value="1" />
							<a href="#" onclick="return false;" class="state-published"> 
							</a>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
	</div>
<?php } else {?>
	<input type="hidden" name="catid" value="<?php echo $item->catid;?>" />
<?php } if(in_array("settings", $category->allowed_tabs) || $interface == "administrator") {?>
	<div id="tab-settings">
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Votes'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="number extralarge">
						<td class="param">
							<?php echo JText::_('NUMBER_OF_POSSIBLE_VOTES'); ?>:
						</td>
						<td class="field">
							<input type="text" name="allowed_votes" id="allowed_votes" size="5" maxlength="3" value="<?php echo $item->allowed_votes;?>" />
						</td>
					</tr>
					<tr class="number extralarge">
						<td class="param">
							<?php echo JText::_('MAX_VOTES_ON_ANSWER'); ?>:
						</td>
						<td class="field">
							<input type="text" name="max_votes_on_answer" id="max_votes_on_answer" size="5" maxlength="3" value="<?php echo $item->max_votes_on_answer;?>" />
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Time'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="date medium">
						<td class="param">
							<?php echo JText::_('Startdatum'); ?>:
						</td>
						<td class="field">
							 <?php echo JHtml::_('calendar', $item->start_time, 'start_time', 'start_time', '%Y-%m-%d');?>
						</td>
					</tr>
					<tr class="date medium">
						<td class="param">
							<?php echo JText::_('Enddatum'); ?>:
						</td>
						<td class="field">
							 <?php echo JHtml::_('calendar', $item->end_time, 'end_time', 'end_time', '%Y-%m-%d');?>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Add'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('ALLOW_TO_ADD_ANSWERs'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->add_answer == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="add_answer1" name="add_answer">
							<label for="add_answer1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->add_answer == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="add_answer0" name="add_answer">
							<label for="add_answer0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
					<tr class="option extralarge">
						<td class="param">
							<?php echo JText::_('ALLOW_TO_ADD_COMMENTS'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->add_comment == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="add_comment1" name="add_comment">
							<label for="add_comment1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->add_comment == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="add_comment0" name="add_comment">
							<label for="add_comment0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('After'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="option extralarge">
						<td class="param">
							<?php echo JText::_('SHOW_THANKYOU_MESSAGE_AFTER_VOTE'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->show_thankyou_message == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="show_thankyou_message1" name="show_thankyou_message">
							<label for="show_thankyou_message1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->show_thankyou_message == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="show_thankyou_message0" name="show_thankyou_message">
							<label for="show_thankyou_message0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
				</table>
				<table class="params">
					<tr class="option medium paddingbot">
						<td class="param" rowspan="3">
							<?php echo JText::_('JVS_ASSISTANT_REDIRECT_AFTER_VOTE'); ?>
						</td>
						<td class="field">
							<input type="radio" <?php echo ($item->redirect == "chart") ? 'checked="checked"' : "";?> value="chart" id="redirectChart" name="redirect">
							<label for="redirectChart"><?php echo JText::_('JVS_ASSISTANT_GO_TO_CHART'); ?></label>
						</td>
					</tr>
					<tr class="text option medium paddingbot">
						<td class="field">
							<input type="radio" <?php echo ($item->redirect == "page") ? 'checked="checked"' : "";?> value="page" id="redirectPage" name="redirect">
							<label for="redirectPage"><?php echo JText::_('JVS_ASSISTANT_PAGE'); ?>:</label>
							<input type="text" name="redirectPage" id="redirectPageField" size="25" maxlength="120" value="<?php echo @$item->redirectPage;?>" style="border: 2px solid rgb(204, 204, 204);" />
						</td>
					</tr>
					<tr class="option medium paddingbot">
						<td class="field">
							<input type="radio" <?php echo ($item->redirect == "no") ? 'checked="checked"' : "";?> value="no" id="redirectNo" name="redirect">
							<label for="redirectNo"><?php echo JText::_('JVS_ASSISTANT_NO_REDIRECT'); ?></label>
						</td>
					</tr>
				</table>
			</td> 
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Result'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="option medium paddingbot">
						<td class="param" rowspan="4">
							<?php echo JText::_('SHOW_RESULT_IF'); ?>...
						</td>
						<td class="field">
							<input type="radio" <?php echo ($item->show_result == "always") ? 'checked="checked"' : "";?> value="always" id="show_resultAlways" name="show_result">
							<label for="show_resultAlways"><?php echo JText::_('SHOW_RESULT_ALWAYS'); ?></label>
						</td>
					</tr>
					<tr class="option medium paddingbot">
						<td class="field">
							<input type="radio" <?php echo ($item->show_result == "afterVote") ? 'checked="checked"' : "";?> value="afterVote" id="show_resultAfterVote" name="show_result">
							<label for="show_resultAfterVote"><?php echo JText::_('SHOW_RESULT_AFTER_VOTING'); ?></label>
						</td>
					</tr>
					<tr class="option medium paddingbot date">
						<td class="field">
							<input type="radio" <?php echo ($item->show_result == "afterDate") ? 'checked="checked"' : "";?> value="afterDate" id="show_resultAfterDate" name="show_result">
							<label for="show_resultAfterDate"><?php echo JText::_('SHOW_RESULT_AFTER_DATE'); ?>:</label>
							<?php echo JHtml::_('calendar', $item->show_result_after_date, 'show_result_after_date', 'show_result_after_date', '%Y-%m-%d');?>
						</td>
					</tr>
					<tr class="option medium">
						<td class="field">
							<input type="radio" <?php echo ($item->show_result == "never") ? 'checked="checked"' : "";?> value="never" id="show_resultNever" name="show_result">
							<label for="show_resultNever"><?php echo JText::_('SHOW_RESULT_NEVER'); ?></label>
						</td>
					</tr>
				</table>
			</td> 
		</tr></table>
	</div>
<?php } if(in_array("display", $category->allowed_tabs) || $interface == "administrator") {?>
	<div id="tab-display">
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Order'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="select large">
						<td class="param">
							<?php echo JText::_('answers_orderby'); ?>
						</td>
						<td class="field">
							<select name="answers_orderby" id="answers_orderby">
								<?php 
								$orderbys = array(	array(	"name"	=>	JText::_("VOTES"),		"value"	=> "votes"),
													array(	"name"	=>	JText::_("ID"),			"value"	=> "id"),
													array(	"name"	=>	JText::_("Name"),		"value"	=> "name"),
													array(	"name"	=>	JText::_("Created"),	"value"	=> "created")/*,
													array(	"name"	=>	JText::_("Random"),		"value"	=> "random")*/);
								foreach($orderbys AS $orderby) { ?>
								<option <?php if($item->answers_orderby == $orderby["value"]) { ?> selected="selected" <?php } ?> value="<?php echo $orderby["value"];?>">
									<?php echo $orderby["name"];?>
								</option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('answers_orderby_direction'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->answers_orderby_direction == "DESC") ? 'checked="checked"' : "";?> type="radio" value="DESC" id="answers_orderby_direction1" name="answers_orderby_direction">
							<label for="answers_orderby_direction1"><?php echo JText::_('DESC'); ?></label>
							<input <?php echo ($item->answers_orderby_direction == "ASC") ? 'checked="checked"' : "";?> type="radio" value="ASC" id="answers_orderby_direction0" name="answers_orderby_direction">
							<label for="answers_orderby_direction0"><?php echo JText::_('ASC'); ?></label>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Ranking'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('ACTIVATE_RANK'); ?>
						</td>
						<td class="field">
							<input <?php echo ($item->activate_ranking == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="activate_ranking1" name="activate_ranking">
							<label for="activate_ranking1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->activate_ranking == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="activate_ranking0" name="activate_ranking">
							<label for="activate_ranking0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
					<tr class="select large">
						<td class="param">
							<?php echo JText::_('ranking_orderby'); ?>
						</td>
						<td class="field">
							<select name="ranking_orderby" id="ranking_orderby">
								<?php 
								$orderbys = array(	array(	"name"	=>	JText::_("VOTES"),		"value"	=> "votes"),
													array(	"name"	=>	JText::_("ID"),			"value"	=> "id"),
													array(	"name"	=>	JText::_("Name"),		"value"	=> "name"),
													array(	"name"	=>	JText::_("Created"),	"value"	=> "created"));
								foreach($orderbys AS $orderby) { ?>
								<option <?php if($item->ranking_orderby == $orderby["value"]) { ?> selected="true" <?php } ?> value="<?php echo $orderby["value"];?>">
									<?php echo $orderby["name"];?>
								</option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('ranking_orderby_direction'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->ranking_orderby_direction == "DESC") ? 'checked="checked"' : "";?> type="radio" value="DESC" id="ranking_orderby_direction1" name="ranking_orderby_direction">
							<label for="ranking_orderby_direction1"><?php echo JText::_('DESC'); ?></label>
							<input <?php echo ($item->ranking_orderby_direction == "ASC") ? 'checked="checked"' : "";?> type="radio" value="ASC" id="ranking_orderby_direction0" name="ranking_orderby_direction">
							<label for="ranking_orderby_direction0"><?php echo JText::_('ASC'); ?></label>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Other'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="select large">
						<td class="param">
							<?php echo JText::_('Template'); ?>:
						</td>
						<td class="field">
							<select name="template" id="template">
							<?php foreach($this->template->getTemplates() AS $template) { ?>
									<option <?php if($item->template == $template["id"]) { ?> selected="selected" <?php } ?> value="<?php echo $template["id"];?>">
										<?php echo $template["name"];?>
									</option>
							<?php } ?>
							</select>
						</td>
					</tr>
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('Chart_Type'); ?>
						</td>
						<td class="field">
							<input <?php echo ($item->chart_type == "bar") ? 'checked="checked"' : "";?> type="radio" value="bar" id="chart_type1" name="chart_type">
							<label for="chart_type1"><?php echo JText::_('BAR'); ?></label>
							<input <?php echo ($item->chart_type == "pie") ? 'checked="checked"' : "";?> type="radio" value="pie" id="chart_type0" name="chart_type">
							<label for="chart_type0"><?php echo JText::_('PIE'); ?></label>
							<input <?php echo ($item->chart_type == "both") ? 'checked="checked"' : "";?> type="radio" value="both" id="chart_type2" name="chart_type">
							<label for="chart_type2"><?php echo JText::_('BOTH'); ?></label>
						</td>
					</tr>
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('show_author'); ?>
						</td>
						<td class="field">
							<input <?php echo ($item->show_author == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="show_author1" name="show_author">
							<label for="show_author1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->show_author == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="show_author0" name="show_author">
							<label for="show_author0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('JVS_ASSISTANT_SHOW_SEARCHFIELD'); ?>
						</td>
						<td class="field">
							<input <?php echo ($item->show_searchfield == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="show_searchfield1" name="show_searchfield">
							<label for="show_searchfield1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->show_searchfield == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="show_searchfield0" name="show_searchfield">
							<label for="show_searchfield0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('Elements'), 1, "<br />"); ?>
			</td>
			<td>

				<table class="params">
					<tr class="number large">
						<td class="param">
							<?php echo JText::_('P_ANSWERSPERPAGE'); ?>:
						</td>
						<td class="field">
							<input type="text" name="answers_per_page" id="answers_per_page" size="5" maxlength="3" value="<?php echo isset($item->answers_per_page) ? $item->answers_per_page : "-1" ;?>" data-jvs_tooltip="<?php echo JText::_('P_POLLCOUNT_DESC'); ?>" />
						</td>
					</tr>
					<tr class="number large">
						<td class="param">
							<?php echo JText::_('P_COMMENTSPERPAGE'); ?>:
						</td>
						<td class="field">
							<input type="text" name="comments_per_page" id="comments_per_page" size="5" maxlength="3" value="<?php echo isset($item->comments_per_page) ? $item->comments_per_page : "-1" ;?>" data-jvs_tooltip="<?php echo JText::_('P_POLLCOUNT_DESC'); ?>" />
						</td>
					</tr>
					<tr class="number large">
						<td class="param">
							<?php echo JText::_('P_BARCOUNT'); ?>:
						</td>
						<td class="field">
							<input type="text" name="barcount" id="barcount" size="5" maxlength="3" value="<?php echo isset($item->barcount) ? $item->barcount : "-1" ;?>" data-jvs_tooltip="<?php echo JText::_('P_POLLCOUNT_DESC'); ?>" />
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
	</div>
<?php } if(in_array("email_spam", $category->allowed_tabs) || $interface == "administrator") {?>
	<div id="tab-email-spam">
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('eMail'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('SENDING_EMAILS_ADMINS_NEW_ANSWERS'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->send_mail_admin_answer == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="send_mail_admin_answer1" name="send_mail_admin_answer">
							<label for="send_mail_admin_answer1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->send_mail_admin_answer == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="send_mail_admin_answer0" name="send_mail_admin_answer">
							<label for="send_mail_admin_answer0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
					<tr class="option extralarge">
						<td class="param">
							<?php echo JText::_('SENDING_EMAILS_ADMINS_NEW_COMMENTS'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->send_mail_admin_comment == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="send_mail_admin_comment1" name="send_mail_admin_comment">
							<label for="send_mail_admin_comment1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->send_mail_admin_comment == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="send_mail_admin_comment0" name="send_mail_admin_comment">
							<label for="send_mail_admin_comment0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
					<tr class="option extralarge">
						<td class="param">
							<?php echo JText::_('SENDING_EMAILS_USER_NEW_COMMENTS'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->send_mail_user_answer_comments == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="send_mail_user_answer_comments1" name="send_mail_user_answer_comments">
							<label for="send_mail_user_answer_comments1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->send_mail_user_answer_comments == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="send_mail_user_answer_comments0" name="send_mail_user_answer_comments">
							<label for="send_mail_user_answer_comments0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('SPAM_PROTECTION'), 1, "<br />"); ?>
			</td>
			<td>
				<table class="params">
					<tr class="option large">
						<td class="param">
							<?php echo JText::_('ACTIVATE_SPAM_REPORT'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->activate_spam == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="activate_spam1" name="activate_spam">
							<label for="activate_spam1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->activate_spam == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="activate_spam0" name="activate_spam">
							<label for="activate_spam0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
					<tr class="number extralarge">
						<td class="param">
							<?php echo JText::_('LIMIT_OF_MAX_REPORTS'); ?>?
						</td>
						<td class="field">
							<input type="text" name="spam_count" id="spam_count" size="5" maxlength="3" value="<?php echo $item->spam_count;?>" />
						</td>
					</tr>
					<tr class="option extralarge">
						<td class="param">
							<?php echo JText::_('INFORM_ADMINS_REPORTS'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->spam_mail_admin_report == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="spam_mail_admin_report1" name="spam_mail_admin_report">
							<label for="spam_mail_admin_report1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->spam_mail_admin_report == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="spam_mail_admin_report0" name="spam_mail_admin_report">
							<label for="spam_mail_admin_report0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
					<tr class="option extralarge">
						<td class="param">
							<?php echo JText::_('INFORM_ADMINS_BLOCKS'); ?>?
						</td>
						<td class="field">
							<input <?php echo ($item->spam_mail_admin_ban == 1) ? 'checked="checked"' : "";?> type="radio" value="1" id="spam_mail_admin_ban1" name="spam_mail_admin_ban">
							<label for="spam_mail_admin_ban1"><?php echo JText::_('JYES'); ?></label>
							<input <?php echo ($item->spam_mail_admin_ban == 0) ? 'checked="checked"' : "";?> type="radio" value="0" id="spam_mail_admin_ban0" name="spam_mail_admin_ban">
							<label for="spam_mail_admin_ban0"><?php echo JText::_('JNO'); ?></label>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
	</div>
<?php } if($interface == "administrator") {?>
	<div id="tab-template">
		<table class="category"><tr>
			<td class="name">
				<?php echo chunk_split(JText::_('JVS_TMPL_CONFIG'), 1, "<br />"); ?>
			</td>
			<td>
				<p><?php echo JText::_("JVS_TMPL_CONFIG_DESC");?></p>
				<table class="params">
					<tr class="select medium">
						<td class="param">
							<?php echo JText::_('JVS_TMPL_Template'); ?>:
						</td>
						<td class="field">
							<select id="template_config">
								<option value="0" selected="selected"><?php echo JText::_("JVS_TMPL_SELECT_TEMPLATE");?></option>
							<?php foreach($this->template->getTemplates() AS $template) { ?>
								<option value="<?php echo $template["name"];?>">
									<?php echo $template["name"];?>
								</option>
							<?php } ?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
		<?php $vbtemplate =& VBTemplate::getInstance();
		foreach($this->template->getTemplates() AS $tmpl) {
			$vbtemplate->setTemplate($tmpl["name"]); ?>
		<div class="config-page config-<?php echo $tmpl["name"];?>" style="display: none;">
		<?php 
			$path = $vbtemplate->getTemplatePath("config").DS."assistant.php";
			if(JFile::exists($path)) @require $path;
			else echo JText::_("JVS_TMPL_NO_TEMPLATE_SETTINGS");
		?>
		</div>
		<?php }?>			
	</div>
<?php } if(in_array("access", $category->allowed_tabs) || $interface == "administrator") {?>
	<div id="tab-access">
		<?php echo $accessCode;?>
	</div>	
<?php }?>
	
<input type="hidden" name="view" value="ajax" />
<input type="hidden" id="pollid" name="id" value="<?php echo $item->id;?>" />
<input type="hidden" name="interface" value="<?php echo $interface;?>" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="type" value="<?php echo $type;?>" />
<input type="hidden" name="only_page" value="1" />
</form>
<?php if($type != "defaultSettings") {?>
<?php if((in_array("result", $category->allowed_tabs) || $interface == "administrator") && !$new) {?>
	<div id="tab-result" class="charttab">
		<div id="resultchart">
			<div id="gchart<?php echo $item->id;?>"></div>
		</div>
	</div>
<?php } if((in_array("votes", $category->allowed_tabs) || $interface == "administrator") && !$new && !empty($answersVotes)) {?>
	<div id="tab-votes">
		<table class="overview">
			<tr>
				<th><?php echo JText::_("Votes");?></th>
				<th><?php echo JText::_("Answer");?></th>
			</tr>
			<?php foreach($answersVotes AS $answer) { ?>
			<tr>
				<td class="title small"><?php echo $answer->votes;?></td>
				<td class="content">
					<div class="answer"><?php echo $answer->answer;?></div>
					<?php if(isset($uVotes[$answer->id])) { ?>
					<table class="overview hidden">
						<?php foreach($uVotes[$answer->id] AS $vote) { ?>
						<tr>
							<td class="title small"><?php echo $vote->voted;?></td>
							<td class="info">
							<?php if($vote->id == null) { 
								echo sprintf(JText::_('%o unregistrierte(r) Benutzer'), $vote->users);
							} elseif($this->vbparams->get('displayName') == 'username') {
								echo $vote->username;
							} else {
								echo $vote->name;
							} ?>
							</td>
						</tr>
						<?php } ?>
					</table>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
<?php } }?>
</div>

<?php $this->general->getAdminFooter();?>