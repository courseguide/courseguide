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
if ($turn_header_block_off == 2 ){ ?>
 <!--header-->
<div id="header" style="height:<?php echo $logo_height?>;">
  <?php if ($turn_logo_off == 2 ){ ?>
    <div id="logo" class="png" style="height:<?php echo $logo_height?>;width:<?php echo $logo_out?>%;">
     <?php if ($turn_seo_off == 1 ){ ?>
      <h1><a href="<?php echo $yj_base ?>" style="height:<?php echo $logo_height?>;" title="<?php echo $tags?>"><?php echo $seo ?></a> </h1>
     <?php }else{ ?>
      <a href="<?php echo $yj_base ?>" style="height:<?php echo $logo_height?>;"></a>
      <?php } ?>
    </div>
    <!-- end logo -->
   <?php } ?>
<?php 
if($topMenuLocation == 1){
	require( YJSGPATH."layouts/yjsg_topmenu.php");
}else{
	yjsg_print_grid_area('yjsgheadergrid');
}
?>
	<div class="yjsg-clear-all"></div>
</div>
  <!-- end header -->
<?php } ?>