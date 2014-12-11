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
defined( '_JEXEC' ) or die( 'Restricted index access' ); ?>
<?php 
// load mootools with mootools more , without true , more is not loaded
JHtml::_('behavior.framework',true);
$document->addStyleDeclaration("body{font-size:".$css_font.";}");
//Boostrap framework for Joomla 3.0 or  2.5 if JBoostrap plugin is enabled
$bootsrtap_here = FALSE;
if(intval(JVERSION) >= 3 || JPluginHelper::getPlugin('system', 'JBootstrap')) {
	JHtml::_('bootstrap.framework');
	$document->_script = str_replace('hasTip','nomoretips',$document->_script);
	$bootsrtap_here = TRUE;
}

// jquery and jquery no conflict if set to on in template manager
if($jq_switch ==1 ){
	$document->addScript($yj_site.'/src/libraries/jquery.js');
	$document->addScript($yj_site.'/src/libraries/jquery-noconflict.js');
}

// remove generator tag
if($joomla_generator_off == 1){
	$this->setGenerator('');
}

// check if compiled and if we want to use compiled CSS
if($use_compiled_css == 1 && JFile::exists($output_css)){
	$compiled_css_on = TRUE;
}else{
	$compiled_css_on = FALSE;
}

// check if compressed css
if($compiled_css_on){
	$document->addStyleSheet($yj_site.'/css_compiled/template-'.$css_file.'.css');
}else{
	if($bootsrtap_here && JFile::exists($output_css)){
		$document->addStyleSheet($yj_site.'/css_compiled/bootstrap-'.$css_file.'.css');
	}
	$document->addStyleSheet($yj_site.'/css/template.css');
	$document->addStyleSheet($yj_site.'/css/'.$css_file.'.css');		
}

if ( $default_menu_style == 3 ||  $default_menu_style == 4 ){ 

	$document->addStyleSheet($yj_site.'/css/dropline.css'); 
	
}

