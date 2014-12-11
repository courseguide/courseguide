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
defined( '_JEXEC' ) or die( 'Restricted access' );
$app 							= JFactory::getApplication();
$yjsg_params 					= $app->getTemplate(true)->params;
$template 						= $this->template;
$check_style_param 				= $yjsg_params->get("yjsg_get_styles");
if(isset($check_style_param)){
	$get_style_value			= explode('|',$yjsg_params->get("yjsg_get_styles"));
	$yjsg_get_styles			= $get_style_value[0];	
	$default_link_color			= $get_style_value[1];
	$site_link_color			= '#'.$default_link_color;	
}else{
 	$yjsg_get_styles			= $yjsg_params->get("default_color");
	$default_link_color			= '';
}
$default_font_family          	= $yjsg_params->get("default_font_family");
$logo_height                  	= $yjsg_params->get("logo_height");
$default_font                 	= $yjsg_params->get("default_font");
?>
<!DOCTYPE html>
<html xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<title><?php echo $this->error->getCode(); ?>-<?php echo $this->title; ?></title>
		<link href="<?php echo JURI::base(); ?>templates/<?php echo $template ?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
		<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $template ?>/css/template.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $template ?>/css/<?php echo $yjsg_get_styles; ?>.css" rel="stylesheet" type="text/css" />
	</head>
	<body id="stylef<?php echo $default_font_family ?>" class="yjsgerror-page">
		<div id="centertop">
			<div id="errorpage">
				<div id="header" style="height:<?php echo $logo_height?>;">
					<img src="<?php echo $this->baseurl; ?>/templates/<?php echo $template ?>/images/<?php echo $yjsg_get_styles; ?>/logo.png" alt="site_logo" />
				</div>
				<div class="error_title">
					<h1><?php echo $this->error->getCode(); ?></h1>
					<h2><?php echo $this->error->getMessage(); ?></h2>
				</div>
				<p class="errorp">
					<strong><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></strong>
				</p>
				<ol id="errorol">
					<li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
					<li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
					<li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
					<li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
					<li><?php echo JText::_('JERROR_LAYOUT_REQUESTED_RESOURCE_WAS_NOT_FOUND'); ?></li>
					<li><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></li>
				</ol>
				<p class="errorp">
					<strong><?php echo JText::_('JERROR_LAYOUT_PLEASE_TRY_ONE_OF_THE_FOLLOWING_PAGES'); ?></strong>
				</p>
				<ol class="error_link">
					<li>
						<a href="<?php echo $this->baseurl; ?>/index.php" title="<?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?>">
							<?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->baseurl; ?>/index.php?option=com_search" title="<?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?>">
							<?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?>
						</a>
					</li>
				</ol>
				<p class="error_contact">
					<?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?>
				</p>
				<p class="error_msg">
					<?php echo $this->error->getMessage(); ?>
				</p>
				<p>
					<?php if($this->debug) : echo $this->renderBacktrace();	endif; ?>
				</p>
			</div>
		</div>
	</body>
</html>