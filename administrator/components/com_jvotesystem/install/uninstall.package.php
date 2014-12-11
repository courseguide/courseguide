<?php
/**
 * This is the special installer addon created by Andrew Eddie and the team of jXtended.
 * We thank for this cool idea of extending the installation process easily
 * @copyright 2005-2008 New Life in IT Pty Ltd.  All rights reserved.
 */

/**
 * @package Component jVoteSystem for Joomla! 1.5
 * @projectsite www.joomess.de/projekte/18
 * @author Johannes Meßmer
 * @copyright (C) 2010- Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport('joomla.application.helper');

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * CONTACT JOOMESS REGISTRATION SYSTEM
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

require_once JPATH_SITE.DS.'plugins'.DS.'system'.( (version_compare( JVERSION, '1.6.0', 'lt' )) ? "" : DS.'joomessLibrary' ).DS.'joomessLibrary.php';
joomessLibrary::getInstance()->connectToServer(1, 'uninstalledExtension');

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * Remove modules and plugins -- BEGIN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/

// -- General settings
jimport('joomla.installer.installer');
$db = & JFactory::getDBO(); 

if(!version_compare(JVERSION,'1.6.0','lt')) {
	$sql = 'SELECT `extension_id` AS id, `name`, `element`, `folder` FROM #__extensions WHERE `type` = "plugin" AND ('
		. '(`element` = "jvotesystemcontent" AND `folder` = "content") OR '
		. '(`element` = "jvotesystembutton" AND `folder` = "editors-xtd") OR '
		. '(`element` = "jvotesystemdatabase" AND `folder` = "system") OR '
		. '(`element` = "jomsocial" AND `folder` = "jvotesystem") ) ';
} else {
	$sql = 'SELECT `id`, `name`, `element`, `folder` FROM #__plugins WHERE ('
		. '(`element` = "jvotesystemcontent" AND `folder` = "content") OR '
		. '(`element` = "jvotesystembutton" AND `folder` = "editors-xtd") OR '
		. '(`element` = "jvotesystemdatabase" AND `folder` = "system") OR '
		. '(`element` = "jomsocial" AND `folder` = "jvotesystem") ) ';
}
$db->setQuery($sql);
$plugins = $db->loadObjectList();

foreach($plugins AS $plugin) {
	$installer = new JInstaller;
	$result = $installer->uninstall('plugin', $plugin->id, 1);
	$status->plugins[] = array('name'=>$plugin->name,'group'=>$plugin->folder, 'result'=>$result);
}

if(!version_compare(JVERSION,'1.6.0','lt')) {
	$sql = 'SELECT `extension_id` AS id, `name`, `element`, `folder` FROM #__extensions WHERE `type` = "module" AND ('
		. '(`element` = "mod_jvotesystemmodule") ) ';
} else {
	$sql = 'SELECT `id`, `name`, `element`, `folder` FROM #__modules WHERE ('
		. '(`element` = "mod_jvotesystemmodule") ) ';
}
$db->setQuery($sql);
$modules = $db->loadObjectList();

foreach($modules AS $module) {
	$installer = new JInstaller;
	$result = $installer->uninstall('module', $module->id, 1);
	$status->modules[] = array('name'=>$module->name,'group'=>$module->folder, 'result'=>$result);
}

/***********************************************************************************************
 * ---------------------------------------------------------------------------------------------
 * OUTPUT TO SCREEN
 * ---------------------------------------------------------------------------------------------
 ***********************************************************************************************/
$rows = 0;
?>

<h2>jVoteSystem Removal</h2>
<table class="adminlist">
    <thead>
        <tr>
            <th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
            <th width="30%"><?php echo JText::_('Status'); ?></th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="3"></td>
        </tr>
    </tfoot>
    <tbody>
        <tr class="row0">
            <td class="key" colspan="2"><?php echo 'jVoteSystem '.JText::_('Component'); ?></td>
            <td><img src="images/publish_g.png" alt="OK" /><strong><?php echo JText::_('Removed'); ?></strong></td>
        </tr>
        <?php if (count($status->modules)) : ?>
        <tr>
            <th><?php echo JText::_('Module'); ?></th>
            <th></th>
        </tr>
        <?php foreach ($status->modules as $module) : ?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo $module['name']; ?></td>
            <td><img src="images/publish_g.png" alt="OK" /><strong><?php echo JText::_('Removed'); ?></strong></td>
        </tr>
        <?php endforeach;
        endif;
        if (count($status->plugins)) : ?>
        <tr>
            <th><?php echo JText::_('Plugin'); ?></th>
            <th><?php echo JText::_('Group'); ?></th>
            <th></th>
        </tr>
        <?php foreach ($status->plugins as $plugin) : ?>
        <tr class="row<?php echo (++ $rows % 2); ?>">
            <td class="key"><?php echo ucfirst($plugin['name']); ?></td>
            <td class="key"><?php echo ucfirst($plugin['group']); ?></td>
            <td><img src="images/publish_g.png" alt="OK" /><strong><?php echo JText::_('Removed'); ?></strong></td>
        </tr>
        <?php endforeach;
        endif; ?>
    </tbody>
</table>

<table class="adminlist">
	<tbody><tr><td style="width: 130px;">
		<a href="http://je.joomess.de/forum.html" target="_blank">
			<img src="http://joomess.de/images/stories/icon-128-help.png" style="float:left;border:0 none;" title="Help" alt="Help" />
		</a>
	</td><td style="vertical-align: middle;">
		<span style="font-size: 16px; font-style: italic; font-weight: bold; ">
			If you have problems when using the extension or have any questions, the support will gladly help you!<br />
			We can solve most problems without much effort! Do not hesitate to ask! <br />
			Just use our <a href="http://joomess.de/forum.html" target="_blank">Forum</a>.
		</span>
	</td></tr>
</tbody></table>
