<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @authors Johannes Meßmer, Andreas Fischer
 * @copyright (C) 2010 - 2012 Johannes Meßmer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport( 'joomla.application.component.model' );

/**
 * jVoteSystem Model
 *
 * @package    jVoteSystem
 * @subpackage Models
 */
class jVoteSystemModelAdvisor extends JModel
{

	var $_advise_php ,$_advise_cookie ,$_advise_livesite ,$_advise_joomla ,$_advise_sh404;

    function __construct()
    {
        parent::__construct();
    }//function
	
	function getAdvise_php()
	{
		if (empty( $this->_advise_php ))
        {
			$this->_advise_php = new JObject();
			$this->_advise_php->check = version_compare(PHP_VERSION, '5.2.0', '>=') ? true : false;
			$this->_advise_php->version = PHP_VERSION;
			$this->_advise_php->required = '5.2.0';
		}
		return $this->_advise_php;
	}
	
	function getAdvise_cookie()
	{
		if (empty( $this->_advise_cookie ))
        {
			$conf =& JFactory::getConfig();
			$cookie_path 	= $conf->get('cookie_path', '');
			$juri_root = JURI::root(true);
			$this->_advise_cookie = new JObject();
			$this->_advise_cookie->check = $cookie_path === $juri_root ? true : false;
			$this->_advise_cookie->juri_root = $juri_root;
			$this->_advise_cookie->cookie_path = $cookie_path;
		}
		return $this->_advise_cookie;
	}
	
	function getAdvise_joomla()
	{
		if (empty( $this->_advise_joomla ))
        {
			$this->_advise_joomla = new JObject();
			$this->_advise_joomla->check = version_compare(JVERSION, '1.6.0', '<') ? 0 : ( version_compare(JVERSION, '2.5.0', '<') ? 1 : 2 );
			$this->_advise_joomla->version = JVERSION;
		}
		return $this->_advise_joomla;
	}
	
	function getAdvise_livesite()
	{
		if (empty( $this->_advise_livesite ))
        {
			$conf =& JFactory::getConfig();
			$livesite 	= $conf->get('live_site', null);
			$this->_advise_livesite = new JObject();
			$this->_advise_livesite->check = $livesite === null ? true : false;
			$this->_advise_livesite->livesite = $livesite ? $livesite : '';
		}
		return $this->_advise_livesite;
	}
	
	function getAdvise_sh404()
	{
		if (empty( $this->_advise_sh404 ))
        {
			$this->_advise_sh404 = new JObject();
			$this->db =& JFactory::getDBO();
			$this->db->setQuery(
				version_compare(JVERSION, '1.6.0', '<') ? 
				'SELECT COUNT(*) as result FROM #__components WHERE `option`= "com_sh404sef"':
				'SELECT COUNT(*) as result FROM #__extensions WHERE `element`= "com_sh404sef"'
			);
			$count = (int) $this->db->loadResult();
			$this->_advise_sh404->check = $count === 0;
		}
		return $this->_advise_sh404;
	}

}//class
