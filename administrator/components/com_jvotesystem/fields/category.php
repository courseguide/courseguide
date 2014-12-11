<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @author Johannes Me�mer
 * @copyright (C) 2010- Johannes Me�mer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die();

require_once JPATH_SITE.DS.'components'.DS.'com_jvotesystem'.DS.'classes'.DS.'loader.php';

class JFormFieldCategory extends JFormField
{
	protected $type 		= 'jVoteSystem';

	function getInput()
	{
		$doc 		=& JFactory::getDocument();
		$fieldName	= $this->name;
		$vbcat =& VBCategory::getInstance();
		
		//Kategorien laden
		$cats = $vbcat->getCategories();

		$html = array();

		$html[] = "<select name=\"$fieldName\">";
		$html[] = '<option value="all">'.JText::_("All").'</option>';
		foreach($cats AS $c) {
			$html[] = '<option value="'.$c->alias.'"'.(($c->alias == $this->value) ? ' selected="selected"' : '').'>';
				for($i = 0; $i <= $c->level; $i++) $html[] = " - ";
				$html[] = JText::_($c->title);
			$html[] = '</option>';
		}
		$html[] = "</select>";
		//$html = "\n<div style=\"float: left;\"><input style=\"background: #ffffff;\" type=\"text\" id=\"a_name\" value=\"$poll->title\" disabled=\"disabled\" /></div>";
		//$html .= "<div class=\"button2-left\"><div class=\"blank\"><a class=\"modal\" title=\"".JText::_('Select')."\"  href=\"$link\" rel=\"{handler: 'iframe', size: {x: 650, y: 375}}\">".JText::_('Select')."</a></div></div>\n";
		//$html .= "\n<input type=\"hidden\" id=\"a_id\" name=\"$fieldName\" value=\"".$this->value."\" />";

		return implode("", $html);
	}
}
?>