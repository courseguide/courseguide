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
define( 'YJSGV', '1.0.15');
jimport('joomla.filesystem.file');
//BEGIN CUSTOM JS VAR
/*

   Begining of $yjsg_js
   Add your script after 
   $yjsg_js='<script type="text/javascript">'; 
   or in custom/yjsg_custom_params.php 
   by using $yjsg_js.='var =my_js_var';
   dont forget the dot after $yjsg_js
   For better performance and cleaner <head></head>
   $yjsg_js is echoed at the end of the page in yjsg_footer.php
 
*/
$yjsg_js='<script type="text/javascript">';


// USABLE VARS 
$yj_site = JURI::base()."templates/".$this->template;          //  Current template folder as http://www.site_name/templates/template_name
$yj_base = JURI::base();                                       //  Site path as  http://www.site_name
$yj_copyrightear = (Date("Y"));                                //  Current Copyright year in footer
$yj_templatename = $this->params->get("custom_cp");        	   //  Get template name for footer otherwise use param value
if(empty($yj_templatename)){
	$yj_templatename = ucfirst($this->template); 
}
$document	    					 = &JFactory::getDocument();
$dispatcher 						 = JDispatcher::getInstance();
$app 								 = JFactory::getApplication('site');
// STYLE SETTINGS
$check_style_param 					 = $this->params->get("yjsg_get_styles");
if(isset($check_style_param)){
	$get_style_value				 = explode('|',$this->params->get("yjsg_get_styles"));
	$yjsg_get_styles				 = $get_style_value[0];	
	$default_link_color				 = $get_style_value[1];
	$site_link_color				 = '#'.$default_link_color;	
}else{
 	$yjsg_get_styles				 = $this->params->get("default_color");
	$default_link_color				 = '';
}


$default_font			             = $this->params->get("default_font"); 
$default_font_family			     = $this->params->get("default_font_family");
$selectors_override			         = $this->params->get("selectors_override");
$css_font_family			         = $this->params->get("css_font_family");
$google_font_family			         = $this->params->get("google_font_family");
$fontfacekit_font_family          	 = $this->params->get("fontfacekit_font_family");
$css_width            				 = $this->params->get("css_width");
$css_widthdefined    				 = $this->params->get ("css_widthdefined");
$text_direction       				 = $this->params->get("text_direction");
$selectors_override_type			 = $this->params->get("selectors_override_type");
$affected_selectors     			 = $this->params->get ("affected_selectors");
$custom_css    		            	 = $this->params->get ("custom_css");
$joomla_generator_off 				 = $this->params->get("joomla_generator_off");
$validators_off 				 	 = $this->params->get("validators_off");
$totop_off 				 			 = $this->params->get("totop_off");
if(!empty($default_font)){
	$css_font 						 = $default_font;
}else{
	$css_font 						 ='12px';
}
//TOOLS CONTROL
// SHOW FONT SWITCH = 1 | HIDE FONT SWITCH = 0
$show_tools    					     = $this->params->get("show_tools");
$show_fres    					     = $this->params->get("show_fres");
$show_rtlc    					     = $this->params->get("show_rtlc");
// LAYOUT
$site_layout    			        = $this->params->get("site_layout");

//MENY TYPE 
// mainmenu by default, can be any Joomla! menu name
$menu_name      			        = $this->params->get("menuName");
$sub_width 							= $this->params->get("sub_width");
$yjsg_menu_offset					= $this->params->get("yjsg_menu_offset");
//MENU STYLE SWITCH
//  1 = Suckerfish  | 2  = SMooth Dropdown | 3 = Dropline Menu | 4 SmoothDropline menu |  5  = Split Menu | 6 = menu module position
$default_menu_style  			    = $this->params->get("default_menu_style"); 
$menu_page_title                    = $this->params->get("menu_page_title");

