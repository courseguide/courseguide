<?php
/**
* @package Component jVoteSystem for Joomla! 1.5-2.5 - 2.5
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
class jVoteSystemModelPolls extends JModel
{

	var $data, $filter;

    function __construct() {
        parent::__construct();
        
        $this->vbparams =& VBParams::getInstance();
        $this->vote =& VBVote::getInstance();
		$this->category =& VBCategory::getInstance();
    }//function

    function getPolls() {
        //-- Load the data if it doesn't already exist
        if(empty($this->data))
        {
        	$this->filter["stats"] = true;
            $this->data = $this->vote->getPolls($this->filter);
        }

        return $this->data;
    }//function
    
    function getFilter() {
    	$params = $this->vbparams->getActiveMenuParams();
    	
    	$this->filter = array();
    	$this->filter["order"] = JRequest::getString("order", $params->get("order", "popular"));
    	$this->filter["time"] = JRequest::getString("time", $params->get("time", "all-time"));
    	$this->filter["keyword"] = JRequest::getString("keyword", "");
    	$this->filter["page"] = JRequest::getInt("page", 1);
    	$catAlias = JRequest::getString("cat", $params->get("cat", null));
    	if($catAlias) $this->filter["cid"] = $this->category->getCatIdByAlias($catAlias);
    	else $this->filter["cid"] = JRequest::getInt("cid", 0);
    	
    	$cat = $this->category->getCategory($this->filter["cid"]);
    	if($cat->id == 0) {
    		
    	}
    	
    	return $this->filter;
    }
}//class
