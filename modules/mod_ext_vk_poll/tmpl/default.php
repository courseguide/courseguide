<?php
/*
# ------------------------------------------------------------------------
# Extensions for Joomla 2.5.x - Joomla 3.x
# ------------------------------------------------------------------------
# Copyright (C) 2011-2013 Ext-Joom.com. All Rights Reserved.
# @license - PHP files are GNU/GPL V2.
# Author: Ext-Joom.com
# Websites:  http://www.ext-joom.com 
# Date modified: 03/09/2013 - 13:00
# ------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die;
?>
<div id="mod_ext_vk_poll <?php echo $moduleclass_sfx ?>">
	<script type="text/javascript">VK.init({apiId: <?php echo $api_id; ?>, onlyWidgets: true});</script>
	<div id="vk_poll_<?php echo $ext_id;?>"></div>
	<script type="text/javascript">
	VK.Widgets.Poll('vk_poll_<?php echo $ext_id;?>', {width: "<?php echo $width; ?>px"}, '<?php echo $poll_id; ?>');
	</script>
	<div style="clear:both;"></div>
</div>