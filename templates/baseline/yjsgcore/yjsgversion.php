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
	
	
	// CURRENT YJSG VERSION
	$your_yjsgversion ='1.0.16';
	
	$yjsg_version = @JFile::read('http://www.youjoomla.com/yjsgversion/yjsgversion.txt');
?>
<body style="background:#fff; font-family:Arial, Helvetica, sans-serif; padding:30px 0 0 0;">
<div class="yjsgversion" style="color:#444;text-align:center;font-weight:bold;height:80px;line-height:20px;">
<?php if ($yjsg_version && ini_get('allow_url_fopen') == '1' ) { ?>
		Current YJSG Version is <?php echo $yjsg_version ?><br />
	<?php if ($your_yjsgversion !== $yjsg_version){ ?>
		Your YJSG Version is <span style="color:#b00201;"><?php echo $your_yjsgversion ?>!</span><br />
	 	<a href="http://www.youjoomla.com/joomla_support/yougrids/" style="color:green;" target="_blank">Visit forums for manual update</a>
		<br />
		or download and reinstall your template.
	<?php }else{ ?>
		<span style="color:green;">Your YJSG Version is up to date</span>
	<?php } ?>
<?php // case allow_url_fopen is off ?>
<?php }elseif (ini_get('allow_url_fopen') !== '1' ) { ?>
			allow_url_fopen is not enabled on your server.<br />
			Click <a href="http://www.youjoomla.com/yjsgversion/yjsgversion.html" style="color:red;"  target="_blank">here</a> to compare 									            YJSG Version Directly.
<?php }else{ ?>
			<span style="color:#fff;">Unable to check your version at this time.Please try again later.</span>
<?php } ?>
</div>
</body>