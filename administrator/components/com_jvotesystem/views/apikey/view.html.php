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

class jVoteSystemViewApiKey extends JView
{
    /**
     * jVoteSystemList view display method
     * @return void
     **/
    function display($tpl = null)
    {        
		$this->general =& VBGeneral::getInstance();
		$this->charts =& VBCharts::getInstance();
		$this->api =& VBApi::getInstance();
		
		//Variablen laden
		$editor 	= & JFactory::getEditor();
		$document	= & JFactory::getDocument();
		$user 		= & JFactory::getUser();
		
		//Daten laden
		$item = $this->get('Data');
		
		$isNew = (!isset($item));
        $text = $isNew ? JText::_('New') : JText::_('Edit');
		
		//Toolbar
		JToolBarHelper::title(JText::_('JVS_API_KEY').': <small><small>[ '.$text.' ]</small></small>');
		JToolBarHelper::save();
		JToolBarHelper::apply();
        if($isNew) JToolBarHelper::cancel();
        else JToolBarHelper::cancel('cancel', JText::_('Close'));
        
        //Lists erstellen
        $lists = new JObject();
        $lists->tasks = $this->api->getTaskList();
        $lists->limit_types = array(
        			"hour",
        			"day",
        			"week",
        			"month"
        		);
		
		if($isNew) {
			$item = new JObject();       	        	
        	$item->key = null;
        	$item->params = new JObject();
        	$item->params->tasks = array();
        		foreach($lists->tasks AS $task)
        			$item->params->tasks[] = $task;
        	$item->params->limit = 1000;
        	$item->params->limit_type = "week";
        }
		
		//Charts
		if(!$isNew) {
			//$this->charts->addchartjs('corechart');
			//$answerStats = $this->charts->getBackendChart("answerStats", $item->id);
		}
		
		//Daten übergeben
		$this->assignRef('new' , $isNew);
		$this->assignRef('item' , $item);
		$this->assignRef('stats', $answerStats);
		$this->assignRef('lists', $lists);

        parent::display($tpl);
    }//function

}//class
