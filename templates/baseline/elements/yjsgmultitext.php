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
JHTML::_('behavior.modal');

/** 
 * Renders a text element
 *
 * @package 	Joomla.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JFormFieldYJSGMultitext extends JFormField
{	

	public $type = 'YJSGMultitext';
	public function getInput()
	{
		



		// process element properties		
		$items 		= $this->element['items'];
		$default 	= explode('|', $this->element['default']);
		$values 	= is_array( $this->value ) ? $this->value : explode('|', $this->value);
				
		$size 		 = $this->element['size'];
		$css_class 	 = $this->element['class'];
		$labels 	 = explode('|', $this->element['labels']);
		$unique_id 	 = $this->element['name'];
		$turnof		 = $this->element['turnof'];
		$turnoflabel = $this->element['turnoflabel'];
		
		if ($turnof == 1){
			$disableme = 'disabled="disabled';
			$disabletext='<div class="disabled_text">'.$turnoflabel.'</div>';
		}else{
			$disableme='';
			$disabletext='';
		}
		
		$chk_name = str_replace($this->element['name'], $this->element['name'].'_locked', $this->name).'[]';	
		
		// create input text elements
        $div 	= array(); 
		$new_div= array();

		for ( $i=0; $i < $items; $i++ ){	
			$div[$i] = array();
			$cell_css = $i % 2 == 0 ? 'even':'odd';
			$div[$i][] = '<div class="'.$cell_css.'"><label for="'.$labels[$i].'">'.$labels[$i].'</label></div>';		
			$div[$i][] = '<div class="'.$cell_css.'"><input type="text" id="'.$labels[$i].'" class="input-mini '.$css_class.' YJSlider '.$unique_id.'" name="'.$this->name.'[]" value="'.( isset($values[$i]) ? $values[$i] : $default[$i] ).'" size="'.$size.'" '.$disableme.'/></div>';		
			
			if( array_key_exists( ($i+$items), $values ) )
				$checked = $values[$i+$items] == 1 ? 'checked="checked"' : '';			
			else 
				$checked = '';

			$div[$i][] = <<<HTML
<div class="$cell_css">
	<div class="option check $unique_id">			
		<div class="check">
			<input type="checkbox" name="$chk_name" class="YJSG_checkbox $unique_id" $checked />
		</div>
		Lock
	</div>
</div>			
HTML;
		}

		foreach($div as $div_row => $div_value){
			if(is_array($div_value)){
				$new_div[] = "<div class='box_".$div_row." grid_box'><div class='grid_box_in'>".implode("\n", $div_value)."</div></div>";
			}else{
				$new_div[] = "<div class='box_".$div_row." grid_box'><div class='grid_box_in'>".$div_value."</div></div>";
			}
		}

		$output = '<div class="YJSG_sbox_large"><div class="YJSG_multiple"><div id="'.$unique_id.'">';
		$output.= implode("\n", $new_div);		
		$output.= '<div><div class="yjclear"></div><a class="YJSG_reset-values btn btn-small btn-primary" id="'.$unique_id.'" href="javascript:;" rel="'.$this->element['default'].'">Reset to default</a></div>';
		$output.= '</div>'.$disabletext.'</div></div>';
		
		if( !defined('SCRIPTS_ON') ){
			$this->addScripts();
			define('SCRIPTS_ON', 1);
		}
		// return HTML
		return $output; 		
	}	
	
	
	
	private function addScripts(){
		
		$document =& JFactory::getDocument();
		if(intval(JVERSION) >= 3) {
			$file_v='30';
		}else{
			$file_v='';
		}
        // determine template filepath 
        $uri = str_replace(DIRECTORY_SEPARATOR,"/",str_replace( JPATH_SITE, JURI::base (), dirname(dirname(__FILE__)) ));
		$uri = str_replace("/administrator/", "", $uri);
		$this->template = end( explode( '/', $uri ) );
	   $document->setMetaData( 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
	   $document->addCustomTag('<script src="'.$uri.'/src/admin/yjsg_admin'.$file_v.'.js" type="text/javascript"></script>');
		$document->addCustomTag('
			<!--[if IE 7]>
			<style type="text/css">
			.elSelect .option{
				margin-top:-1px;
			}
			.selectsContainer .overDiv{
			position:static;
			}
			</style>
			<![endif]--> 
		');
		
		// add css 
		$document->addStyleSheet($uri.'/css/admin/yjsg_admin'.$file_v.'.css');	
		
		
		
		$document 	= &JFactory::getDocument();
		$head_data 	= $document->getHeadData();

		if(isset($head_data['scripts'][$uri.'/src/admin/yjsg_admin'.$file_v.'.js'])){
			$head_scripts['scripts'] = $head_data['scripts'];
			//keep the removed style
			$moved_style = $head_scripts['scripts'][$uri.'/src/admin/yjsg_admin'.$file_v.'.js'];
			//remove style
			unset($head_scripts['scripts'][$uri.'/src/admin/yjsg_admin'.$file_v.'.js']);
			//put the style back at the end of the headdata
			$head_scripts['scripts'][$uri.'/src/admin/yjsg_admin'.$file_v.'.js'] = $moved_style;
			$document->setHeadData($head_scripts);
		}
		if(intval(JVERSION) < 3) {
		 $document->addScript($uri.'/src/libraries/jquery.js');
		 $document->addScript($uri.'/src/libraries/jquery-noconflict.js');
		 //$document->addScript($uri.'/src/admin/button.js');
		}
			
	}
	
}
