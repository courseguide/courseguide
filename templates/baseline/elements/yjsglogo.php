<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Platform.
 * Provides a modal media selector including upload mechanism
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldYJSGLogo extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'YJSGLogo';

	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	protected static $initialised = false;

	/**
	 * Method to get the field input markup for a media selector.
	 * Use attributes to identify specific created_by and asset_id fields
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		
		
		 $template_folder		= basename(dirname(dirname(__FILE__)));
		 $template_path 		= JPATH_ROOT."/templates/".$template_folder;		
		 $params_obj 			= $this->form->getValue('params');
		 $params 				= new JRegistry();
		 $params->loadObject($params_obj);
		 $get_color_value		= explode('|',$params->get('yjsg_get_styles'));
		 $yjsg_get_styles			= $get_color_value[0];	
		 $default_logo_image = JURI::root()."templates/".$template_folder."/images/".$yjsg_get_styles."/logo.png";
		  
		$assetField	= $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
		$authorField= $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
		$asset		= $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'] ;
		if ($asset == '') {
			$asset = JFactory::getApplication()->input->get('option');
		}

		$link = (string) $this->element['link'];
		if (!self::$initialised) {

			// Load the modal behavior script.
			JHtml::_('behavior.modal');

			// Build the script.
			$script = array();
			$script[] = '	function jInsertFieldValue(value, id) {';
			$script[] = '		var old_id = document.id(id).value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			var elem = document.id(id)';
			$script[] = '			elem.value = value;';
			$script[] = '			elem.fireEvent("change");';
			$script[] = '		}';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			self::$initialised = true;
		}
		$document = & JFactory::getDocument();
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		$directory = (string)$this->element['directory'];
		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value)) {
			$folder = explode ('/', $this->value);
			array_shift($folder);
			array_pop($folder);
			$folder = implode('/', $folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $directory)) {
			$folder = $directory;
		}
		else {
			$folder='';
		}
if(intval(JVERSION) < 3) {
	$add_label='<label id="jform_params_logo_image-lbl" for="jform_params_logo_image" class="hasTip" title="'.JText::_('LOGO_IMAGE_LABEL').'::'.JText::_('LOGO_IMAGE_DESC').'">'.JText::_('LOGO_IMAGE_LABEL').'</label>';
}else{
	$add_label="";
}
		// The text field.
		$html[] = '<div id="custom_logo" class="YJSG_sbox">';
		$html[] = $add_label;
		$html[] = '<div id="logo_right_holder">';
		$html[] = '<a id="prev_logo" href="'.$default_logo_image.'" class="modal"><img id="show_logo" src="'.$default_logo_image.'" /></a>';
		$html[] = '<div class="fltlft">';
		$html[] = '	<input type="text" name="'.$this->name.'" class="text_area" id="'.$this->id.'"' .
			' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
			''.$attr.' /><br />';
		
		$html[] = '<div id="logo_buttons">';
		$html[] = '<a class="modal round_b hasTip" title="'.JText::_('LOGO_SELECT_DESC').'"' .
			' href="'.($this->element['readonly'] ? '' : ($link ? $link : 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset='.$asset.'&amp;author='.$this->form->getValue($authorField)) . '&amp;fieldid='.$this->id.'&amp;folder='.$folder).'"' .
			' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
		$html[] = '			'.JText::_('LOGO_SELECT_TXT').'</a>';
		
		
		$html[] = '<a class="round_b hasTip" id="clear_logo" title="'.JText::_('LOGO_RESET_DESC').'"' .
			' href="#"'.
			' onclick="document.getElementById(\''.$this->id.'\').value=\'\'; return false;">';
		$html[] = ''.JText::_('LOGO_RESET_TXT').'</a>';
		$html[] = '<a class="round_b hasTip" title="'.JText::_('LOGO_AUTO_DESC').'" href="javascript:;" id="add_dimensions">'.JText::_('LOGO_AUTO_TXT').'</a></div>';
		
		$html[] = '<div id="image_dimensions"></div>';
		$html[] = '</div></div></div>';

		
		return implode("\n", $html);
	}
	
	public function getLabel(){
		if(intval(JVERSION) >= 3) {
			$label = parent::getLabel();
			return $label;
		}else{
			return false;
		}
	}
}