// Logo and header block settings
$logo_image							= $this->params->get("logo_image");
$logo_height                        = $this->params->get("logo_height");
$logo_width 						= $this->params->get("logo_width");
$turn_logo_off 						= $this->params->get("turn_logo_off");
$turn_header_block_off				= $this->params->get("turn_header_block_off");
$logo_out 							= round($logo_width / $css_width*100,2);
if($turn_logo_off == 1) {
	$headergrid_width = 100;
}else{
	$headergrid_width = 100 -$logo_out;
}


// SEO SECTION //
$seo              				    = $this->params->get ("seo");                     
$tags             				    = $this->params->get ("tags");
$turn_seo_off   		            = $this->params->get ("turn_seo_off");
$ie6notice        			        = $this->params->get("ie6notice"); // 1 = ON | 0 = OFF   


// ADVISE VISITORS THAT THIR JAVASCRIPT IS DISABLED
$nonscript           		        = $this->params->get("nonscript"); // 1 = ON | 0 = OFF 


// ADD JQUERY 
$jq_switch  			    		= $this->params->get ("jq_switch");

// ADD GOOGLE ANALYTICS

$ga_switch  			    		= $this->params->get ("ga_switch");
$GAcode								= $this->params->get ("GAcode");
//FPNI CONTROL
$fp_controll_switch 		        = $this->params->get ("fp_controll_switch");
$fp_chars_limit             		= $this->params->get ("fp_chars_limit");
$fp_after_text              		= $this->params->get ("fp_after_text ");



// YJSimpleGrid LOGO
$branding_off  						= $this->params->get ("branding_off");
$joomlacredit_off  					= $this->params->get ("joomlacredit_off");

//MODULE STYLES
$YJsg1_module_style  				= $this->params->get ("YJsg1_module_style");
$YJsgh_module_style  				= $this->params->get ("YJsgh_module_style");
$YJsg2_module_style  				= $this->params->get ("YJsg2_module_style");
$YJsg3_module_style  				= $this->params->get ("YJsg3_module_style");
$YJsg4_module_style  				= $this->params->get ("YJsg4_module_style");
$YJsgmt_module_style  				= $this->params->get ("YJsgmt_module_style");
$YJsgl_module_style  				= $this->params->get ("YJsgl_module_style");
$YJsgr_module_style  				= $this->params->get ("YJsgr_module_style");
$YJsgi_module_style  				= $this->params->get ("YJsgi_module_style");
$YJsgit_module_style  				= $this->params->get ("YJsgit_module_style");
$YJsgib_module_style  				= $this->params->get ("YJsgib_module_style");
$YJsgmb_module_style  				= $this->params->get ("YJsgmb_module_style");
$YJsg5_module_style  				= $this->params->get ("YJsg5_module_style");
$YJsg6_module_style  				= $this->params->get ("YJsg6_module_style");
$YJsg7_module_style  				= $this->params->get ("YJsg7_module_style");


// widths 
$leftcolumn            				= $this->params->get ("leftcolumn");
$rightcolumn            			= $this->params->get ("rightcolumn"); 
$maincolumn             			= $this->params->get ("maincolumn"); 
$insetcolumn           				= $this->params->get ("insetcolumn");
$widthdefined           			= $this->params->get ("widthdefined");

// widths on specific item id
$leftcolumn_itmid                   = $this->params->get ("leftcolumn_itmid");
$rightcolumn_itmid                  = $this->params->get ("rightcolumn_itmid"); 
$maincolumn_itmid                   = $this->params->get ("maincolumn_itmid"); 
$insetcolumn_itmid                  = $this->params->get ("insetcolumn_itmid");
$widthdefined_itmid                 = $this->params->get ("widthdefined_itmid");



//START COLLAPSING THAT MODULE:)
$left                   			= $this->countModules( 'left' );
$right                  			= $this->countModules( 'right' );
$inset                  			= $this->countModules( 'inset' );
// GET ITEM ID FOR SPECIFIC PARAMS
$itemid 							= $app->input->get('Itemid');
$define_itemid             			= $this->params->get ("define_itemid");
$component_switch          			= $this->params->get ("component_switch");
$turn_topmenu_off          			= $this->params->get("turn_topmenu_off");

