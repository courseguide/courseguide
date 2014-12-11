<?php
/**
 * @package Component jVoteSystem for Joomla! 1.5 - 2.5
 * @projectsite www.joomess.de/projects/jvotesystem
 * @author Johannes Me�mer
 * @copyright (C) 2010- Johannes Me�mer
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

//-- No direct access
defined('_JEXEC') or die('=;)');

jimport('joomla.application.component.model');


class jVoteSystemModelCategory extends JModel
{
	private $access;
    function __construct()
    {
        parent::__construct();

        $app = JFactory::getApplication('administrator');
        
        $this->category =& VBCategory::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->user =& VBUser::getInstance();
    }//function
    
    private $id;
    function getID() {
    	if(isset($this->id)) 
    		return $this->id;
    	else
    		return null;
    }

    function store() {
		$id = JRequest::getInt("id", null);
		$date = JFactory::getDate();
		$this->user->loadUser(true);
		$general = VBGeneral::getInstance();
		
		$ins = new JObject();
		$ins->id = $id;
		$ins->parent_id = JRequest::getInt("parent_id", 0);
		$ins->title = JRequest::getString("title", null);
		$ins->alias = JRequest::getString("alias", null);
		$ins->description = JRequest::getVar("description", "", "default", "STRING", JREQUEST_ALLOWHTML);
		$ins->accesslevel = JRequest::getInt("accesslevel", 1);
		$ins->published = JRequest::getInt("published", 0);
		$ins->access = $this->access->storeAccessData( array( 'add_poll', 'edit_poll', 'remove_poll') );
		//Parameter speichern 
		$params = JRequest::getVar("params", array(), "post", "ARRAY");
		$tabs = JRequest::getVar("allowed_tabs", array(), "post", "ARRAY");
		$params["allowed_tabs"] = array();
		foreach($tabs AS $tab) { $params["allowed_tabs"][] = $tab; }
		
		$ins->params = json_encode($params);
		
		if($id == null) {
			$ins->created = $date->toMySQL();
			$ins->autor_id = $this->user->id;
			if($ins->alias == null) $ins->alias = $ins->title;
			$ins->alias = $general->checkAlias($general->cleanStr($ins->alias), "categories");
			
			$this->_db->insertObject("#__jvotesystem_categories", $ins);
			$this->id = $this->_db->insertid();
			
			//Standardeinstellungen
			$vote =& VBVote::getInstance();
			$vote->addDefaultSettingsBox($this->id);
		} else {
			$cat = VBCategory::getInstance()->getCategory($id);
			$ins->alias = $general->cleanStr($ins->alias);
			if($cat->alias != $ins->alias) $ins->alias = $general->checkAlias($ins->alias);
			
			$this->id = $id;
			$this->_db->updateObject("#__jvotesystem_categories", $ins, "id");
		}
		if($this->_db->getErrorMsg()) return false;		
		
		return true;
	}
	
	function unpublish() {
		$cid = JRequest::getVar('cid', null);
		$id = $cid[0];
		
		$ins = new JObject();
		$ins->id = $id;
		$ins->published = 0;
	
		$this->_db->updateObject('#__jvotesystem_categories',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function publish() {
		$cid = JRequest::getVar('cid', null);
		$id = $cid[0];
		
		$ins = new JObject();
		$ins->id = $id;
		$ins->published = 1;
	
		$this->_db->updateObject('#__jvotesystem_categories',$ins,'id');
		if($this->_db->getErrorMsg()) return false;
		return true;
	}
	
	function move($move) {
		$cid = JRequest::getVar('cid', null);
		$id = $cid[0];
		
		$cat = $this->category->getCategory($id);
		if(!$cat) return false;
	
		$childs = $this->category->getCategoryChilds($cat->parent_id);
		$pos = array_search($cat->id, $childs);
	
		$posChild = $pos + $move;
		if(!isset($childs[$posChild])) return true;
		$idChild = $childs[$posChild];
	
		$order = 0;
		$orders = array();
		foreach($childs AS $i => $child) {
			$orders[$i] = array();
			$orders[$i]["id"] = $child;
			$orders[$i]["order"] = $order;
			if($posChild == $i) {
				$orders[$i]["id"] = $cat->id;
			} elseif($pos == $i) {
				$orders[$i]["id"] = $idChild;
			}
			$order++;
		}
	
		foreach($orders AS $row) {
			$upd = new JObject();
			$upd->id = $row["id"];
			$upd->order = $row["order"];
				
			$this->_db->updateObject("#__jvotesystem_categories", $upd, "id");
			if($this->_db->getErrorMsg) return false;
		}
	
		return true;
	}
	
	function delete() {
		$cid = JRequest::getVar('cid', null);
		if($cid == null) return false;
		
		$all_cats = $this->category->getCategories();
		if(count($all_cats) == count($cid)) {
			JError::raiseNotice("ONECATEGORYREQUIRED", "ERROR_CANT_REMOVE_ALL_CATS");
			return false;
		}
		
		$ret = true;
		foreach($cid AS $id) {
			$cat = $this->category->getCategory($id);
			if($cat->polls == 0) {
				$childs = $this->category->getCategoryChilds($id);
				if(sizeof($childs) == 0) {
					$sql = 'DELETE FROM `#__jvotesystem_categories` '
					. ' WHERE `id` = '.$id
					. ' LIMIT 1';
					$this->_db->setQuery($sql);
					$this->_db->query();
					if($this->_db->getErrorMsg()) return false;
					//DefaultSettings
					$sql = 'DELETE FROM `#__jvotesystem_boxes` '
					. ' WHERE `published` = -1 AND `catid` = '.$id
					. ' LIMIT 1';
					$this->_db->setQuery($sql);
					$this->_db->query();
					if($this->_db->getErrorMsg()) return false;
				} else {
					JError::raiseNotice("CATWITHSUBCATS", "ERROR_REMOVESUBCATS_BEFORE_REMOVECAT"); $ret = false;
				}
			} else {
				JError::raiseNotice("CATWITHPOLLS", "ERROR_REMOVEPOLLS_BEFORE_REMOVECAT"); $ret = false;
			}
		}
		return $ret;
	}
}// class
