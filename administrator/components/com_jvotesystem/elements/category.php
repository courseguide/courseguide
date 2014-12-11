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

class JElementCategory extends JElement
{
	var	$_name = 'Title';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$doc 		=& JFactory::getDocument();
		$fieldName	= $control_name.'['.$name.']';
		$db 		=& JFactory::getDBO();
		
		$vbcat =& VBCategory::getInstance();
		
		//Kategorien laden
		$cats = $vbcat->getCategories();

		$html = array();

		$html[] = "<select name=\"$fieldName\">";
		$html[] = '<option value="all">'.JText::_("All").'</option>';
		foreach($cats AS $c) {
			$html[] = '<option value="'.$c->alias.'"'.(($c->alias == $value) ? ' selected="selected"' : '').'>';
				for($i = 0; $i <= $c->level; $i++) $html[] = " - ";
				$html[] = JText::_($c->title);
			$html[] = '</option>';
		}
		$html[] = "</select>";

		return implode("", $html);
	}
}
?>