// TEMPLATE REQUIRED FILES
require( YJSGPATH."yjsgcore/yjsg_links.php");
require( YJSGPATH."yjsgcore/lib/yjsg_loadgrids.php");
require( YJSGPATH."yjsgcore/yjsg_stylesw.php");



//COMPONENT OFF SWITCH

if(is_array( $component_switch ) && in_array( $itemid, $component_switch )){
	
	$turn_component_off = 1;
	
}else{
	
	$turn_component_off = 2;
}


//TOPMENU OFF SWITCH
	 
if(is_array( $turn_topmenu_off ) && in_array( $itemid, $turn_topmenu_off )){
	
	$topmenu_off = 1;
	
}else{
	
	$topmenu_off = 2;
}
	 
// SPECIFIC ITEM ID WIDTHS	
if( is_array( $define_itemid ) && in_array($itemid, $define_itemid) ){
	require( YJSGPATH."yjsgcore/yjsg_mgwidthsitem.php");
}else{
	require( YJSGPATH."yjsgcore/yjsg_mgwidths.php");
}

//TOP MENU 

$template_menus = FALSE;
if($default_menu_style !=6){

    	
	$renderer	 = $document->loadRenderer( 'module' );
	$options	 = array( 'style' => "raw" );
	$module	     = JModuleHelper::getModule( 'mod_menu','$menu_name' );
	$topmenu     = false; $subnav = false; $sidenav = false;
//DROPDOWN OR SMOOTHDROPDOWN
	if ($default_menu_style == 1 or $default_menu_style== 2) :
			$module->params	= "menutype=$menu_name\nshowAllChildren=1\nclass_sfx=nav\nmenu_images=n";
			$topmenu = $renderer->render( $module, $options );
			$menuclass = 'horiznav';
			$topmenuclass ='top_menu';
// DROPLINE OR SMOOTHDROPLINE

	elseif ($default_menu_style == 3 or $default_menu_style== 4) :
			$module->params	= "menutype=$menu_name\nshowAllChildren=1\nclass_sfx=navd\nmenu_images=n";
			$topmenu = $renderer->render( $module, $options );
			$menuclass = 'horiznav_d';
			$topmenuclass ='top_menu_d';
// SPLIT MENU  NO SUBS
	elseif ($default_menu_style == 5) :
			$module->params	= "menutype=$menu_name\nstartLevel=0\nendLevel=1\nclass_sfx=split\nmenu_images=n";
			$topmenu = $renderer->render( $module, $options );
			$menuclass = 'horiznav';
			$topmenuclass ='top_menu';
	endif;
	$template_menus = TRUE;
}
// LAYOUT SWITCH
switch ($site_layout) {
    case 1:
    $yjsg_loadlayout = "left-mid-right";
	break;
    case 2:
    $yjsg_loadlayout = "mid-left-right";
    break;
    case 3:
    $yjsg_loadlayout = "left-right-mid";
    break;
}
// FIND  MOBILES
include( YJSGPATH."yjsgcore/yjsg_detect.php");
$detect 		= new YJSG_Mobile_Detect();
$yjsg_mobile 	= $detect->isMobile();


// FONTS 

if ($selectors_override_type == 1 ){ // CSS
	require( YJSGPATH."yjsgcore/yjsg_cssfonts.php");
	$nice_font   =  $css_font_family;
	$font_sheet ='';
}elseif ($selectors_override_type == 2){ // GOOGLE

	
	$fontName 		= preg_replace('/:(.*)/','',$google_font_family);
	$nice_font   	= ''.str_replace('+',' ',$fontName).',sans-serif;';
	
	if(strstr($google_font_family,'|')){
		
		$splitFont 			= explode('|',$google_font_family);
		$fontName 			= preg_replace('/:(.*)/','',$splitFont[0]);
		$fontWeight 		= '';
		
		if(isset($splitFont[2])){
			$fontWeight = 'font-weight:'.$splitFont[2].';';
		}
		
		$nice_font   		= ''.str_replace('+',' ',$fontName).','.$splitFont[1].';'.$fontWeight.'';
		$google_font_family = $splitFont[0];
	}	
	$font_sheet  = 'http://fonts.googleapis.com/css?family='.$google_font_family.'';
}

