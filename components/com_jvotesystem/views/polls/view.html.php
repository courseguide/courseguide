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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jVoteSystem Component
 *
 * @package    jVoteSystem
 * @subpackage Views
 */
class jVoteSystemViewPolls extends JView
{
    function show($tpl) {
    	parent::display($tpl);
    }
	
	function display($tpl = null)
    {
		//Klassen laden
		$this->app 		=& JFactory::getApplication();
		$this->pathway  =& $this->app->getPathway();
		$this->vbparams =& VBParams::getInstance('pollslist', true);
		$this->vote 	=& VBVote::getInstance();
		$this->user 	=& VBUser::getInstance();
		$this->category =& VBCategory::getInstance();
		$this->general 	=& VBGeneral::getInstance();
		$this->lib 		=& joomessLibrary::getInstance();
		
		//JS laden
		$template =& VBTemplate::getInstance();
		$template->setTemplate("default");
		
		$doc =& JFactory::getDocument();
		//Title setzen
		$doc->setTitle(JText::_('Polls'));
		//Css laden
		$this->lib->css("components/com_jvotesystem/assets/css/general.css");
		$this->lib->css("components/com_jvotesystem/assets/css/list.css");
		
		//Daten holen
		$this->filter = $this->get("Filter");
		
		//Toolbar
		$this->toolbar = new VBToolbar($this->category->getCategory($this->filter["cid"]));
		$this->toolbar->add();
		
		$this->toolbarHtml = $this->toolbar->out("float:right;margin-top:8px;");;
		
		$this->params = $this->vbparams->getActiveMenuParams();
		$this->menu = $this->vbparams->getActiveMenu();
		
		$cat = $this->category->getCategory($this->filter["cid"]);
		if(@$this->menu->query["view"] == "polls" && $this->filter["cid"] != 0 && $cat->alias != $this->params->get("cat", "")) {
			if($cat) $this->pathway->addItem($cat->title, $this->general->buildLink("category", $cat->id));
		}
		
		$this->layout = $this->params->get("list_layout", "table");
		
		//Head
		?>
			<div id="jvotesystem" class="jvotesystem">
		<?php echo $this->toolbarHtml;?>
		<?php if($this->params->get("show_page_heading", 1)) {?>
			<?php if(!$this->menu) {?>
				<h1><?php echo JText::_("List_of_active_polls"); if($this->filter["cid"] != 0) { echo ": ".@$this->category->getCategory($this->filter["cid"])->title; }?></h1>
			<?php } else {?>
				<h1><?php echo ($this->params->get("page_heading", "") != "") ? $this->params->get("page_heading", "") : $this->menu->title;?></h1>
		<?php } }
		
		//Caching
		$this->cache = & JCache::getInstance();
		$this->cache->setLifeTime( 30 );
		$kid = md5(serialize( array( $this->filter,	VBAccess::getInstance()->isUserAllowedToConfig(), JFactory::getLanguage()->getTag() ) ));
		
		if($html = $this->cache->get($kid, 'jVoteSystem - Lists')) {
			echo $html;
		} else {
			$this->polls = $this->get("Polls");
			$this->count = $this->vote->getNuwRows();
			$this->cats = $this->category->getCategories();
			
			$old = $template->prepare();
			
				parent::display($tpl);
				/* The copyright information may not be removed or made invisible! To remove the code, please purchase a version on www.joomess.de. Thanks!*/
				joomessLibrary::getInstance()->copyright('jVoteSystem');
				
			$html = $template->getHtml($old);
			
			echo $html;
			
			$this->cache->store($html, $kid, 'jVoteSystem - Lists');
		}
		
		?> </div> <?php 		
		
    }//function

}//class

