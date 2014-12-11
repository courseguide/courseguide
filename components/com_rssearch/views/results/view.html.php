<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport( 'joomla.application.component.view');

class rssearchViewResults extends JViewLegacy {
	
	public function display($tpl = null) {
		$this->results			= $this->get('Results');
		$this->pagination		= $this->get('Pagination');
		$this->nr_words			= $this->get('WordsLimit');
		$this->type				= $this->get('Type');
		$this->search			= JFactory::getApplication()->input->getString('search','');
		$this->module_id		= JFactory::getApplication()->input->getInt('module_id',0);
		$this->itemId			= JFactory::getApplication()->input->getInt('Itemid',0);

		parent::display($tpl);
	}
	
	public function cutText($text, $nr) {
		$text	= stripslashes(strip_tags($text));
		$words	= explode(' ', $text);
		$string = '';
		
		if (count($words) < $nr ) {
			$nr = count($words);
		}

		for($i=0; $i<$nr; $i++) {
			$string .= $words[$i].' ';
		}
		
		return $string;
	}
	
	public function HighlightSearch($search, $text) 
	{
		return preg_replace('/('.$search.')/i', '<strong style="color:red;">$1</strong>', $text);
	}
}