/*
	 discover Browsers and version
	 $yjsgBrowser->Name
	 $yjsgBrowser->Version
*/
class YjsgCheckBrowser {       
	public function __construct(){

		if(isset($_SERVER['HTTP_USER_AGENT'])){		
			$who 				= strtolower($_SERVER['HTTP_USER_AGENT']);
			$desktopBrowsers 	= array("firefox", "msie", "opera", "chrome", "safari", 
								"mozilla", "seamonkey",    "konqueror", "netscape", 
								"gecko", "navigator", "mosaic", "lynx", "amaya", 
								"omniweb", "avant", "camino", "flock", "aol"); 
									
			foreach($desktopBrowsers as $browser){ 
				
				if (preg_match("#($browser)[/ ]?([0-9.]*)#", $who, $match)){ 
				
					$this->Name 	= $match[1] ; 
					$this->Version = $match[2] ; 
					break ; 
					
				} 
			} 
		}else{
			
					$this->Name 	= ''; 
					$this->Version  = '' ; 			
		}
			
	}
}
$yjsgBrowser = new YjsgCheckBrowser ;

/* 
	add browser class name for body tag
*/
$browserClassName ='';
if(isset($_SERVER['HTTP_USER_AGENT'])){	
	if($yjsgBrowser->Name =='msie'){
		$browserClassName = ' yjsgbr-'.$yjsgBrowser->Name.str_replace('.','',$yjsgBrowser->Version);
	}else{
		$browserClassName = ' yjsgbr-'.$yjsgBrowser->Name;
	}
}



// remove all midblock divs if no items ,no columns, no links, no modules on specific itemid
if (!$this->countModules('bodytop1') &&
	 !$this->countModules('bodytop2') &&
	 !$this->countModules('bodytop3') &&
	 !$this->countModules('bodybottom1') &&
	 !$this->countModules('bodybottom2') &&
	 !$this->countModules('bodybottom3') &&
	 !$this->countModules('left') &&
	 !$this->countModules('right') &&
	 !$this->countModules('inset') &&
	 !$this->countModules('insettop') &&
	 !$this->countModules('insetbottom')&& 
	 $turn_component_off == 1 ) {	 
	$midblock_off = true;
	
}else{
	$midblock_off = false;
}

$responsive_on 		=  $this->params->get("responsive_on",1);


/* top menu top offsets | if empty top offset is 30 otherwise top offset is what you set here */
$DDverticalTopOffset ='';// SmoothDropdown
$DLverticalTopOffset ='';// SmoothDropline

/* top menu location */
$topMenuLocation 	 =0; // 1 = inside the header block next to logo

// check for topmenupoz navbar bootstrap menu
$navbar_loaded 		= FALSE;
$navbar_class  		= '';
$topmenupoz_name  	= $menu_name;
if($this->countModules('topmenupoz') && $default_menu_style ==6) :
  $top_menu_mod_poz =  JModuleHelper::getModules( 'topmenupoz' );
  foreach($top_menu_mod_poz as $key => $all_menus){
	  $mod_params = json_decode($top_menu_mod_poz[$key]->params);
	  
	  $topmenupoz_name = $mod_params->menutype;
	  
	  if($mod_params->class_sfx == 'navbar' || $mod_params->class_sfx == 'navbar navbar-inverse'){
		  $navbar_loaded = TRUE;
		  $navbar_class  = '_navbar';
		  break;
	  }elseif($mod_params->class_sfx == 'nav nav-pills'){
		  $navbar_loaded = TRUE;
		  $navbar_class  = ' navpills';
		  break;
	  }
  }
