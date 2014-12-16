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

jimport( 'joomla.utilities.date' );
jimport( 'joomla.filesystem.folder' );

class VBVote
{
	//Variablen
	var $db, $user, $document, $id, $page, $loadonceperpage;
	private $general, $access, $lib;
	
	private function __construct($load) {  
		$this->document = & JFactory::getDocument();
		$this->db = JFactory::getDBO();
		$this->loaded = false;
		if(!$load) return ;
		
		$this->general =& VBGeneral::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->comment =& VBComment::getInstance();
		$this->spam =& VBSpam::getInstance();
		$this->charts =& VBCharts::getInstance();
		$this->template =& VBTemplate::getInstance();
		$this->category =& VBCategory::getInstance();
		$this->vbparams =& VBParams::getInstance();
		$this->log =& VBLog::getInstance();
		$this->user =& VBUser::getInstance();
		$this->lib =& joomessLibrary::getInstance();
		
		$this->cache = & JCache::getInstance();
		$this->cache->setCaching( 1 );
		$this->cache->setLifeTime( 30 );
		
		static $loadVSjsOnce;
		if(!($loadVSjsOnce)) {
			VBLoader::getInstance()->loadJSConfig();
			
			//Captcha laden
			if($this->vbparams->get('recaptcha') and $this->vbparams->get('recaptcha_publickey') and $this->vbparams->get('recaptcha_privatekey')) {
				$this->document->addScript('http://api.recaptcha.net/js/recaptcha_ajax.js');
				$js = 'function showRecaptcha(element, themeName, callback) {
										  Recaptcha.create("'.$this->vbparams->get('recaptcha_publickey').'", element, {
												theme: themeName,
												tabindex: 0,
												callback: function() { Recaptcha.focus_response_field(); callback(); }
										  });
										}';
				$this->document->addScriptDeclaration($js);
			}
			
			$loadVSjsOnce = true;
		}
		
		$this->poll_instances = array();
		
