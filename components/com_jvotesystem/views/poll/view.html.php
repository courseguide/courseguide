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

jimport( 'joomla.application.component.view');

class jVoteSystemViewPoll extends JView
{
    function display($tpl = null)
    {
		//Klassen laden
		$params =& VBParams::getInstance('poll', true);
		$general =& VBGeneral::getInstance();
		$mainframe = &JFactory::getApplication();
		$pathway   =& $mainframe->getPathway();
		$lib =& joomessLibrary::getInstance();
		
		$id = JRequest::getInt('bid', null);
		if($id == null) $id = JRequest::getInt('id', null);
		$alias = JRequest::getString("alias", "");
		
		$vote =& VBVote::getInstance();
		$out = "";
		
		//Box laden
		$box = $vote->getBox($id, $alias);
		$page = null;
		//Unbekannte ID
		if(!$box) { 
			//Wenn Box wirklich nicht vorhanden oder deaktiviert => Meldung anzeigen
			$template =& VBTemplate::getInstance();
			$template->setTemplate("default");
			//Parameter
			$par = new JObject();
			$par->msg = sprintf(JText::_("NOBOXFOUNDORPUBLISHED"), $id.$alias);
			$par->type = "error";
			
			//laden
			$this->out = '<div class="jvotesystem jvs-default">'.$template->loadTemplate("notification", $par).'</div>';
			
			parent::display($tpl);
			return ;
		}
		
		$template =& VBTemplate::getInstance();
		
		//Hinweis ausgeben
		$notifi = JRequest::getString("notifi", "");
		if($notifi != "") {
			
			switch($notifi) {
				case "add_success":
					//Parameter
					$par = new JObject();
					$par->msg = JText::_("JVS_NOTICE_CREATED_POLL");
					$par->type = "success";
					
					$out .= '<div class="jvotesystem jvs-'.$template->getTemplate().'">'.$template->loadTemplate("notification", $par).'</div>';
					break;
			}
		}
		
		//Box nicht öffentlicht (Hinweis an Admin oder Autor)
		if($box->published == 0) {			
			//Parameter
			$par = new JObject();
			$par->msg = (VBAccess::getInstance()->isAdmin($box, true)) ? JText::_("JVS_ADMIN_POLL_NOT_PUBLISHED") : JText::_("JVS_AUTOR_POLL_NOT_PUBLISHED");
			$par->type = "notice";
				
			//laden
			$out .= '<div class="jvotesystem jvs-'.$template->getTemplate().'">'.$template->loadTemplate("notification", $par).'</div>';
		}
		
		//Title setzen
		$doc =& JFactory::getDocument();
		$doc->setTitle($box->title);
		
		$menu = $params->getActiveMenu();
		$menu_params = $params->getActiveMenuParams();
		
		if(@$menu->query["view"] == "polls") {
			$cat = VBCategory::getInstance()->getCategory($box->catid);
			if($cat->alias != $menu_params->get("cat", ""))$pathway->addItem($cat->title, $general->buildLink("category", $cat->id));
			$pathway->addItem($box->title, $general->buildLink("poll", $box->id));
		} elseif(@$menu->query["view"] == "poll") {}
		else
			$pathway->addItem(JText::_("Poll").": ".$box->title, $general->buildLink("poll", $box->id));
		
		$aid = JRequest::getInt('aid', null);
		$out .= $vote->getVoteBox($box->id, false, $page, false);
		
		/* The copyright information may not be removed or made invisible! To remove the code, please purchase a version on www.joomess.de. Thanks!*/
		joomessLibrary::getInstance()->copyright('jVoteSystem', $out);
		
		//Wenn Variable aid gesetzt, wird die dazu angegebene Antwort geladen
		if($aid != null) {
			$vbanswer =& VBAnswer::getInstance();
			$page = $vbanswer->getAnswersPageCount($box, $aid); 
			JUri::setFragment("vb".$box->id."answer".$aid);
			
			//Metadaten zur Antwort
			if($answer = $vbanswer->getAnswer($aid)) {
				$path = VBTemplate::getInstance()->getTemplatePath("main").DS."answerHeadData.php";
				if(JFile::exists($path)) @require $path;
			}
		} else {
			//Metadaten zur Umfrage
			$general->setHeadData(
				JText::_('Poll').': '.$box->title,
				$general->buildLink("poll", $box->id, "", array( "ref" => "social" ), false, false),
				$lib->root().'/components/com_jvotesystem/assets/images/icon-100-jvotesystem.png',
				$box->question
			);
		}
		
		$this->assignRef('out', $out);
		$this->assignRef('box', $box);
		
        parent::display($tpl);
    }//function
    
}//class