endif;

// load mobile menu list only if needed
$load_mobile_list = FALSE;
if( ($responsive_on == 1 && ($topmenu_off == 2 || $itemid == 0 )) && ($template_menus || ($this->countModules('topmenupoz') && !$navbar_loaded ))){
			$load_mobile_list = TRUE;
}

// compiler vars
$less_compiler_on 		= $this->params->get("less_compiler_on");
$compile_css 			= $this->params->get("compile_css");
$compiler_compressed	= $this->params->get("compiler_compressed");
$use_compiled_css		= $this->params->get("use_compiled_css");
$ajax_front_recompile	= $this->params->get("ajax_front_recompile");
$buffer_front_recompile	= $this->params->get("buffer_front_recompile");

/* Paths vars */
$compiler_log 			= YJSGPATH."css_compiled/yjsg_compiler_log.php";
$less_dir 				= YJSGPATH."less/";
$css_dir  				= YJSGPATH."css_compiled/";
$yjsg_compiler			= YJSGPATH."yjsgcore/lib/yjsg_compile.php";
$input_less				= $less_dir."template.less";
$layout_file 			= YJSGPATH."css/layout.css";


if($use_compiled_css == 1){
	 $output_css			= $css_dir."template-".$css_file.".css";
}else{
	 $output_css			= $css_dir."bootstrap-".$css_file.".css";
}

// ie8 pie custom var
$pie_add_more='';

// video.js
$videojs_on				= $this->params->get("videojs_on");
$videojs_vimeo_on		= $this->params->get("videojs_vimeo_on");
$videojs_youtube_on		= $this->params->get("videojs_youtube_on");

// site vars 
$yjsg_assigements 		= explode(',',$this->params->get("yjsg_assigements"));
$sp 					= JURI::base();
$fontc					= $this->template.'_'.filesize($layout_file).filemtime($layout_file);



$logo_per_width = str_replace('px','',$logo_width);

// rtl custom body class 
$rtlClass ='';
if($text_direction == 1 ){
	$rtlClass =' yjsgrtl';
}


// custom color styling 
require( YJSGPATH."yjsgcore/lib/yjsg_color.php");
$yjsg_color = new Yjsgcolor($style_color);


//INSERT TEMPLATE CUSTOM PARAMS
require( YJSGPATH."custom/yjsg_template_custom.php");


//INSERT USER CUSTOM PARAMS
// create yjsg_custom_params.php file
$custom_params_file		= dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."custom".DIRECTORY_SEPARATOR."yjsg_custom_params.php";
$custom_params_content 	="<?php ".PHP_EOL."/**".PHP_EOL."* yjsg_custom_params.php file created by ".$yj_templatename." Template".PHP_EOL."* @package ".$yj_templatename." Template".PHP_EOL."* @author Youjoomla.com".PHP_EOL."* @website Youjoomla.com ".PHP_EOL."* @copyright	Copyright (c) since 2007 Youjoomla.com.".PHP_EOL."* @license PHP files are released under GNU/GPL V2 Copyleft License.CSS / LESS / JS / IMAGES are Copyrighted material".PHP_EOL."**/".PHP_EOL."defined( '_JEXEC' ) or die( 'Restricted index access' );".PHP_EOL."/*".PHP_EOL.PHP_EOL."  this file 'sees' all template params and is loaded after them".PHP_EOL."  you can add own code and overwrite core with this file".PHP_EOL."  For more details please see:".PHP_EOL."  http://www.yjsimplegrid.com/documentation/advanced/using-custom-params-file.html".PHP_EOL.PHP_EOL."*/".PHP_EOL."?>";
	
	
	if(!JFile::exists($custom_params_file)){
		JFile::write($custom_params_file,$custom_params_content);
	}
	if(JFile::exists($custom_params_file)){
		require( YJSGPATH."custom/yjsg_custom_params.php");
	}

