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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jVoteSystem Component
 *
 * @package    jVoteSystem
 * @subpackage Views
 */

class jVoteSystemViewAdvisor extends JView
{
    /**
     * jVoteSystemList view display method
     * @return void
     **/
	 
    function display($tpl = null)
    {
    	$this->general =& VBGeneral::getInstance();
 
        $this->assignRef( 'advise_php', $this->get('Advise_php') );
        $this->assignRef( 'advise_cookie', $this->get('Advise_cookie') );
        $this->assignRef( 'advise_livesite', $this->get('Advise_livesite') );
        $this->assignRef( 'advise_joomla', $this->get('Advise_joomla') );
        $this->assignRef( 'advise_sh404', $this->get('Advise_sh404') );

        parent::display($tpl);
    }//function
	
}//class