		$this->loaded = true;
	}
	
	static function &getInstance($load = true) {
		static $instance;
		if(empty($instance)) {
			$instance = new VBVote($load);
		} else {
			if($load && !$instance->loaded) $instance = new VBVote(true);
		}
		return $instance;
	}
	
	private $poll_instances;
	function checkLoaded($id) {
		if(!isset($this->poll_instances[$id])) $this->poll_instances[$id] = true;
		else return true;
		return false;
	}
	
	function getLoaded() {
		$ids = array();
		foreach($this->poll_instances AS $id => $instance) $ids[] = $id;
		return $ids;
	}
	
	function setLoaded($id) {
		$this->poll_instances[$id] = "loaded";
	}

	private $countAnswers;
    function getVotebox($id, $onlyAnswers=false, $page=null, $link = false, $template = null, $showToolbar = true, $search = '', $aid = null) {
		$this->page = JRequest::getInt("jVSPage", null);
		
		$this->id = (int)$id;
		$this->uniqid = uniqid(rand());
		$cur_answer =  ( $answer = VBAnswer::getInstance()->getAnswer(JRequest::getInt('aid', null)) ) ? ( ($answer->box_id == $this->id) ? $answer->id : null ) : null;
		
		$this->setLoaded($id);
		//Page in Session speichern bzw. abrufen
		$session = JSession::getInstance('none',array());
		$pageArray = $session->get('jVoteBoxPageArray', array());
		if(!isset($pageArray[$this->id])) $pageArray[$this->id] = array();
		$view = $this->vbparams->getView();
		
		if($cur_answer != null) { //Wenn Box geladen ist
			
		} elseif($page != null) { 
			$this->page = $page;
			$pageArray[$this->id][$view] = $this->page;
		} elseif($page == null AND $this->page == null AND isset($pageArray[$this->id][$view])) {
			$this->page = $pageArray[$this->id][$view];
		} elseif($this->page == null) {
			$this->page = 1;
			$pageArray[$this->id][$view] = $this->page;
		}
		$session->set("jVoteBoxPageArray", $pageArray);	
		
		//Generate ID for caching -  only answers
		/*if($template == @$this->getBox($this->id)->template) $template = null;
		$id_data = array( $this->page, $template, $search, $aid );
		if($this->lib->getJoomlaVersion() == joomessLibrary::jVersion15) $id_data[] = JFactory::getUser()->gid;
		else {
			$id_data[] = JFactory::getUser()->getAuthorisedGroups();
			$id_data[] = JFactory::getUser()->getAuthorisedViewLevels();
		}
		$cache_id = md5(serialize($id_data));
		if($onlyAnswers && $this->general->checkCache($this->id, $cache_id) && $data = $this->cache->get($cache_id, 'jVoteSystem - Answers['.$this->id.']'))
			return $data;*/
		
		if($cur_answer != null) {
			$box = $this->getBox($this->id);
			
			if($box) {
				$new_page = VBAnswer::getInstance()->getAnswersPageCount($box, $cur_answer, $search);
					
				JRequest::setVar('aid', null);
				if($this->page != $new_page) {
					$this->page = $new_page;
					return $this->getVotebox($id, $onlyAnswers, $this->page, $link, $template, $showToolbar, $search, $cur_answer);
				}
			}	
		} else {
			$cur_answer = $aid;
		}
		
		//Daten aus Datenbank laden
		$data = $this->getData($this->id, true, $search); 
		if($data == null) {
			//Notification ausgeben
			$this->template->setTemplate("default");
			//Parameter
			$par = new JObject();
			$par->msg = sprintf(JText::_("NOBOXFOUNDORPUBLISHED"), $id);
			$par->type = "error";
			
			//laden
			$out = '<div class="jvotesystem jvs-default">'.$this->template->loadTemplate("notification", $par).'</div>';
			
			return $out;
		}
		
		//Ansers per page festlegen
		$answersperpage = (!isset($data->box->answers_per_page) || $data->box->answers_per_page <= 0) ? $this->vbparams->get('answersPerPage') : $data->box->answers_per_page;
		
		//Andere Seite laden, wenn keine Antworten
		//echo count($data->answers) . "-" . $this->page . "-" .$data->box->answers;
		if(!$data->answers AND $this->page > 1) {
			$lastPage = ceil($data->box->answers/$answersperpage);
			if($this->page != $lastPage) {
				$this->page = $lastPage;
				return $this->getVotebox($id, $onlyAnswers, $this->page, $link, $template, $showToolbar, $search, $cur_answer);
			}
		}
		$votes = $this->getVotesByUser($this->id);
		
		//Kategorie laden
		$cat = $this->category->getCategory($data->box->catid);
		//Template setzen
		if($template != null) $this->template->setTemplate($template);
		else $this->template->setTemplate($data->box->template);
		
		//Falls Template-Main AfterLoading: hier laden
		$path = $this->template->getTemplatePath("main").DS."afterLoading.php";
		if(JFile::exists($path)) @require $path;
		
		$out = "";
		
		//Toolbar
		if($showToolbar) {
			$toolbar = new VBToolbar($cat, $data->box);
			$toolbar->remove();
			$toolbar->edit();
			$toolbar->editState();
			$toolbar->info();
			
			$out .= $toolbar->out();
		}

		//Div jVotebox
		$out .= '<div class="jvotesystem jvs-'.$this->template->getTemplate().'" id="jvs-'.$this->uniqid.'" data-box="'.$this->id.'" data-view="'.$this->vbparams->getView().'" data-lastviewpage="'.$this->page.'" data-chart="'.(($data->box->chart_type == "bar" || $data->box->chart_type == "both") ? "bar" : "pie").'" data-mode="'.$data->box->mode.'" data-uniqid="'.$this->uniqid.'">';

		//Überprüfen, ob Umfrage angezeigt werden darf
		if(!$this->access->isUserAllowedToViewPoll($data->box)) {
			//Notification ausgeben
			//Parameter
			$par = new JObject();
			$par->msg = sprintf(JText::_("NOTALLOWEDTOVIEWPOLL"), $data->box->title);
			$par->type = "error";
				
			//laden
			$out .= $this->template->loadTemplate("notification", $par);
			
			//Div schließen & abschließen
			$out .= '</div>';
			return $out;
		}
		//JS-Code..für Typ erkennen
		$this->countAnswers = count($data->answers);

		//VBSpam - BoxDaten laden
		$this->spam->loadData('answer');
		
		//Navigator - nötig?
		$navi = $this->buildnavi($data->box->answers, $onlyAnswers, $this->page, $answersperpage, $this->vbparams->get('shortNavi'), JRequest::getURI(), 'answers');
		
		//Socialbar
		if($this->vbparams->get("showSocialbar") && $this->template->getTemplate() != 'module' && $this->template->getTemplate() != 'easyquestion') {
			if($this->vbparams->get("socialbar2Click")) {
				$socialbar = $this->lib->special("socialbar", array(
								"href" 		=> $this->general->buildLink("poll", $this->id, "", array("ref" => "social"), false, false),
								"gplus" 	=> $this->vbparams->get('socialbarGplus'),
								"facebook" 	=> $this->vbparams->get('socialbarFacebook'),
								"twitter" 	=> $this->vbparams->get('socialbarTwitter')
							));
			} else {
				$par = new JObject();
				$par->social_url = $this->general->buildLink("poll", $this->id, "", array("ref" => "social"), false, false);
				
				$par->facebook = $this->vbparams->get('socialbarFacebook') ? $this->lib->special("facebookLikeButton", array( "href" => $this->general->buildLink("poll", $this->id, "", array("ref" => "social"), false), "width" => 140 )) : false;
				$par->googlePlus = $this->vbparams->get('socialbarGplus') ? $this->lib->special("googlePlusButton", array( "href" => $par->social_url, "size" => "medium" )) : false;
				$par->twitter = $this->vbparams->get('socialbarTwitter') ? $this->lib->special("twitterButton", array( "href" => $par->social_url )) : false;
				
				$socialbar = $this->template->loadTemplate("socialbar", $par);
			}
		} else $socialbar = "";		
		
		//TopContainer
			//Socialbar über Umfrage anzeigen?
			$topcontainer = $socialbar;
			
			//Search
			if($data->box->show_searchfield) {
				$par = new JObject();
				$par->value = JRequest::getString('q');
				
				$topcontainer .= $this->template->loadTemplate("search", $par);
			}
			
		if(trim($topcontainer) != "") $out .= '<div class="topbar_container">'.$topcontainer.'</div>';

		//Datum berechnen
		$app =& JFactory::getApplication();
		$date = JFactory::getDate();
		$date->setOffset($app->getCfg('offset'));
		$start = JFactory::getDate($data->box->start_time);
		$start->setOffset($app->getCfg('offset'));
		$end = JFactory::getDate($data->box->end_time);
		$end->setOffset($app->getCfg('offset'));
		$voteState = null;
		if(($date->toUnix() - $end->toUnix()) > 0 AND $data->box->end_time != '0000-00-00 00:00:00') {
			$voteState = 'over';
		} elseif(($date->toUnix() - $start->toUnix()) < 0) {
			$voteState = 'notStarted';
		} elseif(!$this->access->checkAccessGroup('access', $data->box)) {
			$voteState = 'noRights';
		} elseif($votes->allowed_votes <= 0) {
			$voteState = 'novotesleft';
		}
			
			//Parameter
			$par = new JObject();
			$par->bid = $this->id;
			//Menubar
			$par->chart_show = ($this->vbparams->get('diagramm') AND $this->access->isUserAllowedToViewResult($data->box, $votes, true));
			$par->chart_show_pie = ($par->chart_show && ($data->box->chart_type == "pie" || $data->box->chart_type == "both"));
			$par->chart_show_bar = ($par->chart_show && ($data->box->chart_type == "bar" || $data->box->chart_type == "both"));
			$par->chart_visible = ($this->vbparams->get('diagramm') AND $this->access->isUserAllowedToViewResult($data->box, $votes));
			$par->like_show = false;//$this->vbparams->get('facebookLike');
			$par->like_url = JURI::base(false).'index.php?option=com_jvotesystem&view=poll&alias='.$data->box->alias;
			$par->norights = false;
			//Title
			$par->title = $data->box->title;
			//Question
			$par->question = $this->general->BBCode($data->box->question, ' ');
			//Stimmen übrig
			$par->votes_left_show = ($this->access->isRunning($data->box));
			$par->votes_left = $votes->allowed_votes;
				
			//laden
			$out .= $this->template->loadTemplate("topbox", $par);
			
			//Antworten ausgeben
			$this->lib->special("facebookShareButton", array(), true); //CSS laden
			$this->lib->special("googleShareButton", array(), true); //CSS laden
			
			$out .= '<div class="pagebox">';

			$onlyAnswers ? $pos = 'absolute' : $pos = 'relative';
			$answers = '<div style="position:'.$pos.';width:100%;" data-p="'.$this->page.'">';
				
			//Wenn Vote bereits vorbei, gleich Diagramm anzeigen
			if(($voteState == 'over' OR $voteState == 'novotesleft') AND $onlyAnswers == false AND $this->vbparams->get('diagramm') AND $this->access->isUserAllowedToViewResult($data->box, $votes) AND $cur_answer == null) {
				//Navi
				$par = new JObject();
				$par->translation_scaling = JText::_("Scaling");
				$barcount = (!isset($data->box->barcount) || $data->box->barcount <= 0) ? $this->vbparams->get('chart_barscount') : $data->box->barcount;
				//$par->next_count = $barcount; // wozu???
				$par->translation_next = sprintf(JText::_("LOAD_NEXT"), $barcount);
				$par->show_next = ($data->box->answers > $barcount);
			
				$chart_js = array();
				$chart_js[] = '	var chartdata = '.json_encode($this->charts->getFrontendChartJSON($this->id)).'; ';
				if($data->box->chart_type == "bar" || $data->box->chart_type == "both")
					$chart_js[] = '	jVS.jsbarchart(jQuery("[data-uniqid='.$this->uniqid.']"),chartdata.values,chartdata.answers,chartdata.ids,'.$this->id.',"'.urlencode($this->template->loadTemplate("chartnavi",$par)).'", '.$barcount.', chartdata.colors); ';
				else
					$chart_js[] = ' jVS.jspiechart(jQuery("[data-uniqid='.$this->uniqid.']"),chartdata.values,chartdata.answers,'.$this->id.');';
			
				$this->lib->documentReady(implode(" ", $chart_js));
				$answers .= '<div style="visibility: hidden;">'.$this->getBanner($onlyAnswers, $data->box).'</div>';
				$answers .= '</div>';
			} else {
				/*if($this->general->checkCache($this->id, $cache_id) && ($canswers = $this->cache->get($cache_id, 'jVoteSystem - Answers['.$this->id.']'))) {
					$answers = $canswers;
				} else {
					$this->general->enableCacheAnswer($this->id, $cache_id);*/
					if(count($data->answers) > 0) {
						//Banner
						$zufall = rand(1,count($data->answers));
						//Navi
						if ($this->template->getTemplate() !== 'module' ) $answers .= $navi;
						$i = 1;
						foreach($data->answers AS $answer) {
							$html = $this->getAnswerBox($answer, $data->box, $voteState, $votes, $this->access->isUserAllowedToViewResult($data->box, $votes), ($cur_answer == $answer->id));
							$answers .= $html;
							if($zufall == $i) $answers .= $this->getBanner($onlyAnswers, $data->box);
							$i++;
						}
						$this->comment->unsetData();
						//Navi
						$answers .= $navi;
					} elseif($search != '') {
						//Parameter
						$par = new JObject();
						$par->msg = sprintf(JText::_("JVS_NO_SEARCH_RESULT_ANSWERS"), $search);
						$par->type = "notice";
							
						//laden
						$answers .= $this->template->loadTemplate("notification", $par);
					}
					$answers .= '</div>';
					/*$this->cache->store($answers, $cache_id, 'jVoteSystem - Answers['.$id.']');
				}*/
			}
				
			
			if($onlyAnswers == true) return $answers;
			
			$out .= $answers;
			$out .= '</div>';
			
			//Neue Antwort
			$out .= $this->getNewAnswerBox($data->box,($voteState == 'over' OR $voteState == 'novotesleft') ? true : false);
			
			//EndBox ausgeben
			//Parameter
			$par = new JObject();
				$par->bid = $this->id;
				//Vote
				$par->votes_left_show = ($this->access->isRunning($data->box));
				$par->votes_left = $votes->allowed_votes;
				$par->vote_state = $voteState;
				if($voteState == 'over') {
					$par->vote_state_text =  str_replace('ENDDATE', $end->toFormat('%A, %d.%B %Y (%H:%M)'), JText::_('VOTESCHONVORBEI'));
				} elseif($voteState == 'notStarted') {
					$par->vote_state_text =  str_replace('STARTDATE', $start->toFormat('%A, %d.%B %Y (%H:%M)'), JText::_('NOCHNICHTGESTARTET'));
				} elseif($voteState == 'noRights') {
					$par->vote_state_text =  JText::_('NOVOTERIGHTS');
				} elseif ($voteState == 'novotesleft') {
					$par->vote_state_text = JText::_('ALREADYVOTED');
				} else {
					$par->vote_state_text = null;
				}
				//Goto
				$par->goto_show = $link;
				$par->goto_link = $this->general->buildLink("poll", $data->box->id);
				
				
			//laden
			$out .= $this->template->loadTemplate("endbox", $par);
			
		//Socialbar unter der Umfrage anzeigen?
		//$out .= $socialbar;
			
		//Div jVotebox beenden
		$out .= '</div>';
		
		//Module Charset ausgeben
		if($this->template->getTemplate() == 'module') {
			$old = $this->template->prepare();
			VBGeneral::getInstance()->charset('module');
			$out .= $this->template->getHtml($old);
		}
		
		//Configuration ausgeben
		$conf = array();
		$conf["id"] = $this->id;
		$conf["cur_answer"] = $cur_answer;
		
		$this->lib->jsCode("jVS.conf.polls[{$this->id}] = " . json_encode($conf).";");
		
		unset($this->page);
		
		//Falls Template-Main AfterRendering: hier laden
		$path = $this->template->getTemplatePath("main").DS."afterRendering.php";
		if(JFile::exists($path)) @require $path;
		
		return $out;
	}
	
	function getCountAnswers() {
		return $this->countAnswers;
	}
	
	function getPage() {
		return $this->page;
	}

	function getFooter() {
		/* The copyright information may not be removed or made invisible! To remove the code, please purchase a version on www.joomess.de. Thanks!*/
		$out = '<p style="text-align: center;" class="jVoteSystemFooter"><a href="http://joomess.de/projekte/18-jvotesystem">jVoteSystem</a> developed and designed by <a href="http://www.joomess.de">www.joomess.de</a>.</p>';
		return $out;
	}
	
	function getAnswerBox($answer, $box, $voteState, $votes, $showResult, $loadComments = false) {
		$user = $this->user->getUserData($answer->autor_id);
		
		//Parameter festlegen
		$par = new JObject();
			$par->bid = $box->id;
			$par->poll = $box;
			$par->aid = $answer->id;
			//Ranking
			$par->activate_ranking = $box->activate_ranking;
			$par->rank = $answer->rank;
			//Radiobutton
			$par->radiobutton = ($box->allowed_votes == 1 && $this->vbparams->get("single_radiobutton"));
			//VoteBox
			$par->show_result = $showResult;
			$par->votes = $answer->votes;
			$par->show_userlist = ($this->access->isUserAllowedToViewUserList($box) AND $answer->votes > 0);
			$par->translation_votes = $answer->votes == 1 ? JText::_('VOTES_SINGULAR') : JText::_('VOTES');
			$par->uservotes = $this->getVotesByUser($box->id, $answer->id)->votes;
			$par->resetAllowed = $this->access->isUserAllowedToResetVotes($box, $answer); 
			//VoteButton
			$par->vote_state = $voteState;
			$par->voting_allowed = $this->access->isUserAllowedToVoteAnswer($box, $answer);
			$par->votebutton_class = $par->voting_allowed ? 'vote' : 'novote';//helper
			$par->votebutton_disabled = $this->getVotesByUser($box->id,$answer->id)->votes >= $box->max_votes_on_answer ? ' data-disabled="1"' : '';//helper
			$par->votebuttonradio_disabled = !$par->voting_allowed ? 'disabled="disabled"' : '';
			$par->translation_vote = JText::_('Vote');
			$par->votebutton_tooltip = $this->getAnswerTooltip($box, $answer);
			
			//AnswerField
				//Icons
					//Trash
					$par->icon_trash_active = $this->access->isUserAllowedToMoveAnswerToTrash($answer,$box);
					//PublishState
					$par->icon_state_active = $this->access->isUserAllowedToChangePublishState($box);
					$par->icon_state_show = ($par->icon_state_active OR $answer->published == 0);
					$par->icon_state_state = ($answer->published == 1) ? 'published' : 'unpublished';
					//ReportSpam
					$par->icon_spam_active = $this->access->isUserAllowedToReportAnswer($box, $answer);
				//Answer
				$par->answer = nl2br($this->general->shortText($answer->answer, $this->vbparams->get('shortCountAnswer')));
				//Comment-Icon
				$par->comment_icon = $this->comment->getCommentIcon($box, $answer);
				//Author
				$par->author_show = $box->show_author;
				$par->author_id = $user->id;
				$par->author_name = $user->name;
				$par->author_link = $this->general->buildLink("user", $user->id);
				//Created
				$par->creation_time = $this->general->convertTime($answer->created);
			//Comments
			$par->show_comments = $loadComments;
			if($par->show_comments)
				$par->comments = $this->comment->getComments($box, $answer);
			//Stimmen übrig
			$par->votes_left = $votes->allowed_votes;
			
			//Social
			$par->showShare = $this->vbparams->get('showShareAnswer');
			if($par->showShare) {
				$par->social = '<div class="socialShare">';
				if($this->vbparams->get('shareAnswerFacebook')) $par->social .= $this->lib->special("facebookShareButton", array( "href" => $this->general->buildLink("poll", $box->id, "", array( "aid" => $answer->id, "ref" => "social" ), false ) ));
				if($this->vbparams->get('shareAnswerGplus')) $par->social .= $this->lib->special("googleShareButton", array( "href" => $this->general->buildLink("poll", $box->id, "", array( "aid" => $answer->id, "ref" => "social" ), false ) ));
				$par->social .= '</div>';
			} else $par->social = '';
		
		//Template-Datei laden
		$out = $this->template->loadTemplate("answer", $par);
		
		return $out;
	}
	
	function getAnswerTooltip($box, $answer) {
		$votes = $this->getVotesByUser($box->id);
		$state = $this->access->isUserAllowedToVoteAnswer($box, $answer, false, false);
		
		$msg = "JVS_TOOLTIP_".$state;
		
		if(is_bool($state) && $state == true) {
			$avotes = $this->getVotesByUser($box->id, $answer->id);
			$msg = ($avotes->votes == 0) ? JText::_("JVS_NOTICE_VOTE_FOR_ANSWER") : JText::_("JVS_NOTICE_ANOTHER_VOTE_FOR_ANSWER");
		}
		
		$msg = JText::_($msg);
		
		//Alle Lücken ersetzen
		$app =& JFactory::getApplication();
		$start = JFactory::getDate($box->start_time, $app->getCfg('offset'));
		$end = JFactory::getDate($box->end_time, $app->getCfg('offset'));
		
		$replace = 	array(	
						"MAXVOTESPERANSWER"	=> 	$box->max_votes_on_answer,
						"LEFTVOTES"			=>	$votes->allowed_votes,
						"ENDDATE"			=>	$end->toFormat('%A, %d.%B %Y (%H:%M)'),
						"STARTDATE"			=>	$start->toFormat('%A, %d.%B %Y (%H:%M)'),
						"LOGINLINK"			=> 	JRoute::_(version_compare( JVERSION, '1.6.0', 'lt' ) ? "index.php?option=com_user&view=login&return=".base64_encode(JUri::current()) : "index.php?option=com_users&view=login&return=".base64_encode(JUri::current())),
						"REGISTERLINK"		=> 	JRoute::_(version_compare( JVERSION, '1.6.0', 'lt' ) ? "index.php?option=com_user&view=register" : "index.php?option=com_users&view=register")
					);
		
		foreach($replace AS $find => $repl) {
			$msg = str_replace("%".$find."%", $repl, $msg);
		}
			
		return $msg;
	}
	
	function resetVotes($box, $answer, $reset = 1) {
		$votes = $this->getVotesByUser($box->id, $answer->id);
		unset($this->votesByUser[$box->id]);
		unset($this->votesByAnswer[$answer->id]);
		
		if($reset < 0) $reset = 0;
		
		$newVotes = $votes->votes - $reset;
		if($newVotes <= 0) {
			$sql = "DELETE FROM `#__jvotesystem_votes` WHERE `answer_id`=".$this->db->quote($answer->id)." AND `user_id`=".$this->db->quote($this->user->id);
			$this->db->setQuery($sql);
			$this->db->query();
		} else {
			$sql = "UPDATE `#__jvotesystem_votes` SET `votes`='$newVotes' WHERE `answer_id`=".$this->db->quote($answer->id)." AND `user_id`=".$this->db->quote($this->user->id);
			$this->db->setQuery($sql);
			$this->db->query();
		}
			
		$this->log->add("DB", 'ResettedVoting', array("bid" => $box->id, "aid" => $answer->id, "votes" => $newVotes, "reset" => $reset));
		
		if($this->db->getErrorMsg()) return false;
		else return true;
	}
	
	function getNewAnswerBox($box, $hidden = false) {
		//Parameter festlegen
		$par = new JObject();
		$par->Qaddnew = $this->access->isUserAllowedToAddNewAnswer($box,true);
		if ($par->Qaddnew == "true") {
			$par->bid = $box->id;
			$par->BBToolbar = $this->vbparams->get('activate_bbcode') ? $this->general->getBBCodeToolbar2() : '';
		}
		$par->hidden = $hidden ? ' style="display:none"' : '';
		//Template-Datei laden
		return $this->template->loadTemplate("newanswer", $par);
	}
	
	function getData($id, $limit = true, $search = '') { 
		//Data-Objekt erstellen
		$data = new JObject();
		$data->box = $this->getBox($id, "", true, $search);
		if(!$data->box) return null;
		
		$answersPerPage = (!isset($data->box->answers_per_page) || $data->box->answers_per_page <= 0) ? $this->vbparams->get('answersPerPage') : $data->box->answers_per_page;
		if($limit) $data->answers = $this->getAnswers($id, array( "search" => $search ), $this->page*$answersPerPage - $answersPerPage, $answersPerPage);
		else $data->answers = $this->getAnswers($id, array( "search" => $search ), 0, false);
		
		return $data;
	}
	
	
	private $boxes;
	private $box_curSearch = '';
	function getBox($id, $alias = "", $secure = true, $search = '') { 
		if($this->box_curSearch != $search) unset($this->boxes[(int)$id]);
		if(!isset($this->boxes[(int)$id])) { 
			$this->box_curSearch= $search;
			
			$sql = 'SELECT * FROM `#__jvotesystem_boxes` WHERE (`id` = '.($this->db->quote($id)).' OR `alias`="'.$this->db->getEscaped($alias).'") AND `published` > -1';
			$this->db->setQuery($sql);
			$box = $this->db->loadObject(); //echo str_replace("#__", "jos_", $sql);
			if(!$box) return null;
			if(!$secure) return $this->vbparams->convertBoxParams($box);
			$box = $this->vbparams->convertBoxParams($box);
			//Box-Row holen
			$sql = 'SELECT b. * , COUNT( a. `id` ) AS answers, c.`title` AS cattitle '
	        . ' FROM `#__jvotesystem_boxes` AS b '
	        . ' LEFT JOIN `#__jvotesystem_answers` AS a ON (b. `id` = a. `box_id` AND `answer` LIKE "%'.$this->db->getEscaped($search).'%"  AND ('
			. ' (a.`autor_id` = "'.$this->user->id.'" AND a.`published` = 0 AND "'.$this->user->id.'" != 0) ';
			if(!$this->access->isUserAllowedToChangePublishState($box)) $sql .= ' OR a.`published` = 1 ';  else  $sql .= ' OR a.`published` = 1 OR a.`published` = 0';
	        $sql .= ')), `#__jvotesystem_categories` AS c WHERE c.`id`=b.`catid` AND (b. `id` = '.($this->db->quote($box->id)).')';
	        if($this->access->isAdmin($box, true) || ($box->autor_id == $this->user->id)) { }
	        else $sql .= ' AND b. `published` = 1 ';
	        $sql .= ' GROUP BY b. `id` '; 
			$this->db->setQuery($sql);
			unset($box);
			$box = $this->db->loadObject(); 
			
			if($box) {
				$box->title = nl2br($box->title);
				$box->question = nl2br($box->question);
				
				//Params verarbeiten
				$box = $this->vbparams->convertBoxParams($box); 
				
				//Wenn Benutzer Ergebnis nicht sehen darf..
				$votes = $this->getVotesByUser($box->id);
				if(!$this->access->isUserAllowedToViewResult($box, $votes)) {
					//... orderBy verändern.
					if($box->answers_orderby == "votes") {
						$box->answers_orderby = "created";
						$box->answers_orderby_direction = "ASC";
					}
					//... Ranking entfernen.
					$box->activate_ranking = 0;
				}
			} //echo str_replace("#_", "jos", $sql);
			$this->boxes[(int)$id] = $box;
		}
		return $this->boxes[(int)$id];
	}
	
	public $votesByUser;
	function getVotesByUser($box, $answer = null) {
		if(empty($this->votesByUser)) $this->votesByUser = array();
		if(empty($this->votesByUser[$box])) $this->votesByUser[$box] = array();
		
		if(empty($this->votesByUser[$box][$answer])) {
			$sql = 'SELECT IFNULL(SUM(`votes`), 0) AS votes, (b.`allowed_votes` - IFNULL(SUM(`votes`), 0)) AS allowed_votes '
			. ' FROM `#__jvotesystem_answers` AS a'
			. ' LEFT JOIN `#__jvotesystem_votes` AS v ON v.`answer_id`=a.`id`'
			. ' LEFT JOIN `#__jvotesystem_boxes` AS b ON a.`box_id`=b.`id`'
			. ' LEFT JOIN `#__jvotesystem_users` AS u ON (u.`id`=v.`user_id` AND u.`blocked`=0)'
			. ' WHERE u.`id`="'.$this->user->id.'"'
			. ' AND b.`id`='.$box;
			if($answer != null) $sql .= " AND a. `id` = '$answer' ";
			$sql .= " GROUP BY b.`id` ";
			$this->db->setQuery($sql);
			$votes = $this->db->loadObject(); 
			//echo str_replace("#_", "jos", $sql); var_dump($votes);
			
			if(@$votes->allowed_votes == null && @$votes->votes == 0) {
				$votes = new JObject();
				$this->db->setQuery('SELECT `allowed_votes` FROM `#__jvotesystem_boxes` WHERE `id` = '.$this->db->quote($box).' AND `published` > -1');
				$votes->allowed_votes = (int)$this->db->loadResult();
				$votes->votes = 0;
			}
			$votes->allowed_votes = (int)$votes->allowed_votes;
			$votes->votes = (int)$votes->votes;
			
			$this->votesByUser[$box][$answer] = $votes;
		}
		return $this->votesByUser[$box][$answer];
	}
	
	public $votesByAnswer;
	function getVotesFromAnswer($answerID) {
		if(empty($this->votesByAnswer)) $this->votesByAnswer = array();
		
		if(empty($this->votesByAnswer[$answerID])) {
			$sql = 'SELECT IFNULL( SUM( `votes` ) , 0 ) AS votes '
			. ' FROM `#__jvotesystem_answers` AS a '
			. ' LEFT JOIN `#__jvotesystem_votes` AS v ON v. `answer_id` = a. `id` '
			. ' LEFT JOIN `#__jvotesystem_users` AS u ON ( u. `id` = v. `user_id` AND u. `blocked` = 0 ) '
			. ' WHERE a. `id` = '.$answerID;
			$sql .= " GROUP BY a.`id` ";
			$this->db->setQuery($sql);
			$this->votesByAnswer[$answerID] = $this->db->loadObject();
			$this->votesByAnswer[$answerID]->votes = (int) $this->votesByAnswer[$answerID]->votes;
		}
		return $this->votesByAnswer[$answerID];
	}
	
	function vote($boxID, $answerID) {
		unset($this->votesByUser[$boxID]);
		unset($this->votesByAnswer[$answerID]);
		
		$date = new JDate();
		//Schon bestehenden Vote-Eintrag abrufen
		$sql = 'SELECT v.`id`, v.`votes`'
        . ' FROM `#__jvotesystem_answers` AS a'
        . ' LEFT JOIN `#__jvotesystem_votes` AS v ON v.`answer_id`=a.`id`'
        . ' LEFT JOIN `#__jvotesystem_boxes` AS b ON a.`box_id`=b.`id`'
        . ' LEFT JOIN `#__jvotesystem_users` AS u ON (u.`id`=v.`user_id` AND u.`blocked`=0)'
        . ' WHERE a.`id`='.$answerID
        . ' AND u.`id`='.$this->user->id
        . ' AND b.`id`='.$boxID; 
		$this->db->setQuery($sql);
		$result = $this->db->loadObject();
		if(!isset($result->votes)) {
			//Neuen Eintrag erstellen
			$nV = new JObject();
			$nV->id = null;
			$nV->user_id = $this->user->id;
			$nV->answer_id = $answerID;
			$nV->votes = 1;
			$nV->voted_time = $date->toMySQL();
			
			$this->db->insertObject('#__jvotesystem_votes', $nV);
			
			//other extensions
			JPluginHelper::importPlugin( 'jvotesystem' );
			$dispatcher =& JDispatcher::getInstance();
			$res = $dispatcher->trigger( 'onAnswerVoted', array( $answerID ) );
			
			$this->log->add("DB", 'AddedVoting', array("bid" => $boxID, "aid" => $answerID));
		} else {
			//Alten Eintrag updaten
			$nV = new JObject();
			$nV->id = $result->id;
			$nV->votes = $result->votes + 1;
			$nV->voted_time = $date->toMySQL();
			
			$this->db->updateObject('#__jvotesystem_votes', $nV, 'id');
			$this->log->add("DB", 'UpdatedVoting', array("bid" => $boxID, "aid" => $answerID, "votes" => $result->votes + 1));
		}
		return $nV->votes;
	}
	
	function checkVote($boxID, $answerID, $voteown=false) {
		$this->user->loadUser(true);
	
		$out = array();
		//Variablen überprüfen
		if($boxID == null OR $answerID == null) {
			$out[] = array("key"=>"erfolg","value"=>0);
			$out[] = array("key"=>"error","value"=>JText::_('ERRORVOTE'));
			$this->log->add("ERROR", 'VotingMissingParameters', array("bid" => $boxID, "aid" => $answerID));
			return $out;
		}
		//Box & Answer laden
		$box = $this->getBox($boxID);
		$answer = VBAnswer::getAnswer($answerID);
		if(!$box OR !$answer OR $this->user->id == 0) {
			$out[] = array("key"=>"erfolg","value"=>0);
			$out[] = array("key"=>"error","value"=>JText::_('ERRORVOTE'));
			$this->log->add("ERROR", 'VotingNoBoxOrAnswerFound', array("bid" => $boxID, "aid" => $answerID));
			return $out;
		}
		//User laden
		$jUser = JFactory::getUser($this->user->jid);
		//User blocked
		if($jUser->block == 1 or $this->user->blocked == 1) {
			$out[] = array("key"=>"erfolg","value"=>0);
			$out[] = array("key"=>"error","value"=>JText::_('USERBLOCKED'));
			$this->log->add("NOTICE", 'BlockedUserTriedToVote', array("bid" => $boxID, "aid" => $answerID));
			return $out;
		}
		//Rechte überprüfen
		if(!$this->access->checkAccessGroup('access', $box)) {
			$out[] = array("key"=>"erfolg","value"=>0);
			$out[] = array("key"=>"error","value"=>JText::_('NOVOTERIGHTS'));
			$this->log->add("NOTICE", 'UserTriedToVoteWithoutAccess', array("bid" => $boxID, "aid" => $answerID));
			return $out;
		}
		//Answer von Benutzer erstellt?
		if($answer->autor_id == $this->user->id AND $voteown == false AND $this->vbparams->get('voteOnOwn') == 0) {
			$out[] = array("key"=>"erfolg","value"=>0);
			$out[] = array("key"=>"error","value"=>JText::_('NOVOTEONOWN'));
			$this->log->add("NOTICE", 'UserTriedToVoteOnOwn', array("bid" => $boxID, "aid" => $answerID));
			return $out;
		}
		
		$aVotes = $this->getVotesFromAnswer($answerID);
		//Vote erlaubt?
		$votes = $this->getVotesByUser($boxID);
		if($votes->allowed_votes > 0) {
			//Max-Votes pro Antwort
			if($box->allowed_votes > $box->max_votes_on_answer) {
				$votesA = $this->getVotesByUser($boxID, $answerID);
				
				if($votesA->votes >= $box->max_votes_on_answer) {
					$out[] = array("key"=>"erfolg","value"=>0);
					if($this->access->isUserAllowedToViewResult($box, $votes)) $out[] = array("key"=>"totalVotes","value"=>$aVotes->votes);
					$out[] = array("key"=>"leftVotes","value"=>$votes->allowed_votes);
					$out[] = array("key"=>"error","value"=>sprintf(JText::_('VOTELIMITMAX'), $box->max_votes_on_answer));
					$this->log->add("NOTICE", 'UserReachedVoteLimitPerAnswer', array("bid" => $boxID, "aid" => $answerID, "limit" => $box->max_votes_on_answer));
					return $out;
				}
			}
			//ACCESS überprüfen
			if(!$this->access->isUserAllowedToVoteAnswer($box, $answer, $voteown)) {
				$out[] = array("key"=>"erfolg","value"=>0);
				$out[] = array("key"=>"error","value"=>JText::_('NOVOTERIGHTS'));
				$this->log->add("NOTICE", 'UserTriedToVoteWithoutRights', array("bid" => $boxID, "aid" => $answerID, "voteOwn" => $voteown));
				return $out;
			}
			$userVotes = $this->vote($box->id, $answer->id);
			
			if($userVotes != false) {
				$out[] = array("key"=>"userVotes","value"=>$userVotes);
				$out[] = array("key"=>"leftVotes","value"=>$votes->allowed_votes - 1);
				if ($box->max_votes_on_answer == $userVotes) { //falls maximale anzahl antworten pro antwort erreicht, dann keyword zum deaktivieren (per JS) der Antwort ausgeben.
					$out[] = array("key"=>"disableanswer","value"=>true);
				}
				$out[] = array("key"=>"erfolg","value"=>1);
			} else {
				$out[] = array("key"=>"erfolg","value"=>0);
				$out[] = array("key"=>"leftVotes","value"=>$votes->allowed_votes);
				$out[] = array("key"=>"error","value"=>JText::_('ERRORVOTEMYSQL'));
			}
		} else {
			$out[] = array("key"=>"erfolg","value"=>0);
			$out[] = array("key"=>"leftVotes","value"=>$votes->allowed_votes);
			if($this->access->isUserAllowedToViewResult($box, $votes)) $out[] = array("key"=>"totalVotes","value"=>$aVotes->votes);
			$out[] = array("key"=>"error","value"=>JText::_('VOTELIMIT'));
			$this->log->add("NOTICE", 'UserReachedVoteLimit', array("bid" => $boxID, "aid" => $answerID, "left" => $votes->allowed_votes));
		}
		//TotalVotes
		$aVotes = $this->getVotesFromAnswer($answer->id);
		if($this->access->isUserAllowedToViewResult($box, $votes)) $out[] = array("key"=>"totalVotes","value"=>$aVotes->votes);
		return $out;
	}
	
	function getBanner($noScript, $box) {
		if(!$this->vbparams->get('adsense') OR $this->vbparams->get('adsense_key') == "") return '';
		
		//Parameter festlegen
		$par = new JObject();
			$par->translation_advert = JText::_('Anzeige');
			$par->activate_ranking = $box->activate_ranking;
			$pars = array("adsense_key" => $this->vbparams->get('adsense_key'), "load" => true);
			$par->script = $noScript ? "" : $this->template->loadTemplate("banner_code", JArrayHelper::toObject($pars));
	
		//Template-Datei laden
		$out = $this->template->loadTemplate("banner", $par);
		
		return $out;
	}

	function getPageLink($i, $onlyAnswers, $page, $linkOJ, $name = null) 
	{ 
		//ohne JS
		$link = 'href="'.$linkOJ.$i.'" ';
		$data = 'data-p="'.$i.'"';
		if($onlyAnswers OR $i == null OR $linkOJ == null) {$link = '';}
		//Link
		$class = '';
		if($i == $page) $class = 'class="pageSelected" ';
		elseif($i == null) {$class = 'class="pageNull" ';$data = '';}
		$out = '<a '.$class.$link.$data.'>';
		$out .= ($name != null) ? $name : $i;
		$out .= '</a>';
				
		return $out;
	}
	
	function buildnavi ($answers, $ajax, $page, $answersperpage, $maxdisplaypages, $uri = null, $type) {
		if( $answers > $answersperpage || ($this->template->getTemplate() == 'module' && $type == "answers")) {
			//Link für ohne JS auswerten bzw. erstellen
			$linkOJ = $uri;
			if ( $linkOJ != null ) {
				$linkOJsplit = explode('jVSPage',$linkOJ);
				$linkOJ = $linkOJsplit[0];
				if(isset($linkOJsplit[1])) $linkOJ = substr($linkOJ, 0, -1);
				$linkOJparse = parse_url($linkOJ);
				if(isset($linkOJparse["query"])) $linkOJ .= '&jVSPage=';
				else $linkOJ .= '?jVSPage=';
			}
			
			$pages = (int) ceil($answers/$answersperpage);
			
			//Variablen-Button
			$buttons = new JObject();
			
			$buttons->prev = $page == 1 ? false : true;
			$buttons->start = false;
			$buttons->end = false;
			$buttons->next = ($page*$answersperpage) >= $answers ? false : true;
			
			if($pages > $maxdisplaypages) {
				//Seitenzahl-Anzeige abkürzen
				if($page > (floor($maxdisplaypages/2) + 2)) {
					if($page <= $pages AND $page >= ($pages - (floor($maxdisplaypages/2) + 2))) {
						$buttons->start = true;
						$i = $pages - $maxdisplaypages + 1;
						$end = $pages;
					} else {
						$buttons->start = true;
						$i = $page - floor($maxdisplaypages/2);
						$end = $page + floor($maxdisplaypages/2);
						$buttons->end = true;
					}
				} else {
					$i = 1;
					$end = $maxdisplaypages;
					$buttons->end = true;
				}
			} else {
				//Alle Seitenzahlen anzeigen
				$i = 1;
				$end = $pages;
			}
			
			$par = new JObject();
			$par->type = $type;
			$par->id = $this->id;
			
			if($buttons->prev == true) {
				$par->prev = $this->getPageLink($page-1, $ajax, $page, $linkOJ, JText::_('Vor'));
			}
			$par->main = '';
			if($buttons->start == true) {
				$par->main .= $this->getPageLink(1, $ajax, $page, $linkOJ, JText::_('JVS_NAVI_Start'));
				$par->main .= $this->getPageLink(($page > $maxdisplaypages ? ($page - $maxdisplaypages) : null), $ajax, $page, $linkOJ, '...');
			}
			for($i = $i; $i <= $end; $i++) {
				$par->main .= $this->getPageLink($i, $ajax, $page, $linkOJ);
			}
			if($buttons->end == true) {
				$par->main .= $this->getPageLink(($pages > ($page + $maxdisplaypages) ? ($page + $maxdisplaypages) : null), $ajax, $page, $linkOJ, '...');
				$par->main .= $this->getPageLink($pages, $ajax, $page, $linkOJ, JText::_('JVS_NAVI_End'));
			}
			if($buttons->next == true) {
				$par->next = $this->getPageLink($page+1, $ajax, $page, $linkOJ, JText::_('Weiter'));
			}
			$navi = $this->template->loadTemplate("navi", $par);
		} else {
			$navi = '';
		}
		return $navi;
	}
	
	private $max_popular_votes;
	function getMaxPopularVotes() {
		if(!isset($this->max_popular_votes)) {
			$this->db->setQuery("	SELECT SUM(v.`votes`) AS votes
					FROM `#__jvotesystem_answers` AS a, `#__jvotesystem_votes` AS v
					WHERE v.`answer_id`=a.`id`
					AND v.`voted_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY)
					GROUP BY a.`box_id`
					ORDER BY `votes` DESC
					LIMIT 0,1
					");
			$this->max_popular_votes = $this->db->loadResult();
			if(!$this->max_popular_votes) $this->max_popular_votes = 1;
		}
		return $this->max_popular_votes;
	}
	
	private $max_total_votes;
	function getMaxTotalVotes() {
		if(!isset($this->max_total_votes)) {
			$this->db->setQuery("	SELECT SUM(v.`votes`) AS votes
					FROM `#__jvotesystem_answers` AS a, `#__jvotesystem_votes` AS v
					WHERE v.`answer_id`=a.`id`
					GROUP BY a.`box_id`
					ORDER BY `votes` DESC
					LIMIT 0,1
					");
			$this->max_total_votes = $this->db->loadResult();
			if(!$this->max_total_votes) $this->max_total_votes = 1;
		}
		return $this->max_total_votes;
	}
	
	function getPolls($filter = array(), $start = 0, $limit = null) {
		$needPopular = false;
		$needStats = false;
		if(@$filter["stats"] == true) $needStats = true;
		if(@$filter["order"] == "popular") {
			$needPopular = true;
			$needStats = true;
		} elseif(@$filter["order"] == "most-voted" || @$filter["order"] == "most-discussed") {
			$needStats = true;
		}
		
		$sql = "SELECT b.*";
		if($needStats) $sql .= ", IFNULL(aStats.votes, 0) AS votes, IFNULL(cStats.comments,0) AS comments "; 
		if($needPopular) $sql .= ", (IFNULL(popStats.votes, 0)/{$this->getMaxPopularVotes()})*50 + (IFNULL(aStats.votes, 0)/{$this->getMaxTotalVotes()})*50 AS popular ";
		$sql.= "FROM `#__jvotesystem_boxes` AS b";
		if($needStats) $sql .= "
				LEFT JOIN (
					SELECT a.`box_id`, SUM(v.`votes`) AS votes
					FROM `#__jvotesystem_answers` AS a
					LEFT JOIN `#__jvotesystem_votes` AS v ON (v.`answer_id`=a.`id`)
					GROUP BY a.`box_id`
				) AS aStats ON(aStats.box_id=b.`id`) ";
		if($needPopular) $sql .= "
				LEFT JOIN (
					SELECT a.`box_id`, SUM(v.`votes`) AS votes
					FROM `#__jvotesystem_answers` AS a, `#__jvotesystem_votes` AS v
					WHERE v.`answer_id`=a.`id`
					AND v.`voted_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY)
					GROUP BY a.`box_id`
				) AS popStats ON(popStats.box_id=b.`id`) ";
		if($needStats) $sql.= "
				LEFT JOIN (
					SELECT a.`box_id`, COUNT(c.`id`) AS comments
					FROM `#__jvotesystem_answers` AS a
					LEFT JOIN `#__jvotesystem_comments` AS c ON (c.`answer_id`=a.`id`)
					GROUP BY a.`box_id`
				) AS cStats ON(cStats.box_id=b.`id`)";
		$sql .= ", `#__jvotesystem_categories` AS c
				WHERE c.`published`=1 AND c.`id`=b.`catid` AND b.`published`>= 0 ";
		if(!$this->access->isUserAllowedToConfig()) $sql .= ' AND b.`published`=1 ';
		//Excludes
		if(!empty($filter["excludes"])) {
			$sql .= " AND (";
			foreach($filter["excludes"] AS $i => $exclude) {
				if($i != 0) $sql .= " AND ";
				$sql .= "b.`id` != '".$exclude."'";
			}
			$sql .= ") ";
		}
		//Keyword
		if(isset($filter["keyword"]))
			$sql .= " AND (b.`title` LIKE '%".$this->db->getEscaped($filter["keyword"])."%' OR b.`question` LIKE '%".$this->db->getEscaped($filter["keyword"])."%') ";
		//Time
		if(isset($filter["time"])) {
			switch(strtolower($filter["time"])) {
				case "today": 	$sql .= "AND DATE_SUB(CURDATE(),INTERVAL 1 DAY) < b.`created` "; break;
				case "week": 	$sql .= "AND DATE_SUB(CURDATE(),INTERVAL 1 WEEK) < b.`created` "; break;
				case "month": 	$sql .= "AND DATE_SUB(CURDATE(),INTERVAL 1 MONTH) < b.`created` "; break;
			}
		}
		//Categories
		if(isset($filter["cid"]) AND @$filter["cid"] != 0) {
			$cat = $this->category->getCategory($filter["cid"]);
			if($cat->id != 0) $cats = array($cat->id);
			else $cats = array();
			
			if(!isset($filter["subcats"])) {
				//Wenn Subkategorien
				$params = $this->vbparams->getActiveMenuParams();
				
				$filter["subcats"] = $params->get("subcats", 1);
			}
			
			if($filter["subcats"]) {
				$cats = array_merge($cats, $this->category->getCategoryChilds($filter["cid"], 0));
			}
			//Alle Umfragen mit den IDS
			$sql .= " AND (";
			foreach($cats AS $i => $cat) {
				if($i != 0) $sql .= " OR ";
				$sql .= "b.`catid`='".$cat."'";
			}
			$sql .= ") ";
		}
		//Zugriff erlaubt
		$user = JFactory::getUser();
		if(version_compare( JVERSION, '1.6.0', 'lt' )) {
			$sql .= " AND c.`accesslevel` <= '".$user->gid."' ";
		} else {
			$levels = $user->getAuthorisedViewLevels();
			
			$sql .= " AND (";		
			foreach($levels AS $i => $level) {
				if($i != 0) $sql .= " OR ";
				$sql .= "c.`accesslevel`='".$level."'";
			}
			$sql .= ") ";
		}
		//Order
		switch(@$filter["order"]) {
			case 'popular': $sql .= "ORDER BY popular DESC, aStats.votes DESC "; break;
			case "most-voted": $sql .= "ORDER BY aStats.votes DESC "; break;
			case 'newest':
			case "recent": 
				$sql .= "ORDER BY b.`created` DESC "; 
				break;
			case 'oldest': $sql .= "ORDER BY b.`created` ASC "; break;
			case "most-discussed": $sql .= "ORDER BY cStats.comments DESC "; break;
			case "random": $sql .= "ORDER BY RAND() DESC "; break;
			case 'alpha': $sql .= "ORDER BY b.`title` ASC "; break;
		} 
		
		//Limit
		if($limit == null) $limit = $this->vbparams->get("pollsPerPage");
		if(isset($filter["page"])) {
			$start = ($filter["page"]-1)*$this->vbparams->get("pollsPerPage");
			
			$this->db->setQuery($sql);
			$polls = $this->db->loadObjectList(); //echo nl2br(str_replace("#_", "jos", $this->db->getQuery()));
			$this->numRows = count($polls);
			
			if($this->numRows > 0) {
				while($start > $this->numRows) {
					$start -= $this->vbparams->get("pollsPerPage");
				}
				return array_slice($polls, $start, $limit);
			} else {
				return array();
			}	
			
		} else {
			if($limit != -1) $sql .= " LIMIT $start, $limit";
			
			$this->db->setQuery($sql);
			$polls = $this->db->loadObjectList(); //echo nl2br(str_replace("#_", "jos", $this->db->getQuery()));
		}
		
		return $polls;
	}
	
	function getAnswers($id, $filter = array(), $start = 0, $limit = 100) {
		$box = $this->getBox($id);
		if(!$box) return null;
		
		$sql = array();
		
		//Container Table
		$sql[] = '	SELECT result.* FROM (';
			//Counter for Rank
			$sql[] = '	SELECT (@counter:=(@counter+1)) AS counter, IF(`published` = 1, (@rcounter:=(@rcounter+1)), "#") AS rank, resultRank.* FROM (SELECT @rcounter:=0)rc, (SELECT @counter:=0)r,(';
				//Answers with Votes & Users
				$sql[] = '	SELECT a.*, IFNULL(SUM(v.`votes`),0) AS votes, MAX(v.`voted_time`) AS lastvote, MAX(v.`voted_time`) AS firstvote ';
				//Exact match? top ordering
				$sql[] = '	, IF(`answer` = "'.$this->db->getEscaped(@$filter["search"]).'", 1, 0) AS exact_match ';
				//From tables
				$sql[] = '	FROM `#__jvotesystem_answers` AS a
							LEFT JOIN `#__jvotesystem_votes` AS v ON (v.`answer_id`=a.`id`)
							LEFT JOIN `#__jvotesystem_users` AS u ON (u.`id`=v.`user_id` AND u.`blocked`=0)';
				//Only answers of the poll
				$sql[] = '	WHERE a.`box_id`='.$this->db->quote($box->id).' AND (';
					//When the user is the author of answer, show also unpublished answers
					if($this->user->id != 0)
						$sql[] = '	(a.`autor_id` = "'.$this->user->id.'" AND a.`published` = 0 ) OR';
					//Check access rights of the user.. without special rights => allow only published answers
					if(!$this->access->isUserAllowedToChangePublishState($box) || @$filter["only_published"] == true) 
						$sql[] = '	a.`published` = 1 ';  
					else  
						$sql[] = '	a.`published` = 1 OR a.`published` = 0';
				$sql[] = '	)';
				//Group Answers
				$sql[] = '	GROUP BY a.`id`';
				//Order (Rank)
				if(!isset($filter["order_rank"])) 			$filter["order_rank"] = $box->ranking_orderby;
				if(!isset($filter["order_rank_direction"])) $filter["order_rank_direction"] = $box->ranking_orderby_direction;
				$sql[] = $this->general->getSqlOrderBy($filter["order_rank"], $filter["order_rank_direction"]);
			$sql[] = '	) AS resultRank ';
		$sql[] = '	) AS result WHERE 1 = 1 ';
				//Excludes
				if(!empty($filter["excludes"])) {
					$sql[] = " AND (";
					foreach($filter["excludes"] AS $i => $exclude) {
						if($i != 0) $sql[] = " AND ";
						$sql[] = "`id` != '".$exclude."'";
					}
					$sql[] = ") ";
				}
				//Answers filter
				if(!empty($filter["answers"])) {
					if(!is_array($filter["answers"])) $filter["answers"] = array( $filter["answers"] );
					$sql[] = " AND (";
					foreach($filter["answers"] AS $i => $include) {
						if($i != 0) $sql[] = " OR ";
						$sql[] = "`id` = '".$include."'";
					}
					$sql[] = ") ";
				}
			//Search
			if(isset($filter["search"])) {
				$sql[] = " AND `answer` ";
				switch(@$filter["search_mode"]) {
					case 'sensitive':
						$sql[] = " = '".$this->db->getEscaped($filter["search"])."' ";						
						break;					
					default: //insensitive
						$sql[] = " LIKE '%".$this->db->getEscaped($filter["search"])."%' ";
						break;
				}
			}
				
		//Order
		if(!isset($filter["order"])) 			$filter["order"] = $box->answers_orderby;
		if(!isset($filter["order_direction"])) 	$filter["order_direction"] = $box->answers_orderby_direction;
		$sql[] = $this->general->getSqlOrderBy($filter["order"], $filter["order_direction"], ' `exact_match` DESC ');
		//Limit
		if($limit != false)
			$sql[] = "	LIMIT $start, $limit ";
		
		$this->db->setQuery(implode(" ", $sql)); //echo str_replace("#__", "jos_", implode("<br>", $sql));
		$data = $this->db->loadObjectList(); //var_dump($data);

		if(empty($data)) return $data;
		
		//JoomFish-Support
		if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_joomfish')) {
			$where = "";
			foreach($data AS $answer) {
				if($where != "") $where .= " OR ";
				$where .= " `id`=".$answer->id;
			}
			$sql = ' SELECT `id`, `answer` '
			. ' FROM `#__jvotesystem_answers` ';
			if($where != "") {
				$sql .= ' WHERE '.$where;
		
				$this->db->setQuery($sql);
				$aTranslations = $this->db->loadAssocList();
		
				//Array umsortieren
				$aTranslationsNew = array();
				foreach($aTranslations AS $translation) {
					$aTranslationsNew[$translation["id"]] = $translation["answer"];
				}
		
				//Daten ersetzen
				foreach($data AS &$answer) {
					if(isset($aTranslationsNew[$answer->id])) $answer->answer = $aTranslationsNew[$answer->id];
				}
			}
		}
		
		return $data;
	}
	
	function getNuwRows() {
		return $this->numRows;
	}
	
	function editPollState($id, $state) {
		$upd = new JObject();
		
		$upd->id = $id;
		$upd->published = $state;
		
		$this->db->updateObject('#__jvotesystem_boxes', $upd, 'id');
		if($this->db->getErrorMsg()) {
			$this->log->add("ERROR", 'ChangingPublishStatePoll', array("id"=>$id, "state"=>$state, "db_error"=>$this->db->getErrorMsg())); return false;
		}
					
		JFactory::getCache()->clean('jVoteSystem - Lists');
		
		//Tasks
		$tasks =& VBTasks::getInstance();
		$tasks->removeTask(VBTasks::$_Poll, $id);
		//$tasks->removeTask(VBTasks::$_Spam_Poll, $id);
		
		$this->log->add("DB", 'ChangedStatePoll', array("id"=>$id, "state"=>$state));
		return true;
	}
	
	function removePoll($id) {
		$text = @$this->getBox($id)->title;
		
		$sql = 'DELETE FROM `#__jvotesystem_boxes` '
		. ' WHERE `id` = '.$id
		. ' LIMIT 1';
		$this->db->setQuery($sql);
		$this->db->query();
		if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingPollEntry', array("id"=>$id, "db_error"=>$this->db->getErrorMsg())); return false; }
		//Answers laden
		$sql = 'SELECT `id` '
		. ' FROM `#__jvotesystem_answers` '
		. ' WHERE `box_id` = '.$id;
		$this->db->setQuery($sql);
		$answers = $this->db->loadObjectList();
		//Answers und Votes löschen
		$canswers = 0;
		$votes = 0;
		$comments = 0;
		$spam_reports = 0;
		foreach($answers AS $answer) {
			$sql = 'DELETE FROM `#__jvotesystem_answers` '
			. ' WHERE `id` = '.$answer->id;
			$this->db->setQuery($sql);
			$this->db->query();
			$canswers++;
			if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingAnswerOfPoll', array("id"=>$id, "aid" => $answer->id, "db_error"=>$this->db->getErrorMsg())); return false; }
			//Votes löschen
			$sql = 'DELETE FROM `#__jvotesystem_votes` '
			. ' WHERE `answer_id`='.$answer->id;
			$this->db->setQuery($sql);
			$this->db->query();
			$votes += $this->db->getAffectedRows();
			if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingVotesOfPoll', array("id"=>$id, "aid" => $answer->id, "db_error"=>$this->db->getErrorMsg())); return false; }
			//Kommentare löschen
			$sql = 'DELETE FROM `#__jvotesystem_comments` '
			. ' WHERE `answer_id` = '.$answer->id;
			$this->db->setQuery($sql);
			$this->db->query();
			$comments += $this->db->getAffectedRows();
			if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingCommentsOfPoll', array("id"=>$id, "aid" => $answer->id, "db_error"=>$this->db->getErrorMsg())); return false; }
			//Spam-Reports entfernen
			$sql = 'DELETE FROM `#__jvotesystem_spam_reports` '
			. ' WHERE `block_group`="answer" AND `block_id` = '.$answer->id;
			$this->db->setQuery($sql);
			$this->db->query();
			$spam_reports += $this->db->getAffectedRows();
			if($this->db->getErrorMsg()) { $this->log->add("ERROR", 'RemovingSpamReportsOfPoll', array("id"=> (int)$id, "aid" => $answer->id, "db_error"=>$this->db->getErrorMsg())); return false; };
		}
		
		$this->log->add("DB", 'RemovedPoll', array("id"=> $id, "title" => $text, "answers" => $canswers, "votes"=>$votes, "comments"=>$comments, "spam_reports" => $spam_reports));
		
		JFactory::getCache()->clean('jVoteSystem - Lists');
		
		//Tasks
		$tasks =& VBTasks::getInstance();
		$tasks->removeTask(VBTasks::$_Poll, $id);
		//$tasks->removeTask(VBTasks::$_Spam_Poll, $id);
		
		return true;
	}
	
	function addDefaultSettingsBox($id) {
		if(joomessLibrary::getInstance()->getJoomlaVersion() == joomessLibrary::jVersion15) {
			$access = '{"access":"0","result_access":"0","admin_access":"23","add_answer_access":"18","add_comment_access":"18"}';
		} else {
			$access = '{"access":["1","2","3","4","5","6","7","8"],"result_access":["1","2","3","4","5","6","7","8"],"admin_access":["6","7","8"],"add_answer_access":["2","3","4","5","6","7","8"],"add_comment_access":["2","3","4","5","6","7","8"]}';
		}	
		
		$sql = "INSERT INTO `#__jvotesystem_boxes` (`id`, `catid`, `title`, `question`, `alias`, `access`, `published`, `ordering`, `allowed_votes`, `add_answer`, `add_comment`, `created`, `autor_id`, `start_time`, `end_time`, `params`) VALUES
			(NULL, ".$id.", '', '', '', '".$access."', -1, 0, 3, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '{\"max_votes_on_answer\":\"2\",\"show_thankyou_message\":\"1\",\"goto_chart\":\"1\",\"show_result\":\"always\",\"show_result_after_date\":\"0000-00-00 00:00:00\",\"activate_ranking\":\"1\",\"ranking_orderby\":\"votes\",\"ranking_orderby_direction\":\"DESC\",\"answers_orderby\":\"votes\",\"answers_orderby_direction\":\"DESC\",\"show_author\":\"1\",\"template\":\"default\",\"chart_type\":\"both\",\"send_mail_admin_answer\":\"1\",\"send_mail_user_answer_comments\":\"1\",\"send_mail_admin_comment\":\"1\",\"send_mail_user_comment_comments\":\"1\",\"activate_spam\":\"1\",\"spam_count\":\"5\",\"spam_mail_admin_report\":\"1\",\"spam_mail_admin_ban\":\"1\"}');";
		$this->db->setQuery($sql);
		if(!$this->db->query()) return false;
		
		return true;
	}
}
