<?php
/**
* @version 1.0.0
* @package RSSearch! 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

define('RS_PRODUCT',	'RSSearch!');
define('RS_VERSION',	'1.0.0');
define('RS_REVISION',	'4');

class rssearchViewRssearch extends JViewLegacy {
	
	public function display($tpl = null) {
		JToolBarHelper::title('RSSearch!','rssearch');
		
		$this->moduleid	= $this->get('moduleid');
		$this->plugins	= $this->get('plugins');
		
		parent::display($tpl);
	}
}