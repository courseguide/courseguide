<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class com_rssearchInstallerScript 
{
	function install($parent) {}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}
	
	public function preflight($type, $parent) {
		$app = JFactory::getApplication();
		
		$jversion = new JVersion();
		if (!$jversion->isCompatible('2.5.5')) {
			$app->enqueueMessage('Please upgrade to at least Joomla! 2.5.5 before continuing!', 'error');
			return false;
		}
		
		return true;
	}

	function postflight($type, $parent) {
		$db = JFactory::getDbo();
		
		// Get a new installer
		$installer = new JInstaller();
		
		$plugins = array();
		
		// Install the module
		$installer->install($parent->getParent()->getPath('source').'/other/module');
		$plugins[] = 'RSSearch! search module';
		
		// Install the plugins
		$installer->install($parent->getParent()->getPath('source').'/other/plugins/content');
		$plugins[] = 'Content plugin';

		$this->showInstall($plugins);
	}

	function uninstall($parent) {
		$db			= JFactory::getDbo();
		$installer	= new JInstaller();

		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('mod_rssearch').' AND '.$db->qn('type').' = '.$db->q('module').' LIMIT 1');
		$mid = $db->loadResult();
		if ($mid) $installer->uninstall('module', $mid);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('content').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$content = $db->loadResult();
		if ($content) $installer->uninstall('plugin', $content);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('eventlist').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$eventlist = $db->loadResult();
		if ($eventlist) $installer->uninstall('plugin', $eventlist);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('jevents').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$jevents = $db->loadResult();
		if ($jevents) $installer->uninstall('plugin', $jevents);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('k2').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$k2 = $db->loadResult();
		if ($k2) $installer->uninstall('plugin', $k2);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('kunena').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$kunena = $db->loadResult();
		if ($kunena) $installer->uninstall('plugin', $kunena);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('mosets').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$mosets = $db->loadResult();
		if ($mosets) $installer->uninstall('plugin', $mosets);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rsblog').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$rsblog = $db->loadResult();
		if ($rsblog) $installer->uninstall('plugin', $rsblog);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rsevents').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$rsevents = $db->loadResult();
		if ($rsevents) $installer->uninstall('plugin', $rsevents);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rsfiles').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$rsfiles = $db->loadResult();
		if ($rsfiles) $installer->uninstall('plugin', $rsfiles);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rseventspro').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$rseventspro = $db->loadResult();
		if ($rseventspro) $installer->uninstall('plugin', $rseventspro);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('rsmembership').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$rsmembership = $db->loadResult();
		if ($rsmembership) $installer->uninstall('plugin', $rsmembership);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('sobipro').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$sobipro = $db->loadResult();
		if ($sobipro) $installer->uninstall('plugin', $sobipro);
		
		$db->setQuery('SELECT '.$db->qn('extension_id').' FROM '.$db->qn('#__extensions').' WHERE '.$db->qn('element').' = '.$db->q('virtuemart').' AND '.$db->qn('folder').' = '.$db->q('rssearch').' AND '.$db->qn('type').' = '.$db->q('plugin').' LIMIT 1');
		$virtuemart = $db->loadResult();
		if ($virtuemart) $installer->uninstall('plugin', $virtuemart);
		
		$this->showUninstall();
	}
	
	protected function showUninstall() {
		echo JText::_('COM_RSSEARCH_UNINSTALL_SUCCESS');
	}
	
	protected function showInstall($plugins) {
?>
<style type="text/css">
.version-history {
	margin: 0 0 2em 0;
	padding: 0;
	list-style-type: none;
}
.version-history > li {
	margin: 0 0 0.5em 0;
	padding: 0 0 0 4em;
}
.version-new,
.version-fixed,
.version-upgraded {
	float: left;
	font-size: 0.8em;
	margin-left: -4.9em;
	width: 4.5em;
	color: white;
	text-align: center;
	font-weight: bold;
	text-transform: uppercase;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}

.version-new {
	background: #7dc35b;
}
.version-fixed {
	background: #e9a130;
}
.version-upgraded {
	background: #61b3de;
}

.install-ok {
	background: #7dc35b;
	color: #fff;
	padding: 3px;
}

.install-not-ok {
	background: #E9452F;
	color: #fff;
	padding: 3px;
}

#installer-left {
	float: left;
	width: 230px;
	padding: 5px;
}

#installer-right {
	float: left;
}

.com-rssearch-button {
	display: inline-block;
	background: #459300 url(components/com_rssearch/assets/images/icons/bg-button-green.gif) top left repeat-x !important;
	border: 1px solid #459300 !important;
	padding: 2px;
	color: #fff !important;
	cursor: pointer;
	margin: 0;
	-webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
	text-decoration: none !important;
}

.big-warning {
	background: #FAF0DB;
	border: solid 1px #EBC46F;
	padding: 5px;
}

.big-warning b {
	color: red;
}
</style>
<div id="installer-left">
	<img src="components/com_rssearch/assets/images/rssearch-box.png" alt="RSSearch! Box" />
</div>
<div id="installer-right">
	<?php if ($plugins) { ?>
		<?php foreach ($plugins as $plugin) { ?>
		<p><?php echo $this->escape($plugin); ?> ...
			<b class="install-ok">Installed</b>
		</p>
		<?php } ?>
	<?php } ?>
	<ul class="version-history">
		<li><span class="version-upgraded">Upg</span> Joomla! 3.x compatibility (including responsive design &amp; bootstrap compatibility).</li>
		<li><span class="version-upgraded">Upg</span> Refactored code to use less resources.</li>
	</ul>
	<a class="com-rssearch-button" href="index.php?option=com_rssearch">Start using RSSearch!</a>
	<a class="com-rssearch-button" href="http://www.rsjoomla.com/support/documentation/view-knowledgebase/117-rssearch.html" target="_blank">Read the RSSearch! User Guide</a>
	<a class="com-rssearch-button" href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
</div>
<div style="clear: both;"></div>
<?php
	}
}