<?php
/*======================================================================*\
|| #################################################################### ||
|| # Package - Joomla Template based on YJSimpleGrid Framework          ||
|| # Copyright (C) 2010  Youjoomla.com. All Rights Reserved.            ||
|| # Authors - Dragan Todorovic and Constantin Boiangiu                 ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        ||
|| # bound by Proprietary License of Youjoomla.com                      ||
|| # for more information visit http://www.youjoomla.com/license.html   ||
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         ||
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a text element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

class JFormFieldYJSGList extends JFormFieldList
{
	
	public $type = 'YJSGList';
	public function getInput()
	{
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : 'class="inputbox"' );
		$val = $this->value;// default option
		
		$options = array ();
		
		
		 $template_folder		= basename(dirname(dirname(__FILE__)));
		

		foreach ($this->element->children() as $option)
		{
			
			
/*		if(strstr($option['value'], '|')){
			$split_value 					= explode('|',$option['value']);
			$option_visible_name 			= $split_value[0];
		}else{
			$option_visible_name			=JText::_(trim((string) $option));
		}*/
			if(strstr($option['value'], '|')){
				$add_rel =' rel="'.$option['value'].'"';
			}else{
				$add_rel ='';
			}
			
			
			$value = $option['value'];
			$class = $option['disable'] ? ' class="disable_next '.$option['disable'].' ' : ' class="';
			$class .= $option['enable'] ? 'enable_next '.$option['enable'].'"' : '"';
			$selected = $val == $value ? ' selected="selected"':'';
			$text	= $option['text'];
			

			
			
			
			$options[] = '<option'.$add_rel.' value="'.$value.'"'.$class.$selected.'>'.JText::_(trim((string) $option)).'</option>';
		}
		$select_class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$s = '<div class="YJSG_sbox '.$this->element['name'].'"><select name="'.$this->name.'" '.$select_class.' id="'.$this->element['name'].'">';
		$s.= implode("\n", $options);
		$s.= '</select>';
		
		if($this->element['yjsgstyles']){
			$split_selected_value 			= explode('|',$val);
			$color_value					= '#'.$split_selected_value[1];
			$document	    					 = &JFactory::getDocument();
			$document->addScript(JURI::root().'templates/'.$template_folder.'/src/admin/mooRainbow.js');
			$s.='<div class="linkcolorlabel">';
			$s.='<span class="lclable">Link Color</span>';
			$s.='<input type="text" name="def_link_color" id="def_link_color" value="'.$color_value.'" class="yjsg-colorpicker text_area" />';
			$s.='<span class="show_ranibow"><span class="show_ranibow_in "></span></span>';
			$s.='</div>';
		}
		
		$s.='</div>';
		
		return $s;	
	}
}