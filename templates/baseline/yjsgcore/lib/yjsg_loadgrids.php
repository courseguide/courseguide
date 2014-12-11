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
/*check if grids are present*/
$yjsg1_loaded 				= false;
$yjsg_header_loaded 		= false;
$yjsg2_loaded 				= false;
$yjsg3_loaded 				= false;
$yjsg_bodytop_loaded 		= false;
$yjsg_bodybottom_loaded 	= false;
$yjsg4_loaded 				= false;
$yjsg5_loaded 				= false;
$yjsg6_loaded 				= false;
$yjsg7_loaded 				= false;
$yjsgTopPanel_loaded		= false;
$yjsgBotPanel_loaded 		= false;

//yjsg1
for( $i=1; $i<=5; $i++ ){
	$mods_yjsg1 	= $this->countModules('top'.$i);
	if( $mods_yjsg1){
		$yjsg1_loaded = true;
		break;
	}
}
//header grid
for( $i=1; $i<=5; $i++ ){
	$mods_yjsg_header 	= $this->countModules('header'.$i);
	if( $mods_yjsg_header){
		$yjsg_header_loaded = true;
		break;
	}
}
//yjsg2
for( $i=1; $i<=5; $i++ ){
	$mods_yjsg2 	= $this->countModules('adv'.$i);
	if( $mods_yjsg2){
		$yjsg2_loaded = true;
		break;
	}
}
//yjsg3
for( $i=1; $i<=5; $i++ ){
	$mods_yjsg3 	= $this->countModules('user'.$i);
	if( $mods_yjsg3){
		$yjsg3_loaded = true;
		break;
	}
}
//bodybottom
for( $i=1; $i<=5; $i++ ){
	$mods_yjsg_bodybottom 	= $this->countModules('bodybottom'.$i);
	if( $mods_yjsg_bodybottom){
		$yjsg_bodybottom_loaded = true;
		break;
	}
}
//bodytop
for( $i=1; $i<=5; $i++ ){
	$mods_yjsg_bodytop 	= $this->countModules('bodytop'.$i);
	if( $mods_yjsg_bodytop){
		$yjsg_bodytop_loaded = true;
		break;
	}
}
//yjsg4
for( $i=6; $i<=10; $i++ ){
	$mods_yjsg4 	= $this->countModules('user'.$i);
	if( $mods_yjsg4){
		$yjsg4_loaded = true;
		break;
	}
}
//yjsg5
for( $i=11; $i<=15; $i++ ){
	$mods_yjsg5 	= $this->countModules('user'.$i);
	if( $mods_yjsg5){
		$yjsg5_loaded = true;
		break;
	}
}
//yjsg6
for( $i=16; $i<=20; $i++ ){
	$mods_yjsg6 	= $this->countModules('user'.$i);
	if( $mods_yjsg6){
		$yjsg6_loaded = true;
		break;
	}
}
//yjsg7
for( $i=21; $i<=25; $i++ ){
	$mods_yjsg7 	= $this->countModules('user'.$i);
	if( $mods_yjsg7){
		$yjsg7_loaded = true;
		break;
	}
}
//top panel
for( $i=1; $i<=5; $i++ ){
	$mods_yjsgtoppanel 	= $this->countModules('tpan'.$i);
	if( $mods_yjsgtoppanel){
		$yjsgTopPanel_loaded = true;
		break;
	}
}
//bottom panel
for( $i=1; $i<=5; $i++ ){
	$mods_yjsgbotpanel 	= $this->countModules('bpan'.$i);
	if( $mods_yjsgbotpanel){
		$yjsgBotPanel_loaded = true;
		break;
	}
}
/* load grids */


