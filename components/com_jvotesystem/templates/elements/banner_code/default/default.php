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
<?php if($par->load) { ?><script type="text/javascript"> <?php }?>
	google_ad_client = "<?php echo $par->adsense_key;?>";
	google_ad_width = 468;
	google_ad_height = 60;
	google_ad_format = "468x60_as";
	google_ad_type = "text";
	google_color_border = "AACCEE";
	google_color_bg = "F6FCF6";
	google_color_link = "1F5F9F";
	google_color_text = "000000";
	google_color_url = "1F5F93";
	google_ui_features = "rc:0";
	<?php if($par->load) { ?>jVS.loadBannerScript();<?php }?>
<?php if($par->load) { ?></script><?php }?>