if ($text_direction == 1) {
	
	$document->addStyleSheet($yj_site.'/css/template_rtl.css');
	if($bootsrtap_here){
		$document->addStyleSheet($yj_site.'/css/bootstrap-rtl.css');
	}
	
	if ($yjsgBrowser->Name !='chrome' || $yjsgBrowser->Name !='safari') {
		
		$document->addStyleSheet($yj_site.'/css/menu_rtl.css');
	}
	
	
}
/*responsive layout*/
if($responsive_on == 1) {
	
  $document->addScript($yj_site.'/src/yjresponsive.js');
  if(!$compiled_css_on && $responsive_on   == 1){	
 	 $document->addStyleSheet($yj_site.'/css/yjresponsive.css'); 
	 $document->addStyleSheet($yj_site.'/css/custom_responsive.css'); 
  }
  $document->setMetaData( 'viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
  
  if($yjsg_mobile){
	  
	  $document->setMetaData( 'handheldfriendly', 'true');
	  $document->setMetaData( 'apple-touch-fullscreen', 'yes');
  }
}
/* TOP MENU */
if(empty($DDverticalTopOffset)){
	$DDverticalTopOffset = 30;
}
if(empty($DLverticalTopOffset)){
	$DLverticalTopOffset = 30;
}

/* Calculate offset  percent value for YJ Mega Menu */
$offset_value = ($sub_width / 100) * $yjsg_menu_offset;
$final_offset = number_format($sub_width - $offset_value + 10,0, '.', '') ;

if($topmenu_off == 2 || $itemid == 0 ) {

    
	$smooth_name = '';
	if ( $default_menu_style == 2 && JFactory::getApplication()->input->get('tmpl') !='component') {
	  $document->addScript($yj_site.'/src/mouseover13.js');
	  $yjsg_js.="
		var YJSG_topmenu_font = '".$css_font."';
		window.addEvent('load', function(){
			new SmoothDrop({
				'container':'horiznav',	
				contpoz:0,
				horizLeftOffset: ".$final_offset.", // submenus, left offset
				horizRightOffset: -".$final_offset.", // submenus opening into the opposite direction
				horizTopOffset: 20, // submenus, top offset
				verticalTopOffset:".$DDverticalTopOffset.", // main menus top offset
				verticalLeftOffset: 10, // main menus, left offset
				maxOutside: 50
			});
		});
	  ";	
	}elseif( $default_menu_style == 4 && JFactory::getApplication()->input->get('tmpl') !='component'){
	  $document->addScript($yj_site.'/src/dropd13.js');
	  $yjsg_js.="
		var YJSG_topmenu_font = '".$css_font."';
			var SmoothDroplineParams = {
				'container':'horiznav',	
				contpoz:0,
				horizLeftOffset: ".$final_offset.", // submenus, left offset
				horizRightOffset: -".$final_offset.", // submenus opening into the opposite direction
				horizTopOffset: 20, // submenus, top offset
				verticalTopOffset:".$DLverticalTopOffset.", // main menus top offset
				verticalLeftOffset: 10, // main menus, left offset
				maxOutside: 50
		   };
	  ";		
		
	}elseif( $default_menu_style == 3){

		$document->addScript($yj_site.'/src/donly.js');
		
	}
	
	if ( $default_menu_style == 1 ||  $default_menu_style == 2 ){
		
		$document->addStyleDeclaration(".horiznav li li,.horiznav ul ul a, .horiznav li ul,.YJSG_listContainer{width:".$sub_width.";}");
		
	}elseif ( $default_menu_style == 3 ||  $default_menu_style == 4 ){
		
		$document->addStyleDeclaration("
		.horiznav ul ul.subul_main{width:".$css_width.$css_widthdefined.";}
		.horiznav ul ul.subul_main li a, .horiznav ul ul.subul_main li a:hover{width:auto;}
		.horiznav ul ul.subul_main ul,.horiznav ul ul.subul_main ul a,.horiznav ul ul.subul_main ul a:hover  {width:".$sub_width.";}	
		");
	}
	 
   if ($text_direction == 1) {
	   $yjsg_js.="document.documentElement.style.overflowX = 'hidden';";
	   $document->addStyleDeclaration("
		  a.sublevel {
		  background: url(".$yj_site."/images/".$css_file."/bodyli_rtl.gif) no-repeat 98% 9px;
		  }
		  body li{
		  background: url(".$yj_site."/images/".$css_file."/bodyli_rtl.gif) no-repeat right 6px;
		  }
	   ");
   }
	 
	 
}

// if any other menu but split or menumodpoz
$menus_to_use = array(1,2,3,4);
if(in_array($default_menu_style,$menus_to_use)){
	

	if ($text_direction == 1) {
		
	$document->addStyleDeclaration(".horiznav li ul ul,.subul_main.group_holder ul.subul_main ul.subul_main, .subul_main.group_holder ul.subul_main ul.subul_main ul.subul_main, .subul_main.group_holder ul.subul_main ul.subul_main ul.subul_main ul.subul_main,.horiznav li li li:hover ul.subul_main.dropline{margin-right:".$yjsg_menu_offset."%!important;
margin-top: -32px!important;}");
	
	}else{
		
$document->addStyleDeclaration(".horiznav li ul ul,.subul_main.group_holder ul.subul_main ul.subul_main, .subul_main.group_holder ul.subul_main ul.subul_main ul.subul_main, .subul_main.group_holder ul.subul_main ul.subul_main ul.subul_main ul.subul_main,.horiznav li li li:hover ul.dropline{margin-top: -32px!important;margin-left:".$yjsg_menu_offset."%!important;}");

	}
}
/* end  top menu */
if($selectors_override == 1){
	
	if($selectors_override_type == 1 || $selectors_override_type == 2){// css font or google font
		
		 if($selectors_override_type == 2){
		 	$document->addStyleSheet($font_sheet);
		 }
		 $document->addStyleDeclaration("".$affected_selectors."{font-family:".$nice_font."}");
	}
	
	if (!$compiled_css_on && $selectors_override_type == 3){ // @font-face fontsquirrel font
	
		$document->addStyleSheet($yj_site.'/css/fontfacekits/'.$fontfacekit_font_family.'/stylesheet.css');
		
	}
	

}

/* HTML5,@media, CSS3 for IE8 */
if ($yjsgBrowser->Name =='msie' && $yjsgBrowser->Version == '8.0'){
	
	if(intval(JVERSION) >= 3 || JPluginHelper::getPlugin('system', 'JBootstrap')){
	  $document->addScript($yj_base.'media/jui/js/html5.js');
	}else{
	  $document->addScript('http://html5shim.googlecode.com/svn/trunk/html5.js');
	}
	
	if($responsive_on == 1 ) {
	  $document->addScript($yj_site.'/src/respond.js');
	}
	if(!empty($pie_add_more)){
		$custom_pie = $pie_add_more;
	}else{
		$custom_pie ='';
	}
	$document->addStyleDeclaration("
	
	.itemComments,.itemCommentsForm .inputbox,#submitCommentButton,.itemComments ul.itemCommentsList li,.tagView .itemReadMore a,.userView .itemReadMore a,.genericView .itemReadMore a,.inputbox,.button, .validate,.readon,.jb_pagin a,pre,code".$custom_pie."{
	   behavior: url(".$yj_site."/css/pie/PIE.htc);
	   /*z-index:0!important;*/
	   position:relative!important;
	   /*margin:auto!important;*/
	  }	  
	  div.catItemImageBlock,.tagView .itemImageBlock,
	  .userView .itemImageBlock,.genericView .itemImageBlock {
	   position:relative!important;
	  }
	  .prettyprint{
		  padding:8px 0!important;
		  behavior:none!important;
	  }
	  body ol.linenums{
		  margin:0!important;
	  }
	
	");
	$document->addScript($yj_site.'/src/selectivizr-min.js');
 }

	 
// site scripts
	 if(JFactory::getApplication()->input->get('tmpl') !='component' ){
		$document->addScript($yj_site.'/src/sitescripts.js');
	 }



// site js vars needed for yjsgresponsive.js and sitescripts.js
$yjsg_js.="var logo_w = $logo_per_width;var site_w = $css_width;var site_f = '$css_font';var sp='$sp';var tp ='$this->template';var compileme =$compileme;var fontc ='$fontc';";
if($turn_logo_off == 2 && $css_widthdefined == '%'){
	$yjsg_js.="var site_w_is_per = 1";
}	 


/*GOOGLE ANALYTICS*/
if($ga_switch == 1){
$yjsg_js .="
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '".$GAcode."']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
  ";
}



	 
/*

   Closing of $yjsg_js
   Add your script before
   $yjsg_js.='</script>';
   or in custom/yjsg_custom_params.php 
   by using $yjsg_js.='var =my_js_var';
   dont forget the dot after $yjsg_js
   For better performance and cleaner <head></head>
   $yjsg_js is echoed at the end of the page in yjsg_footer.php
 
*/
$yjsg_js.=PHP_EOL."\t".'</script>'.PHP_EOL;





// video.js
	if(!$compiled_css_on && $videojs_on == 1) {
		$document->addStyleSheet($yj_site.'/css/video-js.min.css');
	}
	if($videojs_on == 1) {
		$document->addScript($yj_site.'/src/video-js/video.min.js');
	}
	if($videojs_on == 1 && $videojs_vimeo_on == 1) {
		$document->addScript($yj_site.'/src/video-js/vimeo.js');
	}
	if($videojs_on == 1 && $videojs_youtube_on == 1) {
		$document->addScript($yj_site.'/src/video-js/youtube.js');
	}



// custom logo
	if($this->params->get("logo_image")){
	  $document->addStyleDeclaration('#logo{background: url('.JURI::base().$this->params->get("logo_image").')  no-repeat 0px 0px; !important;}');
	} 
	
// site links and accent color
//$cc_css="";
if(isset($cc_css)){
	$document->addStyleDeclaration("".$cc_css."");
}

// default site and header grid width class names 

$document->addStyleDeclaration(".yjsgsitew{width:".$css_width.$css_widthdefined.";}.yjsgheadergw{width:".$headergrid_width."%;}");	


// last to load 
	if(!$compiled_css_on && $custom_css   == 1){	
		$document->addStyleSheet($yj_site.'/css/custom.css'); 
	} 
	if(isset($YjsgCustomCss) && is_array($YjsgCustomCss) && !empty($YjsgCustomCss)){
		foreach($YjsgCustomCss as $YjsgCustomFile){
			$document->addStyleSheet($YjsgCustomFile);
		}
	}
	if(isset($YjsgCustomJS) && is_array($YjsgCustomJS) && !empty($YjsgCustomJS)){
		foreach($YjsgCustomJS as $YjsgCustomJSFile){
			$document->addScript($YjsgCustomJSFile);
		}
	}
// add apple touch icon for Apple mobile OS - iOS: Works on Android also
	if($detect->isiOS() || $detect->isAndroidOS()){	
		$document->addCustomTag('<link rel="apple-touch-icon" sizes="57x57" href="'.$yj_site.'/images/system/appleicons/apple-icon-57x57.png" />');	
		$document->addCustomTag('<link rel="apple-touch-icon" sizes="72x72" href="'.$yj_site.'/images/system/appleicons/apple-icon-72x72.png" />');
		$document->addCustomTag('<link rel="apple-touch-icon" sizes="114x114" href="'.$yj_site.'/images/system/appleicons/apple-icon-114x114.png" />');
		$document->addCustomTag('<link rel="apple-touch-icon" sizes="144x144" href="'.$yj_site.'/images/system/appleicons/apple-icon-144x144.png" />');
	}
?>
<jdoc:include type="head" />