<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

/* The following line loads the MooTools JavaScript Library */
JHtml::_('behavior.framework', true);

/* The following line gets the application object for things like displaying the site name */
$app = JFactory::getApplication();
?>
<?php echo '<?'; ?>xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	    <meta name="robots" content="<?php echo @$headData["metaTags"]["standard"]["robots"];?>" />
	    <meta name="description" content="<?php echo $headData["description"];?>" />
	    <title><?php echo $headData["title"];?></title>
		<?php foreach($headData["styleSheets"] AS $key => $stylesheet) { ?>
			<link rel="stylesheet" href="<?php echo $key ?>" type="<?php echo $stylesheet["mime"] ?>" media="<?php echo $stylesheet["media"] ?>" />
		<?php } ?>
		<?php foreach($headData["scripts"] AS $key => $script) { ?>
			<script type="text/javascript" src="<?php echo $key ?>"></script>
		<?php } ?>
		<script type="text/javascript">
			<?php echo implode("\n", $headData["script"]);?>
		</script>
	</head>
	<body>
		<div id="overlay"> </div>
		<?php echo $component;?>
	</body>
</html>
