<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldComponents extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Components';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
		// Get options
		$options	= $this->getOptions();
		$html		= array();
		
		
		if (!is_array($this->value)) {
			$this->value = (array) $this->value;
		}
		
		if (!empty($options)) {
			if (!$this->is30())
				$html[] = '<div style="float: left;">';
			
			$inputStyle	= $this->is30() ? '' : 'style="float: left;"';
			$labelStyle	= $this->is30() ? '' : 'style="clear: none; float: left;"';
			
			foreach($options as $option) {
				$checked	= in_array(strtolower($option),$this->value) ? 'checked="checked"' : '';
				
				$html[] = '<input type="checkbox" '.$checked.' name="'.$this->name.'[]" id="'.$this->id.'_'.strtolower($option).'" value="'.strtolower($option).'" '.$inputStyle.' /> <label '.$labelStyle.' class="checkbox inline" for="'.$this->id.'_'.strtolower($option).'">'.JText::_('RSF_MODULE_PLUGIN_'.strtoupper($option)).'</label> <br /> ';
			}
			
			if (!$this->is30())
				$html[] = '</div>';
		}
		
		
		return implode("\n",$html);
	}
	
	protected function getOptions() {
		jimport('joomla.plugin.helper');
		$plugins = JPluginHelper::getPlugin('rssearch');
		$options = array();
		
		if (!empty($plugins)) {
			foreach($plugins as $plugin) {
				$options[] = $plugin->name;
			}
		}
		
		return $options;
	}
	
	protected function is30() {
		return version_compare(JVERSION, '3.0', '>=');
	}
}