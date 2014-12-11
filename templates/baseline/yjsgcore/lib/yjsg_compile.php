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
defined( '_JEXEC' ) or die( 'Restricted index access' ); 

/* Boostrap compiler for Joomla 3.0 or 2.5 if JBoostrap plugin is enabled */
if(is_dir($less_dir) && $less_compiler_on == 1) {
	
	jimport( 'joomla.filesystem.folder' );
	
	//check less folder and make sure there is no other less class
	require_once "lessc.inc.php";
	require_once "lessphpcss.inc.php";
	require_once "lessphpcache.inc.php";
	
	if(class_exists('lessPhpCss') && JFolder::exists($less_dir) && $compile_css == 1){
		$less 				= new lessPhpCss;
	}elseif(class_exists('lessPhpCache') && JFolder::exists($less_dir)){
		$less 				= new lessPhpCache;
	}	
	
	
	
	if($compiler_compressed == 1){
			$less->setFormatter("compressed");
	}
	// if we are using compiled css file than add this in , otherwise we dont need it 
	if($use_compiled_css == 1){
		$less->setVariables(array(
		  "template_css" => "'../css/template.css'"
		 ));
	}
	if($custom_css   == 1 && $compile_css == 1){
		$less->setVariables(array(
		  "custom_css" => "'../css/custom.css'"
		 ));
	}
	
	if($responsive_on   == 1 && $compile_css == 1){
		$less->setVariables(array(
		  "yjsg_responsive" => "'../css/yjresponsive.css'",
		  "custom_responsive" => "'../css/custom_responsive.css'"
		 ));
	}

	if($videojs_on   == 1 && $compile_css == 1){
		$less->setVariables(array(
		  "yjsg_videocss" => "'../css/video-js.min.css'"
		 ));
	}
	// include template default style var for template.less to be recompiled
	if($compile_css == 1){
		$less->setVariables(array(
		  "template_style" => "'../css/".$css_file.".css'"
		));
	}
	
	// default font size
	$less->setVariables(array(
	  "font_size" 	=> $css_font
	));

	if ($selectors_override == 1 && $selectors_override_type == 3 && $compile_css == 1){
		
		$fontfacekit_folder 	= YJSGPATH."css".DIRECTORY_SEPARATOR.'fontfacekits'.DIRECTORY_SEPARATOR.$fontfacekit_font_family.DIRECTORY_SEPARATOR;
		$fontfacekit_cached		= $fontfacekit_folder."stylesheet-cached.css";
		$fontfacekit_orig		= $fontfacekit_folder."stylesheet.css";
		$path 					= $yj_site.'/css/fontfacekits/'.$fontfacekit_font_family.'/stylesheet.css';
		if(!JFile::exists($fontfacekit_cached)){
			require_once "yjsg_cssurls.php";
			$new_file_content 	= file_get_contents($path);
			if(!JPluginHelper::getPlugin('system', 'YJCompression')){
				$fontfacekit_content	= yjsgRewriteCssUrl($new_file_content, $path);
			}
			JFile::write($fontfacekit_cached, $fontfacekit_content);
		}
		if(JFile::exists($fontfacekit_cached)){		
			if( filemtime($fontfacekit_orig) > filemtime($fontfacekit_cached)){
				require_once "yjsg_cssurls.php";
				$new_file_content 	= file_get_contents($path);
				if(!JPluginHelper::getPlugin('system', 'YJCompression')){
					$fontfacekit_content	= yjsgRewriteCssUrl($new_file_content, $path);
				}
				JFile::write($fontfacekit_cached, $fontfacekit_content);
			}
		}
		$less->setVariables(array(
		  "squirrel_style" => "'../css/fontfacekits/".$fontfacekit_font_family."/stylesheet-cached.css'"
		));	

	}

	
	// add bootstrap files only if jui/less folder is there
	$jbst 	= JPATH_SITE.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'jui'.DIRECTORY_SEPARATOR.'less'.DIRECTORY_SEPARATOR;
	if(JFolder::exists($jbst) && (JPluginHelper::getPlugin('system', 'JBootstrap') || intval(JVERSION) >= 3 )){
		$less->setVariables(array(
		  "bootstrap" 		=> "'bootstrap.less'",
		  "yjsgless" 		=> "'yjsg.less'",
		  "fontawesome" 	=> "'font-awesome.less'",
		  'sitelinkcolor' 	=> '#'.$valid_color,
		  "vars" 			=> "'variables.less'"
		));
	}
		
	
	

	if(JFactory::getApplication()->input->get('change_css') && !JFile::exists($output_css)){
		  JFile::delete($compiler_log);
	}


	if (JFile::exists($compiler_log)){
		include_once ($compiler_log);
		$cache = unserialize($YjsgCompilerLog);
	} else {
		$cache = $input_less; 
	}
	
	$newCache = $less->cachedCompile($cache);
	
	if (!is_array($cache) || $newCache["updated"] > $cache["updated"] || !file_exists($output_css)) {
		try {
			 $less->compileFile($input_less,$output_css);
			 $newCache['files'] = $less->allParsedFiles();
			 $license_data ="/**".PHP_EOL."* compiled template.css file created by ".ucwords($this->template)." Template".PHP_EOL."* @package ".ucwords($this->template)." Template".PHP_EOL."* @author Youjoomla.com".PHP_EOL."* @website Youjoomla.com ".PHP_EOL."* @copyright	Copyright (c) since 2007 Youjoomla.com.".PHP_EOL."* @license PHP files are GNU/GPL V2 Copyleft CSS / LESS / JS / IMAGES are Copyrighted material".PHP_EOL."**/".PHP_EOL."";
			 $license_data .=  JFile::read($output_css);
			 JFile::write($output_css, $license_data);
			 $writeValue = "<?php defined('JPATH_PLATFORM') or die; \$YjsgCompilerLog = '".serialize($newCache)."'; ?>";
			 JFile::write($compiler_log, $writeValue);
		} catch (exception $e) {
			if( $need_to_compile == 1  ){
				header("Content-Type: application/json");
				echo 'Compiler error:<br />'.wordwrap($e->getMessage(),50, "<br />", true);
				exit;

			}else{
				JError::raiseWarning( 100, JText::_('LESS_COMPILER_ERROR').'<br />'.$e->getMessage());
			}
		}

	}

}
?>