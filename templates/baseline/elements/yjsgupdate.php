<?php
/*======================================================================*\
|| #################################################################### ||
|| # Package - Joomla Template based on YJSimpleGrid Framework          ||
|| # Copyright (C) 2010  Youjoomla.com. All Rights Reserved.            ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        ||
|| # bound by Proprietary License of Youjoomla.com                      ||
|| # for more information visit http://www.youjoomla.com/license.html   ||
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         || 
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/
define( '_JEXEC', 1 );
header("Content-Type: application/json");
$get_file_info  = pathinfo(__FILE__);
$jpath = preg_replace('/(\btemplates\b|\bmodules\b|\bcomponents\b|\bplugins\b)(.*)/','',$get_file_info['dirname']);
define('JPATH_BASE',rtrim($jpath,DIRECTORY_SEPARATOR));
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.plugin.helper');
jimport('joomla.session.session');
$mainframe 				=& JFactory::getApplication('administrator');
 	
$mainframe->initialise();
$session 				=& JFactory::getSession();
$user 					=& JFactory::getUser();
$compile_link			= str_replace('templates/'.basename(dirname(dirname(__FILE__))).'/elements/','',JURI::root());

function YjsgStripSlashes($value){
    $value = is_array($value) ?
                array_map('YjsgStripSlashes', $value) :
                stripslashes($value);
    return $value;
}

//$compile_link			= $compile_link."?recompile=1";
if(isset($_POST['task']))	{
// nothing goes pass this
if(intval(JVERSION) >= 3 ){
	JSession::checkToken() or jexit( '{"error":"Invalid Token"}' );
}else{
	JRequest::checkToken() or jexit( '{"error":"Invalid Token"}' );
}
	
	require('yjsgjson.php');
	// get few params
	$post  					= JRequest::get('post');
	$post 					= YjsgStripSlashes($post);
	$data 					= array();
	if(isset($post['jform']['assigned'])){
		$data['assigned']		= $post['jform']['assigned'];
	}else{
		$data['assigned']		='';
	}
	$task					= $post['task'];
	$template_id 			= $post['YJSG_template_id'];
	$template_name 			= $post['jform']['template'];
	$post  					= $post['jform']['params'];
	$get_color_value		= explode('|',$post['yjsg_get_styles']);
	$get_current_style		= $get_color_value[0];
	$custom_css				= $post['custom_css'];
	$use_compiled_css   	= $post['use_compiled_css'];
	$less_compiler_on   	= $post['less_compiler_on'];
	if(isset($post['fontfacekit_font_family'])){
		$fontfacekit_font_family 	= $post['fontfacekit_font_family'];
	}
    

	
	
	// now encode
	$post  					= json_encode($post);

	$template_folder 		= basename(dirname(dirname(__FILE__)));
	$compiler_log 			= dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."css_compiled".DIRECTORY_SEPARATOR."yjsg_compiler_log.php";
	if(isset($fontfacekit_font_family)){
		$squirrel_folder 		= dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR.'fontfacekits'.DIRECTORY_SEPARATOR.$fontfacekit_font_family.DIRECTORY_SEPARATOR;
		$squirrel_cached		= $squirrel_folder."stylesheet-cached.css";	
	}

	if($use_compiled_css == 1){
		$current_style 		= dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."css_compiled".DIRECTORY_SEPARATOR."template-".$get_current_style.".css";
	}else{
		$current_style 		= dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."css_compiled".DIRECTORY_SEPARATOR."bootstrap-".$get_current_style.".css";
	}
	
	
	 
	
	
	// create custom.css file
	$custom_css_file		= dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR."custom.css";
	$custom_css_content 	="/**".PHP_EOL."* custom.css file created by ".ucwords($template_folder)." Template".PHP_EOL."* @package ".ucwords($template_folder)." Template".PHP_EOL."* @author Youjoomla.com".PHP_EOL."* @website Youjoomla.com ".PHP_EOL."* @copyright	Copyright (c) since 2007 Youjoomla.com.".PHP_EOL."* @license PHP files are released under GNU/GPL V2 Copyleft License.CSS / LESS / JS / IMAGES are Copyrighted material".PHP_EOL."**/".PHP_EOL."/*".PHP_EOL." ADD ALL YOUR CUSTOM CSS OVERRIDES TO THIS FILE.".PHP_EOL." THIS WAY IF YOU MAKE A MISTAKE YOU CAN ALWAYS TURN CUSTOM CSS FILE OFF".PHP_EOL." AND REVERT BACK TO ORIGINAL TEMPLATE CSS".PHP_EOL." THIS FILE WILL LOAD VERY LAST AFTER ALL TEMPLATE CSS FILES.".PHP_EOL." SO YOU CAN OVERRIDE ANY CSS PART OF THE TEMPLATE YOU NEED.".PHP_EOL."*/".PHP_EOL."";
	
	
	if($custom_css == 1 && !JFile::exists($custom_css_file)){
		JFile::write($custom_css_file,$custom_css_content);
	}

	//load the language files also
	$language 	= JFactory::getLanguage();
	$language->load('tpl_'.$template_name, JPATH_BASE, null, false, false);
	
	// update db
	function update_db($post,$template_name,$template_id){
		// Update the mapping for menu items that this style IS assigned to.
		$db		= JFactory::getDbo();
		$user	= JFactory::getUser();
		global $data,$task,$compile_link;

		if (!empty($data['assigned']) && is_array($data['assigned'])) {
			JArrayHelper::toInteger($data['assigned']);
			
			
			// Update the mapping for menu items that this style IS assigned to.
			$query = $db->getQuery(true);
			$query->update('#__menu');
			$query->set('template_style_id='.(int)$template_id);
			$query->where('id IN ('.implode(',', $data['assigned']).')');
			$query->where('template_style_id!='.(int) $template_id);
			$query->where('checked_out in (0,'.(int) $user->id.')');
			$db->setQuery($query);
			$db->query();
		}

		// Remove style mappings for menu items this style is NOT assigned to.
		// If unassigned then all existing maps will be removed.
		$query = $db->getQuery(true);
		$query->update('#__menu');
		$query->set('template_style_id=0');
		if (!empty($data['assigned'])) {
			$query->where('id NOT IN ('.implode(',', $data['assigned']).')');
		}

		$query->where('template_style_id='.(int) $template_id);
		$query->where('checked_out in (0,'.(int) $user->id.')');
		$db->setQuery($query);
		$db->query();

		if($task == 'clearCache' && isset($data['assigned'][0])){
			//get the recompile url
			$db->setQuery(
				'SELECT link' .
				' FROM #__menu' .
				' WHERE id = ' . (int)$data['assigned'][0]
			);
			$menu_link = $db->loadResult();
	
			if ($error = $db->getErrorMsg()) {
				throw new Exception($error);
			}
			$compile_link = $compile_link.$menu_link;
		}
		
	
		if(intval(JVERSION) >= 3 ){
			try {
				$query = $db->getQuery(true);
				$query->update("#__template_styles SET params='". $db->escape($post) ."' WHERE template='". $template_name ."' AND id=". $template_id ."");
				$db->setQuery($query);
				$db->query(); 
			}catch (Exception $e){
					$response = array('error'=>wordwrap($e->getMessage(),60, "<br />", true));
					$json = new JSON($response);
					echo $json->result;
					exit;
			}	
		}else{
			$query = $db->getQuery(true);
			$query->update("#__template_styles SET params='". $db->getEscaped($post) ."' WHERE template='". $template_name ."' AND id=". $template_id ."");
			$db->setQuery($query);
			$db->query(); 
		
	// Make sure there aren't any errors
			if ($db->getErrorNum()) {
				$response = array('error'=>wordwrap($db->getErrorMsg(),60, "<br />", true));
				$json = new JSON($response);
				echo $json->result;
				exit;
			}
		}
		
		$compile_link = strstr($compile_link, '?') ? $compile_link."&recompile=1" : $compile_link."?recompile=1";
		return JRoute::_($compile_link);
	}
		
	if($task == 'adminUpdate'){
		$compile_link = update_db($post,$template_name,$template_id);
		
		if($less_compiler_on == 0){
			
			  // clear log
			  if(JFile::exists($compiler_log)){
				  JFile::delete($compiler_log);
			  }
			  // clear current
			  if(JFile::exists($current_style)){
				  JFile::delete($current_style);
			  }
			  // clear squirrel
			  if(isset($fontfacekit_font_family)){
				if(JFile::exists($squirrel_cached)){
					JFile::delete($squirrel_cached);
				}	
			  }
			
		}
		
		if($less_compiler_on == 0 && $use_compiled_css == 1){
			$response = array('message_er'=> JText::_( 'AJAX_RECOMPILED_ERROR' ));
		}else{
			$response = array('message'=>JText::_( 'AJAX_SAVED' ));
		}
		
		$json = new JSON($response);
		echo $json->result;
		exit();
	}
	
	
	// clear compiler cache
	if($task == 'clearCache'){
		// clear log
		if(JFile::exists($compiler_log)){
			JFile::delete($compiler_log);
		}
		// clear current
		if(JFile::exists($current_style)){
			JFile::delete($current_style);
		}
		// clear squirrel
		if(isset($fontfacekit_font_family)){
		  if(JFile::exists($squirrel_cached)){
			  JFile::delete($squirrel_cached);
		  }	
		}
		   
		   
		if(!JFile::exists($compiler_log) || !JFile::exists($current_style)){
		  $compile_link = update_db($post,$template_name,$template_id);
		  file_get_contents($compile_link);
		  $check_page = get_headers($compile_link,1);
		  if($check_page['Content-Type'] == 'application/json'){
			  $response = array('error'=> file_get_contents($compile_link));
		  }else{
			  $response = array('message'=> JText::_( 'AJAX_CACHE_SAVED' ));
		  }
		  $json = new JSON($response);
		  echo $json->result;		  
		  exit();
		}else{
		  $response = array('error'=> JText::_( 'AJAX_CACHE_ERROR' ));
		  $json = new JSON($response);
		  echo $json->result;
		  exit();	
		}
			  
		
	}

	if($task == 'checkCompiled'){
		
		if(JFile::exists($current_style)){
			
		  $response = array('message'=> JText::_( 'AJAX_RECOMPILED_EXISTS' ));
		  $json = new JSON($response);
		  echo $json->result;
		  exit();			
			
			
		}else{
			
		  $response = array('message_er'=> JText::_( 'AJAX_RECOMPILED_ERROR' ));
		  $json = new JSON($response);
		  echo $json->result;
		  exit();	
			
		}
		
		
		
		
	}

}else{
	echo 'Restricted access';
}