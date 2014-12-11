<?php

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport( 'joomla.html.parameter' );

class jVoteSystemViewCategory extends JView
{
	
	private $access;
	public function display($tpl = null)
	{
		$this->category =& VBCategory::getInstance();
		$this->access =& VBAccess::getInstance();
		$this->general =& VBGeneral::getInstance();
    	
        $cid = JRequest::getVar('cid', null);
		if($cid == null) $id = JRequest::getInt('id', 0);
        else $id = $cid[0];
        
		$item = $this->category->getCategory($id);
		$editor 	= & JFactory::getEditor();
		
		$new = ($id == null || $item->id == 0);
		
		JRequest::setVar('hidemainmenu', true);

		JToolBarHelper::title(JText::_('Category').' <small><small>[ '.($new ? JText::_('New') : JText::_('Edit')).' ]</small></small>', 'category'.($new ? '-add' : ""));

		JToolBarHelper::custom('defaultSettings', 'options', 'options', "DEFAULT_SETTINGS_FOR_POLLS", false);
		JToolBarHelper::divider();
		JToolBarHelper::apply();
		JToolBarHelper::save();

		JToolBarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		
		if($new) {
			$item = new JObject();
			$item->id = null;
			$item->parent_id = 0;
			$item->title = "";
			$item->description = "";
			$item->alias = "";
			$item->accesslevel = 0;
			$item->published = 0;
			$item->autopublish_polls = 1;
			$item->mail_admin_new_poll = 1;
			$item->edit_own_poll = 1;
			$item->remove_own_poll = 0;
			$item->allowed_tabs = array("settings", "display", "result", "votes");
			
			$item->access = new JObject();
			$item->access->add_poll = 19;
			$item->access->edit_poll = 20;
			$item->access->remove_poll = 21;
		}
		
		//Lists
		$lists = array();
		//Parent-Category
		$lists["parent"] = $this->category->getCategories($item->id);
		$lists["accesslevel"] = $this->access->getViewLevels();
		
		//Access
		$actions = array();
		$actions[] = array(	"title"	=> JText::_('Add_poll'),	"name"	=> "add_poll");
		$actions[] = array(	"title"	=> JText::_('Edit_poll'),	"name"	=> "edit_poll");
		$actions[] = array(	"title"	=> JText::_('Remove_poll'),	"name"	=> "remove_poll");
		
		$accessHtml = $this->access->getHtmlAccessLists($actions, $item);
		
		//$params = new JParameter( $template->params,  JPATH_SITE.DS."components".DS."com_stripegallery".DS."templates".DS.$template->name.DS.$template->name.".xml", 'template' );
		
		$this->assignRef('item',		$item);
		$this->assignRef('lists',		$lists);
		$this->assignRef('editor'		, $editor);
		$this->assignRef('accessHtml'	, $accessHtml);
	
		parent::display($tpl);
	}
}