function yjsg_print_grid_area($yjsg_grid_name,$add_width =false,$before ='',$after='', $echo = true ){
    
    $document = &JFactory::getDocument();
	$header_grid_width = false;
	  switch ($yjsg_grid_name) {
		  case 'yjsg1':
			  $prefixes = 'top';
			  $maxmods	= 5;
			  $start_key= 1;
			  $get_w_param ='yjsg_1_width';
			  $get_m_style ='YJsg1_module_style';
			  break;
		  case 'yjsgheadergrid':
			  $prefixes = 'header';
			  $maxmods	= 3;
			  $start_key= 1;
			  $get_w_param ='yjsg_header_width';
			  $get_m_style ='YJsgh_module_style';
			  break;
		  case 'yjsg2':
			  $prefixes = 'adv';
			  $maxmods	= 5;
			  $start_key= 1;
			  $get_w_param ='yjsg_2_width';	
			  $get_m_style ='YJsg2_module_style';		  
			  break;
		  case 'yjsg3':
			  $prefixes = 'user';
			  $maxmods	= 5;
			  $start_key= 1;
			  $get_w_param ='yjsg_3_width';
			  $get_m_style ='YJsg3_module_style';			  
			  break;
		  case 'yjsgbodytop':
			  $prefixes = 'bodytop';
			  $maxmods	= 3;
			  $start_key= 1;
			  $get_w_param ='yjsg_bodytop_width';
			  $get_m_style ='YJsgmt_module_style';		  
			  break;
		  case 'yjsgbodybottom':
			  $prefixes = 'bodybottom';
			  $maxmods	= 3;
			  $start_key= 1;
			  $get_w_param ='yjsg_yjsgbodytbottom_width';
			  $get_m_style ='YJsgmb_module_style';			  
			  break;
		  case 'yjsg4':
			  $prefixes = 'user';
			  $maxmods	= 5;
			  $start_key= 6;
			  $get_w_param ='yjsg_4_width';
			  $get_m_style ='YJsg4_module_style';		  
			  break;
		  case 'yjsg5':
			  $prefixes = 'user';
			  $maxmods	= 5;
			  $start_key= 11;
			  $get_w_param ='yjsg_5_width';	
			  $get_m_style ='YJsg5_module_style';		  
			  break;
		  case 'yjsg6':
			  $prefixes = 'user';
			  $maxmods	= 5;
			  $start_key= 16;
			  $get_w_param ='yjsg_6_width';
			  $get_m_style ='YJsg6_module_style';			  
			  break;
		  case 'yjsg7':
			  $prefixes = 'user';
			  $maxmods	= 5;
			  $start_key= 21;
			  $get_w_param ='yjsg_7_width';
			  $get_m_style ='YJsg7_module_style';		  
			  break;
			  
		  case 'toppanel':
			  $prefixes = 'tpan';
			  $maxmods	= 5;
			  $start_key= 1;
			  $get_w_param ='yjsg_toppanel_width';
			  $get_m_style ='toppanel_module_style';		  
			  break;
			  
		  case 'botpanel':
			  $prefixes = 'bpan';
			  $maxmods	= 5;
			  $start_key= 1;
			  $get_w_param ='yjsg_botpanel_width';
			  $get_m_style ='botpanel_module_style';		  
			  break;
			  		
		  case (strstr($yjsg_grid_name,'yjsgName')):
			  $gridKeys 		= explode('|',$yjsg_grid_name);
			  $gridName 		= explode('=',$gridKeys[0]);
			  $gridModName 		= explode('=',$gridKeys[1]);
			  $prefixes 		= $gridModName[1];
			  $maxmods			= 5;
			  $start_key		= 1;
			  $get_w_param 		='yjsg_'.$gridName[1].'_width';
			  $get_m_style 		=$gridName[1].'_module_style';	
			  break;
	  }

	$css_width 			= $document->params->get("css_width");
	$css_widthdefined	= $document->params->get("css_widthdefined");
	$logo_width 		= $document->params->get("logo_width");
	$turn_logo_off 		= $document->params->get("turn_logo_off");
	$logo_out 			= round($logo_width / $css_width*100,2);
	if($turn_logo_off == 1) {
		$headergrid_width = 100;
	}else{
		$headergrid_width = 100 -$logo_out;
	}
	$YJ_modules = array();
	$YJ_max_modules = $maxmods;
	$YJ_module_prefix = $prefixes;
	$YJ_starting_key = $start_key;
	$grid_widths = explode('|', $document->params->get( $get_w_param ));	
	$module_style = $document->params->get( $get_m_style );
	
	$k = 0;
	for( $i = $YJ_starting_key; $i < $YJ_max_modules + $YJ_starting_key; $i++ ){	
		
		$mod_name = $YJ_module_prefix.$i;
		if ( $document->countModules( $mod_name ) && array_key_exists($k, $grid_widths) ){
			
			$width = $grid_widths[$k];
			if( is_numeric( $width ) && $width > 0 )
				$YJ_modules[$i] = $width;	
			
		}	
		$k++;	
	}
	$total_size = array_sum( $YJ_modules );
	if( $total_size < 100 && $YJ_modules ){
		
		$remaining_size = 100 - $total_size;
		foreach ( $YJ_modules as $k=>$module ){
			
			$percent = $module / $total_size;
			$YJ_modules[$k] = number_format( $module + $remaining_size * $percent, 2);
			
		}	
	}
	
	$check_size = array_sum( $YJ_modules );
	if( $check_size > 100 && $YJ_modules ){
		$ratio = ($check_size-100)/100;
		if( $ratio > 1 ){
			foreach ( $YJ_modules as $k=>$m ){
				$YJ_modules[$k] = $m/$ratio;			
			}	
			$check_size = array_sum( $YJ_modules );		
		}
		
		$plus_size = ($check_size - 100) / count( $YJ_modules );
		foreach ( $YJ_modules as $k=>$m ){
			$final_size = $m - $plus_size;
			if( $final_size < 1 ){
				unset( $YJ_modules[$k] );
				continue;
			}	
			$YJ_modules[$k] = $final_size;
		}
	}
    
// print grids
	if(!empty($YJ_modules)){
		if($add_width && $yjsg_grid_name !='yjsgheadergrid'){
			$add_width = ' yjsgsitew';
		}else if($yjsg_grid_name == 'yjsgheadergrid'){
			$add_width = ' yjsgheadergw';
		}else{
			$add_width ='';
		}

		if(strstr($yjsg_grid_name,'yjsgName')){
					 $gridKeys 		= explode('|',$yjsg_grid_name);
					 $gridName 		= explode('=',$gridKeys[0]);
					 $gridDivName	= $gridName[1];
		}else{
					$gridDivName	= $yjsg_grid_name;
		}
		$html = '<div id="'.$gridDivName.'" class="yjsg_grid'.$add_width.'">';
		
		  
		  

		  $cm = 1;
		  foreach ($YJ_modules as $k=>$mod_width){ 
		  		
				  $firstLast = '';
				  $allLast	 = '';
				  $clearRow  = '';
				  if((count($YJ_modules) == 5 && $cm == 5) || (count($YJ_modules) == 3 && $cm == 3)){
					  $firstLast = ' last_mod';
					 
				  }elseif(count($YJ_modules) == 1){
						 
						  $firstLast = ' only_mod';
						  
				  }elseif(count($YJ_modules) > 1 && $cm == 1){
						 
						  $firstLast = ' first_mod';
						  
				  }
				  
				  if(count($YJ_modules) == 4 && $cm  == 3){
						 
						  $clearRow = ' yjsgclearrow';
						  
				  }
				  
				  if(count($YJ_modules) > 1 && $cm  == count($YJ_modules)){
						 
						  $allLast = ' lastModule';
				  }
				  				  
		          $cm++;

			$mod_name = $YJ_module_prefix.$k; 
			if( !$mod_width ) continue;
			$html .='<div id="'.$mod_name.'" class="yjsgxhtml'.$firstLast.$clearRow.$allLast.'" style="width:'.$mod_width.'%;">';
			$html .='<jdoc:include type="modules" name="'.$mod_name.'" style="'.$module_style.'" />';
			$html .='</div>'; 
		  }
		$html .= '</div>';

		if( $echo ){
		  echo $before.$html.$after;
		}else{
		  return $before.$html.$after;
		}
		
	}else{
		return;
	}

}