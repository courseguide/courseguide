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

class jVoteSystemViewBbcode extends JView
{
    /**
     * jVoteSystemList view display method
     * @return void
     **/
    function display($tpl = null)
    {        
		//Variablen laden
		$document	= & JFactory::getDocument();
		$this->general =& VBGeneral::getInstance();
		
		//Daten laden
		$item = $this->get('Data');
		
		$isNew = (!$item) ? true : false;
        $text = $isNew ? JText::_('New') : JText::_('Edit');
        
        if($isNew) {
        	$item->id = 0;
        	$item->name = "";
        	$item->regex = "";
        	$item->replace = "";
        	$item->replaceNot = "";
        	$item->withButton = false;
        	$item->buttonInfo = "";
        	$item->editorCode = "";
        	$item->buttonImage = "";
        }
		
		//Toolbar
		JToolBarHelper::title(JText::_('BBCode').': <small><small>[ '.$text.' ]</small></small>');
		JToolBarHelper::save();
		JToolBarHelper::apply();
        if($isNew) JToolBarHelper::cancel();
        else JToolBarHelper::cancel('cancel', JText::_('Close'));
		
		//Daten übergeben
		$this->assignRef('editor'      	, $editor);
		$this->assignRef('item'      	, $item);

        parent::display($tpl);
    }//function

}//class
