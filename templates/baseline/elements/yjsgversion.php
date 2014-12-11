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
 

class JFormFieldYJSGversion extends JFormField
{
	

	
	public $type = 'YJSGversion';
	
	public function getInput(){
		

		
		 $document =& JFactory::getDocument();
		 jimport('joomla.filesystem.file');
 		 jimport( 'joomla.filesystem.folder' );
		 $params_obj = $this->form->getValue('params');
		 $params 	= new JRegistry();
		 $params->loadObject($params_obj);


		//disable chosen on selects YJSG does not need it 
		$document->_script = str_replace('jQuery(\'select\')','jQuery(\'nomoreselect\')',$document->_script);

$comp_dis  ='<div id="option-resut">';
if($params->get('component_switch')){
	$comp_dis .= JText::_('YJSG_COMPONENT_DISABLED');
}
$comp_dis .='</div>';
		 $less_compiler_on		= $params->get('less_compiler_on');
		 $document->addScriptDeclaration("var comp_dis ='".JText::_('YJSG_COMPONENT_DISABLED')."';var lesscom_on_txt ='".JText::_('COMP_ON_ELEMENT_DESC')."';var lesscom_off_txt ='".JText::_('COMP_OFF_ELEMENT_DESC')."';");
		 if($less_compiler_on ==1){
			 
			 $less_on_msg = '<span class="lesscom_check ison">'.JText::_('COMP_ON_ELEMENT_DESC').'</span>';
		 }else{
			 $less_on_msg ='<span class="lesscom_check">'.JText::_('COMP_OFF_ELEMENT_DESC').'</span>';
		 }
		 
		 $template_folder		= basename(dirname(dirname(__FILE__)));
		 $template_path 		= JPATH_ROOT."/templates/".$template_folder;


		// Get the template XML.
		$client			= JApplicationHelper::getClientInfo(0);
		$path			= JPath::clean($client->path.'/templates/'. $template_folder.'/templateDetails.xml');
		$tpl_default 	= array();
		$skip_types		= array('yjsgversion','yjsgtextblank','yjsgparamtitle','yjsgparamtitle2','yjhandler','yjsgtime','yjsgmultitext','yjsglogo','menuitem');
		
		if (file_exists($path)) {
			$xml = simplexml_load_file($path);
		} else {
			$xml = null;
		}
		if($xml){
			$fieldset_no = count($xml->config->fields->fieldset);
			for($i=0;$i<=$fieldset_no;$i++){
				if($xml->config->fields->fieldset[$i]){
					foreach ($xml->config->fields->fieldset[$i] as $attribut){
						if(!in_array($attribut['type'],$skip_types)){
							$field_name 						= $attribut['name'];
							$default 							= $attribut['default'];
							$tpl_default[(string)$field_name] 	= (string)$default;
						}
					}					
				}
			}
		}
		$document->addScriptDeclaration('var tplDefaults='.json_encode($tpl_default).';');	


//YJSG System Check
$yjmmp_missing 		='';
$yjmmp_unpublished 	='';
$yjjbp_missing 		='';
$yjjbp_unpublished 	='';
$yjmmpMissing 		= false;
$yjmmpUnpublished	= false;
$yjjbpMissing		= false;
$yjjbpUnpublished	= false;

$yjmmManageLink		='index.php?option=com_plugins&view=plugins&filter_folder=system&filter_search=YJMegaMenu';
$yjjbManageLink		='index.php?option=com_plugins&view=plugins&filter_folder=system&filter_search=JBootstrap';
$yjmmDownLink		='http://www.youjoomla.com/joomla-templates/yougrids-free-joomla-template-yjsg-powered.html?opendowns#files_holder';
$yjjbDownLink		='http://www.youjoomla.com/joomla-extensions/jbootstrap-twitter-bootstrap-plugin-for-joomla-2-5.html?opendowns#files_holder';
$yjmmpText 			=''.JText::_('YJMM_INS_PUB').' <a href="'.$yjmmManageLink.'">'.JText::_('YJSC_MAN_EXT').'</a>';
$yjjbpText			=''.JText::_('JBP_INS_PUB').'  <a href="'.$yjjbManageLink.'">'.JText::_('YJSC_MAN_EXT').'</a>';

// check YJ Mega menu Plugin
if (!JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'YJMegaMenu/YJMegaMenu.php')){
	
	$yjmmpMissing = true;
}

if(!JPluginHelper::getPlugin('system', 'YJMegaMenu')){
	
	$yjmmpUnpublished = true;
}

if($yjmmpUnpublished){
	$yjmmp_unpublished 	=' unpublished';
	$yjmmpText 			=''.JText::_('YJMM_UNPUB').' <a href="'.$yjmmManageLink.'">'.JText::_('YJSC_PUB_EXT').'</a>';
	
}

if($yjmmpMissing){
	$yjmmp_missing 		=' missing';
	$yjmmpText 			=''.JText::_('YJMM_UNINS').' <a href="'.$yjmmDownLink.'" target="_blank">'.JText::_('YJSC_DOWN_EXT').'</a>';
}


// check JBootstrap Plugin
if (intval(JVERSION) < 3 && !JFile::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'JBootstrap/JBootstrap.php')){
	
	$yjjbpMissing = true;
}
if (intval(JVERSION) < 3){
		if(!JPluginHelper::getPlugin('system', 'JBootstrap')){
			
			$yjjbpUnpublished = true;
		}
		
		if($yjjbpUnpublished){
			$yjjbp_unpublished 	=' unpublished';
			$yjjbpText 			=''.JText::_('JBP_UNPUB').' <a href="'.$yjjbManageLink.'">'.JText::_('YJSC_PUB_EXT').'</a>';
		}
		
		if($yjjbpMissing){
			$yjjbp_missing 		=' missing';
			$yjjbpText 			=''.JText::_('JBP_UNINS').' <a href="'.$yjjbDownLink.'" target="_blank">'.JText::_('YJSC_DOWN_EXT').'</a>';
		}
}




	// yjsg
	$syshtml ='<div class="yj_system_check">';
	$syshtml .='<div id="yjsgBox" class="systemBox">';
	$syshtml .='<h2 id="yjsgTitle" class="systemBoxTitle hasTip" title="'.JText::_('YJSG_CHECK').'::'.JText::_('YJSG_CHECK_TIP').'">'.JText::_('YJSG_CHECK').'</h2>';
	$syshtml .='<div class="infoText"><span class="showIcon"></span><a href="'.JURI::root().'templates/'.$template_folder.'/yjsgcore/yjsgversion.php" class="modal" rel="{handler: \'iframe\', size: {x: 350, y: 200}}">'.JText::_('YJSG_CHECK_TXT').'</a></div>';
	$syshtml .='</div>';
	
	// yjmm
	$syshtml .='<div id="yjmmpBox" class="systemBox'.$yjmmp_unpublished.$yjmmp_missing.'">';
	$syshtml .='<h2 id="yjmmpTitle" class="systemBoxTitle hasTip" title="'.JText::_('YJMMP_CHECK').'::'.JText::_('YJMMP_CHECK_TIP').'">'.JText::_('YJMMP_CHECK').'</h2>';
	$syshtml .='<div class="infoText"><span class="showIcon"></span>'.$yjmmpText.'</div>';
	$syshtml .='</div>';
	
	// jbootstrap
	if(intval(JVERSION) < 3){
		$syshtml .='<div id="yjjbpBox" class="systemBox'.$yjjbp_unpublished.$yjjbp_missing.'">';
		$syshtml .='<h2 id="yjjbpTitle" class="systemBoxTitle hasTip" title="'.JText::_('YJJBP_CHECK').'::'.JText::_('YJJBP_CHECK_TIP').'">'.JText::_('YJJBP_CHECK').'</h2>';
		$syshtml .='<div class="infoText"><span class="showIcon"></span>'.$yjjbpText.'</div>';
		$syshtml .='</div>';
	}
	$syshtml .='<div  id="settmsgBox" class="systemBox">';
	$syshtml .='<h2 id="yjjbpTitle" class="systemBoxTitle hasTip" title="'.JText::_('SETT_MSG').'::'.JText::_('SETT_MSG_TIP').'">'.JText::_('SETT_MSG').'</h2>';

	$syshtml .='<div class="infoText"><span class="showIcon"></span>'.$less_on_msg.$comp_dis.'</div>';
	$syshtml .='</div>';
	$syshtml .='</div>';// close yj_system_check
		
		

	// Output	
	echo $syshtml;

	}

	public function getLabel() {
		return false;
	}
}

?>