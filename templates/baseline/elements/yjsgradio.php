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
 * Renders a radio element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */

//if(intval(JVERSION) < 3) {
		class JFormFieldYjsgradio extends JFormField
		{
			/**
			 * The form field type.
			 *
			 * @var    string
			 * @since  11.1
			 */
			protected $type = 'Yjsgradio';
		
			/**
			 * Method to get the radio button field input markup.
			 *
			 * @return  string  The field input markup.
			 *
			 * @since   11.1
			 */
			protected function getInput()
			{
				$html = array();
		
				// Initialize some field attributes.
				$class = $this->element['class'] ? ' class="radio ' . (string) $this->element['class'] . '"' : ' class="radio btn-group"';
		
				// Start the radio field output.
				$html[] = '<div id="'.$this->element['name'].'" class="yjsgradio"><fieldset id="' . $this->id . '"' . $class . '>';
		
				// Get the field options.
				$options = $this->getOptions();
		
				// Build the radio field output.
				foreach ($options as $i => $option)
				{
		
					// Initialize some option attributes.
					$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
					$class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
					$disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
		
					// Initialize some JavaScript option attributes.
					$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
		
					$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '"' . ' value="'
						. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';
		
					$html[] = '<label id="lbl-' . $this->id . $i .'" for="' . $this->id . $i . '"' . $class . '>'
						. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';
				}
		
				// End the radio field output.
				$html[] = '</fieldset></div>';
		
				return implode($html);
			}
		
			/**
			 * Method to get the field options for radio buttons.
			 *
			 * @return  array  The field option objects.
			 *
			 * @since   11.1
			 */
			protected function getOptions()
			{
				$options = array();
		
				foreach ($this->element->children() as $option)
				{
		
					// Only add <option /> elements.
					if ($option->getName() != 'option')
					{
						continue;
					}
		
					// Create a new option object based on the <option /> element.
					$tmp = JHtml::_(
						'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
						((string) $option['disabled'] == 'true')
					);
		
					// Set some option attributes.
					$tmp->class = (string) $option['class'];
		
					// Set some JavaScript option attributes.
					$tmp->onclick = (string) $option['onclick'];
		
					// Add the option object to the result set.
					$options[] = $tmp;
				}
		
				reset($options);
		
				return $options;
			}
		}

	
//}else{
//	
//		  class JFormFieldYjsgRadio extends JFormField
//		  {
//			  public $type = 'YjsgRadio';
//			  public function getInput()
//			  {
//				  $options = array ();
//				  foreach ($this->element->children() as $option)
//				  {
//					  $val	= $value = $option['value'];
//					  $text	= $option;
//					  $options[] = JHTML::_('select.option', $val, JText::_($text));
//				  }
//		  
//		  
//		  			$html  ='<div id="'.$this->element['name'].'" class="yjsgradio">';
//					$html .='<fieldset id="jform_params_custom_css" class="radio btn-group">';
//					$html .=JHTML::_('select.radiolist', $options, $this->name, 'ss', 'value', 'text', $this->value, $this->element['name'] );
//					$html .='</fieldset>';
//					$html .='</div>';
//		  
//		  
//				  return $html;
//			  }
//		  }
//	
//}

