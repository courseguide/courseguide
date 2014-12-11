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
<table style="margin:0; padding:0; border: 0 none;width: 100%">
	<tr>
		<td style="vertical-align: top; padding-top: 10px;">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td valign="top">
					<table class="adminlist">
						<tr>
							<td>
								<?php
								
								$link = 'index.php?option=com_jvotesystem&amp;view=categories';
								$this->quickiconButton( $link, 'icon-48-category.png', JText::_( 'Categories' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=category&amp;controller=categories&amp;hidemainmenu=1';
								$this->quickiconButton( $link, 'icon-48-category-add.png', JText::_( 'New_Category' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=boxen';
								$this->quickiconButton( $link, 'icon-48-boxen.png', JText::_( 'Boxen' ) );
								
								$link = JUri::root(true)."/components/com_jvotesystem/assistant/index.php?interface=administrator&lang=".JFactory::getLanguage()->getTag()."&view=poll";
								$this->quickiconButton( $link, 'icon-48-box-add.png', JText::_( 'New_Poll' ), 1 );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=answers';
								$this->quickiconButton( $link, 'icon-48-answers.png', JText::_( 'Answers' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=answer&amp;controller=answers&amp;layout=form&amp;hidemainmenu=1';
								$this->quickiconButton( $link, 'icon-48-answer-add.png', JText::_( 'New_Answer' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=comments';
								$this->quickiconButton( $link, 'icon-48-comments.png', JText::_( 'Comments' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=comment&amp;controller=comments&amp;layout=form&amp;hidemainmenu=1';
								$this->quickiconButton( $link, 'icon-48-comment-add.png', JText::_( 'New_Comment' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=users';
								$this->quickiconButton( $link, 'icon-48-users.png', JText::_( 'Users' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=bbcodes';
								$this->quickiconButton( $link, 'icon-48-generic.png', JText::_( 'BBCodes' ) );
								
								$link = 'index.php?option=com_jvotesystem&amp;view=logs';
								$this->quickiconButton( $link, 'icon-48-logs.png', JText::_( 'Logs' ) );
								
								$link = 'http://joomess.de/forum/jvotesystem.html';
								$this->quickiconButton( $link, 'icon-48-forum.png', JText::_( 'Forum' ) , 0 , "_blank");
								
								$access =& VBAccess::getInstance();
								if($access->isUserAllowedToConfig()) {
									if(version_compare( JVERSION, '1.6.0', 'lt' ))
										$link = 'index.php?option=com_config&controller=component&component=com_jvotesystem&path=';
									else
										$link = 'index.php?option=com_config&view=component&component=com_jvotesystem&path=&tmpl=component';
									$this->quickiconButton( $link, 'icon-48-options.png', JText::_( 'Options' ), 1 );
								}
								
								$link = 'index.php?option=com_jvotesystem&amp;view=advisor';
								$this->quickiconButton( $link, 'icon-48-advisor.png', JText::_( 'JVS_ADV_Advisor' ));
								
								$link = 'index.php?option=com_jvotesystem&amp;view=apikeys';
								$this->quickiconButton( $link, 'icon-48-apikeys.png', JText::_( 'JVS_API_KEYS' ));
								
								?>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 10px;">
				<tr><td>
					<table class="adminlist">
						<tr>
							<td>
								<h3 style="text-align:center;"><?php echo JText::_("VOTES_PER_DAY_SURVEYS");?></h3>
								<?php echo $this->charts->getBackendChart('votesgoogle'); ?>
							</td>
						</tr>
					</table>
				</td></tr>
			</table>
		</td>
		<td style="width: 400px;vertical-align:top;">
			<div id="JSScriptUpdateInfoBar"> </div>
			<table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 10px;">
				<tr>
					<td>
						<table class="adminlist">
							<tr>
								<td>
									<?php 
									
									$j15 = joomessLibrary::getInstance()->getJoomlaVersion() == joomessLibrary::jVersion15;
									
									if($j15) {
										jimport('joomla.html.pane');
										
										$pane =& JPane::getInstance('tabs', array('startOffset'=>0, 'allowAllClose'=>true, 'opacityTransition'=>true, 'duration'=>600)); 
										echo $pane->startPane( 'overview' );
									} else {
										$options = array(
												'onActive' => 'function(title, description){
												description.setStyle("display", "block");
												title.addClass("open").removeClass("closed");
										}',
												'onBackground' => 'function(title, description){
												description.setStyle("display", "none");
												title.addClass("closed").removeClass("open");
										}',
												'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
												'useCookie' => false, // this must not be a string. Don't use quotes.
										);
										
										echo JHtml::_('tabs.start', 'overview_tabs', $options);
									}
									
									if(($taskList = VBTasks::getInstance()->getTaskList()) != "") {
										echo ($j15) ? $pane->startPanel(JText::_('JVS_PANEL_TASKS'), 'tasks') : JHtml::_('tabs.panel', JText::_('JVS_PANEL_TASKS'), 'tasks');
										echo $taskList;
										if($j15) echo $pane->endPanel();
									}
									
									echo ($j15) ? $pane->startPanel(JText::_('JVS_PANEL_RECENT_EVENTS'), 'logs') : JHtml::_('tabs.panel', JText::_('JVS_PANEL_RECENT_EVENTS'), 'logs');
									$log =& VBLog::getInstance();
									$log_data = $log->getEntries("latest", 5, 1, true);
									
									if(count($log_data) > 0) {
									?>
									<table class="logs adminlist" data-type="small" data-max-logs="5">
										<thead>
											<tr>
												<th width="16">#</th>
												<th width="16">*</th>
												<th><?php echo JText::_("Message");?></th>
												<th width="100"><?php echo JText::_("Created");?></th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($log_data AS $row) { $row = $log->convertMsg($row);?>
											<tr>
												<td class="icon-16 icon-<?php echo strtolower($row->type);?>"></td>
												<td class="icon-16 icon-<?php echo strtolower($row->action);?>"></td>
												<td><?php echo $row->msg;?></td>
												<td style="text-align:center;"><?php echo $this->general->convertTime($row->created);?></td>
											</tr>
										<?php }?>
										</tbody>
										<tfoot>
											<tr>
												<td colspan="4">
													<a href="index.php?option=com_jvotesystem&amp;view=logs"> 
														<?php echo JText::_("JVS_PANEL_SHOW_ALL_EVENTS");?> 
													</a> 
												</td>
											</tr>
										</tfoot>
									</table>
									<?php 
									} else {
										echo JText::_("JVS_PANEL_NO_EVENTS_FOUND");
									}
									if($j15) echo $pane->endPanel();
									
									echo ($j15) ? $pane->startPanel(JText::_('JVS_PANEL_POPULAR_POLLS'), 'popular_polls') : JHtml::_('tabs.panel', JText::_('JVS_PANEL_POPULAR_POLLS'), 'popular_polls');
									
									$popular_polls = VBVote::getInstance()->getPolls(array("order" => "popular"), 0, 10);
									?>
									<table class="adminlist">
										<thead>
											<tr>
												<th> <?php echo JText::_("Poll");?> </th>
												<th width="1%"> <?php echo JText::_("Votes");?> </th>
												<th width="1%"> <?php echo JText::_("Comments");?> </th>
											</tr>
										</thead>
										<tbody>
										<?php foreach($popular_polls AS $poll) {?>
											<tr>
												<td> <?php echo $this->general->buildAdminLink("poll", $poll->id, $poll->title);?> </td>
												<td style="text-align:center;"> <?php echo $poll->votes;?> </td>
												<td style="text-align:center;"> <?php echo $poll->comments;?> </td>
											</tr>
										<?php }?>
										</tbody>
									</table>
									<?php 
									if($j15) echo $pane->endPanel();
									
									echo $j15 ? $pane->endPane() : JHtml::_('tabs.end');
									
									?>
									<!--  <h2>jVoteSystem</h2>
									<p><?php echo JText::_('JVOTESYSTEM_COMPONENT_DESC');?></p> -->
									<p> </p>
									<?php
										$link = 'http://www.joomess.de/projects/jvotesystem.html';
										jVoteSystemViewjVoteSystem::quickiconButton( $link, 'icon-48-website.png', JText::_( 'Projektseite' ) , 0 , "_blank");
										
										$link = 'http://extensions.joomla.org/extensions/contacts-and-feedback/polls/15859';
										jVoteSystemViewjVoteSystem::quickiconButton( $link, 'icon-48-rating.png', JText::_( 'WRITE_REVIEW' ) , 0 , "_blank");
										
										$link = 'http://joomess.de/projects/jvotesystem/download.html';
										jVoteSystemViewjVoteSystem::quickiconButton( $link, 'icon-48-cart.png', JText::_( 'BUY_COPYRIGHT_FREE' ) , 0 , "_blank");
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php $this->general->getAdminFooter(); ?>