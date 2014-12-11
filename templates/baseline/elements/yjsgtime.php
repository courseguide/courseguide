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

class JFormFieldYJSGTime extends JFormField
{
	/**
	* Element type
	*
	* @access	protected
	* @var		string
	*/
	public $type = 'YJSGTime';
	public function getInput()
	{
		 jimport('joomla.filesystem.file');
 		 jimport( 'joomla.filesystem.folder' );
		 
		 $document =& JFactory::getDocument();
		 $template_folder		= basename(dirname(dirname(__FILE__)));
		 $template_path 		= JPATH_ROOT."/templates/".$template_folder;

		return '<input type="hidden" name="'.$this->name.'" id="'.$this->element['name'].'" value="'.time().'" />
				<input type="hidden" name="YJSG_template_path" id="YJSG_template_path" value="'.JURI::root().'templates/'.$template_folder.'" />
				<input type="hidden" name="YJSG_site_path" id="YJSG_site_path" value="'.JURI::root().'" />
				<input type="hidden" name="YJSG_template_id" id="YJSG_template_id" value="'.JFactory::getApplication()->input->get('id').'" />';
						
	}
	
	public function getLabel() {
		return false;
	}
}