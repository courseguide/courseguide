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
/* 
	Joomla! Template Based on YJSG Framework version 1.0.16 Stable - 11-01-2013
*/
defined( '_JEXEC' ) or die( 'Restricted index access' );
define( 'TEMPLATEPATH', dirname(__FILE__) );
define( 'YJSGPATH', TEMPLATEPATH.DIRECTORY_SEPARATOR);
require( YJSGPATH."yjsgcore/yjsg_core.php");/* YJSGFramework main functions*/
?>
<!DOCTYPE html>
<html xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
<?php require( YJSGPATH."yjsgcore/yjsg_head.php");/* <head> files containing css , js and php */?>
</head>
<body id="stylef<?php echo $default_font_family ?>" class="contentpane" style="font-size:<?php echo $css_font; ?>;">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