// compile process
$compileme 		=0;// ajax recompile key
$buffer_reload 	=0;
if($less_compiler_on == 1){
	
// check jbootsrtap state	
		if(intval(JVERSION) < 3 ){	
			$checker = JPATH_SITE.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR."JBootstrap".DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."checker.txt";
			$jb_plugin_unpublished = !JPluginHelper::getPlugin('system', 'JBootstrap') && Jfile::exists($checker);
		
		// check jbootstrap checker file if there delete log and force recompile
			if(file_exists($compiler_log) && (file_exists($checker) && filemtime($checker) > filemtime($compiler_log))){
				JFile::delete($compiler_log);
			}	
		}else{
			$jb_plugin_unpublished = false;
		}
		
		$yjsanity 				= JFilterInput::getInstance();
		$need_to_compile 		= $yjsanity->clean(JRequest::getInt('recompile'),'INT');		
		

		
		// get the compiler only if we need to recompile 
		if( $need_to_compile == 1  ){
			require_once ($yjsg_compiler);
		}

		// buffer
		$observer_recompile 			= array();
		$observer_recompile['handler'] 	= "yjsg_buffer_recompile";
		$observer_recompile['event']  	= "onAfterRender";
		
		function yjsg_buffer_recompile (){
			$buffer = JResponse::getBody();
			JResponse::setBody('');
			header("Connection: close\r\n");
			ignore_user_abort(true);
			ob_start();
			echo $buffer;
			$size=ob_get_length();
			header("Content-Length: $size");
			ob_end_flush();
			flush();
			ob_end_clean();
			file_get_contents(JURI::base().'?recompile=1');
			
		}

		// if no log or no compressed file we need to recompile
		if(!JFile::exists($compiler_log) || !JFile::exists($output_css) || $jb_plugin_unpublished){
			
			
			// if plugin just got published force recompile
			if($jb_plugin_unpublished){
				JFile::delete($checker);
				JFile::delete($output_css);
				JFile::delete($compiler_log);
			}	
				  // ajax recompile
				  if($ajax_front_recompile == 1){
					  
					  $compileme =1;	
				  // buffer recompile  
				  }elseif($buffer_front_recompile == 1){
					
					  $dispatcher->attach($observer_recompile);
					
				  }else{
				   //normal recompile   
					  require_once ($yjsg_compiler);
				  }
		}
		
		//check if file is changed and if yes recompile
		if(JFile::exists($compiler_log)){
			require_once ($compiler_log);
			$files 			= unserialize($YjsgCompilerLog);
			
			foreach($files['files'] as $filename => $filetime){
				
				if ((!file_exists($filename)) or filemtime($filename) > $filetime) {
				 
					 // ajax recompile
					  if($ajax_front_recompile == 1){
						  JFile::delete($compiler_log);
						  $compileme =1;
							
					  }elseif($buffer_front_recompile == 1){
					 // buffer recompile
					 	  JFile::delete($compiler_log);
						  $dispatcher->attach($observer_recompile);
						
					  }else{
					  //normal recompile  
						  require_once ($yjsg_compiler);
					  }
					  break;
					
				}
			}
		}

	
}
//yjsg rearange the css files
	$observer_css 				= array();
	$observer_css['handler'] 	= "reorder_css_head";
	$observer_css['event']  	= "onBeforeCompileHead";
	$dispatcher->attach($observer_css);
	
	function reorder_css_head(){
	
		$document = JFactory::getDocument();

		$last_array	= array();
		foreach($document->_styleSheets as $style_path => $file){
			if(strstr($style_path, '/templates/'.$document->template) || strstr($style_path, 'http://fonts.googleapis.com')){
				$last_array[$style_path] = $document->_styleSheets[$style_path];
				unset($document->_styleSheets[$style_path]);
			}
		}
		$changed_styles 		= array_merge($document->_styleSheets, $last_array);
		$document->_styleSheets = $changed_styles;

	}