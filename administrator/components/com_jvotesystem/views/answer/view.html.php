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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jVoteSystem Component
 *
 * @package    jVoteSystem
 * @subpackage Views
 */

class jVoteSystemViewAnswer extends JView
{
    /**
     * jVoteSystemList view display method
     * @return void
     **/
    function display($tpl = null)
    {        
		$this->general =& VBGeneral::getInstance();
		$this->charts =& VBCharts::getInstance();
		
		//Variablen laden
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$user 		= & JFactory::getUser();
		
		//Daten laden
		$item = $this->get('Data');
		
		$isNew = (!isset($item));
        $text = $isNew ? JText::_('New') : JText::_('Edit');
		
		//Toolbar
		JToolBarHelper::title(JText::_('Answer').': <small><small>[ '.$text.' ]</small></small>');
		JToolBarHelper::save();
		JToolBarHelper::apply();
        if($isNew) JToolBarHelper::cancel();
        else JToolBarHelper::cancel('cancel', JText::_('Close'));
		
		//Umfragen holen
		$lists = new JObject();
		$lists->polls = $this->get('Polls');
		if($isNew) $lists->id = JFactory::getSession()->get('bid', '', 'jVS_Admin_Filter');
		else $lists->id = $item->box_id;
		$votes = $this->get('Votes');
		$comments = $this->get('Comments');
        
        if($isNew) {
        	$poll = VBVote::getInstance()->getBox($lists->id);
        	        	
        	$item->id = null;
        	$item->color = ($poll) ? $this->general->getColorCode($poll->cur_color_index) : $this->general->getColorCode();
        	$item->answer = "";
        	$item->published = true;
        	$item->no_spam_admin = true;
        }
		
		//Charts
		if(!$isNew) {
			$this->charts->addchartjs('corechart');
			$answerStats = $this->charts->getBackendChart("answerStats", $item->id);
		}
		
		//Daten übergeben
		$this->assignRef('new' , $isNew);
		$this->assignRef('item' , $item);
		$this->assignRef('votes' , $votes);
		$this->assignRef('comments' , $comments);
		$this->assignRef('lists', $lists);
		$this->assignRef('stats', $answerStats);

        parent::display($tpl);
    }//function

}//class
