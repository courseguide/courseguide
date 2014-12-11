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
function yjsgRewriteCssUrl($new_file_content, $path) {
	/*
	Parentheses, commas, whitespace chars, single quotes, and double quotes are
	escaped with a backslash as described in the CSS spec:
	http://www.w3.org/TR/REC-CSS1#url
	*/
	$relativePath 		= preg_replace('/([\(\),\s\'"])/', '\\\$1',	$path);//str_replace(MINIFY_BASE_DIR, '', $path)
	$path 				= str_replace(JPATH_ROOT."\\",JURI::base(),$path);
	$file_path_explode 	= explode("/",$path);
	$added_url			= "";

	if(count($file_path_explode) > 0){

		//remove the last path, cause is the current file name
		unset($file_path_explode[count($file_path_explode)-1]);
		
		$added_url 			= implode("/",$file_path_explode)."/";
		$new_file_content	= preg_replace("/(:|,)(\s*?[0-9a-zA-Z#-]*?\s*?)(\s*\burl)(\s*\()(['|\"]?)(([^\.]{2})[^\)]+)(['|\"]?)(\))/i", '$1$2$3$4$5'.implode("/",$file_path_explode)."/".'$6$8$9', $new_file_content);
		
		$pattern 			= array(); 
		$replace 			= array(); 
		$first_replace 		= "";
		
		for($i = count($file_path_explode)-1; $i >= 2; $i--){
			unset($file_path_explode[$i]);
			$replace_implode= implode("/",$file_path_explode)."/";
			$first_replace 	.= "\.\.\/";
			$pattern[]		= "/(:|,)(\s*?[0-9a-zA-Z#-]*?\s*?)(\s*\burl)(\s*\()(['|\"]?)(".$first_replace.")([^'\"|\)]*)(['|\"]?)(\))/i"; 
			$replace[] 		= '$1$2$3$4$5'.$replace_implode.'$7$8$9';

			//break the array if we get to the site root folder
			if($replace_implode == JURI::base()) break;
		}
		
		$pattern 			= array_reverse($pattern);
		$replace 			= array_reverse($replace);			
		$new_file_content 	= preg_replace($pattern, $replace, $new_file_content);
	}
	return $new_file_content;
 }