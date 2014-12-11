<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5-2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

?>

<p><?php echo sprintf(JText::_("JVS_MAIL_UPDATE_MESSAGE"), $par->website, $par->version); ?></p>
<p><b>Download: </b><a href="<?php echo $par->download;?>"><?php echo $par->download;?></a></p>
<p><b>Changelog: </b></p>
<?php foreach($par->changelog AS $log) { ?>
<p><i><u> Version <?php echo $log->version;?>: </u></i> <br>
<?php echo $log->changelog;?>
</p>
<?php }?>